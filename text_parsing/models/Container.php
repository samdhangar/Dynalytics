<?php

$_container = Container::instance();

class Container
{
	private static $instance = null;

	public static function instance()
	{
		if(empty(self::$instance))
			self::$instance = new self();

		return self::$instance;
	}

	public function setLastBlock($obj)
	{
		$this->lastBlock = $obj;
	}

	public function getLastBlockType()
	{
		return get_class($this->lastBlock);
	}
}