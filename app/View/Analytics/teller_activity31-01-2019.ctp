<?php
$this->assign('pagetitle', __('Teller Activity Report'));
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape'=>false,'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Teller Activity Report'));
} else {
    $this->Custom->addCrumb(__('Analytics'));
}
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.TellerActivityReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'teller_activity'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
 echo $this->Html->script('user/moment');
 echo $this->Html->script('user/chart'); 
  echo $this->Html->script('user/daterangepicker');
?>


<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
         
        <h5 class="panel-title graphTitle h4"><?php
        echo getReportFilter($this->Session->read('Report.TellerActivityReport'));

        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>
<div class="panel-body" id="chart-div">  
                                    <?php//   echo $temp; ?>
                                     <div class="chart" id="google-column"></div>
                                     
                            </div>


    <div class="col-md-12 col-sm-12 form-group row">
            <div class="box box-primary">
                <div class="box-body">
                    <?php
                    echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'teller_activity'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    if(!isCompany()):
                        echo $this->Form->input('company_id', array('onchange'=>'getBranches(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
                    endif;
                    echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branches: '), 'empty' => __('Select Branches')));
                    echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('Station: '), 'empty' => __('Select Station')));
                    echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'teller_activity', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();

                    ?>
                </div>
            </div>

        </div>


</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Teller Activity Data</h5>
    </div>





    <div class="table-responsive htmlDataTable" >
        <?php echo $this->element('reports/teller_activity', array('activity' => $activity, 'companyDetail' => $companyDetail));?>
    </div>


</div>

<?php

echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
/*echo $this->Html->script('user/chart');*/
/*$temp_data= array([1422086400000,599,211],[1422000000000,943,259],[1421913600000,773,245],[1421827200000,750,252],[1421740800000,809,318]);
$temp=json_encode($temp_data, JSON_NUMERIC_CHECK);*/
?>


<script type="text/javascript">
    'use strict';
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawColumn);
 
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
           
        googleGraphGroup(data, xAxisDates, tickInterval, options);

        var startDate = moment('<?php echo $this->Session->read('Report.TellerActivityReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.TellerActivityReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/teller_activity", 'googleGraphGroup');

    });

    function getBranches(compId)
    {
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/"+ compId,
            type:'post',
            success:function(response){
                loader('hide');
                jQuery('#analyBranchId').html(response);
            },
            error:function(e){
                loader('hide');
            }
        });
    }

    function getStations(branchId)
    {
        console.log(jQuery('#analyBranchId').val());
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/"+ branchId,
            type:'post',
            data: {data:jQuery('#analyBranchId').val()},
            success:function(response){
                loader('hide');
                jQuery('#analyStationId').html(response);
            },
            error:function(e){
                loader('hide');
            }
        });
    }
</script>
