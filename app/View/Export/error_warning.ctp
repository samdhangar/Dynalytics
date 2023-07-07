<?php
$data = array(
    __('Sr. No.'), 
    __('Branch Name'),
    __('Station(s)'),
    __('Date'),
    __('Time'),
    __('Type of Transaction'),
    __('Severity'),
    __('Error Code'),
    __('Description'), 
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($tickets as $ticket):
    
    $stationsName = '';
    foreach ($ticket['Branch']['station'] as $key => $station):
            $stationsName = $stationsName . ' ' . $station['name'] . ((count($ticket['Branch']['station']) > 1) ? ',' : '');
    endforeach;
    $ticketStatus = '';
    if ($ticket['Ticket']['error_warning_status'] == 'error') :
        $ticketStatus = $ticket['Ticket']['error'];
    else:
        $ticketStatus = $ticket['Ticket']['warning'];
    endif;
    
    $exportArr = array(
        $startNo++,
        (isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : ''),
        (isset($ticket['Ticket']['station']) ? $ticket['Ticket']['station'] : ''),
        (isset($ticket['Ticket']['ticket_date']) ? date("m-d-Y", strtotime($ticket['Ticket']['ticket_date'])) : ''),
        (isset($ticket['Ticket']['ticket_date']) ? date("h:m:s a", strtotime($ticket['Ticket']['ticket_date'])) : ''),
        (isset($ticket['ErrorTypes']['transaction_type']) ? $ticket['ErrorTypes']['transaction_type'] : ''),
        (isset($ticket['ErrorTypes']['severity']) ?  $ticket['ErrorTypes']['severity'] : ''),
        (isset($ticket['ErrorTypes']['error_code']) ? $ticket['ErrorTypes']['error_code'] : ''),
        (isset($ticket['Ticket']['error_warning_status']) ? $ticketStatus : ''),
    );
//    debug($exportArr); 
$this->CSV->addRow($exportArr);
endforeach;
$filename = getReportName('ErrorWarning');
echo $this->CSV->render($filename);
?>
