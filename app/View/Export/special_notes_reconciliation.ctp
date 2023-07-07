<?php
$data = array(
    __('Sr. No.'),
    __('Region'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('TellerName'),
    __('Date'),
    __('Transaction Type'),
    __('Denom $100'),
    __('Denom $50'),
    __('Denom $20'),
    __('Denom $10'),
    __('Denom $5'),
    __('Denom $2'),
    __('Denom $1'),
    __('Total Amount'),
    
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($specialNotes as $transaction): 
    if ($all == 'count') {
        $exportArr = array(
            $startNo++,
            (isset($transaction['FileProccessingDetail']['Branch']['regiones']) ? $regiondata[$transaction['FileProccessingDetail']['Branch']['regiones']] : ''),
            (isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''), 
            (isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''),
            (isset($transaction['Specialnotesreconciliation']['teller_name']) ? $transaction['Specialnotesreconciliation']['teller_name'] : '-'),
            (isset($transaction['Specialnotesreconciliation']['trans_datetime']) ? trim(date("m-d-Y h:i A",strtotime($transaction['Specialnotesreconciliation']['trans_datetime']))) : ''),
            (isset($transaction['Specialnotesreconciliation']['transaction_category']) ? str_replace("Online ","",$transaction['Specialnotesreconciliation']['transaction_category']) : '-'),
            (isset($transaction['Specialnotesreconciliation']['denom_100']) ? ($transaction['Specialnotesreconciliation']['denom_100']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_50']) ? ($transaction['Specialnotesreconciliation']['denom_50']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_20']) ? ($transaction['Specialnotesreconciliation']['denom_20']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_10']) ? ($transaction['Specialnotesreconciliation']['denom_10']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_5']) ? ($transaction['Specialnotesreconciliation']['denom_5']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_2']) ? ($transaction['Specialnotesreconciliation']['denom_2']):0),
            (isset($transaction['Specialnotesreconciliation']['denom_1']) ? ($transaction['Specialnotesreconciliation']['denom_1']):0),
            (isset($transaction['Specialnotesreconciliation']['total_amount']) ?  GetNumberFormat(($transaction['Specialnotesreconciliation']['total_amount']),'$'):0)
             
            
        );
    }
    if ($all == 'amount') {
        $exportArr = array(
            $startNo++,
            (isset($transaction['FileProccessingDetail']['Branch']['regiones']) ? $regiondata[$transaction['FileProccessingDetail']['Branch']['regiones']] : ''),
            (isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''), 
            (isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''),
            (isset($transaction['Specialnotesreconciliation']['teller_name']) ? $transaction['Specialnotesreconciliation']['teller_name'] : '-'),
            (isset($transaction['Specialnotesreconciliation']['trans_datetime']) ? trim(date("m-d-Y h:i A",strtotime($transaction['Specialnotesreconciliation']['trans_datetime']))) : ''),
            (isset($transaction['Specialnotesreconciliation']['transaction_category']) ? str_replace("Online ","",$transaction['Specialnotesreconciliation']['transaction_category']) : '-'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_100']*100)?($transaction['Specialnotesreconciliation']['denom_100'])*100:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_50']*50)?($transaction['Specialnotesreconciliation']['denom_50'])*50:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_20']*20)?($transaction['Specialnotesreconciliation']['denom_20'])*20:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_10']*10)?($transaction['Specialnotesreconciliation']['denom_10'])*10:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_5']*5)?($transaction['Specialnotesreconciliation']['denom_5'])*5:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_2']*2)?($transaction['Specialnotesreconciliation']['denom_2'])*2:0,'$'),
            GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_1']*1)?($transaction['Specialnotesreconciliation']['denom_1'])*1:0,'$'),
            (isset($transaction['Specialnotesreconciliation']['total_amount']) ?  GetNumberFormat(($transaction['Specialnotesreconciliation']['total_amount']),'$'):0)
             
            
        );
    }
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('Specialnotesreconciliations');
echo $this->CSV->render($filename);

?>