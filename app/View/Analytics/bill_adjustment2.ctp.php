<?php
$this->assign('pagetitle', __('Bill Adjustment Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillAdjustmentReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
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
                    <div class="page-title" style="float: right;">
                         <?php echo $this->fetch('top_links'); ?> 
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
                                <h5 class="panel-title">Bill Adjustment</h5>
                                <?php echo $temp; ?>
                            </div>
                            <div class="panel-body">
                                <div class="chart" id="c3-bar-chart"></div>
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


?>
    <script type="text/javascript">
        $(function () {
    'use strict';
   
    // Pie chart
    // ------------------------------
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
  // end chart 

 
  
});

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