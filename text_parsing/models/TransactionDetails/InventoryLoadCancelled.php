<?php
$_i = InventoryLoadCancelled::getInstance();

Hook::sub("subscription", array($_i, "index"));

class InventoryLoadCancelled
{
	protected static $instance = null;
	
	protected $line = "";
	protected $canceled = false;

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

		if(preg_match_all("/^[ ]+(Inventory Load Canceled)[ ]+$/", $this->line, $matches))
			$this->canceled = true;
	}

	public function is()
	{
		return $this->canceled;
	}

	public function reset()
	{
		$this->canceled = false;
	}
}