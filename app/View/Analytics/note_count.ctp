<?php
$this->assign('pagetitle', __('Note Count'));
$this->Custom->addCrumb(__('Analytics'));
$this->start('top_links');
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.Transaction'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));

echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'note_count'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));
 
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
                        echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'note_count'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
                    //     if((!isCompany()) && ($sessionData['user_type']!='Region')):
                        
                    //      echo $this->Form->input('company_id', array('onchange'=>'getResion(this.value)','id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    //     endif;
                    //   if($sessionData['user_type']!='Region' AND $sessionData['user_type']!='Branch'):
                    //   echo $this->Form->input('regiones', array('onchange'=>'getBranches(this.value)','id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    //    endif;
 

                    // echo $this->Form->input('branch_id', array('onchange'=>'getStations(this.value)','id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));


                    
                    // echo $this->Form->input('station', array('onchange'=>'formSubmit()','type'=>'select','id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

 

?>
</div>
                 </div>
<?php
                    echo "<label for='analyBranchId' >&nbsp;</label><br>"; 
                    echo $this->Html->link(__('Reset Filter'), array('controller' => 'analytics', 'action' => 'note_count', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));

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
        ));
        echo $this->Form->setValidation($arrValidation);
        echo $this->Form->end();

        

                    ?>
                </div>
            </div>

        </div>
        <div class="panel panel-flat" style="float:left;width:100%;">
            <div class="header-content">
              <div class="page-title total_selected_denom" style="display: inline!important">Total number of Notes  processed in <?php echo  getReportFilter($this->Session->read('Report.Transaction'));?> period:  
                <?php echo number_format($total_selected_denom);?>
              </div> 
            </div>
        </div>

        <div class="panel panel-flat" style="float:left;width:100%;">
            <div class="header-content">
              <div class="page-title total_denom" style="display: inline!important">Total number of Notes processed:  
                <?php echo number_format($total_denom);?>
              </div> 
            </div>
        </div>
          <div class="panel panel-flat" style="float:left;width:100%;">
            <div class="header-content">
              <div class="page-title">
                <?php
                  echo  getReportFilter($this->Session->read('Report.Transaction'));
                ?>
              </div>             
              <div class="elements">
                <a id="export"><i class="fa icon-download position-left"></i> Export</a>
              </div>
            </div>
          <br><br>
          <div class="panel-body" id="chart-div" style="height: 350px; width: 1040px; display: contents;">                                              
            <div class="chart" id="google-column"></div>                                                
          </div>                    
      </div>
      <div class="panel panel-flat"  style="float:left;width:100%;">
         <div class="panel-heading">
             <h5 class="panel-title">Transaction Data</h5>
         </div>
         <div class="table-responsive  htmlDataTable">
           <?php  echo $this->element('user/note_count_details', array('transactions' => $transactions)); ?>
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


</script>

