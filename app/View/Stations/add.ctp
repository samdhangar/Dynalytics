<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s DynaCore Station', $type));
$this->Custom->addCrumb(__('DynaCore Station'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s DynaCore Station', $type));
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
                echo $this->Form->input('company_id', array('onchange'=>'getBranches(this.value)', 'id' => 'dealerCompany','empty' => __('Select All')));
            }
            echo $this->Form->input('branch_id', array('empty' => __('Select Branch'), 'id' => 'companyBranch','empty' => __('Select All'), 'label' => __('Branch Name')));
            echo $this->Form->input('location_category', array('type' => 'select', 'id' => 'location_category', 'label' => __('Location Type: '), 'empty' => __('Select Location Type'), 'options' => $location_list, 'default' => 'all', 'class' => 'form-control'));
            echo $this->Form->input('station_code', array('placeholder' => __('Enter DynaCore Station ID'), 'label' => __('DynaCore Station ID')));
            echo $this->Form->input('name', array('placeholder' => __('Enter DynaLytics Station ID'), 'label' => __('DynaLytics Station ID')));
            echo $this->Form->input('serial_no', array('placeholder' => __('Enter DynaCore serial no'),  'label' => __('DynaCore Serial No')));

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
                'locaion_category' => array('required' => 1),
                'name' => array('required' => 1, 'maxlength' => 200),
                'station_code' => array('required' => 1, 'maxlength' => 16),
            ),
            'Messages' => array(
                'company_id' => array('required' => __('Please enter company')),
                'branch_id' => array('required' => __('Please select branch')),
                'locaion_category' => array('required' => __('Please select Locaion Category')),
                'name' => array(
                    'required' => __('Please enter station name'),
                    'maxlength' => __('Please enter station name having maximum 7 digits'),
                    'number' => __('Please enter numeric value')
                ),
        ));

        echo $this->Form->setValidation($arrValidation);

        ?>

        <?php echo $this->Form->end(); ?>
      </div>
  </div>



</div>

</div>
<script>
    function getBranches(companyId)
    {
        console.log(jQuery('#dealerCompany').val());
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/"+ companyId,
            type:'post',
            data: {data:jQuery('#dealerCompany').val()},
            success:function(response){
                loader('hide');
                comsole.log(url);
                jQuery('#companyBranch').html(response);
            },
            error:function(e){
                loader('hide');
            }
        });
    }
</script>
<!--Rightbar Chat-->

<!--/Rightbar Chat-->
