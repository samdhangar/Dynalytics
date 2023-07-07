<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Help page', $type));
$this->Custom->addCrumb(__('Help pages'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Help page', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>


<div class="row">

    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
          <?php echo $this->Form->create('Help', array('inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group')))); ?>

            <div class="panel-body">


              <div class="row">
                  <div class="col-md-6">
                      <?php
                      echo $this->Form->input('id', array('type' => 'hidden'));
                      echo $this->Form->input('title', array('id' => 'Addtitle', 'type' => 'text', 'div' => array('class' => 'form-group required')));
                      echo $this->Form->input('report_name', array('id' => 'Addtitle', 'type' => 'text', 'div' => array('class' => 'form-group required')));
                      echo $this->Form->input('description', array('id' => 'description', 'div' => array('class' => 'form-group required')));
                      echo $this->Ck->load('description');

                      ?>
                  </div>
                  <div class="col-md-6">
                      <?php
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
        'title' => array(
            'required' => 1,
            'minlength' => 2,
            'maxlength' => 50,
        ),
        'description' => array(
            'required' => 1,
            'minlength' => 10,
            'maxlength' => 1000,
        ),
        'status' => array(
            'required' => 1
        )
    ),
    'report_name' => array(
        'required' => 1,
        'minlength' => 10,
        'maxlength' => 1000,
    ),
    'Messages' => array(
        'title' => array(
            'required' => __('Please enter title of help page'),
            'minlength' => __('Please enter title of help page with minimum 2 characters.'),
            'maxlength' => __('Please enter title of help page with maximum 50 characters.'),
        ),
        'description' => array(
            'required' => __('Please enter answer of help page'),
            'minlength' => __('Please enter answer of help page with minimum 10 characters.'),
            'maxlength' => __('Please enter answer of help page with maximum 1000 characters.'),
        ),
        'status' => array(
            'required' => __('Please select status of help page.')
        ),
        'report_name' => array(
            'required' => __('Please select status of help page.'),
            'minlength' => __('Please enter answer of help page with minimum 10 characters.'),
            'maxlength' => __('Please enter answer of help page with maximum 1000 characters.'),
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
