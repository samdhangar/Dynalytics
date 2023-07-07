<?php

Hook::sub("subscription", function($line){		
	if(UserSettings::isStarted($line))
	{
		$name = 'user_settings';
		$section_id = UserSettings::getSectionId(UserSettings::instance()->File_id, $name);
		UserSettings::instance()->Section = $section_id;

		return UserSettings::instance();
	}

	if(!UserSettings::has())
		return;	
	$matches = array();

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

	if(preg_match_all("/(([A-Za-z])*)+[ ]+(([A-Za-z0-9])*)/", strtolower($line), $matches))
	{
		if (!empty($matches[3][1])) {
			if ($matches[3][0]." ".$matches[3][1] == 'active directory')
			{
				UserSettings::instance()->active_directory = $matches[3][2];
			}
			if ($matches[3][0]." ".$matches[3][1] == 'batch totals')
			{
				UserSettings::instance()->batch_total = $matches[3][2];
			}
		}

		if ($matches[0][0] == 'ibutton mode')
		{
			UserSettings::instance()->ibutton_mode = trim($matches[0][1]," ")." ".$matches[0][2];
		}
	}

	if(UserSettings::isEnded($line))
	{
		UserSettings::instance()->completed();
		UserSettings::destroy();
	}
});

class UserSettings extends Model
{
	public static $instance = null;
	
	protected $fields = array("Id",
						"File_id",
						"Section",
						"Station",
						"Branch",
						"Region",
						"active_directory",
						"batch_total",
						"ibutton_mode",
						"created_date");

	public $Id;
	public $File_id;
	public $Section;
	public $Station;
	public $Branch;
	public $Region;
	public $active_directory;
	public $batch_total;
	public $ibutton_mode;
	public $created_date;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("user_settings", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();	
		return preg_match_all("/^[ ]+User Settings[ ]+/", $str, $matches);
	}

	public static function isEnded($str)
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