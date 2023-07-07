<?php
// print_r($stationArr);exit;
 // echo '<pre><b>' . __FILE__ . ' (Line:'. __LINE__ .')</b><br>';
 // print_r($stationArr);echo '<br>';exit;
$data_first = array(
    __('Sr. No.'),
);
if (!isCompany() && empty($companyDetail)):
    $data_first = array(
        __('Sr. No.'),
        __('Company Name')
    );
endif;

$data_second = array(
    __('Branch Name'),
    __('DynaCore Serial Number'),
    __('DynaCore Station ID'),
    __('Last File Date'),
    __('No. Of Files processed '),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($stationArrs as $stationArr):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($stationArr['Company']['first_name']) ? $stationArr['Company']['first_name'] : ''),
        );
    endif;
    $exportArr_second = array(
        (isset($stationArr['CompanyBranch']['name']) ? $stationArr['CompanyBranch']['name'] : ''),
        (isset($stationArr['station']['id']) ? $stationArr['station']['id'] : ''),
        (isset($stationArr['station']['station_code']) ? $stationArr['station']['station_code'] : ''),
        (isset($stationArr['station']['last_file_date']) ? showdatetime($stationArr['station']['last_file_date']) : ''),
        (isset($stationArr['station']['file_processed_count']) ? $stationArr['station']['file_processed_count'] : ''),
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;
$filename = getReportName('Station');
echo $this->CSV->render($filename);
?>