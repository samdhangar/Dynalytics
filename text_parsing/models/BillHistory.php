<?php

Hook::sub("subscription", function($line){

	if($bt = BillHistory::isBegin($line))
	{
		BillHistory::instance()->bill_type_id = BillType::getId($bt);
		BillHistory::instance()->__status = "in_progress";
		return BillHistory::instance()->bill_type_fulltext = $bt;
	}

	if(!BillHistory::has())
		return;

	if(BillHistory::isEnd($line))
	{
		BillHistory::instance()->completed();
		return BillHistory::destroy();
	}

	if(BillHistory::instance()->__status != "in_progress")
		return;

	$line = strtolower($line);
	$matches = array();
	if(preg_match_all("/([A-Za-z0-9]*?)[ #]+([0-9\/:]+( am| pm)?)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);

		if($key == "transaction")
			BillHistory::instance()->trans_number = $key;

		if($key == "date")
			BillHistory::instance()->date = format_date($value);

		if($key == "time")
			BillHistory::instance()->time = format_time($value);

		if($key == "manager")
			BillHistory::instance()->manager_id = intVal($value);

		if($key == "station")
			BillHistory::instance()->station = intVal($value);
	}
	else if(preg_match_all("/[-]+/", $line, $matches))
	{
		BillHistory::instance()->__status = "header_ok";
		BillHistory::instance()->headerReady();
		return;
	}
});

class BillHistory extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"trans_datetime",
								"manager_id",
								"bill_type_id",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $trans_datetime;
	public $manager_id;
	public $bill_type_id;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function headerReady()
	{
		$this->trans_datetime = implode(" ", array($this->date, $this->time));

		DB::insert("bills_history", $this->toArray(), $this->id);
	}

	public function completed()
	{

	}

	public static function isBegin($subject)
	{
		$matches = array();
		if(!preg_match_all("/^[ ]+(([A-Za-z]+ Bills) (History))[ ]+$/", $subject, $matches))
			return;

		return array_shift($matches[2]);
	}

	public static function isEnd($subject)
	{
		$matches = array();
		return preg_match_all("/(=)+/", $subject, $matches);
	}
}