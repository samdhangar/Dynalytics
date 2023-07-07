<?php
$this->assign('pagetitle', __('Transaction Vault Buys Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.TransactionVaultBuyReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'transaction_vault_buys'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

?>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title"><?php
      echo getReportFilter($this->Session->read('Report.TransactionVaultBuyReport'));

        ?></h5>
    </div>

    <?php
    echo $this->element('user/line_chart_container');
    ?>
</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Transaction Vault Buys Data</h5>
    </div>





    <div class="table-responsive">
        <?php echo $this->element('reports/transaction_vault_buys', array('activity' => $TransactionVaultBuys, 'companyDetail' => $companyDetail,'type'=>'vault'));?>
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

        var startDate = moment('<?php echo $this->Session->read('Report.TransactionVaultBuyReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.TransactionVaultBuyReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/transaction_vault_buys", 'lineChart');

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
