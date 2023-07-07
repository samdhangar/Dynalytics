<?php
$this->assign('pagetitle', __('Database Growth Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.databaseGrowthReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'errors'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));
$this->end();

?>
<div class="panel panel-flat" style="float:left;width:100%;">
  <div class="panel-heading">
    <?php
    echo getReportFilter($this->Session->read('Report.databaseGrowthReport'));

    ?>
  </div>
  <?php
  echo $this->element('user/line_chart_container');
  ?>
</div>


<div class="panel panel-flat" style="float:left;width:100%;">
  <div id="containerPie"></div>
</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Database Growth Data</h5>
    </div>





    <div class="table-responsive">
      <?php echo $this->element('reports/database_growth', array('dbGrowth' => $DatabaseGrowths));?>

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
        var pieData = '<?php echo $dbGrowthPieData; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var options = {
            name: '<?php echo $lineChartName; ?>',
            title: '<?php echo $lineChartTitle; ?>',
            xTitle: '<?php echo $lineChartxAxisTitle; ?>',
            yTitle: '<?php echo $lineChartyAxisTitle; ?>',
            id: '#container'
        };
        lineChart(data, xAxisDates, tickInterval, options);
        pieChart(pieData, pieTitle, pieName);

        var startDate = moment('<?php echo $this->Session->read('Report.databaseGrowthReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.databaseGrowthReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/database_growth", 'lineChart');

    });
</script>
