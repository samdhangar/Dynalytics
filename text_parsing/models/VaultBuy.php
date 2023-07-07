<?php

Hook::sub("subscription", function($line){

	if(VaultBuy::isBegin($line))
	{
		VaultBuy::instance()->status = "init";
		return VaultBuy::instance()->counter = 0;
	}

	if(!VaultBuy::has())
		return;

	VaultBuy::instance()->counter++;

	if(VaultBuy::isEnd($line))
	{
		if(VaultBuy::instance()->counter == 1)
			return;
		VaultBuy::instance()->completed();
		return VaultBuy::destroy();
	}

	$matches = array();
	if(VaultBuy::instance()->counter == 1 AND preg_match_all("/[-]+/", $line, $matches))
	{
		VaultBuy::destroy();
		return;
	}

	$matches = array();
	$line = strtolower($line);
	if(VaultBuy::instance()->status == "init" AND preg_match_all("/([A-Za-z]+) #?[ ]+([0-9\/:]+( am| pm)?)/", $line, $matches))
	{
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);
		
		if($key == "date")
			VaultBuy::instance()->date = format_date($value);

		if($key == "time")
			VaultBuy::instance()->time = format_time($value);

		if($key == "manager")
			VaultBuy::instance()->manager_id = $value;

		return VaultBuy::instance()->initCompleted();
	}

	if(preg_match_all("/(cashier|teller) #?[ ]+([0-9])/", $line, $matches))
	{
		if(TellerTransactionVB::has())
		{
			TellerTransactionVB::instance()->completed();
			TellerTransactionVB::destroy();
		}

		$value = array_shift($matches[2]);

		VaultBuy::instance()->status = "teller";
		return VaultBuy::instance()->teller = $value;
	}

	if(VaultBuy::instance()->status == "teller")
	{
		if(preg_match_all("/([A-Za-z]+)( #?[ ]+)([0-9\/:]+( am| pm)?)/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$value = array_shift($matches[3]);

			if($key == "transaction")
			{
				if(TellerTransactionVB::has())
				{
					// Save & destroy entry if exist
					TellerTransactionVB::instance()->completed();
					TellerTransactionVB::destroy();
					
				}

				// Create entry
				return TellerTransactionVB::instance()->trans_number = $value;
			}

			if($key == "date")
				TellerTransactionVB::instance()->date = format_date($value);
			if($key == "time")
				TellerTransactionVB::instance()->time = format_time($value);

			if($key == "tendered")
			{
				TellerTransactionVB::instance()->total = $value;
				TellerTransactionVB::instance()->completed();
				return TellerTransactionVB::destroy();
			}

			return TellerTransactionVB::instance()->initCompleted();
		}
		else if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if($key == "coin")
				return TellerTransactionVB::instance()->coin = intval($piece);

			$key = "denom_$key";
			return TellerTransactionVB::instance()->$key = intval($piece);
		}
	}

	if(preg_match_all("/[\*]+ (totals) [\*]+/", $line, $matches))
	{
		if(TellerTransactionVB::has())
		{
			TellerTransactionVB::instance()->completed();
			TellerTransactionVB::destroy();
		}

		return VaultBuy::instance()->status = "total";
	}

	if(VaultBuy::instance()->status == "total")
	{
		if(preg_match_all("/([0-9]+)[ ]+([0-9]+)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$piece = array_shift($matches[2]);
			$value = array_shift($matches[3]);

			if($key == "coin")
				return TellerTransactionTotalVB::instance()->$key = intval($piece);

			$key = "denom_$key";
			return TellerTransactionTotalVB::instance()->$key = intval($piece);
		}
		else if(preg_match_all("/(total vault buys)[ ]+([0-9]*.[0-9]{2})/", $line, $matches))
		{
			TellerTransactionTotalVB::instance()->total = array_shift($matches[2]);
			TellerTransactionTotalVB::instance()->completed();
			return TellerTransactionTotalVB::destroy();
		}
	}
});

class VaultBuy extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"trans_datetime",
								"manager_id",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $trans_datetime;
	public $manager_id;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date;
	public $time;
	public $status = null;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function initCompleted()
	{
		if(count(array_filter(array($this->manager_id, $this->date, $this->time))) < 3)
			return;

		$this->trans_datetime = implode(" ", array($this->date, $this->time));

		$this->status = "initcompleted";

		DB::insert("vault_buys", $this->toArray(), $this->id);
	}

	public function completed()
	{
	}

	public static function isBegin($str)
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Vault Buys)[ ]+\n/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}
}

class TellerTransactionVB extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"vault_buys_id",
								"trans_number",
								"trans_datetime",
								"teller_id",
								"denom_100",
								"denom_50",
								"denom_20",
								"denom_10",
								"denom_5",
								"denom_2",
								"denom_1",
								"coin",
								"total",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $vault_buys_id;
	public $trans_number;
	public $trans_datetime;
	public $teller_id;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $total;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time;
	public $date;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->vault_buys_id = VaultBuy::instance()->id;
		$this->teller_id = VaultBuy::instance()->teller;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function initCompleted()
	{
		$this->trans_datetime = implode(" ", array($this->date, $this->time));
	}

	public function completed()
	{
		DB::insert("teller_transaction_vault_buys", $this->toArray(), $this->id);
	}
}
class TellerTransactionTotalVB extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"vault_buys_id",
								"teller_id",
								"denom_100",
								"denom_50",
								"denom_20",
								"denom_10",
								"denom_5",
								"denom_2",
								"denom_1",
								"coin",
								"total",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $vault_buys_id;
	public $teller_id;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $total;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time;
	public $date;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->vault_buys_id = VaultBuy::instance()->id;
		$this->teller_id = VaultBuy::instance()->teller;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function initCompleted()
	{
	}

	public function completed()
	{
		DB::insert("teller_transaction_total_vault_buys", $this->toArray(), $this->id);
	}
}