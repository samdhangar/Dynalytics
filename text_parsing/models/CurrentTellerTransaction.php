<?php

Hook::sub("subscription", function($line){

	if(CurrentTellerTransaction::isBegin($line))
	{
		return CurrentTellerTransaction::instance();
	}

	if(!CurrentTellerTransaction::has())
		return;

	if(CurrentTellerTransaction::isEnd($line))
	{
		CurrentTellerTransaction::instance()->completed();
		return CurrentTellerTransaction::destroy();
	}

	if(CurrentTellerTransaction::checkSub($line))
		return;

	$line = strtolower($line);
	$matches = array();
	if(preg_match_all("/([A-Za-z]+( [A-Za-z]+)*) #?[ ]+(([0-9\/\.:]+( pm| am)?)|\n)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[4]);

		if($key == "date")
			CurrentTellerTransaction::instance()->date = format_date($value);
		if($key == "time")
			CurrentTellerTransaction::instance()->time = format_time($value);

		if($key == "station")
			CurrentTellerTransaction::instance()->station = $value;
		if($key == "teller" OR $key == "cashier")
			CurrentTellerTransaction::instance()->teller_id = $value;
		if($key == "of deposits")
			CurrentTellerTransaction::instance()->no_of_deposits = $value;
		if($key == "of withdrawals")
			CurrentTellerTransaction::instance()->no_of_withdrawals = $value;
		if($key == "withdrawals")
			CurrentTellerTransaction::instance()->withdrawals_amt = $value;
		if($key == "batch withdrawals")
			CurrentTellerTransaction::instance()->batch_withdrawals_amt = $value;
		if($key == "buy")
			CurrentTellerTransaction::instance()->buy_amt = $value;
		if($key == "batch deposits")
			CurrentTellerTransaction::instance()->batch_deposit_amt = $value;
		if($key == "deposits")
			CurrentTellerTransaction::instance()->deposit_amt = $value;
		if($key == "sell")
			CurrentTellerTransaction::instance()->sell_amt = $value;
		if($key == "vault buys")
			CurrentTellerTransaction::instance()->vault_buys_amt = $value;
		if($key == "batch vault buys")
			CurrentTellerTransaction::instance()->batch_vault_buys_amt = $value;
	}
	else if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);
		
		if( in_array($key, array("100", "50", "20", "10", "5", "2", "1", "coin")) )
		{
			$m = ($key != "coin") ? "_denom_" : "_";

			$k = CurrentTellerTransaction::instance()->getSub().$m.$key;

			CurrentTellerTransaction::instance()->$k = $value;
		}
	}
	else if(preg_match_all("/ *\*\*\* (.*?) \*\*\* */", $line, $matches))
	{
		CurrentTellerTransaction::instance()->message = array_shift($matches[1]);
		CurrentTellerTransaction::instance()->completed();
		return CurrentTellerTransaction::destroy();
	}

});

