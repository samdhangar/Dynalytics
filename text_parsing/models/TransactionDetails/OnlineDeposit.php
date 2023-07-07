<?php

$_i = OnlineDeposit::getInstance();

Hook::sub("subscription", array($_i, "index"));

class OnlineDeposit
{
	protected static $instance = null;
	
	protected $line = "";
	protected $emptyLine = 0;
	protected $depositeCanceled = false;
	protected $transaction = null;
	protected $start = false;
	protected $end = true;
	protected $flag = -1;
	protected $date_flag = 0;

	public static function getInstance()
	{
		if(empty(self::$instance))
			self::$instance = new self();

		return self::$instance;
	}

	public function __construct()
	{
	}

	public function index($line)
	{
		$this->line = $line;


		if(!strlen(trim($this->line)))
		{
			if($this->transaction AND $this->emptyLine == 2)
				$this->complete();
			
			return $this->emptyLine++;
		}

		$this->emptyLine = 0;

		if($this->isOnlineDeposit())
		{
			$tt = "Online Deposit";
			$this->transaction = new TransactionDetails();
			$this->transaction->trans_type_id = TransactionType::getId($tt);
			$this->transaction->trans_type_full_text = $tt;

			if(DepositCanceled::getInstance()->is())
			{
				$this->transaction->messages = "Deposit Cancelled";
				DepositCanceled::getInstance()->reset();
			}

			return;
		}

		if($this->transaction AND $this->isEndLine())
			return $this->complete();

		if(!$this->transaction)
			return;

		$matches = array();
		if(preg_match_all("/([A-Za-z]+(( |-)([A-Za-z]+|[0-9]))*) #?[ ]+(([0-9\/\.:]+( pm| am)?)|\n)/", strtolower($this->line), $matches))
		{
			$key = array_shift($matches[1]);
			$value = array_shift($matches[6]);

			if($key == "transaction"){				
				$this->transaction->trans_number = $value;
				$this->flag=2;
			}

			if($key == "date"){
				$this->transaction->date = format_date($value);
				$this->date_flag = 1;
			}

			if($key == "time")
				$this->transaction->time = format_time($value);

			if($key == "manager")
				$this->transaction->manager_id = intVal($value);

			if($key == "teller" OR $key == "cashier")
				$this->transaction->teller_id = intVal($value);

			if($key == "coin")
				$this->transaction->coin = strval($value);

			/*    *** Amount Total Section ***     */
			if(in_array($key, array("deposit total", "amount tendered", "total inventory removed", "excess bills processed", "total inventory added", "amount verified", "exposed bills reconciled")))
			{
				$this->transaction->total_amount = strval($value);

				ProcessingFile::instance()->add_total_amount_tendered(floatval($value));
			}

			if($key == "other cash deposited")
				$this->transaction->other_cash_deposited = floatval($value);
			if($key == "machine total")
				$this->transaction->machine_total = floatval($value);
			if($key == "non-cash total")
				$this->transaction->non_cash_total = floatval($value);
			
			if($key == "amount requested")
				$this->transaction->amount_requested = strval($value);

			if($key == "balance due")
				$this->transaction->balance_due = strval($value);

			if(TransactionCategory::getVal($key))
			{
				$this->transaction->transaction_category = TransactionCategory::getVal($key);
			}

			if($key == "account number")
			{
				$this->transaction->account_number = $value;
			}

			return;
		}
		elseif(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if(!in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
				return;

			$key = "denom_$key";

			$this->transaction->$key = intVal($piece);
			return;
		}else{
			if ($this->date_flag == 0) {		
				if ($this->flag <=2 && $this->flag >= 0) 
				{
					if(preg_match_all("/([0-9\/\.:]+( pm| am)?)/", strtolower($line), $datematches))
					{
						list($key) = $datematches[0];
						$date = format_date($datematches[0][0]);
						$time = date("H:i:s", strtotime($datematches[0][1]));

						$this->transaction->date = $date;
						$this->transaction->time = $time;
					}
					if (preg_match_all("/(([A-Za-z])*)/", strtolower($line), $user_name_matches)) {
						if ($user_name_matches[0][0] != 'transaction' && !empty($user_name_matches[0][0])) {
							$user_id = $this->getUserId($user_name_matches[0][0]);
							$this->transaction->user = $user_id;
							$this->flag = -1;
						}
					}
					if ($this->flag == 2 || $this->flag == 1) 
					{
						$this->flag = $this->flag - 1;
					}
				}
			}
		}
	}

	protected function complete()
	{
		$this->transaction->close();
		$this->transaction->completed();
		$this->transaction = null;
	}

	protected function isDepositeCanceled()
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Deposit Cancelled)[ ]+/", $this->line, $matches);
	}
	protected function isOnlineDeposit()
	{
		$matches = array();
		return preg_match_all("/^(\(Online Deposit\))[ ]+/", $this->line, $matches);
	}
	protected function isEndLine()
	{
		$matches = array();
		return preg_match_all("/[\-]{2,}/", $this->line, $matches);
	}

	protected function getUserId($user_name)
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
}