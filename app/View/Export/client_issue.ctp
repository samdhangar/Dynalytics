<?php
$data_first = array(
    __('Sr. No.'),
    __('Client Name'),
    __('Date Period'),
);

$data_second = array();
if (!empty($companyDetail)):
    $data_second = array(
        __('Branch Name'),
        __('DynaCore Station ID'),
    );
endif;

$data_third = array(
    __('Total Error/Warning'),
    __('Last Occurrence Date'),
);
$data = array_merge($data_first,$data_second,$data_third) ;
$this->CSV->addRow(array_values($data));
$startNo = 1;

foreach ($tickets as $ticket):
    
    $exportArr_first = array(
        $startNo++,
        (isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : ''),
        (showdatetime($this->Session->read('Report.ClientIssueReport.start_date')) .' To ' . showdatetime($this->Session->read('Report.ClientIssueReport.end_date'))),
    );

    $exportArr_second = array();
    if (!empty($companyDetail)):
        $exportArr_second = array(
            (isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : ''),
            (!empty($ticket['ErrorDetail']['FileProccessingDetail']['station']) ? $ticket['ErrorDetail']['FileProccessingDetail']['station'] : ''),
        );
    endif;
    
    $exportArr_third = array(
        (isset($ticket['Ticket']['ticket_count']) ? $ticket['Ticket']['ticket_count'] : ''),
        (isset($ticket['Ticket']['ticket_date']) ? showdatetime($ticket['Ticket']['ticket_date']) : ''),
    );
    
    
$exportArr = array_merge($exportArr_first, $exportArr_second , $exportArr_third);
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('ClientIssue');
echo $this->CSV->render($filename);
?>
