var datesRange = {
    today: {
        title: 'Today',
        key: 'today',
        start: moment(),
        end: moment()
    },
    last_7days: {
        title: 'Last 7 Days',
        key: 'last_7days',
        start: moment().subtract(6, 'days'),
        end: moment()
    },
    last_10days: {
        title: 'Last 10 Days',
        key: 'last_10days',
        start: moment().subtract(10, 'days'),
        end: moment()
    },
    last_18days: {
        title: 'Current Month',
        key: 'last_18days',
        start: moment().subtract(17, 'days'),
        end: moment()
    },
    last_months: {
        title: 'Last Month',
        key: 'last_months',
        start: moment().subtract(1, 'month'),
        end: moment()
    },
    // working_months: {
    //     title: 'Current Month',
    //     key: 'working_months',
    //     start: moment().subtract(30,'days'),
    //     end: moment()
    // },
    last_3months: {
        title: 'Last 3 Month',
        key: 'last_3months',
        start: moment().subtract(3, 'month'),
        end: moment()
    },
    // last_6months: {
    //     title: 'Last 6 Month',
    //     key: 'last_6months',
    //     start: moment().subtract(6, 'month'),
    //     end: moment()
    // },
    last_12months: {
        title: 'Year to Date',
        key: 'last_12months',
        start: moment().subtract(12, 'month'),
        end: moment()
    },
    all_dates: {
        title: 'All Dates',
        key: 'all_dates',
        start: moment().subtract(10, 'year'),
        end: moment()
    },
    custom_range: {
        title: 'Custom Range',
        key: 'custom_range',
        start: moment(),
        end: moment()
    }
};
jQuery(document).ready(function() {
    Highcharts.setOptions({ // This is for all plots, change Date axis to local timezone
        global: {
            useUTC: false
        },
        lang: {
            noData: 'No data available for selected period',
            decimalPoint: '.',
            thousandsSep: ','
        },
        exporting: {
            sourceWidth: 1500,
            sourceHeight: 800,
            enabled: true
        }
    });
});
var graph_pastel_colour = ['#b6cabe', '#96b1ce', '#f0dacd', '#d7c6ea', '#f1b9c4', '#f0dacd', '#aee9f7', '#f1f7ad'];

