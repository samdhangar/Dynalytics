<?php

/**
 * Description of functions
 *
 * @author DynaLytics
 */
function getPhoto($id = null, $photo = null, $dirpath = PROFILE_IMAGE, $dir = false, $thumb = false)
{
    if (!empty($thumb)) {
        $thumb = 'thumb_';
    }
    if ($dir) {
        $path = $dirpath . $id . DS;
        if (!is_dir($path)) {
            $data = createFolder($path);
        }
        return $path;
    } else {
        if ((!empty($id) && !empty($photo)) && !is_array($photo) && file_exists(WWW_ROOT . $dirpath . $id . DS . $photo)) {
            if (!empty($thumb) && file_exists(WWW_ROOT . $dirpath . $id . '/' . $thumb . $photo)) {
                return $dirpath . $id . '/' . $thumb . $photo;
            }
            return $dirpath . $id . '/' . $photo;
        }
    }


    return "/img/" . NO_IMAGE;
}
function getUserPhoto($id = null, $photo = null, $dir = false, $thumb = true, $type = 'user')
{
    if ($type == 'user') {
        $dirpath = USER_IMAGE;
    }
    if ($thumb) {
        $thumb = 'thumb_';
    }
    if ($dir) {
        $path = $dirpath . $id . DS;
        if (!is_dir($path)) {
            $data = createFolder($path);
        }
        return $path;
    } else {
        $fullPath = WWW_ROOT . $dirpath . $id . DS . $photo;
        if (!is_dir($fullPath) && file_exists($fullPath)) {
            if (in_array($thumb, array('thumb90_', 'thumb200_'))) {
                return $dirpath . $id . '/' . $thumb . $photo;
            }
            return $dirpath . $id . '/' . $photo;
        }
    }
    return "/img/" . NO_IMAGE;
}

function getInvoicePath($invoiceId = null, $isFullPath = false)
{
    $fullPath = WWW_ROOT . INVOICE_PATH . $invoiceId . '.pdf';
    if (!is_dir($fullPath) && file_exists($fullPath)) {
        if (!empty($isFullPath)) {
            return $fullPath;
        }
        return INVOICE_PATH . $invoiceId . '.pdf';
    }
    return "";
}

function getFileProcessPath($fileName = null, $isFullPath = false)
{
    $fullPath = WWW_ROOT . FILE_PROCESS_PATH . $fileName . '.pdf';
    if (!is_dir($fullPath)) {
        if (!empty($isFullPath)) {
            return $fullPath;
        }
        return FILE_PROCESS_PATH . $fileName . '.pdf';
    }
    return "";
}

function addBranchDirectory($branchFtp = null, $isGetShortPath = false)
{
    if (!empty($branchFtp)) {
        $shorthPath = BRANCH_PATH . $branchFtp . DS;
        $path = $shorthPath . 'queue';
        $savePath = ROOT . DS . $path;
        if (!is_dir($savePath)) {
            $data = createFolder($savePath);
            if ($isGetShortPath) {
                return $shorthPath;
            }
            return $path;
        }
        if (is_dir($savePath)) {
            return $shorthPath;
        }
        return '';
    }
}

function getCompanyPhoto($id = null, $photo = null, $dir = false, $thumb = true)
{
    return getUserPhoto($id, $photo, $dir, $thumb, 'company');
}

function showdate($date, $na = '', $requireTooltip = false, $format = DATE_DEF_FORMAT)
{

    $isValid = (!is_numeric($date) ? strtotime($date) : $date) > 0 ? true : false;
   return $isValid ? getDateTimeInTimezone($date, $format, $requireTooltip) : $na;
    
}

function showtime($date, $na = '', $requireTooltip = false)
{
    $isValid = (!is_numeric($date) ? strtotime($date) : $date) > 0 ? true : false;
    return $isValid ? getDateTimeInTimezone($date, 'h:i a', $requireTooltip) : $na;
}

function showdatetime($datetime, $na = '', $format = '', $requireTooltip = false)
{
    $format = empty($format) ? DATE_DEF_FORMAT . ' h:i a' : $format;
    $formats = explode(' ', $format);
    $isValid = (!is_numeric($datetime) ? strtotime($datetime) : $datetime) > 0 ? true : false;
    if ($isValid) {
        if (count($formats) > 1) {
            return showdate($datetime, $na, $requireTooltip, $formats[0]) . '  ' . showtime($datetime, $na, $requireTooltip);
        } else {
            return getDateTimeInTimezone($datetime, $format, $requireTooltip);
        }
    }
    return $na;
}

