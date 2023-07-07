<?php
$this->assign('pagetitle', __('User Performance Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.UserPerformanceReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
$this->end();

?>


<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
        echo getReportFilter($this->Session->read('Report.UserPerformanceReport'));
        ?></h5>
    </div>


    <div class="col-md-12 col-sm-12 form-group">
        <div class="box box-primary">
            <div class="box-body row">
                <?php
                echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'user_performance'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'col-md-4 form-group'))));
                echo $this->Form->input('dealer_id', array('label' => __('Support Person: '), 'empty' => __('Select Support Person')));
                ?>
                <div class="col-md-6">
                    <label class="col-md-12">&nbsp;</label>
                <?php
                echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'user_performance', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                ?>
                </div>
                <?php
                echo $this->Form->end();

                ?>
            </div>
        </div>
    </div>
    <?php
    echo $this->element('user/line_chart_container');
    ?>


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
            name: 'Support Person Performance Report',
            title: 'Support Person Performance Report',
            xTitle: 'Support Person',
            yTitle: 'No. of hours',
            id: '#container'
        };
        barChart(data, xAxisDates, tickInterval, options);
        var startDate = moment('<?php echo $this->Session->read('Report.UserPerformanceReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.UserPerformanceReport.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/user_performance", 'barChart');

    });

</script>
