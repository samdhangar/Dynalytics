<?php

$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('Denom 1'),
    __('Denom 2'),
    __('Denom 5'),
    __('Denom 10'),
    __('Denom 20'),
    __('Denom 50'),
    __('Denom 100'),
    __('Coin'),
    __('Total'),
    __('Transaction Date'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($transactionVaultBuys as $transactionVaultBuy):

    $exportArr = array(
        $startNo++,
        (isset($transactionVaultBuy['FileProccessingDetail']['Branch']['name']) ? $transactionVaultBuy['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_1']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_1'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_2']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_2'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_5']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_5'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_10']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_10'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_20']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_20'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_50']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_50'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['denom_100']) ? $transactionVaultBuy['TransactionVaultBuy']['denom_100'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['coin']) ? $transactionVaultBuy['TransactionVaultBuy']['coin'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['total']) ? $transactionVaultBuy['TransactionVaultBuy']['total'] : ''),
        (isset($transactionVaultBuy['TransactionVaultBuy']['trans_datetime']) ? showdatetime($transactionVaultBuy['TransactionVaultBuy']['trans_datetime']) : ''),
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TellerUserBuys');
echo $this->CSV->render($filename);
?>
