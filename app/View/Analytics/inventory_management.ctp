<?php
    $this->assign('pagetitle', __('Inventory Report'));
    if (!empty($activityReportId)) {
        $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
        $this->Custom->addCrumb(__('# %s', $activityReportId), array(
            'controller' => 'analytics',
            'action' => 'activity_report_view',
            encrypt($activityReportId)
            ), array('escape' => false, 'title' => __('Activity Report Id')));
        $this->Custom->addCrumb(__('Inventory Report'));
    } else {
        $this->Custom->addCrumb(__('Analytics'));
    }
    $this->start('top_links');
    // echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.inventoryReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
    if ($displayflag):
        echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'inventory_management'), array('title' => __('Export CSV'), 'id'=> 'export_btn_inventory','icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
    endif;
    $this->end();
    echo $this->Html->script('user/moment');
     echo $this->Html->script('user/chart');
      echo $this->Html->script('user/highcharts');
      echo $this->Html->script('user/daterangepicker');
    ?>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
            // echo getReportFilter($this->Session->read('Report.GlobalFilter'));
            
            ?></h5>
    </div>
    <?php if (!isCompany()): ?>
    <div class="col-md-12 col-sm-12">
        <div class="box box-primary">
            <div class="box-body row">
                <?php
                    echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'inventory_management'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'col-md-3 form-group'))));
                    
                    echo $this->Form->input('company_id', array('onchange' => 'getBranches(this.value)', 'id' => 'analyCompId', 'label' => false, 'empty' => __('Select Company')));
                    
                    echo $this->Form->input('branch_id', array('onchange' => 'getStation(this.value)','id' => 'analyBranchId', 'label' =>false, 'empty' => __('Select Branch')));
                    
                    echo $this->Form->input('station_id', array('id' => 'analyStationId', 'label' => false, 'empty' => __('Select Station')));
                    
                    echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'inventory_management', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    
                    ?>
                <?php
                    echo $this->Form->end();
                    
                    ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(isCompany()):?>
    <div class="col-md-12 col-sm-12 form-group row">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                    echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'inventory_management'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    
                    // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :
                    
                    //   echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    // endif;
                    
                    // if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
                    //   echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    
                    
                    // endif;
                    
                    
                    // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    
                    
                    // echo $this->Form->input('station', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    // echo "<label for='analyBranchId' >&nbsp;</label><br>";
                    // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                    // echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'inventory_management', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();
                    ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($displayflag): echo $this->element('user/line_chart_container'); ?>
</div>
<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php echo __('Inventory'); ?></h5>
    </div>
    <div class="panel-heading1" style="margin: 2px 7px; text-align:end">
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
        <label class="switch">
        <input type="checkbox">
        <span class="slider round"></span>
        </label>
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>
    </div>
    <div class="table-responsive">
        <?php echo $this->element('reports/inventory_management', array('activity' => $Inventorys, 'companyDetail' => $companyDetail)); ?>
    </div>
</div>
<?php echo $this->Html->script('user/chart'); ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
    
    
        var data = '<?php echo $temp; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        var options = {
            name: '<?php echo $lineChartName; ?>',
            title: '<?php echo $lineChartTitle; ?>',
            xTitle: '<?php echo $lineChartxAxisTitle; ?>',
            yTitle: '<?php echo $lineChartyAxisTitle; ?>',
            id: '#container'
        };
        multiLineChart(data, xAxisDates, tickInterval, options);
    
        var startDate = moment('<?php  echo $this->Session->read('Report.inventoryReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php  echo $this->Session->read('Report.inventoryReport.end_date') ?>', 'YYYY-MM-DD');
        // var startDate = moment('<?php echo $this->Session->read('Report.GlobalFilter.start_date') ?>', 'YYYY-MM-DD');
        // var endDate = moment('<?php echo $this->Session->read('Report.GlobalFilter.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/inventory_management/display", 'multiLineChart');
    
    });
    
</script>
<?php endif; ?>
<script type="text/javascript">
    // function getResion(compId) {
    
    // jQuery.ajax({
    //   url: BaseUrl + "/company_branches/get_region/" + compId,
    //   type: 'post',
    //   success: function(response) {
    
    //     jQuery('#analyRegionId').html(response);
    //     jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //     jQuery('#analyStationId').html('<option value="">Select All</option>');
    //   },
    //   error: function(e) {
    
    //   }
    // });
    // $("#daterange").val('');
    //     $('#analyticForm').submit();
    // }
    
    // function getBranches(compId) {
    
    // if (compId == '') {
    //   jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //   jQuery('#analyStationId').html('<option value="">Select All</option>');
    //   $('#analyticForm').submit();
    //   $('#analyBranchId').val('');
    //   $('#analyStationId').val('');
    // } else {
    //   jQuery('#analyBranchId').val('');
    //   jQuery('#analyStationId').val('');
    //   jQuery.ajax({
    //     url: BaseUrl + "/company_branches/get_branches/" + compId,
    //     type: 'post',
    //     success: function(response) {
    
    //       jQuery('#analyBranchId').html(response);
    //       jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     },
    //     error: function(e) {
    
    //     }
    //   });
    //   $("#daterange").val('');
    //     $('#analyticForm').submit();
    // }
    // }
    
    // function getStations(branchId) {
    // if (branchId == '') {
    //   jQuery('#analyStationId').html('<option value="">Select All</option>');
    //   $('#analyticForm').submit();
    // } else {
    //   jQuery.ajax({
    //     url: BaseUrl + "/company_branches/get_stations/" + branchId,
    //     type: 'post',
    //     data: {
    //       data: jQuery('#analyBranchId').val()
    //     },
    //     success: function(response) {
    
    //       jQuery('#analyStationId').html(response);
    //     },
    //     error: function(e) {
    
    //     }
    //   });
    //   $("#daterange").val('');
    //     $('#analyticForm').submit();
    // }
    // }
    // function formSubmit(){
    //     $("#daterange").val('');
    //     $('#analyticForm').submit();
    // }
        var getUrl = $('#export_btn_inventory').attr('href');
            $(document).ready(function () {
                $('#export_btn_inventory').attr('href',getUrl+"/count");
    
            });
    $(':checkbox').change(function (event) {
        var checkSlider = event.currentTarget.checked;
        if(checkSlider == false){
            $('#export_btn_inventory').attr('href',getUrl+"/count");
            $(".dis_count").show();
            $(".dis_amount").hide();
    
        }
        if(checkSlider == true){
            $('#export_btn_inventory').attr('href',getUrl+'/amount');
            $(".dis_count").hide();
            $(".dis_amount").show();
        }
    });
</script>
<style>
    .switch {
    position: relative;
    display: inline-block;
    width: 53px;
    height: 28px;
    padding:2px;
    }
    .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
    }
    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }
    .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }
    input:checked + .slider {
    background-color: #2196F3;
    }
    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }
    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }
    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }
    .slider.round:before {
    border-radius: 50%;
    }
</style>