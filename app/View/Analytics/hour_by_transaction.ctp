<?php
$this->assign('pagetitle', __('Transactions By Hours Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

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
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'analytics', 'action' => 'hour_by_transaction'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


            ?>
        </div>
            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'hour_by_transaction', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));

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
    <!-- Remove the Charts -->
    <div class="header-content">
        <div class="page-title"><?php
                                echo  getReportFilter($this->Session->read('Report.Transaction'));

                                ?></div>
    </div>
    <div class="panel-body" id="chart-div">  
                                  
        <div class="chart" id="chart_div"></div> 
                                  
                         </div>

</div>



</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Transactions By Hours</h5>
    </div>





    <div class="table-responsive  htmlDataTable">
        <?php echo $this->element('user/hour_by_transaction', array('transactions' => $transactions, 'companyDetail' => $companyDetail)); ?>

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
        
        $('#daterange').daterangepicker({
            opens: 'center'
        }, function(start, end, label) {
            $('#analyticForm').submit();
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        //form submit 

        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo $newBarchat;?>);
            var options = {
                chart: {
                    title: 'Transactions By Hours',
                },
                bars: 'vertical',
                vAxis: {
                    format: ''
                },
                height: 350,
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };

            var chart = new google.charts.Bar(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    });
</script>