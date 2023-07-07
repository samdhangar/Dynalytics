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
        start: moment().subtract('month', 1),
        end: moment()
    },
    last_3months: {
        title: 'Last 3 Month',
        key: 'last_3months',
        start: moment().subtract('month', 3),
        end: moment()
    },
    last_6months: {
        title: 'Last 6 Month',
        key: 'last_6months',
        start: moment().subtract('month', 6),
        end: moment()
    }
};
jQuery(document).ready(function () {
    Highcharts.setOptions({// This is for all plots, change Date axis to local timezone
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

function getGraphFunctionName(funName, response, title, name)
{
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
        lineChart(response.data, response.xAxisDates, response.tickInterval, options);
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
    if (funName == 'pieChart') {
        pieChart(response.pieChartData, response.pieTitle, response.pieName);
    }

}

function resetGraph(start, end, action, chartFun, pieGraph, extraParams) {
    var clickedLabel = getSelectedRange(start, end);
    loader('show');
    var formData = {
        'Filter': {
            'start_date': start.format('YYYY-MM-DD'),
            'end_date': end.format('YYYY-MM-DD'),
            'from': clickedLabel.key
        }
    };
    jQuery.ajax({
        url: BaseUrl + action,
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function (response) {
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
                $.each(extraParams.charts, function (key, value) {
                    if (typeof value.type == undefined || value.type == '') {
                        pieChart(response[value.data], value.title, value.chartName, value.id);
                    } else if (value.type == 'line') {
//                        pieChart(response[value.data], value.title, value.chartName, value.id);
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
                        if (value.id == undefined) {
                            value.id = '#container';
                        }
                        multiLineChart(response[value.data], response.xAxisDates, response.tickInterval, options);
                    } else {
                        pieChart(response[value.data], value.title, value.chartName, value.id);
                    }
                });
            }
        },
        error: function () {
            loader('hide');
        }
    });
}

function fileProcessChart(data, xAxisDates, tickInterval)
{
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
            text: 'File Processing View',
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            title: {
                text: 'Dates',
                style: {
                    fontWeight: 'bold'
                },
                formatter: function () {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
            labels: {
                rotation: -45,
                formatter: function () {
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
                text: 'No. of Files',
                style: {
                    fontWeight: 'bold'
                },
                formatter: function () {
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
            formatter: function () {
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
                name: 'Files',
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

function lineChart(data, xAxisDates, tickInterval, options)
{
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
            formatter: function () {
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

function multiLineChart(data, xAxisDates, tickInterval, options)
{
    var seriesOptions = [];
    data = JSON.parse(data);
    $.each(data, function (key, value) {
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
            title: 'Line Chart',
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
//                formatter: function () {
//                    return Highcharts.dateFormat('%b-%d-%Y', this.value);
//                }
            },
            title: {
                text: options.xTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function () {
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
//            tickInterval: 1.5 * 3600 * 1000,
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
                , hour: '%I:%M %p'
            }
        },
        yAxis: {
			allowDecimals: false,
            title: {
                text: options.yTitle,
                style: {
                    fontWeight: 'bold'
                },
                formatter: function () {
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
            formatter: function () {
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

function barChart(data, xAxisDates, tickInterval, options)
{
    var seriesOptions = [];
    data = JSON.parse(data);
    $.each(data, function (key, value) {
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

function transactionDetailChart(data, xAxisDates, tickInterval)
{
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
            'Last 7 Days': [moment().subtract('days', 6), moment()],
            'Last 15 Days': [moment().subtract('days', 14), moment()],
            'Last Month': [moment().subtract('month', 1), moment()],
            'Last 3 Month': [moment().subtract('month', 3), moment()],
            'Last 6 Month': [moment().subtract('month', 6), moment()],
//            'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
//            'Last 3 Month': [moment().subtract('month', 3).startOf('month'), moment().subtract('month', 1).endOf('month')],
//            'Last 6 Month': [moment().subtract('month', 6).startOf('month'), moment().subtract('month', 1).endOf('month')],
        },
        startDate: startDate,
        endDate: endDate
    },
    function (start, end) {
        if (action == 'dashboardData') {
            changeDashboardData(start, end);
        }else{
            resetGraph(start, end, action, graphName, pieGraphName, extraParams);
        }

    });
}

function changeDashboardData(start, end)
{
    var clickedLabel = getSelectedRange(start, end);
    loader('show');
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
        success: function (response) {
            loader('hide');
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
            loader('hide');
            var displText = jQuery('.ranges li.active').html();
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
        error: function () {
            loader('hide');
        }
    });
}

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
    start = datesRange.last_6months.start.format('YYYY-MM-DD');
    end = datesRange.last_6months.end.format('YYYY-MM-DD');
    if (start <= cStart && end >= cEnd) {
        response.title = datesRange.last_6months.title;
        response.key = datesRange.last_6months.key;
        return response;
    }
    response.title = 'Range: ' + cStart + " to " + cEnd;
    response.key = 'customrange';
    return response;
}