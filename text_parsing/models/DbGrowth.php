<?php
class DbGrowth extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"table_name",
								"check_date",
								"size");


	public function __construct()
	{
		DB::getInstance()->query('INSERT INTO db_growth SELECT null, table_name AS "Table", date(now()) as Check_date,round(((data_length + index_length) / 1024 / 1024), 2) as Size FROM information_schema.TABLES WHERE table_schema = "'.DB_NAME.'" ORDER BY Size;');
	}
}