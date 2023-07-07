<?php
$data= array(
    __('Sr. No.'),
    __('Company Name'),
    __('Branch Name'),
    __('File Name'),
    __('File Date'),
    __('No. of Transaction'),
    __('Message'),
    __('Message Date'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($unidentifyMessages as $unidentifyMessage):
    $exportArr= array(
        $startNo++,
        (!empty($unidentifyMessage['FileProccessingDetail']['Company']['first_name']) ? $unidentifyMessage['FileProccessingDetail']['Company']['first_name'] : ''),
        (!empty($unidentifyMessage['FileProccessingDetail']['Branch']['name']) ? $unidentifyMessage['FileProccessingDetail']['Branch']['name'] : ''),
        (!empty($unidentifyMessage['FileProccessingDetail']['filename']) ? $unidentifyMessage['FileProccessingDetail']['filename'] : ''),
        (!empty($unidentifyMessage['FileProccessingDetail']['file_date']) ? $unidentifyMessage['FileProccessingDetail']['file_date'] : ''),
        (!empty($unidentifyMessage['FileProccessingDetail']['transaction_number']) ? $unidentifyMessage['FileProccessingDetail']['transaction_number'] : ''),
        (isset($unidentifyMessage['Message']['message']) ? $unidentifyMessage['Message']['message'] : ''),
        (isset($unidentifyMessage['Message']['datetime']) ? showdatetime($unidentifyMessage['Message']['datetime']) : ''),
        
      
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('UnidentifyMessage');
echo $this->CSV->render($filename);
?>
