<?php
//debug($tickets);exit;
$this->assign('pagetitle', __('Historical Errors/Warnings'));
$this->Custom->addCrumb(__('Historical Errors/Warnings'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Export CSV'), array('action' => 'download_history'), array('icon' => 'icon-download', 'title' => __('Export history'), 'class' => 'btn btn-primary', 'escape' => false));
$this->end();

/**
 * Filter panel
 */
$searchPanelArray = array(
    'name' => 'Ticket',
    'options' => array(
        'id' => 'UserSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-2'
            )
        )
    ),
    'searchDivClass' => 'col-md-10',
    'search' => array(
        'title' => 'Search ',
        'options' => array(
            'id' => 'HistorySearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the history'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'client_id',
            'options' => array(
                'type' => 'select',
                'options' => $companyList,
                'empty' => __('Select Client'),
                'onchange' => 'getBranches(this.value)'
            )
        ),
        array(
            'name' => 'branch_id',
            'options' => array(
                'type' => 'select',
                'options' => $branches,
                'empty' => __('Select Branch'),
                'onchange' => 'getStations(this.value)'
            )
        ),
        array(
            'name' => 'station_id',
            'options' => array(
                'type' => 'select',
                'options' => $stations,
                'empty' => __('Select Station')
            )
        ),
        array(
            'name' => 'ticket_date',
            'options' => array(
                'label' => __('Occurrence Date'),
                'type' => 'text',
                'class' => 'form-control date',
                'placeholder' => __('Select Occurrence date')
            )
        ),
        array(
            'name' => 'error_warning_status',
            'options' => array(
                'label' => __('Error / Warning'),
                'type' => 'select',
                'empty' => __('Select History Type'),
                'options' => array('error' => __('Error'), 'warning' => __('Warning')),
                'placeholder' => __('Select Type')
            )
        ),
        array(
            'name' => 'updated_by',
            'options' => array(
                'label' => __('Resolved By'),
                'type' => 'select',
                'empty' => __('Select Support Person'),
                'options' => $dealers,
                'placeholder' => __('Select Support Person')
            )
        ),
        array(
            'name' => 'updated',
            'options' => array(
                'label' => __('Resolved Date'),
                'type' => 'text',
                'class' => 'form-control date',
                'placeholder' => __('Select Resolved Date')
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
                  <?php $fieldCount = 12; ?>
                  <th width="2%">
                      <?php echo __('#') ?>
                  </th>
                  <th width="10%">
                      <?php
                      echo $this->Paginator->sort('Company.first_name', __('Client Name'));

                      ?>
                  </th>
                  <th width="10%">
                      <?php
                      echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                      ?>
                  </th>
                  <th width="11%">
                      <?php
                      echo $this->Paginator->sort('Ticket.ticket_date', __('Occurrence Date'));

                      ?>
                  </th>
                  <th width="10%">
                      <?php echo __('Station(s)') ?>
                  </th>
                  <th width="10%">
                      <?php
                      echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));

                      ?>
                  </th>
                  <th width="15%">
                      <?php
                      echo $this->Paginator->sort('Ticket.error', __('Errors/Warning'));

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
                      echo $this->Paginator->sort('Ticket.updated', __('Resolved Date'));

                      ?>
                  </th>
                  <td>
                      <?php echo __('Actions'); ?>
                  </td>
              </tr>
          </thead>
          <tbody>
              <?php
              if (empty($tickets)):

                  ?>
                  <tr>
                      <td colspan="<?php echo $fieldCount; ?>">
                          <?php echo __('No data available.'); ?>
                      </td>
                  </tr>
                  <?php
              endif;
              foreach ($tickets as $ticket):

                  ?>
                  <tr>
                      <td>
                          <?php echo $startNo++; ?>
                      </td>
                      <td class="table-text">
                          <?php echo isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : ''; ?>
                      </td>
                      <td class="table-text">
                          <?php echo isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : ''; ?>
                      </td>
                      <td>
                          <?php echo showdatetime($ticket['Ticket']['ticket_date']); ?>
                      </td>
                      <td class="table-text">
                          <?php
                          foreach ($ticket['Branch']['Station'] as $key => $station):
                              echo $station['name'] . ((count($ticket['Branch']['Station']) > 1) ? ',' : '');
                          endforeach;

                          ?>
                      </td>
                      <td>
                          <?php echo isset($ticket['FileProccessingDetail']['file_date']) ? showdate($ticket['FileProccessingDetail']['file_date']) : 'N/A' ?>
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
                          <?php echo showdatetime($ticket['Ticket']['acknowledge_date']); ?>
                      </td>
                      <td>
                          <?php echo isset($ticket['UpdatedBy']['first_name']) ? $ticket['UpdatedBy']['first_name'] : ''; ?>
                      </td>

                      <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']); ?>">
                          <?php echo cropDetail($ticket['Ticket']['note'], 30); ?>
                      </td>
                      <td>
                          <?php
                          echo ($ticket['Ticket']['status'] == 'Closed') ? showdatetime($ticket['Ticket']['updated']) : '';

                          ?>
                      </td>
                      <td>
                          <?php
                          if ($ticket['Ticket']['status'] != 'Closed') {
                              echo $this->Html->link(__('Closed'), array('controller' => 'tickets', 'action' => 'status_change', encrypt($ticket['Ticket']['id']), 'Closed'), array('title' => __('Click here to closed this ticket'), 'data-message' => __('Are you sure want to change the status to closed?'), 'class' => 'ticketStatus'));
                          }

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
