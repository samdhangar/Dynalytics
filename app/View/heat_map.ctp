<?php
$this->assign('pagetitle', __('Heat Map'));
$this->Custom->addCrumb(__('Reports'));
$this->start('top_links');
echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.HeatMapReport'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
/*echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'bill_count'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));*/
$this->end();

 echo $this->Html->script('user/moment');
  
  echo $this->Html->script('user/highcharts');
  echo $this->Html->script('user/daterangepicker');
   echo $this->Html->script('mapbox-gl');
?>
  <div class="col-md-12 col-sm-12 form-group row">
       
            <div class="box box-primary">
                 <div class="box-body">
               
                    <?php
                    echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'heat_map'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    if((!isCompany()) && ($sessionData['user_type']!='Region')):
                        
                         echo $this->Form->input('company_id', array('onchange'=>'getResion(this.value)','id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    endif;
                     if($sessionData['user_type']!='Region'):
                      echo $this->Form->input('regiones', array('onchange'=>'getBranches(this.value)','id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                       endif;
 

                    echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


                    echo $this->Form->input('station', array('type'=>'select','id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                     echo "<label for='analyBranchId' >&nbsp;</label><br>";
                    echo $this->Form->submit(__('Search'),array('class'=>'btn btn-primary'));
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'heat_map', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
                    echo $this->Form->end();
                    ?>
                </div>
            </div>

        </div>
     
<div class="panel panel-flat" style="float:left;width:100%;">
   
<div class="panel-body" id="chart-div"  >  
                                  
                                     <div class="chart" id="google-column" style="height: 500px;">  <div style="height: 500px;" id='map'></div>  </div> 
                                     
                            </div>
                        </div>

<div class="panel panel-flat"  style="float:left;width:100%;">
    <div class="panel-heading">
         <h5 class="panel-title graphTitle h4"><?php
       echo getReportFilter($this->Session->read('Report.HeatMapReport'));

        ?></h5>
        <h5 class="panel-title">Activity Report</h5>
    </div> 
     <div class="panel-heading">
        
    </div>

 




    <div class="table-responsive htmlDataTable">
        <?php echo $this->element('reports/heat_map', array('bills' => $bills, 'companyDetail' => $companyDetail)); ?>
    </div>


</div>

<?php
echo $this->Html->script('user/chart');

?>

 
<script>
var data2='<?php echo $heatmap_data ?>'
var data = JSON.parse(data2);
var cars2=[];
  var cars3=[]; 
  var cars=[];


    $.each(data, function (key, value) {
          cars={ "type": "Feature", "properties":{ "id": "ak16994521", "mag": value[2], "time": 1507425650893, "felt": null, "tsunami": 0 },"geometry": { "type": "Point", "coordinates": [ value[1], value[0], 0.0 ] } };
          cars2.push(cars);
          cars = [];
    });

 mapboxgl.accessToken = 'pk.eyJ1IjoidHVzaGFyZGV3ODQiLCJhIjoiY2p0bzB3YmtiMHI5NzN5cWdjajFnaXBzZyJ9.qYjdp9xOq5zJyeIvbW8R9Q';
/*
// Set bounds to New York, New York
var bounds = [
[-74.04728500751165, 40.68392799015035], // Southwest coordinates
[-73.91058699000139, 40.87764500765852]  // Northeast coordinates
];*/

var map = new mapboxgl.Map({
container: 'map',
style: 'mapbox://styles/mapbox/dark-v10',
center: [-120, 40],
zoom: 2
/*
,
maxBounds: bounds // Sets bounds as max*/
});

map.on('load', function() {
// Add a geojson point source.
// Heatmap layers also work with a vector tile source.
map.addSource('earthquakes', {
"type": "geojson",
"data": {
"type": "FeatureCollection",
"crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
"features": cars2
}
});
 
map.addLayer({
"id": "earthquakes-heat",
"type": "heatmap",
"source": "earthquakes",
"maxzoom": 9,
"paint": {
// Increase the heatmap weight based on frequency and property magnitude
"heatmap-weight": [
"interpolate",
["linear"],
["get", "mag"],
1,5,
3,5,
6,5
],
// Increase the heatmap color weight weight by zoom level
// heatmap-intensity is a multiplier on top of heatmap-weight
"heatmap-intensity": [
"interpolate",
["linear"],
["zoom"],
0, 1,
9, 3
],
// Color ramp for heatmap.  Domain is 0 (low) to 1 (high).
// Begin color ramp at 0-stop with a 0-transparancy color
// to create a blur-like effect.
"heatmap-color": [
"interpolate",
["linear"],
["heatmap-density"],
0, "rgba(0,128,0,0)",//this is green color
0.4, "rgb(255,255,0)",//this is yellow color
1, "rgb(255,0,0)"//this is red color
],
// Adjust the heatmap radius by zoom level
"heatmap-radius": [
"interpolate",
["linear"],
["zoom"],
0, 10,
9, 20
],
// Transition from heatmap to circle layer by zoom level
"heatmap-opacity": [
"interpolate",
["linear"],
["zoom"],
7, 1,
9, 0
],
}
}, 'waterway-label');
 
map.addLayer({
"id": "earthquakes-point",
"type": "circle",
"source": "earthquakes",
"minzoom": 7,
"paint": {
// Size circle radius by earthquake magnitude and zoom level
"circle-radius": [
"interpolate",
["linear"],
["zoom"],
7, [
"interpolate",
["linear"],
["get", "mag"],
1, 1,
6, 4
],
16, [
"interpolate",
["linear"],
["get", "mag"],
1, 5,
6, 50
]
],
// Color circle by earthquake magnitude
"circle-color": [
"interpolate",
["linear"],
["get", "mag"],
1, "rgba(0,128,0,0)",//this is green color
3, "rgb(255,255,0)",//this is yellow color
6, "rgb(255,0,0)"  //this is red color
],
"circle-stroke-color": "white",
"circle-stroke-width": 1,
// Transition from heatmap to circle layer by zoom level
"circle-opacity": [
"interpolate",
["linear"],
["zoom"],
7, 0,
8, 1
]
}
}, 'waterway-label');
});
</script>

<script type="text/javascript">
function pageload() {
    
}

    jQuery(document).ready(function () {
          
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
        //lineChart(data, xAxisDates, tickInterval, options);

        var startDate = moment('<?php echo $this->Session->read('Report.BillCountReport.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.BillCountReport.end_date') ?>', 'YYYY-MM-DD');

        addDateRange(startDate, endDate, "analytics/heat_map", '');
 
    });

    function getBranches(compId)
    {
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/company_branches/get_branches/"+ compId,
            type:'post',
            success:function(response){
                loader('hide');
                jQuery('#analyBranchId').html(response);
            },
            error:function(e){
                loader('hide');
            }
        });
    }

    function getStations(branchId)
    {
        console.log(jQuery('#analyBranchId').val());
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
</script>