function getGraphFunctionName(funName, response, title, name) {

    if (funName == 'multiLineChart' && response.htmlData == 'dashboard') {

        var pieData = response.pie_data;
        data = JSON.stringify(response.pie_data);
        data_hard_ware = JSON.stringify(response.chartDataHardwareError);
        $('#c3-pie-chart').remove();
        $('#chart-div4').append("<div class='isplay-inline-block c3'  id='c3-pie-chart'><span></span></div>");

        $('#google-bar').remove();
        $('#chart-div3').append("<div class='chart'  id='google-bar'><span></span></div>");
        pieChart_details(data, 'c3-pie-chart');
        var data_hardware = response.chartDataHardwareError;

        drawBar(data_hard_ware);



    }

    if (funName == 'lineChart_bill_inventory') {

        var options = {
            name: '<?php echo $lineChartName; ?>',
            title: '<?php echo $lineChartTitle; ?>',
            xTitle: '<?php echo $lineChartxAxisTitle; ?>',
            yTitle: '<?php echo $lineChartyAxisTitle; ?>',
            id: '#container'
        };
        graph_data_all_new = JSON.parse(response.data);
        $.each(graph_data_all_new, function(key, value) {
            options['title'] = key;
            graph_inventory(value, key);

        });

    }



    if (funName == 'fileProcessChart') {
        fileProcessChart(response.data, response.xAxisDates, response.tickInterval);
    } else if (funName == 'transactionDetailChart') {
        transactionDetailChart(response.data, response.xAxisDates, response.tickInterval);
    } else if (funName == 'transactionDetailPieChart') {
        transactionDetailPieChart(response.transactionPie);
    }
    if (funName == 'lineChart') {
        if (response.options == undefined) {
            var options = {
                name: 'Errors',
                title: 'Errors',
                xTitle: 'Error Date',
                yTitle: 'No. of Errors',
                id: '#container'
            };
        } else {
            options = response.options;

        }
        if (options.id == undefined) {
            options.id = '#container';
        }

        multiLineChart2(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'lineChart2') {
        if (response.options == undefined) {
            var options = {
                name: 'Errors',
                title: 'Errors',
                xTitle: 'Error Date',
                yTitle: 'No. of Errors',
                id: '#container'
            };
        } else {
            options = response.options;

        }
        if (options.id == undefined) {
            options.id = '#container';
        }

        multiLineChart2(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'googleGraph') {
        if (response.options == undefined) {
            var options = {
                name: 'Errors',
                title: 'Errors',
                xTitle: 'Error Date',
                yTitle: 'No. of Errors',
                id: '#container'
            };
        } else {
            options = response.options;

        }
        if (options.id == undefined) {
            options.id = '#container';
        }

        googleGraph(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'googleGraphGroup') {
        if (response.options == undefined) {
            var options = {
                name: 'Errors',
                title: 'Errors',
                xTitle: 'Error Date',
                yTitle: 'No. of Errors',
                id: '#container'
            };
        } else {
            options = response.options;

        }
        if (options.id == undefined) {
            options.id = '#container';
        }

        googleGraphGroup(response.data, response.xAxisDates, response.tickInterval, options);
    }

    if (funName == 'google_error_warning') {

        if (response.options == undefined) {
            var options = {
                name: 'Error Warning',
                title: 'Error Warning',
                xTitle: 'Error Warning',
                yTitle: 'Error Warning',
                id: '#container'
            };
        } else {
            options = response.options;

        }
        if (options.id == undefined) {
            options.id = '#container';
        }
        google_error_warning(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'multiLineChart') {

        if (response.options == undefined) {
            var options = {
                name: 'Issue Report',
                title: 'Issue Report',
                xTitle: 'Issue Report Date',
                yTitle: 'No. of Issue Report',
                id: '#container'
            };
        } else {
            options = response.options;
        }
        if (options.id == undefined) {
            options.id = '#container';
        }
        multiLineChart(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'multiLineChart2') {

        if (response.options == undefined) {
            var options = {
                name: 'Issue Report',
                title: 'Issue Report',
                xTitle: 'Issue Report Date',
                yTitle: 'No. of Issue Report',
                id: '#container'
            };
        } else {
            options = response.options;
        }
        if (options.id == undefined) {
            options.id = '#container';
        }
        multiLineChart2(response.data, response.xAxisDates, response.tickInterval, options);
    }
    if (funName == 'pieChart') {
        pieChart2(response.pieChartData, 'c3-pie-chart');
        pieChart2(response.transactionClientPie, 'c3-pie-chart2');
        //  pieChart(response.pieChartData, response.pieTitle, response.pieName);
    }


}

function resetGraph(start, end, action, chartFun, pieGraph, extraParams) {
    var clickedLabel = getSelectedRange(start, end,chartFun);
    console.log(clickedLabel);
    loader('show');
    var formData = {
        'Filter': {
            'start_date': start.format('YYYY-MM-DD'),
            'end_date': end.format('YYYY-MM-DD'),
            'from': clickedLabel.key
        }
    };
    //     document.getElementById("whereToPrint1").innerHTML = JSON.stringify(formData, null, 4);
        // console.log(formData);
    // alert(BaseUrl+action);

    jQuery.ajax({
        url: BaseUrl + action,
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function(response) {
            loader('hide');
            jQuery('.daterange span').html(clickedLabel.title);
            jQuery('.graphTitle').html(clickedLabel.title);
            getGraphFunctionName(chartFun, response);
            if (response.htmlData != undefined && response.htmlData != '') {
                jQuery('.htmlDataTable').html(response.htmlData);
            }
            if (pieGraph != '' || pieGraph != undefined) {
                getGraphFunctionName(pieGraph, response);
            }
            
            if (extraParams != undefined && extraParams.charts != undefined && extraParams.charts != '') {
                $.each(extraParams.charts, function(key, value) {

                    if (typeof value.type == undefined || value.type == '') {

                        pieChart(response[value.data], value.title, value.chartName, value.id);
                    } else if (value.type == 'line') {

                        pieChart(response[value.data], value.title, value.chartName, value.id);
                    } else if (value.type == 'multiLine') {
                        if (value == undefined) {
                            var options = {
                                name: 'Issue Report',
                                title: 'Issue Report',
                                xTitle: 'Issue Report Date',
                                yTitle: 'No. of Issue Report',
                                id: '#container1'
                            };
                        } else {
                            var optionsName = 'options' + value.data;
                            options = response[optionsName];
                        }
                        /* if (con == undefined) {
                             value.id = '#container';
                         }*/
                        multiLineChart2(response[value.data], response.xAxisDates, response.tickInterval, options);
                    } else {
                        pieChart2(response[value.transactionClientPie], 'c3-pie-chart');
                        pieChart2(response[value.transactionPie], 'c3-pie-chart2');
                        // pieChart(response[value.data], value.title, value.chartName, value.id);
                    }
                });
            }
            // location.reload();
            // alert("heloooo");
        },
        error: function() {
            loader('hide');
        }
    });
}

function fileProcessChart(data, xAxisDates, tickInterval) {
    $chart = $('#container').highcharts({
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'line',
            zoomType: 'xy',
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Log File Processing',
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            title: {
                text: 'Date',
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
            labels: {
                rotation: -45,
                formatter: function() {
                    return Highcharts.dateFormat('%b-%d-%Y', this.value);
                }
            },
            //            tickInterval: tickInterval,
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
            },
            //            categories: JSON.parse(xAxisDates)

        },
        yAxis: {
            title: {
                text: 'Number of Log Files Processed',
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
        },
        plotOptions: {
            line: {
                marker: {
                    enabled: false
                },
                dataLabels: {
                    enabled: false
                }
            }
        },
        tooltip: {
            formatter: function() {
                return Highcharts.dateFormat('%A, %b %e', new Date(this.x)) + '<br>' + this.series.name + ' : ' + this.y;
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Log Files',
            data: JSON.parse(data),
            pointInterval: tickInterval
        }]
    });
    //    $chart.exportChartLocal({ type: 'image/jpeg' });
}

function pieChart(data, title, name, id) {
    id = (id == undefined) ? '#containerPie' : id;
    title = (title == undefined) ? 'Transaction Details' : title;
    name = (name == undefined) ? 'Transactions' : name;
    $chart = $(id).highcharts({
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            zoomType: 'xy',
            height: 500,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: title
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        legend: {
            layout: 'vertical',
            align: 'right'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: name,
            colorByPoint: true,
            data: JSON.parse(data)
        }]
    });
}
/*function pieChart2(data , id) {
   var transaction_vs_transaction_type = [];
var transaction_vs_transaction_type2 = [];
var pieData =data;
pieData = JSON.parse(pieData);
 
$.each(pieData, function (key, value) {
transaction_vs_transaction_type.push(value.name);
transaction_vs_transaction_type.push(value.totalcount);
transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
transaction_vs_transaction_type = [];
});
  
if (transaction_vs_transaction_type2== "") {
      transaction_vs_transaction_type.push('No data Found');
transaction_vs_transaction_type.push(null); 
transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
transaction_vs_transaction_type = [];
  

} 
 
//alert(transaction_vs_transaction_type2);
var pie_chart = c3.generate({
bindto: id,
size: { width: 350 },
color: {
 pattern:graph_pastel_colour
},
data: {
 columns:
 transaction_vs_transaction_type2
 ,
 type : 'pie'
}

});
    
}*/

function pieChart2(data, id) {
    if (id == 'c3-pie-chart') {
        var tital = 'Transactions VS Transactions Type';
        var export_id = '#export4';
    } else if (id == 'c3-pie-chart2') {
        var tital = 'Transactions VS Branches';
        var export_id = '#export5';
    }
    var transaction_vs_transaction_type = [];
    var transaction_vs_transaction_type2 = [];

    var transaction_vs_transaction_type = ['Task', 'Hours per Day'];
    transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
    transaction_vs_transaction_type = [];
    var pieData = data;
    pieData = JSON.parse(pieData);

    $.each(pieData, function(key, value) {
        transaction_vs_transaction_type.push(value.name);
        transaction_vs_transaction_type.push(parseInt(value.totalcount));
        transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
        transaction_vs_transaction_type = [];
    });

    if (transaction_vs_transaction_type2 == "") {
        transaction_vs_transaction_type.push('No data Found');
        transaction_vs_transaction_type.push(null);
        transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
        transaction_vs_transaction_type = [];


    }
    var data = google.visualization.arrayToDataTable(transaction_vs_transaction_type2);

    var options = {
        title: tital,
        chartArea: {
            top: 120,
            width: "100%"
        },
        legend: {
            position: 'top',
            maxLines: 10
        },
        colors: graph_pastel_colour,
        'width': 450,
        'height': 350
    };

    var chart = new google.visualization.PieChart(document.getElementById(id));

    google.visualization.events.addListener(chart, 'ready', function() {
        var imgUri = chart.getImageURI();
        console.log(imgUri);
        $(export_id).attr({
            'href': imgUri,
            'download': tital
        });
    });
    chart.draw(data, options);
}

function pieChart_details(data, id) {
    var transaction_vs_transaction_type = [];
    var transaction_vs_transaction_type2 = [];
    var pieData = data;
    var transaction_vs_transaction_type = ['Task', 'Hours per Day'];
    transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
    transaction_vs_transaction_type = [];
    pieData = JSON.parse(pieData);
    $.each(pieData, function(key, value) {
        if (value.totalcount != null) {
            transaction_vs_transaction_type.push(value.name);
            transaction_vs_transaction_type.push(value.totalcount);
            transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
            transaction_vs_transaction_type = [];
        }
    });
    if (transaction_vs_transaction_type2[0][1] == null && transaction_vs_transaction_type2[1][1] == null) {
        transaction_vs_transaction_type2 = [];
        transaction_vs_transaction_type.push('No data');
        transaction_vs_transaction_type.push(null);
        transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
    }
    var data = google.visualization.arrayToDataTable(transaction_vs_transaction_type2);
    var options = {
        title: 'Number of Transactions',
        colors: graph_pastel_colour,
        'legend': 'bottom',
        'width': 450,
        'height': 350
    };

    var chart = new google.visualization.PieChart(document.getElementById(id));

    google.visualization.events.addListener(chart, 'ready', function() {
        var imgUri = chart.getImageURI();
        console.log(imgUri);
        $('#export2').attr({
            'href': imgUri,
            'download': 'Number of Transactions'
        });
    });
    chart.draw(data, options);
}

function getResion(compId) {

    jQuery.ajax({
      url: BaseUrl + "/company_branches/get_region/" + compId,
      type: 'post',
      success: function(response) {
    
        jQuery('#analyRegionId').html(response);
        jQuery('#analyBranchId').html('<option value="">Select All</option>');
        jQuery('#analyStationId').html('<option value="">Select All</option>');
      },
      error: function(e) {
    
      }
    });
    $("#daterange").val('');
        $('#analyticHeaderForm').submit();
    }
    
    function getBranches(compId) {
    if (compId == '') {
      jQuery('#analyBranchId').html('<option value="">Select All</option>');
      jQuery('#analyStationId').html('<option value="">Select All</option>');
      $('#analyticHeaderForm').submit();
      jQuery('#analyBranchId').val('');
      jQuery('#analyStationId').val('');
    } else {
      jQuery('#analyBranchId').val("");
      jQuery('#analyStationId').val("")
      jQuery.ajax({
        url: BaseUrl + "/company_branches/get_branches/" + compId,
        type: 'post',
        success: function(response) {
    
          jQuery('#analyBranchId').html(response);
          jQuery('#analyStationId').html('<option value="">Select All</option>');
        },
        error: function(e) {
    
        }
      });
      $("#daterange").val('');
        $('#analyticHeaderForm').submit();
    }
    }

    function getStations(branchId) {
        if (branchId == '') {
          jQuery('#analyStationId').html('<option value="">Select All</option>');
          $('#analyticHeaderForm').submit();
          jQuery('#analyStationId').val("")
        } else {
            jQuery('#analyStationId').val("")
          jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/" + branchId,
            type: 'post',
            data: {
              data: jQuery('#analyBranchId').val()
            },
            success: function(response) {
    
              jQuery('#analyStationId').html(response);
            },
            error: function(e) {
    
            }
          });
          $("#daterange").val('');
            $('#analyticHeaderForm').submit();
        }
      }
    function formSubmit(){
            // $("#daterange").val('');
            $('#analyticHeaderForm').submit();
        }
/*
function pieChart_details(data , id) {
   var transaction_vs_transaction_type = [];
var transaction_vs_transaction_type2 = [];
var pieData =data;

pieData = JSON.parse(pieData);
$.each(pieData, function (key, value) {
transaction_vs_transaction_type.push(value.name);
transaction_vs_transaction_type.push(value.totalcount);
transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
transaction_vs_transaction_type = [];
});
if(transaction_vs_transaction_type2[0][1]==null && transaction_vs_transaction_type2[1][1]==null){
      transaction_vs_transaction_type2 = [];
transaction_vs_transaction_type.push('No data');
transaction_vs_transaction_type.push(null);
transaction_vs_transaction_type2.push(transaction_vs_transaction_type);
} 

var pie_chart = c3.generate({
bindto: id,
size: { width: 350 },
color: {
 pattern: graph_pastel_colour
},
data: {
 columns:
 transaction_vs_transaction_type2
 ,
 type : 'pie'
},
    pie: {
        label: {
            format: function (value, ratio, id) {
                return d3.format(' ')(value);
            }
        }
    }

});

}*/
function lineChart(data, xAxisDates, tickInterval, options) {


    if (options == undefined) {
        options = {
            id: '#container',
            xTitle: 'X-Axis',
            yTitle: 'Y-Axis',
            title: 'Line Chart',
            name: 'Data'
        }
    }
    /*options.id = (options.id == undefined) ? '#container' : options.id;
     options.xTitle = (options.xTitle == undefined) ? 'X-Axis' : options.xTitle;
     options.yTitle = (options.yTitle == undefined) ? 'Y-Axis' : options.yTitle;
     options.title = (options.title == undefined) ? 'Line Chart' : options.title;
     options.name = (options.name == undefined) ? 'Data' : options.name;*/
    $chart = $(options.id).highcharts({
        rangeSelector: {
            selected: 1
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'line',
            zoomType: 'xy'
        },
        credits: {
            enabled: false
        },
        title: {
            text: options.title,
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            labels: {
                rotation: -45
            },
            title: {
                text: options.xTitle
            },
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
            }
        },
        yAxis: {
            title: {
                text: options.yTitle
            },
            plotLines: [{
                value: 0,
                width: 1
            }]
        },
        tooltip: {
            formatter: function() {

                return Highcharts.dateFormat('%A, %b %e', new Date(this.x)) + '<br>' + this.series.name + ' : ' + this.y;
                //                return  '%e<br/><b>' + this.series.name + '</b><br/>' +
                //                        Highcharts.dateFormat('%e - %b - %Y',
                //                                new Date(this.x))
                //                        + ' date, ' + this.y + ' Kg.';
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
                    enabled: false
                },
                dataLabels: {
                    enabled: false
                }
            },
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: [{
            name: options.name,
            data: JSON.parse(data),
            pointInterval: tickInterval
        }]
    });




}

function lineChart1(data, data1, withdraws_data, inventory_data, xAxisDates, tickInterval, options, options1, options2, options3) {


    if (options == undefined) {
        options = {
            id: '#container',
            xTitle: 'X-Axis',
            yTitle: 'Y-Axis',
            title: 'Line Chart',
            name: 'Data'
        }
    }
    var series1_color = 'blue';
var sereis2_color = 'red';
    /*options.id = (options.id == undefined) ? '#container' : options.id;
     options.xTitle = (options.xTitle == undefined) ? 'X-Axis' : options.xTitle;
     options.yTitle = (options.yTitle == undefined) ? 'Y-Axis' : options.yTitle;
     options.title = (options.title == undefined) ? 'Line Chart' : options.title;
     options.name = (options.name == undefined) ? 'Data' : options.name;*/
    $chart = $(options.id).highcharts({
        rangeSelector: {
            selected: 1
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            colors: [series1_color, sereis2_color],
            type: 'line',
            zoomType: 'xy'
        },
        credits: {
            enabled: false
        },
        title: {
            text: options.title,
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            labels: {
                rotation: -45
            },
            title: {
                text: options.xTitle
            },
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
            }
        },
        yAxis: {
            title: {
                text: options.yTitle
            },
            plotLines: [{
                value: 0,
                width: 1
            }]
        },
        tooltip: {
            formatter: function() {

                return Highcharts.dateFormat('%A, %b %e', new Date(this.x)) + '<br>' + this.series.name + ' : ' + this.y;
                //                return  '%e<br/><b>' + this.series.name + '</b><br/>' +
                //                        Highcharts.dateFormat('%e - %b - %Y',
                //                                new Date(this.x))
                //                        + ' date, ' + this.y + ' Kg.';
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
                    enabled: false
                },
                dataLabels: {
                    enabled: false
                }
            },
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: [{
            name: options.name,
            data: JSON.parse(data),
            color: '#79D1CF',
            pointInterval: tickInterval,
            zones: [ { color: '#79D1CF'}]
        },{
            name: options1.name,
            data: JSON.parse(data1),
            color: '#D9DD81',
            pointInterval: tickInterval,
            zones: [ { color: '#D9DD81'}]
        },{
            name: options2.name,
            data: JSON.parse(withdraws_data),
            color: '#E67A77',
            pointInterval: tickInterval,
            zones: [ { color: '#E67A77'}]
        },{
            name: options3.name,
            color: '#95D7BB',
            data: JSON.parse(inventory_data),
            pointInterval: tickInterval,
            zoneAxis: 'x',
            zones: [{color: '#95D7BB'}],
        }]
    });




}


