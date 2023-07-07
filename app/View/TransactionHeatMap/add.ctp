<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Transaction Heat Map', $type));
$this->Custom->addCrumb(__('Transaction Heat Map'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Transaction Heat Map', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
 
<div class="box box-primary">
    <div class="overflow-hide-break">
            <?php echo $this->Form->create('TransactionHeatMap', array('class' => 'form-validate', 'type' => 'file')); ?>
        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
             echo $this->Form->input('status', array('type' => 'hidden', 'value' =>'1'));
 
 
  ?>

             <div class="col-md-12 col-sm-12 row">
               <div class="col-md-2 col-sm-12"><label>&nbsp;</label><h5>Transaction Heat Map Name:</h5></div>
              <?php echo $this->Form->input('name', array('id' => 'name', 'label' => __(' '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

              ?>
              </div>

              <div class="col-md-12 col-sm-12 row">
                <div class="col-md-2 col-sm-12"><label>&nbsp;</label><h5>Branch Name:</h5></div>
               <?php echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __(' '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

               ?>
               </div>

               <div class="col-md-12 col-sm-12 row">
                 <div class="col-md-2 col-sm-12"><label>&nbsp;</label><h5>DynaCore Station ID:</h5></div>
                <?php   echo $this->Form->input('machine_id', array('id' => 'analyMachineId', 'label' => __(' '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

                ?>
                </div>
                <div class="col-md-12 col-sm-12 row">
                  <div class="col-md-2 col-sm-12"></div>
                  <div class="col-md-2 col-sm-12"><h4 style="margin-bottom:0px;">Lower Limit</h4></div>
                  <div class="col-md-2 col-sm-12"><h4 style="margin-bottom:0px;">Upper Limit</h4></div>
                </div>

                <div class="col-md-12 col-sm-12 row">

<div class='col-md-2'><label>&nbsp;</label><h5> Total Transactions</h5></div>
  <?php
echo $this->Form->input('trans_lower', array('id' => 'trans_lower',  'type'=>'text', 'label' => __(' '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2')));


echo $this->Form->input('trans_upper', array('id' => 'trans_upper', 'type'=>'text', 'label' => __(' '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2')));
 ?>

</div>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    
            </div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
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
            'minlength' => 8,
            'maxlength' => 50,
            'required' => 1,
            'alphaNumaric' =>''
        ),
        'branch_id' => array(
            'required' => 1
        ),
        'trans_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        'trans_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 'trans'
        ),

    ),
    'Messages' => array( 
         'name' => array( 
            'minlength' => __('Please enter  with minimum 8 character.'),
            'maxlength' => __('Please enter  between 2 to 50 character.'), 
            'required' => __('Please enter name'),
            'alphaNumaric' => __('Nmae Only Alpha Numaric')
        ),
        'branch_id' => array(
            'required' => __('Please Select Branch')
        ),
        'trans_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        'trans_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        )
    )
);

  

        echo $this->Form->setValidation($arrValidation);
        ?>

<?php echo $this->Form->end(); ?>
    </div>
</div>
<script type="text/javascript">
    function getStations(branchId)
    {

        console.log(jQuery('#analyBranchId').val());
        loader('show');   
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/"+ branchId,
            type:'post',
            data: {data:jQuery('#analyBranchId').val()},
            success:function(response){
                  loader('hide'); 
                jQuery('#analyMachineId').html(response);
            },
            error:function(e){
                
            }
        });
    }

</script>