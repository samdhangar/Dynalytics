<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('Manager Name'),
    __('DynaCore Station ID'),
    __('Transaction Time'),
);


$this->CSV->addRow(array_values($data));
$startNo = 1;

$startNo = 1;
foreach ($valutBuys as $valutBuy):

    $exportArr = array(
        $startNo++,
        (isset($valutBuy['FileProccessingDetail']['Branch']['name']) ? $valutBuy['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($valutBuy['Manager']['first_name']) ? trim($valutBuy['Manager']['first_name']) : ''),
        (isset($valutBuy['ValutBuy']['station']) ? $valutBuy['ValutBuy']['station'] : ''),
        (isset($valutBuy['ValutBuy']['trans_datetime']) ? showdatetime($valutBuy['ValutBuy']['trans_datetime']) : ''),
    );


    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('VaultBuy');
echo $this->CSV->render($filename);
?>
