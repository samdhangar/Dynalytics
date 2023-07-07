<?php

Hook::sub("subscription", function($line){

	if(OnlineDeposit::canceled($line))
	{
		OnlineDeposit::destroy();
		OnlineDeposit::cancel();
	}

	if(OnlineDeposit::starting($line))
	{
		if(OnlineDeposit::has() AND OnlineDeposit::instance()->__type == "cancel")
			return OnlineDeposit::destroy();

		OnlineDeposit::destroy();
		OnlineDeposit::begin();
	}
	
	$line = strtolower($line);

	if(!OnlineDeposit::has())	
		return;

	if(OnlineDeposit::instance()->__type == "cancel" AND OnlineDeposit::counter() > 5)
		return;

	if(OnlineDeposit::ending($line))
	{
		OnlineDeposit::instance()->complete();
		return OnlineDeposit::destroy();
	}

	$matches = array();
	if(preg_match_all("/([A-Za-z]+( [A-Za-z]+)*) #?[ ]+([0-9\/\.]+( pm| am)?)/", $line, $matches))
	{
		list($key) = $matches[1];
		list($value) = $matches[3];

		$key = str_replace(" ", "_", strtolower($key));

		if(in_array($key, array('transaction', 'teller', 'coin', 'other_cash_deposited', 'machine_total', 'deposit_total')))
		{
			OnlineDeposit::instance()->$key = $value;
		}

		if($key == "date" AND DateTime::createFromFormat("n/d/y", $value))
			OnlineDeposit::instance()->date = format_date($value);

		if($key == "time")
			OnlineDeposit::instance()->time = format_time($value);
	}
	else if(preg_match_all("/([A-Za-z0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
	{
		list(, $name, $count, $amount) = $matches;
		$name = "denom_".array_shift($name);
		$count = array_shift($count);
		$amount = array_shift($amount);

		OnlineDeposit::instance()->$name = $count;
	}

});

class OnlineDeposit extends Model
{
	public static $instance = null;
	protected static $type = null;
	protected static $counter = 0;

	public $transaction;
	public $date;
	public $time;
	public $teller;
	public $coin;
	public $other_cash_deposited;
	public $machine_total;
	public $deposit_total;

	public function complete()
	{
		OnlineDeposit::instance()->dateTime = implode(" ", array(OnlineDeposit::instance()->date, OnlineDeposit::instance()->time));

		OnlineDeposit::destroy();
	}

	public static function starting($str)
	{
		$matches = array();
		return preg_match_all("/\(Online Deposit\)/", $str, $matches);
	}

	public function ending($str)
	{
		$matches = array();
		return preg_match_all("/[-]+/", $str, $matches);
	}

	public function canceled($str)
	{
		$matches = array();
		return preg_match_all("/(Deposit Cancelled)/", $str, $matches);
	}
}