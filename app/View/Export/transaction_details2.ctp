<?php
$data = array(
    __('Sr. No.'),
    __('Region'),
    __('Branch Name'),
    __('Station'),
    __('Date'),
    __('TellerName'),
    __('Transaction Type'),
    __('Denom $100'),
    __('Denom $50'),
    __('Denom $20'),
    __('Denom $10'),
    __('Denom $5'),
    __('Denom $2'),
    __('Denom $1'),
    __('Coins'),
    __('Total Amount'),
    
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($transactionsDetails as $transaction): 
    $exportArr = array(
        $startNo++,
        (isset($transaction['FileProccessingDetail']['Branch']['regiones']) ? $regiondata[$transaction['FileProccessingDetail']['Branch']['regiones']] : ''),
        (isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''), 
        (isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''),
        (isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y h:i A",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''),
        (isset($transaction['TransactionDetail']['teller_name']) ? $transaction['TransactionDetail']['teller_name'] : '-'),
        (isset($transaction['TransactionType']['text']) ? str_replace("Online ","",$transaction['TransactionType']['text']) : '-'),
        (isset($transaction['TransactionDetail']['denom_100']) ? ($transaction['TransactionDetail']['denom_100']):0),
        (isset($transaction['TransactionDetail']['denom_50']) ? ($transaction['TransactionDetail']['denom_50']):0),
        (isset($transaction['TransactionDetail']['denom_20']) ? ($transaction['TransactionDetail']['denom_20']):0),
        (isset($transaction['TransactionDetail']['denom_10']) ? ($transaction['TransactionDetail']['denom_10']):0),
        (isset($transaction['TransactionDetail']['denom_5']) ? ($transaction['TransactionDetail']['denom_5']):0),
        (isset($transaction['TransactionDetail']['denom_2']) ? ($transaction['TransactionDetail']['denom_2']):0),
        (isset($transaction['TransactionDetail']['denom_1']) ? ($transaction['TransactionDetail']['denom_1']):0),
        (isset($transaction['TransactionDetail']['coin']) ? ($transaction['TransactionDetail']['coin']):0),
        (isset($transaction['TransactionDetail']['total_amount']) ?  GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0)
         
        
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TransactionDetails');
echo $this->CSV->render($filename);

?>