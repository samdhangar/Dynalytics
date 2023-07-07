<?php
$this->assign('pagetitle', __('Inventory by teller'));
$this->Custom->addCrumb(__('Performance Management'));
//$this->Custom->addCrumb(__('Inventory By Teller'));
$this->start('top_links');
if ($displayflag) {
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'inventory_by_teller'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
}
//echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.inventoryByTellerReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
$this->end();


$searchPanelArray = array(
    'name' => 'TellerSearch',
    'options' => array(
        'id' => 'UserSearchForm',
        'url' => $this->Html->url(array('controller' => 'analytics', 'action' => 'inventory_by_teller'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-3'
            )
        )
    ),
    'searchDivClass' => 'col-md-6',
    'search' => array(
        'title' => 'Search ',
        'options' => array(
            'id' => 'SearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => 'analytics', 'action' => 'inventory_by_teller', 'all'), array('escape' => false, 'title' => __('Display data'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'Teller',
            'options' => array(
                'type' => 'select',
                'options' => $tellerId,
                'empty' => __('Select Teller')
            )
        ),
        array(
            'name' => 'Date',
            'options' => array(
                'type' => 'text',
                'class' => 'form-control date',
                'id' => 'InventoryDate',
                'placeholder' => __('Select Date')
            )
        )
    )
);
?>



<div class="panel panel-flat">


    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div class="dataTables_filter" style="width:100%;">
            <?php echo $this->CustomForm->setSearchPanel($searchPanelArray); ?>
        </div>
      </div>

    </div>

</div>

<?php if ($displayflag) { ?>
  <div class="panel panel-flat" style="float:left;width:100%;">
    <?php
    echo $this->element('user/line_chart_container');
    ?>
  </div>

    <div class="panel panel-flat"  style="float:left;width:100%;">
        <div class="panel-heading">
            <h5 class="panel-title">Inventory</h5>
        </div>

        <div class="table-responsive">
            <?php echo $this->element('reports/inventory_by_teller', array('activity' => $Inventorys, 'companyDetail' => $companyDetail)); ?>
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
        multiLineChart(data, xAxisDates, tickInterval, options);

        var startDate = moment('<?php echo $this->Session->read('Report.inventoryByTellerReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.inventoryByTellerReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/inventory_by_teller", 'multiLineChart');

    });
</script>
<?php }
?>
<script type="text/javascript">
jQuery(document).ready(function () {
    validateSearch("UserSearchForm", ["TellerSearchTeller", "InventoryDate"]);
    jQuery('#TellerSearchTeller').on('change', function () {

        if (jQuery('#InventoryDate').val() == '') {
            jQuery("#UserSearchForm button[type='submit']").addClass('disabled');
        }
    }).trigger('change');

    jQuery('#InventoryDate').focusout(function () {

        setTimeout(function () {
            if (jQuery('#InventoryDate').val() == '') {
                jQuery("#UserSearchForm button[type='submit']").addClass('disabled');
            }else{
                jQuery("#UserSearchForm button[type='submit']").removeClass('disabled');
            }
        }, 300);
    });

    jQuery('#InventoryDate').on('focusin', function () {
        jQuery("#UserSearchForm button[type='submit']").addClass('disabled');
    });
    jQuery('#InventoryDate').keyup(function () {
        if (jQuery('#InventoryDate').val() == '') {
            jQuery("#UserSearchForm button[type='submit']").addClass('disabled');
        }
    })


});
</script>
