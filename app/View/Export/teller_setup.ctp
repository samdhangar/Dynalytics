<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Date'),
    __('Teller Id'),
    __('Action'),
    __('Transaction Limit'),
    __('Daily Limit'),
    __('Deposit Limit'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($tellerSetups as $tellerSetup):
    $exportArr = array(
        $startNo++,
        (isset($tellerSetup['FileProccessingDetail']['Branch']['name']) ? $tellerSetup['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($tellerSetup['TellerSetup']['station']) ? $tellerSetup['TellerSetup']['station'] : ''),
        (isset($tellerSetup['FileProccessingDetail']['file_date']) ? showdate($tellerSetup['FileProccessingDetail']['file_date']) : ''),
        (isset($tellerSetup['TellerSetup']['teller_id']) ? $tellerSetup['TellerSetup']['teller_id'] : ''),
        (isset($tellerSetup['TellerSetup']['action']) ? $tellerSetup['TellerSetup']['action'] : ''),
        (isset($tellerSetup['TellerSetup']['trans_limit']) ? $tellerSetup['TellerSetup']['trans_limit'] : ''),
        (isset($tellerSetup['TellerSetup']['daily_limit']) ? $tellerSetup['TellerSetup']['daily_limit'] : ''),
        (isset($tellerSetup['TellerSetup']['deposit_limit']) ? $tellerSetup['TellerSetup']['deposit_limit'] : ''),
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TellerSetupReport');
echo $this->CSV->render($filename);
?>
