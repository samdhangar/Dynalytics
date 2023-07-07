<?php
$this->assign('pagetitle', __('Error Reporting'));
$this->Custom->addCrumb(__('Error Reporting'));


/**
 * Filter panel
 */
$searchPanelArray = array(
    'name' => 'MachineError',
    'options' => array(
        'id' => 'ErrorSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'error_reporting'), true),
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
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'error_reporting', 'all'), array('escape' => false, 'title' => __('Display the all the error/warning'), 'class' => 'btn btn-default')),
    'fields' => array(
        array(
            'name' => 'machine_type',
            'options' => array(
                'label' => __('Machine Type'),
                'type' => 'select',
                'empty' => __('Select Machine Type'),
                'options' => $machineName,
                'placeholder' => __('Select Type')
            )
        ),
        array(
            'name' => 'error_type',
            'options' => array(
                'label' => __('Error Type'),
                'type' => 'select',
                'empty' => __('Select Error Type'),
                'options' => $machineErrorType,
                'placeholder' => __('Select Error Type')
            )
        )
    )
);

echo $this->CustomForm->setSearchPanel($searchPanelArray);

?>

<div class="row">
    <div class="col-xs-12">
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
                            <?php $fieldCount = 11; ?>
                            <th width="2%">
                                <?php echo __('Sr. No.') ?>
                            </th>
                            <th width="10%">
                                <?php
                                echo $this->Paginator->sort('MachineType.name', __('Machine Type'));
                                ?>
                            </th>
                            <th width="10%">
                                <?php
                                echo $this->Paginator->sort('ErrorType.name', __('Error Type'));

                                ?>
                            </th>
                            <th width="10%">
                                <?php echo $this->Paginator->sort('MachineType.created',__('Occurance Time')) ?>
                            </th>
                            <th width="15%">
                                <?php
                                echo $this->Paginator->sort('MachineError.error_message', __('Generate Error'));
                                ?>
                            </th>
                        </tr>			
                    </thead>		
                    <tbody>
                        <?php
                        if (empty($machineErrorDetails)):
                            ?>
                            <tr>
                                <td colspan="<?php echo $fieldCount; ?>">
                                    <?php echo __('No Any Error Reporting'); ?>
                                </td>
                            </tr>
                            <?php
                        endif;
                        $startNo = 1;
                        foreach ($machineErrorDetails as $errorDetail):

                            ?>
                            <tr>
                                <td>
                                    <?php echo $startNo++; ?>
                                </td>
                                <td>
                                    <?php echo isset($errorDetail['MachineType']['name']) ? $errorDetail['MachineType']['name'] : ''; ?>
                                </td>
                                <td>
                                    <?php echo isset($errorDetail['ErrorType']['error_level']) ? $errorDetail['ErrorType']['error_level'] : ''; ?>
                                </td>
                                <td>
                                    <?php echo isset($errorDetail['MachineType']['created']) ? showdatetime($errorDetail['MachineType']['created']) : ''; ?>
                                </td>
                                <td>
                                    <?php echo isset($errorDetail['MachineError']['error_message']) ? $errorDetail['MachineError']['error_message'] : ''; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('pagination'); ?>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    function getBranches(compId)
    {
        loader('show');
        jQuery.ajax({
            type: 'post',
            url: BaseUrl + "company_branches/get_branches/" + compId,
            success: function (response) {
                loader('hide');
                jQuery('#TicketBranchId').html(response);
            },
            error: function (e) {
                loader('hide');
            }
        });
    }
    function getStations(branchId)
    {
        loader('show');
        jQuery.ajax({
            type: 'post',
            url: BaseUrl + "company_branches/get_stations/" + branchId,
            data: {data: jQuery('#TicketBranchId').val()},
            success: function (response) {
                loader('hide');
                jQuery('#TicketStationId').html(response);
            },
            error: function (e) {
                loader('hide');
            }
        });
    }
    jQuery(document).ready(function () {

    });
</script>