<?php

Hook::sub("subscription", function($line){		
	if(ConfigModeAccess::isStarted($line))
	{
		$name = 'config_mode_access';
		$section_id = ConfigModeAccess::getSectionId(ConfigModeAccess::instance()->File_id, $name);
		ConfigModeAccess::instance()->Section = $section_id;

		return ConfigModeAccess::instance();
	}

	if(!ConfigModeAccess::has())
		return;	

	if(ConfigModeAccess::isEnded($line))
	{
		ConfigModeAccess::instance()->flag = 2;
	}

	$enter_matches = array();	

	if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $enter_matches))
	{		
		list($key) = $enter_matches[0];
		if (!empty($enter_matches[0][0]) && !empty($enter_matches[0][1]) && !empty($enter_matches[0][2])) {			
			$date = format_date($enter_matches[0][0]);
			$time = '';
			if (!empty($enter_matches[0][1])) 
			{
				$time = date("H:i:s", strtotime($enter_matches[0][1].":".$enter_matches[0][2]));
			}
			$datetime = $date." ".$time;			
			if (!ConfigModeAccess::instance()->Enter_datetime) {
				ConfigModeAccess::instance()->Enter_datetime = $datetime;
			}
		}	
	}

	if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z])*)/", strtolower($line), $enter_matches)){	
		if ($enter_matches[1][0] == 'logon' || $enter_matches[1][0] == 'id')
		{	
			if (!empty($enter_matches[3][0])) {
				$user_id = ConfigModeAccess::getUserId($enter_matches[3][0]);
				if (!ConfigModeAccess::instance()->Enter_by_user) {
					ConfigModeAccess::instance()->Enter_by_user = $user_id;
				}
			}
		}
	}

	$confiq_mode_activity_matches = array();

	if (preg_match_all("/ *\*\*\* (.*?) \*\*\* */", $line, $confiq_mode_activity_matches)) 
	{
		if ($confiq_mode_activity_matches[1][0] != 'Entered Configuration Mode' && $confiq_mode_activity_matches[1][0] != 'Exited Configuration Mode') 
		{			
			ConfigModeActivity::instance()->Action = $confiq_mode_activity_matches[1][0];
			ConfigModeActivity::instance()->setflag = 2;

			$name = 'config_mode_activity';
			$section_id = ConfigModeAccess::getSectionId(ConfigModeAccess::instance()->File_id, $name);
			ConfigModeActivity::instance()->Section = $section_id;
		}
	}

	if (ConfigModeActivity::instance()->setflag >= 0 && ConfigModeActivity::instance()->setflag <=2) {

		$confiq_mode_activity_matches_date = array();

		if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $confiq_mode_activity_matches_date))
		{
			list($key) = $confiq_mode_activity_matches_date[0];
			$action_date_time ='';
			if ($confiq_mode_activity_matches_date[0]) 
			{			# code...
				$date = format_date($confiq_mode_activity_matches_date[0][0]);			
				$time = '';
				if (!empty($confiq_mode_activity_matches_date[0][1])) 
				{
					$time = date("H:i:s", strtotime($confiq_mode_activity_matches_date[0][1].":".$confiq_mode_activity_matches_date[0][2]));
				}
				$action_date_time = $date." ".$time;	
			}			
			if (!ConfigModeActivity::instance()->action_datetime) {
				ConfigModeActivity::instance()->action_datetime = $action_date_time;
			}
		}

		$confiq_mode_activity_matches_id = array();

		if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z0-9])*)/", strtolower($line), $confiq_mode_activity_matches_id)){		

			if ($confiq_mode_activity_matches_id[1][0] == 'id')
			{
				if (!empty($confiq_mode_activity_matches_id[3][0])) {
					$user_id = ConfigModeAccess::getUserId($confiq_mode_activity_matches_id[3][0]);
					if (!ConfigModeActivity::instance()->Action_affected_id) {
						ConfigModeActivity::instance()->Action_affected_id = $user_id;
					}
				}
			}	
		}

		ConfigModeActivity::instance()->setflag = ConfigModeActivity::instance()->setflag - 1;
		
		if (ConfigModeActivity::instance()->setflag < 0) {
			ConfigModeActivity::instance()->completed();
			$id = ConfigModeActivity::instance()->id;
			array_push(ConfigModeAccess::instance()->activity_id, $id);
			return ConfigModeActivity::destroy();
		}		
	}		

	$exit_matches = array();
	if (ConfigModeAccess::instance()->flag >= 0 && ConfigModeAccess::instance()->flag <=2) {		
		if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $exit_matches))
		{
			list($key) = $exit_matches[0];
			$date = format_date($exit_matches[0][0]);
			$time = '';
			if (!empty($exit_matches[0][1])) {
				$time = date("H:i:s", strtotime($exit_matches[0][1].":".$exit_matches[0][2]));
			}
			$exitdatetime = $date." ".$time;

			if (!ConfigModeAccess::instance()->Exit_datetime) {
				ConfigModeAccess::instance()->Exit_datetime = $exitdatetime;
			}
		}

		if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z0-9])*)/", strtolower($line), $exit_matches)){	
			if ($exit_matches[1][0] == 'id')
			{
				if (!ConfigModeAccess::instance()->Exit_by_user) {
					if (!empty($exit_matches[3][0])) {
						$user_id = ConfigModeAccess::getUserId($exit_matches[3][0]);
						if (!ConfigModeAccess::instance()->Exit_by_user) {
							ConfigModeAccess::instance()->Exit_by_user = $user_id;
						}
					}
				}
			}	
		}

		ConfigModeAccess::instance()->flag = ConfigModeAccess::instance()->flag - 1;
		if (ConfigModeAccess::instance()->flag < 0) {
			ConfigModeAccess::instance()->completed();
			$access_id = ConfigModeAccess::instance()->id;

			ConfigModeAccess::updateAccessID(ConfigModeAccess::instance()->activity_id, $access_id);

			return ConfigModeAccess::destroy();
		}
	}
});

