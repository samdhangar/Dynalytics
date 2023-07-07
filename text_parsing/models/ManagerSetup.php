<?php

Hook::sub("subscription", function($line){

	if(ManagerSetup::isEnd($line))
	{
		ManagerSetup::instance()->completed();
		ManagerSetup::destroy();
		return;
	}

	if(ManagerSetup::isBegin($line))
	{
		ManagerSetup::instance()->a = 1;
		return;
	}

	if(!ManagerSetup::has())
		return;

	$matches = array();
	$line = strtolower($line);
	if(preg_match_all("/^([A-Za-z]*( [A-Za-z]+)*)[ ]+([0-9\/:]+( am| pm)?)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[3]);

		if($key == "date")
			ManagerSetup::instance()->date = format_date($value);

		if($key == "time")
			ManagerSetup::instance()->time = format_time($value);

		if($key == "added manager")
		{
			ManagerSetup::instance()->action = "added";
			ManagerSetup::instance()->manager_id = $value;
		}
	}
	else if(preg_match_all("/([A-Za-z0-9#\*\.: \-]+)/", $line, $matches))
	{
		ManagerSetup::instance()->line[] = array_shift($matches[0]);
	}


});

class ManagerSetup extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"datetime",
								"action",
								"manager_id",
								"trans_limit",
								"daily_limit",
								"deposit_limit",
								"text",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $datetime;
	public $action;
	public $manager_id;
	public $trans_limit;
	public $daily_limit;
	public $deposit_limit;
	public $text;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time = "00:00:00";
	public $date = "1999-01-01";
	public $line = array();

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->station = Station::has() ? Station::instance()->id : null;
	}

	public function completed()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));
		$this->text = implode("\n", $this->line);
		$this->text = substr($this->text, 0, min(100, strlen($this->text)));

		DB::insert("manager_setup", $this->toArray(), $this->id);
	}

	public static function isBegin($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Manager Setup)[ ]+/", $s, $matches);
	}

	public static function isEnd($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Exit Manager Setup)[ ]+/", $s, $matches);
	}
}