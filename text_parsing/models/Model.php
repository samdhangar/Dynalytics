<?php
class Model
{
	protected $fields = array();

	public $__status;
	public $__number = 0;

	public static function destroy()
	{
		$class = get_called_class();

		$class::$instance = null;
	}

	public static function instance()
	{
		$class = get_called_class();

		if(empty($class::$instance))
			$class::$instance = new $class();

		return $class::$instance;
	}

	public static function has()
	{
		$class = get_called_class();

		return !empty($class::$instance);
	}
	
	public function toArray()
	{
		$arr = array();

		foreach($this->fields AS $k => $v)
		{
			$arr[$v] = $this->$v;
		}

		return $arr;
	}
}