class ConfigModeAccess extends Model
{
	public static $instance = null;

	public $flag = -1;

	public $activity_id = array();

	protected $fields = array("Id",
						"File_id",
						"Section",
						"Enter_datetime",
						"Enter_by_user",
						"Exit_datetime",
						"Exit_by_user");
	
	public $Id;
	public $File_id;
	public $Section;
	public $Enter_datetime;
	public $Enter_by_user;
	public $Exit_datetime;
	public $Exit_by_user;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("config_mode_access", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();		
		return preg_match_all("/ \*\*\* (Entered Configuration Mode) \*\*\*/", $str, $matches);
	}

	public static function isEnded($str)
	{
		$matches = array();
		return preg_match_all("/ \*\*\* (Exited Configuration Mode) \*\*\*/", $str, $matches);
	}

	public static function getUserId($user_name)
	{
		$db = DB::getInstance();
		$user_id_query = $db->prepare("SELECT id FROM machine_users where user_name='$user_name'");
        $user_id_query->execute();
        $user_details = $user_id_query->fetch();
        if (!$user_details) {
        	$sql = $db->prepare("INSERT INTO  machine_users (user_name , status , created) VALUES('$user_name','active',now())");
            $sql->execute(); 
            $sql_machine = $db->prepare("SELECT id FROM machine_users where user_name='$user_name'");
            $sql_machine->execute();
            $user_details = $sql_machine->fetch();
        }
        return $user_details['id'];
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

	public static function updateAccessID($activity_id, $access_id)
	{
		$db = DB::getInstance();
		if (!empty($activity_id)) {
			foreach ($activity_id as $key => $value) {				
				$sql = $db->prepare("UPDATE config_mode_activity SET config_mode_access_id=$access_id WHERE id=$value");
	        	$sql->execute(); 
			}
		}
	}
}


class ConfigModeActivity extends Model
{
	public static $instance = null;

	public $setflag = -1;

	protected $fields = array("Id",
						"File_id",
						"Config_mode_access_id",
						"Action",
						"action_datetime",
						"Action_affected_id");
	
	public $Id;
	public $File_id;
	public $Config_mode_access_id;
	public $Action;
	public $action_datetime;
	public $Action_affected_id;
	public $created_at;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_at = date("Y-m-d H:i:s");
	}

	public function completed()
	{		
		DB::insert("config_mode_activity", $this->toArray(), $this->id);
	}	
}