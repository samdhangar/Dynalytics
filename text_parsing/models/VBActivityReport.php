<?php

Hook::sub("subscription", function($line){

	if(VBActivityReport::isStart($line))
	{
		if(!ActivityReport::has())
			return;

		VBActivityReport::instance()->lineCounter = 0;
		VBActivityReport::instance()->side = "Vault Buys";
	}

	if(!VBActivityReport::has())
		return;

	if(!VBActivityReport::instance()->isOk)
	{
		if(VBActivityReport::isReal($line))
			return VBActivityReport::instance()->isOk = true;

		VBActivityReport::instance()->lineCounter++;
	}

	if(VBActivityReport::instance()->isOk AND VBActivityReport::isEnd($line))
	{
		VBActivityReport::instance()->completed();
		return VBActivityReport::destroy();
	}

	if(!VBActivityReport::instance()->isOk AND VBActivityReport::instance()->lineCounter > 2)
	{
		return VBActivityReport::destroy();
	}

	$matches = array();
	if(preg_match_all("/^Vault Buy Total[ ]+([0-9]*\.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		VBActivityReport::instance()->total = $value;

		//echo "Total: $value\n";
	}
	elseif(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})\n/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		$key = "denom_{$key}";

		VBActivityReport::instance()->$key = $piece;

		//echo "$key => $value\n";
	}
});

class VBActivityReport extends Model
{
	public $lineCounter = 0;
	public $isOk = 0;

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
								"number_of_transactions",
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
	public $number_of_transactions;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = station::has() ? Station::instance()->id : null;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("side_activity_report", $this->toArray(), $this->id);
		 echo "Adarsh 2";
		 echo "<pre>";
         print_r($this->id);
          echo "<pre>";
         print_r($this->toArray());
 echo "Adarsh 2 end";
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Vault Buys)[ ]+/", $str, $matches);
	}

	public static function isReal($subject)
	{
		$matches = array();
		return preg_match_all("/^(Denom)[ ]+(Pieces)[ ]+(Value)\n/", $subject, $matches);
	}

	public static function isEnd($subject)
	{
		$matches = array();
		return preg_match_all("/^[-]+/", $subject, $matches);
	}
}