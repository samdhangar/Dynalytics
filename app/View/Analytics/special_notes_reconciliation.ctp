<?php
$this->assign('pagetitle', __('Special notes Reconciliation'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'special_notes_reconciliation'), array('title' => __('Export CSV'), 'id' => 'export_btn_specialNotes', 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

echo $this->Html->script('user/moment');
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');

?>
<!--Page Container-->
<div class="col-md-12 col-sm-12 form-group row">

    <div class="box box-primary">
        <div class="box-body">

            <?php
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'special_notes_reconciliation'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
          
            echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            ?>
        </div>
            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'special_notes_reconciliation', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));

            // echo $this->Form->end();

            $arrValidation = array(
                'Rules' => array(
                    'company_id' => array(
                        'required' => 1
                    ),
                    'date' => array(
                        'required' => 1
                    )
                ),
                'Messages' => array(
                    'company_id' => array(
                        'required' => __('Please select Region')
                    ),
                    'regiones' => array(
                        'required' => __('Please select Region')
                    ),
                    'date' => array(
                        'required' => __('Please select Date')
                    )
                )
            );
            echo $this->Form->setValidation($arrValidation);
            echo $this->Form->end();



            ?>
    </div>

</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Special Notes Reconciliation Data</h5>
    </div>
    <div class="panel-heading1" style="margin: 0px 7px; text-align:end">

        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
        <label class="switch">
            <input type="checkbox">
            <span class="slider round"></span>
        </label>
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>

        <div class="table-responsive  htmlDataTable">
            <?php echo $this->element('user/special_notes_reconciliation', array('specialNotes' => $specialNotes, 'companyDetail' => $companyDetail)); ?>

        </div>
    </div>
</div>
</div>

<?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
//echo $this->Html->script('/app/webroot/js/charts/c3/c3_bars_pies');
echo $this->Html->script('user/chart');


