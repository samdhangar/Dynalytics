<?php
$this->assign('pagetitle', __('Bill Adjustment Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillAdjustmentReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_adjustment'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
 echo $this->Html->script('user/moment');
 echo $this->Html->script('user/chart');
  echo $this->Html->script('user/highcharts');
  echo $this->Html->script('user/daterangepicker');
?>


<div class="panel panel-flat" style="float:left;width:100%;">
   
    <div class="panel-heading">
 
        <h5 class="panel-title  graphTitle h4"><?php
         echo getReportFilter($this->Session->read('Report.BillAdjustmentReport'));

        ?></h5>
    </div>

    <?php
     echo $this->element('user/line_chart_container');
    ?>
 
   <div class="panel-body" id="chart-div">
                                <div class="chart" id="google-column"></div> 
                                  
                            </div>
    <div class="col-md-12 col-sm-12 form-group row">
       
            <div class="box box-primary">
                 <div class="box-body">
               
                    <?php
                    echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'bill_adjustment'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    if(!isCompany()):
                        
                         echo $this->Form->input('company_id', array('onchange'=>'getBranches(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All')));
                    endif;
                      echo $this->Form->input('regiones', array('onchange'=>'getBranches(this.value)','id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All')));
                   

                    echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branches: '), 'empty' => __('Select All')));


                    echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('Station: '), 'empty' => __('Select All')));
                    echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'bill_adjustment', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();
                    ?>
                </div>
            </div>

        </div>


</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Bill Adjustment Data</h5>
    </div>





    <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
        <?php  echo $this->element('reports/bill_adjustment', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
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
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawColumn);
 
 
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
 
        googleGraph(data, xAxisDates, tickInterval, options);
         var  startDate = moment('<?php echo $this->Session->read('Report.BillAdjustmentReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillAdjustmentReport.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/bill_adjustment", 'googleGraph');

    });
    
    function getResion(compId)
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