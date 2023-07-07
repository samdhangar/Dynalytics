<?php
$this->assign('pagetitle', __('Active Teller Sign On Report'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
/*echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.SideLogReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));*/
/*echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.SideLogReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));*/
echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'side_log'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
echo $this->Html->script('user/moment');
 echo $this->Html->script('user/chart'); 
  echo $this->Html->script('user/daterangepicker');
    echo $this->Html->script('user/loader');
date_default_timezone_set("America/New_York");

?>
<style>
#chartdiv {
  width: 90%;
  height: 70px;
}

</style>
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

  <div class="col-md-12 col-sm-12 form-group row">
       
            <div class="box box-primary">
                 <div class="box-body">
               
                    <?php
                    echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'side_log'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    //     if($sessionData['user_type']!='Region' AND $sessionData['user_type']!='Branch'):
                    //    echo $this->Form->input('regiones', array('onchange'=>'getBranches(this.value)','id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    //     endif;
                    // echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


                    // echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
?>
                  <div class="col-md-3">

<?php    //echo $this->Form->input('date', array('label' => __('Date: ') , 'class' => 'form-control pickadate-selectors'));  ?>
                 </div>
<?php
                    //  echo "<label for='analyBranchId' >&nbsp;</label><br>";
                    // echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                    // echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'side_log', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                   $arrValidation = array(
           /* 'Rules' => array(
                'branch_id' => array(
                    'required' => 1
                ),
                'regiones' => array(
                    'required' => 1
                ) 
            ),
            'Messages' => array(
                'branch_id' => array(
                    'required' => __('Please select Branch')
                ),
                'regiones' => array(
                    'required' => __('Please select Region')
                ) 
        )*/);
        echo $this->Form->setValidation($arrValidation);
                    echo $this->Form->end();
                    ?>
                </div>
            </div>

        </div>


<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading"><div>
        
      <div id="chartdiv"></div>
       
 
  </div>
</div>


<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
        <h5 class="panel-title">Active Teller Sign On Report</h5>
    </div>





    <div class="table-responsive">
        <?php echo $this->element('reports/side_log', array('activity' => $SideLogs, 'companyDetail' => $companyDetail,'type'=>'vault'));?>
    </div>


</div>

<?php
echo $this->Html->script('user/chart');

?>

 
<script type="text/javascript">
    jQuery(document).ready(function () {
        var data2 = '<?php echo $temp; ?>';
        var xAxisDates = '<?php echo $xAxisDates; ?>';
        var tickInterval = '<?php echo $tickInterval; ?>';
        var options = {
            name: '<?php echo $lineChartName; ?>',
            title: '<?php echo $lineChartTitle; ?>',
            xTitle: '<?php echo $lineChartxAxisTitle; ?>',
            yTitle: '<?php echo $lineChartyAxisTitle; ?>',
            id: '#container'
        };
          var startDate = moment('<?php echo $this->Session->read('Report.SideLogReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.SideLogReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/side_log", 'lineChart');

    });

    function getBranches(compId)
    {
       if(compId==''){
jQuery('#analyBranchId').html('<option value="">Select All</option>');
jQuery('#analyStationId').html('<option value="">Select All</option>');
        }else{
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/"+ compId,
            type:'post',
            success:function(response){
                loader('hide');
                jQuery('#analyBranchId').html(response);
                jQuery('#analyStationId').html('<option value="">Select All</option>');
            },
            error:function(e){
                loader('hide');
            }
        });
      }
    }

    function getStations(branchId)
    {
       if(branchId==''){
 jQuery('#analyStationId').html('<option value="">Select All</option>');
        }else{
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_stations/"+ branchId,
            type:'post',
            data: {data:jQuery('#analyBranchId').val()},
            success:function(response){
                loader('hide');
                jQuery('#analyStationId').html(response);
            },
            error:function(e){
                loader('hide');
            }
        });
      }
    }

</script>
<style type="text/css">
  .amcharts-amexport-item { 
    background-color: rgb(255, 255, 255);
    padding-top: 7px;
  }
  .amcharts-amexport-item:hover, .amcharts-amexport-item.active {
     background: rgb(255, 255, 255);
}
.icon-image3{
   opacity: 2.0;
  width: 41px;
  height: 36px;
 padding-top: 7px;
}
.chartdiv > div > svg > g > g > g > g > g > g > g >{
  /*display: none;
  stroke-opacity:0;
  stroke-width:0;
*/
}
</style>

