<?php
Hook::sub("subscription", function($line){
	if(InventorySetting::isStart($line))
	{	
		$name = 'inventory_setting';
		$section_id = InventorySetting::getSectionId(InventorySetting::instance()->File_id, $name);
		InventorySetting::instance()->Section = $section_id;

		return InventorySetting::instance();
	}

	if(!InventorySetting::has())
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

	if (preg_match_all("/^[ ]+AutoMix Settings[ ]+/", $line, $matches)) {
		InventorySetting::instance()->automix_settings_flag = 1;

		$name = 'automix_setting';
		$section_id = InventorySetting::getSectionId(InventorySetting::instance()->File_id, $name);
		AutomixSetting::instance()->Section = $section_id;
	}

	if (InventorySetting::instance()->automix_settings_flag == 1) {		
		if(preg_match_all("/[ ]+((([A-Za-z])*)+[ ])*([A-Za-z])*/", strtolower($line), $matches)){
			if (trim($matches[0][0]) == "low mid high weight odds") {
				InventorySetting::instance()->automix_settings_details_flag = 1;
			}			
		}
	}

	if (InventorySetting::instance()->automix_settings_details_flag == 1) {
		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]*)[ ]+([0-9]*)[ ]+([0-9]*)[ ]+([0-9]*)[ ]+(([A-Za-z0-9])*)/", strtolower($line), $matches))
		{
			AutomixSetting::instance()->denom = $matches[1][0];						
			AutomixSetting::instance()->low = $matches[2][0];						
			AutomixSetting::instance()->mid = $matches[3][0];						
			AutomixSetting::instance()->high = $matches[4][0];						
			AutomixSetting::instance()->weight = $matches[5][0];						
			AutomixSetting::instance()->odds = $matches[6][0];
			
			AutomixSetting::instance()->completed();
			
			$id = AutomixSetting::instance()->id;				
			array_push(InventorySetting::instance()->automix_id_array, $id);

			return AutomixSetting::destroy(); 
		}

		if (preg_match_all("/[-]+/", $line, $matches)) {		
			InventorySetting::instance()->automix_settings_details_flag = 0;
		}
	}

	if (preg_match_all("/^[ ]+Warnings\/Bundling[ ]+/", $line, $matches)) {
		InventorySetting::instance()->inventory_warn_bundle_flag = 1;

		$name = 'warning_bundles';
		$section_id = InventorySetting::getSectionId(InventorySetting::instance()->File_id, $name);
		InventoryWarnBundle::instance()->Section = $section_id;
	}

	if (InventorySetting::instance()->inventory_warn_bundle_flag == 1) {		
		if(preg_match_all("/[ ]+((([A-Za-z])*)+[ ])*([A-Za-z])*/", strtolower($line), $matches)){
			if (trim($matches[0][0]) == "low high teller manager") {
				InventorySetting::instance()->inventory_warn_bundle_details_flag = 1;
			}			
		}
	}

	if (InventorySetting::instance()->inventory_warn_bundle_details_flag == 1) {
		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]*)[ ]+([0-9]*)[ ]+([0-9]*)[ ]+([0-9]*)/", strtolower($line), $matches))
		{
			InventoryWarnBundle::instance()->denom = $matches[1][0];						
			InventoryWarnBundle::instance()->warn_low = $matches[2][0];						
			InventoryWarnBundle::instance()->warn_high = $matches[3][0];						
			InventoryWarnBundle::instance()->bundle_teller = $matches[4][0];						
			InventoryWarnBundle::instance()->bundle_manager = $matches[5][0];
			
			InventoryWarnBundle::instance()->completed();				
			$id = InventoryWarnBundle::instance()->id;

			array_push(InventorySetting::instance()->inventory_bundle_id_array, $id);

			return InventoryWarnBundle::destroy(); 
		}

		if (preg_match_all("/[-]+/", $line, $matches)) {		
			InventorySetting::instance()->inventory_warn_bundle_details_flag = 0;
		}
	}

	if(InventorySetting::isEnd($line))
	{
		InventorySetting::instance()->completed();

		$security_id = InventorySetting::instance()->id;
		InventorySetting::updateSecurityID(InventorySetting::instance()->automix_id_array, $security_id);

		InventorySetting::updateInventoryBundleSecurityID(InventorySetting::instance()->inventory_bundle_id_array, $security_id);

		InventorySetting::instance()->automix_settings_flag = 0;

		InventorySetting::instance()->inventory_warn_bundle_flag = 0;

		return InventorySetting::destroy(); 
	}
});

class InventorySetting extends Model
{
	public static $instance = null;

	public $automix_settings_flag = 0;

	public $inventory_warn_bundle_flag = 0;

	public $automix_settings_details_flag = 0;

	public $inventory_warn_bundle_details_flag = 0;

	public $automix_id_array = array();

	public $inventory_bundle_id_array = array();

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
		DB::insert("inventory_settings", $this->toArray(), $this->id);
	}

	public static function isStart($str)
	{
		$matches = array();	
		return preg_match_all("/^[ ]+Inventory Settings[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}

	public static function updateSecurityID($automix_id_array, $security_id)
	{
		$db = DB::getInstance();
		if (!empty($automix_id_array)) {
			foreach ($automix_id_array as $key => $value) {				
				$sql = $db->prepare("UPDATE automix_settings SET inventory_settings_id=$security_id WHERE id=$value");
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

	public static function updateInventoryBundleSecurityID($inventory_bundle_id_array, $security_id)
	{
		$db = DB::getInstance();
		if (!empty($inventory_bundle_id_array)) {
			foreach ($inventory_bundle_id_array as $key => $value) {				
				$sql = $db->prepare("UPDATE inventory_warn_bundle SET inventory_settings_id=$security_id WHERE id=$value");
	        	$sql->execute(); 
			}
		}
	}
}

class AutomixSetting extends Model
{
	public static $instance = null;	

	protected $fields = array("id",
							"File_id",
							"Section",
							"inventory_settings_id",
							"denom",
							"low",
							"mid",
							"high",
							"weight",
							"odds");
	
	public $id;
	public $File_id;
	public $Section;
	public $inventory_settings_id;
	public $denom;
	public $low;
	public $mid;
	public $high;
	public $weight;
	public $odds;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("automix_settings", $this->toArray(), $this->id);
	}	
}

class InventoryWarnBundle extends Model
{
	public static $instance = null;	

	protected $fields = array("id",
							"File_id",
							"Section",
							"inventory_settings_id",
							"denom",
							"warn_low",
							"warn_high",
							"bundle_teller",
							"bundle_manager");
	
	public $id;
	public $File_id;
	public $Section;
	public $inventory_settings_id;
	public $denom;
	public $warn_low;
	public $warn_high;
	public $bundle_teller;
	public $bundle_manager;

	public function __construct()
	{
		 $this->File_id = ProcessingFile::instance()->id;
		 $this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("inventory_warn_bundle", $this->toArray(), $this->id);
	}	
}
