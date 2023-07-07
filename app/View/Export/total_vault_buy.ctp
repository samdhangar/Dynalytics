<?php
$data = array(
    __('Sr. No.'),
    __('Branch Name'),
    __('Denom 1'),
    __('Denom 2'),
    __('Denom 5'),
    __('Denom 10'),
    __('Denom 20'),
    __('Denom 50'),
    __('Denom 100'),
    __('Coin'),
    __('Total'),
    __('Created Date'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($totalValutBuys as $totalValutBuy):
    
    $exportArr = array(
        $startNo++,
        (isset($totalValutBuy['FileProccessingDetail']['Branch']['name']) ? $totalValutBuy['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_1']) ? $totalValutBuy['TotalVaultBuy']['denom_1'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_2']) ? $totalValutBuy['TotalVaultBuy']['denom_2'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_5']) ? $totalValutBuy['TotalVaultBuy']['denom_5'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_10']) ? $totalValutBuy['TotalVaultBuy']['denom_10'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_20']) ? $totalValutBuy['TotalVaultBuy']['denom_20'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_50']) ? $totalValutBuy['TotalVaultBuy']['denom_50'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['denom_100']) ? $totalValutBuy['TotalVaultBuy']['denom_100'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['coin']) ? $totalValutBuy['TotalVaultBuy']['coin'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['total']) ? $totalValutBuy['TotalVaultBuy']['total'] : ''),
        (isset($totalValutBuy['TotalVaultBuy']['created_date']) ? showdatetime($totalValutBuy['TotalVaultBuy']['created_date']) : ''),
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('TotalVaultBuys');
echo $this->CSV->render($filename);
?>
