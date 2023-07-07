<?php
$this->assign('pagetitle', __('Activity Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.ActivityReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'activity_report'), array('title' => __('Export CSV'), 'icon' => 'fa-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

?>
<div class="row ccccc">
    <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
        echo getReportFilter($this->Session->read('Report.ActivityReport'));
        ?>
    </label>
    <?php
    echo $this->element('user/line_chart_container');
    ?>

    <div class="col-md-12 col-sm-12 ">
        <label class="h3"> <strong>Activity Report Data</strong></label>
    </div>
    <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
        <?php echo $this->element('reports/activity_report', array('activity' => $activity, 'companyDetail' => $companyDetail));?>
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

        var startDate = moment('<?php echo $this->Session->read('Report.ActivityReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.ActivityReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/activity_report", 'lineChart');

    });
</script>
