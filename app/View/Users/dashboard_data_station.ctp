<?php
$this->assign('pagetitle', __('Station / Machine Management'));
$startNo = (int) $this->Paginator->counter('{:start}')+1;
$this->Custom->addCrumb(__('Stations'));
    // if (isSuparCompany() || isSuparDealer()) {
    //     $this->start('top_links');

    //     echo $this->Html->link(__('Add Station'), array('action' => 'add'), array('icon' => 'cc icon-plus3', 'title' => __('Add Station'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
    //     $this->end();
    // }
    $this->start('top_links');
    if (isSuparCompany() || isSuparDealer()) {
        echo $this->Html->link(__('Add Station'), array('action' => 'add'), array('icon' => 'cc icon-plus3', 'title' => __('Add Station'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
    }
    if (isCompany() || isCompanyAdmin() || isSuparCompany()) {
        echo $this->Html->link(__('Export Stations'), array('action' => 'export'), array('title' => __('Export Stations'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));
    }
    $this->end();
//generate search panel
$searchPanelArray = array(
    'name' => 'Station',
    'options' => array(
        'id' => 'StationSearchForm',
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
        'title' => 'Search',
        'options' => array(
            'id' => 'StationSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the Stations'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'branch_name',
            'options' => array(
                'type' => 'text',
                'label' => __('Branch Name'),
                'placeholder' => __('Enter branch name')
            )
        ),
        array(
            'name' => 'name',
            'options' => array(
                'type' => 'text',
                'label' => __('DynaCore Station ID'),
                'placeholder' => __('Enter name')
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
            
               <div class="col-md-12">
                  
                </div>
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
        <?php $startNo = (int) $this->Paginator->counter('{:start}');?>
        <table class="table table-hover user-list" id="datatable">
          <thead>
              <tr>
                  <?php $columns = 6;?>
                  <th width="5%">
                      <?php
                      echo __('#');

                      ?>
                  </th>
                  <th width="20%"><?php echo $this->Paginator->sort('CompanyBranch.name', __('Branch Name')); ?></th>
                  <th width="23%"><?php echo $this->Paginator->sort('name', __('DynaCore Station ID')); ?></th>
                  <th width="23%"><?php echo $this->Paginator->sort('station_code', __('Station Code')); ?></th>
                  <th width="23%"><?php echo $this->Paginator->sort('serial_no', __('DynaCore Serial Number')); ?></th>
                  <th width="12%"><?php echo $this->Paginator->sort('Station.last_file_date', __('Last File Date')); ?></th>
                  <th width="12%"><?php echo $this->Paginator->sort('file_processed_count', __('Total Files processed')); ?></th>
                  <th width="12%"><?php echo $this->Paginator->sort('status', __('Status')); ?></th>
                  <?php if (isSuparCompany() || isSuparDealer()): $columns++; ?>
                      <th width="10%"><?php echo __('Actions'); ?></th>
                  <?php endif; ?>
              </tr>
          </thead>
          <tbody>
              <?php if (empty($stationsData)) { ?>
                  <tr>
                      <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No Station found.') ?></td>
                  </tr>
              <?php } else { ?>
                  <?php foreach ($stationsData as $Station): ?>
                      <tr>

                          <td>
                          <?php echo $startNo++; ?>
                          </td>
                          <td class="table-text">
                              <?php echo isset($temp_companydata[$Station['Station']['branch_id']]) ? $temp_companydata[$Station['Station']['branch_id']] : ''; ?>
                          </td>
                          <td>
                              <?php echo $Station['Station']['name']; ?>
                          </td>
						  <td>
                              <?php echo $Station['Station']['station_code']; ?>
                          </td>
                          <td>
                              <?php echo $Station['Station']['serial_no']; ?>
                          </td>
                          <td>
                              <?php echo isset($Station['Station']['last_file_date']) ? showdate($Station['Station']['last_file_date']) : ''; ?>
                          </td>
                          <td>
                              <?php echo $Station['Station']['file_processed_count']; ?>
                          </td>
                          <td>
                              <?php
                              if (isSuparCompany()):
                                  echo $this->Custom->getToggleButton($Station['Station']['status'], 'userStatusChange', array('data-uid' => encrypt($Station['Station']['id']), 'data-id' => 'userStatus_' . $Station['Station']['id']));
                                   
                              else:
                                  echo $this->Custom->showStatus($Station['Station']['status']);
                              endif;
                               
                              ?>
                          </td>
                          <?php if (isSuparCompany() || isSuparDealer()): ?>

                              <td class="actions text-center">
                                  <?php
                                  echo $this->Html->link(__(''), array('action' => 'edit', encrypt($Station['Station']['id'])), array('icon' => 'icon-pencil5 edit', 'class' => 'no-hover-text-decoration', 'title' => __('Edit Station')));
                                  echo $this->Html->link(__(''), array('action' => 'delete', encrypt($Station['Station']['id'])), array('icon' => 'icon-trash delete', 'class' => 'no-hover-text-decoration', 'title' => __('Delete Station')), __('Are you sure you want to delete station ?'));

                                  ?>
                              </td>
                          <?php endif; ?>
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

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        //validateSearch("StationSearchForm", ["StationBranchName", "StationName"]);
        jQuery('.userStatusChange').on('click', function () {
          
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm("<?php echo __('Are you sure ? want to change status as ') ?>" + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + "<?php echo $this->params['controller']; ?>/change_status/" + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'active' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        //alert(response.message);
                        $(almessage).html(response.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });

      
    });
    <?php
     if (isDealer()) {
    ?>
   

    getBranches($("#dealerCompany").val());
    <?php
     }
    ?>  
    
    
    // function getStations(branchId){
    //     console.log(jQuery('#CompanyBranch').val());
    //     loader('show');
    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_stations/"+ branchId,
    //         type:'post',
    //         data: {data:jQuery('#CompanyBranch').val()},
    //         success:function(response){
    //             console.log(response);
    //             loader('hide');
    //             jQuery('#StationId').html(response);
    //         },
    //         error:function(e){
    //             loader('hide');
    //         }
    //     });
    // }
</script>
 