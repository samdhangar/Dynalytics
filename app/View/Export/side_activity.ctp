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
    __('Activity Report Id'),
    __('DynaCore Station ID'),
    __('Side'),
    __('Type'),
    __('Denom 1'),
    __('Denom 2'),
    __('Denom 5'),
    __('Denom 10'),
    __('Denom 20'),
    __('Denom 50'),
    __('Denom 100'),
    __('Coin'),
    __('Non cash Dispence Total'),
    __('Total'),
    __('Machine Total'),
    __('Check Cashing'),
    __('Credit Card Advance'),
    __('No. Of Transaction'),
    __('Meta'),
    __('message'),
    __('Created Date'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($activity as $act):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($act['SideActivityReport']['activity_report_id']) ? $act['SideActivityReport']['activity_report_id'] : ''),
        (isset($act['SideActivityReport']['station']) ? $act['SideActivityReport']['station'] : ''),
        (isset($act['SideActivityReport']['side']) ? $act['SideActivityReport']['side'] : ''),
        (isset($act['SideActivityReport']['type']) ? $act['SideActivityReport']['type'] : ''),
        (isset($act['SideActivityReport']['denom_1']) ? $act['SideActivityReport']['denom_1'] : ''),
        (isset($act['SideActivityReport']['denom_2']) ? $act['SideActivityReport']['denom_2'] : ''),
        (isset($act['SideActivityReport']['denom_5']) ? $act['SideActivityReport']['denom_5'] : ''),
        (isset($act['SideActivityReport']['denom_10']) ? $act['SideActivityReport']['denom_10'] : ''),
        (isset($act['SideActivityReport']['denom_20']) ? $act['SideActivityReport']['denom_20'] : ''),
        (isset($act['SideActivityReport']['denom_50']) ? $act['SideActivityReport']['denom_50'] : ''),
        (isset($act['SideActivityReport']['denom_100']) ? $act['SideActivityReport']['denom_100'] : ''),
        (isset($act['SideActivityReport']['coin']) ? $act['SideActivityReport']['coin'] : ''),
        (isset($act['SideActivityReport']['non_cash_dispence_total']) ? $act['SideActivityReport']['non_cash_dispence_total'] : ''),
        (isset($act['SideActivityReport']['total']) ? $act['SideActivityReport']['total'] : ''),
        (isset($act['SideActivityReport']['machine_total']) ? $act['SideActivityReport']['machine_total'] : ''),
        (isset($act['SideActivityReport']['check_cashing']) ? $act['SideActivityReport']['check_cashing'] : ''),
        (isset($act['SideActivityReport']['credit_card_advance']) ? $act['SideActivityReport']['credit_card_advance'] : ''),
        (isset($act['SideActivityReport']['number_of_transactions']) ? $act['SideActivityReport']['number_of_transactions'] : ''),
        (isset($act['SideActivityReport']['meta']) ? $act['SideActivityReport']['meta'] : ''),
        (isset($act['SideActivityReport']['message']) ? $act['SideActivityReport']['message'] : ''),
        (isset($act['SideActivityReport']['created_date']) ? showdatetime($act['SideActivityReport']['created_date']) : ''),
        
        
        
        
      
    );
$exportArr = array_merge($exportArr_first,$exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('SideActivity');
echo $this->CSV->render($filename);
?>
