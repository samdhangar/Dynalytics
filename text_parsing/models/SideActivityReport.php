<?php

Hook::sub("subscription", function($line){

	if(SideActivityReport::isStart($line))
	{
		// ...
		return;
	}

	if(!SideActivityReport::has())
		return;

	if(SideActivityReport::isEnded($line))
	{
		// ...
		SideActivityReport::instance()->completed();
		SideActivityReport::destroy();
		return;
	}

	$line = strtolower($line);
	$matches = array();
	if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*\.[0-9]{2})/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		$key = "denom_$key";

		if(!in_array($key, array("denom_100", "denom_50", "denom_20", "denom_10", "denom_5", "denom_2", "denom_1")))
			return;

		SideActivityReport::instance()->$key = intVal($piece);
		return;
	}
	else if(preg_match_all("/([A-Za-z]+(( |\-)[A-Za-z]+)*)[ ]+([0-9]*\.?[0-9]+)/", strtolower($line), $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[4]);

		$k = "total";
		if(in_array($key, array("total deposited", "total dispensed", "vault buy total")))
			$k = "total";
		if($key == "machine total")
			$k = "machine_total";
		if($key == "non-cash total")
			$k = "non_cash_dispence_total";
		if($key == "coin")
			$k = "coin";
		if($key == "of deposits")
			$k = "number_of_transactions";
		if($key == "credit card advance")
			$k = "credit_card_advance";
		if($key == "check cashing")
			$k = "check_cashing";

		SideActivityReport::instance()->$k = floatVal($value);

		if(in_array($k, array("credit_card_advance", "check_cashing")))
		{
			SideActivityReport::instance()->meta[$k] = floatVal($value);
		}
	}
});

class SideActivityReport extends Model
{
	public static $instance = null;
	protected $fields = array( "id",
								"file_processing_detail_id",
								"station",
								"activity_report_id",
								"side",
								"type",
								"denom_100",
								"denom_50",
								"denom_20",
								"denom_10",
								"denom_5",
								"denom_2",
								"denom_1",
								"coin",
								"non_cash_dispence_total",
								"total",
								"machine_total",
								"credit_card_advance",
								"check_cashing",
								"number_of_transactions",
								"meta",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $activity_report_id;
	public $side;
	public $type;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $non_cash_dispence_total;
	public $total;
	public $machine_total;
	public $credit_card_advance;
	public $check_cashing;
	public $number_of_transactions;
	public $meta = array();
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->meta = json_encode($this->meta);
		DB::insert("side_activity_report", $this->toArray(), $this->id);
		$this->meta = json_decode($this->meta, 1);

		Container::instance()->setLastBlock($this);
		 echo "Adarsh 3";
		 echo "<pre>";
         print_r($this->id);
          echo "<pre>";
         print_r($this->toArray());
 echo "Adarsh 3 end";


	}

	public static function isStart($subject)
	{
		$matches = array();
		if(preg_match_all("/[ ]+((Side )?(([A-Z])|(Grand Total)) ((Deposited)|(Dispensed))|( Vault Buys))[ ]+/", $subject, $matches))
		{
			// Close last active
			if(self::has())
			{
				self::instance()->completed();
				self::destroy();
			}

			self::instance()->side = trim(array_shift($matches[3]));
			self::instance()->type = trim(array_shift($matches[6]));

			return true;

			//if(empty(self::instance()->side) AND empty(self::instance()->type))
		}

		return false;
	}

	public static function isEnded($subject)
	{
		$matches = array();
		return preg_match_all("/[=]{2,}/", $subject, $matches);
	}
}