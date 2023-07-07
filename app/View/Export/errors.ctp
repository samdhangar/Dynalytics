<?php
$data = array(
    __('Sr. No.'),
    __('Region'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Datetime'),
    __('Time By Hour'),
    __('No. of transaction'), 
);
$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($errors as $error):

    $notificationMessage = '';
    if (isset($error['Ticket'][0]['ticket_date'])):
        $notificationMessage = showdatetime($error['Ticket'][0]['ticket_date']);
    endif;

    $acknowledgeDateTime = '';
    if (!empty($error['Ticket'][0]['is_acknowledge']) && isset($error['Ticket'][0]['acknowledge_date'])):
        $acknowledgeDateTime = showdatetime($error['Ticket'][0]['acknowledge_date']);
    endif;

    $resolvedDateTime = '';
    if (isset($error['Ticket'][0]['status']) && ($error['Ticket'][0]['status'] == 'Closed')):
        $resolvedDateTime = showdatetime($error['Ticket'][0]['updated']);
    endif;

    $supportEngineer = '';
    if (!empty($error['Ticket'][0]['dealer_id'])):
        $supportEngineer = showdatetime($error['Ticket'][0]['Dealer']['first_name']);
    endif;

    $exportArr = array(
        $startNo++,
        (isset($error['regions']['name']) ? $error['regions']['name'] : ''),
        (isset($error['FileProccessingDetail']['Branch']['name']) ? $error['FileProccessingDetail']['Branch']['name'] : ''),
        (isset($error['FileProccessingDetail']['station']) ? $error['FileProccessingDetail']['station'] : ''),
        (isset($error['TransactionDetail']['trans_datetime']) ? date("m/d/Y",strtotime($error['TransactionDetail']['trans_datetime'])) : ''),
        (isset($error['TransactionDetail']['trans_datetime']) ?  date("H",strtotime($error['TransactionDetail']['trans_datetime'])) : ''),
        (isset($error['TransactionDetail']['total_transaction']) ? ($error['TransactionDetail']['total_transaction']) : ''),
         
    );

    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('Error');
echo $this->CSV->render($filename);
