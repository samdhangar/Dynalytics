<?php
error_reporting(E_ALL);

require_once(__DIR__."/config.php");
require_once(__DIR__."/Hook.php");
require_once(__DIR__."/models/DB.php");
require_once(__DIR__."/models/Model.php");

require_once(__DIR__."/models/DbGrowth.php");

class DbGrowthCron
{
	protected $debug = false;

	public function __construct()
	{
		 $this->debug = true;
	}

	public function run()
	{
		$this->loadLibrary();

		$this->out("\nUpdating Database Growth", 1);
		$this->process();
	}

	protected function loadLibrary()
	{
	}

	protected function process()
	{
		//UPDATE DATABASE GROWTH
		new DbGrowth();
	}
	protected function out($msg, $lvl=4)
	{
		echo "$msg\n";
	}

}

function runApp()
{
	$DbGrowthCron = new DbGrowthCron();
	$DbGrowthCron->run();
}

runApp();
