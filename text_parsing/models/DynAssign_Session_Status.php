<?php

$_i = DynAssign_Session_Status_Ctrl::getInstance();

Hook::sub("subscription", array($_i, "index"));

class DynAssign_Session_Status_Ctrl
{
	protected static $instance = null;
	
	protected $line = "";
	protected $status = null;

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
			$this->status = new DynAssign_Session_Status();
			return;
		}

		if(!$this->status)
			return;

		if(!strlen(trim($this->line)))
		{
			$this->status->completed();
			$this->status = null;
			return;
		}

		$matches =  array();
		if(preg_match_all("/^DynAssign Admin: ([A-Za-z]+( [A-Za-z]+)*)/", $this->line, $matches))
		{
			return $this->status->admin = array_shift($matches[1]);
		}
		else if(preg_match_all("/^(Date|Time)[ ]+([0-9\/\: AMP]+)/", $this->line, $matches))
		{
			$key = array_shift($matches[1]);
			$val = array_shift($matches[2]);

			if(strtolower($key) == "date")
				$this->status->date = format_date($val);

			if(strtolower($key) == "time")
				$this->status->time = format_time($val);

			return;
		}
		else
		{
			$this->status->messages .= $this->line;
		}
	}

	protected function isStartLine()
	{
		$matches = array();
		return preg_match_all("/^[ ]+[\*]{3} DynAssign Session Status [\*]{3}[ ]+/", $this->line, $matches);
	}

	protected function isEndLine()
	{

	}
}

class DynAssign_Session_Status extends Model
{
	public static $instance = null;
	protected  $fields = array("id",
									"file_processing_detail_id",
									"admin",
									"datetime",
									"messages",
									"created_date",
									"created_by",
									"updated_date",
									"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $admin;
	public $datetime;
	public $messages = "";
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));

		DB::insert("dynassign_session_status", $this->toArray(), $this->id);
	}
}