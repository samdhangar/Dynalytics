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
    __('Activity Report Id'),
    __('Denom 100'),
    __('Denom 50'),
    __('Denom 20'),
    __('Denom 10'),
    __('Denom 5'),
    __('Denom 2'),
    __('Denom 1'),
    __('Coin'),
    __('Net Total'),
    __('Message')
    
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($netCashes as $netCash):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($netCash['FileProccessingDetail']['Company']['first_name']) ? $netCash['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($netCash['FileProccessingDetail']['Branch']['name']) ? $netCash['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['station']) ? $netCash['NetCashUsageActivityReport']['station'] : ''),
        (isset($netCash['FileProccessingDetail']['file_date']) ? showdate($netCash['FileProccessingDetail']['file_date']) : ''),
        (isset($netCash['NetCashUsageActivityReport']['activity_report_id']) ? $netCash['NetCashUsageActivityReport']['activity_report_id'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_100']) ? $netCash['NetCashUsageActivityReport']['denom_100'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_50']) ? $netCash['NetCashUsageActivityReport']['denom_50'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_20']) ? $netCash['NetCashUsageActivityReport']['denom_20'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_10']) ? $netCash['NetCashUsageActivityReport']['denom_10'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_5']) ? $netCash['NetCashUsageActivityReport']['denom_5'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_2']) ? $netCash['NetCashUsageActivityReport']['denom_2'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['denom_1']) ? $netCash['NetCashUsageActivityReport']['denom_1'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['coin']) ? $netCash['NetCashUsageActivityReport']['coin'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['net_total']) ? $netCash['NetCashUsageActivityReport']['net_total'] : ''),
        (isset($netCash['NetCashUsageActivityReport']['message']) ? $netCash['NetCashUsageActivityReport']['message'] : ''),
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('NetCashUsage');
echo $this->CSV->render($filename);
?>
