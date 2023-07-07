<?php

Hook::sub("subscription", function($line){

	if(UserReport::isStart($line))
	{
		UserReport::instance()->section = "init";
		return;
	}

	if(!UserReport::has())
		return;

	if(UserReport::isEnd($line))
	{
		UserReport::instance()->completed();
		UserReport::destroy();
		return;
	}

	$matches = array();
	if(preg_match_all("/(Managers)/", $line, $matches))
		return UserReport::instance()->section = "managers";

	if(preg_match_all("/(Tellers|Cashiers)/", $line, $matches))
	{
		UserReport::instance()->section = "tellers";

		if(TellerUserReport::has())
		{
			TellerUserReport::instance()->completed();
			TellerUserReport::destroy();
		}

		return TellerUserReport::instance();
	}

	if(UserReport::instance()->section == "init")
	{
		$line = strtolower($line);
		if(!preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9\/:]+( am| pm)?)/", $line, $matches))
			return;

		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);

		if($key == "date" AND empty(UserReport::instance()->date))
			UserReport::instance()->date = format_date($value);

		if($key == "time" AND empty(UserReport::instance()->time))
			UserReport::instance()->time = format_time($value);

		UserReport::instance()->initComplete();
	}
	else if(UserReport::instance()->section == "tellers")
	{
		if(!preg_match_all("/([A-Za-z]+( Limit)?)[ #?]+(([0-9]+)|([A-Za-z]+( [A-Za-z]+)?))/", $line, $matches))
			return;

		$key = array_shift($matches[1]);
		$value = array_shift($matches[3]);

		if(in_array($key, array("Teller", "Cashier")))
			return TellerUserReport::instance()->teller_id = intVal($value);
		if($key == "Transaction Limit")
			return TellerUserReport::instance()->trans_limit = $value;
		if($key == "Daily Limit")
			return TellerUserReport::instance()->daily_limit = $value;
		if($key == "Deposit Limit")
			return TellerUserReport::instance()->deposit_limit = $value;
	}
	else if(UserReport::instance()->section == "managers")
	{
		if(!preg_match_all("/(Manager)( #[ ]+)([0-9])/", $line, $matches))
			return;

		$value = array_shift($matches[3]);

		$m = new ManagerUserReport();
		$m->manager_id = $value;
		$m->completed();
	}

});

class UserReport extends Model
{
	protected static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"trans_datetime",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $trans_datetime;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time;
	public $date;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		if(TellerUserReport::has())
		{
			TellerUserReport::instance()->completed();
			TellerUserReport::destroy();
		}

		$this->updated_date = date("Y-m-d H:i:s");
		$this->updated_by = "sys";
	}

	public function initComplete()
	{
		if(count(array_filter(array($this->time, $this->date))) < 2)
			return;

		$this->time = $this->time ? $this->time : "00:00:00";
		$this->date = $this->date ? $this->date : "1999:01:01";

		$this->trans_datetime = implode(" ", array($this->date, $this->time));



		DB::insert("user_report", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/(User Report)/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}
}

class TellerUserReport extends Model
{
	protected static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"user_report_id",
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
	public $user_report_id;
	public $teller_id;
	public $trans_limit;
	public $daily_limit;
	public $deposit_limit;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->user_report_id = UserReport::has() ? UserReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("teller_user_report", $this->toArray(), $this->id);
	}
}

class ManagerUserReport extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"user_report_id",
								"manager_id",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $user_report_id;
	public $manager_id;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->user_report_id = UserReport::has() ? UserReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("manager_user_report", $this->toArray(), $this->id);
	}
}