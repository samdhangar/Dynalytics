<style>
    body {
        font-family: Arial;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
</style>
<?php
$uri_get = (($this->request->here()));
$uri_get = explode('/', $uri_get);
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
$this->start('top_links');

if (in_array("daily", $uri_get)) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'allTransactionData','daily'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));
} elseif (in_array("weekly", $uri_get)) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'allTransactionData','weekly'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));
} elseif (in_array("monthly", $uri_get)) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'allTransactionData','monthly'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));
} elseif (in_array("yearly", $uri_get)) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'allTransactionData','yearly'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));
}elseif (in_array("byHour", $uri_get)) {
    echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'allTransactionData','byHour'), array('title' => __('Export CSV'), 'icon' => ' icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'id' => 'exportBtn', 'escape' => false));
}
$this->end();
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');
$url_argument = $this->request->params['pass'];
$url_argument = !empty($url_argument) ? $url_argument[0] : 'byHour';
?>
<div class="row">
  <div class="col-md-12 col-sm-12 form-group row">
    <div class="box box-primary">
      <div class="box-body">

        <?php
        ?>
      </div>
    </div>

  </div>
</div>
<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="tab">
        <?php echo $this->Form->postButton('By Hour', array('controller' => 'users', 'action' => 'allTransactionData', 'byHour')); ?>
        <?php echo $this->Form->postButton('Daily', array('controller' => 'users', 'action' => 'allTransactionData', 'daily')); ?>
        <?php echo $this->Form->postButton('Weekly', array('controller' => 'users', 'action' => 'allTransactionData', 'weekly')); ?>
        <?php echo $this->Form->postButton('Monthly', array('controller' => 'users', 'action' => 'allTransactionData', 'monthly')); ?>
        <?php echo $this->Form->postButton('Yearly', array('controller' => 'users', 'action' => 'allTransactionData', 'yearly')); ?>
    </div>
    <div class="table-responsive htmlDataTable">
        <div class="box box-primary">
            <div class="box-footer clearfix">
                <?php
                echo $this->element('paginationtopNew'); ?>
            </div>

            <div class="box-body table-responsive no-padding">
                <?php
                $startNo = (int) $this->Paginator->counter('{:start}') + 1;

                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $noOfFields = 63; ?>
                            <th style="width: 3%;">
                                <?php
                                echo __('#');

                                ?>
                            </th>
                            <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                                <th>
                                    <?php
                                    echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Region'));
                                    ?>
                                </th>
                            <?php } ?>

                            <th>
                                <?php
                                if (in_array("daily", $uri_get)) {
                                    echo $this->Paginator->sort('TransactionDetail.updated_date', __('Days'));
                                } elseif (in_array("weekly", $uri_get)) {
                                    echo $this->Paginator->sort('TransactionDetail.updated_date', __('Weeks'));
                                } elseif (in_array("monthly", $uri_get)) {
                                    echo $this->Paginator->sort('TransactionDetail.updated_date', __('Months'));
                                } elseif (in_array("yearly", $uri_get)) {
                                    echo $this->Paginator->sort('TransactionDetail.updated_date', __('Year'));
                                }elseif (in_array("byHour", $uri_get)) {
                                    echo $this->Paginator->sort('TransactionDetail.updated_date', __('Hours'));
                                }
                                ?>
                            </th>
                            <th>
                                <?php
                                echo __('Minimum Transactions');
                                ?>
                            </th>
                            <th>
                                <?php
                                echo  __('Maximum Transactions');
                                ?>
                            </th>
                            <th>
                                <?php
                                echo  __('Total Transactions');
                                ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allData)) : ?>
                            <tr>
                                <td colspan="<?php echo $noOfFields; ?>">
                                    <?php echo __('No data available for selected period'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($allData as $key => $all_transactions) : ?>
                            <tr>
                                <td>
                                    <?php echo $startNo++; ?>
                                </td>
                                <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                                    <td class="table-text">
                                        <?php
                                        if (in_array("daily", $uri_get)) {
                                            echo isset($all_transactions['m']['regiones']) ? $this->Html->link($regiones[$all_transactions['m']['regiones']], array('controller' => 'users', 'action' => 'dashboard_daily', 'daily', 'branchDaily','?' => ['regiones_id' => $all_transactions['m']['regiones']])) : '-';
                                        } elseif (in_array("weekly", $uri_get)) {
                                            echo isset($all_transactions['m']['regiones']) ? $this->Html->link($regiones[$all_transactions['m']['regiones']], array('controller' => 'users', 'action' => 'dashboard_weekly', 'weekly', 'branchWeekly','?' => ['regiones_id' => $all_transactions['m']['regiones']])) : '-';
                                        }elseif (in_array("byHour", $uri_get)) {
                                            echo isset($all_transactions['m']['regiones']) ? $this->Html->link($regiones[$all_transactions['m']['regiones']], array('controller' => 'users', 'action' => 'dashboard_hour', 'byHour', 'hourbranch','?' => ['regiones_id' => $all_transactions['m']['regiones']])) : '-';
                                        }elseif (in_array("monthly", $uri_get)) {
                                            echo isset($all_transactions['m']['regiones']) ? $this->Html->link($regiones[$all_transactions['m']['regiones']], array('controller' => 'users', 'action' => 'dashboard_monthly', 'monthly', 'hourmonthly','?' => ['regiones_id' => $all_transactions['m']['regiones']])) : '-';
                                        }elseif(in_array("yearly", $uri_get)){ 
                                            echo isset($all_transactions['m']['regiones']) ? $this->Html->link($regiones[$all_transactions['m']['regiones']], array('controller' => 'users', 'action' => 'dashboard_yearly', 'yearly', 'branchYearly','?' => ['regiones_id' => $all_transactions['m']['regiones']])) : '-';;
                                        }
                                        ?>
                                    </td>
                                <?php } ?>
                                <td>

                                    <?php
                                    if (in_array("daily", $uri_get)) {
                                        echo isset($all_transactions['m']['DAY']) ? $all_transactions['m']['DAY'] : '';
                                    } elseif (in_array("weekly", $uri_get)) {
                                        echo isset($all_transactions['m']['WEEK']) ? $all_transactions['m']['WEEK'] : '';
                                    } elseif (in_array("monthly", $uri_get)) {
                                        echo isset($all_transactions['m']['MONTHNAME']) ? $all_transactions['m']['MONTHNAME'] : '-';
                                    } elseif (in_array("yearly", $uri_get)) {
                                        echo isset($all_transactions['m']['YEAR']) ? $all_transactions['m']['YEAR'] : '';
                                    } elseif (in_array("byHour", $uri_get)) {
                                        // echo '<pre><b></b><br>';
                                        // // print_r($all_transactions['m']['HOURw']);echo '<br>';exit;
                                        // print_r(date('h a', strtotime($all_transactions['m']['HOURw'])));echo '<br>';exit;

                                        $date=date_create($all_transactions['m']['HOUR'].":00:00");
                                        $time = date_format($date,"g A");
                                        echo isset($all_transactions['m']['HOUR']) ? $time: '';
                                    }
                                    ?>
                                </td>
                                <td>

                                    <?php echo isset($all_transactions[0]['min(m.COUNT)']) ? $all_transactions[0]['min(m.COUNT)'] : 0; ?>
                                </td>
                                <td>

                                    <?php echo isset($all_transactions[0]['max(m.COUNT)']) ? $all_transactions[0]['max(m.COUNT)'] : 0; ?>
                                </td>
                                <td>
                                    <?php 
                                    if (in_array("byHour", $uri_get)) {
                                        echo $this->Html->link(isset($all_transactions[0]['sum(m.COUNT)'])  ? $all_transactions[0]['sum(m.COUNT)'] : 0,array('controller' => 'users', 'action' => 'transaction_detail',$all_transactions['m']['company_id'],$all_transactions['m']['regiones'],'hour',sprintf("%02d", $all_transactions['m']['HOUR'])));
                                    } elseif (in_array("daily", $uri_get)) {
                                        echo $this->Html->link(isset($all_transactions[0]['sum(m.COUNT)'])  ? $all_transactions[0]['sum(m.COUNT)'] : 0,array('controller' => 'users', 'action' => 'transaction_detail',$all_transactions['m']['company_id'],$all_transactions['m']['regiones'],'daily',$all_transactions['m']['DAY']));
                                    }elseif (in_array("weekly", $uri_get)) {
                                        echo $this->Html->link(isset($all_transactions[0]['sum(m.COUNT)'])  ? $all_transactions[0]['sum(m.COUNT)'] : 0,array('controller' => 'users', 'action' => 'transaction_detail',$all_transactions['m']['company_id'],$all_transactions['m']['regiones'],'weekly',$all_transactions['m']['WEEK']));
                                    }elseif (in_array("monthly", $uri_get)) {
                                        echo $this->Html->link(isset($all_transactions[0]['sum(m.COUNT)'])  ? $all_transactions[0]['sum(m.COUNT)'] : 0,array('controller' => 'users', 'action' => 'transaction_detail',$all_transactions['m']['company_id'],$all_transactions['m']['regiones'],'monthly',$all_transactions['m']['MONTHNAME']));
                                    }elseif (in_array("yearly", $uri_get)) {
                                        echo $this->Html->link(isset($all_transactions[0]['sum(m.COUNT)'])  ? $all_transactions[0]['sum(m.COUNT)'] : 0,array('controller' => 'users', 'action' => 'transaction_detail',$all_transactions['m']['company_id'],$all_transactions['m']['regiones'],'yearly',$all_transactions['m']['YEAR']));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('paginationCustom'); ?>
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



    // $(function() {


    //     $(window).on('resize', resize);
    //     $(".sidebar-control").on('click', resize);

    //     function resize() {
    //         drawBar();
    //     }
    // });

    jQuery(document).ready(function() {

        var url_get = window.location.pathname;

        var result = url_get.split('/');
        var i = 0;
        $(".tab").find('button').each(function() {
            if(result.includes("byHour") && i == 0) {
                $(this).addClass('active');
            }else if (result.includes("daily") && i == 1) {
                $(this).addClass('active');
            } else if(result.includes("weekly") && i == 2) {
                $(this).addClass('active');
            }else if(result.includes("monthly") && i == 3) {
                $(this).addClass('active');
            }else if(result.includes("yearly") && i == 4) {
                $(this).addClass('active');
            }
            i++;
        });
        
        var prev = $("li.active").prev().find("a").attr("href");
            $("li.prev").find("a").attr("href", prev);

            var next = $("li.active").next().find("a").attr("href");
            $("li.next").find("a").attr("href", next);
            
            var disable = $("li.active").next().hasClass("next");
            if (disable) {
                $("li.next").find("a").attr("href", null);
            }

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
    });
</script>