function google_error_warning(data, xAxisDates, tickInterval, options) {
    drawColumn_error_warning(data, options.title);


}

function googleGraph(data, xAxisDates, tickInterval, options) {
    $('.chart').remove();
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var data2 = '<?php echo $temp; ?>';
    drawColumn(data, options.title);


}

function googleGraphhour(data, xAxisDates, tickInterval, options) {
    drawColumn4(data, options.title);
}

function googleGraphGroup(data, xAxisDates, tickInterval, options) {
    $('.chart').remove();
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var data2 = '<?php echo $temp; ?>';
    BarChart2(data, options.title);


}

function formatDate(date) {
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var d = new Date(date),
        month = '' + monthNames[(d.getMonth())],
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [month, day].join('-');
}

function formatDate_time(date) {
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var d = new Date(date),
        month = '' + monthNames[(d.getMonth())],
        day = '' + d.getDate(),
        year = d.getFullYear();
    var hour = d.getHours();
    if (hour > 12) {
        hour = "\n" + (hour - 12) + 'P.M.';
    } else {
        hour = "\n" + hour + 'A.M.';
    }
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [month, day, hour].join('-');
}

function drawBar(data) {
    var cars2 = [];

    var cars = ['Name', 'Number of Hardware Errors', {
        role: 'annotation'
    }];
    console.log(data);
    data = JSON.parse(data);
    cars2.push(cars);
    cars = [];
    $.each(data, function(key, value) {

        cars.push(value['HardwareErrorType']['name']);

        cars.push(parseInt(value[0]['total']));
        cars.push(parseInt(value[0]['total']));


        cars2.push(cars);

        cars = [];

    });
    // alert(cars2.length);
    if (cars2.length == 1) {
        cars.push('');
        cars.push(0);
        cars.push('');
        cars2.push(cars);
        cars = [];
    }
    // Data
    var data = google.visualization.arrayToDataTable(cars2);
    // Options

    var options_bar = {
        colors: graph_pastel_colour,
        title: 'Number of Hardware Errors',
        height: 350,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 245
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        vAxis: {
            gridlines: {
                color: '#e5e5e5',
                count: 10
            },
            minValue: 0
        },

        hAxis: {
            minValue: 4
        },

        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        }
    };
    // Draw chart
    //  document.getElementById("whereToPrint7").innerHTML = JSON.stringify(data, null, 4);
    var bar = new google.visualization.BarChart($('#google-bar')[0]);
    google.visualization.events.addListener(bar, 'ready', function() {
        var imgUri = bar.getImageURI();
        console.log(imgUri);
        $('#export').attr({
            'href': imgUri,
            'download': 'Number of Hardware Errors'
        });
    });
    bar.draw(data, options_bar);
}



