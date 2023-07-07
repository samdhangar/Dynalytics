<?php
$this->assign('pagetitle', __('Log File Processing'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');

echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'file_processing', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default btn-sm pull-right marginleft', 'escape' => false));

echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.FileProcessingCompany'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export PDF'), array('controller' => 'analytics', 'action' => 'file_processing_pdf'), array('title' => __('Export PDF'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'file_processing'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));

echo $this->Form->create('Analytic', array('id' => 'analyticForm', 'class' => 'pull-left paddingRight10 headerForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
if (!isCompany()):
    echo $this->Form->input('company_id', array('id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select Company')));
endif;

echo $this->Form->end();

$this->end();

?>

    <div class="panel panel-flat" style="float:left;width:100%;">
      <div class="panel-heading">
          <h5 class="panel-title"><?php
          echo getReportFilter($this->Session->read('Report.FileProcessingCompany'));

          ?></h5>
      </div>
    <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="container"></div>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12 form-group">
        <div id="containerPie" style="display: none;"></div>
    </div>
  </div>

    <div class="panel panel-flat"  style="float:left;width:100%;">
        <div class="panel-heading">
            <h5 class="panel-title">Files</h5>
        </div>
        <div class="table-responsive">
            <?php echo $this->element('user/file_processing', array('processFiles' => $processFiles, 'companyDetail' => $companyDetail)); ?>
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
        fileProcessChart(data, xAxisDates, tickInterval);
        pieChart(pieData, pieTitle, pieName);

        var startDate = moment('<?php echo $this->Session->read('Report.FileProcessingCompany.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.FileProcessingCompany.end_date') ?>', 'YYYY-MM-DD');
//        startDate = startDate.format('YYYY-MM-DD');
//        endDate = endDate.format('YYYY-MM-DD');
        addDateRange(startDate, endDate, "analytics/file_processing", 'fileProcessChart', 'pieChart');
    });
</script>
