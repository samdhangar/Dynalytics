<?php
$this->assign('pagetitle', __('Out Of Balance'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'transaction_details'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));

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
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'out_of_balance'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :

            //     echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            // if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
            //     echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;


            // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

            // echo $this->Form->input('station', array('onchange' => 'formSubmit()', 'type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('tellerName', array('onchange' => 'formSubmit1()', 'type' => 'select', 'id' => 'tellerName', 'label' => __('Teller: '), 'empty' => __('Select All'), 'options' => $tellerNames_Arr, 'default' => 'all', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('trasaction_type', array('onchange' => 'formSubmit1()', 'type' => 'select', 'id' => 'trasaction_type', 'label' => __('Transaction Type: '), 'empty' => __('Select All'), 'options' => $transaction_type_arr, 'default' => 'all', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            ?>
            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'out_of_balance', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));

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
        <div class="col-md-12 col-sm-12 form-group row" style=" padding-left: 0px; ">
        </div>
    </div>

</div>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="header-content">
        <div class="page-title"><?php
    
                             echo getReportFilter($this->Session->read('Report.OutOfBalance'));
                                ?></div>

        <div class="elements hidden">
            <a id="export"><i class="fa icon-download position-left"></i> Export</a>
        </div>
    </div>

    <div class="panel-body hidden" id="chart-div">

        <div class="chart" id="google-column"></div>

    </div>

    <div class="panel-body hidden" id="chart-div2">
        <div class="elements"><a id="export2"><i class="fa icon-download position-left"></i> Export</a></div>
        <div class="chart" id="google-column2"></div>

    </div>
    <div class="panel-body hidden" id="chart-div3">
        <div class="elements"><a id="export3"><i class="fa icon-download position-left"></i> Export</a></div>
        <div class="chart" id="google-column3"></div>

    </div>
    <div class="panel panel-flat" id="data-print" style="float:left;width:100%;">
        <div class="panel-heading">
            <!--  <pre id="whereToPrint1"></pre>
                  <pre id="whereToPrint2"></pre>
 <pre id="whereToPrint3"></pre>
                  <pre id="whereToPrint4"></pre>
                   <pre id="whereToPrint5"></pre>
                  <pre id="whereToPrint6"></pre>
 <pre id="whereToPrint7"></pre>
                  <pre id="whereToPrint8"></pre> -->

        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 hidden">
            <div class="panel panel-flat">
                <div class="header-content">
                    <div class="page-title"></div>
                    <div class="elements">
                        <a id="export4"><i class="fa icon-download position-left"></i> Export</a>
                    </div>
                </div>

                <div class="panel-body text-center">

                    <div class="display-inline-block" id="c3-pie-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12 hidden">
            <div class="panel panel-flat">
                <div class="header-content">
                    <div class="page-title"></div>
                    <div class="elements">
                        <a id="export5"><i class="fa icon-download position-left"></i> Export</a>
                    </div>
                </div>

                <div class="panel-body text-center">
                    <div class="display-inline-block" id="c3-pie-chart2"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
            <div id="container"></div>
        </div>
    </div>



</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Out Of Balance Transactions Data</h5>
    </div>
    <div class="panel-heading1" style="margin: 0px 7px; text-align:end">
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
        <label class="switch">
            <input type="checkbox">
            <span class="slider round"></span>
        </label>
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>




        <div class="table-responsive  htmlDataTable">
            <?php echo $this->element('user/out_of_balance', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

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

        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        // var today = now.getFullYear() + "/" + (month) + "/" + (day - 1) + "-" + now.getFullYear() + "/" + (month) + "/" + (day);
        var startDate = $("input[name=daterangepicker_start]").val();
        var endDate = $("input[name=daterangepicker_end]").val();
        var date_set = startDate + "-" + endDate;
        $('#daterange').val(date_set);
        $('#daterange').attr('disabled', true);

        // $('#daterange').val(today);
        $('#daterange').daterangepicker({
            opens: 'center'

        }, function(start, end, label) {
            $('#analyticForm').submit();
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        // $('#daterange').attr('disabled', true);

        //var data = '<?php //echo $temp; 
                        ?>';
        
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';

        var data1 = '<?php echo $newchartdata; ?>';
        var options1 = {
            name: 'Out of Balance',
            title: '<?php echo __('Out of Balance Details'); ?>',
            xTitle: '<?php echo __('Out of Balance Date'); ?>',
            yTitle: '<?php echo __('Number Out of Balance'); ?>',
            id: '#container',
        };

        // lineChart(data1, xAxisDates, tickInterval, options1);
        lineChart(data1, xAxisDates, tickInterval, options1);
        //        transactionDetailChart(data, xAxisDates, tickInterval);
 

        var startDate = moment('<?php echo $this->Session->read('Report.Transaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Transaction.end_date') ?>', 'YYYY-MM-DD');
    });
    function formSubmit1() {
        $("#daterange").val('');
        $('#analyticForm').submit();
    }

    var getUrl = $('#exportBtn').attr('href');
    $(document).ready(function() {
        $('#exportBtn').attr('href', getUrl + "/count");

    });
    $(':checkbox').change(function(event) {
        var checkSlider = event.currentTarget.checked;
        if (checkSlider == false) {
            $('#exportBtn').attr('href',getUrl+"/count");
            $(".dis_count").show();
            $(".dis_amount").hide();

        }
        if (checkSlider == true) {
            $('#exportBtn').attr('href',getUrl+'/amount');
            $(".dis_count").hide();
            $(".dis_amount").show();
        }
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