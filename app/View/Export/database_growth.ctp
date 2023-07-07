<?php
$data = array(
    __('Sr. No.'),
    __('Table Name'),
    __('Check Date'),
    __('Size'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($databaseGrowths as $databaseGrowth):
    $exportArr = array(
        $startNo++,
        (!empty($databaseGrowth['DatabaseGrowth']['table_name']) ? $databaseGrowth['DatabaseGrowth']['table_name'] : ' '),
        (!empty($databaseGrowth['DatabaseGrowth']['check_date']) ? showdatetime($databaseGrowth['DatabaseGrowth']['check_date']) : ''),
        (!empty($databaseGrowth['DatabaseGrowth']['size']) ? $databaseGrowth['DatabaseGrowth']['size'] : ''),
      
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('DatabaseGrowth');
echo $this->CSV->render($filename);
?>
