<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Machine', $type));
$this->Custom->addCrumb(__('Machine'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Machine', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="box box-primary">
    <div class="overflow-hide-break">
            <?php echo $this->Form->create('Machine', array('class' => 'form-validate', 'type' => 'file')); ?>
        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
             echo $this->Form->input('status', array('type' => 'hidden', 'value' =>'1'));
              echo $this->Form->input('company_id', array('type' => 'hidden' , 'value' =>''));
               echo $this->Form->input('branches', array('id' => 'analyRegionId', 'label' => __('Branch: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-12')));
                       
            echo $this->Form->input('machine', array('placeholder' => 'Machine','label' => __('Machine'), 'class' => 'form-control', 'div' => array('class' => 'form-group')));

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
                'name' => array('required' => 1)
            ),
            'Messages' => array(
                'name' => array('required' => __('Please enter Machine'))));



        echo $this->Form->setValidation($arrValidation);

        ?>

<?php echo $this->Form->end(); ?>
    </div>
</div>
