<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Faq', $type));
$this->Custom->addCrumb(__('Faqs'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Faq', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>


<div class="row">

    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
          <?php echo $this->Form->create('Faq', array('inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group')))); ?>

            <div class="panel-body">


              <div class="row">
                  <div class="col-md-6">
                      <?php
                      echo $this->Form->input('id', array('type' => 'hidden'));
                      echo $this->Form->input('question', array('id' => 'AddFaqQuestion', 'type' => 'text', 'div' => array('class' => 'form-group required')));
                      echo $this->Form->input('answer', array('id' => 'FaqAnswer', 'div' => array('class' => 'form-group required')));
                      echo $this->Ck->load('FaqAnswer');

                      ?>
                  </div>
                  <div class="col-md-6">
                      <?php
                      echo $this->Form->input('user_role', array('type' => 'select', 'empty' => __('Select user role')));
                      echo $this->Form->input('order_no', array('lable' => __('Display order no')));
                      echo $this->Form->input('status', array('empty' => __('Select status'), 'options' => array('active' => __('Active'), 'inactive' => __('Inactive'))));

                      ?>
                  </div>





              </div>








<div class="form-action">
<?php echo $this->Form->submit(__('Save'), array('div' => false, 'class' => 'btn btn-primary')); ?>
&nbsp;&nbsp;
<?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
</div>


<?php
$arrValidation = array(
    'Rules' => array(
        'user_role' => array(
            'required' => 1
        ),
        'question' => array(
            'required' => 1,
            'minlength' => 2,
            'maxlength' => 50,
        ),
        'answer' => array(
            'required' => 1,
            'minlength' => 10,
            'maxlength' => 1000,
        ),
        'order_no' => array(
            'required' => 1,
            'positiveNumber' => 1
        ),
        'status' => array(
            'required' => 1
        )
    ),
    'Messages' => array(
        'user_role' => array(
            'required' => __('Please select user role.')
        ),
        'question' => array(
            'required' => __('Please enter question of faq'),
            'minlength' => __('Please enter question of faq with minimum 2 characters.'),
            'maxlength' => __('Please enter question of faq with maximum 50 characters.'),
        ),
        'answer' => array(
            'required' => __('Please enter answer of faq'),
            'minlength' => __('Please enter answer of faq with minimum 10 characters.'),
            'maxlength' => __('Please enter answer of faq with maximum 1000 characters.'),
        ),
        'order_no' => array(
            'required' => __('Please enter display order no. of faq.'),
            'positiveNumber' => __('Please enter valid display order no. of faq.')
        ),
        'status' => array(
            'required' => __('Please select status of faq.')
        )
    )
);
echo $this->Form->setValidation($arrValidation);

?>
<?php echo $this->Form->end(); ?>



            </div>
        </div>

<?php echo $this->Form->end(); ?>

    </div>

</div>

<!--Rightbar Chat-->

<!--/Rightbar Chat-->