<script type="text/javascript">
    jQuery(document).ready(function () 
    {       
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
            opens: 'left'
        }, function(start, end, label) {
            $('#analyticForm').submit();
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        $('#daterange').attr('disabled', true);

        var data1 = '<?php echo json_encode($final_data,JSON_NUMERIC_CHECK ); ?>';
        multiLineChart3(data1); 

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
            },
            last_6months: {
                title: 'Last 6 Month',
                key: 'last_6months',
                start: moment().subtract('month', 6).startOf('month'),
                end: moment().subtract('month').startOf('month')
            }
        };
        var startDate = moment('<?php echo $this->Session->read('Report.Transaction.start_date') ?>', 'YYYY-MM-DD');
        var endDate = moment('<?php echo $this->Session->read('Report.Transaction.end_date') ?>', 'YYYY-MM-DD');

        $('.daterange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 15 Days': [moment().subtract('days', 14), moment()],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Last 3 Month': [moment().subtract('month', 3).startOf('month'), moment().subtract('month').startOf('month')],
                'Last 6 Month': [moment().subtract('month', 6).startOf('month'), moment().subtract('month').startOf('month')],
            },
            startDate: startDate,
            endDate: endDate,
        },
                function (start, end) {
                    var clickedLabel = getSelectedRange(start, end);
                    console.log(clickedLabel);
                    loader('show');
                    var formData = {
                        'start_date': start.format('YYYY-MM-DD'),
                        'end_date': end.format('YYYY-MM-DD'),
                        'from': clickedLabel.key
                    };
                    jQuery.ajax({
                        url: BaseUrl + "analytics/note_count",
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        success: function (response) 
                        {
                            loader('hide');
                            jQuery('.daterange span').html(clickedLabel.title);
                            jQuery('.page-title').html(clickedLabel.title);
                            var chartdata = response.final_data;
                            
                            var epi_chart = $('#chart-div').highcharts();
                              if (epi_chart.series.length == 0) {
                                  epi_chart.addSeries({
                                      name: "Notes",
                                      data: chartdata
                                  });
                              } else {
                                  epi_chart.series[0].update({
                                      name: "Notes",
                                      data: chartdata
                                  });
                              }
                            jQuery('#chart-div').highcharts().reflow();

                            response.total_selected_denom = Number(response.total_selected_denom.toFixed(0)).toLocaleString().split(/\s/).join(',');

                            response.total_denom = Number(response.total_denom.toFixed(0)).toLocaleString().split(/\s/).join(',');
                            
                            $('.total_selected_denom').html("Total number of Notes processed in "+clickedLabel.title+" period: "+response.total_selected_denom);
                            $('.total_denom').html("Total number of Notes processed: "+response.total_denom);


                            if (response.htmlData != undefined && response.htmlData != '') {
                                jQuery('.htmlDataTable').html(response.htmlData);
                            }

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

            start = datesRange.last_6months.start.format('YYYY-MM-DD');
            end = datesRange.last_6months.end.format('YYYY-MM-DD');
            if (start <= cStart && end >= cEnd) {
                response.title = datesRange.last_6months.title;
                response.key = datesRange.last_6months.key;
                return response;
            }
            response.title = 'Custom Range: ' + cStart + " to " + cEnd;
            response.key = 'customrange';
            return response;
        }

    });


    // function getResion(compId)
    // {
    //    loader('show');
    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_region/"+ compId,
    //         type:'post',
    //         success:function(response){
    //              loader('hide'); 
    //             jQuery('#analyRegionId').html(response);
    //             jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //             jQuery('#analyStationId').html('<option value="">Select All</option>');
    //         },
    //         error:function(e){
    //           loader('hide');    
    //         }
    //     });
    //     $("#daterange").val('');
    //     $('#analyticForm').submit();
    // }
    
    // function getBranches(compId)
    // {
    //   if(compId==''){ 
    //     jQuery('#analyBranchId').html('<option value="">Select All</option>');
    //     jQuery('#analyStationId').html('<option value="">Select All</option>');
    //   }else{
    //    loader('show');
    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_branches/"+ compId,
    //         type:'post',
    //         success:function(response){
    //              loader('hide'); 
    //             jQuery('#analyBranchId').html(response);
    //             jQuery('#analyStationId').html('<option value="">Select All</option>');
    //         },
    //         error:function(e){
    //             loader('hide');  
    //         }
    //     });
    //     $("#daterange").val('');
    //     $('#analyticForm').submit();
    //   }
    // }
    
    // function getStations(branchId)
    // { 
    //     if(branchId==''){
    //         jQuery('#analyStationId').html('<option value="">Select All</option>');
    //     }else{
    //     loader('show');
    //     jQuery.ajax({
    //         url: BaseUrl + "/company_branches/get_stations/"+ branchId,
    //         type:'post',
    //         data: {data:jQuery('#analyBranchId').val()},
    //         success:function(response){
    //              loader('hide');  
    //             jQuery('#analyStationId').html(response);
    //         },
    //         error:function(e){
    //            loader('hide');  
    //         }
    //     });
    //     $("#daterange").val('');
    //     $('#analyticForm').submit();
    //   }
    // }
   
</script>