<script type="text/javascript">
 var graph_pastel_colour=['#b6cabe','#96b1ce','#f0dacd','#d7c6ea','#f1b9c4','#f0dacd','#aee9f7','#f1f7ad']; 
var cars={category:"", start:"",end:"", color:"" , task:""};      
var data3 = '<?php echo $temp; ?>';
console.log(data3);
var data2=JSON.parse(data3); 
var cars3=[]; 
var all_teller=[];

  var cars2=[];
  var i=0;
  $.each(data2, function (key, value2) {
    
    $.each(JSON.parse(value2.data), function (key, value) {
      var a = all_teller.indexOf(value[1]);
      if(a==-1){
         all_teller.push(value[1]);
      }
    cars["category"]=(value[1]);
    cars["start"]=(new Date(value[2]*1000));
     cars["end"]=(new Date(value[3]*1000));
    cars["color"]=graph_pastel_colour[(i%8)]; 
     cars["task"]=(value[1]); 
 i++;
       cars2.push(cars);
       cars={category:"", start:"",end:"", color:"" , task:""};
 }); 
}); 
  if(cars2.length==0){
 cars2=[];
 cars["category"]=('No data');
    cars["start"]=(0);
     cars["end"]=(0);
    cars["color"]=graph_pastel_colour[(1)]; 
     cars["task"]=('No data'); 
 
       cars2.push(cars);
     }
    // document.getElementById("whereToPrint7").innerHTML = JSON.stringify(all_teller, null, 4);

    height=(((all_teller.length)+1)*54);
    height = height+50;
    document.getElementById("chartdiv").style.height = height+"px";
    

    </script>


<script>
am4core.ready(function() {
am4core.useTheme(am4themes_animated);
var chart = am4core.create("chartdiv", am4charts.XYChart);
chart.hiddenState.properties.opacity = 0; 
chart.paddingRight = 30;
chart.dateFormatter.inputDateFormat = "yyyy-MM-dd HH:mm";
var colorSet = new am4core.ColorSet('#ff0000');
colorSet.saturation = 0.9;
chart.data = cars2;
chart.dateFormatter.dateFormat = "yyyy-MM-dd HH:mm";
chart.dateFormatter.inputDateFormat = "yyyy-MM-dd HH:mm:ss";

var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "category";
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.renderer.inversed = true;

var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
dateAxis.renderer.minGridDistance = 70;
dateAxis.baseInterval = { count: 1, timeUnit: "H" };
dateAxis.renderer.tooltipLocation = 0;

var series1 = chart.series.push(new am4charts.ColumnSeries());
series1.columns.template.height = am4core.percent(50);
//series1.columns.template.tooltipText = "[bold]{task}: {openDateX}[/] - [bold]{dateX}[/]";
series1.columns.template.tooltipHTML = `<center><strong>Teller Id: {task}</strong> <br>
<strong>Login: {openDateX}</strong> <br>
<strong>Log Out: {dateX}</strong></center>
 `;
chart.exporting.menu = new am4core.ExportMenu();
chart.exporting.menu.items = [{
      "type": "png", "label": "<i class='a icon-download position-left' style=' height:30px;' aria-hidden=''>&nbsp;&nbsp;Export</i>" , 
  }];
series1.dataFields.openDateX = "start";
series1.dataFields.dateX = "end";
series1.dataFields.categoryY = "category";
series1.columns.template.propertyFields.fill = "color"; // get color from data
series1.columns.template.propertyFields.stroke = "color";
series1.columns.template.strokeOpacity = 1;
 
let axisy = chart.yAxes.push(new am4charts.ValueAxis()); 
axisy.paddingLeft = -60; 
axisy.layout = "absolute";
  
axisy.title.text = "Teller Id";
axisy.title.rotation = 0;
axisy.title.align = "center";
axisy.title.valign = "left";
axisy.title.dy = -20; 
let axis = chart.xAxes.push(new am4charts.ValueAxis());
//axis.renderer.grid.template.disabled = !showgrid;
//axis.paddingLeft = 10;
 //axis.paddingRight = 100; 
axis.layout = "absolute";
 
// Set up axis title
axis.title.text = "Time";
axis.title.rotation = 0;
axis.title.align = "center";
axis.title.valign = "bottom";
axis.title.dy = 50;
let title = chart.titles.create();
title.text = "Active Teller Sign On Report";
title.fontSize = 20;
title.marginBottom = 30;
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>

