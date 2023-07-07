<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Subscription', $type));
$this->Custom->addCrumb(__('Subscriptions'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Subscription', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();
$addModel = 'Subscription';

?>

<div class="row">

    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">

            <div class="panel-body">

              <?php
              echo $this->Form->create('Subscription', array(
                  'class' => 'form-validate', 'type' => 'file',
                  'inputDefaults' => array(
                      'div' => array('class' => 'form-group required'),
                      'class' => 'form-control'
                  )
              ));

              ?>
<div class="box-body box-content">
<?php
echo $this->Form->input('id', array('type' => 'hidden'));

echo $this->Form->input('name', array('placeholder' => 'Name', 'label' => __('Name')));
echo $this->Form->input('setup_cost', array('placeholder' => 'Total setup cost', 'label' => __('Total Setup Cost')));
echo $this->Form->input('branch_cost', array('placeholder' => 'Branch cost', 'label' => __('Branch Cost')));
echo $this->Form->input('charge', array('placeholder' => 'Charge', 'label' => __('Charge')));
echo $this->Form->input('type', array(
    'empty' => __('Select Subscption Type'),
    'label' => __('Subscription Type'),
    'options' => array(
        COMPANY => 'Financial Institution',
        DEALER => DEALER
    )
));
echo $this->Form->input('status', array('empty' => __('Select Status'), 'options' => array('active' => __('Active'), 'inactive' => __('InActive')), 'label' => __('Status')));

?>
<div class="form-action">
<?php echo $this->Form->submit(__('Save'), array('div' => false, 'class' => 'btn btn-primary')); ?>
&nbsp;&nbsp;
<?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
</div>
<?php
$arrValidation = array(
    'Rules' => array(
        'name' => array(
            'required' => 1,
            'minlength' => 2,
            'maxlength' => 50,
        ),
        'status' => array(
            'required' => 1
        ),
        'setup_cost' => array(
            'required' => 1,
            'min' => 1
        ),
        'branch_cost' => array(
            'required' => 1,
            'min' => 1
        ),
        'charge' => array(
            'required' => 1,
            'min' => 1
        ),
        'type' => array(
            'required' => 1
        ),
    ),
    'Messages' => array(
        'name' => array(
            'required' => __('Please enter name'),
            'minlength' => __('Subscription name having minimum 2 characters'),
            'maxlength' => __('Subscription name between 2 to 50 characters')
        ),
        'status' => array(
            'required' => __('Please select status')
        ),
        'setup_cost' => array(
            'required' => __('Please enter valid setup cost'),
            'min' => __('Please enter valid setup cost'),
        ),
        'branch_cost' => array(
            'required' => __('Please enter valid branch cost'),
            'min' => __('Please enter valid branch cost'),
        ),
        'charge' => array(
            'required' => __('Please enter valid charge'),
            'min' => __('Please enter valid charge'),
        ),
        'type' => array(
            'required' => __('Please select subscription type'),
        ),
));
echo $this->Form->setValidation($arrValidation);

?>
<?php echo $this->Form->end(); ?>



            </div>
        </div>



    </div>

</div>

<!--Rightbar Chat-->

<!--/Rightbar Chat-->
