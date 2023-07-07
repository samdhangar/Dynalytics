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
    __('Time'),

    __('Total Inventory $100'),
    __('Total Inventory $50'),
    __('Total Inventory $20'),
    __('Total Inventory $l0'),
    __('Total Inventory $5'),
    __('Total Inventory $2'),
    __('Total Inventory $1'),
    __('Grand Total'),

    __('Dispensable $100'),
    __('Dispensable $50'),
    __('Dispensable $20'),
    __('Dispensable $10'),
    __('Dispensable $5'),
    __('Dispensable $2'),
    __('Dispensable $1'),
    __('Dispensable Total'),

    __('Op Cassette $100'),
    __('Op Cassette $50'),
    __('Op Cassette $20'),
    __('Op Cassette $10'),
    __('Op Cassette $5'),
    __('Op Cassette $2'),
    __('Op Cassette $1'),
    __('Op Cassette Total'),

    __('Reject Cassette $100'),
    __('Reject Cassette $50'),
    __('Reject Cassette $20'),
    __('Reject Cassette $l0'),
    __('Reject Cassette $5'),
    __('Reject Cassette $2'),
    __('Reject Cassette $1'),
    __('Reject Cassette Total')
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;

foreach ($bills as $bill):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;
        if($exportCondition == 'count') {
            $exportArr_second = array(
        
                (isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''),
                (isset($bill['BillsActivityReport']['station']) ? $temp_station[$bill['BillsActivityReport']['station']] : ''),
                (isset($bill['FileProccessingDetail']['file_date']) ? showdate($bill['FileProccessingDetail']['file_date']) : ''),
                (isset($bill['BillsActivityReport']['entry_timestamp']) ? date("h:m:s a", strtotime($bill['BillsActivityReport']['entry_timestamp'])) : ''),
                (($bill['BillsActivityReport']['denom_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])),
                (($bill['BillsActivityReport']['denom_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])),
                (($bill['BillsActivityReport']['denom_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])),
                (($bill['BillsActivityReport']['denom_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])),
                (($bill['BillsActivityReport']['denom_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])),
                (($bill['BillsActivityReport']['denom_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])),
                (($bill['BillsActivityReport']['denom_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])),
                (((($bill['BillsActivityReport']['denom_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100']))) + ((($bill['BillsActivityReport']['denom_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50']))) + ((($bill['BillsActivityReport']['denom_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20']))) + ((($bill['BillsActivityReport']['denom_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10']))) + ((($bill['BillsActivityReport']['denom_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5']))) + ((($bill['BillsActivityReport']['denom_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2']))) + ((($bill['BillsActivityReport']['denom_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])))),
                (isset($bill['BillsActivityReport']['denom_100']) ? $bill['BillsActivityReport']['denom_100'] : '0'),
                (isset($bill['BillsActivityReport']['denom_50']) ? $bill['BillsActivityReport']['denom_50'] : '0'),
                (isset($bill['BillsActivityReport']['denom_20']) ? $bill['BillsActivityReport']['denom_20'] : '0'),
                (isset($bill['BillsActivityReport']['denom_10']) ? $bill['BillsActivityReport']['denom_10'] : '0'),
                (isset($bill['BillsActivityReport']['denom_5']) ? $bill['BillsActivityReport']['denom_5'] : '0'),
                (isset($bill['BillsActivityReport']['denom_2']) ? $bill['BillsActivityReport']['denom_2'] : '0'),
                (isset($bill['BillsActivityReport']['denom_1']) ? $bill['BillsActivityReport']['denom_1'] : '0'),
                ((($bill['BillsActivityReport']['denom_100'] + ($bill['BillsActivityReport']['denom_50']) + ($bill['BillsActivityReport']['denom_20']) + ($bill['BillsActivityReport']['denom_10']) + ($bill['BillsActivityReport']['denom_5']) + $bill['BillsActivityReport']['denom_2']) + ($bill['BillsActivityReport']['denom_1']))),
                (isset($bill['BillsActivityReport']['denom2_100']) ? $bill['BillsActivityReport']['denom2_100'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_50']) ? $bill['BillsActivityReport']['denom2_50'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_20']) ? $bill['BillsActivityReport']['denom2_20'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_10']) ? $bill['BillsActivityReport']['denom2_10'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_5']) ? $bill['BillsActivityReport']['denom2_5'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_2']) ? $bill['BillsActivityReport']['denom2_2'] : '0'),
                (isset($bill['BillsActivityReport']['denom2_1']) ? $bill['BillsActivityReport']['denom2_1'] : '0'),
                ((($bill['BillsActivityReport']['denom2_100'] + ($bill['BillsActivityReport']['denom2_50']) + ($bill['BillsActivityReport']['denom2_20']) + ($bill['BillsActivityReport']['denom2_10']) + ($bill['BillsActivityReport']['denom2_5']) + $bill['BillsActivityReport']['denom2_2']) + ($bill['BillsActivityReport']['denom2_1']))),
                (isset($bill['BillsActivityReport']['denom3_100']) ? $bill['BillsActivityReport']['denom3_100'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_50']) ? $bill['BillsActivityReport']['denom3_50'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_20']) ? $bill['BillsActivityReport']['denom3_20'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_10']) ? $bill['BillsActivityReport']['denom3_10'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_5']) ? $bill['BillsActivityReport']['denom3_5'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_2']) ? $bill['BillsActivityReport']['denom3_2'] : '0'),
                (isset($bill['BillsActivityReport']['denom3_1']) ? $bill['BillsActivityReport']['denom3_1'] : '0'),
                ((($bill['BillsActivityReport']['denom3_100'] + ($bill['BillsActivityReport']['denom3_50']) + ($bill['BillsActivityReport']['denom3_20']) + ($bill['BillsActivityReport']['denom3_10']) + ($bill['BillsActivityReport']['denom3_5']) + $bill['BillsActivityReport']['denom3_2']) + ($bill['BillsActivityReport']['denom3_1'])))
                
            );
        }
        if ($exportCondition == 'amount') {
            $exportArr_second = array(
        
                (isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''),
                (isset($bill['BillsActivityReport']['station']) ? $temp_station[$bill['BillsActivityReport']['station']] : ''),
                (isset($bill['FileProccessingDetail']['file_date']) ? showdate($bill['FileProccessingDetail']['file_date']) : ''),
                (isset($bill['BillsActivityReport']['entry_timestamp']) ? date("h:m:s a", strtotime($bill['BillsActivityReport']['entry_timestamp'])) : ''),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])) * 100), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])) * 50), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])) * 20), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])) * 10), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])) * 5), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])) * 2), '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])) * 1), '$'),
                GetNumberFormat((((($bill['BillsActivityReport']['denom_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])) * 100) + ((($bill['BillsActivityReport']['denom_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])) * 50) + ((($bill['BillsActivityReport']['denom_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])) * 20) + ((($bill['BillsActivityReport']['denom_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])) * 10) + ((($bill['BillsActivityReport']['denom_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])) * 5) + ((($bill['BillsActivityReport']['denom_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])) * 2) + ((($bill['BillsActivityReport']['denom_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])) * 1)), '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_100'] * 100) ? ($bill['BillsActivityReport']['denom_100']) * 100 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_50'] * 50) ? ($bill['BillsActivityReport']['denom_50']) * 50 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_20'] * 20) ? ($bill['BillsActivityReport']['denom_20']) * 20 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_10'] * 10) ? ($bill['BillsActivityReport']['denom_10']) * 10 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_5'] * 5) ? ($bill['BillsActivityReport']['denom_5']) * 5 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_2'] * 2) ? ($bill['BillsActivityReport']['denom_2']) * 2 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom_1'] * 1) ? ($bill['BillsActivityReport']['denom_1']) * 1 : 0, '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom_100'] * 100) + ($bill['BillsActivityReport']['denom_50'] * 50) + ($bill['BillsActivityReport']['denom_20'] * 20) + ($bill['BillsActivityReport']['denom_10'] * 10) + ($bill['BillsActivityReport']['denom_5'] * 5) + $bill['BillsActivityReport']['denom_2'] * 2) + ($bill['BillsActivityReport']['denom_1'] * 1)), '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_100'] * 100) ? ($bill['BillsActivityReport']['denom2_100']) * 100 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_50'] * 50) ? ($bill['BillsActivityReport']['denom2_50']) * 50 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_20'] * 20) ? ($bill['BillsActivityReport']['denom2_20']) * 20 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_10'] * 10) ? ($bill['BillsActivityReport']['denom2_10']) * 10 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_5'] * 5) ? ($bill['BillsActivityReport']['denom2_5']) * 5 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_2'] * 2) ? ($bill['BillsActivityReport']['denom2_2']) * 2 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom2_1'] * 1) ? ($bill['BillsActivityReport']['denom2_1']) * 1 : 0, '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom2_100'] * 100) + ($bill['BillsActivityReport']['denom2_50'] * 50) + ($bill['BillsActivityReport']['denom2_20'] * 20) + ($bill['BillsActivityReport']['denom2_10'] * 10) + ($bill['BillsActivityReport']['denom2_5'] * 5) + $bill['BillsActivityReport']['denom2_2'] * 2) + ($bill['BillsActivityReport']['denom2_1'] * 1)), '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_100'] * 100) ? ($bill['BillsActivityReport']['denom3_100']) * 100 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_50'] * 50) ? ($bill['BillsActivityReport']['denom3_50']) * 50 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_20'] * 20) ? ($bill['BillsActivityReport']['denom3_20']) * 20 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_10'] * 10) ? ($bill['BillsActivityReport']['denom3_10']) * 10 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_5'] * 5) ? ($bill['BillsActivityReport']['denom3_5']) * 5 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_2'] * 2) ? ($bill['BillsActivityReport']['denom3_2']) * 2 : 0, '$'),
                GetNumberFormat(($bill['BillsActivityReport']['denom3_1'] * 1) ? ($bill['BillsActivityReport']['denom3_1']) * 1 : 0, '$'),
                GetNumberFormat(((($bill['BillsActivityReport']['denom3_100'] * 100) + ($bill['BillsActivityReport']['denom3_50'] * 50) + ($bill['BillsActivityReport']['denom3_20'] * 20) + ($bill['BillsActivityReport']['denom3_10'] * 10) + ($bill['BillsActivityReport']['denom3_5'] * 5) + $bill['BillsActivityReport']['denom3_2'] * 2) + ($bill['BillsActivityReport']['denom3_1'] * 1)), '$')
                
            );
        }
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('BillActivity');
echo $this->CSV->render($filename);
?>
