<?php
$this->assign('pagetitle', __('Error Reporting'));
$this->Custom->addCrumb(__('Configuration'));
//generate search panel
/**
 * Filter panel
 */
$searchPanelArray = array(
    'name' => 'MachineError',
    'options' => array(
        'id' => 'ErrorSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-3'
            )
        )
    ),
    'searchDivClass' => 'col-md-6',
    'search' => array(
        'title' => 'Search ',
        'options' => array(
            'id' => 'HistorySearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the error/warning'), 'class' => 'btn btn-default')),
    'fields' => array(
        array(
            'name' => 'machine_type_id',
            'options' => array(
                'label' => __('Machine Type'),
                'type' => 'select',
                'empty' => __('Select Machine Type'),
                'placeholder' => __('Select Type')
            )
        ),
        array(
            'name' => 'error_type_id',
            'options' => array(
                'label' => __('Error Type'),
                'type' => 'select',
                'empty' => __('Select Error Type'),
                'placeholder' => __('Select Error Type')
            )
        )
    )
);

echo $this->CustomForm->setSearchPanel($searchPanelArray);

?>

<div class="box box-primary">           
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $columns = 5; ?>
                    <th width="5%">
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <th width="15%">
                        <?php
                        echo $this->Paginator->sort('MachineType.name', __('Machine type'));

                        ?>
                    </th>
                    <th width="15%">
                        <?php
                        echo $this->Paginator->sort('ErrorType.error_level', __('Error type'));

                        ?>
                    </th>
                    <th width="15%">
                        <?php
                        echo $this->Paginator->sort('error_message', __('Error Message'));

                        ?>
                    </th>
                    <th width="5%" class="actions text-center"><?php echo __('Error Status'); ?></th>
                    <th width="5%" class="actions text-center"><?php echo __('Action'); ?></th>
                </tr>			
            </thead>		
            <tbody>
                <?php if (empty($machineErrors)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($machineErrors as $machineError): ?>
                        <tr>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <td>
                                <?php
                                echo $machineError['MachineType']['name'];

                                ?>
                            </td>
                            <td>
                                <?php
                                echo $machineError['ErrorType']['error_level'];

                                ?>
                            </td>
                            <td>
                                <?php
                                echo $machineError['MachineError']['error_message'];

                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $status = isset($dealerErrors[$machineError['MachineError']['id']]) ? 'no' : 'yes';
                                echo $this->Custom->getToggleButton($status, 'userStatusChange', array('data-uid' => $machineError['MachineError']['id'], 'data-id' => 'userStatus_' . $machineError['MachineError']['id']), array('yes', 'no'));

                                ?>

                            </td>
                            <td class="text-center">
                                <?php
                                $disabled = ($status == 'no') ? 'disabled' : '';
                                echo $this->Html->link('', array('controller' => 'ticket_configs', 'action' => 'add'), array(
                                    'escape' => false,
                                    'class' => 'addTicketConfig ' . $disabled,
                                    'data-machineid' => $machineError['MachineError']['id'],
                                    'icon' => 'add',
                                    'title' => __('Add Custom Ticket Config')
                                ));

                                ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>			
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<div class="modal fade" id="TicketConfigModel" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            echo $this->Form->create('TicketConfig', array(
                'id' => 'TicketConfigForm',
                'type' => 'file',
                'url' => array(
                    'controller' => 'ticket_configs',
                    'action' => 'add'
                ),
                'inputDefaults' => array(
                    'dir' => 'ltl',
                    'class' => 'form-control',
                    'div' => array(
                        'class' => 'required form-group'
                    )
                )
            ));

            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo __('Add Ticket Config'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
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
            <div class="modal-footer clearfix">
                <?php
                echo $this->Form->submit(__('Save'), array('class' => 'btn btn-primary margin-right10', 'div' => false));

                ?>
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
    jQuery(document).ready(function () {
        validateSearch("ErrorSearchForm", ["MachineErrorMachineTypeId", "MachineErrorErrorTypeId"]);
        jQuery('#TicketConfigForm').on('submit', function (e) {
            e.preventDefault();
            if (jQuery(this).valid()) {
                loader('show');
                jQuery.ajax({
                    url: jQuery(this).attr('action'),
                    dataType: 'json',
                    async: false,
                    method: 'post',
                    data: jQuery(this).serializeArray(),
                    success: function (response) {
                        if (response.error != undefined) {
                            if (response.error.machine_error_id != undefined) {
                                errorDiv = '<span class="errorDV">' + response.error.machine_error_id[0] + '</span>';
                                jQuery('#TicketConfigMachineErrorId').parent().find('.authError').html(errorDiv);
                                jQuery('#TicketConfigMachineErrorId').parent().find('.authError').css('display', 'block');
                            }
                        } else {
                            window.location.href = "<?php echo Router::url(array('controller'=>'machine_errors','action'=>'index'),true)?>";
                        }
                        loader('hide');
                    }
                });
            }
        })
        jQuery('.addTicketConfig').on('click', function (e) {
            e.preventDefault();
            loader('show');
            var machineId = jQuery(this).data('machineid');
            if (jQuery(this).hasClass('disabled')) {
            } else {
                jQuery.ajax({
                    url: BaseUrl + "machine_errors/dealer_errors/html",
                    async: false,
                    success: function (response) {
                        jQuery('#TicketConfigMachineErrorId').html(response);
                        jQuery('#TicketConfigMachineErrorId').val(machineId)
                    }
                });
                jQuery('#TicketConfigModel').modal('show');
                loader('hide');
            }
        });
        jQuery('.userStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'yes' : 'no';
            var $this = jQuery(this);

            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status.toUpperCase())) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'yes' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.parents('tr').find('.addTicketConfig').attr('disabled', 'disabled');
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        alert(response.message);
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });
    });
</script>