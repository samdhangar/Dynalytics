<?php
$uri_get = (($this->request->here()));
$uri_get = explode('/', $uri_get);
if (in_array("daily", $uri_get)) {
    $duration = 'Days';
} elseif (in_array("weekly", $uri_get)) {
    $duration = 'Weeks';
} elseif (in_array("monthly", $uri_get)) {
    $duration = 'Months';
} elseif (in_array("yearly", $uri_get)) {
    $duration = 'Year';
}elseif (in_array("byHour", $uri_get)) {
    $duration = 'Hours';
}

$data = array(
    __('Sr. No.'),
    __('Region'),
    __($duration),
    __('Minimum Transactions'),
    __('Maximum Transactions'),
    __('Total Transactions'),
    
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($allData as $all_transactions): 
    if (in_array("daily", $uri_get)) {
         $duration_data =  $all_transactions['m']['DAY'];
    } elseif (in_array("weekly", $uri_get)) {
        $duration_data =  $all_transactions['m']['WEEK'];
    } elseif (in_array("monthly", $uri_get)) {
        $duration_data =  $all_transactions['m']['MONTHNAME'];
    } elseif (in_array("yearly", $uri_get)) {
        $duration_data =  $all_transactions['m']['YEAR'];
    } elseif (in_array("byHour", $uri_get)) {
        $duration_data =  $all_transactions['m']['HOUR'];
    }
        $exportArr = array(
            $startNo++,
            (isset($all_transactions['m']['regiones']) ? $regiones[$all_transactions['m']['regiones']] : ''),
            (isset($duration_data) ? $duration_data : '0'), 
            (isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0),
            (isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0),
            (isset($all_transactions[0]['sum(m.COUNT)']) ? $all_transactions[0]['sum(m.COUNT)'] : 0),
        );
    $this->CSV->addRow($exportArr);
endforeach;
if (in_array("daily", $uri_get)) {
    $filename = getReportName('Daily_TransactionDetails_');   
} elseif (in_array("weekly", $uri_get)) {
    $filename = getReportName('Weekly_TransactionDetails_');
} elseif (in_array("monthly", $uri_get)) {
    $filename = getReportName('Monthly_TransactionDetails_');
} elseif (in_array("yearly", $uri_get)) {
    $filename = getReportName('Yearly_TransactionDetails_');
} elseif (in_array("byHour", $uri_get)) {
    $filename = getReportName('Hour_TransactionDetails_');
}
echo $this->CSV->render($filename);
?>
