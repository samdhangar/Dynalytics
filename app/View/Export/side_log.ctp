<?php
$data = array(
    __('Sr. No.'),
    __('DynaCore Station ID'),
    __('User Type'),
    __('User ID'),
    __('Side'),
    __('Date Logged on'),
    __('Date Logged off'),
    __('Time Logged on'),
    __('Time Logged off'), 
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($sideLogs as $sideLog):
   
    $exportArr = array(
        $startNo++,
        (isset($sideLog[0]['station']) ? $sideLog[0]['station'] : ''),
        (isset($sideLog[0]['Teller']) ? $sideLog[0]['Teller'] : ''),
        (isset($sideLog[0]['teller_id']) ? $sideLog[0]['teller_id'] : ''),
        (isset($sideLog[0]['side_type']) ? $sideLog[0]['side_type'] : ''),
        (date("m-d-Y", strtotime($sideLog[0]['logon_datetime']))),
        (date("m-d-Y", strtotime($sideLog[0]['logoff_datetime']))),
        (isset($sideLog[0]['logon_datetime']) ? date("h:m:s a", strtotime($sideLog[0]['logon_datetime'])) : ''),
        (isset($sideLog[0]['logoff_datetime']) ? date("h:m:s a", strtotime($sideLog[0]['logoff_datetime'])) : ''),

    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('SideLog');
echo $this->CSV->render($filename);
?>
