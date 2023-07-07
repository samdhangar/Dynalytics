<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Configuration', $type));
$this->Custom->addCrumb(__('Configuration'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Configuration', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>

<?php
if($type='Edit' && $this->request->data['Configuration']['Is_default']==1){
    $branch_condition='';
}else{
    $branch_condition=1;
} ?>
<div class="box box-primary">
    <div class="overflow-hide-break">
            <?php echo $this->Form->create('Configuration', array('class' => 'form-validate', 'type' => 'file')); ?>
        <div class="box-body box-content" >
             <div class="col-md-12 col-sm-12 form-group row">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
             echo $this->Form->input('status', array('type' => 'hidden', 'value' =>'1'));
              echo $this->Form->input('name', array('id' => 'name', 'label' => __('New Config Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-4')));

              echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-4')));


  echo $this->Form->input('machine_id', array('id' => 'analyMachineId', 'label' => __('Machine: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-4')));
  //echo $this->Form->input('machine_id', array('onchange'=>'getStations(this.value)','id' => 'analyMachineId', 'label' => __('Machine: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-4')));
/* echo "<label for='analyBranchId' >&nbsp;</label><br>";*/   
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $1</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('1_lower', array('id' => '1_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('1_upper', array('id' => '1_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 

?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $2</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('2_lower', array('id' => '2_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('2_upper', array('id' => '2_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $5</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('5_lower', array('id' => '5_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('5_upper', array('id' => '5_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $10</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('10_lower', array('id' => '10_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('10_upper', array('id' => '10_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $20</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('20_lower', array('id' => '20_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('20_upper', array('id' => '20_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $50</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('50_lower', array('id' => '50_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('50_upper', array('id' => '50_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
    <?php 
echo "<label  class='col-md-4'><br> $100</label> ";  

echo "<label  class='col-md-2'><br> Lower Limit</label> ";  
 
echo $this->Form->input('100_lower', array('id' => '100_lower',  'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
echo "<label  class='col-md-2'><br> Upper Limit</label> ";  
 
echo $this->Form->input('100_upper', array('id' => '100_upper', 'type'=>'text', 'label' => __('  '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2'))); 
?>
</div>
<div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
   
            <div class="form-action">
                <?php echo $this->Form->submit(__('Save'), array('onclick'=>'submitValidatetion()','div' => false, 'class' => 'btn btn-primary')); ?>
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
            'required' => $branch_condition
        ),
        '1_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '1_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 1
        ),
        '2_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '2_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 2
        ),
        '5_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '5_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 5
        ),
        '10_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '10_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 10
        ),
        '20_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '20_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 20
        ),
        '50_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '50_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 50
        ),
        '100_lower' => array(
            'required' => 1,
             'number' => 1
        ),
        '100_upper' => array(
            'required' => 1,
             'number' => 1,
              'greater' => 100
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
        '1_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '1_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '2_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '2_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '5_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '5_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '10_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '10_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '20_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '20_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '50_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only'),
             'greater' => __('Please Enter Upper Limit Grater Than Lower Limit')
        ),
        '50_upper' => array(
            'required' => __('Please Enter Upper Limit'),
             'number' => __('Please enter Number Only')
        ),
        '100_lower' => array(
            'required' => __('Please Enter Lower Limit'),
             'number' => __('Please enter Number Only')
        ),
        '100_upper' => array(
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

      function submitValidatetion()
    {
        
       /* console.log(jQuery('#analyBranchId').val());
          
        jQuery.ajax({
            url: BaseUrl + "/configuration/validate/"+$('#analyBranchId').val(),
            type:'post',
            data: {data:jQuery('#analyBranchId').val(),data2:jQuery('#analyBranchId').val(),data3:'<?php echo $type ?>'},
            success:function(response){
                 
                jQuery('#analyMachineId').html(response);
            },
            error:function(e){
                
            }
        });
       */
    }
   
</script>