<?php
$this->assign('pagetitle', __('Inventory Report'));
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
        ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Inventory Report'));
} else {
    $this->Custom->addCrumb(__('Analytics'));
}
$this->start('top_links');
//echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.inventoryReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));

$this->end();

?>
<div class="row">
    <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
        //echo getReportFilter($this->Session->read('Report.inventoryReport'));

        ?>
    </label>
    <div class="col-md-12 col-sm-12 ">
        <label class="h3"> 
            <strong>
                <?php echo __('Inventory By Hour'); ?>
            </strong>
        </label>
    </div>

    <div class="col-md-12 col-sm-12 form-group row">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'inventory_by_hours'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                if (!isCompany()):
                    echo $this->Form->input('company_id', array('onchange' => 'getBranches(this.value)', 'id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
                endif;
                echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'multiple' => true, 'label' => __('Branch Name: '), 'empty' => __('Select Branches')));
                echo $this->Form->input('station', array('type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select Station')));
                echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
                echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'inventory_by_hours', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default margin-left10', 'escape' => false));
                echo $this->Form->end();
                ?>
            </div>
        </div>

    </div>
    
    <?php
    
        echo $this->element('user/line_chart_container');

        ?>


        
        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
            <div id="container1"></div>
        </div> 

        <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
            <?php //echo $this->element('reports/inventory_management', array('activity' => $Inventorys, 'companyDetail' => $companyDetail)); ?>
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
                name: '<?php echo "Inventory By Hour 1-Day"; ?>',
                title: '<?php echo "Inventory By Hour 1-Day"; ?>',
                xTitle: '<?php echo ""; ?>',
                yTitle: '<?php echo ""; ?>',
                id: '#container'
            };
           
            multiLineChart(data, xAxisDates, tickInterval, options);

            var data1 = '<?php echo $temp1; ?>';
            var xAxisDates1 = '<?php echo $xAxisDates1; ?>';
            var tickInterval1 = '<?php echo $tickInterval; ?>';
            var options1 = {
                name: '<?php echo "Inventory By Hour Weekly"; ?>',
                title: '<?php echo "Inventory By Hour Weekly"; ?>',
                xTitle: '<?php echo ""; ?>',
                yTitle: '<?php echo ""; ?>',
                id: '#container1'
            };
           
            inventoryHoursMultiLineChart(data1, xAxisDates1, tickInterval1, options1);

            var startDate = moment('<?php echo $this->Session->read('Report.inventoryReport.start_date') ?>', 'YYYY-MM-DD');
            var endDate = moment('<?php echo $this->Session->read('Report.inventoryReport.end_date') ?>', 'YYYY-MM-DD');

            //addDateRange(startDate, endDate, "analytics/inventory_management/display", 'multiLineChart');

        });

    </script>

<script type="text/javascript">
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