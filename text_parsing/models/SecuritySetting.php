<?php
Hook::sub("subscription", function($line){
	if(SecuritySetting::isStart($line))
	{	
		$name = 'security_setting';
		$section_id = SecuritySetting::getSectionId(SecuritySetting::instance()->File_id, $name);
		SecuritySetting::instance()->Section = $section_id;

		return SecuritySetting::instance();
	}

	if(!SecuritySetting::has())
		return;	
	
	if(preg_match_all("/(([A-Za-z])*):+[ ]+(([A-Za-z0-9]( |))*)/", strtolower($line), $matches))
	{
		if ($matches[1][0] == 'station')
		{
			if (!SecuritySetting::instance()->Station) {
				SecuritySetting::instance()->Station = $matches[3][0];
			}
		}else if($matches[1][0] == 'branch'){
			if (!SecuritySetting::instance()->Branch) {
				SecuritySetting::instance()->Branch = $matches[3][0];
			}
		}else if($matches[1][0] == 'region'){
			if (!SecuritySetting::instance()->Region) {
				SecuritySetting::instance()->Region = $matches[3][0];
			}
		}
	}	

	if(preg_match_all("/(([A-Za-z0-9])*)+[ ]+(([A-Za-z0-9])*)+[ ]+\(([^)]+)\)/", strtolower($line), $matches)){
		if ($matches[0][0] == 'vault times (24 hour format)') {
			SecuritySetting::instance()->vault_flag = 1; 
			$name = 'valut_time';
			$section_id = SecuritySetting::getSectionId(SecuritySetting::instance()->File_id, $name);
			ValutTime::instance()->Section = $section_id;
		}
	}

	if (SecuritySetting::instance()->vault_flag == 1) 
	{
		if(preg_match_all("/(([A-Za-z0-9])*)+[ ]+(([A-Za-z0-9])*)/", strtolower($line), $matches)){
			if ($matches[3][0] == 'start' && $matches[3][1] == 'end') 
			{
				ValutTime::instance()->start_end_flag = 1;
			}
		}
	}

	if (ValutTime::instance()->start_end_flag == 1) 
	{
		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+:+[0-9]*)[ ]+([0-9]+:+[0-9]*)/", strtolower($line), $matches)){			
			$key = $matches[1][0];
			$start = $matches[2][0];
			$end = $matches[3][0];

			if(!in_array($key, array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday")))
				return;

			$day_star = $key."_start";
			$day_end = $key."_end";

			ValutTime::instance()->$day_star = $start;
			ValutTime::instance()->$day_end = $end;
		}

		if (preg_match_all("/[-]+/", $line, $matches)) {		
			ValutTime::instance()->start_end_flag =2;		
		}
	}

	if (ValutTime::instance()->start_end_flag == 2) {
		ValutTime::instance()->completed();
		$id = ValutTime::instance()->id;		
		array_push(SecuritySetting::instance()->vault_id_array, $id);		
		ValutTime::instance()->start_end_flag == 0;	
		return ValutTime::destroy(); 	
	}	
	if (preg_match_all("/^[ ]+duress settings/", strtolower($line), $matches)) {		
		SecuritySetting::instance()->duress_setting_flag = 1;
		$name = 'duress_settings';
		$section_id = SecuritySetting::getSectionId(SecuritySetting::instance()->File_id, $name);
		DuressSetting::instance()->Section = $section_id;
	}

	if (SecuritySetting::instance()->duress_setting_flag == 1) {
		if(preg_match_all("/([A-Za-z]+[ ]+([A-Za-z])+[ ]+([A-Za-z])*)+[ ]+(([A-Za-z0-9])*)/", $line, $matches)){			

			if ($matches[1][0] == "Use Duress Lockout")
			{
				if (!DuressSetting::instance()->Duress_lockout) {
					DuressSetting::instance()->Duress_lockout = $matches[4][0];
				}
			}else if($matches[1][0] == "Use DynaCore Alarm"){
				if (!DuressSetting::instance()->Dynacore_alarm) {
					DuressSetting::instance()->Dynacore_alarm = $matches[4][0];
				}
			}else if($matches[1][0] == "Use Machine Alarm"){
				if (!DuressSetting::instance()->Machine_alarm) {
					DuressSetting::instance()->Machine_alarm = $matches[4][0];
				}
			}else if($matches[1][0] == "Duress Lump Sum"){
				if (!DuressSetting::instance()->Duress_lumpsum) {
					DuressSetting::instance()->Duress_lumpsum = $matches[4][0];
				}
			}
		}

		if(preg_match_all("/([A-Za-z]+[ ])+[ ]+(([A-Za-z0-9])*)/", $line, $matches))
		{			
			if(trim($matches[1][0]) == "AutoLogoff"){				
				if (!DuressSetting::instance()->AutoLogoff) {					
					DuressSetting::instance()->AutoLogoff = $matches[2][0];
				}	
			}
		}

		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			DuressSetting::instance()->__number = 0;
			
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
				return;				
			
			$piece_key = "Denom_".$key."_Pieces";
			$value_key = "Denom_".$key."_Value";
			
			DuressSetting::instance()->$piece_key = intVal($piece);
			DuressSetting::instance()->$value_key = $value;			
		}
	}


	if(SecuritySetting::isEnd($line))
	{
		SecuritySetting::instance()->completed();
		$security_id = SecuritySetting::instance()->id;
		SecuritySetting::updateSecurityID(SecuritySetting::instance()->vault_id_array, $security_id);
		SecuritySetting::instance()->vault_flag = 0;

		DuressSetting::instance()->Security_settings_id = $security_id;

		DuressSetting::instance()->completed();

		SecuritySetting::instance()->duress_setting_flag = 0;

		DuressSetting::destroy();

		return SecuritySetting::destroy(); 
	}
});

