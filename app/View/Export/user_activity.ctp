<?php
$data = array(
    __('Sr. No.'),
    __('Teller Name'),
    __('Date'),
    __('Transaction'),
    __('Transaction Time'),
    __('Total Amount'),
    __('Type'),
    __('$100'),
    __('$50'),
    __('$20'),
    __('$10'),
    __('$5'),
    __('$2'),
    __('$1'),
    __('Coins'),
    __('Status'),
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($transactions as $transaction): 
    if ($all == 'count') {
        $exportArr = array(
            $startNo++,
            (isset($transaction['TransactionDetail']['teller_name']) ? $transaction['TransactionDetail']['teller_name'] : '-'),
            (isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''),
            (isset($transaction['TransactionDetail']['trans_number']) ? $transaction['TransactionDetail']['trans_number'] : '-'),
            (isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y h:i A",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''),
            (isset($transaction['TransactionDetail']['total_amount']) ?  GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0),
            (isset($transaction['TransactionType']['text']) ? str_replace("Online ","",$transaction['TransactionType']['text']) : '-'),
            (isset($transaction['TransactionDetail']['denom_100']) ? ($transaction['TransactionDetail']['denom_100']):0),
            (isset($transaction['TransactionDetail']['denom_50']) ? ($transaction['TransactionDetail']['denom_50']):0),
            (isset($transaction['TransactionDetail']['denom_20']) ? ($transaction['TransactionDetail']['denom_20']):0),
            (isset($transaction['TransactionDetail']['denom_10']) ? ($transaction['TransactionDetail']['denom_10']):0),
            (isset($transaction['TransactionDetail']['denom_5']) ? ($transaction['TransactionDetail']['denom_5']):0),
            (isset($transaction['TransactionDetail']['denom_2']) ? ($transaction['TransactionDetail']['denom_2']):0),
            (isset($transaction['TransactionDetail']['denom_1']) ? ($transaction['TransactionDetail']['denom_1']):0),
            (isset($transaction['TransactionDetail']['coin']) ? ($transaction['TransactionDetail']['coin']):0),
            ((isset($transaction['TransactionDetail']['status']) && ($transaction['TransactionDetail']['status'] == "C")) ? "Completed" : "Incomplete"),
            
        );
    }
        if ($all == 'amount') {
            $exportArr = array(
                $startNo++,
                (isset($transaction['TransactionDetail']['teller_name']) ? $transaction['TransactionDetail']['teller_name'] : '-'),
                (isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''),
                (isset($transaction['TransactionDetail']['trans_number']) ? $transaction['TransactionDetail']['trans_number'] : '-'),
                (isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y h:i A",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''),
                (isset($transaction['TransactionDetail']['total_amount']) ?  GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0),
                (isset($transaction['TransactionType']['text']) ? str_replace("Online ","",$transaction['TransactionType']['text']) : '-'),
                (isset($transaction['TransactionDetail']['denom_100']) ? ($transaction['TransactionDetail']['denom_100']):0),
                (isset($transaction['TransactionDetail']['denom_50']) ? ($transaction['TransactionDetail']['denom_50']):0),
                (isset($transaction['TransactionDetail']['denom_20']) ? ($transaction['TransactionDetail']['denom_20']):0),
                (isset($transaction['TransactionDetail']['denom_10']) ? ($transaction['TransactionDetail']['denom_10']):0),
                (isset($transaction['TransactionDetail']['denom_5']) ? ($transaction['TransactionDetail']['denom_5']):0),
                (isset($transaction['TransactionDetail']['denom_2']) ? ($transaction['TransactionDetail']['denom_2']):0),
                (isset($transaction['TransactionDetail']['denom_1']) ? ($transaction['TransactionDetail']['denom_1']):0),
                (isset($transaction['TransactionDetail']['coin']) ? ($transaction['TransactionDetail']['coin']):0),
                ((isset($transaction['TransactionDetail']['status']) && ($transaction['TransactionDetail']['status'] == "C")) ? "Completed" : "Incomplete"),
            );
    }
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('UserActivity');
echo $this->CSV->render($filename);

?>