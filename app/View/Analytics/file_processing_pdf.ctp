<?php
echo $this->Html->css(array('lib/font-awesome.min', 'user/style'));
echo $this->Html->script(
    array(
        'user/jquery-2.1.0.min',
        'user/bootstrap.min',
        'lib/moment',
        'lib/jquery-ui-1.11.4',
        'lib/highcharts',
        'lib/exporting',
        'lib/daterangepicker/daterangepicker'
    )
);

?>



<?php if (!empty($displayFlag)): ?>
    <div id="removeWaitDiv" class="wysihtml5-supported  pace-done skin-blue">
        <div id="ajaxLoader">
            <span>
                <i class="fa fa-spin fa-spinner"></i>
                <?php echo __('Please Wait While PDF Generating...'); ?>
            </span>
        </div>
    </div>
<?php endif; ?>
<div id="anchoraTag"></div>
<?php if (empty($displayFlag)): ?>
    <div class="row">
        <div class="col-md-12 col-sm-12 table-responsive htmlDataTable">
            <div class="box box-primary">
                <div class="box-footer clearfix">
                    <?php if (!empty($companyDetail)): ?>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <strong>
                                    <?php echo __('Company Name:') ?>
                                </strong>
                                <span>
                                    <?php echo $companyDetail['Company']['first_name']; ?>
                                </span>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <?php $noOfFields = 30; ?>
                                <th>
                                    <?php
                                    echo __('Sr. No.');

                                    ?>
                                </th>
                                <th>
                                    <?php echo __('DynaCore Station ID'); ?>
                                </th>
                                <th> 
                                    <?php
                                    echo __('Branch Name');

                                    ?>
                                </th>
                                <th> 
                                    <?php
                                    echo __('Date');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. File received for processing');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('File Processed');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Errors');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of transactions');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of deposits');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of withdrawals');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of reports');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Automix Settings');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Bill Activity Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Bill Adjustment Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Bill Count Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Bill History Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Coin Inventory');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Current Teller Transaction');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of History Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Manager Setup');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Net Cash Usage Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Side Activity Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Teller Activity Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Valut Buy Report');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('No. of Teller Setup');

                                    ?>
                                </th>
                                <th>
                                    <?php echo __('Total Cash Deposited'); ?>
                                </th>
                                <th>
                                    <?php echo __('Total Cash Requested'); ?>
                                </th>
                                <th>
                                    <?php echo __('Total Cash Withdrawal'); ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('First Process Time');

                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo __('Last Process Time');

                                    ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($processFiles)): ?>
                                <tr>
                                    <td colspan="<?php echo $noOfFields; ?>">
                                        <?php echo __('No data available for selected period'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php
                            if (isTestMode()) {
                                debug($processFiles);
                                exit;
                            }
                            $startNo = 1;
                            foreach ($processFiles as $processFile):

                                ?> 
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['FileProccessingDetail']['station']) ? $processFile['FileProccessingDetail']['station'] : ''; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''; ?>
                                    </td>
                                    <td> 
                                        <?php echo showdate($processFile['FileProccessingDetail']['file_date']); ?>
                                    </td>
                                    <td> 
                                        <?php // echo $processFile['FileProccessingDetail']['no_of_file_received'];   ?>
                                        <?php echo isset($processFile['FileProccessingDetail']['processing_counter']) ? $processFile['FileProccessingDetail']['processing_counter'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php // echo $processFile['FileProccessingDetail']['fileProcessed'];   ?>
                                        <?php echo isset($processFile['FileProccessingDetail']['processing_counter']) ? $processFile['FileProccessingDetail']['processing_counter'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['ErrorDetail'][0]['no_of_errors']) ? $processFile['ErrorDetail'][0]['no_of_errors'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['TransactionDetail'][0]['no_of_transaction']) ? $processFile['TransactionDetail'][0]['no_of_transaction'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['TransactionDetail'][0]['no_of_deposit']) ? $processFile['TransactionDetail'][0]['no_of_deposit'] : 0; ?>

                                    </td>
                                    <td>
                                        <?php echo isset($processFile['TransactionDetail'][0]['no_of_withdrawal']) ? $processFile['TransactionDetail'][0]['no_of_withdrawal'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['TransactionDetail'][0]['no_of_report']) ? $processFile['TransactionDetail'][0]['no_of_report'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['AutomixSetting'][0]['no_of_automix']) ? $processFile['AutomixSetting'][0]['no_of_automix'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['BillsActivityReport'][0]['no_of_billactivity']) ? $processFile['BillsActivityReport'][0]['no_of_billactivity'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['BillAdjustment'][0]['no_of_billadjustment']) ? $processFile['BillAdjustment'][0]['no_of_billadjustment'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['BillCount'][0]['no_of_billcount']) ? $processFile['BillCount'][0]['no_of_billcount'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['BillHistory'][0]['no_of_billhistory']) ? $processFile['BillHistory'][0]['no_of_billhistory'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['CoinInventory'][0]['no_of_coininventory']) ? $processFile['CoinInventory'][0]['no_of_coininventory'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans']) ? $processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['HistoryReport'][0]['no_of_historyReport']) ? $processFile['HistoryReport'][0]['no_of_historyReport'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['ManagerSetup'][0]['no_of_mgrSetup']) ? $processFile['ManagerSetup'][0]['no_of_mgrSetup'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage']) ? $processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['SideActivityReport'][0]['no_of_sideActivity']) ? $processFile['SideActivityReport'][0]['no_of_sideActivity'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['TellerActivityReport'][0]['no_of_tellerActivity']) ? $processFile['TellerActivityReport'][0]['no_of_tellerActivity'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['ValutBuy'][0]['no_of_vaultBuy']) ? $processFile['ValutBuy'][0]['no_of_vaultBuy'] : 0; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['TellerSetup'][0]['no_of_teller_setup']) ? $processFile['TellerSetup'][0]['no_of_teller_setup'] : 0; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['TransactionDetail'][0]['total_cash_deposit']) ? $processFile['TransactionDetail'][0]['total_cash_deposit'] : ''; ?>
                                    </td>
                                    <td> 
                                        <?php echo isset($processFile['TransactionDetail'][0]['total_cash_requested']) ? $processFile['TransactionDetail'][0]['total_cash_requested'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($processFile['TransactionDetail'][0]['total_cash_withdrawal']) ? $processFile['TransactionDetail'][0]['total_cash_withdrawal'] : ''; ?>
                                    </td>
                                    <td> 
                                        <?php echo showdatetime($processFile['FileProccessingDetail']['processing_starttime']); ?>
                                    </td>
                                    <td> 
                                        <?php echo showdatetime($processFile['FileProccessingDetail']['processing_endtime']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>
<div style="visibility: hidden;">
    <div id="header">

    </div>
    <div class="row" style="margin: 0;">
        <div id="lineChartCanvasImg"  style="width: 100%"><img src=""></div>
        <div id="lineChartCanvas"  style="width: 100%"></div>
        <div id="pieChartCanvas"></div>
        <div>
            <div id="container" style="display: none;width: 100%"></div>
        </div>

        <div>
            <div id="containerPie" style="display: none;width: 100%"></div>
        </div>


    </div>
</div>

<?php
if ($displayFlag):
    $redirectUrl = Router::url(array('controller' => 'analytics', 'action' => 'file_processing'), true);
    echo $this->Html->script('user/chart');

    ?>


    <script type="text/javascript">
        //        setTimeout(function () {
        //            window.location.href = "<?php echo $redirectUrl; ?>";
        //        }, 15000);


        function autoServerExportChart() {
            var obj = {},
                    chart;

            chart = $('#container').highcharts();
            obj.svg = chart.getSVG();
            obj.type = 'image/png';
            obj.width = 1024;
            obj.async = true;

            var objPie = {},
                    chart;

            chartPie = $('#containerPie').highcharts();
            objPie.svg = chartPie.getSVG();
            objPie.type = 'image/png';
            objPie.width = 1024;
            objPie.async = true;
            console.log('here');
            $.ajax({
                type: 'post',
                url: chart.options.exporting.url,
                data: obj,
                async: false,
                success: function (data) {
                    var lineFullPath = this.url + data;
                    var pieFullPath = '';
                    $.ajax({
                        type: 'post',
                        url: chartPie.options.exporting.url,
                        data: objPie,
                        async: false,
                        success: function (data1) {
                            pieFullPath = this.url + data1;
                            //save url
                            $.ajax({
                                type: 'post',
                                url: '<?php echo Router::url(array('controller' => 'analytics', 'action' => 'file_processing_pdf'), true); ?>',
                                async: false,
                                data: {lineChartUrl: lineFullPath, pieChartUrl: pieFullPath},
                                dataType: 'json',
                                success: function (data3) {
                                    if (data3.status == 'success') {
                                        var downloadUrl = '<?php echo Router::url(array('controller' => 'analytics', 'action' => 'download_file'), true); ?>/' + data3.filename;
                                        window.location = downloadUrl;
                                        setTimeout(function () {
                                            window.location = '<?php echo Router::url(array('controller' => 'analytics', 'action' => 'file_processing'), true); ?>';
                                        }, 1500);
                                    } else {
                                        alert('Unable to generate pdf.Please try again later');
                                        window.location = '<?php echo Router::url(array('controller' => 'analytics', 'action' => 'file_processing'), true); ?>';
                                    }
                                }
                            });
                        }
                    });
                }

            });
        }

        var data = '<?php echo $temp; ?>';
        var pieData = '<?php echo $filesPieData; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        fileProcessChart(data, xAxisDates, tickInterval);
        pieChart(pieData, pieTitle, pieName);
        autoServerExportChart();
        //        autoServerExportLineChart();//Get Url Of Line Chart
        //        autoServerExportPieChart();//Get Url Of Pie Chart
    </script>
<?php endif; ?>
<style type="text/css">

    th{
        border: 1px solid #ddd;
        border-bottom-width: 2px;
        padding: 8px;
        line-height: 1.428571429;
    }
    td{
        border: 1px solid #ddd;
        padding: 8px;
        line-height: 1.428571429;
        vertical-align: top;
    }
    table{
        max-width: 100%;
    }
    /*    #lineChartCanvas{
            margin-top:  30px;
        }
        #lineChartCanvas canvas{
            margin-right: 30px;
            margin-bottom: 30px;
        }*/
    body{
        overflow: hidden !important;
    }

</style>