<?php

class TransactionCategory
{
	public static $instance = null;

	protected $fields = array("id", "bill_type");

	public static function getVal($key)
	{
		$key = str_replace(" ", ".", strtolower(trim($key)));
		
		if(empty(self::$instance))
		{
			$stmt = DB::getInstance()->prepare("SELECT * FROM `transaction_category`");
			$stmt->execute();

			while($a = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				self::$instance[str_replace(" ", ".", strtolower(trim($a["text"])))] = $a;
			}
		}

		return isset(self::$instance[$key]) ? self::$instance[$key]["id"] : null;
	}
}