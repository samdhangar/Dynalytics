<?php
//debug($processFiles);exit;
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
    __('Station No'),
    __('Date'),
    __('File Name'),
    __('Process Start Time'),
    __('No. Of Transactions '),
    __('No. Of Time Processed'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($processFiles as $processFile):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($processFile['Company']['first_name']) ? $processFile['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''),
        (isset($processFile['FileProccessingDetail']['station']) ? $processFile['FileProccessingDetail']['station'] : ''),
        (isset($processFile['FileProccessingDetail']['file_date']) ? showdate($processFile['FileProccessingDetail']['file_date']) : ''),
        (isset($processFile['FileProccessingDetail']['filename']) ? $processFile['FileProccessingDetail']['filename'] : ''),
        (isset($processFile['FileProccessingDetail']['processing_starttime']) ? showdatetime($processFile['FileProccessingDetail']['processing_starttime']) : ''),
        (isset($processFile['FileProccessingDetail']['transaction_number']) ? $processFile['FileProccessingDetail']['transaction_number'] : ''),
        (isset($processFile['FileProccessingDetail']['processing_counter']) ? $processFile['FileProccessingDetail']['processing_counter'] : ''),
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;
$filename = getReportName('Dashboard');
echo $this->CSV->render($filename);
?>
