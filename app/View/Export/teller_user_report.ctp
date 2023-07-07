<?php
//debug($TellerUserReports);exit;
$data = array(
    __('Sr. No.'),
    __('Teller Id'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Date'),
    __('Transaction Limit'),
    __('Daily Limit'),
    __('Deposit Limit'),
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($TellerUserReports as $TellerUserReport):
    
    $exportArr = array(
        $startNo++,
        (isset($TellerUserReport['TellerUserReport']['teller_id']) ? $TellerUserReport['TellerUserReport']['teller_id'] : ''),
        (isset($TellerUserReport['FileProccessingDetail']['Branch']['name']) ? $TellerUserReport['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($TellerUserReport['FileProccessingDetail']['station']) ? $TellerUserReport['FileProccessingDetail']['station'] : ''),
        (isset($TellerUserReport['FileProccessingDetail']['file_date']) ? showdate($TellerUserReport['FileProccessingDetail']['file_date']) : ''),
        (isset($TellerUserReport['TellerUserReport']['trans_limit']) ? $TellerUserReport['TellerUserReport']['trans_limit'] : ''),
        (isset($TellerUserReport['TellerUserReport']['daily_limit']) ? $TellerUserReport['TellerUserReport']['daily_limit'] : ''),
        (isset($TellerUserReport['TellerUserReport']['deposit_limit']) ? $TellerUserReport['TellerUserReport']['deposit_limit'] : ''),
      
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TellerUser');
echo $this->CSV->render($filename);
?>