class SecuritySetting extends Model
{
	public static $instance = null;	

	public $vault_id_array = array();

	public $vault_flag = 0;

	public $duress_setting_flag = 0;

	protected $fields = array("Id",
							"File_id",
							"Section",
							"Station",
							"Branch",
							"Region");
	
	public $Id;
	public $File_id;
	public $Section;
	public $Station;
	public $Branch;
	public $Region;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("security_settings", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();	
		return preg_match_all("/^[ ]+Security Settings[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}

	public static function updateSecurityID($vault_id_array, $security_id){	

		$db = DB::getInstance();
		if (!empty($vault_id_array)) {
			foreach ($vault_id_array as $key => $value) {				
				$sql = $db->prepare("UPDATE vault_time SET security_settings_id=$security_id WHERE id=$value");
	        	$sql->execute(); 
			}
		}
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

class ValutTime extends Model
{
	public static $instance = null;

	public $start_end_flag = 0;

	protected $fields = array("Id",
							"File_id",
							"Security_settings_id",
							"Section",
							"monday_start",
							"monday_end",
							"tuesday_start",
							"tuesday_end",
							"wednesday_start",
							"wednesday_end",
							"thursday_start",
							"thursday_end",
							"friday_start",
							"friday_end",
							"saturday_start",
							"saturday_end",
							"sunday_start",
							"sunday_end");
	
	public $Id;
	public $File_id;
	public $Security_settings_id;
	public $Section;
	public $monday_start;
	public $monday_end;
	public $tuesday_start;
	public $tuesday_end;
	public $wednesday_start;
	public $wednesday_end;
	public $thursday_start;
	public $thursday_end;
	public $friday_start;
	public $friday_end;
	public $saturday_start;
	public $saturday_end;
	public $sunday_start;
	public $sunday_end;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("vault_time", $this->toArray(), $this->id);
	}	
}

class DuressSetting extends Model
{
	public static $instance = null;	

	protected $fields = array("Id",
							"File_id",
							"Security_settings_id",
							"Section",
							"Duress_lockout",
							"Dynacore_alarm",
							"Machine_alarm",
							"AutoLogoff",
							"Duress_lumpsum",
							"Denom_100_Pieces",
							"Denom_50_Pieces",
							"Denom_20_Pieces",
							"Denom_10_Pieces",
							"Denom_5_Pieces",
							"Denom_1_Pieces",	
							"Denom_100_Value",
							"Denom_50_Value",
							"Denom_20_Value",
							"Denom_10_Value",
							"Denom_5_Value",
							"Denom_1_Value");
	
	public $Id;
	public $File_id;
	public $Security_settings_id;
	public $Section;
	public $Duress_lockout;
	public $Dynacore_alarm;
	public $Machine_alarm;
	public $AutoLogoff;
	public $Duress_lumpsum;
	public $Denom_100_Pieces;
	public $Denom_50_Pieces;
	public $Denom_20_Pieces;
	public $Denom_10_Pieces;
	public $Denom_5_Pieces;
	public $Denom_1_Pieces	;
	public $Denom_100_Value;
	public $Denom_50_Value;
	public $Denom_20_Value;
	public $Denom_10_Value;
	public $Denom_5_Value;
	public $Denom_1_Value;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("duress_settings", $this->toArray(), $this->id);
	}	
}