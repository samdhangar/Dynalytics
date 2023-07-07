<?php
$this->assign('pagetitle', __('Out Of Balance'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

// echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'transaction_details'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));

$this->end();

echo $this->Html->script('user/moment');
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');

?>
<!--Page Container-->
<div class="col-md-12 col-sm-12 form-group row">

</div>


<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Previous 10 Transactions</h5>
    </div>
    <div class="panel-heading1" style="margin: 0px 7px; text-align:end">
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;"> Count</label>
        <label class="switch">
            <input type="checkbox">
            <span class="slider round"></span>
        </label>
        <label class="count" style="font-size: 15px; font-weight: bold; vertical-align: middle;">Amount</label>




        <div class="table-responsive  htmlDataTable">
            <?php echo $this->element('user/previous_transaction', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

        </div>
    </div>
</div>
</div>

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