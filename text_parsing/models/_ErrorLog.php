<?php

class _ErrorLog extends Model
{
	public static $instance = null;

	protected $fields = array(
								"id",
								"error_message",
								"trace",
								"file",
								"created_date"
							);

	public $id;
	public $error_message;
	public $trace;
	public $file;
	public $created_date;

	public function save()
	{
		return DB::insert("_error_log", $this->toArray(), $this->id);
	}
}