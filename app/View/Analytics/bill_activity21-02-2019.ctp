<?php
$this->assign('pagetitle', __('Bill Inventory Report'));
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape'=>false,'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Bill Inventory Report'));
} else {
    $this->Custom->addCrumb(__('Analytics'));
}
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillsActivityReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_activity'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

?>


<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
        echo getReportFilter($this->Session->read('Report.BillsActivityReport'));

        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>


    <div class="col-md-12 col-sm-12 form-group row">
            <div class="box box-primary">
                <div class="box-body">
                    <?php
                    echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'bill_activity'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    if (!isCompany()):
                        echo $this->Form->input('company_id', array('onchange' => 'getBranches(this.value)', 'id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
                    endif;
                    echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branches: '), 'empty' => __('Select Branches')));
                    echo $this->Form->input('station', array('type' => 'select', 'id' => 'analyStationId', 'label' => __('Station: '), 'empty' => __('Select Station')));
                    echo $this->Form->input('bill_type_id', array('label' => __('Bill type: '), 'empty' => __('Select Bill Type')));
                    echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'bill_activity', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();

                    ?>
                </div>
            </div>

        </div>


</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Bill Inventory Data</h5>
    </div>





    <div class="table-responsive">
      <?php echo $this->element('reports/bill_activities', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
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

        var startDate = moment('<?php echo $this->Session->read('Report.BillsActivityReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillsActivityReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/bill_activity", 'lineChart');

    });

    function getBranches(compId)
    {
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/" + compId,
            type: 'post',
            success: function (response) {
                loader('hide');
                jQuery('#analyBranchId').html(response);
            },
            error: function (e) {
                loader('hide');
            }
        });
    }

    function getStations(branchId)
    {
        console.log(jQuery('#analyBranchId').val());
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/" + branchId,
            type: 'post',
            data: {data: jQuery('#analyBranchId').val()},
            success: function (response) {
                loader('hide');
                jQuery('#analyStationId').html(response);
            },
            error: function (e) {
                loader('hide');
            }
        });
    }
</script>
