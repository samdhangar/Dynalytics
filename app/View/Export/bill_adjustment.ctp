<?php
$data = array(
    __('Sr. No.'),
    __('Region'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Date Time'),
    __('No. of transaction'),
    
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($bills as $bill): 
    $exportArr = array(
        $startNo++,
        (isset($bill['regions']['name']) ? $bill['regions']['name'] : ''),
        (isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''), 
        (isset($bill['FileProccessingDetail']['station']) ? $temp_station[$bill['FileProccessingDetail']['station']] : ''),
        (isset($bill['TransactionDetail']['trans_datetime']) ? date("m/d/Y",strtotime($bill['TransactionDetail']['trans_datetime'])) : ''),
        (isset($bill['TransactionDetail']['total_transaction']) ? $bill['TransactionDetail']['total_transaction'] : ''),
         
         
        
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('Bill_Adjustment_Report');
echo $this->CSV->render($filename);

?>