<?php
$this->assign('pagetitle', __('Dashboard'));
$this->Custom->addCrumb(__('Dashboard'));
$this->start('top_links');
//echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.DashboardErrors'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
echo $this->Html->link('<span>' . __(!empty($this->Session->read('Dashboard.Filter')) ? getReportFilter($this->Session->read('Dashboard.Filter')) : 'Last 7 Days') . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
if (isCompany()) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'dashboard'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));
}
if (isCompany() || isAdminDealer() || isSuparDealer()):
    echo $this->Html->link(__('Inventory Management'), array('controller' => 'analytics', 'action' => 'inventory_management'), array('icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm'));
endif;
$this->end();

?>
<?php if (!isSupportDealer() && !isCompany()): ?>
    <div class="row">

      <div class="col-md-3 procesedFiles">
						<div class="panel panel-flat">
							<div class="panel-body p-b-10">
								<div class="row">
									<div class="col-md-8 col-xs-8">
										<div class="text-size-huge text-regular text-blue-dark text-semibold no-padding no-margin m-t-5 m-b-10"><?php echo $totalPFiles; ?></div>
										<span class="text-muted"><?php echo __('Total Processed Files'); ?></span>
									</div>
									<div class="col-md-4 col-xs-4">
										<i class="icon-magazine icon-4x icon-light"></i>
									</div>
								</div>
							</div>

						</div>
					</div>


          <div class="col-md-3 errors">
    						<div class="panel panel-flat">
    							<div class="panel-body p-b-10">
    								<div class="row">
    									<div class="col-md-8 col-xs-8">
    										<div class="text-size-huge text-regular text-danger-dark text-semibold no-padding no-margin m-t-5 m-b-10"><?php echo $totalErros; ?></div>
    										<span class="text-muted"><?php echo __('Total Error/Warnings'); ?></span>
    									</div>
    									<div class="col-md-4 col-xs-4">
    										<i class="icon-warning icon-4x icon-light"></i>
    									</div>
    								</div>
    							</div>

    						</div>
    					</div>

              <div class="col-md-3 notification">
        						<div class="panel panel-flat">
        							<div class="panel-body p-b-10">
        								<div class="row">
        									<div class="col-md-8 col-xs-8">
        										<div class="text-size-huge text-regular text-success-dark text-semibold no-padding no-margin m-t-5 m-b-10">  <?php
                              echo count($tickets['New']) + count($tickets['Open']) + count($tickets['Closed']);

                              ?></div>
        										<span class="text-muted"><?php echo __('Notification Events'); ?></span>
        									</div>
        									<div class="col-md-4 col-xs-4">
        										<i class="icon-notification2 icon-4x icon-light"></i>
        									</div>
        								</div>
        							</div>

        						</div>
        					</div>


        <?php if (!isDealer() && !isCompany()) : ?>

          <div class="col-md-3 messages">
                <div class="panel panel-flat">
                  <div class="panel-body p-b-10">
                    <div class="row">
                      <div class="col-md-8 col-xs-8">
                        <div class="text-size-huge text-regular text-amber-dark text-semibold no-padding no-margin m-t-5 m-b-10">  <?php echo $totalUnIdentiMsg; ?></div>
                        <span class="text-muted"><?php echo __('Unidentified Message'); ?></span>
                      </div>
                      <div class="col-md-4 col-xs-4">
                        <i class="icon-envelop3 icon-4x icon-light"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>



        <?php endif; ?>

        <?php if (isDealer()): ?>

          <div class="col-md-3 clients">
                <div class="panel panel-flat">
                  <div class="panel-body p-b-10">
                    <div class="row">
                      <div class="col-md-8 col-xs-8">
                        <div class="text-size-huge text-regular text-amber-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo!empty($totalClients) ? $totalClients : '0'; ?></div>
                        <span class="text-muted"><?php echo __('No. of Client'); ?></span>
                      </div>
                      <div class="col-md-4 col-xs-4">
                        <i class="icon-user icon-4x icon-light"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

        <?php endif; ?>

        <?php if (isCompanyAdmin()): ?>

          <div class="col-md-3 transactions">
                <div class="panel panel-flat">
                  <div class="panel-body p-b-10">
                    <div class="row">
                      <div class="col-md-8 col-xs-8">
                        <div class="text-size-huge text-regular text-danger-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo $totalTrans; ?></div>
                        <span class="text-muted"><?php echo __('No. of Transactions'); ?></span>
                      </div>
                      <div class="col-md-4 col-xs-4">
                        <i class="icon-coin-dollar icon-4x icon-light"></i>
                      </div>
                    </div>
                  </div>

                </div>
              </div>


        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (isDealer() || isSuparAdmin()): ?>
    <div class="row">
        <div class="col-md-12">

          <div class="panel panel-flat">
									<div class="panel-heading">
										<h5 class="panel-title">
									<?php echo __('Tickets'); ?>
										</h5>
									</div>
                  <div class="panel-body">
            <!-- Custom Tabs (Pulled to the right) -->
            <div class="nav-tabs-custom" id="ticketTable">
                <?php echo $this->element('ticketAjaxTable') ?>
            </div>
          </div>
        </div>


        </div>
    </div>
<?php endif; ?>

