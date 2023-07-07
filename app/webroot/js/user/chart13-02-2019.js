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
    last_15days: {
        title: 'Last 15 Days',
        key: 'last_15days',
        start: moment().subtract(14, 'days'),
        end: moment()
    },
    last_months: {
        title: 'Last Month',
        key: 'last_months',
        start: moment().subtract(1, 'month'),
        end: moment()
    },
    last_3months: {
        title: 'Last 3 Month',
        key: 'last_3months',
        start: moment().subtract(3, 'month'),
        end: moment()
    },
    last_6months: {
        title: 'Last 6 Month',
        key: 'last_6months',
        start: moment().subtract(6, 'month'),
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
        pieChart2(response.pieChartData,'#c3-pie-chart');
                         pieChart2(response.transactionClientPie,'#c3-pie-chart2');
      //  pieChart(response.pieChartData, response.pieTitle, response.pieName);
    }


}

function resetGraph(start, end, action, chartFun, pieGraph, extraParams) {
    var clickedLabel = getSelectedRange(start, end);
    
    var formData = {
        'Filter': {
            'start_date': start.format('YYYY-MM-DD'),
            'end_date': end.format('YYYY-MM-DD'),
            'from': clickedLabel.key
        }
    }; 
//     document.getElementById("whereToPrint1").innerHTML = JSON.stringify(formData, null, 4);
//     console.log(formData);
// alert(BaseUrl+action);
 
    jQuery.ajax({
        url: BaseUrl + action,
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function (response) {
          
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
                         pieChart2(response[value.transactionClientPie],'#c3-pie-chart');
                         pieChart2(response[value.transactionPie],'#c3-pie-chart2');
                       // pieChart(response[value.data], value.title, value.chartName, value.id);
                    }
                });
            }
        },
        error: function () {
 
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
function pieChart2(data , id) {
   

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

var pie_chart = c3.generate({
bindto: id,
size: { width: 350 },
color: {
 pattern: ['#3F51B5', '#FF9800', '#4CAF50', '#00BCD4', '#F44336']
},
data: {
 columns:
 transaction_vs_transaction_type2
 ,
 type : 'pie'
}
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
function googleGraph(data, xAxisDates, tickInterval, options) {
     $('.chart').remove();
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var data2 = '<?php echo $temp; ?>'; 
        drawColumn(data , options.title );
       

}
 function googleGraphhour(data, xAxisDates, tickInterval, options) {
        drawColumn4(data , options.title );
   }

function googleGraphGroup(data, xAxisDates, tickInterval, options) {
     $('.chart').remove(); 
    $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    var data2 = '<?php echo $temp; ?>'; 
            drawColumn2(data , options.title );


}
 function formatDate(date) {
  var monthNames = ["Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct","Nov", "Dec"];
    var d = new Date(date),
        month = '' + monthNames[(d.getMonth())],
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [ month, day].join('-');
}

function drawColumn2(data , tital ) {

  
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
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
              max:largest,
              min:smallest
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        },
        isStacked: true
    };
 
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    column.draw(data, options_column);
}
function drawColumn4(data , tital ) {
 
  var bill_adjustment = [];
  var cars2=[];
  var cars3=[]; 
  var cars=['Year','No of Transaction'];

  data = JSON.parse(data);
   

    cars2.push(cars);
        cars = [];      
    $.each(data, function (key, value) {
         cars.push(value[0]+''); 
        cars.push(value[1]);
          cars2.push(cars);
             cars = [];

            
    });

 
  var largest = 0;
    var smallest=0; 
      for(var i = 0; i < cars2.length; i++){ 
          if(cars2[i][1] > largest){
               largest = cars2[i][1];
            }
            if(cars2[i][1] < smallest){
              smallest = cars2[i][1];
            }
    }
(largest>0)?largest++ : largest=-(smallest/5); 
(smallest<0)?smallest-- : smallest=-(largest/5); 
(largest<5)?largest=4 : '';
(smallest<5)?smallest=-4:'';
 
    var data = google.visualization.arrayToDataTable(cars2);
    
    var options_column = {

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
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
              max:largest,
              min:smallest,
              ticks: [0, .3, .6, .9, 1]
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        }
    };
 
    var column = new google.visualization.ColumnChart($('#google-column3')[0]);
    column.draw(data, options_column);
}

function drawColumn(data , tital ) {

 
   //  alert(data);
  var bill_adjustment = [];
  var cars2=[];
  var cars3=[]; 
  var cars=['Year','No of Bill Adjustment'];

  data = JSON.parse(data);
   var intervall=parseInt(data.length/7);
   var count=1;
   var sum=0;

    cars2.push(cars);
        cars = [];      
    $.each(data, function (key, value) {
        if(count==intervall){

              var date=value[0]/1000;
    var data = {"date_created":date};
var date = new Date(parseInt(data.date_created, 10) * 1000);
 var m=formatDate(date); 
        cars.push(m);
         sum=sum+value[1];
        cars.push(sum);
          cars2.push(cars);
             cars = [];

          
            count=1;
            sum=0;
        }else{
            sum=sum+value[1];
            count++
        }


      
         
    });

 
  var largest = 0;
    var smallest=0; 
      for(var i = 0; i < cars2.length; i++){ 
          if(cars2[i][1] > largest){
               largest = cars2[i][1];
            }
            if(cars2[i][1] < smallest){
              smallest = cars2[i][1];
            }
    }
(largest>0)?largest++ : largest=-(smallest/5); 
(smallest<0)?smallest-- : smallest=-(largest/5); 
(largest<5)?largest=4 : '';
(smallest<5)?smallest=-4:'';
    var data = google.visualization.arrayToDataTable(cars2);
    
    var options_column = {

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
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
              max:largest,
              min:smallest,
              ticks: [0, .3, .6, .9, 1]
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        }
    };
 
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    column.draw(data, options_column);
}
function drawColumn3(data , tital ) {
 
         var x_details = [];
    var cars = [];
var cars2 = [];
var seriesOptions = [];
data = JSON.parse(data);
var i=1;
var data_date;
var count;
if(tital=='Transaction'){
$.each(data, function (key, value) {
seriesOptions[key] = {
 name: value.name,
 data: JSON.parse(value.data)
}
count=1;
//data_date = JSON.parse(value.data);
x_details=[];
var pointInterval=value.pointInterval;
var sum=0;
x_details.push("Year");
cars.push(value.name);
$.each(JSON.parse(value.data), function (key, value) {
if(count==pointInterval){
  var date=value[0]/1000;
    var data = {"date_created":date};
var date = new Date(parseInt(data.date_created, 10) * 1000);
 var m=formatDate(date);  
 x_details.push(m);
 count=1;
sum=sum+value[1];
 cars.push(sum);
 sum=0;
}else{
    count++;
    sum=sum+value[1];
}
});

cars2.push(cars);
cars = [];
});
}else{
$.each(data, function (key, value) {
seriesOptions[key] = {
 name: value.name,
 data: JSON.parse(value.data)
}

//data_date = JSON.parse(value.data);
x_details=[];

x_details.push("Year");
cars.push(value.name);
$.each(JSON.parse(value.data), function (key, value) {
        cars.push(value[1]);   
  var date=value[0]/1000;
    var data = {"date_created":date};
var date = new Date(parseInt(data.date_created, 10) * 1000);
 var m=formatDate(date);  
 x_details.push(m);
});
cars2.push(cars);

cars = [];
});
}


cars2.splice(0,0,x_details);
  var largest = 0;
    var smallest=0; 
    var sum; 
      for(var i = 0; i < cars2.length; i++){ 
       sum=0
         for(var j = 1; j < cars2[i].length; j++){
            sum=cars2[i][j];
              if((sum) > largest){
               largest = sum; 
            }
            if((sum) < smallest){
              smallest = sum; 
            } 
        }  
    } 
(largest>0)?largest-=(smallest/5) : largest=-(smallest/5); 
(smallest<0)?smallest-=(largest/5) : smallest=-(largest/5);
largest=largest*1.2;
smallest=smallest*1.2;
 (largest<5)?largest=4 : '';
(smallest>0 && smallest<5)?smallest=-4:'';
 
    var data = google.visualization.arrayToDataTable(cars2[0].map((col,i)=>cars2.map(row=>row[i])));
    
    var options_column = {

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
                italic: false
            },
            gridlines:{
                color: '#e5e5e5',
                count: 10
            },
            viewWindow: {
              max:largest,
              min:smallest
            }
        },
        legend: {
            position: 'top',
            alignment: 'center',
            textStyle: {
                fontSize: 12
            }
        }
    };
 if(tital=='Transaction'){ 
  
    var column = new google.visualization.ColumnChart($('#google-column')[0]);
    column.draw(data, options_column);
}else{
    var column = new google.visualization.ColumnChart($('#google-column2')[0]);
    column.draw(data, options_column);
 }
}
function multiLineChart2(data, xAxisDates, tickInterval, options)
{
 
     
    
   if(options.title=='Transaction'){
 
$('google-column').remove();
   }
   
 

    // $('.chart').remove();

   /* $('#chart-div').append("<div class='chart'  id='google-column'><span></span></div>");
    $('#chart-div2').append("<div class='chart'  id='google-column2'><span></span></div>");*/
   
    
        drawColumn3(data , options.title );

 
     
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
                formatter: function () {
                    console.log(this.value);
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },
//           tickInterval: 1.5 * 3600 * 1000,
            
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
                , hour: '%I:%M %p'
            }
        },

        yAxis: {
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

function inventoryHoursMultiLineChart(data, xAxisDates, tickInterval, options)
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
                formatter: function () {
                    console.log(this.value);
                    return "<h3><b>" + this.value + "</b></h3>";
                }
            },

            tickInterval: 14* 3600 * 1000,
            dateTimeLabelFormats: {
                day: '%b-%d-%Y'
                , hour: '%I:%M %p'
            }
        },

        yAxis: {
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