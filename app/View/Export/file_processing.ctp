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
    __('No. File received for processing'),
    __('File Processed'),
    __('No. of Errors'),
    __('No. of transactions'),
    __('No. of deposits'),
    __('No. of withdrawals'),
    __('No. of reports'),
    __('No. of Automix Settings'),
    __('No. of Bill Activity Report'),
    __('No. of Bill Adjustment Report'),
    __('No. of Bill Count Report'),
    __('No. of Bill History Report'),
    __('No. of Coin Inventory'),
    __('No. of Current Teller Transaction'),
    __('No. of History Report'),
    __('No. of Manager Setup'),
    __('No. of Net Cash Usage Report'),
    __('No. of Side Activity Report'),
    __('No. of Teller Activity Report'),
    __('No. of Valut Buy Report'),
    __('No. of Teller Setup'),
    __('Total Cash Deposited'),
    __('Total Cash Requested'),
    __('Total Cash Withdrawal'),
    __('First Process Time'),
    __('Last Process Time'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($processFiles as $processFile):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($processFile['FileProccessingDetail']['Company']['first_name']) ? $processFile['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($processFile['FileProccessingDetail']['station']) ? $processFile['FileProccessingDetail']['station'] : ''),
        (isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''),
        (isset($processFile['FileProccessingDetail']['file_date']) ? showdatetime($processFile['FileProccessingDetail']['file_date']) : ''),
        (isset($processFile['FileProccessingDetail']['processing_counter'])?$processFile['FileProccessingDetail']['processing_counter']:0),
        (isset($processFile['FileProccessingDetail']['processing_counter'])?$processFile['FileProccessingDetail']['processing_counter']:0),
        (isset($processFile['ErrorDetail'][0]['no_of_errors']) ? $processFile['ErrorDetail'][0]['no_of_errors'] : 0),
        (isset($processFile['TransactionDetail'][0]['no_of_transaction']) ? $processFile['TransactionDetail'][0]['no_of_transaction'] : 0),
        (isset($processFile['TransactionDetail'][0]['no_of_deposit']) ? $processFile['TransactionDetail'][0]['no_of_deposit'] : 0),
        (isset($processFile['TransactionDetail'][0]['no_of_withdrawal']) ? $processFile['TransactionDetail'][0]['no_of_withdrawal'] : 0),
        (isset($processFile['TransactionDetail'][0]['no_of_report']) ? $processFile['TransactionDetail'][0]['no_of_report'] : 0),
        (isset($processFile['AutomixSetting'][0]['no_of_automix']) ? $processFile['AutomixSetting'][0]['no_of_automix'] : 0),
        (isset($processFile['BillsActivityReport'][0]['no_of_billactivity']) ? $processFile['BillsActivityReport'][0]['no_of_billactivity'] : 0),
        (isset($processFile['BillAdjustment'][0]['no_of_billadjustment']) ? $processFile['BillAdjustment'][0]['no_of_billadjustment'] : 0),
        (isset($processFile['BillCount'][0]['no_of_billcount']) ? $processFile['BillCount'][0]['no_of_billcount'] : 0),
        (isset($processFile['BillHistory'][0]['no_of_billhistory']) ? $processFile['BillHistory'][0]['no_of_billhistory'] : 0),
        (isset($processFile['CoinInventory'][0]['no_of_coininventory']) ? $processFile['CoinInventory'][0]['no_of_coininventory'] : 0),
        (isset($processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans']) ? $processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans'] : 0),
        (isset($processFile['HistoryReport'][0]['no_of_historyReport']) ? $processFile['HistoryReport'][0]['no_of_historyReport'] : 0),
        (isset($processFile['ManagerSetup'][0]['no_of_mgrSetup']) ? $processFile['ManagerSetup'][0]['no_of_mgrSetup'] : 0),
        (isset($processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage']) ? $processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage'] : 0),
        (isset($processFile['SideActivityReport'][0]['no_of_sideActivity']) ? $processFile['SideActivityReport'][0]['no_of_sideActivity'] : 0),
        (isset($processFile['TellerActivityReport'][0]['no_of_tellerActivity']) ? $processFile['TellerActivityReport'][0]['no_of_tellerActivity'] : 0),
        (isset($processFile['ValutBuy'][0]['no_of_vaultBuy']) ? $processFile['ValutBuy'][0]['no_of_vaultBuy'] : 0),
        (isset($processFile['TellerSetup'][0]['no_of_teller_setup']) ? $processFile['TellerSetup'][0]['no_of_teller_setup'] : 0),
        (isset($processFile['TransactionDetail'][0]['total_cash_deposit']) ? $processFile['TransactionDetail'][0]['total_cash_deposit'] : ''),
        (isset($processFile['TransactionDetail'][0]['total_cash_requested']) ? $processFile['TransactionDetail'][0]['total_cash_requested'] : ''),
        (isset($processFile['TransactionDetail'][0]['total_cash_withdrawal']) ? $processFile['TransactionDetail'][0]['total_cash_withdrawal'] : ''),
        (isset($processFile['FileProccessingDetail']['processing_starttime']) ? showdatetime($processFile['FileProccessingDetail']['processing_starttime']) : ''),
        (isset($processFile['FileProccessingDetail']['processing_endtime']) ? showdatetime($processFile['FileProccessingDetail']['processing_endtime']) : '')
        
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('FileProcessing');
echo $this->CSV->render($filename);
?>
