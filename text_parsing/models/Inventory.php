<?php

Hook::sub("subscription", function($line){	

	if(Inventory::isStart($line))
	{
		Inventory::instance();
	}

	if(!Inventory::has())
		return;

	$matches = array();
	if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		$key = "denom_$key";

		Inventory::instance()->$key = $piece;
	}
	else if(preg_match_all("/Coin[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Inventory::instance()->coin = $value;
	}
	else if(preg_match_all("/[\*]{3} ([A-Za-z0-9]+( [A-Za-z0-9]+)+) [\*]{3}/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Inventory::instance()->message = $value;
	}
	else if(preg_match_all("/^Starting Inventory[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Inventory::instance()->starting_inventory = floatval($value);
	}
	else if(preg_match_all("/^Net Adjustments[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Inventory::instance()->net_adjustments = floatval($value);
	}
	else if(preg_match_all("/^Total Inventory[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Inventory::instance()->total = floatval($value);

		/*Inventory::instance()->completed();
		Inventory::destroy();*/
	}
	
	if (preg_match_all("/[ ]+Dispensable Notes[ ]+/", $line, $matches)) {
		Inventory::instance()->dispensable_notes_flag = 1;

		$name = 'inventory_setting';
		$section_id = Inventory::getSectionId(Inventory::instance()->file_processing_detail_id, $name);
		DispensableNotes::instance()->Section = $section_id;
	}

	if (Inventory::instance()->dispensable_notes_flag == 1) 
	{
		if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
				return;

			$piece_key = "Denom_".$key."_pieces";
			$value_key = "Denom_".$key."_value";

			DispensableNotes::instance()->$piece_key = $piece;
			DispensableNotes::instance()->$value_key = $value;
		}

		if (preg_match_all("/((A-Za-z )*)+[ ]+([0-9]*.[0-9]{2})/", $line, $matches)) {
			DispensableNotes::instance()->Total_dispensable_notes = $matches[3][0];
		}

		if(preg_match_all("/(([A-Za-z])*).[ ]+([0-9]+)[ ]+([$0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[3]);
			$case= array_shift($matches[4]);
			$pieces = array_shift($matches[5]);
			$value = array_shift($matches[6]);

			$case_key = "Case_".$key;
			$piece_key = "Case_".$key."_pieces";
			$value_key = "Case_".$key."_value";

			DispensableNotes::instance()->$case_key = $case;
			DispensableNotes::instance()->$piece_key = $pieces;
			DispensableNotes::instance()->$value_key = $value;
		}
	}

	if(Inventory::isEnd($line))
	{
		Inventory::instance()->completed();

		$inventry_id = Inventory::instance()->id;

		if (Inventory::instance()->dispensable_notes_flag == 1) {
			DispensableNotes::instance()->inventory_id = $inventry_id;

			DispensableNotes::instance()->completed();
			DispensableNotes::destroy();

			Inventory::instance()->dispensable_notes_flag == 0;
		}
		return Inventory::destroy();
	}

});

class Inventory extends Model
{
	public static $instance = null;
	public $dispensable_notes_flag = 0;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"activity_report_id",
								"denom_100",
								"denom_50",
								"denom_20",
								"denom_10",
								"denom_5",
								"denom_2",
								"denom_1",
								"coin",
								"starting_inventory",
								"net_adjustments",
								"total",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");
	
	public $id;
	public $file_processing_detail_id;
	public $station;
	public $activity_report_id;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $starting_inventory;
	public $net_adjustments;
	public $total;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::instance()->id;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("inventory", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+Inventory[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}

	public static function getSectionId($file_id,$name)	{
		$db = DB::getInstance();
		$sql = $db->prepare("INSERT INTO  sections (file_id , section , created_date) VALUES('$file_id','$name',now())");
        $sql->execute(); 
        $sql_machine = $db->prepare("SELECT id FROM sections where section='$name' order by id DESC LIMIT 1");
        $sql_machine->execute();
        $section_details = $sql_machine->fetch();

        return $section_details['id'];
	}

}

class DispensableNotes extends Model
{	
	public static $instance = null;

	protected $fields = array("id",
							"File_id",
							"Section",
							"inventory_id",
							"Denom_100_pieces",
							"Denom_50_pieces",
							"Denom_20_pieces",
							"Denom_10_pieces",
							"Denom_5_pieces",
							"Denom_2_pieces",
							"Denom_1_pieces",
							"Denom_100_value",
							"Denom_50_value",
							"Denom_20_value",
							"Denom_10_value",
							"Denom_5_value",
							"Denom_2_value",
							"Denom_1_value",
							"Total_dispensable_notes",
							"Case_1",
							"Case_1_pieces",
							"Case_1_value",
							"Case_2",
							"Case_2_pieces",
							"Case_2_value",
							"Case_3",
							"Case_3_pieces",
							"Case_3_value",
							"Case_4",
							"Case_4_pieces",
							"Case_4_value",
							"Case_5",
							"Case_5_pieces",
							"Case_5_value",
							"Case_6",
							"Case_6_pieces",
							"Case_6_value",
							"Case_a_low",
							"Case_b_low",
							"Case_c_low",
							"Case_d_low",
							"Case_e_low",
							"Case_f_low",
							"created_date");

	public $id;
	public $File_id;
	public $Section;
	public $inventory_id;
	public $Denom_100_pieces;
	public $Denom_50_pieces;
	public $Denom_20_pieces;
	public $Denom_10_pieces;
	public $Denom_5_pieces;
	public $Denom_2_pieces;
	public $Denom_1_pieces;
	public $Denom_100_value;
	public $Denom_50_value;
	public $Denom_20_value;
	public $Denom_10_value;
	public $Denom_5_value;
	public $Denom_2_value;
	public $Denom_1_value;
	public $Total_dispensable_notes;
	public $Case_1;
	public $Case_1_pieces;
	public $Case_1_value;
	public $Case_2;
	public $Case_2_pieces;
	public $Case_2_value;
	public $Case_3;
	public $Case_3_pieces;
	public $Case_3_value;
	public $Case_4;
	public $Case_4_pieces;
	public $Case_4_value;
	public $Case_5;
	public $Case_5_pieces;
	public $Case_5_value;
	public $Case_6;
	public $Case_6_pieces;
	public $Case_6_value;
	public $Case_a_low;
	public $Case_b_low;
	public $Case_c_low;
	public $Case_d_low;
	public $Case_e_low;
	public $Case_f_low;
	public $created_date;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;		
		$this->created_date = date("Y-m-d H:i:s");	
	}

	public function completed()
	{
		DB::insert("dispensable_notes", $this->toArray(), $this->id);
	}
	
}