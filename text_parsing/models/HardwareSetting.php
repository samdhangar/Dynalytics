<?php

Hook::sub("subscription", function($line)
{
	$matches = array();
	if(HardwareSetting::isStart($line))
	{	
		$name = 'hardware_setting';
		$section_id = HardwareSetting::getSectionId(HardwareSetting::instance()->File_id, $name);
		HardwareSetting::instance()->Section = $section_id;

		return HardwareSetting::instance();
	}

	if(!HardwareSetting::has())
		return;

	if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z0-9]( |))*)/", strtolower($line), $matches))
	{
		if ($matches[1][0] == 'station')
		{
			if (!UserSettings::instance()->Station) {
				UserSettings::instance()->Station = $matches[3][0];
			}
		}else if($matches[1][0] == 'branch'){
			if (!UserSettings::instance()->Branch) {
				UserSettings::instance()->Branch = $matches[3][0];
			}
		}else if($matches[1][0] == 'region'){
			if (!UserSettings::instance()->Region) {
				UserSettings::instance()->Region = $matches[3][0];
			}
		}
	}

	if(preg_match_all("/(([A-Za-z\/-])*)+[ ]+(([A-Za-z0-9.])*)/", strtolower($line), $matches)){
		if ($matches[0][0] == 'tcd/tcr address') 
		{
			HardwareSetting::instance()->tcd_tcr_address = $matches[3][1];
		}
		if ($matches[0][0] == 'tcd/tcr port') 
		{
			HardwareSetting::instance()->tcd_tcr_port = $matches[3][1];
		}
		if ($matches[0][0] == 'tcd/tcr connection') 
		{
			HardwareSetting::instance()->tcd_tcr_connection = $matches[3][1]." ".$matches[3][2];
		}
		if ($matches[0][0] == 'coin dispenser') 
		{
			if (!empty($matches[3][2]) || !empty($matches[3][3])) {	
				HardwareSetting::instance()->coin_dispenser = $matches[3][1]." ".$matches[3][2]." ".$matches[3][3];
			}
		}
		if ($matches[0][0] == 'bill discriminator') 
		{
			HardwareSetting::instance()->bill_discriminator = $matches[3][1];
		}
		if ($matches[0][0] == 'coin sorter') 
		{
			HardwareSetting::instance()->coin_sorter = $matches[3][1];
		}
		if ($matches[0][0] == 'use printer') 
		{
			HardwareSetting::instance()->use_printer = $matches[3][1];
		}
		if ($matches[0][0] == 'per-pass max') 
		{
			HardwareSetting::instance()->pre_pass_max_dispense_count = $matches[3][3];
		}
		if ($matches[0][0] == 'transaction end') 
		{
			HardwareSetting::instance()->transaction_end_line_feeds = $matches[3][3];
		}
		if ($matches[0][0] == 'report end') 
		{
			HardwareSetting::instance()->report_end_line_feeds = $matches[3][3];
		}
	}
	
	if(HardwareSetting::isEnd($line))
	{
		HardwareSetting::instance()->completed();			
		return HardwareSetting::destroy();
	}
});

class HardwareSetting extends Model
{
	public static $instance = null;

	public $flag = -1;

	protected $fields = array("Id",
						"File_id",
						"Section",
						"station",
						"branch",
						"region",
						"tcd_tcr_address",
						"tcd_tcr_port",
						"tcd_tcr_connection",
						"coin_dispenser",
						"bill_discriminator",
						"coin_sorter",
						"use_printer",
						"pre_pass_max_dispense_count",
						"transaction_end_line_feeds",
						"report_end_line_feeds",
						"created_date"
					);
	
	public $Id;
	public $File_id;
	public $Section;
	public $station;
	public $branch;
	public $region;
	public $tcd_tcr_address;
	public $tcd_tcr_port;
	public $tcd_tcr_connection;
	public $coin_dispenser;
	public $bill_discriminator;
	public $coin_sorter;
	public $use_printer;
	public $pre_pass_max_dispense_count;
	public $transaction_end_line_feeds;
	public $report_end_line_feeds;
	public $created_dat;
	
	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("hardware_settings", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+Hardware Settings[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}

	public static function getSectionId($file_id,$name)	{
		$db = DB::getInstance();
		$sql = $db->prepare("INSERT INTO  sections (file_id , section , created_date) VALUES('$file_id','$name',now())");
        $sql->execute(); 
        $sql_machine = $db->prepare("SELECT id FROM sections where section='$name' order by id DESC LIMIT 1");
        $sql_machine->execute();
        $section_details = $sql_machine->fetch();

        return $section_details['id'];
	}
}