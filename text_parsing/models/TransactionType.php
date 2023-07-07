<?php

class TransactionType extends Model
{
	public static $instance = null;

	protected $fields = array("id", "text", "type", "total_text");
	
	public $id;
	public $text;
	public $type;
	public $total_text;

	public static function getId($text)
	{
		if(is_null(self::$instance))
		{
			$stmt = DB::getInstance()->prepare("SELECT * FROM `transaction_type`");
			$stmt->setFetchMode(PDO::FETCH_CLASS, "TransactionType");
			$stmt->execute();

			while($a = $stmt->fetch())
			{
				self::$instance[md5($a->text)] = $a;
			}
		}

		if(empty(self::$instance[md5($text)]))
		{
			$a = new self();
			$a->text = $text;
			$a->total_text = $text;

			if(!DB::insert("transaction_type", $a->toArray(), $a->id))
				throw new Exception("Unable to add transaction type", 1);
				
			self::$instance[md5($text)] = $a;
		}

		return self::$instance[md5($text)]->id;
	}
}