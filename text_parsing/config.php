<?php

define("DB_HOST", "localhost");
define("DB_NAME", "dynalitics");
define("DB_USER", "root");
define("DB_PWD", "");

define('MAIL_HOST',"ssl://smtp.gmail.com");
define('MAIL_USERNAME',"securemetasys.test@gmail.com");
define('MAIL_PASSWORD',"securemetasys");
define('MAIL_PORT',"465");
/*define('MAIL_HOST',"smtp.gmail.com");
define('MAIL_USERNAME',"aditya@webrevolution.in");
define('MAIL_PASSWORD',"Web@123$");
define('MAIL_PORT',"993");*/
define('BASE_URL',"");

// Set default timezone;
date_default_timezone_set('America/Los_Angeles');

function format_date($value)
{
	return DateTime::createFromFormat("n/d/y", $value) ? DateTime::createFromFormat("n/d/y", $value)->format("Y-m-d") : "1999-01-01"; 
}
function format_time($value)
{
	$value = trim($value);
	if(count(explode(" ", $value)) != 2)
		return "00:00:00";

	if(strpos($value, ":") !== false)
	{
		if(count(explode(":", $value)) != 3)
			return "00:00:00";

		$value = str_replace(":", "", $value);
	}

	if(strlen($value) < 9)
		$value = "0".$value;

	return DateTime::createFromFormat("gis A", $value) ? DateTime::createFromFormat("gis A", $value)->format("H:i:s") : "00:00:00";
}

function is_file_ready($pathname)
{
	$fp = fopen($pathname, "r+");

	if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
	    flock($fp, LOCK_UN);    // release the lock
		fclose($fp);
		return true;
	}
	else
	{
	    return false;
	}
}
