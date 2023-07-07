<?php
$this->assign('pagetitle', __('Bill Adjustment Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
/*echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillAdjustmentReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));*/
echo $this->Form->button('<span>' . __(getReportFilter($this->Session->read('Report.BillAdjustmentReport'))) . '</span>', array('data-toggle' => 'dropdown', 'title' => __('Dropdown'), 'icon' => 'icon-calendar',  'id' => 'dropdownMenu9',   'type' => 'button',  'class' => 'btn btn-success dropdown-toggle', 'aria-haspopup' =>true, 'aria-expanded' =>false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_adjustment'), array('title' => __('Export CSV'), 'icon' => 'fa-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));

$this->end();
 //echo $this->Html->script('user/moment');
 //echo $this->Html->script('user/chart');

?>
 <!--Page Container-->
    <section class="main-container">    
            <!--Page Header-->

              
           <div class="header">
                <div class="header-content">
                    <div class="page-title" style="float: left;">
                        <i class="icon-select2 position-left"></i><?php echo $this->fetch('pagetitle'); ?>
                    </div>
                   <!--  <div class="page-title" style="float: right;">
                         <?php echo $this->fetch('top_links'); ?> 
                    </div> -->


<div class="dropdown clearfix" style="float: right;"> 
     <?php echo $this->fetch('top_links'); ?> 
                                            
                                            <ul class="dropdown-menu bg-success"> 
                                               <li ><a href="#">Last 7 Days</a></li>
                                            <li><a href="#">Last 15 Days</a></li>
                                            <li ><a href="#">Last Month</a></li>
                                            <li class="active"><a href="#">Last 3 Month</a></li>
                                            <li ><a href="#">Last 6 Month</a></li>
                                             </ul> 
                                        </div>

   
                  <div class="clearfix"></div>
                  <div class="page-title" style="float: left;">
                <?php echo $this->Custom->getCrumbs('', array('text' => '<i class="fa fa-home"></i> Home', 'url' => array('controller' => 'users', 'action' => 'dashboard'))); ?>
                  </div>                     
                </div>
            </div>   
            <!--/Page Header-->
              <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
       echo getReportFilter($this->Session->read('Report.BillAdjustmentReport'));
        ?>
    </label>
    <?php 
    echo $this->element('user/line_chart_container');
    ?>
            <div class="container-fluid page-content">
                
                <div class="row">
                      
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h5 class="panel-title">Column chart</h5>                           
                            </div>
                            <div class="panel-body">
                                <div class="chart" id="google-column"></div>
                            </div>
                        </div>
                    </div>
                      <div class="col-md-12 col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'bill_adjustment'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                if(!isCompany()):
                    echo $this->Form->input('company_id', array('onchange'=>'getBranches(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
                endif;
                echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branches: '), 'empty' => __('Select Branches')));
                echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('Station: '), 'empty' => __('Select Station')));
                echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'bill_adjustment', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default margin-left10', 'escape' => false));
                echo $this->Form->end();

                ?>
            </div>
        </div>

    </div>

     <div class="col-md-12 col-sm-12 ">
        <label class="h3"> <strong>Bill Adjustment Data</strong></label>
    </div>
    <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
        <?php echo $this->element('reports/bill_adjustment', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
    </div>
    <?php
echo $this->Html->script('user/chart');

?>
               
                    
                   
               
                </div>
            </div>
            <?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('https://www.google.com/jsapi');
//echo $this->Html->script('/app/webroot/js/charts/google/bars/column');
 $arr_date=array("15398460000","15398460000","15398460000","15398460000","15398460000","15398460000","15398460000");
?>
    <script type="text/javascript">
'use strict';
// Column chart
// ------------------------------

// Initialize chart
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawColumn);
 

// Chart settings
function drawColumn() {

    
  var data = '<?php echo $temp; ?>';
    
  var bill_adjustment = [];
  var cars2=[];
  var cars3=[];
  var arr_date=["13 Jan","14 Jan","15 Jan","16 Jan","17 Jan","18 Jan","19 Jan"];
  var cars=['Year','No of Bill Adjustment'];
  data = JSON.parse(data);
   cars2.push(cars);
        cars = [];
       // '<?php $i=0; ?>';
       var i=0;
    $.each(data, function (key, value) {
        var date=value[0]/100;
        
        /*var date2='<?php echo date("M-d", $arr_date[$i]); ?>';
          '<?php $i++; ?>';*/
          var date2=arr_date[i];
          i++;
        cars.push(date2);
        cars.push(value[1]);
         cars2.push(cars);
        cars = [];
    });

 
  var largest = 0;
    var smallest=0; 
      for(var i = 0; i < cars2.length; i++){ 
          if(cars2[i][1] > largest){
               largest = cars2[i][1];
            }
            if(cars2[i][1] < smallest){
              smallest = cars2[i][1];
            }
    }
   
(largest>0)?largest++ : largest=-(smallest/5); 
(smallest<0)?smallest-- : smallest=-(largest/5); 
    var data = google.visualization.arrayToDataTable(cars2);
     // Options
    var options_column = {

        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        hAxis: {
            viewWindow: {
                min: 0,
                max: 10
            }
            
        },
        vAxis: {
            title: 'Sales and Expenses',
            titleTextStyle: {
                fontSize: 11,
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
              max:largest,
              min:smallest
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        }
    };

    // Draw chart
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    column.draw(data, options_column);
}


// Resize chart
// ------------------------------

$(function () {

    // Resize chart on sidebar width change and window resize
    $(window).on('resize', resize);
    $(".sidebar-control").on('click', resize);

    // Resize function
    function resize() {
        drawColumn();
    }
});


      /*  $(function () {
    'use strict';
    
  var data = '<?php echo $temp; ?>';
    
  var bill_adjustment = [];
  var cars2=[];
  var cars3=[];
  var cars=['Bill Adjestment'];
  data = JSON.parse(data);
    $.each(data, function (key, value) {
        cars.push(value[1]);
    });
 cars2.push(cars);
        cars = [];
 
var cars3 = ['data1', 10];
     cars2.push(cars3);

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
  
 
  
});
*/
    </script>


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

        lineChart(data, xAxisDates, tickInterval, options);

        var  startDate = moment('<?php echo $this->Session->read('Report.BillAdjustmentReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillAdjustmentReport.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/bill_adjustment", 'lineChart');

    });
    
    function getBranches(compId)
    {
       
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/"+ compId,
            type:'post',
            success:function(response){
                
                jQuery('#analyBranchId').html(response);
            },
            error:function(e){
                
            }
        });
    }
    
    function getStations(branchId)
    {
        console.log(jQuery('#analyBranchId').val());
        
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/"+ branchId,
            type:'post',
            data: {data:jQuery('#analyBranchId').val()},
            success:function(response){
                 
                jQuery('#analyStationId').html(response);
            },
            error:function(e){
               
            }
        });
    }
</script>