function drawColumn_error_warning(data, tital) {


    var bill_adjustment = [];
    var cars2 = [];
    var cars3 = [];
    var cars = ['Year', 'Hight', 'Med', 'Low'];
    var tital2 = 'Station';
    data = JSON.parse(data);
    var intervall = parseInt(data.length / 7);
    var count = 1;
    var sum = 0;
    cars2.push(cars);
    cars = [];
    cars = [];
    $.each(data, function(key, value) {

        var flag = 0;
        for (var i = 0; i < cars2.length; i++) {
            if (cars2[i][0] == value['Ticket']['station']) {
                if (value['ErrorTypes']['severity'] == "High") {
                    cars2[i][1] = value[0]['total'];
                } else if (value['ErrorTypes']['severity'] == "Med") {
                    cars2[i][2] = value[0]['total'];
                } else if (value['ErrorTypes']['severity'] == "Low") {
                    cars2[i][3] = value[0]['total'];
                }
                flag = 1;
            }
        }
        if (flag == 0) {
            var station = value['Ticket']['station'] + '';
            if (value['ErrorTypes']['severity'] == "High") {
                cars = [station, value[0]['total'], 0, 0];
            } else if (value['ErrorTypes']['severity'] == "Med") {
                cars = [station, 0, value[0]['total'], 0];
            } else if (value['ErrorTypes']['severity'] == "Low") {
                cars = [station, 0, 0, value[0]['total']];
            }

            cars2.push(cars);
            cars = [];
        }
        flag = 0;
    });

    if (cars2.length == 1) {
        cars.push('');
        cars.push(0);
        cars.push(0);
        cars.push(0);
        cars2.push(cars);
        cars = [];
    }
    var largest = 0;
    var smallest = 0;
    for (var i = 0; i < cars2.length; i++) {
        if (cars2[i][1] > largest) {
            largest = cars2[i][1];
        }
        if (cars2[i][1] < smallest) {
            smallest = cars2[i][1];
        }
    }
    (largest > 0) ? largest++ : largest = -(smallest / 5);
    (smallest < 0) ? smallest-- : smallest = -(largest / 5);
    (largest < 5) ? largest = 4: '';
    (smallest < 5) ? smallest = -4: '';
    var data = google.visualization.arrayToDataTable(cars2);

    var options_column = {
        colors: graph_pastel_colour,
        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
        bold: true,
        hAxis: {
            minValue: 0
        },
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        vAxis: {
            viewWindow: {
                min: 0
            },

            title: tital,
            titleTextStyle: {
                fontSize: 12,
                bold: true,
                italic: false
            },
            gridlines: {
                color: '#e5e5e5',
                count: 10
            },

        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                bold: true,
                fontSize: 12
            }
        }
    };

    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    google.visualization.events.addListener(column, 'ready', function() {
        var imgUri = column.getImageURI();
        console.log(imgUri);
        $('#export').attr({
            'href': imgUri,
            'download': 'Error Warning'
        });
    });
    column.draw(data, options_column);
}

function BarChart2(data, tital) {
    var bill_adjustment = [];
    var cars2 = [];
    var cars3 = [];
    var cars = ['Date', 'Debit', 'Credit'];
    var tital2 = 'No. of Transaction';
    var data = JSON.parse(data);
    cars2.push(cars);
    cars = [];
    $.each(data, function(key, value) {

        var date = value[0] / 1000;
        var data = {
            "date_created": date
        };
        var date = new Date(parseInt(data.date_created, 10) * 1000);
        var m = formatDate(date);
        cars.push(value[0] + ' ');

        if (value[1] == null) {
            cars.push(0);
        } else {
            cars.push(value[1]);
        }

        if (value[2] == null) {
            cars.push(0);
        } else {
            cars.push(value[2]);
        }

        cars2.push(cars);

        cars = [];

    });
    if (cars2.length == 1) {
        cars.push('');
        cars.push(0);
        cars.push(0);
        cars2.push(cars);
        cars = [];
    }
    var data = google.visualization.arrayToDataTable(cars2);
    // var graph_colour=['#88bde0','#a7dbd2','#91d0b6','#88bdab','#f8f491','#dfea8d','#cdab90','#f8b3ba','#f9a586','#fabf7d','#b8b3dd','#c0c0c0']; 
    var options = {
        title: 'Teller Activity Report',
        height: ((cars2.length) * 35),

        width: 1000,
        chartArea: {
            width: '80%'
        },
        bar: {
            groupWidth: "80%"
        },
        legend: {
            position: 'top',
            alignment: 'center'
        },
        hAxis: {
            title: 'Teller Activity Report',
            minValue: 0,
            textStyle: {
                bold: true,
                fontSize: 12,
                color: '#4d4d4d'
            },
            titleTextStyle: {
                bold: true,
                fontSize: 10,
                color: '#4d4d4d'
            }
        },
        colors: ['#ff6961', '#333333'],
        vAxis: {
            title: 'Teller Activity',
            textStyle: {
                fontSize: 12,
                bold: true,
                color: '#4d4d4d'
            },
            titleTextStyle: {
                fontSize: 12,
                bold: true,
                color: '#4d4d4d'
            },
            viewWindow: {
                min: 0
            }
        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
            viewWindow: {
                min: 0
            }
        },

    };
    // var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
    var chart = new google.visualization.BarChart($('#google-column')[0]);
    google.visualization.events.addListener(chart, 'ready', function() {
        var imgUri = chart.getImageURI();
        console.log(imgUri);
        $('#export').attr({
            'href': imgUri,
            'download': 'Teller Activity Report'
        });
    });
    chart.draw(data, options);


}
/*function drawColumn2(data , tital ) {

  
  var bill_adjustment = [];
  var cars2=[];
  var cars3=[]; 
  var cars=['Year','Debit','Credit'];
  
    var data = JSON.parse(data);
   cars2.push(cars);
        cars = [];      
    $.each(data, function (key, value) {
        var date=value[0]/1000;
    var data = {"date_created":date};
var date = new Date(parseInt(data.date_created, 10) * 1000);
 var m=formatDate(date); 
        cars.push(m);
        cars.push(value[1]);
        cars.push(value[2]);
          cars2.push(cars);

        cars = [];
         
    });
   if(cars2.length==1){
            cars.push('');
            cars.push(0);
            cars.push(0); 
            cars2.push(cars);
            cars = [];
  }
  var largest = 0;
    var smallest=0; 
      for(var i = 0; i < cars2.length; i++){ 
          if((cars2[i][1]+cars2[i][2]) > largest){
               largest = cars2[i][1]+cars2[i][2];
            }
            if((cars2[i][1]+cars2[i][2]) < smallest){
              smallest = (cars2[i][1]+cars2[i][2]);
              
            }
            if(cars2[i][1]<smallest){
                smallest = cars2[i][1]; 
            }
            if(cars2[i][2]<smallest){
                smallest = cars2[i][2]; 
            }
    }
(largest>0)?largest-=(smallest/5) : largest=-(smallest/5); 
(smallest<0)?smallest-=(largest/5) : smallest=-(largest/5);
 
    var data = google.visualization.arrayToDataTable(cars2);
    
    var options_column = {

        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
         bold: true,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        vAxis: {
            title: tital,
            titleTextStyle: {
                fontSize: 11,
                bold: true,
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: { 
              min:0
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12,
                 bold: true,
            }
        },
        isStacked: true
    };
 
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
      var column = new google.visualization.ColumnChart($('#google-column')[0]); 
google.visualization.events.addListener(column, 'ready', function () {
    var imgUri = column.getImageURI();
    console.log(imgUri);
    $('#export').attr({ 'href': imgUri, 'download': 'Teller Activity'});
});
    column.draw(data, options_column);
}*/
function drawColumn4(data, tital) {

    var bill_adjustment = [];
    var cars2 = [];
    var cars3 = [];
    var cars = ['Year', 'No of Transaction'];
    var tital2 = 'Hour';
    data = JSON.parse(data);


    cars2.push(cars);
    cars = [];
    $.each(data, function(key, value) {
        cars.push(value[0] + '');
        cars.push(value[1]);
        cars2.push(cars);
        cars = [];


    });


    var largest = 0;
    var smallest = 0;
    for (var i = 0; i < cars2.length; i++) {
        if (cars2[i][1] > largest) {
            largest = cars2[i][1];
        }
        if (cars2[i][1] < smallest) {
            smallest = cars2[i][1];
        }
    }
    (largest > 0) ? largest++ : largest = -(smallest / 5);
    (smallest < 0) ? smallest-- : smallest = -(largest / 5);
    (largest < 5) ? largest = 4: '';
    (smallest < 5) ? smallest = -4: '';

    var data = google.visualization.arrayToDataTable(cars2);

    var options_column = {
        colors: graph_pastel_colour,
        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                bold: true,
                fontSize: 11
            }
        },

        vAxis: {
            title: tital,
            titleTextStyle: {
                fontSize: 11,
                bold: true,
                italic: false
            },
            gridlines: {
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
                min: 0,
                ticks: [0, .3, .6, .9, 1]
            }
        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                bold: true,
                fontSize: 12
            }
        }
    };


    var column = new google.visualization.ColumnChart($('#google-column3')[0]);
    google.visualization.events.addListener(column, 'ready', function() {
        var imgUri = column.getImageURI();
        console.log(imgUri);
        $('#export3').attr({
            'href': imgUri,
            'download': 'Transactions Report'
        });
    });
    column.draw(data, options_column);
}

