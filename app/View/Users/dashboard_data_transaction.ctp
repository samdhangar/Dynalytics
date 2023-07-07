<?php
$this->assign('pagetitle', __('Transaction Data'));
$count_amount_flag = 0;
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Transaction Data'));
} else {
    $this->Custom->addCrumb(__('Transaction data'));
}
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');
?>

<div class="col-md-12 col-sm-12 form-group row">
    <div class="box box-primary">
        <div class="box-body">
            <?php
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'users', 'action' => 'dashboard_data_transaction', 'all'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));
            // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :

            //     echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Company: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            // if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
            //     echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            // endif;
            


            // echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

            $option_filter = ['all'=>'All','above'=>'Above avarage','below'=>'Below avarage'];
            echo $this->Form->input('filterID', array('type' => 'select', 'id' => 'filterID', 'label' => __('Average: '), 'empty' => __('Select All'), 'options'=>$option_filter, 'default' => 'all','class' => 'form-control', 'div' => array('class' => 'col-md-3')));
            ?>
            <?php echo $this->Form->input('daterange', array('label' => __('Selected Dates: '), 'id' => 'daterange', 'class' => 'form-control', 'div' => array('class' => 'col-md-3'))); ?>


            <?php
            echo "<label for='analyBranchId' >&nbsp;</label><br>";
            // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary', 'id' => 'submit'));
            echo $this->Html->link(__('Reset Filter'), array('controller' => 'users', 'action' => 'dashboard_data_transaction', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
            echo $this->Form->end();

            ?>
        </div>
    </div>

</div>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="panel-heading">

        <h5 class="panel-title">Transaction Data</h5>
    </div>





    <div class="table-responsive htmlDataTable">
        <?php // debug($activity);exit; 
        ?>
        <div class="box box-primary">
            <div class="box-footer clearfix">
                <?php
                echo $this->element('paginationtopNew'); ?>
                <?php if (!empty($filter_criteria)) : ?>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Filter Criteria') ?>
                    </strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <strong>
                        <?php echo __('Region:') ?>
                    </strong>
                    <span>
                        <?php echo $filter_criteria['region']; ?>
                    </span>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['branch'])) : ?>
                        <strong>
                            <?php echo __('Branch:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['branch']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['selected_dates'])) : ?>
                        <strong>
                            <?php echo __('Selected Dates:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['selected_dates']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
            </div>
            <div class="box-body table-responsive no-padding">
                <?php
                $startNo = (int) $this->Paginator->counter('{:start}')+1;

                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $noOfFields = 63; ?>
                            <th>
                                <?php
                                echo __('Sr. No.');

                                ?>
                            </th>
                            <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                                <th>
                                    <?php
                                    echo ( __('Region'));
                                    ?>
                                </th>
                                <th>
                                    <?php
                                    echo ( __('Branch Name'));
                                    ?>
                                </th>
                            <?php } ?>
                            <th>
                                <?php
                                echo ( __('Teller Name'));
                                ?>
                            </th>
                            <th>
                                <?php
                                echo (__('Teller Transactions'));
                                ?>
                            </th>

                            <th>
                                <?php
                                echo ( __('Inventory Mgmt. Transactions'));
                                ?>
                            </th>
                            <th>
                                <?php
                                echo (__('Total Transactions'));
                                ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_transaction)) : ?>
                            <tr>
                                <td colspan="<?php echo $noOfFields; ?>">
                                    <?php echo __('No data available for selected period'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($all_transaction as $key_main => $all_transactions) :?>
                                <?php foreach ($all_transactions as $key => $value) {
                                  $type = $value['type'];
                                }
                                $style = '';
                                if(!empty($type)){
                                    if($type == 'belowavg'){
                                        $style = "background: #e6bbbb;
                                        border-color: #e6bbbb;
                                        color: #000;";
                                    }elseif ($type == 'aboveavg') {
                                        $style = "background: #c0e5c4;
                                        border-color: #c0e5c4;
                                        color: #000;";
                                        
                                    }
                                }?>
                                <?php 
                                if(!empty($type)){ ?>
                                <style>
                                td a {
                                    color: #000;
                                }</style>
                               <?php } ?>
                                <tr style="<?php echo !empty($style)  ? $style: '';?>">
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                                        <td class="table-text">
                                           <?php
                                           $region_name = [];
                                            foreach ($all_transactions as $key => $act) :
                                                // echo isset($key) ? $set_region[$key] : '-';
                                                $region_name[] = $set_region[$key];
                                            endforeach; 
                                            echo implode(", ",$region_name);
                                            ?>
                                        </td>
                                        <td class="table-text">
                                            <?php
                                            $branch_name = [];
                                            foreach ($all_transactions as $key => $act) :
                                                $branch_name[] = $branchList[$key];
                                                // echo isset($key) ? $branchList[$key] : '-';
                                            endforeach; 
                                            echo implode(", ",$branch_name);
                                            ?>
                                                

                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php
                                        echo !empty( $key_main) ?  $key_main : '-';
                                        ?>
                                    </td>
                                    <?php 
                                    $cash_transaction = 0;
                                    $admin_transaction = 0;
                                    foreach ($all_transactions as $key => $act) :
                                    $cash_transaction =  $cash_transaction + $act['cash_transaction'];
                                    $admin_transaction =  $admin_transaction + $act['admin_transaction'];
                                    endforeach; ?>
                                    <td>
                                       
                                        <?php
                                        if (!empty($branch_id)) {
                                            echo $this->Html->link(!empty($act['cash_transaction']) ? $act['cash_transaction'] : '0',array('controller' => 'analytics', 'action' => 'transaction_details2',$sessData['id'],$branch_id,$key,$startDate.'_'.$endDate,9));    
                                        }else {

                                            echo $this->Html->link(!empty($cash_transaction) ? $cash_transaction : '0',array('controller' => 'analytics', 'action' => 'transaction_details2',$sessData['id'],$key,$key_main,0,9));    
                                        }
                                        
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if (!empty($branch_id)) {
                                            echo $this->Html->link(!empty($act['admin_transaction']) ? $act['admin_transaction'] : '0',array('controller' => 'analytics', 'action' => 'transaction_details2',$sessData['id'],$branch_id,$key,$startDate.'_'.$endDate,5));    
                                            }else {
                                            echo $this->Html->link(!empty($admin_transaction) ? $admin_transaction : '0',array('controller' => 'analytics', 'action' => 'transaction_details2',$sessData['id'],$key_main,$key,0,5));    
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo (isset($admin_transaction) || !empty($cash_transaction)) ? ($cash_transaction + $admin_transaction) : '0';
                                        ?>
                                    </td>
                                </tr>
                            <?php //endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
    </div>


</div>

<?php

echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');



if (isCompany()) {
    $sessionData = getMySessionData(); ?>
<?php
}

?>


<script type="text/javascript">
    // 'use strict'; 



    $(function() {


        $(window).on('resize', resize);
        $(".sidebar-control").on('click', resize);

        function resize() {
            drawBar();
        }
    });

    jQuery(document).ready(function() {
    
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear() + "/" + (month) + "/" + (day - 1) + "-" + now.getFullYear() + "/" + (month) + "/" + (day);

        $('#daterange').val(today);
        $('#daterange').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });

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
</script>