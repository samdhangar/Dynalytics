<?php

Hook::sub("subscription", function($line){

	if(AutomixSettings::isStart($line))
	{
		return AutomixSettings::instance();
	}

	if(!AutomixSettings::has())
		return;

	if(AutomixSettings::isEnd($line))
	{
		if(Tire::has())
		{
			Tire::instance()->completed();
			Tire::destroy();
		}

		AutomixSettings::instance()->completed();
		return AutomixSettings::destroy();
	}

	$matches = array();
	if(preg_match_all("/Denom Weighting:? ([0-9\, ]+)/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		AutomixSettings::instance()->denom_weighting = $value;
	}
	else if(preg_match_all("/^[ ]+Tier ([0-9]+) Mix[ ]+/", $line, $matches))
	{
		if(Tire::has())
		{
			Tire::instance()->completed();
			Tire::destroy();
		}

		$value = array_shift($matches[1]);

		Tire::instance()->tier_type = $value;
	}
	else if(preg_match_all("/^(Tier [0-9A-Za-z \$]+)/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		Tire::instance()->tier_text = $value;
	}
	else if(preg_match_all("/^([0-9]+)[ ]+([0-9]+)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);

		$key = "denom_$key";

		Tire::instance()->$key = $value;
	}
});

class AutomixSettings extends Model
{
	public static $instance = null;
	protected $fields = array();

	public $denom_weighting;

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/[ ]+Automix Settings[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}

	public function completed()
	{

	}
}

class Tire extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"denom_weighting",
								"tier_type",
								"tier_text",
								"denom_100",
								"denom_50",
								"denom_20",
								"denom_10",
								"denom_5",
								"denom_2",
								"denom_1",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $denom_weighting;
	public $tier_type;
	public $tier_text;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->denom_weighting = AutomixSettings::instance()->denom_weighting;
		DB::insert("automix_setting", $this->toArray(), $this->id);
	}
}