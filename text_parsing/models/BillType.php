<?php

class BillType extends Model
{
	public static $instance = null;

	protected $fields = array("id", "bill_type");

	public $id;
	public $bill_type;

	public static function getId($text)
	{
		if(is_null(self::$instance))
		{
			$stmt = DB::getInstance()->prepare("SELECT * FROM `bill_type`");
			$stmt->setFetchMode(PDO::FETCH_CLASS, "TransactionType");
			$stmt->execute();

			while($a = $stmt->fetch())
			{
				self::$instance[md5($a->bill_type)] = $a;
			}
		}

		if(empty(self::$instance[md5($text)]))
		{
			$a = new self();
			$a->bill_type = $text;

			if(!DB::insert("bill_type", $a->toArray(), $a->id))
				throw new Exception("Unable to add transaction type", 1);
				
			self::$instance[md5($text)] = $a;
		}

		return self::$instance[md5($text)]->id;
	}
}