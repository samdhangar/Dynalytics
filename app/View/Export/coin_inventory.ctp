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
    __('Coin Adjusted 1'),
    __('New Coin Total 1'),
    __('Coin adjusted 2'),
    __('New Coin Total 2'),
    __('Date'),
);
$data = array_merge($data_first, $data_second);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($coinInventorys as $coin):
    $exportArr_first = array(
        $startNo++,
    );
    if (!isCompany() && empty($companyDetail)):
        $exportArr_first = array(
            $startNo++,
            (isset($coin['FileProccessingDetail']['Company']['first_name']) ? $coin['FileProccessingDetail']['Company']['first_name'] : ''),
        );
    endif;

    $exportArr_second = array(
        (isset($coin['FileProccessingDetail']['Branch']['name']) ? $coin['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($coin['FileProccessingDetail']['station']) ? $coin['FileProccessingDetail']['station'] : ''),
        (isset($coin['CoinInventory']['coin_adjusted_1']) ? $coin['CoinInventory']['coin_adjusted_1'] : ''),
        (isset($coin['CoinInventory']['new_coin_total_1']) ? $coin['CoinInventory']['new_coin_total_1'] : ''),
        (isset($coin['CoinInventory']['coin_adjusted_2']) ? $coin['CoinInventory']['coin_adjusted_2'] : ''),
        (isset($coin['CoinInventory']['new_coin_total_2']) ? $coin['CoinInventory']['new_coin_total_2'] : ''),
        (isset($coin['FileProccessingDetail']['file_date']) ? showdatetime($coin['FileProccessingDetail']['file_date']) : ''),
    );
    $exportArr = array_merge($exportArr_first, $exportArr_second);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('CoinInventory');
echo $this->CSV->render($filename);
?>
