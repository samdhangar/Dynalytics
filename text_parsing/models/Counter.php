<?php

class Counter
{
	public static $instance = null;

	private $counter = 0;

	public static function getInstance()
	{
		if(empty(self::$instance))
			self::init();

		return self::$instance;
	}

	public static function init()
	{
		self::$instance = new self();
	}

	public static function inc()
	{
		self::getInstance()->increment();
	}

	public static function get()
	{
		return self::getInstance()->count();
	}

	public static function set($count)
	{
		self::getInstance()->count($count);
	}

	public function increment()
	{
		$this->counter++;
	}

	public function count($count=null)
	{
		if(is_null($count))
			return $this->counter;

		$this->counter = $count;
	}
}