?>
<script type="text/javascript">
    google.load("visualization", "1", {
        packages: ["corechart"]
    });
    google.setOnLoadCallback(drawColumn);

    //google.charts.load('current', {'packages':['corechart']});
    //google.charts.setOnLoadCallback(drawChart);

    $(function() {
        'use strict';
        // Pie chart
        // ------------------------------
        var data = '<?php echo $temp1; ?>';
        var cars = [];
        var cars2 = [];



        var data2 = '<?php echo $sentTemp; ?>';
        var denom_data = [];
        var denom_data2 = [];
        var seriesOptions = [];




    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var getUrl = $('#export_btn_specialNotes').attr('href');
        $('#export_btn_specialNotes').attr('href', getUrl + "/count");
        $(':checkbox').change(function(event) {
            var checkSlider = event.currentTarget.checked;
            if (checkSlider == false) {
                $('#export_btn_specialNotes').attr('href', getUrl + "/count");
                $(".dis_count").show();
                $(".dis_amount").hide();

            }
            if (checkSlider == true) {
                $('#export_btn_specialNotes').attr('href', getUrl + '/amount');
                $(".dis_count").hide();
                $(".dis_amount").show();
            }
        });
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var startDate = $("input[name=daterangepicker_start]").val();
        var endDate = $("input[name=daterangepicker_end]").val();
        var date_set = startDate + "-" + endDate;
        $('#daterange').val(date_set);
        $('#daterange').attr('disabled', true);
        $('#daterange').daterangepicker({
            opens: 'center'
        }, function(start, end, label) {
            $('#analyticForm').submit();
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        $('#daterange').attr('disabled', true);
        //form submit 

        jQuery('#analyCompId').on('change', function() {
            if (jQuery(this).val() != '') {
                jQuery('#analyticForm').submit();
            }
        });

        //var data = '<?php //echo $temp; 
                        ?>';
        var pieData = '<?php echo $transactionPie; ?>';
        var pieTitle = '<?php echo $pieTitle; ?>';
        var pieCatTitle = '<?php echo $pieCatTitle; ?>';
        var pieClientTitle = '<?php echo $pieClientTitle; ?>';
        var pieName = '<?php echo $pieName; ?>';
        var pieCatData = '<?php echo $transactionCatPie; ?>';
        var pieClientData = '<?php echo $transactionClientPie; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';

        // var options = {
        //     name: '<?php echo __('Transaction'); ?>',
        //     title: '<?php echo __('Transaction Details'); ?>',
        //     xTitle: '<?php echo __('Transaction Date'); ?>',
        //     yTitle: '<?php echo __('No. of Transactions'); ?>',
        //     id: '#container',
        // };

        // lineChart(data, xAxisDates, tickInterval, options);
        var data1 = '<?php echo $newchartdata; ?>';

        // var data1 = '[[1621017000000,0],[1621103400000,0],[1621189800000,0],[1621276200000,0],[1621362600000,0],[1621449000000,0],[1621535400000,0],[1621621800000,0],[1621708200000,0],[1621794600000,0],[1621881000000,0],[1621967400000,0],[1622053800000,0],[1622140200000,0],[1622226600000,0],[1622313000000,0],[1622399400000,0],[1622485800000,0],[1622572200000,0],[1622658600000,0],[1622745000000,0],[1622831400000,0],[1622917800000,0],[1623004200000,0],[1623090600000,0],[1623177000000,0],[1623263400000,0],[1623349800000,0],[1623436200000,0],[1623522600000,0],[1623609000000,0],[1623695400000,0],[1623781800000,0],[1623868200000,0],[1623954600000,0],[1624041000000,0],[1624127400000,0],[1624213800000,0],[1624300200000,0],[1624386600000,0],[1624473000000,0],[1624559400000,0],[1624645800000,0],[1624732200000,0],[1624818600000,0],[1624905000000,0],[1624991400000,0],[1625077800000,0],[1625164200000,0],[1625250600000,0],[1625337000000,0],[1625423400000,0],[1625509800000,0],[1625596200000,0],[1625682600000,0],[1625769000000,0],[1625855400000,0],[1625941800000,0],[1626028200000,0],[1626114600000,0],[1626201000000,0],[1626287400000,0],[1626373800000,0],[1626460200000,0],[1626546600000,0],[1626633000000,0],[1626719400000,0],[1626805800000,0],[1626892200000,0],[1626978600000,0],[1627065000000,0],[1627151400000,0],[1627237800000,0],[1627324200000,0],[1627410600000,0],[1627497000000,0],[1627583400000,0],[1627669800000,0],[1627756200000,0],[1627842600000,0],[1627929000000,0],[1628015400000,0],[1628101800000,0],[1628188200000,0],[1628274600000,0],[1628361000000,0],[1628447400000,0],[1628533800000,0],[1628620200000,0],[1628706600000,0],[1628793000000,0],[1628879400000,0],[1628965800000,0],[1629052200000,0],[1629138600000,0],[1629225000000,0],[1629311400000,0],[1629397800000,0],[1629484200000,0],[1629570600000,0],[1629657000000,0],[1629743400000,0],[1629829800000,0],[1629916200000,0],[1630002600000,0],[1630089000000,0],[1630175400000,0],[1630261800000,0],[1630348200000,0],[1630434600000,0],[1630521000000,0],[1630607400000,0],[1630693800000,0],[1630780200000,0],[1630866600000,0],[1630953000000,0],[1631039400000,0],[1631125800000,0],[1631212200000,0],[1631298600000,0],[1631385000000,0],[1631471400000,0],[1631557800000,0],[1631644200000,0],[1631730600000,0],[1631817000000,0],[1631903400000,0],[1631989800000,0],[1632076200000,0],[1632162600000,0],[1632249000000,0],[1632335400000,0],[1632421800000,0],[1632508200000,0],[1632594600000,0],[1632681000000,0],[1632767400000,7],[1632853800000,180],[1632940200000,208],[1633026600000,278],[1633113000000,0],[1633199400000,0],[1633285800000,111],[1633372200000,89],[1633458600000,51],[1633545000000,122],[1633631400000,0],[1633717800000,0],[1633804200000,0],[1633890600000,0],[1633977000000,82],[1634063400000,39],[1634149800000,95],[1634236200000,0],[1634322600000,0],[1634409000000,0],[1634495400000,294],[1634581800000,20],[1634668200000,0],[1634754600000,0],[1634841000000,0],[1634927400000,0],[1635013800000,0],[1635100200000,0],[1635186600000,0],[1635273000000,0],[1635359400000,0],[1635445800000,0],[1635532200000,0],[1635618600000,0],[1635705000000,0],[1635791400000,0],[1635877800000,0],[1635964200000,0],[1636050600000,0],[1636137000000,0],[1636223400000,0],[1636309800000,0],[1636396200000,0],[1636482600000,0],[1636569000000,0],[1636655400000,0],[1636741800000,0],[1636828200000,0],[1636914600000,0]]';
        var options1 = {
            name: 'Transaction',
            title: '<?php echo __('Transaction Details'); ?>',
            xTitle: '<?php echo __('Transaction Date'); ?>',
            yTitle: '<?php echo __('No. of Transactions'); ?>',
            id: '#container',
        };
        lineChart(data1, xAxisDates, tickInterval, options1);
        //        transactionDetailChart(data, xAxisDates, tickInterval);
        pieChart(pieData, pieTitle, pieName, '#containerPie');
        pieChart(pieCatData, pieCatTitle, pieName, '#containerCatPie');
        pieChart(pieClientData, pieClientTitle, pieName, '#containerClientPie');

        var startDate = moment('<?php echo $this->Session->read('Report.Transaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Transaction.end_date') ?>', 'YYYY-MM-DD');
        var extraParams = {
            pieCatTitle: pieCatTitle,
            pieClientTitle: pieClientTitle,
            charts: {
                0: {
                    name: 'categoryChart',
                    data: 'transactionCatPie',
                    id: '#containerCatPie',
                    title: pieCatTitle,
                    chartName: pieName
                },
                1: {
                    name: 'clientChart',
                    data: 'transactionClientPie',
                    id: '#containerClientPie',
                    title: pieClientTitle,
                    chartName: pieName
                },
                2: {
                    type: 'multiLine',
                    name: 'Transaction',
                    title: 'Transaction',
                    data: 'transactionDetails',
                    xTitle: 'Transaction Date',
                    yTitle: 'Transaction',
                    id: '#container'
                }
            }

        };
        addDateRange(startDate, endDate, "analytics/transaction_details", 'lineChart2', 'pieChart', extraParams);

    });


   
</script>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 53px;
        height: 28px;
        padding: 2px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>