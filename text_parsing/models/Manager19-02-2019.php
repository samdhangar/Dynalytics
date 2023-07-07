<?php

Hook::sub("subscription", function($line){


	if(Manager::manager_logged_on($line))
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

		Manager::instance()->__status = "login";
		return Manager::instance()->__number = 0;
	}
	
	if(!Manager::has())
		return;
	
	if(Manager::manager_logged_off($line))
	{
		Manager::instance()->__status = "logout";
		Manager::instance()->__number = 0;
	}

	$matches = array();
	echo "manager page start ";
	echo "<pre>";
	print_r($matches);
	echo "manager page end ";
	if(Manager::instance()->__status == "loggedin" AND preg_match_all("/^((Coin|Cassette [123456]{1}) Adjusted|New (Coin|Cassette) (Total))[ ]+((\+|\-)?[0-9]+\.[0-9]{0,2})/", $line, $matches))
	{
		echo "dddddddddddd";
		echo $type = array_shift($matches[2])."<br>";
		echo $total = array_shift($matches[4])."<br>";
		echo $amount = array_shift($matches[5])."<br>";


		if(!empty($type))
		{
			if(BillAdjustment::has())
			{
				BillAdjustment::instance()->completed();
				BillAdjustment::destroy();
			}

			BillAdjustment::instance()->adjustment_type = $type;
			BillAdjustment::instance()->adjustment_value = trim($amount, "+");
		}
		else if(empty($type) AND $total == "Total")
		{
			BillAdjustment::instance()->new_value_total = trim($amount, "+");
			BillAdjustment::instance()->completed();
			BillAdjustment::destroy();
		}

		return;
	}

	if(Manager::instance()->__status == "loggedin" AND preg_match_all("/^\((Coin|[0-9]+)\) (Assumed|Actual) Count\:[ ]+([0-9]+)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$type = array_shift($matches[2]);
		$amount = array_shift($matches[3]);

		$key = strtolower("_{$key}_{$type}_count");

		BillsCount::instance()->$key = $amount;
	}
	else if(Manager::instance()->__status == "loggedin" AND BillsCount::has())
	{
		BillsCount::instance()->completed();
		BillsCount::destroy();
	}

	if(preg_match_all("/^([A-Za-z0-9]+( [A-Za-z0-9]+)*)[ #]+([0-9\/:]+( am| pm)?)/", strtolower($line), $matches))
	{
		list($key) = $matches[1];
		list($value) = $matches[3];

		if(!in_array($key, array("manager", "date", "time", "new time")))
			return;

		if(Manager::instance()->__status == "login")
		{
			if($key == "manager")
				Manager::instance()->manager_id = intVal($value);

			if($key == "date")
				Manager::instance()->logon_date = format_date($value);

			if($key == "time")
			{
				if(strlen($value) < 9)
					$value = "0".$value;

				Manager::instance()->logon_time = format_time($value);
			}

			if($key == "new time")
				Manager::instance()->new_time = format_time($value);
/*
			if(Manager::instance()->__number == 2)
			{
				Manager::instance()->__status = "loggedin";
				Manager::instance()->__number = 0;
				return Manager::instance()->logOn();
			}

			return Manager::instance()->__number++;
*/
			return;
		}
		else if(Manager::instance()->__status == "logout")
		{
			if($key == "manager")
				Manager::instance()->manager_id = Manager::instance()->manager_id ? Manager::instance()->manager_id : intVal($value);

			if($key == "date")
				Manager::instance()->logoff_date = format_date($value);

			if($key == "time")
			{
				if(strlen($value) < 9)
					$value = "0".$value;

				Manager::instance()->logoff_time = format_time($value);
			}

			if(Manager::instance()->__number == 2)
			{
				Manager::instance()->logOff();
				Manager::instance()->completed();
				return Manager::destroy();
			}

			return Manager::instance()->__number++;
		}
	}
	else if(preg_match_all("/ *\*\*\* (.*?) \*\*\* */", strtolower($line), $matches))
	{
		Manager::instance()->message[] = array_shift($matches[1]);
	}

	if(!strlen(trim($line)))
	{
		if(Manager::has() AND Manager::instance()->__status == "login")
		{
			Manager::instance()->__status = "loggedin";
			Manager::instance()->__number = 0;
			return Manager::instance()->logOn();
		}
		else if(Manager::has() AND BillAdjustment::has())
		{
			BillAdjustment::instance()->completed();
			BillAdjustment::destroy();
		}
	}

});

class Manager extends Model
{
	public static $instance = null;
	protected static $type = null;
	protected static $counter = 0;

	protected $fields = array("id",
								"file_processing_detail_id",
								"manager_id",
								"logon_datetime",
								"logoff_datetime",
								"new_time",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $manager_id;
	public $logon_datetime;
	public $logoff_datetime;
	public $new_time;
	public $message = array();
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

		$this->message = implode("; ", $this->message);
		DB::insert("manager_log", $this->toArray(), $this->id);
		$this->message = explode("; ", $this->message);
	}

	public function logOff()
	{
		$this->logoff_datetime = implode(" ", array($this->logoff_date, $this->logoff_time));

		$this->message = implode("; ", $this->message);
		DB::update("manager_log", $this->toArray(), "id");
		$this->message = explode("; ", $this->message);
	}

	public function completed()
	{
	}

	public static function manager_logged_on($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Manager Logged On)[ ]+/", $str, $matches);
	}
	public static function manager_logged_off($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Manager Logged Off)[ ]+/", $str, $matches);
	}
}

class BillAdjustment extends Model
{
	public static $instance = null;

	protected $fields = array(
								"id",
								"file_processing_detail_id",
								"station",
								"manager_id",
								"datetime",
								"adjustment_type",
								"adjustment_value",
								"new_value_total",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by"
							);
	
	public $id;
	public $file_processing_detail_id;
	public $station;
	public $manager_id;
	public $datetime;
	public $adjustment_type;
	public $adjustment_value;
	public $new_value_total;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
		$this->manager_id = Manager::has() ? Manager::instance()->manager_id : null;
		$this->datetime = Manager::has() ? Manager::instance()->logon_datetime : "1999-01-01 00:00:00";
	}

	public function completed()
	{
		DB::insert("bill_adjustments", $this->toArray(), $this->id);
	}
}

class BillsCount extends Model
{
	public static $instance = null;

	protected $fields = array(
								"id",
								"file_processing_detail_id",
								"manager_id",
								"_1_assumed_count",
								"_1_actual_count",
								"_2_assumed_count",
								"_2_actual_count",
								"_5_assumed_count",
								"_5_actual_count",
								"_10_assumed_count",
								"_10_actual_count",
								"_20_assumed_count",
								"_20_actual_count",
								"_50_assumed_count",
								"_50_actual_count",
								"_100_assumed_count",
								"_100_actual_count",
								"_coin_assumed_count",
								"_coin_actual_count",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $manager_id;
	public $_1_assumed_count;
	public $_1_actual_count;
	public $_2_assumed_count;
	public $_2_actual_count;
	public $_5_assumed_count;
	public $_5_actual_count;
	public $_10_assumed_count;
	public $_10_actual_count;
	public $_20_assumed_count;
	public $_20_actual_count;
	public $_50_assumed_count;
	public $_50_actual_count;
	public $_100_assumed_count;
	public $_100_actual_count;
	public $_coin_assumed_count;
	public $_coin_actual_count;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
		$this->manager_id = Manager::has() ? Manager::instance()->manager_id : null;
	}

	public function completed()
	{
		DB::insert("bills_count", $this->toArray(), $this->id);
	}
}