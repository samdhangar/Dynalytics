<?php
$this->assign('pagetitle', __('Transaction Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');

echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'index', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default btn-sm pull-right', 'escape' => false));
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
echo $this->Form->end();

$this->end();

?>
<div class="row">
    <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
        echo getReportFilter($this->Session->read('Report.Transaction'));

        ?>
    </label>
    <div class="col-md-12 col-sm-12">
        <div id="container"></div>
    </div>
    <div class="col-md-12 col-sm-12">
        <div id="containerPie"></div>

    </div>
    <div class="col-md-12 col-sm-12 ">
        <label>Transaction Data</label>
    </div>
    <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
        <?php echo $this->element('user/transaction_details', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>
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
        var pieData = '<?php echo $transactionPie; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        transactionDetailChart(data, xAxisDates, tickInterval);
        pieChart(pieData, pieTitle, pieName);

        var startDate = moment('<?php echo $this->Session->read('Report.Transaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Transaction.end_date') ?>', 'YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/transaction_details", 'transactionDetailChart', 'pieChart');

    });
</script>