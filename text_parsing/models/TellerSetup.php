<?php

Hook::sub("subscription", function($line){

	if(TellerSetup::isEnd($line))
	{
		TellerSetup::instance()->completed();
		TellerSetup::destroy();
		return;
	}

	if(TellerSetup::isBegin($line))
	{
		TellerSetup::instance()->counter = 0;
		return;
	}

	if(!TellerSetup::has())
		return;

	$matches = array();
	$line = strtolower($line);
	if(preg_match_all("/([A-Za-z]*)[ ]+([0-9\/:]+( am| pm)?)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);

		if($key == "date")
			TellerSetup::instance()->date = format_date($value);

		if($key == "time")
			TellerSetup::instance()->time = format_time($value);
	}
	else
	{
		TellerSetup::instance()->counter++;

		if(TellerSetup::instance()->counter > 1)
		{
			TellerSetup::instance()->completed();
			TellerSetup::destroy();
		}
	}


});

class TellerSetup extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"datetime",
								"action",
								"teller_id",
								"trans_limit",
								"daily_limit",
								"deposit_limit",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");


	public $id;
	public $file_processing_detail_id;
	public $station;
	public $datetime;
	public $action;
	public $teller_id;
	public $trans_limit;
	public $daily_limit;
	public $deposit_limit;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time = "00:00:00";
	public $date = "1999-01-01";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->station = Station::has() ? Station::instance()->id : null;
	}

	public function completed()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));

		DB::insert("teller_setup", $this->toArray(), $this->id);
	}

	public static function isBegin($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Teller Setup)[ ]+/", $s, $matches);
	}

	public static function isEnd($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Exit Teller Setup)[ ]+/", $s, $matches);
	}
}