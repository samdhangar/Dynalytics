<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__."/models/DB.php");
require_once(__DIR__ . "/models/SendEmail.php");
require_once(__DIR__ . "/PHPMailer/PHPMailerAutoload.php");

$db = DB::getInstance();
$data = [];
$final_data = [];

$allBranches = $db->prepare("SELECT id from company_branches where branch_status='active'");
$allBranches->execute();
$allBranches = $allBranches->fetchAll(PDO::FETCH_ASSOC);

$todayFileProcessingDetail = $db->prepare("SELECT DISTINCT branch_id from daily_file_processing_detail where file_date = date(now())");
$todayFileProcessingDetail->execute();
$todayFileProcessingDetails = $todayFileProcessingDetail->fetchAll();

foreach ($allBranches as $key => $branch) {
	foreach ($todayFileProcessingDetails as $key => $bramchToSearch) {
		if ($bramchToSearch['branch_id'] != $branch['id'] ) 
		{
			$data[] = $branch['id'];
		}
	}
}

foreach ($data as $key => $id) {
	$branches = $db->prepare("SELECT cb.id, cb.name, cb.ftpuser, CONCAT(uc.first_name,' ',uc.last_name) as company_name from company_branches cb LEFT JOIN users uc on uc.id = cb.company_id where cb.id=$id and uc.role='company'");	
	$branches->execute();
	$branches = $branches->fetch();
	$finalData[] = $branches;
}

if (!empty($todayFileProcessingDetails)) {	
	try 
	{
	    $email="sharad.gaikwad@securemetasys.com";
	    $subject="Branch file Report";
	    $body=$finalData;
	    $flag = 2;
	    SendEmail::init();
	    SendEmail::send_report($email, $subject, $body, $flag);

	    echo "\n\nBranch report mail has been send ===========";
	}
	catch(Exception $e) {
	 	echo 'Exception in sendind process report : ' .$e->getMessage();
	}
}
?>