<?php
$this->assign('pagetitle', __('Dashboard: %s',$user_detail['User']['first_name']));
$this->Custom->addCrumb(__('%s',$pageDetailWithRole['singularTitle']),$this->request->referer());
$this->Custom->addCrumb(__('Dashboard'));
$this->start('top_links');
echo $this->Html->link('<span>' . __('All Data') . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
$this->end();

?>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'user_dashboard', encrypt($user_detail['User']['id'])), true) ?>" class="procesedFiles">
            <div class="info-box">
                <span class="info-box-icon bg-green">
                    <i class="fa fa-file"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">
                        <?php echo __('Total Processed Files'); ?>
                    </span>
                    <span class="info-box-number">
                        <?php echo $totalPFiles; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'user_dashboard', encrypt($user_detail['User']['id'])), true) ?>" class="errors">
            <div class="info-box">
                <span class="info-box-icon bg-yellow">
                    <i class="fa fa-warning"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">
                        <?php echo __('Total Error/Warnings'); ?>
                    </span>
                    <span class="info-box-number">
                        <?php echo $totalErros; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'user_dashboard', encrypt($user_detail['User']['id'])), true) ?>" class="notification">
            <div class="info-box">
                <span class="info-box-icon bg-navy">
                    <i class="fa fa-cube"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">
                        <?php echo __('Notification Events'); ?>
                    </span>
                    <span class="info-box-number">
                        <?php echo 0; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'user_dashboard' , encrypt($user_detail['User']['id'])), true) ?>" class="messages">
            <div class="info-box">
                <span class="info-box-icon bg-purple">
                    <i class="fa fa-envelope"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">
                        <?php echo __('Unidentified Message'); ?>
                    </span>
                    <span class="info-box-number">
                        <?php echo 0; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="<?php echo Router::url(array('controller' => 'users', 'action' => 'user_dashboard', encrypt($user_detail['User']['id'])), true) ?>" class="transactions">
            <div class="info-box">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-money"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">
                        <?php echo __('No. of Transactions'); ?>
                    </span>
                    <span class="info-box-number">
                        <?php echo $totalTrans; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>

<?php if ($user_detail['User']['role'] == DEALER): ?>
    <div class="row">
        <div class="col-md-6">
            <!-- Custom Tabs (Pulled to the right) -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs pull-right">
                    <li class="active">
                        <?php 
                        $title = __('New');
                        if(!empty($tickets['New'])){
                            $title .= '&nbsp;<i class="badge bg-green">'.count($tickets['New']).'</i>';
                        }
                        
                        echo $this->Html->link($title, '#tab_1-1', array('data-toggle' => 'tab','escape'=>false)); ?>
                    </li>
                    <li class="">
                        <?php 
                        $title = __('Open');
                        if(!empty($tickets['Open'])){
                            $title .= '&nbsp;<i class="badge bg-green">'.count($tickets['Open']).'</i>';
                        }
                        
                        echo $this->Html->link($title, '#tab_2-2', array('data-toggle' => 'tab','escape'=>false)); ?>
                    </li>
                    <li class="">
                        <?php 
                        $title = __('Closed');
                        if(!empty($tickets['Closed'])){
                            $title .= '&nbsp;<i class="badge bg-green">'.count($tickets['Closed']).'</i>';
                        }
                        echo $this->Html->link($title, '#tab_3-2', array('data-toggle' => 'tab','escape'=>false)); ?>
                    </li>
                    <li class="pull-left header">
                        <i class="fa fa-tasks"></i> 
                        <?php echo __('Tickets'); ?>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab_1-1" class="tab-pane active">
                        <?php echo $this->element('ticketTable', array('ticketData' => $tickets['New'], 'ticketType' => 'new')); ?>
                    </div>
                    <div id="tab_2-2" class="tab-pane">
                        <?php echo $this->element('ticketTable', array('ticketData' => $tickets['Open'], 'ticketType' => 'open')); ?>
                    </div>
                    <div id="tab_3-2" class="tab-pane">
                        <?php echo $this->element('ticketTable', array('ticketData' => $tickets['Closed'], 'ticketType' => 'closed')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
echo $this->Html->script('lib/daterangepicker/daterangepicker');
echo $this->Html->css('lib/daterangepicker/daterangepicker-bs3');

?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.daterange').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
//                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 15 Days': [moment().subtract('days', 14), moment()],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Quarter to Date': [moment().subtract('month', 3).startOf('month'), moment().subtract('month').startOf('month')],
                'Year to Date': [moment().subtract('month', 12), moment()],
                'All Dates':[moment().subtract('year', 10), moment()],
//                'Last 30 Days': [moment().subtract('days', 29), moment()],
//                'This Month': [moment().startOf('month'), moment().endOf('month')],
            },
            startDate: moment().subtract('days', 29),
            endDate: moment()
        },
        function (start, end) {
            loader('show');
            var formData = {
                'start_date': start.format('YYYY-MM-DD'),
                'end_date': end.format('YYYY-MM-DD'),
            };            
            jQuery.ajax({
                url: BaseUrl + "users/user_dashboard/<?php echo encrypt($user_detail['User']['id']);?>",
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    jQuery('.transactions').attr('href', response.transactions.url);
                    totalTrans = parseInt(response.transactions.totalTrans);
                    jQuery('.transactions').find('.info-box-number').html(totalTrans);

                    jQuery('.errors').attr('href', response.errors.url);
                    totalErros = parseInt(response.errors.totalErros);
                    jQuery('.errors').find('.info-box-number').html(totalErros);

                    jQuery('.procesedFiles').attr('href', response.errors.url);
                    totalPFiles = parseInt(response.files.totalPFiles);
                    jQuery('.procesedFiles').find('.info-box-number').html(totalPFiles);
                    loader('hide');
                    var displText = jQuery('.ranges li.active').html();
                    console.log(displText);
                    if (displText == 'Custom Range') {
                        displText = start.format('YYYY-MM-DD') + " to " + end.format('YYYY-MM-DD');
                    }
                    jQuery('.daterange span').html(displText);

                },
                error: function () {
                    loader('hide');
                }
            });
        });
    });
</script>