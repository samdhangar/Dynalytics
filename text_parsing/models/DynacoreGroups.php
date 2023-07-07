<?php

Hook::sub("subscription", function($line){		
	if(DynacoreGroups::isStarted($line))
	{
		$name = 'dynacore_groups';
		$section_id = DynacoreGroups::getSectionId(DynacoreGroups::instance()->File_id, $name);
		DynacoreGroups::instance()->Section = $section_id;

		return DynacoreGroups::instance();
	}

	if(!DynacoreGroups::has())
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
			if (!DynacoreGroups::instance()->report_datetime) {
				DynacoreGroups::instance()->report_datetime = $datetime;
			}
		}	
	}
	$key = '';
	if(preg_match_all("/([A-Za-z()]+(( |-)([A-Za-z()]+|[0-9]*))*)/", strtolower($line), $matches))
	{
		$key = array_shift($matches[0]);		
		if(in_array($key, array("group 0", "group 1", "group 2", "group 3", "group 4", "group 5", "group 6", "group 7", "group 8", "group 9", "group 10", "group 11", "group 12", "group 13", "group 14", "group 15", "dynacoreteller", "dynacoremanager", "dynacoreadmin", "magner tech","master", "manager", "test", "teller")))
		{
			DynacoreGroups::instance()->flag = 1;
			
			$name = 'dynacore_groups_details';
			$section_id = DynacoreGroups::getSectionId(DynacoreGroups::instance()->File_id, $name);
			DynacoreGroupsDetails::instance()->Section = $section_id;

			DynacoreGroupsDetails::instance()->Group_name = $key;
		}

		if (DynacoreGroups::instance()->flag ==1) 
		{
			if(preg_match_all("/([A-Za-z()]+(( |-)([A-Za-z()]+|[0-9]))*)/", strtolower($line), $matches))
			{
				$key = array_shift($matches[0]);

				if (!in_array($key, array("group 0", "group 1", "group 2", "group 3", "group 4", "group 5", "group 6", "group 7", "group 8", "group 9", "group 10", "group 11", "group 12", "group 13", "group 14", "group 15", "dynacoreteller", "dynacoremanager", "dynacoreadmin", "magner tech", "master", "manager", "test", "teller"))) 
				{
					if ( $key != '' && $matches[1][0] != 'no enabled functions' && $key != 'station' && $key != 'branch' && $key != 'region' && $key != 'am' && $key != "transaction limit" && $key != "daily limit"  ) 
					{
						$function_id = DynacoreGroups::getFunctionId($key);
						DynacoreGroupsDetails::instance()->allowed_function = DynacoreGroupsDetails::instance()->allowed_function.",".$function_id;
					}

					if ($key == 'transaction limit') 
					{
						DynacoreGroupsDetails::instance()->Tansaction_limit = $matches[1][1];
					}
					if ($key == 'daily limit') 
					{
						DynacoreGroupsDetails::instance()->Daily_limit = $matches[1][1];
						DynacoreGroups::instance()->flag = 0;

						DynacoreGroupsDetails::instance()->completed();
						
						$id = DynacoreGroupsDetails::instance()->id;
						array_push(DynacoreGroups::instance()->group_detail_id, $id);

						DynacoreGroupsDetails::destroy();
					}			
				}
			}
		}	

	}

	if(DynacoreGroups::isEnded($line))
	{
		DynacoreGroups::instance()->completed();

		$group_id = DynacoreGroups::instance()->id;

		DynacoreGroups::updateGroupID(DynacoreGroups::instance()->group_detail_id, $group_id);

		DynacoreGroups::destroy();
	}
});

class DynacoreGroups extends Model
{
	public static $instance = null;

	public $flag = 0;
	
	public $group_detail_id = array();
	
	protected $fields = array("Id",
						"File_id",
						"Section",
						"Station",
						"Branch",
						"Region",
						"report_datetime",
						"created_date");

	public $Id;
	public $File_id;
	public $Section;
	public $Station;
	public $Branch;
	public $Region;
	public $report_datetime;
	public $created_date;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("dynacore_groups", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();	
		return preg_match_all("/^[ ]+DynaCore Groups[ ]+/", $str, $matches);
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

	public static function getFunctionId($function_name)
	{
		$db = DB::getInstance();
		$user_id_query = $db->prepare("SELECT id FROM dynaCore_functions where function_name='$function_name'");
        $user_id_query->execute();
        $user_details = $user_id_query->fetch();
        if (!$user_details) {
        	$sql = $db->prepare("INSERT INTO  dynaCore_functions (function_name, created) VALUES('$function_name', now())");
            $sql->execute(); 
            $sql_machine = $db->prepare("SELECT id FROM dynaCore_functions where function_name='$function_name'");
            $sql_machine->execute();
            $user_details = $sql_machine->fetch();
        }
        return $user_details['id'];
	}

	public static function updateGroupID($group_detail_id, $group_id)
	{
		$db = DB::getInstance();
		if (!empty($group_detail_id)) {
			foreach ($group_detail_id as $key => $value) {				
				$sql = $db->prepare("UPDATE dynacore_groups_details SET dynacore_group_id=$group_id WHERE id=$value");
	        	$sql->execute(); 
			}
		}
	}

}

class DynacoreGroupsDetails extends Model
{
	public static $instance = null;

	public $flag = 0;
	
	protected $fields = array("Id",
							"File_id",
							"Section",
							"Dynacore_group_id",
							"Group_name",
							"allowed_function",
							"Tansaction_limit",
							"Daily_limit",
							"created_date");

	public $Id;
	public $File_id;
	public $Section;
	public $Dynacore_group_id;
	public $Group_name;
	public $allowed_function;
	public $Tansaction_limit;
	public $Daily_limit;
	public $created_date;

	public function __construct()
	{
		$this->File_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("dynacore_groups_details", $this->toArray(), $this->id);
	}
}