function drawColumn(data, tital) {


    //  alert(data);
    var bill_adjustment = [];
    var cars2 = [];
    var cars3 = [];
    var cars = ['Year', 'No of Bill Adjustment'];
    var tital2 = 'Date';
    console.log(data);

    data = JSON.parse(data);
    var intervall = parseInt(data.length / 7);
    var count = 1;
    var sum = 0;

    cars2.push(cars);
    cars = [];
    $.each(data, function(key, value) {
        if (count == intervall) {

            var date = value[0] / 1000;
            var data = {
                "date_created": date
            };
            var date = new Date(parseInt(data.date_created, 10) * 1000);
            var m = formatDate(date);
            cars.push(m);
            sum = sum + value[1];
            cars.push(sum);
            cars2.push(cars);
            cars = [];


            count = 1;
            sum = 0;
        } else {
            sum = sum + value[1];
            count++
        }




    });


    var largest = 0;
    var smallest = 0;
    for (var i = 0; i < cars2.length; i++) {
        if (cars2[i][1] > largest) {
            largest = cars2[i][1];
        }
        if (cars2[i][1] < smallest) {
            smallest = cars2[i][1];
        }
    }
    (largest > 0) ? largest++ : largest = -(smallest / 5);
    (smallest < 0) ? smallest-- : smallest = -(largest / 5);
    (largest < 5) ? largest = 4: '';
    (smallest < 5) ? smallest = -4: '';
    var data = google.visualization.arrayToDataTable(cars2);

    var options_column = {
        colors: graph_pastel_colour,
        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11,

            }
        },
        vAxis: {
            title: tital,
            titleTextStyle: {
                fontSize: 11,
                bold: true,
                italic: false
            },
            gridlines: {
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
                min: 0,
                ticks: [0, .3, .6, .9, 1]
            }
        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12,
                bold: true
            }
        }
    };

    /* var column = new google.visualization.ColumnChart($('#google-column')[0]);
     column.draw(data, options_column);*/
    $('#google-column').remove();
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    google.visualization.events.addListener(column, 'ready', function() {
        var imgUri = column.getImageURI();
        console.log(imgUri);
        $('#export').attr({
            'href': imgUri,
            'download': 'Bill Adjustment'
        });
    });
    column.draw(data, options_column);
}

function graph_inventory(data, tital) 
{
    var denom = ['1','2','5','10','20','50','100'];
    var dispance_bill = [];
    var op_cassette = [];
    var reject = [];
    var total_inventory = [];
    console.log("in graph_inventory"+tital);
    console.log(data)
    if (tital == 'Total_Inventory') {
        /*$('#google-column4').remove();
        $('#chart-div4').append("<div class='chart'  id='google-column4'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column4')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export4').attr({
                'href': imgUri,
                'download': 'Total Inventory'
            });
        });*/

        if (data.Total_inventory_$1 != '' || data.Total_inventory_$1 != 'undefined' || data.Total_inventory_$1 != 'null') {
            total_inventory.push(data.Total_inventory_$1);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$2 != '' || data.Total_inventory_$2 != 'undefined' || data.Total_inventory_$2 != 'null') {
            total_inventory.push(data.Total_inventory_$2);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$5 != '' || data.Total_inventory_$5 != 'undefined' || data.Total_inventory_$5 != 'null') {
            total_inventory.push(data.Total_inventory_$5);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$10 != '' || data.Total_inventory_$10 != 'undefined' || data.Total_inventory_$10 != 'null') {
            total_inventory.push(data.Total_inventory_$10);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$20 != '' || data.Total_inventory_$20 != 'undefined' || data.Total_inventory_$20 != 'null') {
            total_inventory.push(data.Total_inventory_$20);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$50 != '' || data.Total_inventory_$50 != 'undefined' || data.Total_inventory_$50 != 'null') {
            total_inventory.push(data.Total_inventory_$50);
        }else{
            total_inventory.push(0);
        }
        if (data.Total_inventory_$100 != '' || data.Total_inventory_$100 != 'undefined' || data.Total_inventory_$100 != 'null') {
            total_inventory.push(data.Total_inventory_$100);
        }else{
            total_inventory.push(0);
        }

        total_inventory.reverse();
        Highcharts.setOptions({
            lang: {
              numericSymbols: ["K", "M", "G", "T", "P", "E"],
              thousandsSep: ','
            }
        });
        Highcharts.chart('chart-div4', {
            colors: ['#f28f43', '#492970', '#1aadce', '#910000', '#8bbc21', '#0d233a', '#2f7ed8'],
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['$100','$50','$20','$10','$5','$2','$1'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Inventory'
                },
                labels: {
                    formatter: function() {
                      if (this.value >= 1E6) {
                        return '$' + this.value / 1000000 + 'M';
                      }
                      return '$' + this.value / 1000 + 'K';
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                },
                column: {
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Denomination',
                data: total_inventory
            }]
        });
    } else if (tital == 'Reject') {
        /*$('#google-column3').remove();
        $('#chart-div3').append("<div class='chart'  id='google-column3'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column3')[0]);
        var column = new google.visualization.ColumnChart($('#google-column3')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export3').attr({
                'href': imgUri,
                'download': 'Reject'
            });
        });*/
        if (data.Reject_$1 != '' || data.Reject_$1 != 'undefined' || data.Reject_$1 != 'null') {
            reject.push(data.Reject_$1);
        }else{
            reject.push(0);
        }
        if (data.Reject_$2 != '' || data.Reject_$2 != 'undefined' || data.Reject_$2 != 'null') {
            reject.push(data.Reject_$2);
        }else{
            reject.push(0);
        }
        if (data.Reject_$5 != '' || data.Reject_$5 != 'undefined' || data.Reject_$5 != 'null') {
            reject.push(data.Reject_$5);
        }else{
            reject.push(0);
        }
        if (data.Reject_$10 != '' || data.Reject_$10 != 'undefined' || data.Reject_$10 != 'null') {
            reject.push(data.Reject_$10);
        }else{
            reject.push(0);
        }
        if (data.Reject_$20 != '' || data.Reject_$20 != 'undefined' || data.Reject_$20 != 'null') {
            reject.push(data.Reject_$20);
        }else{
            reject.push(0);
        }
        if (data.Reject_$50 != '' || data.Reject_$50 != 'undefined' || data.Reject_$50 != 'null') {
            reject.push(data.Reject_$50);
        }else{
            reject.push(0);
        }
        if (data.Reject_$100 != '' || data.Reject_$100 != 'undefined' || data.Reject_$100 != 'null') {
            reject.push(data.Reject_$100);
        }else{
            reject.push(0);
        }

        reject.reverse();
        Highcharts.setOptions({
            lang: {
              numericSymbols: ["K", "M", "G", "T", "P", "E"],
              thousandsSep: ','
            }
        });
        Highcharts.chart('chart-div3', {
            colors: ['#f28f43', '#492970', '#1aadce', '#910000', '#8bbc21', '#0d233a', '#2f7ed8'],
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['$100','$50','$20','$10','$5','$2','$1'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Reject Cassette'
                },
                labels: {
                    formatter: function() {
                      if (this.value >= 1E6) {
                        return '$' + this.value / 1000000 + 'M';
                      }
                      return '$' + this.value / 1000 + 'K';
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                },
                column: {
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Denomination',
                data: reject
            }]
        });
    } else if (tital == 'Operation_Cassette') {
        /*$('#google-column2').remove();
        $('#chart-div2').append("<div class='chart'  id='google-column2'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column2')[0]);
        var column = new google.visualization.ColumnChart($('#google-column2')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export2').attr({
                'href': imgUri,
                'download': 'Operation Cassette'
            });
        });*/

        if (data.op_cassette_$1 != '' || data.op_cassette_$1 != 'undefined' || data.op_cassette_$1 != 'null') {
            op_cassette.push(data.op_cassette_$1);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$2 != '' || data.op_cassette_$2 != 'undefined' || data.op_cassette_$2 != 'null') {
            op_cassette.push(data.op_cassette_$2);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$5 != '' || data.op_cassette_$5 != 'undefined' || data.op_cassette_$5 != 'null') {
            op_cassette.push(data.op_cassette_$5);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$10 != '' || data.op_cassette_$10 != 'undefined' || data.op_cassette_$10 != 'null') {
            op_cassette.push(data.op_cassette_$10);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$20 != '' || data.op_cassette_$20 != 'undefined' || data.op_cassette_$20 != 'null') {
            op_cassette.push(data.op_cassette_$20);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$50 != '' || data.op_cassette_$50 != 'undefined' || data.op_cassette_$50 != 'null') {
            op_cassette.push(data.op_cassette_$50);
        }else{
            op_cassette.push(0);
        }
        if (data.op_cassette_$100 != '' || data.op_cassette_$100 != 'undefined' || data.op_cassette_$100 != 'null') {
            op_cassette.push(data.op_cassette_$100);
        }else{
            op_cassette.push(0);
        }

        op_cassette.reverse();
        Highcharts.setOptions({
            lang: {
              numericSymbols: ["K", "M", "G", "T", "P", "E"],
              thousandsSep: ','
            }
        });
        Highcharts.chart('chart-div2', {
            colors: ['#f28f43', '#492970', '#1aadce', '#910000', '#8bbc21', '#0d233a', '#2f7ed8'],
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['$100','$50','$20','$10','$5','$2','$1'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Operation Cassette'
                },
                labels: {
                    formatter: function() {
                      if (this.value >= 1E6) {
                        return '$' + this.value / 1000000 + 'M';
                      }
                      return '$' + this.value / 1000 + 'K';
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                },
                column: {
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Denomination',
                data: op_cassette
            }]
        });
    } else if (tital == 'Dispense_Bill') {
        $('#google-column').remove();
        $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
        //var column = new google.visualization.ColumnChart($('#google-column')[0]);
        /*var column = new google.visualization.ColumnChart($('#google-column')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export').attr({
                'href': imgUri,
                'download': 'Dispense Bill'
            });
        });*/

        if (data.dispance_bill_$1 != '' || data.dispance_bill_$1 != 'undefined' || data.dispance_bill_$1 != 'null') {
            dispance_bill.push(data.dispance_bill_$1);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$2 != '' || data.dispance_bill_$2 != 'undefined' || data.dispance_bill_$2 != 'null') {
            dispance_bill.push(data.dispance_bill_$2);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$5 != '' || data.dispance_bill_$5 != 'undefined' || data.dispance_bill_$5 != 'null') {
            dispance_bill.push(data.dispance_bill_$5);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$10 != '' || data.dispance_bill_$10 != 'undefined' || data.dispance_bill_$10 != 'null') {
            dispance_bill.push(data.dispance_bill_$10);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$20 != '' || data.dispance_bill_$20 != 'undefined' || data.dispance_bill_$20 != 'null') {
            dispance_bill.push(data.dispance_bill_$20);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$50 != '' || data.dispance_bill_$50 != 'undefined' || data.dispance_bill_$50 != 'null') {
            dispance_bill.push(data.dispance_bill_$50);
        }else{
            dispance_bill.push(0);
        }
        if (data.dispance_bill_$100 != '' || data.dispance_bill_$100 != 'undefined' || data.dispance_bill_$100 != 'null') {
            dispance_bill.push(data.dispance_bill_$100);
        }else{
            dispance_bill.push(0);
        }

        dispance_bill.reverse();
        Highcharts.setOptions({
            lang: {
              numericSymbols: ["K", "M", "G", "T", "P", "E"],
              thousandsSep: ','
            }
        });
        Highcharts.chart('chart-div', {
            colors: ['#f28f43', '#492970', '#1aadce', '#910000', '#8bbc21', '#0d233a', '#2f7ed8'],
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['$100','$50','$20','$10','$5','$2','$1'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Dispensable Cassette'
                },
                labels: {
                    formatter: function() {
                      if (this.value >= 1E6) {
                        return '$' + this.value / 1000000 + 'M';
                      }
                      return '$' + this.value / 1000 + 'K';
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.1,
                    borderWidth: 0
                },
                column: {
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Denomination',
                data: dispance_bill

            }]
        });        
    }
}

