<?php
$this->assign('pagetitle', __('Client Issue Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.ClientIssueReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'client_issue'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
?>



<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
        echo getReportFilter($this->Session->read('Report.ClientIssueReport'));
        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>


    <?php if (!isCompany()): ?>


        <div class="col-md-12 col-sm-12">
            <div class="box box-primary">
                <div class="box-body row">
                    <?php
                    echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'client_issue'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'col-md-4 form-group'))));
                    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company'), 'selected' => $selectedCompanies));
                    ?>
                    <div class="col-md-6">
                        <label class="col-md-12">&nbsp;</label>
                        <?php
                        echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                        echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'client_issue', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                        ?>
                    </div>
                    <?php
                    echo $this->Form->end();
                    ?>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>



<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Issue Report Data</h5>
    </div>

    <?php echo $this->element('reports/client_issue', array('tickets' => $tickets, 'companyDetail' => $companyDetail)); ?>
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
            name: 'Client Issue Report',
            title: 'Client Issue Report',
            xTitle: 'Client Issue Report Date',
            yTitle: 'No. of Issue Report By Client',
            id: '#container'
        };
        multiLineChart(data, xAxisDates, tickInterval, options);
        var startDate = moment('<?php echo $this->Session->read('Report.ClientIssueReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.ClientIssueReport.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/client_issue", 'multiLineChart');

    });

</script>
