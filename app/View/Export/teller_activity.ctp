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
    __('Date'),
    __('No. Of Deposite'),
    __('No. Of Withdrawals'), 
    __('Total Transaction'), 
    __('Deposite Total'),
    __('Withdrawal Total'),
    __('Net Total'), 
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($tellerActivity as $act):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($act['TellerActivityReport']['station']) ? $act['TellerActivityReport']['station'] : ''),
        (isset($act['FileProccessingDetail']['file_date']) ? showdatetime($act['FileProccessingDetail']['file_date']) : ''),
        (isset($act['TellerActivityReport']['number_of_deposits']) ? $act['TellerActivityReport']['number_of_deposits'] : ''),
        (isset($act['TellerActivityReport']['number_of_withdrawals']) ? $act['TellerActivityReport']['number_of_withdrawals'] : ''),
        ($act['TellerActivityReport']['number_of_deposits']+$act['TellerActivityReport']['number_of_withdrawals']),
        (isset($act['TellerActivityReport']['deposit_total']) ? $act['TellerActivityReport']['deposit_total'] : ''),
        (isset($act['TellerActivityReport']['Withdrawal_total']) ? $act['TellerActivityReport']['Withdrawal_total'] : ''), 
        (isset($act['TellerActivityReport']['net_total']) ? $act['TellerActivityReport']['net_total'] : ''), 
    );
    $exportArr = array_merge($exportArr_first, $exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TellerActivity');
echo $this->CSV->render($filename);

?>
