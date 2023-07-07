<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__."/models/DB.php");

$db = DB::getInstance();

echo "============ Start Renew data ================";

$max_date_query = $db->prepare("SELECT max(file_date) from file_processing_detail where file_date <= date(now())");
$max_date_query->execute();
$max_date = $max_date_query->fetch();
$max_date=$max_date[0];

echo "  \n max_date ".$max_date;

$difference_query = $db->prepare("SELECT datediff( date(now()), max('$max_date')) from file_processing_detail");
$difference_query->execute();
$difference = $difference_query->fetch();
$difference = $difference[0];

echo "  \n difference ".$difference;

try {

	$file_processing_detail_query = $db->prepare("UPDATE file_processing_detail set file_date = DATE_ADD(file_date, INTERVAL $difference DAY), processing_starttime = DATE_ADD(processing_starttime, INTERVAL $difference DAY), processing_endtime = DATE_ADD(processing_endtime, INTERVAL $difference DAY), created_date = DATE_ADD(created_date, INTERVAL $difference DAY), updated_date = DATE_ADD(updated_date, INTERVAL $difference DAY)");
	$file_processing_detail_query->execute();

	$test_transaction_details_query = $db->prepare("UPDATE test_transaction_details SET trans_datetime = DATE_ADD(trans_datetime, INTERVAL $difference DAY)");
	$test_transaction_details_query->execute();

	$test_error_detail_query = $db->prepare("UPDATE test_error_detail SET start_date = DATE_ADD(start_date, INTERVAL $difference DAY), entry_timestamp = DATE_ADD(entry_timestamp, INTERVAL $difference DAY)");
	$test_error_detail_query->execute();

	$error_detail_query = $db->prepare("UPDATE error_detail SET start_date = DATE_ADD(start_date, INTERVAL $difference DAY), entry_timestamp = DATE_ADD(entry_timestamp, INTERVAL $difference DAY)");
	$error_detail_query->execute();

	$test_bill_adjustments_query = $db->prepare("UPDATE test_bill_adjustments SET datetime = DATE_ADD(datetime, INTERVAL $difference DAY), created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$test_bill_adjustments_query->execute();

	$teller_activity_report_query = $db->prepare("UPDATE teller_activity_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$teller_activity_report_query->execute();

	$side_activity_report_query = $db->prepare("UPDATE side_activity_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$side_activity_report_query->execute();

	$teller_user_report_query = $db->prepare("UPDATE teller_user_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$teller_user_report_query->execute();

	$manager_user_report_query = $db->prepare("UPDATE manager_user_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$manager_user_report_query->execute();

	$user_report_query = $db->prepare("UPDATE user_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY), trans_datetime = DATE_ADD(trans_datetime, INTERVAL $difference DAY)");
	$user_report_query->execute();

	$inventory_query = $db->prepare("UPDATE inventory SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY)");
	$inventory_query->execute();

	$histories_query = $db->prepare("UPDATE histories SET created = DATE_ADD(created, INTERVAL $difference DAY)");
	$histories_query->execute();

	$activity_report_query = $db->prepare("UPDATE activity_report SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY),trans_datetime = DATE_ADD(trans_datetime, INTERVAL $difference DAY)");
	$activity_report_query->execute();

	$transaction_details_query = $db->prepare("UPDATE transaction_details SET created_date = DATE_ADD(created_date, INTERVAL $difference DAY),trans_datetime = DATE_ADD(trans_datetime, INTERVAL $difference DAY)");
	$transaction_details_query->execute();

	$transaction_heat_map_history_query = $db->prepare("UPDATE transaction_heat_map_history SET update_on = DATE_ADD(update_on, INTERVAL $difference DAY)");
	$transaction_heat_map_history_query->execute();

	echo "\n============ All Query Executed ================";
  
}
catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
}

echo "\n============ Finished ================";
?>