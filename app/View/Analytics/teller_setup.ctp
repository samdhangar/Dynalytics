<?php
$this->assign('pagetitle', __('Teller Setup Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.TellerSetupReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'teller_setup'), array('title' => __('Export CSV'), 'icon' => 'fa-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

?>
<div class="row">
    <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
        echo getReportFilter($this->Session->read('Report.TellerSetupReport'));

        ?>
    </label>
    <?php 
    echo $this->element('user/line_chart_container');
    ?>
<!--    <div class="col-md-12 col-sm-12 form-group row">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'teller_setup'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                if(!isCompany()):
                    echo $this->Form->input('company_id', array('onchange'=>'getBranches(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
                endif;
                echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branch Name: '), 'empty' => __('Select Branches')));
                echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select DynaCore Station ID')));
                echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'teller_setup', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default margin-left10', 'escape' => false));
                echo $this->Form->end();

                ?>
            </div>
        </div>

    </div>-->
    <div class="col-md-12 col-sm-12 ">
        <label class="h3"> <strong>Teller Setup Data</strong></label>
    </div>
    <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
        <?php echo $this->element('reports/teller_setup', array('activity' => $TellerSetups, 'companyDetail' => $companyDetail,'type'=>'vault'));?>
    </div>
</div>
<?php
echo $this->Html->script('user/chart');

?>

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

        var startDate = moment('<?php echo $this->Session->read('Report.TellerSetupReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.TellerSetupReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/teller_setup", 'lineChart');

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