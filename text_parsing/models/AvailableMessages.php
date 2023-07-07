<?php

class AvailableMessages
{
	protected static $instance = null;

	protected $data = array();

	public function __construct()
	{
		$stmt = DB::getInstance()->prepare("SELECT * FROM `available_messages` WHERE 1");
		$stmt->execute();

		while($val = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->data[md5(trim($val["text"]))] = array("text" => $val["text"], "type" => $val["type"]);
		}
	}

	public static function instance()
	{
		if(!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	public function find($msg)
	{
		if(!empty($this->data[md5(trim($msg))]))
			return $this->data[md5(trim($msg))];
		return null;
	}
}