function graph_inventory1(data, tital) {
    var x_details = [];
    var cars = [];
    var cars2 = [];
    var seriesOptions = [];
    var cars3 = [];
    //data = JSON.parse(data);
    var tital2 = 'Denom';

    var i = 1;
    var data_date;
    var count;
    tital = tital.replace('_', " ");

    cars.push(tital);
    x_details.push("Year");
    var key_temp = ''; // str.replace("Microsoft", "W3Schools");
    //cars2.push(x_details);
    //x_details.push(tital);
    $.each(data, function(key, value) {
        key_temp = key.split("$");
        x_details.push('$' + key_temp[1]);
        cars.push(value);

    });
    cars2.push(cars);
    cars = [];
    cars2.splice(0, 0, x_details);
    // document.getElementById("whereToPrint2").innerHTML = JSON.stringify(cars2, null, 4);
    var largest = 0;
    var smallest = 0;
    var sum;
    for (var i = 0; i < cars2.length; i++) {
        sum = 0
        for (var j = 1; j < cars2[i].length; j++) {
            sum = cars2[i][j];
            if ((sum) > largest) {
                largest = sum;
            }
            if ((sum) < smallest) {
                smallest = sum;
            }
        }
    }
    (largest > 0) ? largest -= (smallest / 5): largest = -(smallest / 5);
    (smallest < 0) ? smallest -= (largest / 5): smallest = -(largest / 5);
    largest = largest * 1.2;
    smallest = smallest * 1.2;
    (largest < 5) ? largest = 4: '';
    (smallest > 0 && smallest < 5) ? smallest = -4: '';

    var data = google.visualization.arrayToDataTable(cars2[0].map((col, i) => cars2.map(row => row[i])));
    var graph_colour = [];
    if (tital == 'Total Inventory') {
        var graph_colour = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce','#492970', '#f28f43'];
    } else if (tital == 'Reject Cassette') {
        var graph_colour = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce','#492970', '#f28f43'];
    } else if (tital == 'Operation Cassette') {
        var graph_colour = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce','#492970', '#f28f43'];
    } else if (tital == 'Dispensable Cassette') {
        var graph_colour = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce','#492970', '#f28f43'];
    }
    var options_column = {
        vAxis: {
            title: tital,
            titleTextStyle: {
                fontSize: 11,
                bold: true,
                italic: false
            }
        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
        },

        fontName: 'tahoma',
        colors: graph_colour,
        height: 300,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12,
                bold: true
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            },
            column: {
                colorByPoint: true
            }
        }
    };

    if (tital == 'Total Inventory') {
        $('#google-column4').remove();
        $('#chart-div4').append("<div class='chart'  id='google-column4'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column4')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export4').attr({
                'href': imgUri,
                'download': 'Total Inventory'
            });
        });

        column.draw(data, options_column);
    } else if (tital == 'Reject') {
        $('#google-column3').remove();
        $('#chart-div3').append("<div class='chart'  id='google-column3'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column3')[0]);
        var column = new google.visualization.ColumnChart($('#google-column3')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export3').attr({
                'href': imgUri,
                'download': 'Reject'
            });
        });
        column.draw(data, options_column);
    } else if (tital == 'Operation Cassette') {
        $('#google-column2').remove();
        $('#chart-div2').append("<div class='chart'  id='google-column2'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column2')[0]);
        var column = new google.visualization.ColumnChart($('#google-column2')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export2').attr({
                'href': imgUri,
                'download': 'Operation Cassette'
            });
        });
        column.draw(data, options_column);
    } else if (tital == 'Dispensable Cassette') {
        $('#google-column').remove();
        $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
        var column = new google.visualization.ColumnChart($('#google-column')[0]);
        var column = new google.visualization.ColumnChart($('#google-column')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export').attr({
                'href': imgUri,
                'download': 'Dispensable Cassette'
            });
        });
        column.draw(data, options_column);
    }

}

