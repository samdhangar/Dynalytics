<?php

Hook::sub("subscription", function($line)
{

	$matches = array();
	if(AlertEvent::isStarted($line))
	{		
		if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z])*)/", strtolower($line), $matches)){	
			if ($matches[1][0] == 'logon')
			{	
				if (!empty($matches[3][0])) {
					$user_id = AlertEvent::getUserId($matches[3][0]);					
					AlertEvent::instance()->user_login_id = $user_id;					
				}
			}
		}

		$name = 'alert_event';
		$section_id = AlertEvent::getSectionId(AlertEvent::instance()->File_id, $name);
		AlertEvent::instance()->Section = $section_id;

		return AlertEvent::instance();
	}

	if(!AlertEvent::has())
		return;

	if (AlertEvent::instance()->flag == -1 && $line != '') 
	{
		if (preg_match_all("/\*\*\* (.*?) \*\*\*/", $line, $matches)) 
		{
			AlertEvent::instance()->type = $matches[1][0];
			AlertEvent::instance()->flag = 2;
		}
	}
	if (AlertEvent::instance()->flag >= 0 && AlertEvent::instance()->flag <= 2) 
	{
		if (AlertEvent::instance()->flag == 1) 
		{
			if (preg_match_all("/\*\*\* (.*?) \*\*\*/", $line, $matches)) 
			{
				AlertEvent::instance()->message = $matches[1][0];
				AlertEvent::instance()->completed();
				AlertEvent::instance()->flag = -1;
			}
			if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
			{
				$date = format_date($matches[0][0]);
				$time = '';
				if (!empty($matches[0][1])) 
				{
					$time = date("H:i:s", strtotime($matches[0][1].":".$matches[0][2]));
				}
				$alert_date_time = $date." ".$time;	
				AlertEvent::instance()->datetime = $alert_date_time;		
				AlertEvent::instance()->flag = AlertEvent::instance()->flag -1;
			}
		}
		if (AlertEvent::instance()->flag == 0)
		{			
			if (preg_match_all("/\*\*\* (.*?) \*\*\*/", $line, $matches)) 
			{				
				AlertEvent::instance()->message = $matches[1][0];
				AlertEvent::instance()->completed();
				AlertEvent::instance()->flag = -1;
			}
		}
		if (AlertEvent::instance()->flag == 2)
		{
			AlertEvent::instance()->flag = AlertEvent::instance()->flag - 1;
		}
	}	
	if(AlertEvent::isEnded($line))
	{			
		return AlertEvent::destroy();
	}
});

class AlertEvent extends Model
{
	public static $instance = null;

	public $flag = -1;

	protected $fields = array("Id",
						"File_id",
						"Section",
						"user_login_id",
						"type",
						"message",
						"datetime",
						"created_date");
	
	public $Id;
	public $File_id;
	public $Section;
	public $user_login_id;
	public $type;
	public $message;
	public $datetime;
	public $created_date;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("alert_event", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();
		return preg_match_all("/logon:+[ ]+(([A-Za-z])*)/", strtolower($str), $matches);
	}

	public static function isEnded($str)
	{
		$matches = array();
		return preg_match_all("/logoff:+[ ]+(([A-Za-z])*)/", strtolower($str), $matches);
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
}


