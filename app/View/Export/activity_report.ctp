<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('Manager Name'),
    __('DynaCore Station ID'),
    __('Date'),
    __('No. of Activity'),
    __('Message'),
    __('Transaction Date'),
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($activities as $act):
    $exportArr = array(
        $startNo++,
        (isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($act['Manager']['name']) ? trim($act['Manager']['name']) : ''),
        (isset($act['ActivityReport']['station']) ? $act['ActivityReport']['station'] : ''),
        (isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : ''),
        (isset($act['ActivityReport']['bill_count']) ? $act['ActivityReport']['bill_count'] : ''),
        (isset($act['ActivityReport']['message']) ? $act['ActivityReport']['message'] : ''),
        (isset($act['ActivityReport']['trans_datetime']) ? showdatetime($act['ActivityReport']['trans_datetime']) : '')
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('Activity');
echo $this->CSV->render($filename);

?>