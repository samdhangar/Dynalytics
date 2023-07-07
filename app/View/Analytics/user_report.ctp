<?php
$this->assign('pagetitle', __('DynaCore User Report'));
$count_amount_flag = 0;
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('DynaCore User Report'));
} else {
    $this->Custom->addCrumb(__('Analytics'));
}
$this->start('top_links');
/*echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.TellerActivityReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));*/
$this->end();

echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');
?>

<script>
    $(function() {
        $("#date").datepicker("setDate", -1);
    });
</script>
<div class="col-md-12 col-sm-12 form-group row">
    <div class="box box-primary">
        <div class="box-body">
            <?php
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'userReport'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :

            //     echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            // if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
            //     echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;


            // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            // echo $this->Form->input('station', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('tellerName', array('onchange' => 'formSubmit1()','type' => 'select', 'id' => 'tellerName', 'label' => __('Teller Name'), 'empty' => __('Select All'), 'options'=>$tellerNames_Arr, 'default' => 'all','class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            ?>

                <?php echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));  ?>


            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary', 'id' => 'submit'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'userReport', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
            echo $this->Form->end();

            ?>
        </div>
    </div>

</div>

<!-- <div class="panel panel-flat" style="float:left;width:100%;">
    <div class="header-content">

        <div class="page-title"><?php
                                echo getReportFilter($this->Session->read('Report.GlobalFilter'));

                                ?></div>
    </div>

    <?php
    // echo $this->element('user/line_chart_container');
    ?>
    <div id="chart_div"></div>
    <div class="panel-body" id="chart_div">

        <div class="chart" id="google-column"></div>

    </div>




</div> -->

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">

        <h5 class="panel-title">DynaCore User Report</h5>
    </div>





    <div class="table-responsive htmlDataTable">
        <?php echo $this->element('reports/user_report', array('activity' => $activity, 'companyDetail' => $companyDetail)); ?>
    </div>


</div>

<?php

echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');



if (isCompany()) {
    $sessionData = getMySessionData(); ?>
    <script>
        $(document).ready(function() {
            var now = new Date();

            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);

            // var today = now.getFullYear() + "/" + (month) + "/" + (day - 1) + "-" + now.getFullYear() + "/" + (month) + "/" + (day);

            var startDate = $("input[name=daterangepicker_start]").val();
            var endDate = $("input[name=daterangepicker_end]").val();
            var date_set = startDate + "-" + endDate;
            $('#daterange').val(date_set);
            $('#daterange').attr('disabled', true);
            $('#daterange').daterangepicker({
                opens: 'center'
            }, function(start, end, label) {
                $('#analyticForm').submit();
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
<?php
}

?>


<script type="text/javascript">
    // 'use strict'; 



    // $(function() {


    //     $(window).on('resize', resize);
    //     $(".sidebar-control").on('click', resize);

    //     function resize() {
    //         drawBar();
    //     }
    // });

    // jQuery(document).ready(function() {
    //     // var data = '<?php echo $temp; ?>';
    //     // var xAxisDates = '<?php echo $xAxisDates; ?>';
    //     // var tickInterval = '<?php echo $tickInterval; ?>';
    //     // var options = {
    //     //     name: '<?php echo $lineChartName; ?>',
    //     //     title: '<?php echo $lineChartTitle; ?>',
    //     //     xTitle: '<?php echo $lineChartxAxisTitle; ?>',
    //     //     yTitle: '<?php echo $lineChartyAxisTitle; ?>',
    //     //     id: '#container'
    //     // };
    //     // //googleGraphGroup(data, xAxisDates, tickInterval, options);   
    //     // //drawBar2(data, xAxisDates, tickInterval, options);        

    //     // var startDate = moment('<?php echo $this->Session->read('Report.TellerActivityReport.start_date') ?>', 'YYYY-MM-DD');
    //     // var endDate = moment('<?php echo $this->Session->read('Report.TellerActivityReport.end_date') ?>', 'YYYY-MM-DD');

    //     // //addDateRange(startDate, endDate, "analytics/teller_activity", 'googleGraphGroup');        
    //     // google.load("visualization", "1", {packages:["corechart"]});
    //     // //google.setOnLoadCallback(drawBar);

    //     // lineChart(data, xAxisDates, tickInterval, options);
    //     // addDateRange(startDate, endDate, "analytics/teller_activity", 'lineChart');

    //     google.charts.load('current', {
    //         packages: ['corechart', 'bar']
    //     });

    //     // google.charts.setOnLoadCallback(drawChart);
    //     google.charts.setOnLoadCallback(drawChart_count);
    //     $(':checkbox').change(function (event) {
    //         var checkSlider = event.currentTarget.checked;
    //         if(checkSlider == false){
    //             google.charts.setOnLoadCallback(drawChart_count);
    //         }
    //         if(checkSlider == true){
    //         google.charts.setOnLoadCallback(drawChart);
    //         }
    //     });

    //         function drawChart() {
    //                 var data = google.visualization.arrayToDataTable(
    //                     <?php echo $chartData_Arr; ?>
    //                 );

    //                 var options = {
    //                 chart: {
    //                     title: 'Teller Activity'

    //                 },
    //                 bars: 'vertical',
    //                 vAxis: {format: 'decimal'},
    //                 height: 400,
    //                 colors: ['#FF0000', '#0000FF']
    //                 };

    //             var chart = new google.visualization.ColumnChart(
    //                         document.getElementById('chart_div'));
    //                     chart.draw(data, options);    
    //         }
    //         function drawChart_count() {

    //             var data = google.visualization.arrayToDataTable(
    //                 <?php echo $chartData_Arr2; ?>
    //             );

    //         var options = {
    //                 chart: {
    //                     title: 'Teller Activity'
    //                 },
    //                 bars: 'vertical',
    //                 vAxis: {format: 'decimal'},
    //                 height: 400,
    //                 colors: ['#FF0000', '#0000FF']
    //         };
    //     var chart = new google.visualization.ColumnChart(
    //             document.getElementById('chart_div'));
    //         chart.draw(data, options);    
    //     }

    // });

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
    //         $("#daterange").val('');
    //         $('#analyticForm').submit();
    //     }
    // }

    // function getResion(compId) {

    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_region/" + compId,
    //         type: 'post',
    //         success: function(response) {

    //             jQuery('#analyRegionId').html(response);
    //             jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //             jQuery('#analyStationId').html('<option value="">Select All</option>');
    //         },
    //         error: function(e) {

    //         }
    //     });
    //     $("#daterange").val('');
    //     $('#analyticForm').submit();
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
    //         $("#daterange").val('');
    //         $('#analyticForm').submit();
    //     }
    // }
    function formSubmit1(){
        $("#daterange").val('');
        $('#analyticForm').submit();
    }
</script>