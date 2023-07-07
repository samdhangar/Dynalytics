<?php
namespace Setup;

$_i = Manager::getInstance();

\Hook::sub("subscription", array($_i, "index"));

class Manager
{
	protected static $instance = null;
	
	protected $line = "";
	protected $emptyLine = 0;
	protected $setup = null;
	protected $start = false;

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

		if($this->isStartLine())
		{
			return $this->start = 1;
		}

		if(!$this->start)
			return;

		if($this->isEndLine())
		{
			return $this->complete();
		}

		if(!strlen(trim($this->line)))
		{
			if($this->setup AND $this->emptyLine > 2)
				$this->complete();
			
			return $this->emptyLine++;
		}

		$this->change = $this->emptyLine ? true : false;

		$this->emptyLine = 0;

		if($this->change)
		{
			if($this->setup)
			{
				$this->setup->completed();
			}

			$this->setup = new ManagerSetup();
			$this->setup->date = $this->date;
			$this->setup->time = $this->time;
		}

		$matches = array();
		if(preg_match_all("/^(Date|Time)[ ]+([0-9\/:]+( AM| PM)?)/", $this->line, $matches))
		{
			$key = array_shift($matches[1]);
			$val = array_shift($matches[2]);

			if($key == "Date")
				$this->date = format_date($val);

			if($key == "Time")
				$this->time = format_time($val);

			return;
		}

		if($this->setup AND preg_match_all("/^(Added Manager)[ ]+([0-9]+)/", $this->line, $matches))
		{
			$key = array_shift($matches[1]);
			$val = array_shift($matches[2]);

			$this->setup->action = "Added Manager";
			$this->setup->manager_id = $val;
		}
		else if($this->setup AND preg_match_all("/^(Changed PIN) \# \- Manager[ ]+([0-9]+)/", $this->line, $matches))
		{
			$key = array_shift($matches[1]);
			$val = array_shift($matches[2]);

			$this->setup->action = "Changed PIN";
			$this->setup->manager_id = $val;
		}
		else if($this->setup AND preg_match_all("/([A-Za-z]+( Limit)?)[ #?]+(([0-9\.]+)|([A-Za-z]+( [A-Za-z]+)?))/", $line, $matches))
		{
			$key = array_shift($matches[1]);
			$value = array_shift($matches[3]);

			if($key == "Transaction Limit" AND is_numeric($value))
				return $this->setup->trans_limit = $value;
			if($key == "Daily Limit" AND is_numeric($value))
				return $this->setup->daily_limit = $value;
			if($key == "Deposit Limit" AND is_numeric($value))
				return $this->setup->deposit_limit = $value;
		}
	}

	public function complete()
	{
		if(!$this->setup)
		{
			$this->setup = new ManagerSetup();
			$this->setup->date = $this->date;
			$this->setup->time = $this->time;
		}
		
		$this->setup->completed();

		$this->start = 0;
		$this->emptyLine = 0;
		$this->change = false;
		$this->setup = null;
	}

	public function isStartLine()
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Manager Setup)[ ]+/", $this->line, $matches);
	}

	public function isEndLine()
	{
		$matches = array();
		return preg_match_all("/^(([ ]+(Exit Manager Setup)[ ]+)|(Station \#[ ]+[0-9]+))/", $this->line, $matches);
	}
}

class ManagerSetup extends \Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"station",
								"datetime",
								"action",
								"manager_id",
								"trans_limit",
								"daily_limit",
								"deposit_limit",
								"text",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $station;
	public $datetime;
	public $action;
	public $manager_id;
	public $trans_limit;
	public $daily_limit;
	public $deposit_limit;
	public $text;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $time = "00:00:00";
	public $date = "1999-01-01";
	public $line = array();

	public function __construct()
	{
		$this->file_processing_detail_id = \ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->station = \Station::has() ? \Station::instance()->id : null;
	}

	public function completed()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));
		$this->text = implode("\n", $this->line);
		$this->text = substr($this->text, 0, min(100, strlen($this->text)));

		\DB::insert("manager_setup", $this->toArray(), $this->id);
	}

	public static function isBegin($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Manager Setup)[ ]+/", $s, $matches);
	}

	public static function isEnd($s)
	{
		$matches = array();
		return preg_match_all("/[ ]+(Exit Manager Setup)[ ]+/", $s, $matches);
	}
}