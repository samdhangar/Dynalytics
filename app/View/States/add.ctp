 <?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s State', $type));
$this->Custom->addCrumb(__('States'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s State', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();
$addModel = 'State';

?>


                <div class="row">

                    <div class="col-md-12 col-sm-12">
                        <div class="panel panel-flat">

                            <div class="panel-body">

                                 <?php echo $this->Form->create('State', array('class' => 'form-validate', 'type' => 'file')); ?>
        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));

            echo $this->Form->input('country_id', array('empty'=>__('Select Country'),'class' => 'form-control', 'div' => array('class' => 'form-group required')));

            echo $this->Form->input('name', array('placeholder' => 'State' , 'label' => __('State'), 'class' => 'form-control', 'div' => array('class' => 'form-group required')));

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
                'country_id' => array('required' => 1),
                'name' => array('required' => 1),
                'status' => array('required' => 1),
            ),
            'Messages' => array(
                'country_id' => array('required' => __('Please select country')),
                'name' => array('required' => __('Please enter state')),
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

      
