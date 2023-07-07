<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Ticket Config', $type));
$this->Custom->addCrumb(__('Ticket Configs'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Ticket Config', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <?php
        echo $this->Form->create('TicketConfig', array(
            'id' => 'TicketConfigForm',
            'inputDefaults' => array(
                'dir' => 'ltl',
                'class' => 'form-control',
                'div' => array(
                    'class' => 'required form-group'
                )
            )
        ));

        ?>

        <div class="box-body box-content">
            <?php
            echo $this->Form->input('id', array('type' => 'hidden'));
            echo $this->Form->input('dealer_id', array('type' => 'hidden'));
            echo $this->Form->input('company_id', array(
                'type' => 'select',
                'onchange' => 'getCompanyBranches(this.value)',
                'empty' => __('Select company')
            ));
            echo $this->Form->input('branch_id', array(
                'type' => 'select',
                'onchange' => 'getBranchStations(this.value)',
                'empty' => __('Select branch')
            ));
            echo $this->Form->input('station', array(
                'type' => 'select',
                'empty' => __('Select station')
            ));
            echo $this->Form->input('machine_error_id', array(
                'type' => 'select',
                'empty' => __('Select machine error'),
                'after' => '<div for="TicketConfigMachineErrorId" generated="true" class="authError"></div>'
            ));
            echo $this->Form->input('exceed_limit', array(
            ));

            ?>
        </div>
        <div class="form-action">
            <?php echo $this->Form->submit(__('Save'), array('div' => false, 'class' => 'btn btn-primary')); ?>
            &nbsp;&nbsp;
            <?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
        </div>

        <?php
        echo $this->Form->setValidation(array(
            'Rules' => array(
                'company_id' => array(
                    'required' => 1
                ),
                'branch_id' => array(
                    'required' => 1
                ),
                'station' => array(
                    'required' => 1
                ),
                'machine_error_id' => array(
                    'required' => 1
                ),
                'exceed_limit' => array(
                    'required' => 1,
                    'number' => 1
                )
            ),
            'Messages' => array(
                'company_id' => array(
                    'required' => __('Please select company'),
                ),
                'branch_id' => array(
                    'required' => __('Please select branch'),
                ),
                'station' => array(
                    'required' => __('Please select station'),
                ),
                'machine_error_id' => array(
                    'required' => __('Please select machine error'),
                ),
                'exceed_limit' => array(
                    'required' => __('Please enter exceed limit after this generate ticket'),
                    'number' => __('Please enter valid number')
                )
            )
        ));

        echo $this->Form->end();

        ?>
    </div>
</div>

<script type="text/javascript">
    function getCompanyBranches(companyId)
    {
        if (companyId != undefined && companyId != '') {
            loader('show');
            jQuery.ajax({
                url: BaseUrl + "company_branches/get_branches/" + companyId,
                success: function (response) {
                    jQuery('#TicketConfigBranchId').html(response);
                    loader('hide');
                }
            });
        }
    }
    function getBranchStations(branchId)
    {
        if (branchId != undefined && branchId != '') {
            loader('show');
            jQuery.ajax({
                url: BaseUrl + "company_branches/get_stations/" + branchId,
                success: function (response) {
                    jQuery('#TicketConfigStation').html(response);
                    loader('hide');
                }
            });
        }
    }
</script>