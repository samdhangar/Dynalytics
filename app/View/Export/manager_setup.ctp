<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Date'),
    __('Manager'),
    __('Action'),
    __('Transaction Limit'),    
    __('Daily Limit'),    
    __('Deposit Limit'),    
    __('Text'),    
    __('Setup Date'),    
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($managerSetups as $managerSetup):

    $exportArr = array(
        $startNo++,
        (isset($managerSetup['FileProccessingDetail']['Branch']['name']) ? $managerSetup['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($managerSetup['ManagerSetup']['station']) ? $managerSetup['ManagerSetup']['station'] : ''),
        (isset($managerSetup['FileProccessingDetail']['file_date']) ? showdatetime($managerSetup['FileProccessingDetail']['file_date']) : ''),
        (isset($managerSetup['Manager']['name']) ? trim($managerSetup['Manager']['name']) : ''),
        (isset($managerSetup['ManagerSetup']['action']) ? $managerSetup['ManagerSetup']['action'] : ''),
        (isset($managerSetup['ManagerSetup']['trans_limit']) ? $managerSetup['ManagerSetup']['trans_limit'] : ''),
        (isset($managerSetup['ManagerSetup']['daily_limit']) ? $managerSetup['ManagerSetup']['daily_limit'] : ''),
        (isset($managerSetup['ManagerSetup']['deposit_limit']) ? $managerSetup['ManagerSetup']['deposit_limit'] : ''),
        (isset($managerSetup['ManagerSetup']['text']) ? $managerSetup['ManagerSetup']['text'] : ''),
        (isset($managerSetup['ManagerSetup']['datetime']) ? showdatetime($managerSetup['ManagerSetup']['datetime']) : ''),
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('ManagerSetup');
echo $this->CSV->render($filename);
?>
