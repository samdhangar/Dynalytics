<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Station', $type));
$this->Custom->addCrumb(__('Stations'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Station', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>

<div class="row">

    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">

            <div class="panel-body">


        <?php echo $this->Form->create('Station', array('class' => 'form-validate', 'type' => 'file', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>
        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
            if (isCompany()) {
                echo $this->Form->input('company_id', array('value' => $companyId, 'type' => 'hidden'));
            } else {
                echo $this->Form->input('company_id', array());
            }
            echo $this->Form->input('branch_id', array('empty' => __('Select Branch')));
            echo $this->Form->input('name', array('placeholder' => __('Enter station name')));

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
                'company_id' => array('required' => 1),
                'branch_id' => array('required' => 1),
                'name' => array('required' => 1, 'maxlength' => 7, 'number' => 1)
            ),
            'Messages' => array(
                'company_id' => array('required' => __('Please enter company')),
                'branch_id' => array('required' => __('Please select branch')),
                'name' => array(
                    'required' => __('Please enter station name'),
                    'maxlength' => __('Please enter station name having maximum 7 digits'),
                    'number' => __('Please enter numeric value')
                )
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
