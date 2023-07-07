<?php

Hook::sub("subscription", function($line){

	if(NetCasheUsage::isStart($line))
	{
		NetCasheUsage::instance();
	}

	if(!NetCasheUsage::has())
		return;

	if(NetCasheUsage::isEnd($line))
	{
		NetCasheUsage::instance()->completed();
		NetCasheUsage::destroy();
	}

	$matches = array();
	if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		$key = "denom_$key";

		NetCasheUsage::instance()->$key = $piece;
	}
	else if(preg_match_all("/Coin[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		NetCasheUsage::instance()->coin = $value;
	}
	else if(preg_match_all("/[\*]{3} ([A-Za-z0-9]+( [A-Za-z0-9]+)+) [\*]{3}/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		NetCasheUsage::instance()->message = $value;
	}
	else if(preg_match_all("/^Net Total[ ]+(((\+|\-)?[0-9]*.[0-9]{2}|[A-Za-z ]+))/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		NetCasheUsage::instance()->net_total = floatval($value);
	}
});

class NetCasheUsage extends Model
{
	public static $instance = null;
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
								"net_total",
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
	public $net_total;
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
		DB::insert("net_cash_usage_activity_report", $this->toArray(), $this->id);
		 echo "Adarsh 1";
		 echo "<pre>";
         print_r($this->id);
          echo "<pre>";
         print_r($this->toArray());
 echo "Adarsh 1 end";
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Net Cash Usage)[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}
}