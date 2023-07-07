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

if (in_array("weekly", $uri_get) && in_array("branchWeekly", $uri_get)) {
    $data = array(
        __('Sr. No.'),
        __('Region'),
        __('Branch Name'),
        __($duration),
        __('Minimum Transactions'),
        __('Maximum Transactions'),
        __('Total Transactions'),
        
    );
}elseif (in_array("weekly", $uri_get) && in_array("stationWeekly", $uri_get)) {
    $data = array(
        __('Sr. No.'),
        __('Region'),
        __('Branch Name'),
        __('Station Name'),
        __($duration),
        __('Minimum Transactions'),
        __('Maximum Transactions'),
        __('Total Transactions'),
        
    );
}elseif (in_array("weekly", $uri_get) && in_array("userWeekly", $uri_get)) {
    $data = array(
        __('Sr. No.'),
        __('Region'),
        __('Branch Name'),
        __('Station Name'),
        __('User Name'),
        __($duration),
        __('Minimum Transactions'),
        __('Maximum Transactions'),
        __('Total Transactions'),
        
    );
}
foreach ($tellerNames_Arr as $key => $value) {
    if (in_array("weekly", $uri_get) && in_array("userdetails",$uri_get) && in_array($value,$uri_get)) {
        $data = array(
            __('Sr. No.'),
            __('Region'),
            __('Branch Name'),
            __('Station Name'),
            __('User Name'),
            __($duration),
            __('Minimum Transactions'),
            __('Maximum Transactions'),
            __('Total Transactions'),
            
        );   
    }
}

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
    if (in_array("weekly", $uri_get) && in_array("branchWeekly", $uri_get)) {
        $exportArr = array(
            $startNo++,
            (isset($all_transactions['m']['regiones']) ? $regiones[$all_transactions['m']['regiones']] : ''),
            (isset($all_transactions['m']['branch_id']) ? $branches[$all_transactions['m']['branch_id']] : '-'),
            (isset($duration_data) ? $duration_data : '0'), 
            (isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0),
            (isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0),
            (isset($all_transactions[0]['sum(m.COUNT)']) ? $all_transactions[0]['sum(m.COUNT)'] : 0),
        );
    }elseif (in_array("weekly", $uri_get) && in_array("stationWeekly", $uri_get)) {
        $exportArr = array(
            $startNo++,
            (isset($all_transactions['m']['regiones']) ? $regiones[$all_transactions['m']['regiones']] : ''),
            (isset($all_transactions['m']['branch_id']) ? $branches[$all_transactions['m']['branch_id']] : '-'),
            (isset($all_transactions['m']['station']) ? $temp_station[$all_transactions['m']['station']] : '-'),
            (isset($duration_data) ? $duration_data : '0'), 
            (isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0),
            (isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0),
            (isset($all_transactions[0]['sum(m.COUNT)']) ? $all_transactions[0]['sum(m.COUNT)'] : 0),
        );
    }elseif (in_array("weekly", $uri_get) && in_array("userWeekly", $uri_get)) {
        $exportArr = array(
            $startNo++,
            (isset($all_transactions['m']['regiones']) ? $regiones[$all_transactions['m']['regiones']] : ''),
            (isset($all_transactions['m']['branch_id']) ? $branches[$all_transactions['m']['branch_id']] : '-'),
            (isset($all_transactions['m']['station']) ? $temp_station[$all_transactions['m']['station']] : '-'),
            (isset($all_transactions['m']['teller_name']) ? $all_transactions['m']['teller_name'] : '-'),
            (isset($duration_data) ? $duration_data : '0'), 
            (isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0),
            (isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0),
            (isset($all_transactions[0]['sum(m.COUNT)']) ? $all_transactions[0]['sum(m.COUNT)'] : 0),
        );
    }
    foreach ($tellerNames_Arr as $key => $value) {
        if (in_array("weekly", $uri_get) && in_array("userdetails",$uri_get) && in_array($value,$uri_get)) {
            $exportArr = array(
                $startNo++,
                (isset($all_transactions['m']['regiones']) ? $regiones[$all_transactions['m']['regiones']] : ''),
                (isset($all_transactions['m']['branch_id']) ? $branches[$all_transactions['m']['branch_id']] : '-'),
                (isset($all_transactions['m']['station']) ? $temp_station[$all_transactions['m']['station']] : '-'),
                (isset($all_transactions['m']['teller_name']) ? $all_transactions['m']['teller_name'] : '-'),
                (isset($duration_data) ? $duration_data : '0'), 
                (isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0),
                (isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0),
                (isset($all_transactions[0]['sum(m.COUNT)']) ? $all_transactions[0]['sum(m.COUNT)'] : 0),
            ); 
        }
    }
    $this->CSV->addRow($exportArr);
endforeach;
if (in_array("weekly", $uri_get) && in_array("branchWeekly", $uri_get)) {
    $filename = getReportName('Weekly_TransactionDetails_by_branch_');
}elseif (in_array("weekly", $uri_get) && in_array("stationWeekly", $uri_get)) {
    $filename = getReportName('Weekly_TransactionDetails_by_station_');
}elseif (in_array("weekly", $uri_get) && in_array("userWeekly", $uri_get)) {
    $filename = getReportName('Weekly_TransactionDetails_by_user_');
}
foreach ($tellerNames_Arr as $key => $value) {
    if (in_array("weekly", $uri_get) && in_array("userdetails",$uri_get) && in_array($value,$uri_get)) {
        $filename = getReportName('Weekly_TransactionDetails_by_'.$value.'_');
    }
}
echo $this->CSV->render($filename);