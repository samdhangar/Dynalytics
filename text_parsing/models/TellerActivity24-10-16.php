<?php

Hook::sub("subscription", function($line){

	if(TellerActivity::isStart($line))
	{
		TellerActivity::instance()->status = "start";
	}

	if(!TellerActivity::has())
		return;

	if(TellerActivity::isEnd($line))
	{
		TellerActivity::instance()->completed();
		TellerActivity::destroy();
	}

	$matches = array();
	if(preg_match_all("/^(Cashier|Teller) #[ ]+([0-9]+)/", $line, $matches))
	{
		if(TellerActivityRecord::has())
		{
			TellerActivityRecord::instance()->completed();
			TellerActivityRecord::destroy();
		}

		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->teller_id = $value;
	}
	else if(preg_match_all("/^# of Deposits[ ]+([0-9]+)/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->number_of_deposits = intVal($value);
	}
	else if(preg_match_all("/^# of Withdrawals[ ]+([0-9]+)/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->number_of_withdrawals = intVal($value);

		TellerActivityRecord::instance()->section = "deposit";
	}
	else if(preg_match_all("/^Deposit Total[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->deposit_total = $value;

		TellerActivityRecord::instance()->section = "vault";
	}
	else if(preg_match_all("/^Vault Buy Total[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->Vault_buy_total = $value;

		TellerActivityRecord::instance()->section = null;
	}
	else if(preg_match_all("/^Withdrawal Total[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->Withdrawal_total = $value;
	}
	else if(preg_match_all("/^Net Total[ ]+(((\+|\-)?[0-9]*.[0-9]{2}|[A-Za-z ]+))/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->net_total = floatval($value);
	}
	else if(preg_match_all("/^Check Cashing[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->check_cashing = floatval($value);
		TellerActivityRecord::instance()->meta["check_cashing"] = floatval($value);
	}
	else if(preg_match_all("/^Credit Card Advance[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->credit_card_advance = floatval($value);
		TellerActivityRecord::instance()->meta["credit_card_advance"] = floatval($value);
	}
	else if(preg_match_all("/^Other Cash Deposited[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->other_cash_deposited = floatval($value);
	}
	else if(preg_match_all("/^Other Cash Withdrawal[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->other_cash_withdrawal = floatval($value);
	}
	else if(preg_match_all("/^Machine Total[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->machine_total = floatval($value);
	}
	else if(preg_match_all("/^Non-Cash Total[ ]+(\+|\-)?([0-9]*.[0-9]{2}|[A-Za-z ]+)/", $line, $matches))
	{
		$value = array_shift($matches[2]);

		TellerActivityRecord::instance()->non_cash_total = floatval($value);
	}

	if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$piece = array_shift($matches[2]);
		$value = array_shift($matches[3]);

		$key = (TellerActivityRecord::instance()->section == "deposit") ? "d_$key" : ((TellerActivityRecord::instance()->section == "vault") ? "v_$key" : $key);

		TellerActivityRecord::instance()->$key = intVal($piece);
	}
	else if(preg_match_all("/Coin[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		TellerActivityRecord::instance()->coin = $value;
	}
});

class TellerActivity extends Model
{
	public static $instance = null;
	public static $end_sign = "=";

	public function completed()
	{
		if(TellerActivityRecord::has())
		{
			TellerActivityRecord::instance()->completed();
			TellerActivityRecord::destroy();
		}
	}

	public static function isStart($str)
	{
		$matches = array();
		$return = preg_match_all("/^[ ]+((Teller|Cashier) Activity|No Teller Transactions)[ ]+/", $str, $matches);

		if($return AND $matches[1] == "No Teller Transactions")
			self::$end_sign = "-";
		else if($return AND in_array($matches[1], array("Teller Activity", "Cashier Activity")))
			self::$end_sign = "=";

		return $return;
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[".self::$end_sign."]{2,}/", $str, $matches);
	}
}

class TellerActivityRecord extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"teller_id",
								"activity_report_id",
								"number_of_deposits",
								"number_of_withdrawals",
								"d_denom_100",
								"d_denom_50",
								"d_denom_10",
								"d_denom_5",
								"d_denom_2",
								"d_denom_1",
								"d_coin",
								"v_denom_100",
								"v_denom_50",
								"v_denom_20",
								"v_denom_10",
								"v_denom_5",
								"v_denom_2",
								"v_denom_1",
								"v_coin",
								"deposit_total",
								"Withdrawal_total",
								"Vault_buy_total",
								"check_cashing",
								"credit_card_advance",
								"other_cash_deposited",
								"other_cash_withdrawal",
								"machine_total",
								"non_cash_total",
								"net_total",
								"meta",
								"message",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $teller_id;
	public $activity_report_id;
	public $number_of_deposits;
	public $number_of_withdrawals;
	public $d_denom_100;
	public $d_denom_50;
	public $d_denom_10;
	public $d_denom_5;
	public $d_denom_2;
	public $d_denom_1;
	public $d_coin;
	public $v_denom_100;
	public $v_denom_50;
	public $v_denom_20;
	public $v_denom_10;
	public $v_denom_5;
	public $v_denom_2;
	public $v_denom_1;
	public $v_coin;
	public $deposit_total;
	public $Withdrawal_total;
	public $Vault_buy_total;
	public $check_cashing;
	public $credit_card_advance;
	public $other_cash_deposited;
	public $other_cash_withdrawal;
	public $machine_total;
	public $non_cash_total;
	public $net_total;
	public $meta = array();
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $section = null;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::instance()->id;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->meta = json_encode($this->meta);
		DB::insert("teller_activity_report", $this->toArray(), $this->id);
		$this->meta = json_decode($this->meta, 1);
	}
}