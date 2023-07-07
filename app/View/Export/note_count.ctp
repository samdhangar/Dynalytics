<?php
$data = array(
    __('Sr. No.'),
    __('Date Range'),
    __('Region'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Transaction type'),
    __('Denom 100'),
    __('Denom 50'),
    __('Denom 20'),
    __('Denom 10'),
    __('Denom 5'),
    __('Denom 2'),
    __('Denom 1'),
    
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($transactions as $transaction): 
    $exportArr_first = array(
        $startNo++,
        ($this->Session->check('Report.GlobalFilter') ? getReportFilter($this->Session->read('Report.GlobalFilter')) : '-'),
        (isset($transaction['regions']['name']) ? $transaction['regions']['name'] : ''),
        isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : '',
        isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : '',
        isset($transaction['FileProccessingDetail']['station']) ? "Notes Deposited" : '',
        (isset($transaction[0]['deposit_denom_100']) ? $transaction[0]['deposit_denom_100'] : 0),
        (isset($transaction[0]['deposit_denom_50']) ? $transaction[0]['deposit_denom_50'] : 0),
        (isset($transaction[0]['deposit_denom_20']) ? $transaction[0]['deposit_denom_20'] : 0),
        (isset($transaction[0]['deposit_denom_10']) ? $transaction[0]['deposit_denom_10'] : 0),
        (isset($transaction[0]['deposit_denom_5']) ? $transaction[0]['deposit_denom_5'] : 0),
        (isset($transaction[0]['deposit_denom_2']) ? $transaction[0]['deposit_denom_2'] : 0),
        (isset($transaction[0]['deposit_denom_1']) ? $transaction[0]['deposit_denom_1'] : 0),
        
    );
    $this->CSV->addRow($exportArr_first);
    $exportArr_second = array(
        $startNo++,
        ($this->Session->check('Report.GlobalFilter') ? getReportFilter($this->Session->read('Report.GlobalFilter')) : '-'),
        (isset($transaction['regions']['name']) ? $transaction['regions']['name'] : ''),
        isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : '',
        isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : '',
        isset($transaction['FileProccessingDetail']['station']) ? "Notes Dispensed" : '',
        (isset($transaction[0]['withdrawal_denom_100']) ? $transaction[0]['withdrawal_denom_100'] : 0),
        (isset($transaction[0]['withdrawal_denom_50']) ? $transaction[0]['withdrawal_denom_50'] : 0),
        (isset($transaction[0]['withdrawal_denom_20']) ? $transaction[0]['withdrawal_denom_20'] : 0),
        (isset($transaction[0]['withdrawal_denom_10']) ? $transaction[0]['withdrawal_denom_10'] : 0),
        (isset($transaction[0]['withdrawal_denom_5']) ? $transaction[0]['withdrawal_denom_5'] : 0),
        (isset($transaction[0]['withdrawal_denom_2']) ? $transaction[0]['withdrawal_denom_2'] : 0),
        (isset($transaction[0]['withdrawal_denom_1']) ? $transaction[0]['withdrawal_denom_1'] : 0),
        
    );
    $this->CSV->addRow($exportArr_second);
    $exportArr_third = array(
        $startNo++,
        ($this->Session->check('Report.GlobalFilter') ? getReportFilter($this->Session->read('Report.GlobalFilter')) : '-'),
        (isset($transaction['regions']['name']) ? $transaction['regions']['name'] : ''),
        isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : '',
        isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : '',
        isset($transaction['FileProccessingDetail']['station']) ? "Total Notes" : '',
        $transaction[0]['deposit_denom_100'] +  $transaction[0]['withdrawal_denom_100'],
        $transaction[0]['deposit_denom_50'] +  $transaction[0]['withdrawal_denom_50'],
        $transaction[0]['deposit_denom_20'] +  $transaction[0]['withdrawal_denom_20'],
        $transaction[0]['deposit_denom_10'] +  $transaction[0]['withdrawal_denom_10'],
        $transaction[0]['deposit_denom_5'] +  $transaction[0]['withdrawal_denom_5'],
        $transaction[0]['deposit_denom_2'] +  $transaction[0]['withdrawal_denom_2'],
        $transaction[0]['deposit_denom_1'] +  $transaction[0]['withdrawal_denom_1'],
        
    );
    $this->CSV->addRow($exportArr_third);

endforeach;

$filename = getReportName('NoteCount');
echo $this->CSV->render($filename);

?>