function drawColumn3(data, tital) {

    var x_details = [];
    var cars = [];
    var cars2 = [];


    var seriesOptions = [];
    var intervall = 1;
    data = JSON.parse(data);
    var i = 1;

    var data_date;
    var count;
    if (tital == 'Transactions') {
        var tital2 = 'Denom';
        var graph_pastel_colour_new = ['#ff6961', '#333333'];
        $.each(data, function(key, value) {
            seriesOptions[key] = {
                name: "sharad",
                data: JSON.parse(value.data)
            }
            count = 1;
            //data_date = JSON.parse(value.data);
            var pointInterval = value.pointInterval;
            intervall = pointInterval;
            x_details = [];
            x_details.push("Year");
            cars.push(value.name);
            $.each(JSON.parse(value.data), function(key, value) {
                var date = value[0] / 1000;
                var data = {
                    "date_created": date
                };
                var date = new Date(parseInt(data.date_created, 10) * 1000);
                var m = formatDate(date);
                x_details.push(m);
                cars.push(value[1]);
            });
            cars2.push(cars);
            cars = [];
        });
    } else {
        var tital2 = 'Time';
        var graph_pastel_colour_new = graph_pastel_colour;
        $.each(data, function(key, value) {
            seriesOptions[key] = {
                name: value.name,
                data: JSON.parse(value.data)
            }

            //data_date = JSON.parse(value.data);
            x_details = [];

            x_details.push("Year");
            cars.push(value.name);
            $.each(JSON.parse(value.data), function(key, value) {
                cars.push(value[1]);
                var date = value[0] / 1000;
                var data = {
                    "date_created": date
                };
                var date = new Date(parseInt(data.date_created, 10) * 1000);
                var m = formatDate_time(date);
                x_details.push(m);
            });
            cars2.push(cars);

            cars = [];
        });
    }


    cars2.splice(0, 0, x_details);
    var largest = 0;
    var smallest = 0;
    var sum;
    for (var i = 0; i < cars2.length; i++) {
        sum = 0
        for (var j = 1; j < cars2[i].length; j++) {
            sum = cars2[i][j];
            if ((sum) > largest) {
                largest = sum;
            }
            if ((sum) < smallest) {
                smallest = sum;
            }
        }
    }
    (largest > 0) ? largest -= (smallest / 5): largest = -(smallest / 5);
    (smallest < 0) ? smallest -= (largest / 5): smallest = -(largest / 5);
    largest = largest * 1.2;
    smallest = smallest * 1.2;
    (largest < 5) ? largest = 4: '';
    (smallest > 0 && smallest < 5) ? smallest = -4: '';

    var data = google.visualization.arrayToDataTable(cars2[0].map((col, i) => cars2.map(row => row[i])));

    var options_column = {
        colors: graph_pastel_colour_new,
        fontName: 'tahoma',
        height: 300,
        fontSize: 12,
        chartArea: {
            left: '10%',
            width: '100%',
            height: 225
        },
        tooltip: {
            textStyle: {
                fontName: 'tahoma',
                fontSize: 11
            }
        },
        vAxis: {
            title: tital,
            titleTextStyle: {
                fontSize: 11,
                bold: true,
                italic: false
            },
            gridlines: {
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
                min: 0
            }
        },
        hAxis: {
            title: tital2,
            titleTextStyle: {
                fontSize: 13,
                bold: true,
                italic: false
            },
            showTextEvery: intervall,
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                bold: true,
                fontSize: 12
            }
        }
    };

    if (tital == 'Transactions') {


        var column = new google.visualization.ColumnChart($('#google-column')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export').attr({
                'href': imgUri,
                'download': 'Transactions'
            });
        });
        column.draw(data, options_column);
    } else {

        var column = new google.visualization.ColumnChart($('#google-column2')[0]);
        google.visualization.events.addListener(column, 'ready', function() {
            var imgUri = column.getImageURI();
            console.log(imgUri);
            $('#export2').attr({
                'href': imgUri,
                'download': 'Transactions Denom'
            });
        });
        column.draw(data, options_column);
    }
}




function multiLineChart2(data, xAxisDates, tickInterval, options) {


    if (options.title == 'Transactions') {

        $('google-column').remove();
    }



    // $('.chart').remove();

    /* $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
     $('#chart-div2').append("<div class='chart'  id='google-column2'><span></span></div>");*/


    drawColumn3(data, options.title);



}

function multiLineChart3(chartdata) 
{
    /*$('#google-column').remove();
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    google.visualization.events.addListener(column, 'ready', function() {
        var imgUri = column.getImageURI();
        console.log(imgUri);
        $('#export').attr({
            'href': imgUri,
            'download': 'D'
        });
    });*/
    var chartdata = JSON.parse(chartdata);

    Highcharts.chart('chart-div', {
        colors: ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce','#492970', '#f28f43'],
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: [
                '$1',
                '$2',
                '$5',
                '$10',
                '$20',
                '$50',
                '$100'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Note Count Amount'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            },
            column: {
                colorByPoint: true
            }
        },
        series: [{
            name: 'Notes',
            data: chartdata

        }]
    });
}



function multiLineChart(data, xAxisDates, tickInterval, options) {

    var seriesOptions = [];
    data = JSON.parse(data);
    $.each(data, function(key, value) {
        seriesOptions[key] = {
            name: value.name,
            data: JSON.parse(value.data)
        }
    });
    if (options == undefined) {
        options = {
            id: '#container',
            xTitle: 'Transaction Date',
            yTitle: 'Transaction',
            title: 'Transaction',
            name: 'Data'
        }
    }

    $chart = $(options.id).highcharts({
        rangeSelector: {
            selected: 1
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'line',
            zoomType: 'xy',
        },
        credits: {
            enabled: false
        },
        title: {
            text: options.title,
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            labels: {
                rotation: -45,
                /*formatter: function () {
                    return Highcharts.dateFormat('%b-%d-%Y %I:%M %p', this.value);
               }*/
            },
            title: {
                text: options.xTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    console.log(this.value);
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
            //           tickInterval: 1.5 * 3600 * 1000,

            dateTimeLabelFormats: {
                day: '%b-%d-%Y',
                hour: '%I:%M %p'
            }
        },

        yAxis: {
            title: {
                text: options.yTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
            plotLines: [{
                value: 0,
                width: 1,
            }]
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            //            verticalAlign: 'right',
            borderWidth: 0
        },
        tooltip: {
            formatter: function() {
                return Highcharts.dateFormat('%A, %b %e', new Date(this.x)) + '<br>' + this.series.name + ' : ' + this.y;
            }
        },
        plotOptions: {
            series: {
                compare: 'percent'
            },
            line: {
                compare: 'percent',
                marker: {
                    enabled: false
                },
                dataLabels: {
                    enabled: false
                }
            },
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: seriesOptions
    });
}

function inventoryHoursMultiLineChart(data, xAxisDates, tickInterval, options) {

    var seriesOptions = [];
    data = JSON.parse(data);
    $.each(data, function(key, value) {
        seriesOptions[key] = {
            name: value.name,
            data: JSON.parse(value.data)
        }
    });
    if (options == undefined) {
        options = {
            id: '#container',
            xTitle: 'Transaction Date',
            yTitle: 'Transaction',
            title: 'Transaction',
            name: 'Data'
        }
    }

    $chart = $(options.id).highcharts({
        rangeSelector: {
            selected: 1
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'line',
            zoomType: 'xy',
        },
        credits: {
            enabled: false
        },
        title: {
            text: options.title,
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            labels: {
                rotation: -45,
                /*formatter: function () {
                    return Highcharts.dateFormat('%b-%d-%Y %I:%M %p', this.value);
               }*/
            },
            title: {
                text: options.xTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    console.log(this.value);
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },

            tickInterval: 14 * 3600 * 1000,
            dateTimeLabelFormats: {
                day: '%b-%d-%Y',
                hour: '%I:%M %p'
            }
        },

        yAxis: {
            title: {
                text: options.yTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function() {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
            plotLines: [{
                value: 0,
                width: 1,
            }]
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            //            verticalAlign: 'right',
            borderWidth: 0
        },
        tooltip: {
            formatter: function() {
                return Highcharts.dateFormat('%A, %b %e', new Date(this.x)) + '<br>' + this.series.name + ' : ' + this.y;
            }
        },
        plotOptions: {
            series: {
                compare: 'percent'
            },
            line: {
                compare: 'percent',
                marker: {
                    enabled: false
                },
                dataLabels: {
                    enabled: false
                }
            },
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: seriesOptions
    });
}

function barChart(data, xAxisDates, tickInterval, options) {
    var seriesOptions = [];
    data = JSON.parse(data);
    $.each(data, function(key, value) {
        seriesOptions[key] = {
            name: value.name,
            data: JSON.parse(value.data)
        }
    });
    if (options == undefined) {
        options = {
            id: '#container',
            xTitle: 'X-Axis',
            yTitle: 'Y-Axis',
            title: 'Bar Chart',
            name: 'Data'
        }
    }
    xAxisDates = JSON.parse(xAxisDates);
    $chart = $(options.id).highcharts({
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'bar',
            zoomType: 'xy',
        },
        title: {
            text: options.title
        },
        xAxis: {
            categories: xAxisDates,
            title: {
                text: options.xTitle
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: options.yTitle
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' hours'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: seriesOptions
    });
}

function transactionDetailChart(data, xAxisDates, tickInterval) {
    $chart = $('#container').highcharts({
        rangeSelector: {
            selected: 1
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            height: 500,
            type: 'line',
            zoomType: 'xy',
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Transaction Details',
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            labels: {
                rotation: -45,
            },
            title: {
                text: 'Transaction Date'
            },
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
            },
            //            tickInterval: tickInterval,
            //            categories: JSON.parse(xAxisDates)
        },
        yAxis: {
            title: {
                text: 'No. of Transactions'
            },
            plotLines: [{
                value: 0,
                width: 1,
            }]
            //                categories: JSON.parse(yAxisData)
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
                    enabled: false
                },
                dataLabels: {
                    enabled: false
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
            pointInterval: tickInterval
        }]
    });

}

function transactionDetailPieChart(data) {

    $chart = $('#containerPie').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            height: 500,
            type: 'pie'
        },
        lang: {
            noData: 'No Data Available'
        },
        credits: {
            enabled: false
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
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: 'Transactions',
            colorByPoint: true,
            data: JSON.parse(data)
        }]
    });
}

function addDateRange(startDate, endDate, action, graphName, pieGraphName, extraParams) {
    if (pieGraphName == undefined || pieGraphName == '') {
        pieGraphName = '';
    }
    $('.daterange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Last 10 Days': [moment().subtract('days', 10), moment()],
                'Current Month': [moment().subtract('days', 17), moment()],
                'Last Month': [moment().subtract('month', 1), moment()],
                // 'Current Month': [moment().subtract('days', 15), moment()],
                'Quarter to Date': [moment().subtract('month', 3), moment()],
                'Year to Date': [moment().subtract('month', 12), moment()],
                'All Dates':[moment().subtract('year', 10), moment()],
                //            'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                //            'Last 3 Month': [moment().subtract('month', 3).startOf('month'), moment().subtract('month', 1).endOf('month')],
                //            'Last 6 Month': [moment().subtract('month', 6).startOf('month'), moment().subtract('month', 1).endOf('month')],
            },
            startDate: startDate,
            endDate: endDate
        },
        function(start, end) {
            if (action == 'dashboardData') {
                changeDashboardData(start, end);
            } else {
                resetGraph(start, end, action, graphName, pieGraphName, extraParams);
                // window.location(BaseUrl+action);
                window.location.reload();
            }

        });
}

