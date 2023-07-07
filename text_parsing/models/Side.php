<?php

Hook::sub("subscription", function($line){

	if($side = Side::logged_on($line))
	{
		if(Side::has())
		{
			Side::instance()->logOff();
			Side::instance()->completed();
			Side::destroy();
		}

		if(Manager::has())
		{
			Manager::instance()->logOff();
			Manager::instance()->completed();
			Manager::destroy();
		}

		Side::instance()->__status = "login";
		Side::instance()->__number = 0;
		return Side::instance()->side_type = $side;
	}

	if(!Side::has())
	{
		return;
	}
	
	if($side = Side::logged_off($line))
	{
		Side::instance()->__status = "logout";
		return Side::instance()->__number = 0;
	}

	if(!in_array(Side::instance()->__status, array("login", "logout")))
		return;

	$line = strtolower($line);

	$matches = array();
	if(!preg_match_all("/([A-Za-z0-9]*?)[ #]+([0-9\/:]+( am| pm)?)/", $line, $matches))
		return;

	list($key) = $matches[1];
	list($value) = $matches[2];

	if(!in_array($key, array("teller", "date", "time")))
		return;

	if(Side::instance()->__status == "login")
	{
		if($key == "teller")
			Side::instance()->teller_id = intVal($value);

		if($key == "date")
			Side::instance()->logon_date = format_date($value);

		if($key == "time")
			Side::instance()->logon_time = format_time($value);

		if(Side::instance()->__number == 2)
		{
			Side::instance()->logOn();
			Side::instance()->__status = "loggedin";
			Side::instance()->__number = 0;
			return;
		}

		return Side::instance()->__number++;
	}
	else if(Side::instance()->__status = "logout")
	{
		if($key == "teller")
			Side::instance()->teller_id = Side::instance()->teller_id ? Side::instance()->teller_id : intVal($value);

		if($key == "date")
			Side::instance()->logoff_date = format_date($value);

		if($key == "time")
			Side::instance()->logoff_time = format_time($value);

		if(Side::instance()->__number == 2)
		{
			Side::instance()->logOff();
			Side::instance()->completed();
			return Side::destroy();
		}

		return Side::instance()->__number++;
	}

});


class Side extends Model
{
	public static $instance = null;

	protected $fields = array("id",
								"file_processing_detail_id",
								"side_type",
								"teller_id",
								"teller_setup_id",
								"logon_datetime",
								"logoff_datetime",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $side_type;
	public $teller_id;
	public $teller_setup_id;
	public $logon_datetime;
	public $logoff_datetime;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $logoff_date = "1999-01-01";
	public $logoff_time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function logOn()
	{
		$this->logon_datetime = implode(" ", array($this->logon_date, $this->logon_time));

		DB::insert("side_log", $this->toArray(), $this->id);
	}

	public function logOff()
	{
		$this->logoff_datetime = implode(" ", array($this->logoff_date, $this->logoff_time));

		DB::update("side_log", $this->toArray(), "id");
	}


	 


	public function completed()
	{
	}

	public static function logged_on($str)
	{
		$matches = array();
		preg_match_all("/^[ ]+(Side ([A-Za-z ]+) Logged On)[ ]+$/", $str, $matches);

		return array_shift($matches[2]);
	}
	public static function logged_off($str)
	{
		$matches = array();
		preg_match_all("/^[ ]+(Side ([A-Za-z ]+) Logged Off)[ ]+$/", $str, $matches);

		return array_shift($matches[2]);
	}
}