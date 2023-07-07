<?php
$this->assign('pagetitle', __('User Activity'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'user_activity'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));

$this->end();

echo $this->Html->script('user/moment');
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<!--Page Container-->
<div class="col-md-12 col-sm-12 form-group row">

    <div class="box box-primary">
        <div class="box-body">

            <?php
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'user_activity'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :

            //     echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            // if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
            //     echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;


            // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

            // echo $this->Form->input('station', array('onchange' => 'formSubmit()', 'type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID'), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('tellerName', array('onchange' => 'formSubmit1()', 'type' => 'select', 'id' => 'tellerName', 'label' => __('Teller: '), 'empty' => __('Select All'), 'options' => $tellerNames_Arr, 'default' => 'all', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            echo $this->Form->input('date', array('onchange' => 'formSubmit1()','label' => __('Selected Date: '), 'id' => 'datepicker', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

            // echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            ?>
            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'user_activity', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));

            // echo $this->Form->end();

            $arrValidation = array(
                'Rules' => array(
                    'tellerName' => array(
                        'required' => 1
                    ),
                    'date' => array(
                        'required' => 1
                    )
                ),
                'Messages' => array(
                    'tellerName' => array(
                        'required' => __('Please select Teller Name')
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
            <h3><b>(Note : Please select teller and date to view the data)</b></h3>
        </div>
    </div>

</div>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">User Activity Data</h5>
    </div>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-family:Helvetica,Arial,sans-serif;font-size:16px;font-style:normal;font-variant-caps:normal;font-weight:normal;letter-spacing:normal;text-align:start;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(77,122,186);text-decoration:none;border:0px;border-collapse:collapse!important">
        <tbody>
            <tr>
                <td align="center" bgcolor="#edf2f8" style="">
                    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:100%;border:0px;border-collapse:collapse!important">
                        <tbody>
                            <tr>
                                <td align="left" bgcolor="#ffffff" valign="top" style="border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-right-radius:4px;border-bottom-left-radius:4px;color:rgb(85,85,85);font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:22px;padding:0px 20px 20px">
                                    <table cellpadding="2px" cellspacing="0" style="width:100%;table-layout:fixed;color:rgb(255,255,255);text-align:center;margin:20px 0px;border-collapse:collapse!important">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="width:129px;max-width:20%;margin:0px">
                                                    <table cellpadding="0" cellspacing="0" style="width:129px;border-collapse:collapse!important">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" bgcolor="#4d7ABA" valign="middle" style="width:129px;max-width:20%;color:rgb(255,255,255);font-size:24px;font-weight:300;border-bottom-width:1px;border-bottom-color:rgb(71,117,182);border-bottom-style:solid;margin:0px;padding:12px 5px"><?php echo !empty($transaction_count_data['total_deposit']) ? number_format($transaction_count_data['total_deposit']) : 0?></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" bgcolor="#84a3cf" style="width:129px;max-width:20%;color:rgb(255,255,255);font-weight:300;font-size:14px;border-top-width:1px;border-top-color:rgb(151,177,214);border-top-style:solid;margin:0px;padding:5px">Total Deposits</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td align="center" style="width:129px;max-width:20%;margin:0px">
                                                    <table cellpadding="0" cellspacing="0" style="width:129px;border-collapse:collapse!important">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" bgcolor="#5cb85c" valign="middle" style="width:129px;max-width:20%;color:rgb(255,255,255);font-size:24px;font-weight:300;border-bottom-width:1px;border-bottom-color:rgb(85,181,85);border-bottom-style:solid;margin:0px;padding:12px 5px"><?php echo !empty($transaction_count_data['total_withdrawal']) ? number_format($transaction_count_data['total_withdrawal']) : 0?></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" bgcolor="#91cf91" style="width:129px;max-width:20%;color:rgb(255,255,255);font-weight:300;font-size:14px;border-top-width:1px;border-top-color:rgb(163,215,163);border-top-style:solid;margin:0px;padding:5px">Total Withdrawal</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td align="center" style="width:165px;max-width:20%;margin:0px">
                                                    <table cellpadding="0" cellspacing="0" style="width:165px;border-collapse:collapse!important">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" bgcolor="#f0ad4e" valign="middle" style="width:129px;max-width:20%;color:rgb(255,255,255);font-size:24px;font-weight:300;border-bottom-width:1px;border-bottom-color:rgb(239,169,69);border-bottom-style:solid;margin:0px;padding:12px 5px">$ <?php echo !empty($transaction_count_data['total_amount_deposit']) ? number_format($transaction_count_data['total_amount_deposit']) : 0?></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" bgcolor="#f6ce95" style="width:129px;max-width:20%;color:rgb(255,255,255);font-weight:300;font-size:14px;border-top-width:1px;border-top-color:rgb(248,217,172);border-top-style:solid;margin:0px;padding:5px">Total Deposits Amount</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td align="center" style="width:165px;max-width:20%;margin:0px">
                                                    <table cellpadding="0" cellspacing="0" style="width:165px;border-collapse:collapse!important">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" bgcolor="#d9534f" valign="middle" style="width:129px;max-width:20%;color:rgb(255,255,255);font-size:24px;font-weight:300;border-bottom-width:1px;border-bottom-color:rgb(215,75,71);border-bottom-style:solid;margin:0px;padding:12px 5px">$ <?php echo !empty($transaction_count_data['total_amount_withdrawal']) ? number_format($transaction_count_data['total_amount_withdrawal']) : 0?></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" bgcolor="#e7908e" style="width:129px;max-width:20%;color:rgb(255,255,255);font-weight:300;font-size:14px;border-top-width:1px;border-top-color:rgb(235,165,163);border-top-style:solid;margin:0px;padding:5px">Total Withdrawal Amount</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>

                                                <td align="center" style="width:129px;max-width:20%;margin:0px">
                                                    <table cellpadding="0" cellspacing="0" style="width:129px;border-collapse:collapse!important">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" bgcolor="#4d7ABA" valign="middle" style="width:129px;max-width:20%;color:rgb(255,255,255);font-size:24px;font-weight:300;border-bottom-width:1px;border-bottom-color:rgb(71,117,182);border-bottom-style:solid;margin:0px;padding:12px 5px">$ <?php echo !empty($transaction_count_data['net_cash_usage']) ? number_format($transaction_count_data['net_cash_usage']) : 0?></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" bgcolor="#84a3cf" style="width:129px;max-width:20%;color:rgb(255,255,255);font-weight:300;font-size:14px;border-top-width:1px;border-top-color:rgb(151,177,214);border-top-style:solid;margin:0px;padding:5px">Net Cash Usage</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="panel-heading1" style="margin: 0px 7px; text-align:end">
    <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
        <label class="switch">
            <input type="checkbox">
            <span class="slider round"></span>
        </label>
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>

        <div class="table-responsive  htmlDataTable">
            <?php echo $this->element('user/user_activity', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

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
        $(function(){
            $("#datepicker").datepicker();
        });
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        // var today = now.getFullYear() + "/" + (month) + "/" + (day - 1) + "-" + now.getFullYear() + "/" + (month) + "/" + (day);

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