<?php

$_msg = Msg::instance();
Hook::sub("subscription", array($_msg, "index"));

class Msg
{
	private static $instance = null;

	public $depositCancelled = "";

	public static function instance()
	{
		if(empty(self::$instance))
			self::$instance = new self();

		return self::$instance;
	}

	public function index($line)
	{
		$matches = array();


		if(preg_match_all("/[ ]+[\*]{3} (Activity Clear Not Permitted) [\*]{3}[ ]+/", $line, $matches))
		{
			ActivityReport::lastUpdateMsg("Activity Clear Not Permitted");
		}


		if(preg_match_all("/[ ]+[\*]{3} (Exposed Bills History Cleared) [\*]{3}[ ]+/", $line, $matches))
		{
			TransactionDetails::lastUpdateMsg("Exposed Bills History Cleared");
		}
/*
		if(preg_match_all("/[ ]+[\*]{3} (Manual Bill Removal Attempt) [\*]{3}[ ]+/", $line, $matches))
		{
			TransactionDetails::lastUpdateMsg("Manual Bill Removal Attempt");
		}

		if(preg_match_all("/[ ]+[\*]{3} (No Bills Were Removed) [\*]{3}[ ]+/", $line, $matches))
		{
			TransactionDetails::lastUpdateMsg("No Bills Were Removed");
		}
*/

		if(preg_match_all("/^[ ]+(Cashier Batch Cleared)[ ]+\n/", $line, $matches))
		{
			$key = array_shift($matches[1]);

			if($key == "Cashier Batch Cleared")
			{
				CurrentTellerTransaction::updateLastWithMsg($key);
			}
		}
	}
}