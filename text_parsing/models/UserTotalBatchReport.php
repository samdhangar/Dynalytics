<?php

Hook::sub("subscription", function($line){		
	
	$matches = array();

	if(UserTotalBatchReport::isStarted($line))
	{
		$name = 'user_report';
		$section_id = UserTotalBatchReport::getSectionId(UserTotalBatchReport::instance()->file_processing_detail_id, $name);
		UserTotalBatchReport::instance()->Section = $section_id;

		if (preg_match_all("/(User)(.*?)(Report)/", strtolower($line), $matches))
		{
			UserTotalBatchReport::instance()->type = $matches[2][0];		
		}		
		return UserTotalBatchReport::instance();
	}

	if(!UserTotalBatchReport::has())
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

	if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
	{
		list($key) = $matches[0];
		if (!empty($matches[0][0]) && !empty($matches[0][1]) && !empty($matches[0][2])) {			
			$date = format_date($matches[0][0]);
			$time = date("H:i:s", strtotime($matches[0][1].":".$matches[0][2]));
			$datetime = $date." ".$time;			
			if (!UserTotalBatchReport::instance()->trans_datetime) 
			{
				UserTotalBatchReport::instance()->trans_datetime = $datetime;
			}
		}	
	}

	if(preg_match_all("/(([A-Za-z])*)/", strtolower($line), $matches))
	{
		if ($matches[0][0] == 'ray' || $matches[0][0] == 'josh')
		{	
			if (!empty($matches[0][0])) {
				$user_id = UserTotalBatchReport::getUserId($matches[0][0]);
				UserTotalBatchReport::instance()->user_id = $user_id;			
			}
		}	
	}

	if (preg_match_all("/\*\*\* (.*?) \*\*\*/", $line, $matches)) 
	{
		if ($matches[1][0] != 'No Transactions For This User') 
		{
			UserTotalBatchReport::instance()->completed();
			UserTotalBatchReport::destroy();
		}
	}

	if (UserTotalBatchReport::instance()->deposit_flag == -1) 
	{
		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
				return;				
			
			$piece_key = "deposit_denom_".$key."_Pieces";
			$value_key = "deposit_denom_".$key."_Value";
			
			UserTotalBatchReport::instance()->$piece_key = intVal($piece);
			UserTotalBatchReport::instance()->$value_key = $value;
		}
	}

	if(preg_match_all("/(([A-Za-z])*)+([ ])+(([A-Za-z0-9])*)/", strtolower($line), $matches))
	{
		if ($matches[0][0] == 'deposit total')
		{	
			UserTotalBatchReport::instance()->deposit_total = $matches[3][1];
			UserTotalBatchReport::instance()->deposit_flag = 1;
		}

		if ($matches[0][0] == 'withdrawal total') {
			UserTotalBatchReport::instance()->withdrawal_total = $matches[3][1];
		}
		if ($matches[2][0] == 'y' || $matches[2][0] == 'l') {
			UserTotalBatchReport::instance()->buy_sell_amount = $matches[3][0];
		}

		if ($matches[0][0] != '' && $matches[0][1] != '') {
			if ($matches[0][0]." ".$matches[0][1] == 'of deposits') 
			{
				UserTotalBatchReport::instance()->hash_of_deposit= $matches[3][2];
			}

			if ($matches[0][0]." ".$matches[0][1] == 'of withdrawals') 
			{
				UserTotalBatchReport::instance()->hash_of_withdrawals= $matches[3][2];
			}
		}
	}

	if (UserTotalBatchReport::instance()->deposit_flag == 1) 
	{
		if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
				return;				
			
			$piece_key = "withdrawals_denom_".$key."_Pieces";
			$value_key = "withdrawals_denom_".$key."_Value";
			
			UserTotalBatchReport::instance()->$piece_key = intVal($piece);
			UserTotalBatchReport::instance()->$value_key = $value;
		}
	}

	if(UserTotalBatchReport::isEnded($line))
	{
		UserTotalBatchReport::instance()->completed();
		UserTotalBatchReport::destroy();
	}
});

class UserTotalBatchReport extends Model
{
	public static $instance = null;

	public $flag = -1;

	public $deposit_flag = -1;

	protected $fields = array("Id",
						"file_processing_detail_id",
						"Section",
						"Station",
						"Branch",
						"Region",
						"trans_datetime",
						"user_id",
						"hash_of_deposit",
						"hash_of_withdrawals",
						"deposit_denom_100_Pieces",						
						"deposit_denom_50_Pieces",						
						"deposit_denom_20_Pieces",						
						"deposit_denom_10_Pieces",						
						"deposit_denom_5_Pieces",						
						"deposit_denom_1_Pieces",							
						"deposit_denom_100_Value",						
						"deposit_denom_50_Value",						
						"deposit_denom_20_Value",						
						"deposit_denom_10_Value",						
						"deposit_denom_5_Value",						
						"deposit_denom_1_Value",
						"deposit_total",
						"withdrawals_denom_100_Pieces",
						"withdrawals_denom_50_Pieces",
						"withdrawals_denom_20_Pieces",
						"withdrawals_denom_10_Pieces",
						"withdrawals_denom_5_Pieces",
						"withdrawals_denom_1_Pieces",	
						"withdrawals_denom_100_Value",
						"withdrawals_denom_50_Value",
						"withdrawals_denom_20_Value",
						"withdrawals_denom_10_Value",
						"withdrawals_denom_5_Value",
						"withdrawals_denom_1_Value",						
						"withdrawal_total",
						"buy_sell_amount",
						"created_date"
					);
	
	public $Id;
	public $file_processing_detail_id;
	public $Section;
	public $Station;
	public $Branch;
	public $Region;
	public $trans_datetime;
	public $user_id;
	public $hash_of_deposit;
	public $hash_of_withdrawals;
	public $deposit_denom_100_Pieces;
	public $deposit_denom_50_Pieces;
	public $deposit_denom_20_Pieces;
	public $deposit_denom_10_Pieces;
	public $deposit_denom_5_Pieces;
	public $deposit_denom_1_Pieces	;
	public $deposit_denom_100_Value;
	public $deposit_denom_50_Value;
	public $deposit_denom_20_Value;
	public $deposit_denom_10_Value;
	public $deposit_denom_5_Value;
	public $deposit_denom_1_Value;
	public $withdrawals_denom_100_Pieces;
	public $withdrawals_denom_50_Pieces;
	public $withdrawals_denom_20_Pieces;
	public $withdrawals_denom_10_Pieces;
	public $withdrawals_denom_5_Pieces;
	public $withdrawals_denom_1_Pieces;
	public $withdrawals_denom_100_Value;
	public $withdrawals_denom_50_Value;
	public $withdrawals_denom_20_Value;
	public $withdrawals_denom_10_Value;
	public $withdrawals_denom_5_Value;
	public $withdrawals_denom_1_Value;
	public $deposit_total;
	public $withdrawal_total;
	public $buy_sell_amount;
	public $created_date;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		DB::insert("user_report", $this->toArray(), $this->id);
	}

	public static function isStarted($str)
	{
		$matches = array();		
		return preg_match_all("/(User)(.*?)(Report)/", $str, $matches);
	}

	public static function isEnded($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
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

	public static function getSectionId($file_processing_detail_id,$name)	{
		$db = DB::getInstance();
		$sql = $db->prepare("INSERT INTO  sections (file_id , section , created_date) VALUES('$file_processing_detail_id','$name',now())");
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