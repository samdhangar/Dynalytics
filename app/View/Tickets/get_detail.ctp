<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <?php echo __('Ticket Detail'); ?>
    </h4>
    <?php
    if (!empty($ticket['Ticket']['dealer_id'])):
        echo $this->Html->link(__('Notify Support'), array('controller' => 'tickets', 'action' => 'notify', encrypt($ticket['Ticket']['id'])), array('icon' => 'fa-send', 'title' => __('Notify Support'), 'class' => 'btn btn-primary', 'escape' => false));
    endif;

    ?>
</div>
<div class="modal-body table-responsive">
    <?php
    if (empty($ticket)):
        echo __('Invalid Ticket');
    else:

        ?>
        <table class="table table-bordered">
            <tr>
                <td>
                    <?php echo __('History Type:'); ?>
                </td>
                <td>
                    <?php echo $this->Custom->showStatus($ticket['Ticket']['error_warning_status']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Client Name:'); ?>
                </td>
                <td>
                    <?php echo $ticket['Company']['first_name']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Branch Name:'); ?>
                </td>
                <td>
                    <?php echo $ticket['Branch']['name']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Date :'); ?>
                </td>
                <td>
                    <?php echo showdate($ticket['Ticket']['ticket_date']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    echo $this->Custom->showStatus($ticket['Ticket']['error_warning_status']) . ':';

                    ?>
                </td>
                <td>
                    <?php
                    echo $ticket['Ticket']['error_warning_status'] == 'error' ? $ticket['Ticket']['error'] : $ticket['Ticket']['warning'];

                    ?>
                </td>
            </tr>
        </table>
        <?php $srNo = 1; ?>
        <label class="h5">
            <strong>
                <?php echo __('Ticket Messages'); ?>
            </strong>
        </label>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php $fieldCount = 4; ?>
                    <th>
                        <?php echo __('Sr. No.') ?>
                    </th>
                    <th>
                        <?php echo __('Text') ?>
                    </th>
                    <th>
                        <?php echo __('Added By') ?>
                    </th>
                    <th>
                        <?php echo __('Added On') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (empty($ticket['TicketMessage'])):

                    ?>
                    <tr>
                        <td colspan="<?php echo $fieldCount; ?>">
                            <?php echo __('No Any Message.'); ?>
                        </td>
                    </tr>
                    <?php
                endif;
                foreach ($ticket['TicketMessage'] as $ticketMessage):

                    ?>
                    <tr>
                        <td>
                            <?php echo $srNo++; ?>
                        </td>
                        <td>
                            <?php
                            echo $ticketMessage['note'];

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($ticketMessage['Dealer']['first_name']) ? $ticketMessage['Dealer']['first_name'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo showdatetime($ticketMessage['created']);

                            ?>
                        </td>
                    </tr>
                    <?php
                endforeach;

                ?>
            </tbody>
        </table>
    <?php
    endif;

    ?>
</div>
<div class="modal-footer clearfix">
</div>