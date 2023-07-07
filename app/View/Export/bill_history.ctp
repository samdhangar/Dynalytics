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
    __('Manager Name'),
    __('DynaCore Station ID'),
    __('Transaction Datetime'),
    __('Bill type'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($bills as $bill):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($bill['Manager']['name']) ? trim($bill['Manager']['name']) : ''),
        (isset($bill['BillHistory']['station']) ? $bill['BillHistory']['station'] : ''),
        (isset($bill['BillHistory']['trans_datetime']) ? showdatetime($bill['BillHistory']['trans_datetime']) : ''),
        (isset($bill['BillType']['bill_type']) ? $bill['BillType']['bill_type'] : ''),
      
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('BillHistory');
echo $this->CSV->render($filename);
?>