function getDateTimeInTimezone($datetime, $format = '', $requireTooltip = false)
{
    $format = empty($format) ? DATE_DEF_FORMAT . ' H:i:s' : $format;
    if ($requireTooltip) {
        return "<span title='" . $datetime . "\n" . date_default_timezone_get() . "'>" . serverToSiteTimezone($datetime, $format) . "</span>";
    } else {
        return serverToSiteTimezone($datetime, $format);
    }
}

function serverToSiteTimezone($datetime, $format = '')
{
    $format = empty($format) ? DATE_DEF_FORMAT . ' H:i:s' : $format;
    return convertToTimezone($datetime, date_default_timezone_get(), date_default_timezone_get(), $format);
}

function siteToServerTimezone($datsetime, $format = '')
{
    $format = empty($format) ? DATE_DEF_FORMAT . ' H:i:s' : $format;
    return convertToTimezone($datetime, date_default_timezone_get(), date_default_timezone_get(), $format);
}

function convertToTimezone($datetime, $currentTimezone, $newTimezone, $format = '')
{
    $format = empty($format) ? DATE_DEF_FORMAT . ' H:i:s' : $format;
    if (empty($datetime)) {
        return "";
    }
    if (is_numeric($datetime)) {
        $datetime = date('Y-m-d H:i:s', $datetime);
    }
    $date = new DateTime($datetime, new DateTimeZone($currentTimezone)); //Set Current timezone
    $date->setTimezone(new DateTimeZone($newTimezone)); //Set New Timezone
    return $date->format($format);
}

function createFolder($path = '', $permission = 0777)
{
    if (empty($path)) {
        return false;
    }
    if (!file_exists($path) && !is_dir($path)) {
        mkdir($path, $permission, true);
        return true;
    }
}

function generateCode($field1, $field2 = null)
{
    $hash = $field1 . $field2;
    return Security::hash($hash, 'sha256', true);
}

function clean_url($string, $replaceWith = "-")
{
    $string = preg_replace('/&+/', 'and', $string);
    $string = preg_replace('/[^A-Za-z0-9\.]/', '-', $string); // Removes special chars.
    $string = str_replace(' ', '-', trim($string, '-'));
    return strtolower(preg_replace('/[\-]+/', $replaceWith, $string));
}

function encrypt($sData)
{
    $id = (double) $sData * 525325.24;
    return base64_encode($id);
}

function decrypt($sData)
{
    $url_id = base64_decode($sData);
    $id = (double) $url_id / 525325.24;
    return $id;
}

function getrandompassword($len = 6)
{
    if (!isLive()) {
        return '123456';
    }
    $str = '';
    for ($i = 1; $i <= $len; $i++) {
        $ord = rand(48, 90);
        if ((($ord >= 48) && ($ord <= 57)) || (($ord >= 65) && ($ord <= 90)))
            $str.=chr($ord);
        else
            $str.= getrandompassword(1);
    }
    return $str;
}

function getPercentage($totalAmount = 1, $amount = 0)
{
    if (empty($totalAmount)) {
        return 0;
    }
    return round((($amount / $totalAmount) * 100), 2);
}

function isLive()
{
    if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'dynalitics.local')) {
        return false;
    }
    return true;
}

function isAdmin()
{
    return isSuparAdmin() || isAdminAdmin() || isSupportAdmin();
}

function isDealer()
{
    return isSuparDealer() || isAdminDealer() || isSupportDealer();
}

function isCompany()
{
    return isSuparCompany() || isCompanyAdmin() || isCompanyBranchAdmin() || isCompanyRegionalAdmin();
}

function isSuparAdmin()
{
    return chkLoginUsrType(ADMIN, SUPAR_ADM);
}

function isAdminAdmin()
{
    return chkLoginUsrType(ADMIN, ADMIN);
}

function isSupportAdmin()
{
    return chkLoginUsrType(ADMIN, SUPPORT);
}

function isSuparDealer()
{
    return chkLoginUsrType(DEALER, SUPAR_ADM);
}

