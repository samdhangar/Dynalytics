<?php

Hook::sub("subscription", function($line){

	if(ActivityReport::isStarted($line))
	{
		return ActivityReport::instance();
	}

	if(!ActivityReport::has())
		return;

	if(ActivityReport::isEnded($line))
	{
		ActivityReport::instance()->completed();
		return ActivityReport::destroy();
	}

	$matches = array();
	if(preg_match_all("/([A-Za-z]+( [A-Za-z]+)*) #?[ ]+([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
	{
		list($key) = $matches[1];
		list($value) = $matches[3];

		if($key == "station")
			ActivityReport::instance()->station = intVal($value);
		if($key == "date")
			ActivityReport::instance()->date = format_date($value);
		if($key == "time")
			ActivityReport::instance()->time = format_time($value);
		if($key == "manager")
		{
			ActivityReport::instance()->manager_id = intVal($value);
			ActivityReport::instance()->saveHeader();
		}
	}
	else if(preg_match_all("/ *\*\*\* (.*?) \*\*\* */", $line, $matches))
	{
		//ActivityReport::instance()->message = array_shift($matches[1]);
		//ActivityReport::instance()->completed();
		return ActivityReport::destroy();
	}

});

class ActivityReport extends Model
{
	public static $instance = null;

	protected $fields = array("id",
							"file_processing_detail_id",
							"station",
							"manager_id",
							"trans_datetime",
							"message",
							"created_date",
							"created_by",
							"updated_date",
							"updated_by");
	
	public $id;
	public $file_processing_detail_id;
	public $station;
	public $manager_id;
	public $trans_datetime;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		 $this->file_processing_detail_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function saveHeader()
	{
		$this->trans_datetime = implode(" ", array($this->date, $this->time));
		
		DB::insert("activity_report", $this->toArray(), $this->id);

		ProcessingFile::instance()->inc_activity_report_number();
	}

	public function completed()
	{
		DB::update("activity_report", $this->toArray(), "id");

	}

	public static function isStarted($str)
	{
		$matches = array();
		return preg_match_all("/(Activity Report)/", $str, $matches);
	}

	public static function isEnded($str)
	{
		$matches = array();
		return preg_match_all("/(Activity Cleared)/", $str, $matches);
	}

	public static function lastUpdateMsg($message)
	{
		$db = DB::getInstance();

		$s = $db->prepare("UPDATE `activity_report` a SET a.message=:message WHERE 1 ORDER BY a.id DESC LIMIT 1");
		$s->bindParam(":message", $message, PDO::PARAM_STR);
		return $s->execute();
	}
}