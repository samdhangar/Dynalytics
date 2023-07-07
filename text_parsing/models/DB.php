<?php

class DB
{
	protected static $instance = null;
	protected static $transaction = null;

	public static function getInstance($test=false)
	{
		if($test)
		{
			return new TEST();
		}
		if(empty(self::$instance))
		{
			self::$instance = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PWD);
		}

		return self::$instance;
	}

	public static function insert($table, $params, &$id, $test=false)
	{
		$array = array();
		foreach($params AS $k => $v)
		{
			if(!is_null($v))
				$array[$k] = $v;
		}

		print_r($params);


		$fields = implode("`, `", array_keys($array));
		$binding = implode("_____key, :", array_keys($array));

		$sql = "INSERT INTO `$table` (`$fields`) VALUE(:{$binding}_____key)";

		  
		$stmt = DB::getInstance($test)->prepare($sql);
		
		foreach($array AS $k => $v)
		{
			$cb = function($k, $v) use($stmt) {
						$stmt->bindParam(":{$k}_____key", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
					};
			$cb($k, $v);
		}
		
		print_r(" Statement to execute ================ "); 
		print_r($stmt);

		$ok = $stmt->execute(); 
		$id = $ok ? self::getInstance()->lastInsertId() : null;

		if($ok)
			EntryCounter::newEntry($table);

		return $ok;
	}

	public static function update($table, $params, $primary="id")
	{
		$array = array();
		$binding = array();
		foreach($params AS $k => $v)
		{
			if(is_null($v))
				continue;
			
			$array[$k] = $v;
			$binding[$k] = "`$k`=:{$k}_____key";
		}

		$binding = implode(", ", $binding);

		$sql = "UPDATE `$table` SET $binding WHERE `$primary`=:$primary";

		$stmt = DB::getInstance()->prepare($sql);
		$stmt->bindParam(":$primary", $params[$primary], is_int($params[$primary]) ? PDO::PARAM_INT : PDO::PARAM_STR);

		foreach($array AS $k => $v)
		{
			$cb = function($k, $v) use($stmt) {
						$stmt->bindParam(":{$k}_____key", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
					};
			$cb($k, $v);
		}

		return $stmt->execute();
	}

	public static function beginTransaction()
	{
		if($r = self::getInstance()->beginTransaction())
			self::$transaction = true;

		return $r;
	}

	public static function rollBackTransaction()
	{
		if(self::$transaction)
		{
			self::getInstance()->rollBack();
			self::$transaction = false;
		}

		return true;
	}

	public static function commitTransaction()
	{
		if(self::$transaction)
		{
			self::getInstance()->commit();
			self::$transaction = false;
		}
	}
}

class TEST
{
	public function prepare($q)
	{
		return new Query($q);
	} 
}

class Query
{
	public function __construct($p)
	{
		$this->q = $p;
	}

	public function bindParam($key, &$value, $t=PDO::PARAM_STR)
	{
		if($t == PDO::PARAM_INT)
			return $this->q = str_replace(array($key), array("$value"), $this->q);

		return $this->q = str_replace(array($key), array("\"$value\""), $this->q);
	}

	public function execute()
	{
		echo $this->q;
		die();
	}
}