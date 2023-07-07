<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Date'),
    __('Denom weighting'),
    __('Tier Type'),
    __('Tier Text'),
    __('Demon 100'),
    __('Demon 50'),
     __('Demon 20'),
    __('Demon 10'),
    __('Demon 5'),
    __('Demon 1')
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($automixSettings as $act):
    $exportArr = array(
        $startNo++,
        (isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($act['AutomixSetting']['station']) ? $act['AutomixSetting']['station'] : ''),
        (isset($act['FileProccessingDetail']['file_date']) ? $act['FileProccessingDetail']['file_date'] : ''),
        (isset($act['AutomixSetting']['denom_weighting']) ? $act['AutomixSetting']['denom_weighting'] : ''),
        (isset($act['AutomixSetting']['tier_type']) ? $act['AutomixSetting']['tier_type'] : ''),
        (isset($act['AutomixSetting']['tier_text']) ? $act['AutomixSetting']['tier_text'] : ''),
        (isset($act['AutomixSetting']['denom_100']) ? $act['AutomixSetting']['denom_100'] : ''),
        (isset($act['AutomixSetting']['denom_50']) ? $act['AutomixSetting']['denom_50'] : ''),
        (isset($act['AutomixSetting']['denom_20']) ? $act['AutomixSetting']['denom_20'] : ''),
        (isset($act['AutomixSetting']['denom_10']) ? $act['AutomixSetting']['denom_10'] : ''),
        (isset($act['AutomixSetting']['denom_5']) ? $act['AutomixSetting']['denom_5'] : ''),
        (isset($act['AutomixSetting']['denom_1']) ? $act['AutomixSetting']['denom_1'] : '')
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('AutomixSettings');
echo $this->CSV->render($filename);

?>