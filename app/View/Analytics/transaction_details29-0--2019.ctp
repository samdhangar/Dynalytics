<?php
$this->assign('pagetitle', __('Transaction Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');

echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Errors'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'errors'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'errors', 'all'), array('title' => __('Reset Filter'),  'class' => 'btn btn-default btn-sm pull-right marginleft', 'escape' => false));
if(!isCompany()):

    echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));

    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => false, 'empty' => __('Select Company')));

    echo $this->Form->end();
endif;

$this->end();
?>
 <!--Page Container-->




 <div class="panel panel-flat" style="float:left;width:100%;">
   <div class="panel-heading">

   </div>
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Basic bar chart</h5>
               <?php//echo $transactionPie; ?>
               <?php //echo $transactionClientPie ?>

           </div>
           <div class="panel-body">
               <div class="chart" id="c3-bar-chart"></div>
           </div>
       </div>
   </div>
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Stacked bar chart</h5>
           </div>
           <div class="panel-body">
               <div class="chart" id="c3-bar-stacked-chart"></div>

                <!-- <pre id="whereToPrint2"></pre>
                  <pre id="whereToPrint"></pre>
                  <pre id="whereToPrint3"></pre>
                  <pre id="whereToPrint4"></pre> -->
           </div>
       </div>
   </div>
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Basic pie chart</h5>
           </div>
           <div class="panel-body text-center">
               <div class="display-inline-block" id="c3-pie-chart"></div>
           </div>
       </div>
   </div>
   <div class="col-md-6 col-sm-6 col-xs-12">
       <div class="panel panel-flat">
           <div class="panel-heading">
               <h5 class="panel-title">Basic pie chart</h5>
           </div>
           <div class="panel-body text-center">
               <div class="display-inline-block" id="c3-pie-chart2"></div>
           </div>
       </div>
   </div>

 </div>





 <div class="panel panel-flat"  style="float:left;width:100%;">
     <div class="panel-heading">
         <h5 class="panel-title">Transaction Data</h5>
     </div>





     <div class="table-responsive">
       <?php echo $this->element('user/transaction_details', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

     </div>


 </div>
</div>

 <?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
//echo $this->Html->script('/app/webroot/js/charts/c3/c3_bars_pies');



?>
<script type="text/javascript">
$(function () {
'use strict';

// Pie chart
// ------------------------------
var data = '<?php echo $temp1; ?>';
var cars = [];
var cars2 = [];
var seriesOptions = [];
data = JSON.parse(data);
$.each(data, function (key, value) {
seriesOptions[key] = {
 name: value.name,
 data: JSON.parse(value.data)
}
cars.push(value.name);
$.each(JSON.parse(value.data), function (key, value) {
 cars.push(value[1]);
});
cars2.push(cars);
cars = [];
});

var cars3 = ['data1', -3000, 2000, -1100, -8800, 1500, -2500];
cars2.push(cars3);


var transaction_vs_transaction_type = [];
var transaction_vs_transaction_type2 = [];
var pieData = '<?php echo $transactionPie; ?>';
pieData = JSON.parse(pieData);
$.each(pieData, function (key, value) {
transaction_vs_transaction_type.push(value.name);
transaction_vs_transaction_type.push(value.totalcount);
transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
transaction_vs_transaction_type = [];
});

var transaction_vs_branch = [];
var transaction_vs_branch2 = [];
var pieClientData = '<?php echo $transactionClientPie; ?>';
pieClientData = JSON.parse(pieClientData);
$.each(pieClientData, function (key, value) {
transaction_vs_branch.push(value.name);
transaction_vs_branch.push(value.totalcount);
transaction_vs_branch2.push(transaction_vs_branch);
transaction_vs_branch = [];
});


//document.getElementById("whereToPrint4").innerHTML = JSON.stringify(pieClientData, null, 4);
//pieChart(pieData, pieTitle, pieName, '#containerPie');

var data2 = '<?php echo $sentTemp; ?>';
var denom_data = [];
var denom_data2 = [];
var seriesOptions = [];
data = JSON.parse(data2);
$.each(data, function (key, value) {
seriesOptions[key] = {
 name: value.name,
 data: JSON.parse(value.data)
}
denom_data.push(value.name);
$.each(JSON.parse(value.data), function (key, value) {
 denom_data.push(value[1]);
});
denom_data2.push(denom_data);
denom_data = [];
});
var denom_data3 = ['data1', -300, 200, -110, -880, 150, -250];
denom_data2.push(denom_data3);
// Generate chart
var pie_chart = c3.generate({
bindto: '#c3-pie-chart',
size: { width: 350 },
color: {
 pattern: ['#3F51B5', '#FF9800', '#4CAF50', '#00BCD4', '#F44336']
},
data: {
 columns:
 transaction_vs_transaction_type2
 ,
 type : 'pie'
}
});
// End Generate chart

// Generate chart2
var pie_chart = c3.generate({
bindto: '#c3-pie-chart2',
size: { width: 350 },
color: {
 pattern: ['#3F51B5', '#FF9800', '#4CAF50', '#00BCD4', '#F44336']
},
data: {
 columns:
 transaction_vs_branch2
 ,
 type : 'pie'
}
});
// End Generate chart2


var bar_chart = c3.generate({
bindto: '#c3-bar-chart',
size: { height: 300 },
data: {
 columns:
     cars2
 ,
 type: 'bar'
},
color: {
 pattern: ['#2196F3', '#FF9800', '#4CAF50']
},
bar: {
 width: {
     ratio: 0.5
 }
},
grid: {
 y: {
     show: true
 }
}
});
// end chart

// Generate chart
var bar_stacked_chart = c3.generate({
bindto: '#c3-bar-stacked-chart',
size: { height: 300 },
color: {
 pattern: ['#FF9800', '#F44336', '#009688', '#4CAF50']
},
data: {
 columns:
 denom_data2
 ,
 type: 'bar',
 groups: [
     ['data1', 'data2']
 ]
},
grid: {
 x: {
     show: true
 },
 y: {
     lines: [{value:0}]
 }
}
});

/// Ene chart



});

</script>
