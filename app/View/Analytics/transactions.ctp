<?php
$this->assign('pagetitle', __('Transaction Details'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');

echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.NoOfTransaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
$this->end();

?>
<div class="row">
    <label class="col-md-12 col-sm-12 graphTitle h4">
        <?php
        echo getReportFilter($this->Session->read('Report.NoOfTransaction'));

        ?>
    </label>
    <div class="col-md-6 col-sm-6">
        <div id="container" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div id="containerPie" style="min-width: 310px; height: 600px; margin: 0 auto"></div>

    </div>
</div>
<?php
$yAxisData = array('1');

?>
<script type="text/javascript">
    function transactionDetailChart(data, xAxisDates, yAxisData)
    {
        $chart = $('#container').highcharts({
            rangeSelector: {
                selected: 1
            },
            chart: {
                height: 500,
                type: 'line',
                zoomType: 'xy',
            },
            title: {
                text: 'Transaction Details',
                x: -20 //center
            },
            xAxis: {
                labels: {
                    rotation: -45,
                },
                title: {
                    text: 'Transaction Date'
                },
                categories: JSON.parse(xAxisDates)
            },
            yAxis: {
                title: {
                    text: 'No. of Transactions'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            plotOptions: {
                line: {
                    marker: {
                        enabled: true
                    }
                },
                spline: {
                    marker: {
                        enabled: true
                    }
                }
            },
            series: [{
                    name: 'Files',
                    data: JSON.parse(data),
                }]
        });

    }
    function transactionDetailPieChart(data)
    {
        
        $chart = $('#containerPie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                height: 500,
                type: 'pie'
            },
            title: {
                text: 'Transaction Details'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: "Transactions",
                    colorByPoint: true,
                    data: JSON.parse(data),
                }]
        });
    }
    jQuery(document).ready(function () {
        Highcharts.setOptions({// This is for all plots, change Date axis to local timezone
            global: {
                useUTC: false
            },
			lang: {
				thousandsSep: ','
			}
        });
        var data = '<?php echo $temp; ?>';
        var data1 = '<?php echo $transactionPie; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        transactionDetailChart(data, xAxisDates);
        transactionDetailPieChart(data1);

        var datesRange = {
            today: {
                title: 'Today',
                key: 'today',
                start: moment(),
                end: moment(),
            },
            last_7days: {
                title: 'Last 7 Days',
                key: 'last_7days',
                start: moment().subtract('days', 6),
                end: moment()
            },
            last_15days: {
                title: 'Last 15 Days',
                key: 'last_15days',
                start: moment().subtract('days', 14),
                end: moment()
            },
            last_months: {
                title: 'Last Month',
                key: 'last_months',
                start: moment().subtract('month', 1).startOf('month'),
                end: moment().subtract('month', 1).endOf('month')
            },
            last_3months: {
                title: 'Last 3 Month',
                key: 'last_3months',
                start: moment().subtract('month', 3).startOf('month'),
                end: moment().subtract('month').startOf('month')
            }
        };
        var startDate = moment('<?php echo $this->Session->read('Report.NoOfTransaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.NoOfTransaction.end_date') ?>', 'YYYY-MM-DD');

        $('.daterange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 15 Days': [moment().subtract('days', 14), moment()],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Last 3 Month': [moment().subtract('month', 3).startOf('month'), moment().subtract('month').startOf('month')],
            },
            startDate: startDate,
            endDate: endDate,
        },
                function (start, end) {
                    var clickedLabel = getSelectedRange(start, end);
                    loader('show');
                    var formData = {
                        'start_date': start.format('YYYY-MM-DD'),
                        'end_date': end.format('YYYY-MM-DD'),
                        'from': clickedLabel.key
                    };
                    jQuery.ajax({
                        url: BaseUrl + "analytics/transactions",
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            loader('hide');
                            jQuery('.daterange span').html(clickedLabel.title);
                            jQuery('.graphTitle').html(clickedLabel.title);
                            transactionDetailChart(response.data, response.xAxisDates);
                            transactionDetailPieChart(response.transactionPie);
                        },
                        error: function () {
                            loader('hide');
                        }
                    });
                });
        function getSelectedRange(cStart, cEnd)
        {
            cStart = cStart.format('YYYY-MM-DD');
            cEnd = cEnd.format('YYYY-MM-DD');
            var response = {
                title: '',
                key: ''
            };
            /**
             * 
             * For the check today's date 
             */
            var start = datesRange.today.start.format('YYYY-MM-DD');
            var end = datesRange.today.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.today.title;
                response.key = datesRange.today.key;
                return response;
            }

            start = datesRange.last_7days.start.format('YYYY-MM-DD');
            end = datesRange.last_7days.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.last_7days.title;
                response.key = datesRange.last_7days.key;
                return response;
            }

            start = datesRange.last_15days.start.format('YYYY-MM-DD');
            end = datesRange.last_15days.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.last_15days.title;
                response.key = datesRange.last_15days.key;
                return response;
            }

            start = datesRange.last_months.start.format('YYYY-MM-DD');
            end = datesRange.last_months.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.last_months.title;
                response.key = datesRange.last_months.key;
                return response;
            }
            start = datesRange.last_3months.start.format('YYYY-MM-DD');
            end = datesRange.last_3months.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.last_3months.title;
                response.key = datesRange.last_3months.key;
                return response;
            }
            response.title = 'Custom Range: ' + cStart + " to " + cEnd;
            response.key = 'customrange';
            return response;
        }
    });
    $(':checkbox').change(function (event) {
    var checkSlider = event.currentTarget.checked;
    if(checkSlider == false){
        $(".dis_count").show();
        $(".dis_amount").hide();

    }
    if(checkSlider == true){
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