<?php
$this->assign('pagetitle', __('Denom Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');


$this->end();
?>

<?php if (isCompany()){ ?>

  <div class="panel panel-flat" style="float:left;width:100%;">
      <div class="panel-heading">
          <h5 class="panel-title"><?php echo $this->fetch('pagetitle'); ?></h5>
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

            var startDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.start_date') ?>', 'YYYY-MM-DD');
            var endDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.end_date') ?>', 'YYYY-MM-DD');


            var data2 = '<?php echo $sentTemp; ?>';
            var options2 = {
                name: 'Transaction',
                title: 'Transaction Denom',
                xTitle: 'Transaction Time',
                yTitle: 'No. Of Denom',
                id: '#container'
            };
            var xAxisDatesTime = '<?php echo $xAxisDatesTime; ?>';
            multiLineChart(data2, xAxisDatesTime, tickInterval, options2);


        });
    </script>
<?php }else{ ?>
    <h1>Only Company can Access this Section </h1>
<?php } ?>