function isAdminDealer()
{
    return chkLoginUsrType(DEALER, ADMIN);
}

function isSupportDealer()
{
    return chkLoginUsrType(DEALER, SUPPORT);
}

function isSuparCompany()
{
    return chkLoginUsrType(COMPANY, SUPAR_ADM);
}

function isCompanyAdmin()
{
    return chkLoginUsrType(COMPANY, ADMIN);
}

function isCompanyBranchAdmin()
{
    return chkLoginUsrType(COMPANY, BRANCH);
}

function isCompanyRegionalAdmin()
{
    return chkLoginUsrType(COMPANY, REGIONAL);
}

function chkLoginUsrType($role = 'Dealer', $type = 'Support')
{
//    $uRole = CakeSession::read('Auth.User.role');
//    $uType = CakeSession::read('Auth.User.user_type');
    $sesData = getMySessionData();
    $uRole = $sesData['role'];
    $uType = $sesData['user_type'];
    if ($uRole == $role && $uType == $type) {
        return true;
    }
    return false;
}

function getNamedParameter($named = array(), $wantArr = false)
{
    $return = '';
    $i = 0;
    $retArr = array();
    foreach ($named as $key => $value) {
        if (in_array($key, array(ADMIN, DEALER, COMPANY))) {
            $retArr['type'] = $key;
            $retArr['value'] = decrypt($value);
            $return .= (empty($i) ? '' : '/') . $key . ':' . $value;
            $i++;
        }
    }
    if ($wantArr) {
        return $retArr;
    }
    return $return;
}

function getMySessionData()
{
    /**
     * return company login data
     */
    if (CakeSession::check('Auth.User.companyDetail')) {
        return CakeSession::read('Auth.User.companyDetail');
    } elseif (CakeSession::check('Auth.User.dealerDetail')) {
        /**
         * dealer login data
         * @return string
         */
        return CakeSession::read('Auth.User.dealerDetail');
    } else {
        return CakeSession::read('Auth.User');
    }
}

function getUserRole()
{
    $sessionData = getMySessionData();
//    $uRole = CakeSession::read('Auth.User.role');
//    $uType = CakeSession::read('Auth.User.user_type');
    $uRole = $sessionData['role'];
    $uType = $sessionData['user_type'];

    if (!empty($uRole) && !empty($uType)) {
        return $uRole . ' ' . $uType;
    }
    return '';
}

function getMyRole()
{
    $sessionData = getMySessionData();
//    $uRole = CakeSession::read('Auth.User.role');
//    $uType = CakeSession::read('Auth.User.user_type');
    $uRole = $sessionData['role'];
    $uType = $sessionData['user_type'];
    $arr = array(
        ADMIN . '_' . SUPAR_ADM => SUPAR_ADM . ' ' . ADMIN,
        ADMIN . '_' . ADMIN => SUPAR_ADM . ' ' . ADMIN,
        ADMIN . '_' . SUPPORT => SUPPORT . ' ' . ADMIN,
        DEALER . '_' . SUPAR_ADM => SUPAR_ADM . ' ' . DEALER,
        DEALER . '_' . ADMIN => ADMIN . ' ' . DEALER,
        DEALER . '_' . SUPPORT => SUPPORT . ' ' . DEALER,
        COMPANY . '_' . SUPAR_ADM => COMPANY . ' ' . ADMIN,
        COMPANY . '_' . ADMIN => COMPANY . ' ' . ADMIN,
        COMPANY . '_' . BRANCH => BRANCH . ' ' . ADMIN
    );
    return isset($arr[$uRole . '_' . $uType]) ? $arr[$uRole . '_' . $uType] : '';
}

