<?php
$this->assign('pagetitle', __('Cash Inventory Report'));
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape'=>false,'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Cash Inventory Report'));
} else {
    $this->Custom->addCrumb(__('Cash Inventory '));
}
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillsActivityReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_activity'), array('title' => __('Export CSV'), 'id' => 'exportBtn', 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
 echo $this->Html->script('user/moment');
   echo $this->Html->script('user/chart');
//   echo $this->Html->script('user/highcharts');
  echo $this->Html->script('user/daterangepicker');
?>
<div class="col-md-12 col-sm-12 form-group row">

            <div class="box box-primary">
                 <div class="box-body">

                    <?php
                    echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'bill_activity'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                  // if((!isCompany()) && ($sessionData['user_type']!='Region')):

                  //        echo $this->Form->input('company_id', array('onchange'=>'getResion(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                  //   endif;
                  //      if($sessionData['user_type']!='Region' AND $sessionData['user_type']!='Branch'):
                  //     echo $this->Form->input('regiones', array('onchange'=>'getBranches(this.value)','id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                  //      endif;


                  //   echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

                    // echo $this->Form->input('station', array('onchange'=>'formSubmit()','type'=>'select','id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    //  echo "<label for='analyBranchId' >&nbsp;</label><br>";
                    // echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                    // echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'bill_activity', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();
                    ?>
                </div>
            </div>

        </div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="header-content">
        <div class="page-title graphTitle"><?php
        echo getReportFilter($this->Session->read('Report.GlobalFilter'));

        ?></div>

        <div class="elements">
    <a id="export"><i class="fa icon-download position-left"></i> Export</a>
  </div>

    </div>
<div class="panel-body" id="chart-div" style="height: 300px!important; display: flex">


                                     <div class="chart" id="google-column">  </div>



                            </div>

<div class="panel-body" id="chart-div2" style="height: 300px!important; display: flex">
    <div class="elements"><a id="export2"><i class="fa icon-download position-left"></i> Export</a></div>
                                      <div class="chart" id="google-column2"></div>

                            </div>
 <div class="panel-body" id="chart-div3" style="height: 300px!important; display: flex">
    <div class="elements"><a id="export3"><i class="fa icon-download position-left"></i> Export</a></div>
                                      <div class="chart" id="google-column3"></div>

                            </div>

   <div class="panel-body" id="chart-div4" style="height: 300px!important; display: flex">
    <div class="elements"><a id="export4"><i class="fa icon-download position-left"></i> Export</a></div>
                                      <div class="chart" id="google-column4"> </div>


                            </div>
    <?php
    echo $this->element('user/line_chart_container');
    ?>





</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Cash Inventory Data</h5>
    </div>
    <div class="panel-heading1" style="margin: 0px 7px; text-align:end">
    <?php 
            
           // echo $this->Form->radio('type', $options, $attributes);
        //    echo $this->Form->checkBox('company_id', array('data-toggle' => 'switchbutton', 'checked' ,'data-onlabel' => 'Ready', 'data-offlabel'=>'Not Ready', 'data-onstyle'=>'success' ,'data-offstyle'=> 'danger'));
    ?>
    <!-- <input type="checkbox" data-toggle="switchbutton" checked data-onlabel="Ready" data-offlabel="Not Ready" data-onstyle="success" data-offstyle="danger"> -->
    <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
    <label class="switch">
        <input type="checkbox">
        <span class="slider round"></span>
    </label>
    <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>
    <div class="table-responsive htmlDataTable">
      <?php echo $this->element('reports/bill_activities', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
    </div>
</div>


<?php
echo $this->Html->script('user/chart');

?>
 <?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
echo $this->Html->script('user/chart');


?>
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
<script type="text/javascript">

    function pageload() {
        // body...
    }
  google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawColumn);

    jQuery(document).ready(function () {
        var graph_data_all='<?php echo $graph_data_all; ?>'
        // console.log("graphdata",graph_data_all);
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
      //  lineChart(data, xAxisDates, tickInterval, options);
 graph_data_all_new = JSON.parse(graph_data_all);
// document.getElementById("whereToPrint2").innerHTML = JSON.stringify(graph_data_all_new, null, 4);
 $.each(graph_data_all_new, function (key, value) {
options['title']=key;
graph_inventory(value,key);

 });


      //  graph_drow(graph_data_all);


        var startDate = moment('<?php echo $this->Session->read('Report.BillsActivityReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillsActivityReport.end_date') ?>', 'YYYY-MM-DD');

        // var startDate = moment('<?php //echo $this->Session->read('Report.GlobalFilter.start_date') ?>', 'YYYY-MM-DD');
        // var endDate = moment('<?php //echo $this->Session->read('Report.GlobalFilter.end_date') ?>', 'YYYY-MM-DD');
        // addDateRange(startDate, endDate, "analytics/bill_activity", 'lineChart_bill_inventory');
        // addDateRange(startDate, endDate, "analytics/bill_activity", 'lineChart_bill_inventory');

    });