class CurrentTellerTransaction extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"trans_datetime",
								"teller_id",
								"no_of_deposits",
								"no_of_withdrawals",
								"w_denom_100",
								"w_denom_50",
								"w_denom_20",
								"w_denom_10",
								"w_denom_5",
								"w_denom_2",
								"w_denom_1",
								"w_coin",
								"d_denom_100",
								"d_denom_50",
								"d_denom_20",
								"d_denom_10",
								"d_denom_5",
								"d_denom_2",
								"d_denom_1",
								"d_coin",
								"bw_denom_100",
								"bw_denom_50",
								"bw_denom_20",
								"bw_denom_10",
								"bw_denom_5",
								"bw_denom_2",
								"bw_denom_1",
								"bw_coin",
								"bd_denom_100",
								"bd_denom_50",
								"bd_denom_20",
								"bd_denom_10",
								"bd_denom_5",
								"bd_denom_2",
								"bd_denom_1",
								"bd_coin",
								"v_denom_100",
								"v_denom_50",
								"v_denom_20",
								"v_denom_10",
								"v_denom_5",
								"v_denom_2",
								"v_denom_1",
								"v_coin",
								"bv_denom_100",
								"bv_denom_50",
								"bv_denom_20",
								"bv_denom_10",
								"bv_denom_5",
								"bv_denom_2",
								"bv_denom_1",
								"bv_coin",
								"withdrawals_amt",
								"batch_withdrawals_amt",
								"buy_amt",
								"batch_deposit_amt",
								"deposit_amt",
								"sell_amt",
								"vault_buys_amt",
								"batch_vault_buys_amt",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");
	public $id;
	public $file_processing_detail_id;
	public $station;
	public $trans_datetime;
	public $teller_id;
	public $no_of_deposits;
	public $no_of_withdrawals;
	public $w_denom_100;
	public $w_denom_50;
	public $w_denom_20;
	public $w_denom_10;
	public $w_denom_5;
	public $w_denom_2;
	public $w_denom_1;
	public $w_coin;
	public $d_denom_100;
	public $d_denom_50;
	public $d_denom_20;
	public $d_denom_10;
	public $d_denom_5;
	public $d_denom_2;
	public $d_denom_1;
	public $d_coin;
	public $bw_denom_100;
	public $bw_denom_50;
	public $bw_denom_20;
	public $bw_denom_10;
	public $bw_denom_5;
	public $bw_denom_2;
	public $bw_denom_1;
	public $bw_coin;
	public $bd_denom_100;
	public $bd_denom_50;
	public $bd_denom_20;
	public $bd_denom_10;
	public $bd_denom_5;
	public $bd_denom_2;
	public $bd_denom_1;
	public $bd_coin;
	public $v_denom_100;
	public $v_denom_50;
	public $v_denom_20;
	public $v_denom_10;
	public $v_denom_5;
	public $v_denom_2;
	public $v_denom_1;
	public $v_coin;
	public $bv_denom_100;
	public $bv_denom_50;
	public $bv_denom_20;
	public $bv_denom_10;
	public $bv_denom_5;
	public $bv_denom_2;
	public $bv_denom_1;
	public $bv_coin;
	public $withdrawals_amt;
	public $batch_withdrawals_amt;
	public $buy_amt;
	public $batch_deposit_amt;
	public $deposit_amt;
	public $sell_amt;
	public $vault_buys_amt;
	public $batch_vault_buys_amt;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	protected $subSection = null;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
	}

	public static function isBegin($subject)
	{
		$matches = array();
		return preg_match_all("/(Current (Teller|Cashier) Transactions)/", $subject, $matches);
	}
	public static function isEnd($subject)
	{
		$matches = array();
		return preg_match_all("/(=)+/", $subject, $matches);
	}

	public static function checkSub($subject)
	{
		$matches = array();
		if(!preg_match_all("/(( )+((Batch Withdrawals)|(Batch Deposits)|(Withdrawals)|(Deposits)|(Vault Buys)|(Batch Vault Buys))( )+\n)/", $subject, $matches))
		{
			return false;
		}

		$v = array_shift($matches[3]);

		if($v == "Batch Withdrawals")
			self::instance()->subSection = "bw";
		else if($v == "Batch Deposits")
			self::instance()->subSection = "bd";
		else if($v == "Withdrawals")
			self::instance()->subSection = "w";
		else if($v == "Deposits")
			self::instance()->subSection = "d";
		else if($v == "Vault Buys")
			self::instance()->subSection = "v";
		else if($v == "Batch Vault Buys")
			self::instance()->subSection = "bv";

		return true;
	}

	public function getSub()
	{
		return $this->subSection;
	}

	public function completed()
	{
		CurrentTellerTransaction::instance()->trans_datetime = implode(" ", array(CurrentTellerTransaction::instance()->date, CurrentTellerTransaction::instance()->time));
		DB::insert("current_teller_transactions", $this->toArray(), $this->id);
	}

	public static function updateLastWithMsg($message)
	{
		$db = DB::getInstance();

		$s = $db->prepare("UPDATE `current_teller_transactions` a SET a.message=:message WHERE 1 ORDER BY a.id DESC LIMIT 1");
		$s->bindParam(":message", $message, PDO::PARAM_STR);
		return $s->execute();
	}
}