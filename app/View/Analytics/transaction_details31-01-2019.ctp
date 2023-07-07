<?php
$this->assign('pagetitle', __('Transaction Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'errors'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'errors', 'all'), array('title' => __('Reset Filter'),  'class' => 'btn btn-default btn-sm pull-right marginleft', 'escape' => false));
if(!isCompany()):

    echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));

    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => false, 'empty' => __('Select Company')));

    echo $this->Form->end();
endif;

$this->end();

 echo $this->Html->script('user/moment');
 echo $this->Html->script('user/chart'); 
  echo $this->Html->script('user/daterangepicker');
 
?>
 <!--Page Container-->

 <div class="panel-heading">
        <h5 class="panel-title graphTitle h4"><?php
         echo  getReportFilter($this->Session->read('Report.Transaction'));

        ?></h5>
    </div>

<div class="panel-body" id="chart-div">  
                                    <?php  //  echo $temp1; ?>
                                     <?php//   echo $sentTemp; ?>
                                     <div class="chart" id="google-column"></div> 
                                     
                            </div>

<div class="panel-body" id="chart-div2">  
                                    <?php  //  echo $temp1; ?>
                                     <?php//   echo $sentTemp; ?> 
                                     <div class="chart" id="google-column2"></div>
                                     
                            </div>
 <div class="panel panel-flat" id="data-print" style="float:left;width:100%;">
   <div class="panel-heading">
    <!--  <pre id="whereToPrint1"></pre>
                  <pre id="whereToPrint2"></pre>
 <pre id="whereToPrint3"></pre>
                  <pre id="whereToPrint4"></pre>
                   <pre id="whereToPrint5"></pre>
                  <pre id="whereToPrint6"></pre>
 <pre id="whereToPrint7"></pre>
                  <pre id="whereToPrint8"></pre> -->
   </div>
    
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Transaction VS Transaction Type</h5>
           </div>

           <div class="panel-body text-center">
               <div class="display-inline-block" id="c3-pie-chart"></div>
           </div>
       </div>
   </div>
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Transaction VS Branch</h5>
           </div>
           <div class="panel-body text-center">
               <div class="display-inline-block" id="c3-pie-chart2"></div>
           </div>
       </div>
   </div>
   <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="container"></div>
    </div> 
     <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="container2"></div>
    </div>    
    <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="containerPie"></div>

    </div>
    <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="containerClientPie"></div>

    </div>
 </div>





 <div class="panel panel-flat"  style="float:left;width:100%;">
     <div class="panel-heading">
         <h5 class="panel-title">Transaction Data</h5>
     </div>





     <div class="table-responsive  htmlDataTable">
       <?php  echo $this->element('user/transaction_details', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

     </div>
 

 </div>
</div>

 <?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
//echo $this->Html->script('/app/webroot/js/charts/c3/c3_bars_pies');
echo $this->Html->script('user/chart');

 
?>
<script type="text/javascript">
function pageload(argument) {
 
//  window.location.reload();
}
  google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawColumn);

$(function () {
'use strict';

// Pie chart
// ------------------------------
var data = '<?php echo $temp1; ?>';
var cars = [];
var cars2 = [];   

  

var data2 = '<?php echo $sentTemp; ?>';
var denom_data = [];
var denom_data2 = [];
var seriesOptions = [];
 
 
  

});

</script>

<script type="text/javascript">
    jQuery(document).ready(function () {
     
        //form submit 
       
        jQuery('#analyCompId').on('change', function () {
            if (jQuery(this).val() != '') {
                jQuery('#analyticForm').submit();
            }
        });

        var data = '<?php echo $temp; ?>';
        var pieData = '<?php echo $transactionPie; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieCatTitle = '<?php echo $pieCatTitle; ?>';
        var pieClientTitle = '<?php echo $pieClientTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var pieCatData = '<?php echo $transactionCatPie; ?>';
        var pieClientData = '<?php echo $transactionClientPie; ?>';
        var xAxisDates = '<?php echo $xAxisDates1; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        /*var options = {
            name: '<?php echo __('Transaction'); ?>',
            title: '<?php echo __('Transaction Details'); ?>',
            xTitle: '<?php echo __('Transaction Date'); ?>',
            yTitle: '<?php echo __('No. of Transactions'); ?>',
            id: '#container'
        };
        lineChart(data, xAxisDates, tickInterval, options);*/
        var data1 = '<?php echo $temp1; ?>';
            var options1 = {
                name: 'Transaction',
                title: 'Transaction',
                xTitle: 'Transaction Date',
                yTitle: 'Transaction',
                id: '#container'
            };
           // multiLineChart(data1, xAxisDates, tickInterval, options1);
           
        //transaction graph 
        //denom chart

        var data2 = '<?php echo $sentTemp; ?>';
        var options2 = {
            name: 'Transaction',
            title: 'Transaction Denom',
            xTitle: 'Transaction Time',
            yTitle: 'No. Of Denom',
            id: '#container2'
        };
        var xAxisDatesTime = '<?php echo $xAxisDatesTime; ?>';
         multiLineChart2(data2, xAxisDates, tickInterval, options2);
       // multiLineChart(data2, xAxisDatesTime, tickInterval, options2);
        //end chart
  multiLineChart2(data1, xAxisDates, tickInterval, options1); 

  pieChart2(pieData,'#c3-pie-chart');
   pieChart2(pieClientData,'#c3-pie-chart2'); 
//        transactionDetailChart(data, xAxisDates, tickInterval);
    /*    pieChart(pieData, pieTitle, pieName, '#containerPie');
        pieChart(pieCatData, pieCatTitle, pieName, '#containerCatPie');
        pieChart(pieClientData, pieClientTitle, pieName, '#containerClientPie');*/

        var startDate = moment('<?php echo $this->Session->read('Report.Transaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Transaction.end_date') ?>', 'YYYY-MM-DD');
        var extraParams = {
            pieCatTitle: pieCatTitle,
            pieClientTitle: pieClientTitle,
            charts: {
                0: {
                    name: 'categoryChart',
                    data: 'transactionCatPie',
                    id: '#containerCatPie',
                    title: pieCatTitle,
                    chartName: pieName
                },
                1: {
                    name: 'clientChart',
                    data: 'transactionClientPie',
                    id: '#containerClientPie',
                    title: pieClientTitle,
                    chartName: pieName
                },
                2: {
                   type: 'multiLine',
                    name: 'Transaction',
                    title: 'Transaction',
                    data: 'transactionDetails',
                    xTitle: 'Transaction Date',
                    yTitle: 'Transaction',
                    id: '#container'
                }
            }

        };
        addDateRange(startDate, endDate, "analytics/transaction_details", 'lineChart2', 'pieChart', extraParams);
 
    });
</script>
