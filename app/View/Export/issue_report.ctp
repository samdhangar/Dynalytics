<?php
$data = array(
    __('Sr. No.'),
    __('Client Name'),
    __('Date'),
    __('Branch Name'),
    __('DynaCore Station ID'),
    __('Total Error/Warnings'),
    __('Open Date'),
    __('Resolve Date'),
    __('Resolve By'),
    __('Last Note'),
);

$this->CSV->addRow(array_values($data));
$startNo = 1;
foreach ($tickets as $ticket):
    $resolvedDate = '';
    if ($ticket['Ticket']['status'] == 'Closed'):
        $resolvedDate = showdatetime($ticket['Ticket']['updated']);
    endif;
    
    $resolvedBy = '';
    if ($ticket['Ticket']['status'] == 'Closed' && isset($ticket['Dealer']['first_name'])):
        $resolvedBy = $ticket['Dealer']['first_name'];
    endif;
    
    $exportArr = array(
        $startNo++,
        (isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : ''),
        (isset($ticket['Ticket']['ticket_date']) ? showdatetime($ticket['Ticket']['ticket_date']) : ''),
        (isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : ''),
        (!empty($ticket['ErrorDetail']['FileProccessingDetail']['station']) ? $ticket['ErrorDetail']['FileProccessingDetail']['station'] : ''),
        (isset($ticket['Ticket']['ticket_count']) ? $ticket['Ticket']['ticket_count'] : ''),
        (isset($ticket['ErrorDetail']['start_date']) ? showdatetime($ticket['ErrorDetail']['start_date']) : ''),
        (isset($resolvedDate)? $resolvedDate : ''),
        (isset($resolvedBy )? $resolvedBy : ''),
        (isset($ticket['Ticket']['note']) ? $ticket['Ticket']['note'] : ''),
        
        
        
      
    );
    $this->CSV->addRow($exportArr);
endforeach;

$filename = getReportName('Issue');
echo $this->CSV->render($filename);
?>
