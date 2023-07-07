<?php
$data = array(
    __('Sr. No.'),
    __('DynaCore Station ID'),
    __('File Name'),
    __('Coin'),
    __('Starting Inventory'),
    __('Net Adjustment'),
    __('Total'),
    __('Message'),
    __('Denom 1'),
    __('Denom 2'),
    __('Denom 5'),
    __('Denom 10'),
    __('Denom 20'),
    __('Denom 50'),
    __('Denom 100'),
    __('Created Date'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($inventorys as $inventory):
    
    $exportArr = array(
        $startNo++,
        (isset($inventory['Inventory']['station']) ? $inventory['Inventory']['station'] : ''),
        (isset($inventory['FileProccessingDetail']['filename']) ? $inventory['FileProccessingDetail']['filename'] : ''),
        (isset($inventory['Inventory']['coin']) ? $inventory['Inventory']['coin'] : ''),
        (isset($inventory['Inventory']['starting_inventory']) ? $inventory['Inventory']['starting_inventory'] : ''),
        (isset($inventory['Inventory']['net_adjustments']) ? $inventory['Inventory']['net_adjustments'] : ''),
        (isset($inventory['Inventory']['total']) ? $inventory['Inventory']['total'] : ''),
        (isset($inventory['Inventory']['message']) ? $inventory['Inventory']['message'] : ''),
        (isset($inventory['Inventory']['denom_1']) ? $inventory['Inventory']['denom_1'] : ''),
        (isset($inventory['Inventory']['denom_2']) ? $inventory['Inventory']['denom_2'] : ''),
        (isset($inventory['Inventory']['denom_5']) ? $inventory['Inventory']['denom_5'] : ''),
        (isset($inventory['Inventory']['denom_10']) ? $inventory['Inventory']['denom_10'] : ''),
        (isset($inventory['Inventory']['denom_20']) ? $inventory['Inventory']['denom_20'] : ''),
        (isset($inventory['Inventory']['denom_50']) ? $inventory['Inventory']['denom_50'] : ''),
        (isset($inventory['Inventory']['denom_100']) ? $inventory['Inventory']['denom_100'] : ''),
        (isset($inventory['Inventory']['created_date']) ? date('m/d/y h:i:s a',  strtotime($inventory['Inventory']['created_date'])) : ''),
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('InventoryByTeller');
echo $this->CSV->render($filename);
?>