<?php if (isCompany()): ?>
  <div class="panel panel-flat">

      <div class="panel-body">
        <?php
        //echo $this->element('user/line_chart_container');

        ?>
        <div class="col-lg-6  col-md-12 col-sm-12 form-group">
            <div id="container"></div>
        </div>
        <div class="col-lg-6  col-md-12 col-sm-12 form-group">
            <div id="container1"></div>
        </div>
        <div class="col-lg-6  col-md-12 col-sm-12 form-group">
            <div id="container2"></div>
        </div>

    </div>

    </div>

<div class="panel panel-flat"  style="float:left;width:100%;">
  <div class="panel-heading">
      <h5 class="panel-title">File Processed Data</h5>
  </div>

    <div class="table-responsive">
        <?php echo $this->element('reports/dashboard_filedata', array('processFiles' => $processFiles,'navId'=>'file_processed_data')); ?>
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
                name: 'Errors',
                title: 'Errors',
                xTitle: 'Error Date',
                yTitle: 'No. of Errors',
                id: '#container'
            };
            multiLineChart(data, xAxisDates, tickInterval, options);
            var startDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.start_date') ?>', 'YYYY-MM-DD');
            var endDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.end_date') ?>', 'YYYY-MM-DD');
            //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');

            var data1 = '<?php echo $temp1; ?>';
            var options1 = {
                name: 'Transaction',
                title: 'Transaction',
                xTitle: 'Transaction Date',
                yTitle: 'Transaction',
                id: '#container1'
            };
            multiLineChart(data1, xAxisDates, tickInterval, options1);

            //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');


           /* var data2 = '<?php echo $sentTemp; ?>';
            var options2 = {
                name: 'Transaction',
                title: 'Transaction Denom',
                xTitle: 'Transaction Time',
                yTitle: 'No. Of Denom',
                id: '#container2'
            };
           // alert(data2);

            var xAxisDatesTime = '<?php echo $xAxisDatesTime; ?>';
            multiLineChart(data2, xAxisDatesTime, tickInterval, options2);*/
            var extraParams = {
                //                pieCatTitle: pieCatTitle,
                //                pieClientTitle: pieClientTitle,
                charts: {
                    0: {
                        type: 'multiLine',
                        name: 'Transaction',
                        title: 'Transaction',
                        data: 'transactionDetails',
                        xTitle: 'Transaction Date',
                        yTitle: 'Transaction',
                        id: '#container1'
                    }
                }

            };
            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart', '', extraParams);
            //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');
        });
    </script>
<?php endif; ?>
<?php echo $this->Html->script('user/chart'); ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var startDate = moment().subtract('days', 7);
        var endDate = moment();
        addDateRange(startDate, endDate, 'dashboardData');
//        $('.daterange').daterangepicker({
//            ranges: {
//                'Today': [moment(), moment()],
////                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
//                'Last 7 Days': [moment().subtract('days', 7), moment()],
//                'Last 15 Days': [moment().subtract('days', 14), moment()],
////                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
//                'Last Month': [moment().subtract('month', 1), moment()],
////                'Last 3 Month': [moment().subtract('month', 3).startOf('month'), moment().subtract('month').startOf('month')],
//                'Last 3 Month': [moment().subtract('month', 3), moment()],
//                'Last 6 Month': [moment().subtract('month', 6), moment()],
////                'Last 30 Days': [moment().subtract('days', 29), moment()],
////                'This Month': [moment().startOf('month'), moment().endOf('month')],
//            },
//            startDate: moment().subtract('days', 7),
//            endDate: moment()
//        },
//        function (start, end) {
//            var clickedLabel = getSelectedRange(start, end);
//            loader('show');
//            var formData = {
//                'Filter': {
//                    'start_date': start.format('YYYY-MM-DD'),
//                    'end_date': end.format('YYYY-MM-DD'),
//                    'from': clickedLabel.key
//                }
//            };
//            console.log(formData);
//            jQuery.ajax({
//                url: BaseUrl + "users/dashboard",
//                type: 'post',
//                data: formData,
//                dataType: 'json',
//                success: function (response) {
////                    jQuery('.transactions').attr('href', response.transactions.url);
//                    totalTrans = parseInt(response.transactions.totalTrans);
//                    jQuery('.transactions').find('.info-box-number').html(totalTrans);
//
////                    jQuery('.clients').attr('href', response.clients.url);
//                    totalClients = parseInt(response.clients.totalClients);
//                    jQuery('.clients').find('.info-box-number').html(totalClients);
//
////                    jQuery('.errors').attr('href', response.errors.url);
//                    totalErros = parseInt(response.errors.totalErros);
//                    jQuery('.errors').find('.info-box-number').html(totalErros);
//
////                    jQuery('.messages').attr('href', response.messages.url);
//                    totalUnIdentiMsg = parseInt(response.messages.totalUnIdentiMsg);
//                    jQuery('.messages').find('.info-box-number').html(totalUnIdentiMsg);
//
////                    jQuery('.procesedFiles').attr('href', response.errors.url);
//                    totalPFiles = parseInt(response.files.totalPFiles);
//                    jQuery('.procesedFiles').find('.info-box-number').html(totalPFiles);
//                    loader('hide');
//                    var displText = jQuery('.ranges li.active').html();
//                    if (displText == 'Custom Range') {
//                        displText = start.format('YYYY-MM-DD') + " to " + end.format('YYYY-MM-DD');
//                    }
//                    jQuery('.daterange span').html(displText);
//
//                    if (response.ticketTable) {
//                        jQuery('#ticketTable').html(response.ticketTable);
//                    }
//
//                },
//                error: function () {
//                    loader('hide');
//                }
//            });
//        });

    });
</script>
