<?php
$_i = DepositCanceled::getInstance();

Hook::sub("subscription", array($_i, "index"));

class DepositCanceled
{
	protected static $instance = null;
	
	protected $line = "";
	protected $depositeCanceled = false;

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
		$matches = array();

		if(preg_match_all("/^[^A-Za-z0-9]*$/", $line, $matches))
		{
			return;
		}

		if(preg_match_all("/^[ ]+(Deposit Canceled)[ ]+$/", $this->line, $matches))
		{
			$this->depositeCanceled = true;
		}
	}

	public function is()
	{		
		return $this->depositeCanceled;
	}

	public function reset()
	{
		$this->depositeCanceled = false;
	}
}