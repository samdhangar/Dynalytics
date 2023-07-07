<?php

Hook::sub("subscription", function($line){		
	if(DynacoreUsers::isStarted($line))
	{		
		$name = 'dynacore_users';
		$section_id = DynacoreUsers::getSectionId(DynacoreUsers::instance()->File_id, $name);
		DynacoreUsers::instance()->Section = $section_id;

		return DynacoreUsers::instance();
	}

	if(!DynacoreUsers::has())
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
	if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
	{		
		list($key) = $matches[0];
		if (!empty($matches[0][0]) && !empty($matches[0][1]) && !empty($matches[0][2])) {			
			$date = format_date($matches[0][0]);
			$time = date("H:i:s", strtotime($matches[0][1].":".$matches[0][2]));
			$datetime = $date." ".$time;			
			if (!DynacoreUsers::instance()->report_datetime) {
				DynacoreUsers::instance()->report_datetime = $datetime;
			}
		}	
	}
	if (DynacoreUsers::instance()->flag == 0){		
		if(preg_match_all("/(([A-Za-z]+[0-9]*)+(( |-)([A-Za-z()]+|[0-9]*))*)/", strtolower($line), $matches))
		{	
			$key = array_shift($matches[0]);
			$key = trim($key);

			if ($key != '' && $key != 'station' && $key != 'branch' && $key != 'region' && $key != 'am' && $key != 'pm' && !in_array($key, array("group 0", "group 1", "group 2", "group 3", "group 4", "group 5", "group 6", "group 7", "group 8", "group 9", "group 10", "group 11", "group 12", "group 13", "group 14", "group 15", "dynacoreteller", "dynacoremanager", "dynacoreadmin", "magner tech"))) 
			{
				DynacoreUsers::instance()->username = $key;
				DynacoreUsers::instance()->flag = 1;
			}
		}
	}

	if (DynacoreUsers::instance()->flag == 1) 
	{
		if(preg_match_all("/([ ]+([A-Za-z]+|[0-9]*))/", strtolower($line), $matches))
		{	
			if (!empty($matches[2][1])) 
			{
				DynacoreUsers::instance()->Group_roles = DynacoreUsers::instance()->Group_roles.",".$matches[2][0]." ".$matches[2][1];
			}else{
				DynacoreUsers::instance()->Group_roles = DynacoreUsers::instance()->Group_roles.",".$matches[2][0];
			}			
		}
	}

	if(preg_match_all("/([ ])/", strtolower($line), $matches))
	{
		if (DynacoreUsers::instance()->flag == 1) 
		{
			if (DynacoreUsers::instance()->username != '' && DynacoreUsers::instance()->Group_roles != '') {
				DynacoreUsers::instance()->completed();
				DynacoreUsers::instance()->username = '';
				DynacoreUsers::instance()->Group_roles = '';
			}
		}
		DynacoreUsers::instance()->flag = 0;
	}

	if(DynacoreUsers::isEnded($line))
	{
		DynacoreUsers::instance()->completed();
		DynacoreUsers::destroy();
	}
});

class DynacoreUsers extends Model
{
	public static $instance = null;

	public $flag  = 0;
	
	protected $fields = array("Id",
						"File_id",
						"Section",
						"Station",
						"Branch",
						"Region",
						"report_datetime",
						"Group_roles",
						"username",
						"created_date");

	public $Id;
	public $File_id;
	public $Section;
	public $Station;
	public $Branch;
	public $Region;
	public $report_datetime;
	public $Group_roles;
	public $username;
	public $created_date;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("dynacore_users", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();	
		return preg_match_all("/^[ ]+DynaCore Users[ ]+/", $str, $matches);
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