function getLoginRole($uRole = '', $uType = '')
{
    if (empty($uRole)) {
        $uRole = CakeSession::read('Auth.User.role');
    }
    if (empty($uType)) {
        $uType = CakeSession::read('Auth.User.user_type');
    }
    $arr = array(
        ADMIN . '_' . SUPAR_ADM => SUPAR_ADM . ' ' . ADMIN,
        ADMIN . '_' . ADMIN => SUPAR_ADM . ' ' . ADMIN,
        ADMIN . '_' . SUPPORT => SUPPORT . ' ' . ADMIN,
        DEALER . '_' . SUPAR_ADM => SUPAR_ADM . ' ' . DEALER,
        DEALER . '_' . ADMIN => ADMIN . ' ' . DEALER,
        DEALER . '_' . SUPPORT => SUPPORT . ' ' . DEALER,
        COMPANY . '_' . SUPAR_ADM => COMPANY . ' ' . ADMIN,
        COMPANY . '_' . ADMIN => COMPANY . ' ' . ADMIN,
        COMPANY . '_' . BRANCH => BRANCH . ' ' . ADMIN,
        COMPANY . '_' . REGION => REGION . ' ' . ADMIN
    );
    return isset($arr[$uRole . '_' . $uType]) ? $arr[$uRole . '_' . $uType] : '';
}

function getAllUserRoleTypes($user_role = null)
{
//        'Admin ' . SUPAR_ADM => 'Admin ' . SUPAR_ADM,
    $roleArr = array(
        'Admin_Admin' => 'Supar Admin',
        'Admin_Support' => 'Support Admin',
        'Dealer_' . SUPAR_ADM => SUPAR_ADM . ' Dealer',
        'Dealer_Admin' => 'Dealer Admin',
        'Dealer_Support' => 'Support Dealer',
        'Company_' . SUPAR_ADM => SUPAR_ADM . ' Company',
        'Company_Admin' => 'Company Admin',
        'Company_Branch' => 'Branch Admin',
        'Company_Regional' => 'Regional Admin'
    );
    if (!empty($user_role)) {
        return isset($roleArr[$user_role]) ? $roleArr[$user_role] : '';
    }
    return $roleArr;
}

function cropDetail($string = null, $character = 100, $appendText = '...')
{
    $string = strip_tags($string);
    if (strlen($string) > ($character - 1)) {
        $string = substr($string, 0, ($character - 1)) . $appendText;
        return $string;
    }
    return $string;
}

function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d')
{
//    date_default_timezone_set('America/Los_Angeles');
    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);
    while ($current <= $last) {
        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }
    return $dates;
}

function showPhoneNo($phone = '(000)00-0000')
{
    return $phone;
}

function getMemberShipType($memberShip = null, $idDisplay = 0)
{
    $memArr = array(
        'gold' => 'Gold',
        'silver' => 'Silver',
        'platinum' => 'Platinum'
    );
    if (!empty($idDisplay)) {
        return isset($memArr[$memberShip]) ? $memArr[$memberShip] : '-';
    }
    return $memArr;
}

function getReportFilter($value = null)
{
    $arr = array(
        'today' => __('Today'),
        'last_10days' => __('Last 10 days'),
        // 'last_7days' => __('Last 7 days'),
        // 'last_15days' => __('Last 15 days'),
        'last_months' => __('Last Month'),
        // 'last_3months' => __('Last 3 Month'),
        'last_3months' => __('Quarter to Date'),
        'last_12months' => __('Year to Date'),
        'all_dates' => __('All Dates'),
    );
    if (empty($value)) {
        return $arr;
    }
    if($value['from'] == "all_dates"  && ($value['start_date'] == '0NaN-NaN-NaN')){
       $start_date =  date('m-d-Y',strtotime('2000-01-01'));
    }else{
        $start_date =  date('m-d-Y',strtotime($value['start_date']));
    }
    return isset($arr[$value['from']]) ? $arr[$value['from']] : __('Start date:') .' '.$start_date .' ' . __('End date:').' '. date('m-d-Y',strtotime($value['end_date']));
}

function getCommunicationType($comm_type = null, $idDisplay = 0)
{
    $memArr = array(
        'email' => 'Email',
        'sms' => 'Sms'
    );
    if (!empty($idDisplay)) {
        return isset($memArr[$comm_type]) ? $memArr[$comm_type] : '-';
    }
    return $memArr;
}

function getBranchFtpDetail($isGetFtpPath = false, $isSortPath = false)
{
    $responseArr = array(
        'ftp_username' => strtolower(substr(str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 5)),
        'ftp_password' => substr(str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 10)
    );
    if ($isGetFtpPath) {
        $responseArr['ftp_path'] = addBranchDirectory($responseArr['ftp_username'], $isSortPath);
    }
    return $responseArr;
}

