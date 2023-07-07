<?php
$this->assign('pagetitle', __('Log File Processing'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
//echo $this->Html->link(__('Export'), array('controller' => 'analytics', 'action' => 'index', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default btn-sm pull-right', 'escape' => false));

// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.FileProcessing'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'index'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

if (isSuparAdmin() || isAdminAdmin() || isSupportAdmin()):
echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'errors', 'all'), array('title' => __('Reset Filter'),  'class' => 'btn btn-default btn-sm pull-right marginleft', 'escape' => false));
endif;

if(!isCompany()):

    echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));

    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => false, 'empty' => __('Select Company')));

    echo $this->Form->end();
endif;

$this->end();

?>

<div class="panel panel-flat" style="float:left;width:100%;">
  <div class="panel-heading">
    <?php
    echo getReportFilter($this->Session->read('Report.FileProcessing'));

    ?>
  </div>
  <?php
  echo $this->element('user/line_chart_container');
  ?>
</div>

<!-- <div class="panel panel-flat" style="float:left;width:100%;">
    <div id="containerPie"></div>
</div> -->


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Log Files</h5>
    </div>





    <div class="table-responsive">
      <?php echo $this->element('user/process_files', array('processFiles' => $processFiles, 'companyDetail' => $companyDetail)); ?>

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
        var pieData = '<?php echo $filesPieData; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        console.log(pieData);
        console.log(pieTitle);
        console.log(data);
        fileProcessChart(data, xAxisDates, tickInterval);
        pieChart(pieData, pieTitle, pieName);

        var startDate = moment('<?php echo $this->Session->read('Report.FileProcessing.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.FileProcessing.end_date') ?>', 'YYYY-MM-DD');
//        startDate = startDate.format('YYYY-MM-DD');
//        endDate = endDate.format('YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/index", 'fileProcessChart', 'pieChart');
        var html = jQuery('#containerPie svg').html();
        console.log(html);


    });
</script>
