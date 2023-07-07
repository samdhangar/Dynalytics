<?php

Hook::sub("subscription", function($line){

	if(CoinInventory::isStart($line))
	{
		CoinInventory::instance();
	}

	if(!CoinInventory::has())
		return;

	if(CoinInventory::isEnd($line))
	{
		CoinInventory::instance()->completed();
		CoinInventory::destroy();
	}

	$matches = array();
	if(preg_match_all("/^New Date[ ]+([0-9\/]+)/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		CoinInventory::instance()->date = format_date($value);
	}
	else if(preg_match_all("/^New Time[ ]+([0-9:]+ (PM|AM))/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		CoinInventory::instance()->time = format_time($value);
	}
	else if(preg_match_all("/^Coin  Adjusted[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		CoinInventory::instance()->array_adj[] = $value;
	}
	else if(preg_match_all("/^New Coin Total[ ]+((\+|\-)?[0-9]*.[0-9]{2})/", $line, $matches))
	{
		$value = array_shift($matches[1]);

		CoinInventory::instance()->array_tot[] = $value;
	}
});

class CoinInventory extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"new_datetime",
								"coin_adjusted_1",
								"new_coin_total_1",
								"coin_adjusted_2",
								"new_coin_total_2",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $new_datetime;
	public $coin_adjusted_1;
	public $new_coin_total_1;
	public $coin_adjusted_2;
	public $new_coin_total_2;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public $array_adj = array();
	public $array_tot = array();

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->new_datetime = implode(" ", array($this->date, $this->time));

		list($this->coin_adjusted_1, $this->coin_adjusted_2) = array_merge($this->array_adj, array(null, null));
		list($this->new_coin_total_1, $this->new_coin_total_2) = array_merge($this->array_tot, array(null, null));

		DB::insert("coin_inventory", $this->toArray(), $this->id);

		ProcessingFile::instance()->inc_adjustment_number();
	}

	public static function isStart($str)
	{
		$matches = array();
		return preg_match_all("/[ ]+Coin Inventory Cleared[ ]+/", $str, $matches);
	}

	public static function isEnd($str)
	{
		$matches = array();
		return preg_match_all("/[=]+/", $str, $matches);
	}
}