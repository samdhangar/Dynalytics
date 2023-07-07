<?php

class Hook
{
	private static $instance = null;
	public static function trigger()
	{
		$args = func_get_args();
	 	$i = self::getInstance();
	 	 call_user_func_array(array($i, "call"), $args); 
	}
	public static function sub()
	{
		$args = func_get_args();

		$name = array_shift($args);
		$callable = array_pop($args);

		if(!is_callable($callable))
		{
			print_r($callable);
			throw new Exception("Not callable!");
		}

		self::getInstance()->subscribe($name, $callable);
	}

	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	public function subscribe($key, $value)
	{
		$this->data[$key] = !empty($this->data[$key]) ? $this->data[$key] : array();
		$this->data[$key][] = $value; 
	}

	public function call()
	{
		$args = func_get_args();

		$key = array_shift($args);

		if(empty($this->data[$key]))
			return;

		foreach($this->data[$key] AS $key => $value)
		{
			call_user_func_array($value, $args);
		}
	}
}