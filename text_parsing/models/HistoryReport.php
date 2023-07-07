<?php

Hook::sub("subscription", function($line){

	if(HistoryReport::starting($line))
	{
		HistoryReport::instance()->__status = "opening";
		HistoryReport::instance()->__number = 0;
	}


	if(!HistoryReport::has())
		return;

	if(HistoryReport::ending($line))
	{
		HistoryReport::instance()->close();
		HistoryReport::instance()->completed();
		return HistoryReport::destroy();
	}
	
	$line = strtolower($line);

	$matches = array();
	if(!preg_match_all("/([A-Za-z0-9]*?)[ #]+([0-9\/:]+( am| pm)?)/", $line, $matches))
		return;

	list($key) = $matches[1];
	list($value) = $matches[2];

	if(!in_array($key, array("station", "date", "time", "manager")))
		return;

	if(HistoryReport::instance()->__status == "opening")
	{
		if($key == "station")
			HistoryReport::instance()->station = intVal($value);

		if($key == "manager")
			HistoryReport::instance()->manager_id = intVal($value);

		if($key == "date")
			HistoryReport::instance()->date = format_date($value);

		if($key == "time")
			HistoryReport::instance()->time = format_time($value);

		if(HistoryReport::instance()->__number == 2)
		{
			HistoryReport::instance()->__status = "open";
			HistoryReport::instance()->__number = 0;
			return HistoryReport::instance()->open();
		}

		return HistoryReport::instance()->__number++;
	}
});

class HistoryReport extends Model
{
	public static $instance = null;
	protected static $type = null;
	protected static $counter = 0;

	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"report_datetime",
								"manager_id",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");
	public $id;
	public $file_processing_detail_id;
	public $station;
	public $report_datetime;
	public $manager_id;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function open()
	{
		$this->report_datetime = implode(" ", array($this->date, $this->time));

		DB::insert("history_report", $this->toArray(), $this->id);
	}

	public function close()
	{

	}

	public function completed()
	{
	}

	public static function starting($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(History Report)[ ]+$/", $str, $matches);
	}

	public static function ending($str)
	{
		$matches = array();
		return preg_match_all("/([\=]+)/", $str, $matches);
	}
}