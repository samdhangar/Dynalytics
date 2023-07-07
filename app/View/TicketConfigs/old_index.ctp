<?php
$this->assign('pagetitle', __('Ticket Configs'));
$this->Custom->addCrumb(__('Configuration'));
/**
 * Filter panel
 */
$searchPanelArray = array(
    'name' => 'TicketConfig',
    'options' => array(
        'id' => 'TicketConfigForm',
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
                    <th width="12%">
                        <?php
                        echo __('Company');

                        ?>
                    </th>
                    <th width="12%">
                        <?php
                        echo __('Branch');

                        ?>
                    </th>
                    <th width="10%">
                        <?php
                        echo __('Station');

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
                    <th width="10%">
                        <?php
                        echo __('Ticket Limit Exceed');

                        ?>
                    </th>
                    <th width="5%" class="actions text-center"><?php echo __('Action'); ?></th>
                </tr>
            </thead>		
            <tbody>
                <?php if (empty($ticketConfigs)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($ticketConfigs as $ticketConfig): ?>
                        <?php foreach ($stations as $stationId => $stationDetail): ?>
                            <tr>
                                <td>
                                    <?php echo $startNo++; ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($stationDetail['Company']) ? $stationDetail['Company'] : '';

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($stationDetail['Branch']) ? $stationDetail['Branch'] : '';

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($stationDetail['Station']['name']) ? $stationDetail['Station']['name'] : '';

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $ticketConfig['MachineType']['name'];

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $ticketConfig['ErrorType']['error_level'];

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $ticketConfig['MachineError']['error_message'];

                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($dealerErrors[$ticketConfig['MachineError']['id']])?$dealerErrors[$ticketConfig['MachineError']['id']]['exceed_limit']:DEFAULT_TKT_LIMIT;

                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    echo $this->Html->link('', array('controller' => 'ticket_configs', 'action' => 'edit', encrypt($ticketConfig['MachineError']['id']), encrypt($stationId)), array('icon' => 'edit'));

                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("ErrorSearchForm", ["MachineErrorMachineTypeId", "MachineErrorErrorTypeId"]);
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