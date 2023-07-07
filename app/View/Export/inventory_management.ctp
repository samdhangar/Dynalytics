<?php
$data = array(
    __('Sr. No.'),
    __('Date'),
    __('Time'),
    __('DynaCore Station ID'),
    // __('File Name'),
    __('Coin Dispenser'),
    __('Starting Inventory'),
    __('Net Adjustment'),
    __('Total'),
    __('Denom 100'),
    __('Denom 50'),
    __('Denom 20'),
    __('Denom 10'),
    __('Denom 5'),
    __('Denom 2'),
    __('Denom 1'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($inventorys as $inventory):
    if ($all == 'count') {
        $exportArr = array(
            $startNo++,
            (isset($inventory['FileProccessingDetail']['file_date']) ? trim(date('m-d-Y',  strtotime($inventory['FileProccessingDetail']['file_date']))) : ''),
            (isset($inventory['Inventory']['entry_timestamp']) ? date("h:m:s a", strtotime($inventory['Inventory']['entry_timestamp'])) : ''),
            (isset($inventory['Inventory']['station']) ? $temp_station[$inventory['Inventory']['station']] : ''),
            // (isset($inventory['FileProccessingDetail']['filename']) ? $inventory['FileProccessingDetail']['filename'] : ''),
            (isset($inventory['Inventory']['coin']) ? $inventory['Inventory']['coin'] : '0'),
            (isset($inventory['Inventory']['starting_inventory']) ? $inventory['Inventory']['starting_inventory'] : ''),
            (isset($inventory['Inventory']['net_adjustments']) ? $inventory['Inventory']['net_adjustments'] : ''),
            (isset($inventory['Inventory']['total']) ? $inventory['Inventory']['total'] : ''),
            (isset($inventory['Inventory']['denom_100']) ? $inventory['Inventory']['total_denom_100'] : ''),
            (isset($inventory['Inventory']['denom_50']) ? $inventory['Inventory']['total_denom_50'] : ''),
            (isset($inventory['Inventory']['denom_20']) ? $inventory['Inventory']['total_denom_20'] : ''),
            (isset($inventory['Inventory']['denom_10']) ? $inventory['Inventory']['total_denom_10'] : ''),
            (isset($inventory['Inventory']['denom_5']) ? $inventory['Inventory']['total_denom_5'] : ''),
            (isset($inventory['Inventory']['denom_2']) ? $inventory['Inventory']['total_denom_2'] : ''),
            (isset($inventory['Inventory']['denom_1']) ? $inventory['Inventory']['total_denom_1'] : ''),
            
        );
    }
    if ($all == 'amount') {
        $exportArr = array(
            $startNo++,
            (isset($inventory['FileProccessingDetail']['file_date']) ? trim(date('m-d-Y',  strtotime($inventory['FileProccessingDetail']['file_date']))) : ''),
            (isset($inventory['Inventory']['entry_timestamp']) ? date("h:m:s a", strtotime($inventory['Inventory']['entry_timestamp'])) : ''),
            (isset($inventory['Inventory']['station']) ? $temp_station[$inventory['Inventory']['station']] : ''),
            // (isset($inventory['FileProccessingDetail']['filename']) ? $inventory['FileProccessingDetail']['filename'] : ''),
            (isset($inventory['Inventory']['coin']) ? $inventory['Inventory']['coin'] : '0'),
            isset($inventory['Inventory']['starting_inventory']) ? GetNumberFormat($inventory['Inventory']['starting_inventory'],'$') : '0',
            isset($inventory['Inventory']['net_adjustments']) ? GetNumberFormat($inventory['Inventory']['net_adjustments'],'$') : '0',
            (isset($inventory['Inventory']['total']) ? $inventory['Inventory']['total'] : ''),
            GetNumberFormat(($inventory['Inventory']['denom_100']*100)?($inventory['Inventory']['total_denom_100'])*100:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_50']*50)?($inventory['Inventory']['total_denom_50'])*50:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_20']*20)?($inventory['Inventory']['total_denom_20'])*20:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_10']*10)?($inventory['Inventory']['total_denom_10'])*10:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_5']*5)?($inventory['Inventory']['total_denom_5'])*5:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_2']*2)?($inventory['Inventory']['total_denom_2'])*2:0,'$'),
            GetNumberFormat(($inventory['Inventory']['denom_1']*1)?($inventory['Inventory']['total_denom_1'])*1:0,'$'),
            
        );
    }
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('InventoryManagement');
echo $this->CSV->render($filename);
?>
