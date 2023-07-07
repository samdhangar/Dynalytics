<?php
$logUserName = $this->Session->read('Auth.User.first_name');
?>
<style>
    .col-md-2.header_label {
    color: #fff;
}
.col-md-1.header_label {
    color: #fff;
}
    </style>
    <header class="main-nav clearfix" style="height:69px;">  
        <div class="navbar-left pull-left" style="height: 69px;">
            <div class="clearfix">
                <ul class="left-branding pull-left">
                    <li><span class="left-toggle-switch visible-handheld"><i class="icon-menu7"></i></span></li>
                    <li>
                                  <?php
    $actionArr = array('controller' => 'users', 'action' => 'dashboard');

    echo $this->Html->link($this->Html->image('logo_w.png', array('class' => 'img-responsive')), $actionArr, array('escape' => false, 'class' => 'logo'));
    ?> 
                    </li>
                </ul>
                   
            </div>
        </div>
         <?php
            // echo $this->Form->create('Analytic', array('url'=>array('controller'=>'analytics','action'=>'setDataFilter'),'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group','style'=> 'margin-left:252px'))));
            echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.GlobalFilter'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange  marginleft pull-left', 'escape' => false ,'style' =>'margin-top: 20px;'));
            
            // Start header filter form
            echo $this->Form->create('Analytic', array('url' => array('controller' => 'users', 'action' => 'setHeaderFilter'), 'id' => 'analyticHeaderForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group col-md-12'))));
                if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :
                    echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-1 header_label', 'style' =>'margin-top: -19px;')));
                endif;
              
                if ($sessionData['user_type'] != 'Region' and $sessionData['user_type'] != 'Branch') :
                    echo $this->Form->input('regiones', array('onchange' => 'getBranches(this.value)', 'id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-1 header_label')));
                endif;
                echo $this->Form->input('branch_id', array('onchange' => 'getStations(this.value)', 'id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2 header_label')));
                echo $this->Form->input('station', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-2 header_label', 'style' =>'')));
                echo $this->Html->link(__('Reset'), array('controller' => 'users', 'action' => 'setHeaderFilter', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-danger marginleft col-md-1', 'escape' => false,'style' =>'margin-top: 19px;width: 65px;'));
            echo $this->Form->end();
        ?>
        <div class="navbar-right pull-right" style="margin-top: 10px;">
            <div class="clearfix">              
                <ul class="pull-right top-right-icons">
                <?php if (isCompany() && $this->params['controller'] == 'analytics') : ?>
                    <li><a class="btn-top-search"  id="helpIcon" data-id= <?php echo $this->params["action"];?>><i class="fa icon-question7"></i></a></li>                 
                <?php endif; ?>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="btn-user dropdown-toggle hidden-xs" data-toggle="dropdown">
                    
                         <?php
                            echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo')), array('class' => 'img-circle user'));
                        ?></a>
                        <a href="#" class="dropdown-toggle visible-xs"><i class="icon-more"></i></a>
                        <div class="dropdown-menu"> 
                            <div class="text-center">
                            <?php 
                                echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo'), false, false), array('class' => 'img-circle img-70')); 
                            ?></div>
                            <h5 class="text-center"><b>Hi!  <?php echo $logUserName; ?>l</b></h5>
                        <center>
                            <small>
                                    <?php
                                    $lastLoginDate = str_replace('k', 'sup', showdatetime($this->Session->read('Auth.User.last_login_time'), 'N/A', 'd<k>S</k>M,Y H:i:s'));
                                    ?>
                                    <?php echo ($lastLoginDate == 'N/A') ? __('First Time Login') : __('Last login: ') . $lastLoginDate ?>
                                </small>
                        </center>
                            <ul class="more-apps">
                                
                                <li><?php echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'profile'), array('icon' => 'icon-profile')); ?></li>
                                
                                <li><?php echo $this->Html->link('Change Password', array('controller' => 'users', 'action' => 'change_password'), array('icon' => 'icon-lock5')); ?></li>
                            </ul>
                            <div class="text-center"><?php echo $this->Html->link('Sign out', array('controller' => 'users', 'action' => 'logout'), array('class' => 'btn btn-sm btn-info', 'icon' => 'icon-exit3 i-16 position-left')) ?></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <?php
echo $this->Html->script('user/chart');
?>
<script type="text/javascript">
jQuery(document).ready(function () {


var startDate = moment('<?php  echo $this->Session->read('Report.GlobalFilter.start_date') ?>', 'YYYY-MM-DD');
var endDate = moment('<?php  echo $this->Session->read('Report.GlobalFilter.end_date') ?>', 'YYYY-MM-DD');
// var startDate = moment('<?php //echo $this->Session->read('Report.GlobalFilter.start_date') ?>', 'YYYY-MM-DD');
// var endDate = moment('<?php //echo $this->Session->read('Report.GlobalFilter.end_date') ?>', 'YYYY-MM-DD');

addDateRange(startDate, endDate, "analytics/setDataFilter");


});
</script>