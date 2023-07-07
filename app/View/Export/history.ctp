<?php
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
    __('DynaCore Station ID'),
    __('Manager'),
    __('Date'),
    __('Report Datetime'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($historyReport as $history):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($history['FileProccessingDetail']['Company']['first_name']) ? $history['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($history['FileProccessingDetail']['Branch']['name']) ? $history['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($history['HistoryReport']['station']) ? $history['HistoryReport']['station'] : ''),
        (isset($history['Manager']['name']) ? trim($history['Manager']['name']) : ''),
        (isset($history['FileProccessingDetail']['file_date']) ? showdate($history['FileProccessingDetail']['file_date']) : ''),
        (isset($history['HistoryReport']['report_datetime']) ? showdatetime($history['HistoryReport']['report_datetime']) : ''),
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('History');
echo $this->CSV->render($filename);
?>