function getRandomEmailId($email = 'test.user@gmail.com')
{
    return $email;
    $email = explode('@', $email);
    $email[0] = $email[0] . '+' . rand(10, 100);
    return implode('@', $email);
}

function isTestMode()
{
    if ($_SERVER['REMOTE_ADDR'] == '116.74.122.170' && isset($_GET['test']) && $_GET['test'] == 1) {
        return true;
    }
    return false;
}

function isDisplayFields()
{
    $sesData = getMySessionData();
    if (empty($sesData['parent_id'])) {
        return true;
    }
    return false;
}

function getCompanyId()
{
    $sessionData = getMySessionData();
    $companyId = 0;
    if (isSuparCompany()) {
        $companyId = $sessionData['id'];
    } elseif (isCompanyAdmin() || isCompanyBranchAdmin() || isCompanyRegionalAdmin()) {
        $companyId = $sessionData['parent_id'];
    }
    return $companyId;
}

function getDealerId()
{
    $sessionData = getMySessionData();
    $dealerId = 0;
    if (isSuparDealer()) {
        $dealerId = $sessionData['id'];
    } elseif (isAdminDealer() || isSupportDealer()) {
        $dealerId = $sessionData['parent_id'];
    }
    return $dealerId;
}

function showTransStatus($status = 'I')
{
    if (strtolower($status) == 'c') {
        return 'Complete';
    }
    return 'Incomplete';
}

function getSubscriptionCharge($charge = 0, $date = 0)
{
    $getMonDays = date('t', strtotime($date));
    $parDayCharge = (float) $charge / (float) $getMonDays;
    $monthLastDate = date('Y-m-t', strtotime($date));
    $date1 = new DateTime($date);
    $date2 = new DateTime($monthLastDate);
    $diff = $date2->diff($date1)->format("%d");
    $total = (float) $diff * (float) $parDayCharge;
    return round($total, 2);
}

function getSubcriptionDiscount($totalStation = 0)
{
    if ($totalStation >= 11 and $totalStation <= 20) {
        return 0.05;
    } elseif ($totalStation >= 21 and $totalStation <= 50) {
        return 0.07;
    } elseif ($totalStation >= 51 and $totalStation <= 100) {
        return 0.09;
    } elseif ($totalStation >= 101 and $totalStation <= 200) {
        return 0.11;
    } elseif ($totalStation > 200) {
        return 0.15;
    } else {
        return 0.0;
    }
}

function showInvoiceNo($invoiceId = null)
{
    $invoiceId = sprintf('%04u', $invoiceId);
    return __('#') . $invoiceId;
}

function showAmount($amount = 0)
{
    return '$ ' . number_format($amount,2,".",",");
}

function showDiscount($discount = 0)
{
    return $discount . ' %';
}


function getReportName($name = 'Report')
{
    return $name .'Report_'. date('mdYHm');
}

function checkImageAvailable($imageUrl = '')
{
    if(!empty($imageUrl)){
        return getimagesize($imageUrl);
    }
}

function getWorkedMinutes($startDate = '')
{
    if(empty($startDate)){
        return 0;
    }
    $startTime = strtotime($startDate);
    $todayDate = date('Y-m-d H:i:s');
    $endTime = strtotime($todayDate);
    return round(abs($endTime - $startTime) / 60,2);
}

function getWorkedHours($minutes = 0)
{
    return round($minutes / 60,2);
}

function GetNumberFormat($value,$type)
{
    if($value<0){
        return ("(".$type."".number_format(($value*-1),2).")");
    }else{
        return ($type."".number_format(($value),2));
    }

}

function get_time_difference($time_in, $time_out) 
{
   /* echo "time input ";
    echo $time_in;
    echo $time_out;
    die();*/
  $time_in = strtotime("1980-01-01 $time_in");
  $time_out = strtotime("1980-01-01 $time_out");

  if ($time_out < $time_in) 
  {
    $time_out += 86400;
  }

  return date("H:i", strtotime("1970-01-01 00:00:00") + ($time_out - $time_in));
}
function get_time_difference_new($time1, $time2) {
    $time1 = strtotime("1980-01-01 $time1");
    $time2 = strtotime("1980-01-01 $time2");
if ($time2 < $time1) {
    $time2 += 86400;
}
return date("H:i", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));
}