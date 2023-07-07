<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Country', $type));
$this->Custom->addCrumb(__('Countries'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Country', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="panel panel-flat">
    <div class="panel-body">
            <?php echo $this->Form->create('Country', array('class' => 'form-validate', 'type' => 'file')); ?>
        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
            echo $this->Form->input('name', array('placeholder' => 'Country','label' => __('Country'), 'class' => 'form-control', 'div' => array('class' => 'form-group')));

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
                'name' => array('required' => __('Please enter country'))));



        echo $this->Form->setValidation($arrValidation);

        ?>

<?php echo $this->Form->end(); ?>
    </div>
</div>