//     function getResion(compId) {
//       jQuery.ajax({
//         url: BaseUrl + "/company_branches/get_region/" + compId,
//         type: 'post',
//         success: function(response) {

//           jQuery('#analyRegionId').html(response);
//           jQuery('#analyBranchId').html('<option value="">Select All</option>');
//           jQuery('#analyStationId').html('<option value="">Select All</option>');
//         },
//         error: function(e) {

//         }
//       });
//           $('#analyticForm').submit();
//   }
//     function getBranches(compId)
//     {
//         if(compId==''){
//           jQuery('#analyBranchId').val('');
//           jQuery('#analyStationId').val('');
// jQuery('#analyBranchId').html('<option value="">Select All</option>');
// jQuery('#analyStationId').html('<option value="">Select All</option>');
// $('#analyticForm').submit();
//         }else{

//           jQuery('#analyBranchId').val('');
//           jQuery('#analyStationId').val('');
//         loader('show');
//         jQuery.ajax({
//             url: BaseUrl + "/company_branches/get_branches/" + compId,
//             type: 'post',
//             success: function (response) {
//                 loader('hide');
//                 jQuery('#analyBranchId').html(response);
//                 jQuery('#analyStationId').html('<option value="">Select All</option>');
//             },
//             error: function (e) {
//                 loader('hide');
//             }
//         });
//         // $("#analyticForm").submit(function(e) {});
//         $('#analyticForm').submit();
//     }
//     }

//     function getStations(branchId)
//     {
//         if(branchId==''){
//           jQuery('#analyBranchId').val('');
//           jQuery('#analyStationId').val('');
//           jQuery('#analyStationId').html('<option value="">Select All</option>');
//           $('#analyticForm').submit();
//         }else{
//         console.log(jQuery('#analyBranchId').val());
//         loader('show');
//         jQuery.ajax({
//             url: BaseUrl + "/company_branches/get_stations/" + branchId,
//             type: 'post',
//             data: {data: jQuery('#analyBranchId').val()},
//             success: function (response) {
//                 loader('hide');
//                 jQuery('#analyStationId').html(response);
//             },
//             error: function (e) {
//                 loader('hide');
//             }
//         });
//         $('#analyticForm').submit();
//         }
//     }
    // function formSubmit(){
    //   $('#analyticForm').submit();
    // }

    var getUrl = $('#exportBtn').attr('href');
    $(document).ready(function () {
        $('#exportBtn').attr('href',getUrl+"/count");

      });
    $(':checkbox').change(function (event) {
    var checkSlider = event.currentTarget.checked;
    if(checkSlider == false){
      $('#exportBtn').attr('href',getUrl+"/count");
        $(".dis_count").show();
        $(".dis_amount").hide();

    }
    if(checkSlider == true){
      $('#exportBtn').attr('href',getUrl+'/amount');
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