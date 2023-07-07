<?php
$this->assign('pagetitle', __('Audit trail Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.BillCountReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_count'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();

echo $this->Html->script('user/moment');

//   echo $this->Html->script('user/highcharts');
echo $this->Html->script('user/daterangepicker');
?>
<div class="col-md-12 col-sm-12 form-group row">

    <div class="box box-primary">
        <div class="box-body">
           
        </div>
    </div>

</div>



<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="header-content">
        <div class="page-title graphTitle"><?php
                                            echo getReportFilter($this->Session->read('Report.BillCountReport'));

                                            ?></div>

    </div>
            <div id="container"></div>



    <div class="table-responsive htmlDataTable">
        <?php echo $this->element('reports/bill_count', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
    </div>


</div>

<?php
echo $this->Html->script('user/chart');

?>

<script type="text/javascript">
    function pageload() {

    }

    jQuery(document).ready(function() {

        var data = '<?php echo $temp; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        var options = {
            name: '<?php echo $lineChartName; ?>',
            title: '<?php echo $lineChartTitle; ?>',
            xTitle: '<?php echo $lineChartxAxisTitle; ?>',
            yTitle: '<?php echo $lineChartyAxisTitle; ?>',
            id: '#container'
        };
        lineChart(data, xAxisDates, tickInterval, options);

        var startDate = moment('<?php echo $this->Session->read('Report.BillCountReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillCountReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/bill_count", '');

    });

    // function getBranches(compId) {
    //     if (compId == '') {
    //         jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //         jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     } else {
    //         loader('show');
    //         jQuery.ajax({
    //             url: BaseUrl + "/company_branches/get_branches/" + compId,
    //             type: 'post',
    //             success: function(response) {
    //                 loader('hide');
    //                 jQuery('#analyBranchId').html(response);
    //                 jQuery('#analyStationId').html('<option value="">Select All</option>');
    //             },
    //             error: function(e) {
    //                 loader('hide');
    //             }
    //         });
    //         $('#analyticForm').submit();
    //     }
    // }

    // function getStations(branchId) {
    //     if (branchId == '') {
    //         jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     } else {
    //         loader('show');
    //         jQuery.ajax({
    //             url: BaseUrl + "/company_branches/get_stations/" + branchId,
    //             type: 'post',
    //             data: {
    //                 data: jQuery('#analyBranchId').val()
    //             },
    //             success: function(response) {
    //                 loader('hide');
    //                 jQuery('#analyStationId').html(response);
    //             },
    //             error: function(e) {
    //                 loader('hide');
    //             }
    //         });
    //         $('#analyticForm').submit();
    //     }
    // }

    // function formSubmit() {
    //     $('#analyticForm').submit();
    // }
    var getUrl = $('#exportBtn').attr('href');
    $(document).ready(function() {
        $('#exportBtn').attr('href', getUrl + "/count");

    });
    $(':checkbox').change(function(event) {
        var checkSlider = event.currentTarget.checked;
        if (checkSlider == false) {
            $('#exportBtn').attr('href', getUrl + "/count");
            $(".dis_count").show();
            $(".dis_amount").hide();

        }
        if (checkSlider == true) {
            $('#exportBtn').attr('href', getUrl + '/amount');
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
  padding:2px;
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

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
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