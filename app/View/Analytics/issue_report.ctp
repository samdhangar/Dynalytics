<?php
$this->assign('pagetitle', __('Issue Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.IssueReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'issue_report'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

?>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
        echo getReportFilter($this->Session->read('Report.IssueReport'));

        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>
    <div class="col-md-12 col-sm-12 ">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'issue_report'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group col-md-3'))));
                if(!isCompany()):
                    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => false, 'empty' => __('Select Company'), 'div' => array('class' => 'form-group col-md-3 no-leftpadding')));
                endif;
                echo $this->Form->input('error_warning_status', array('label' => false, 'empty' => __('Select Error/Warning'), 'options' => array('error' => __('Error'), 'warning' => __('Warning'))));
                echo $this->Form->input('dealer_id', array('label' => false, 'empty' => __('Select Resolved Person'), 'div' => array('class' => 'form-group col-md-3 no-rightpadding')));

                echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary marginleft'));
                echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'issue_report', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                echo $this->Form->end();

                ?>
            </div>
        </div>

    </div>
</div>



<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Issue Report Data</h5>
    </div>


    <div class="table-responsive">
      <?php echo $this->element('reports/issue_report', array('tickets' => $tickets, 'companyDetail' => $companyDetail)); ?>
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
            name: 'Issue Report',
            title: 'Issue Report',
            xTitle: 'Issue Report Date',
            yTitle: 'No. of Issue Report',
            id: '#container'
        };
        multiLineChart(data, xAxisDates, tickInterval, options);
        var startDate = moment('<?php echo $this->Session->read('Report.IssueReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.IssueReport.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/issue_report", 'multiLineChart');

    });

</script>
