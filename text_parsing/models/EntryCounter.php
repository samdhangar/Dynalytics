<?php

class EntryCounter
{
	private static $instance = null;

	/**
	*	Do not count itself and file record
	*/
	private static $notAllowed = array("entry_count", "file_processing_detail");

	private $data = array();
	private $file_id;

	public static function getInstance()
	{
		if(empty(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function newEntry($name)
	{
		if(in_array($name, self::$notAllowed))
			return;

		self::getInstance()->push($name);
	}

	public static function init($id)
	{
		if(empty(self::$instance))
		{
			self::getInstance()->load($id);
		}
	}

	public function load($id)
	{
		$stmt = Db::getInstance()->prepare("SELECT `id`, `file_id`, `section`, `count` FROM `entry_count` WHERE `file_id`=:id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();

		$this->data = array();
		$this->file_id = $id;
		while($a = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->data[$a["section"]] = $a;
		}
	}

	public static function reset()
	{
		self::getInstance()->save();

		self::$instance = null;
	}

	public function push($name)
	{
		$this->data[$name] = isset($this->data[$name]) ? $this->data[$name] : array(
																					"id" => null,
																					"file_id" => $this->file_id,
																					"section" => $name,
																					"count" => 0
																					);
		$this->data[$name]["count"]++;
	}

	public function save()
	{
		foreach($this->data AS $k => $v)
		{
			if(empty($v["id"]))
				Db::insert("entry_count", $v, $v["id"]);
			else
				Db::update("entry_count", $v, "id");
		}
	}

}