<?php
Hook::sub("subscription", function($line){

	if(VerificationRequired::isStart($line))
	{
		return VerificationRequired::instance();
	}

	if(!VerificationRequired::has())
		return;

	if(VerificationRequired::isEnd($line))
	{
		VerificationRequired::instance()->completed();
		return VerificationRequired::destroy();
	}

	$matches = array();
	if(preg_match_all("/^(Cass\. [1-5](U|L)?)[ ]+([A-Za-z ]+)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[3]);

		$key = str_replace(". ", "_", strtolower($key));

		VerificationRequired::instance()->$key = $value;
	}
	else if(preg_match_all("/^Collection Box[ ]+([A-Za-z ]+)/", $line, $matches))
	{
		VerificationRequired::instance()->collection_box = array_shift($matches[1]);
	}
	else if(preg_match_all("/[\*]{3} ([A-Za-z0-9]+( [A-Za-z0-9]+)+) [\*]{3}/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		VerificationRequired::instance()->message = $value;
	}
});

class VerificationRequired extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"activity_report_id",
								"cass_1",
								"cass_2",
								"cass_3",
								"cass_4u",
								"cass_4l",
								"cass_5",
								"collection_box",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $activity_report_id;
	public $cass_1;
	public $cass_2;
	public $cass_3;
	public $cass_4u;
	public $cass_4l;
	public $cass_5;
	public $collection_box;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("verification_required", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Verification Required)[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}
}