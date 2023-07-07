<div class="table-responsive">
    <?php
    if ($ticketType == 'new'):
        echo $this->Form->create('User', array('class' => 'assignAllForm', 'url' => array('controller' => 'tickets', 'action' => 'assigns'), 'id' => 'UserEditProfileForm'));
    endif;

    ?>
    <table class="table table-bordered">
        <thead>
            <?php $ticketColumns = 12; ?>
            <?php $srNo = 1; ?>
            <tr>
                <?php if (!isSupportDealer() && $ticketType == 'new'): ?>
                    <th>
                        <?php
                        $disabled = empty($ticketData) ? 'disabled' : '';
                        echo $this->Form->input('delete.all', array($disabled, 'hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'checkAll'));

                        ?>
                    </th>
                <?php endif; ?>
                <th>
                    <?php echo __('Sr. No.') ?>
                </th>
                <th>
                    <?php echo __('Client Name') ?>
                </th>
				<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                <th>
                    <?php echo __('Branch Name') ?>
                </th>
				<?php }?>
                <th>
                    <?php echo __('Date') ?>
                </th>
                <th>
                    <?php echo __('DynaCore Station ID') ?>
                </th>
                <th>
                    <?php echo __('Errors/Warnings') ?>
                </th>
                <?php if ($ticketType != 'new'): ?>


                    <th>
                        <?php echo __('Acknowledge Date') ?>
                    </th>
                    <th>
                        <?php echo __('Assigned To') ?>
                    </th>
                    <th>
                        <?php echo __('Note') ?>
                    </th>
                    <th>
                        <?php echo __('Updated On') ?>
                    </th>
                    <?php if ($ticketType == 'open'): ?>

                        <th>
                            <?php echo __('Action') ?>
                        </th>
                    <?php endif; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ticketData)): ?>
                <tr>
                    <td colspan="<?php echo $ticketColumns; ?>">
                        <?php echo __('No %s tickets', $ticketType); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($ticketData as $ticket): ?>
                <tr>
                    <?php if (!isSupportDealer() && $ticketType == 'new'): ?>
                        <td>
                            <?php
                            echo $this->Form->input('delete.id.', array('value' => encrypt($ticket['Ticket']['id']), 'hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'deleteRow', 'required' => false));

                            ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php echo $srNo++ ?>
                    </td>
                    <td class="table-text">
                        <?php echo $ticket['Company']['first_name']; ?>
                    </td>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <td class="table-text">
                        <?php echo $ticket['Branch']['name']; ?>
                    </td>
					<?php }?>
                    <td align="center">
                        <?php echo showdate($ticket['Ticket']['ticket_date']); ?>
                    </td>
                    <td>
                        <?php echo $ticket['Ticket']['station']; ?>
                    </td>
                    <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']) ?>">
                        <?php
                        $ticket['Ticket']['error'] = $ticket['Ticket']['error'] . ' ' . $ticket['Ticket']['warning'];
                        echo cropDetail($ticket['Ticket']['error'], 30);
                        ?>
                    </td>
                    <?php if ($ticketType != 'new'): ?>
                        <td align="center">
                            <?php echo showdate($ticket['Ticket']['acknowledge_date']); ?>
                        </td>
                        <td class="table-text">
                            <?php echo $ticket['Dealer']['name']; ?>
                        </td>
                        <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']); ?>">
                            <?php echo cropDetail($ticket['Ticket']['note'], 30); ?>
                        </td>
                        <td>
                            <?php echo showdate($ticket['Ticket']['updated']); ?>
                        </td>
                        <?php if ($ticketType == 'open'): ?>

                            <td>
                                <?php
                                if ($ticket['Ticket']['status'] != 'Closed') {
                                    if(isSupportDealer() && empty($ticket['Ticket']['is_acknowledge'])){
                                        echo $this->Html->link('', array('controller' => 'tickets', 'action' => 'add_ack', encrypt($ticket['Ticket']['id'])), array('icon'=>'fa-location-arrow','title'=>__('Click here to add acknowledge to ticket'),'data-message' => __('Are you sure want to ack to this ticket?'),'class' => 'ticketStatus'));
                                    }
                                    echo $this->Html->link('', array('controller' => 'tickets', 'action' => 'status_change', encrypt($ticket['Ticket']['id']), 'Closed'), array('icon'=>'fa-remove','title'=>__('Click here to close ticket'),'data-message' => __('Are you sure want to change the status to closed?'), 'class' => 'ticketStatus'));
                                }

                                ?>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if (!isSupportDealer() && $ticketType == 'new' && !empty($ticketData)): ?>
            <tfoot>
                <tr>
                    <td colspan="<?php echo $ticketColumns ?>">
                        <div class="deleteMultiple">
                            <?php
                            
                            echo $this->Form->input('dealer_id', array('label' => false, 'class' => 'dealerId disabledBtn', 'disabled', 'empty' => __('Select Support'), 'div' => array('class' => 'no-margin paddingRight5')));
                            echo $this->Form->submit(__('Assigned'), array('icon' => 'fa-plus', 'class' => 'btn btn-primary btn-xs disabled disabledBtn', 'id' => 'DeleteBtn1'));
                            echo '</div>';

                            ?>
                        </div>
                    </td>
                </tr>
            </tfoot>
        <?php endif; ?>
       
    </table>
    <div class="box-footer clearfix">



<?php echo $this->element('pagination'); ?>
</div>
    <?php
    if ($ticketType == 'new'):

        ?>

        <?php
        echo $this->Form->setValidation(array(
            'Rules' => array(
                'dealer_id' => array(
                    'required' => 1
                )),
            'Messages' => array(
                'dealer_id' => array(
                    'required' => __('Please select support')
                )
            )
            )
        );
        echo $this->Form->end();
    endif;

    ?>
</div>
