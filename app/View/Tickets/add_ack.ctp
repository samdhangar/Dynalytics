<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <?php echo __('Add Acknowledgement'); ?>
    </h4>
</div>
<?php echo $this->Form->create('Ticket', array('url' => array('controller' => 'tickets', 'action' => 'add_ack', encrypt($ticketId)))); ?>
<div class="modal-body table-responsive">
    <?php
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->input('acknowledge_date', array('type' => 'hidden', 'value' => date('Y-m-d H:i:s')));
    echo $this->Form->input('is_acknowledge', array('type' => 'hidden', 'value' => 1));
    echo $this->Form->input('note', array('type' => 'textarea', 'class' => 'form-control', 'div' => array('class' => 'form-group required')));

    ?>
</div>
<div class="modal-footer clearfix">
    <?php echo $this->Form->submit(__('Save'), array('class' => 'btn btn-primary')); ?>
</div>
<?php
$arrValidation = array(
    'Rules' => array(
        'note' => array(
            'minlength' => 10,
            'maxlength' => 150,
            'required' => 1
        ),
    ),
    'Messages' => array(
        'note' => array(
            'minlength' => __('Please enter note with minimum 10 character.'),
            'maxlength' => __('Please enter note between 10 to 150 character.'),
            'required' => __('Please enter note')
        ),
    )
);
echo $this->Form->setValidation($arrValidation);
echo $this->Form->end();

?>