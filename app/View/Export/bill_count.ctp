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
    __('Manager Name'),
    __('Assumed Count 100'),
    __('Actual Count 100'),
    __('Difference'),
     __('Assumed Count 50'),
    __('Actual Count 50'),
    __('Difference'),
   __('Assumed Count 20'),
    __('Actual Count 20'),
    __('Difference'),
    __('Assumed Count 10'),
    __('Actual Count 10'),
    __('Difference'), 
     __('Assumed Count 5'),
    __('Actual Count 5'),
    __('Difference'),
    __('Assumed Count 2'),
    __('Actual Count 2'),
    __('Difference'),
     __('Assumed Count 1'),
    __('Actual Count 1'),
    __('Difference'),
    __('Assumed Total'),
    __('Actual Total'),
    __('Difference Total'),
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

    $exportArr_second = array(
        (isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($bill['FileProccessingDetail']['station']) ? $bill['FileProccessingDetail']['station'] : ''),
        (isset($bill['FileProccessingDetail']['file_date']) ? showdatetime($bill['FileProccessingDetail']['file_date']) : ''),
        (isset($bill['Manager']['name']) ? trim($bill['Manager']['name']) : $bill['BillCount']['manager_id']),
        (isset($bill['BillCount']['_100_assumed_count']) ? $bill['BillCount']['_100_assumed_count'] : ''),
        (isset($bill['BillCount']['_100_actual_count']) ? $bill['BillCount']['_100_actual_count'] : ''),
        ($bill['BillCount']['_100_actual_count']-$bill['BillCount']['_100_assumed_count']),
        (isset($bill['BillCount']['_50_assumed_count']) ? $bill['BillCount']['_50_assumed_count'] : ''),
        (isset($bill['BillCount']['_50_actual_count']) ? $bill['BillCount']['_50_actual_count'] : ''),
        ($bill['BillCount']['_50_actual_count']-$bill['BillCount']['_50_assumed_count']),
        (isset($bill['BillCount']['_20_assumed_count']) ? $bill['BillCount']['_20_assumed_count'] : ''),
        (isset($bill['BillCount']['_20_actual_count']) ? $bill['BillCount']['_20_actual_count'] : ''),
        ($bill['BillCount']['_20_actual_count']-$bill['BillCount']['_20_assumed_count']),
        (isset($bill['BillCount']['_10_assumed_count']) ? $bill['BillCount']['_10_assumed_count'] : ''),
        (isset($bill['BillCount']['_10_actual_count']) ? $bill['BillCount']['_10_actual_count'] : ''),
        ($bill['BillCount']['_10_actual_count']-$bill['BillCount']['_10_assumed_count']), 
        (isset($bill['BillCount']['_5_assumed_count']) ? $bill['BillCount']['_5_assumed_count'] : ''),
        (isset($bill['BillCount']['_5_actual_count']) ? $bill['BillCount']['_5_actual_count'] : ''),
        ($bill['BillCount']['_5_actual_count']-$bill['BillCount']['_5_assumed_count']),
        (isset($bill['BillCount']['_2_assumed_count']) ? $bill['BillCount']['_2_assumed_count'] : ''),
        (isset($bill['BillCount']['_2_actual_count']) ? $bill['BillCount']['_2_actual_count'] : ''),
        ($bill['BillCount']['_2_actual_count']-$bill['BillCount']['_2_assumed_count']),
        (isset($bill['BillCount']['_1_assumed_count']) ? $bill['BillCount']['_1_assumed_count'] : ''),
        (isset($bill['BillCount']['_1_actual_count']) ? $bill['BillCount']['_1_actual_count'] : ''),
        ($bill['BillCount']['_1_actual_count']-$bill['BillCount']['_1_assumed_count']),
        (($bill['BillCount']['_1_assumed_count']*1)+($bill['BillCount']['_2_assumed_count']*2)+($bill['BillCount']['_5_assumed_count']*5)+($bill['BillCount']['_10_assumed_count']*10)+($bill['BillCount']['_20_assumed_count']*20)+($bill['BillCount']['_50_assumed_count']*50)+($bill['BillCount']['_100_assumed_count']*100)),
        (($bill['BillCount']['_1_actual_count']*1)+($bill['BillCount']['_2_actual_count']*2)+($bill['BillCount']['_5_actual_count']*5)+($bill['BillCount']['_10_actual_count']*10)+($bill['BillCount']['_20_actual_count']*20)+($bill['BillCount']['_50_actual_count']*50)+($bill['BillCount']['_100_actual_count']*100)),
        (($bill['BillCount']['_1_actual_count']*1)+($bill['BillCount']['_2_actual_count']*2)+($bill['BillCount']['_5_actual_count']*5)+($bill['BillCount']['_10_actual_count']*10)+($bill['BillCount']['_20_actual_count']*20)+($bill['BillCount']['_50_actual_count']*50)+($bill['BillCount']['_100_actual_count']*100))-(($bill['BillCount']['_1_assumed_count']*1)+($bill['BillCount']['_2_assumed_count']*2)+($bill['BillCount']['_5_assumed_count']*5)+($bill['BillCount']['_10_assumed_count']*10)+($bill['BillCount']['_20_assumed_count']*20)+($bill['BillCount']['_50_assumed_count']*50)+($bill['BillCount']['_100_assumed_count']*100)) 
    );
    $exportArr = array_merge($exportArr_first, $exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;
$filename = getReportName('BillCount');
echo $this->CSV->render($filename);
?>
