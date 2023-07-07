<?php
$this->assign('pagetitle', __('Error Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');

echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Errors'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'errors'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'errors', 'all'), array('title' => __('Reset Filter'),  'class' => 'btn btn-default btn-sm pull-right marginleft', 'escape' => false));
if(!isCompany()):

    echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));

    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => false, 'empty' => __('Select Financial Institution')));

    echo $this->Form->end();
endif;

$this->end();

?>

<!-- <div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
        echo getReportFilter($this->Session->read('Report.Errors'));

        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>
</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div id="containerPie"></div>
</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div id="containerClientPie"></div>
</div> -->


    <div class="table-responsive">
        
        <?php echo $this->element('user/error_details', array('errors' => $errors, 'companyDetail' => $companyDetail)); ?>
    </div>


</div>

</div>
<?php
echo $this->Html->script('user/chart');

?>


<script type="text/javascript">
    jQuery(document).ready(function () {
        //form submit
        jQuery('#analyCompId').on('change', function () {
            if (jQuery(this).val() != '') {
                jQuery('#analyticForm').submit();
            }
        });

        var data = '<?php echo $temp; ?>';
        var pieData = '<?php echo $errorPie; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieClientTitle = '<?php echo $pieClientTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var pieClientData = '<?php echo $errorClientPie; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        var options = {
            name: 'Errors',
            title: 'Errors',
            xTitle: 'Error Date',
            yTitle: 'No. of Errors',
            id: '#container'
        };
        lineChart(data, xAxisDates, tickInterval, options);
        pieChart(pieData, pieTitle, pieName, '#containerPie');
        pieChart(pieClientData, pieClientTitle, pieName, '#containerClientPie');

        var startDate = moment('<?php echo $this->Session->read('Report.Errors.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Errors.end_date') ?>', 'YYYY-MM-DD');
        var extraParams = {
            pieClientTitle: pieClientTitle,
            charts: {
                0: {
                    name: 'clientChart',
                    data: 'errorClientPie',
                    id: '#containerClientPie',
                    title: pieClientTitle,
                    chartName: pieName
                }
            }

        };
        addDateRange(startDate, endDate, "analytics/errors", 'lineChart', 'pieChart', extraParams);

    });
</script>
