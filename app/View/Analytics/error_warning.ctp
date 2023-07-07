<?php
$this->assign('pagetitle', __('Errors/Warnings'));
$this->Custom->addCrumb(__('Errors/Warnings'));

$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.ErrorWarningReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'id' => 'data_range', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));


echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'error_warning'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

echo $this->Html->script('user/moment');
echo $this->Html->script('user/chart');
//   echo $this->Html->script('user/highcharts');
echo $this->Html->script('user/daterangepicker');
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


<div class="col-md-12 col-sm-12 form-group row">

    <div class="box box-primary">
        <div class="box-body">

            <?php
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'error_warning'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            // if (!isCompany()) :

            //     echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            // echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));



            // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            // echo $this->Form->input('station', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID : '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // echo $this->Form->input('Error Description', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyerror_msg', 'label' => __('Station: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('error_description', array('onchange' => 'formSubmit1()', 'type' => 'select', 'id' => 'tellerName', 'label' => __('Error Description: '), 'empty' => __('Select All'), 'options' => $error_messages_arr, 'default' => 'all', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'error_warning', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
            echo $this->Form->end();
            ?>
        </div>
    </div>

</div>
<!-- <div class="panel panel-flat" style="float:left;width:100%;"> -->
<!-- <div class="header-content"> -->
<!-- <div class="page-title"><?php

                                //  echo getReportFilter($this->Session->read('Report.ErrorWarningReport'));

                                ?></div> -->
<!-- <div class="elements">
    <a id="export"><i class="fa icon-download position-left"></i> Export</a>
  </div> -->
<!-- </div> -->



<!-- <div class="panel-body" id="chart-div"> 
                
    <div class="chart" id="google-column"></div> 
                                  
</div> -->

<!-- <script type="text/javascript">
  
  
</script>

</div> -->


<div class="panel panel-flat" style="float:left;width:100%;">
        <div id="container"></div>


<div class="col-lg-12 col-md-12 col-sm-12 form-group">
    <!-- <div class="panel panel-flat"> -->
    <div class="panel-heading">
        <h5 class="panel-title">List of all <?php echo $this->fetch('pagetitle'); ?></h5>
    </div>

    <!-- <div class="dataTables_wrapper no-footer">
        <div class="datatable-header">
            <div class="dataTables_filter" style="width:100%;">
                <?php echo $this->CustomForm->setSearchPanel($searchPanelArray); ?>
            </div>
        </div>

    </div> -->
    <div class="table-responsive htmlDataTable">
        <?php echo $this->element('reports/error_warning'); ?>
        <!-- </div> -->

        <!-- <div class="box-footer clearfix">

  
    </div> -->
        <?php echo $this->Form->end(); ?>
    </div>

</div>

<?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
echo $this->Html->script('user/chart');

?>
<script type="text/javascript">
    'use strict';
    // Column chart
    // ------------------------------

    // Initialize chart
    google.load("visualization", "1", {
        packages: ["corechart"]
    });
    google.setOnLoadCallback(drawColumn);
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';

        var errorChartData_arr = '<?php echo $errorChartData_arr; ?>';
        var options = {
            name: 'Errors',
            title: '<?php echo __('Error Details'); ?>',
            xTitle: '<?php echo __('Error Date'); ?>',
            yTitle: '<?php echo __('No. of Errors'); ?>',
            id: '#container',
        };
        lineChart(errorChartData_arr, xAxisDates, tickInterval, options);

        // var data = '<?php echo $result_graph; ?>';
        // var xAxisDates = 'Test';
        // var tickInterval = 'test2';
        // var options = {
        //     name: 'ddddddd',
        //     title: 'Error Warning',
        //     xTitle: 'aaaaaaaaaaaa',
        //     yTitle: 'sssssssss',
        //     id: '#container_data'
        // };

        // google_error_warning(data, xAxisDates, tickInterval, options);
        // var startDate = moment('<?php echo $this->Session->read('Report.ErrorWarningReport.start_date') ?>', 'YYYY-MM-DD');
        // var endDate = moment('<?php echo $this->Session->read('Report.ErrorWarningReport.end_date') ?>', 'YYYY-MM-DD');
        // addDateRange(startDate, endDate, "analytics/error_warning", 'google_error_warning');
    });

    // function getResion(compId) {
    //     loader('show');
    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_region/" + compId,
    //         type: 'post',
    //         success: function(response) {
    //             loader('hide');
    //             jQuery('#analyRegionId').html(response);
    //             jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //             jQuery('#analyStationId').html('<option value="">Select All</option>');
    //         },
    //         error: function(e) {
    //             loader('hide');
    //         }
    //     });
    //     $('#analyticForm').submit();
    // }

    // function getBranches(compId) {
    //     if (compId == '') {
    //         jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //         jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     } else {
    //         loader('show');
    //         jQuery.ajax({
    //             url: BaseUrl + "/company_branches/get_branches/" + compId,
    //             type: 'post',
    //             success: function(response) {
    //                 loader('hide');
    //                 jQuery('#analyBranchId').html(response);
    //                 jQuery('#analyStationId').html('<option value="">Select All</option>');
    //             },
    //             error: function(e) {
    //                 loader('hide');
    //             }
    //         });
    //         $('#analyticForm').submit();
    //     }
    // }

    // function getStations(branchId) {
    //     if (branchId == '') {
    //         jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     } else {
    //         loader('show');
    //         jQuery.ajax({
    //             url: BaseUrl + "/company_branches/get_stations/" + branchId,
    //             type: 'post',
    //             data: {
    //                 data: jQuery('#analyBranchId').val()
    //             },
    //             success: function(response) {
    //                 loader('hide');
    //                 jQuery('#analyStationId').html(response);
    //             },
    //             error: function(e) {
    //                 loader('hide');
    //             }
    //         });
    //         $('#analyticForm').submit();
    //     }
    // }


       function formSubmit1(){
        $('#analyticForm').submit();
    }
   </script>