function changeDashboardData(start, end) {
    var clickedLabel = getSelectedRange(start, end);
    console.log(clickedLabel);
    var formData = {
        'Filter': {
            'start_date': start.format('YYYY-MM-DD'),
            'end_date': end.format('YYYY-MM-DD'),
            'from': clickedLabel.key
        }
    };
    jQuery.ajax({
        url: BaseUrl + 'users/dashboard',
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function(response) {

            jQuery('.daterange span').html(clickedLabel.title);
            jQuery('.graphTitle').html(clickedLabel.title);
            totalTrans = parseInt(response.transactions.totalTrans);
            jQuery('.transactions').find('.info-box-number').html(totalTrans);

            //                    jQuery('.clients').attr('href', response.clients.url);
            totalClients = parseInt(response.clients.totalClients);
            jQuery('.clients').find('.info-box-number').html(totalClients);

            //                    jQuery('.errors').attr('href', response.errors.url);
            totalErros = parseInt(response.errors.totalErros);
            jQuery('.errors').find('.info-box-number').html(totalErros);

            //                    jQuery('.messages').attr('href', response.messages.url);
            totalUnIdentiMsg = parseInt(response.messages.totalUnIdentiMsg);
            jQuery('.messages').find('.info-box-number').html(totalUnIdentiMsg);

            //                    jQuery('.procesedFiles').attr('href', response.errors.url);
            totalPFiles = parseInt(response.files.totalPFiles);
            jQuery('.procesedFiles').find('.info-box-number').html(totalPFiles);

            var displText = jQuery('.ranges li.active').html();
            console.log(displText);
            if (displText == 'Custom Range') {
                displText = start.format('YYYY-MM-DD') + " to " + end.format('YYYY-MM-DD');
            }
            jQuery('.daterange span').html(displText);

            if (response.ticketTable != undefined && response.ticketTable != '') {
                jQuery('#ticketTable').html(response.ticketTable);
                jQuery('.notification .info-box-number').html(response.totalTicketTable);
                jQuery('.errors .info-box-number').html(response.totalTicketTable);
            }

        },
        error: function() {

        }
    });
}

function getSelectedRange(cStart, cEnd) {
    console.log(cStart._d);
    if(cStart._d == "Invalid Date"){
        cStart = '';
    }else{
        cStart = cStart.format('YYYY-MM-DD');
    }
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
    if (start == cStart && end == cEnd) {
        response.title = datesRange.today.title;
        response.key = datesRange.today.key;
        return response;
    }
    
    // start = datesRange.last_7days.start.format('YYYY-MM-DD');
    // end = datesRange.last_7days.end.format('YYYY-MM-DD');
    // if (start == cStart && end == cEnd) {
    //     response.title = datesRange.last_7days.title;
    //     response.key = datesRange.last_7days.key;
    //     return response;
    // }
    start = datesRange.last_10days.start.format('YYYY-MM-DD');
    end = datesRange.last_10days.end.format('YYYY-MM-DD');
    if (start == cStart && end == cEnd) {
        response.title = datesRange.last_10days.title;
        response.key = datesRange.last_10days.key;
        return response;
    }

    start = datesRange.last_18days.start.format('YYYY-MM-DD');
    end = datesRange.last_18days.end.format('YYYY-MM-DD');
 
    if (start == cStart && end == cEnd) {
        response.title = datesRange.last_18days.title;
        response.key = datesRange.last_18days.key;
        return response;
    }

    start = datesRange.last_months.start.format('YYYY-MM-DD');
    end = datesRange.last_months.end.format('YYYY-MM-DD');
    if (start == cStart && end == cEnd) {
        response.title = datesRange.last_months.title;
        response.key = datesRange.last_months.key;
        return response;
    }
    start = datesRange.last_3months.start.format('YYYY-MM-DD');
    end = datesRange.last_3months.end.format('YYYY-MM-DD');
    if (start == cStart && end == cEnd) {
        response.title = datesRange.last_3months.title;
        response.key = datesRange.last_3months.key;
        return response;
    }
    // start = datesRange.last_6months.start.format('YYYY-MM-DD');
    // end = datesRange.last_6months.end.format('YYYY-MM-DD');
    // if (start == cStart && end == cEnd) {
    //     response.title = datesRange.last_6months.title;
    //     response.key = datesRange.last_6months.key;
    //     return response;
    // }
    start = datesRange.last_12months.start.format('YYYY-MM-DD');
    end = datesRange.last_12months.end.format('YYYY-MM-DD');
    if (start == cStart && end == cEnd) {
        response.title = datesRange.last_12months.title;
        response.key = datesRange.last_12months.key;
        return response;
    }
    // start = datesRange.last_12months.start;
    // end = datesRange.last_12months.end;
    // if (datesRange.all_dates.key == 'all_dates' && cStart == '') {
    //     response.title = datesRange.all_dates.title;
    //     response.key = datesRange.all_dates.key;
    //     return response;
    // }
    start = datesRange.all_dates.start;
    end = datesRange.all_dates.end;
    console.log(datesRange.all_dates);
    console.log(cStart);
    if (start == cStart && end == cEnd) {
        console.log(datesRange.all_dates.key);
        response.title = datesRange.all_dates.title;
        response.key = datesRange.all_dates.key;
        return response;
    }
    response.title = 'Range: ' + cStart + " to " + cEnd;
    response.key = 'customrange';
    return response;

}
function pieChartnew(data, title, name, id) {
    id = (id == undefined) ? '#containerPie' : id;
    title = (title == undefined) ? 'Transaction Details' : title;
    name = (name == undefined) ? 'Transactions' : name;
    $chart = $(id).highcharts({
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)']
                ]
            },
            borderWidth: 2,
            plotBackgroundColor: 'rgba(255, 255, 255, .9)',
            plotShadow: true,
            plotBorderWidth: 1,
            zoomType: 'xy',
            height: 500,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: title
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        legend: {
            layout: 'vertical',
            align: 'right'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: name,
            colorByPoint: true,
            data: JSON.parse(data)
        }]
    });
}