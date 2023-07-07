<?php

Hook::sub("subscription", function($line)
{
	$matches = array();
	if (preg_match_all("/ \*\*\*[ ]Cassette ([A-Za-z0-9])*-([A-Za-z0-9])*[ ](NOT Emptied)[ ]\*\*\*/", $line, $matches)) {			
		TempClass::instance()->transaction_message = (string)array_shift($matches[0]);
	}else if(preg_match_all("/^[ ]+Dispense Failure[ ]+/", $line, $matches)){
		TempClass::instance()->transaction_message = (string)array_shift($matches[0]);
	}else if (preg_match_all("/ *\*\*\* (.*?) \*\*\* */", $line, $matches)) 
	{
		if ($matches[1][0] == 'Cassette And Note Removal') {
			TempClass::instance()->transaction_message = (string)array_shift($matches[1]);
		}
	}

	if($tt = TransactionDetails::isBegin($line))
	{
		if ($tt == 'Online Nominated Dispense')
		{
			TransactionDetails::instance()->online_nominated_dispense_flag = 2;
		}

		TransactionDetails::instance()->messages = TransactionDetails::instance()->messages."\n".TempClass::instance()->transaction_message;
		TempClass::destroy();

		TransactionDetails::instance()->trans_type_id = TransactionType::getId($tt);
		/*TransactionDetails::test()->transaction_type=TransactionType::getId($tt);*/
		TransactionDetails::instance()->__status = "begin";
		TransactionDetails::instance()->trans_type_full_text = $tt;

		if(DepositCanceled::getInstance()->is())
		{
			TransactionDetails::instance()->messages = "Deposit Canceled";
			TransactionDetails::instance()->deposit_canceled_flag = 3;
			DepositCanceled::getInstance()->reset();
		}
		elseif(InventoryLoadCancelled::getInstance()->is())
		{
			TransactionDetails::instance()->messages = "Inventory Load Cancelled";
			InventoryLoadCancelled::getInstance()->reset();
		}	
		
		return TransactionDetails::instance()->__number = 0;
	}

	if(!TransactionDetails::has())
		return;

	if(TransactionDetails::isEnd($line))
	{
		TransactionDetails::instance()->close();
		TransactionDetails::instance()->completed();

		if(Manager::instance()->is())
		{
			TransactionDetails::instance()->getTransactionIds(TransactionDetails::instance()->id);
		}
		
		return TransactionDetails::destroy();
	}

	$line = strtolower($line);
	$matches = array();
	
	if(preg_match_all("/([A-Za-z]+(( |-)([A-Za-z]+|[0-9]))*) #?[ ]+(([0-9\/\.:]+( pm| am)?)|\n)/", $line, $matches))
	{
		TransactionDetails::instance()->__number = 0;

		$key = array_shift($matches[1]);
		$value = array_shift($matches[6]);

		if($key == "transaction")
		{
			TransactionDetails::instance()->trans_number = $value;
			TransactionDetails::instance()->flag=2;
		}

		if($key == "date")
		{
			TransactionDetails::instance()->date = format_date($value);
			TransactionDetails::instance()->date_flag = 1;
		}

		if($key == "time")
			TransactionDetails::instance()->time = format_time($value);

		if($key == "manager")
			TransactionDetails::instance()->manager_id = intVal($value);

		if($key == "teller" OR $key == "cashier")
			TransactionDetails::instance()->teller_id = intVal($value);

		if($key == "coin")
			TransactionDetails::instance()->coin = strval($value);

		/*    *** Amount Total Section ***     */
		if(in_array($key, array("total exposed bills", "inventory collected", "deposit total", "amount tendered", "total inventory removed", "excess bills processed", "total inventory added", "amount verified", "exposed bills reconciled")))
		{
			TransactionDetails::instance()->total_amount = strval($value);

			ProcessingFile::instance()->add_total_amount_tendered(floatval($value));
		}

		if($key == "other cash deposited")
			TransactionDetails::instance()->other_cash_deposited = floatval($value);
		if($key == "machine total")
			TransactionDetails::instance()->machine_total = floatval($value);
		if($key == "non-cash total")
			TransactionDetails::instance()->non_cash_total = floatval($value);
		
		if($key == "amount requested")
			TransactionDetails::instance()->amount_requested = strval($value);

		if($key == "balance due")
			TransactionDetails::instance()->balance_due = strval($value);

		if(TransactionCategory::getVal($key))
		{
			TransactionDetails::instance()->transaction_category = TransactionCategory::getVal($key);
		}

		if($key == "account number")
		{
			TransactionDetails::instance()->account_number = $value;
		}

		$test->a[md5($key)] = $key;
		return;
	}
	elseif(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		TransactionDetails::instance()->__number = 0;
		
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
			return;
		$key2=$key;
		$key = "denom_$key";
		TransactionDetails::test($key2, $piece);
		TransactionDetails::instance()->$key = intVal($piece);
		return;
	}
	else if(preg_match_all("/([A-Za-z]+( [A-Za-z]+)+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		echo "$matches[1]\n";
	}
	else
	{
		TransactionDetails::instance()->__number++;
		if (TransactionDetails::instance()->deposit_canceled_flag <=3 && TransactionDetails::instance()->deposit_canceled_flag >= 0) 
		{			
			if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
			{
				list($key) = $matches[0];
				if (!empty($matches[0][0]) && !empty($matches[0][1]) && !empty($matches[0][2])) {			
					$date = format_date($matches[0][0]);
					$time = date("H:i:s", strtotime($matches[0][1].":".$matches[0][2]));

					TransactionDetails::instance()->date = $date;
					TransactionDetails::instance()->time = $time;
				}	
			}
			if (preg_match_all("/(([A-Za-z])*)/", strtolower($line), $matches)) {
				$user_id = TransactionDetails::getUserId($matches[0][0]);
				TransactionDetails::instance()->user = $user_id;					
			}
			TransactionDetails::instance()->deposit_canceled_flag = TransactionDetails::instance()->deposit_canceled_flag - 1;
		}
		
		if (TransactionDetails::instance()->date_flag == 0) {		
			if (TransactionDetails::instance()->flag <=2 && TransactionDetails::instance()->flag >= 0) 
			{
				if(preg_match_all("/([0-9\/\.:]+( pm| am)?)/", strtolower($line), $datematches))
				{
					list($key) = $datematches[0];
					$date = format_date($datematches[0][0]);
					$time = date("H:i:s", strtotime($datematches[0][1]));

					TransactionDetails::instance()->date = $date;
					TransactionDetails::instance()->time = $time;
				}
				if (preg_match_all("/(([A-Za-z])*)/", strtolower($line), $user_name_matches)) 
				{
					if ($user_name_matches[0][0] != 'transaction' && !empty($user_name_matches[0][0])) 
					{
						$user_id = TransactionDetails::getUserId($user_name_matches[0][0]);
						TransactionDetails::instance()->user = $user_id;
						TransactionDetails::instance()->flag = -1;
					}
				}
				if (TransactionDetails::instance()->flag == 2 || TransactionDetails::instance()->flag == 1) 
				{
					TransactionDetails::instance()->flag = TransactionDetails::instance()->flag - 1;
				}
			}

			if (TransactionDetails::instance()->online_nominated_dispense_flag <=2 && TransactionDetails::instance()->online_nominated_dispense_flag >= 0) 
			{			
				if(preg_match_all("/([0-9\/\.]+( pm| am)?)/", strtolower($line), $matches))
				{
					list($key) = $matches[0];
					if (!empty($matches[0][0]) && !empty($matches[0][1]) && !empty($matches[0][2])) {			
						$date = format_date($matches[0][0]);
						$time = date("H:i:s", strtotime($matches[0][1].":".$matches[0][2]));

						TransactionDetails::instance()->date = $date;
						TransactionDetails::instance()->time = $time;
					}	
				}
				if (preg_match_all("/(([A-Za-z])*)/", strtolower($line), $matches)) {
					if ($matches[0][0] != 'online' && !empty($matches[0][0])) {
						$user_id = TransactionDetails::getUserId($matches[0][0]);
						TransactionDetails::instance()->user = $user_id;
					}
				}
				TransactionDetails::instance()->online_nominated_dispense_flag = TransactionDetails::instance()->online_nominated_dispense_flag - 1;
			}			

			if(TransactionDetails::instance()->__number >= 5)
			{
				TransactionDetails::instance()->close();
				TransactionDetails::instance()->completed();

				if(Manager::instance()->is())
				{
					TransactionDetails::instance()->getTransactionIds(TransactionDetails::instance()->id);
				}
				return TransactionDetails::destroy();
			}	
		}
	}	

});

class TransactionDetails extends Model
{
	public static $instance = null;

	public $flag = -1;

	public $date_flag = 0 ;

	public $online_nominated_dispense_flag = -1;

	public $deposit_canceled_flag = -1;

	public static $allTransactionId = array();

	protected $fields = array("id",
							"file_processing_detail_id",
							"transaction_category",
							"side_log_id",
							"trans_type_id",
							"trans_number",
							"trans_datetime",
							"teller_id",
							"bill_history_id",
							"history_report_id",
							"manager_id",
							"denom_100",
							"denom_50",
							"denom_20",
							"denom_10",
							"denom_5",
							"denom_2",
							"denom_1",
							"coin",
							"amount_requested",
							"total_amount",
							"other_cash_deposited",
							"machine_total",
							"non_cash_total",
							"balance_due",
							"total_amount_calculated",
							"total_pieces_calculated",
							"trans_limit_exceeded",
							"trans_limit_override",
							"override_manager_id",
							"messages",
							"status",
							"account_number",
							"error_messages",
							"created_date",
							"created_by",
							"updated_date",
							"updated_by",
							"user");
	
	public $id;
	public $file_processing_detail_id;
	public $transaction_category;
	public $side_log_id;
	public $trans_type_id;
	public $trans_number;
	public $trans_datetime;
	public $teller_id;
	public $bill_history_id;
	public $history_report_id;
	public $manager_id;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $amount_requested;
	public $total_amount;
	public $other_cash_deposited;
	public $machine_total;
	public $non_cash_total;
	public $balance_due;
	public $total_amount_calculated = 0;
	public $total_pieces_calculated = 0;
	public $trans_limit_exceeded;
	public $trans_limit_override;
	public $override_manager_id;
	public $messages;
	public $status;
	public $account_number;
	public $error_messages;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;
	public $user;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->side_log_id = Side::has() ? Side::instance()->id : null;
		$this->bill_history_id = BillHistory::has() ? BillHistory::instance()->id : null;
		$this->history_report_id = HistoryReport::has() ? HistoryReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public function completed()
	{
		if(empty($this->trans_number))
		{
			$this->status = "I";
		}  

		$r = DB::insert("transaction_details", $this->toArray(), $this->id);

		ProcessingFile::instance()->inc_transaction_number();

		Container::instance()->setLastBlock($this);

		return $r;
	}

	public static function test($value1=null , $value2=null)
	{
	 	$transaction_type; 
	 	$db = DB::getInstance();

	 	echo "test Transaction Details";
	 	$data['file_id']=ProcessingFile::instance()->id;
	 	$data['branch_id']=ProcessingFile::instance()->branch_id;
	 	$data['denom']=$value1;
	 	$data['count']=$value2;
	 	$data['trans_type_id']=TransactionDetails::instance()->trans_type_id;
	 	$data['trans_datetime']=implode(" ", array(TransactionDetails::instance()->date,TransactionDetails::instance()->time));

	 	print_r($data);

	 	$file_id = $data['file_id'];
	 	$branch_id = $data['branch_id'];
	 	$denom = $data['denom'];
	 	$count = $data['count'];
	 	$trans_type_id = $data['trans_type_id'];
	 	$trans_datetime = $data['trans_datetime'];

	 	if($data['denom']>0){
	 		/*DB::insert("test_transaction_details", $data);*/
	 		$sql = $db->prepare("INSERT INTO  test_transaction_details (file_id , branch_id , denom, count,trans_type_id, trans_datetime) VALUES('$file_id','$branch_id','$denom','$count','$trans_type_id', '$trans_datetime')");
            $sql->execute(); 
	 	}
	 	
	}

	public function close()
	{
		$this->trans_datetime = implode(" ", array($this->date, $this->time));

		foreach(array("100", "50", "20", "10", "5", "2", "1") AS $k => $v)
		{
			$this->total_amount_calculated += intval($this->{"denom_$v"}) * intVal($v);
			$this->total_pieces_calculated += intval($this->{"denom_$v"});
		}

		$this->total_amount_calculated += floatval($this->coin);
	}

	public static function isBegin($str)
	{
		$matches = array();		
		if(!preg_match_all("/\(((Duress Dispense)|(Inventory Collected)|(Inventory Load)|(Local Mix Dispense)|(Online Mix Dispense)|(Online Nominated Dispense)|(Processed Excess Bills)|(Reconciled Exposed Bills)|(Vault Buy)|(Vault Sell)|(Verify - Mix Mode))\)/", $str, $matches))
				return false;

		return array_shift($matches[1]);	
		
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/^[-]{2,}/", $str, $matches);
	}

	public static function lastUpdateMsg($message)
	{
		$db = DB::getInstance();

		$s = $db->prepare("UPDATE `transaction_details` a SET a.messages=:messages WHERE 1 ORDER BY a.id DESC LIMIT 1");
		$s->bindParam(":messages", $message, PDO::PARAM_STR);
		return $s->execute();
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

	public function getTransactionIds($id)
	{
		self::$allTransactionId = $id;
	}

	public function getTransaction()
	{
		return self::$allTransactionId;
	}

}

class TempClass extends Model
{
	public static $instance = null;

	public $flag = -1;

	public $transaction_message = '' ;
}