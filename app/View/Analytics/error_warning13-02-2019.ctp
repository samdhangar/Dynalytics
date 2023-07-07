<?php
$this->assign('pagetitle', __('Errors/Warnings'));
$this->Custom->addCrumb(__('Errors/Warnings'));

$this->start('top_links');
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'error_warning'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();


/**
 * Filter panel
 */
$searchPanelArray = array(
    'name' => 'Ticket',
    'options' => array(
        'id' => 'UserSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'error_warning'), true),
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
    'searchDivClass' => 'col-md-3',
    'search' => array(
        'title' => 'Search ',
        'options' => array(
            'id' => 'HistorySearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'error_warning', 'all'), array('escape' => false, 'title' => __('Display the all the error/warning'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'client_name',
            'options' => array(
                'label' => __('Client Name'),
                'type' => 'text',
                'class' => 'form-control ',
                'placeholder' => __('Enter client name')
            )
        ),
        array(
            'name' => 'branch_name',
            'options' => array(
                'label' => __('Branch Name'),
                'type' => 'text',
                'class' => 'form-control ',
                'placeholder' => __('Enter branch name')
            )
        ),
        array(
            'name' => 'ticket_status',
            'options' => array(
                'label' => __('Ticket Status'),
                'type' => 'select',
                'empty' => __('Select Ticket Status'),
                'options' => array('New' => __('New'), 'Open' => __('Open'), 'Closed' => __('Closed')),
                'placeholder' => __('Select Ticket Status')
            )
        )
    )
);


?>




                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
                    </div>

                    <div class="dataTables_wrapper no-footer">
                      <div class="datatable-header">
                        <div class="dataTables_filter" style="width:100%;">
                            <?php echo $this->CustomForm->setSearchPanel($searchPanelArray); ?>
                        </div>
                      </div>

                    </div>

                    <div class="dataTables_wrapper no-footer">
                      <div class="datatable-header">
                        <div style="float:none;" class="dataTables_filter">

                    <?php echo $this->element('paginationtop'); ?>

                  </div>
                </div>

              </div>

                    <div class="table-responsive">
                        <table class="table table-hover user-list" id="datatable">
                            <thead>

                              <tr>
                                  <?php $fieldCount = 11; ?>
                                  <th width="2%">
                                      <?php echo __('Sr. No.') ?>
                                  </th>
                                  
                   <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                  <th width="10%">
                                      <?php
                                      echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                                      ?>
                                  </th>
                   <?php }?>
                                  <th width="11%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.ticket_date', __('Date'));

                                      ?>
                                  </th>
                                  <th width="10%">
                                      <?php echo __('Station(s)') ?>
                                  </th>
                                  <th width="15%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.error_warning_status', __('Errors/Warning'));

                                      ?>
                                  </th>
                                  <th width="10%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.status', __('Ticket Status'));

                                      ?>
                                  </th>
                                  <th width="10%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.acknowledge_date', __('Ack Date'));

                                      ?>
                                  </th>
                                  <th width="10%">
                                      <?php echo $this->Paginator->sort('User.updated_by', __('Resolved By')); ?>
                                  </th>
                                  <th width="15%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.note', __('Note'));

                                      ?>
                                  </th>
                                  <th width="10%">
                                      <?php
                                      echo $this->Paginator->sort('Ticket.updated', __('Updated On'));

                                      ?>
                                  </th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($tickets)):

                                    ?>
                                    <tr>
                                        <td colspan="<?php echo $fieldCount; ?>">
                                            <?php echo __('No Any Error/Warning'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                endif;
                                $startNo=1;
                                foreach ($tickets as $ticket):

                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $startNo++; ?>
                                        </td>
                                       
                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                        <td>
                                            <?php echo isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : ''; ?>
                                        </td>
                        <?php }?>
                                        <td>
                                            <?php echo showdatetime($ticket['Ticket']['ticket_date']); ?>
                                        </td>
                                        <td>
                                            <?php
                                            foreach ($ticket['Branch']['Station'] as $key => $station):
                                                echo $station['name'] . ((count($ticket['Branch']['Station']) > 1) ? ',' : '');
                                            endforeach;

                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($ticket['Ticket']['error_warning_status'] == 'error') :
                                                echo cropDetail($ticket['Ticket']['error'], 30);
                                            else:
                                                echo cropDetail($ticket['Ticket']['warning'], 30);
                                            endif;

                                            ?>
                                        </td>
                                        <td>
                                            <?php echo isset($ticket['Ticket']['status']) ?  $ticket['Ticket']['status'] : '';?>
                                        </td>
                                        <td align="center">
                                            <?php echo showdatetime($ticket['Ticket']['acknowledge_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo isset($ticket['Dealer']['first_name']) ? $ticket['Dealer']['first_name'] : ''; ?>
                                        </td>

                                        <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']); ?>">
                                            <?php echo cropDetail($ticket['Ticket']['note'], 30); ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo ($ticket['Ticket']['status'] == 'Closed') ? showdatetime($ticket['Ticket']['updated']) : '';

                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                      <div class="box-footer clearfix">

 

        <?php echo $this->element('pagination'); ?>
    </div>
    <?php echo $this->Form->end(); ?>
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
