<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__."/models/DB.php");
require_once(__DIR__ . "/models/SendEmail.php");
require_once(__DIR__ . "/PHPMailer/PHPMailerAutoload.php");

$db = DB::getInstance();
$data = [];

$todayFileProcessingDetail = $db->prepare("SELECT * from daily_file_processing_detail where file_date = date(now())");
$todayFileProcessingDetail->execute();
$todayFileProcessingDetails = $todayFileProcessingDetail->fetchAll();

$data = $todayFileProcessingDetails;

if (!empty($todayFileProcessingDetails)) {	
	try 
	{
	    $email="sharad.gaikwad@securemetasys.com";
	    $subject="Processing file Report";
	    $body=$data;
	    $flag = 1;
	    SendEmail::init();
	    SendEmail::send_report($email, $subject, $body, $flag);

	    echo "\n\nDaily file processing report mail has been send ===========";
	}
	catch(Exception $e) {
	 	echo 'Exception in sendind process report : ' .$e->getMessage();
	}
}
?>