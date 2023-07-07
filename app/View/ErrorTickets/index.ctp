<?php
$this->assign('pagetitle', __('Error/Warnings of Tickets'));
$this->Custom->addCrumb(__('Error/Warning of ticket'));
//generate search panel
$searchPanelArray = array(
    'name' => 'ErrorTicket',
    'options' => array(
        'id' => 'ErrorTicketSearchForm',
        'url' => $this->Html->url(array('action' => 'index'), true),
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
        'title' => 'Search',
        'options' => array(
            'id' => 'StateSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the countries'), 'class' => 'btn btn-default')),
    'fields' => array(
        array(
            'name' => 'station',
            'options' => array(
                'type' => 'select',
                'label' => __('DynaCore Station ID'),
                'placeholder' => __('Select Station')
            )
        ),
        array(
            'name' => 'machine_error_id',
            'options' => array(
                'type' => 'select',
                'label' => __('Machine Error'),
                'placeholder' => __('Select Machine Error')
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
                    <?php $columns = 7; ?>
                    <th width="5%">
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <th width="10%">
                        <?php echo $this->Paginator->sort('Company.first_name', __('Company Name')); ?>
                    </th>
                    <th width="10%">
                        <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?>
                    </th>
                    <th width="7%">
                        <?php echo $this->Paginator->sort('ErrorTicket.station', __('DynaCore Station ID')); ?>
                    </th>
                    <th width="7%">
                        <?php echo __('Machine Type'); ?>
                    </th>
                    <th width="11%">
                        <?php echo $this->Paginator->sort('ErrorTicket.error', __('Error')); ?>
                    </th>
                    <th width="15%">
                        <?php echo $this->Paginator->sort('ErrorTicket.ticket_date', __('Date')); ?>
                    </th>
                </tr>			
            </thead>		
            <tbody>
                <?php if (empty($errorTickets)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns ?>' class='text-warning'>
                            <?php echo __('No error ticket found.') ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($errorTickets as $errorTicket): ?>
                        <tr>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <td align="center">
                                <?php echo isset($errorTicket['Company']['first_name']) ? $errorTicket['Company']['first_name'] : ''; ?>
                            </td>
                            <td align="center">
                                <?php echo isset($errorTicket['Branch']['name']) ? $errorTicket['Branch']['name'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($errorTicket['ErrorTicket']['station']) ? $errorTicket['ErrorTicket']['station'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($machineTypes[$errorTicket['MachineError']['machine_type_id']]) ? $machineTypes[$errorTicket['MachineError']['machine_type_id']] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($errorTicket['ErrorTicket']['error']) ? cropDetail($errorTicket['ErrorTicket']['error']) : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($errorTicket['ErrorTicket']['ticket_date']) ? showdate($errorTicket['ErrorTicket']['ticket_date']) : ''; ?>
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
<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("ErrorTicketSearchForm", ["ErrorTicketStation","ErrorTicketMachineErrorId"]);
    });
</script>