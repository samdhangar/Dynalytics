<?php

$data = array(
    __('Sr. No.'),
    __('DynaCore Station ID'),
    __('Branch Name'),
    __('Date'),
    __('No. of time file processed'),
    __('First Process Time'),
    __('Last Process Time'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($processFiles as $processFile):
    $dateDiff = $this->Custom->displayDiffDate($processFile['FileProccessingDetail']['processing_starttime'], $processFile['FileProccessingDetail']['processing_endtime']);

    $exportArr = array(
        $startNo++,
        (isset($processFile['FileProccessingDetail']['station']) ? $temp_station[$processFile['FileProccessingDetail']['station']] : ''),
        (isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''),
        (isset($processFile['FileProccessingDetail']['file_date']) ? showdate($processFile['FileProccessingDetail']['file_date']) : ''),
        (isset($processFile['FileProccessingDetail']['processing_counter']) ? $processFile['FileProccessingDetail']['processing_counter'] : ''),
        (isset($processFile['FileProccessingDetail']['processing_starttime']) ? showdatetime($processFile['FileProccessingDetail']['processing_starttime']) : ''),
        (isset($processFile['FileProccessingDetail']['processing_endtime']) ? showdatetime($processFile['FileProccessingDetail']['processing_endtime']) : ''),
      
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('FileProcessing');
echo $this->CSV->render($filename);
?>
