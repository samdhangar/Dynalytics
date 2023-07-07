<?php
$this->assign('pagetitle', __('Dashboard'));
$this->Custom->addCrumb(__('Dashboard'));
$this->start('top_links');
//echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Report.DashboardErrors'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'fa-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right', 'escape' => false));
/*echo $this->Html->link('<span>' . __(!empty($this->Session->read('Dashboard.Filedata')) ? getReportFilter($this->Session->read('Dashboard.Filedata')) : 'Last 7 Days') . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));*/
// echo $this->Html->link('<span>' . __(getReportFilter($this->Session->read('Dashboard.Filter'))) . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));
/*
 echo $this->Html->link('<span>' . __(!empty($this->Session->read('Dashboard.Filter')) ? getReportFilter($this->Session->read('Dashboard.Filter')) : 'Last 7 Days') . '</span>', 'javascript:void(0)', array('data-toggle' => 'tooltip', 'title' => __('Select Date'), 'icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm daterange pull-right marginleft', 'escape' => false));*/
if (isCompany()) {
  echo $this->Html->link(__('Export CSV'), array('controller' => 'export', 'action' => 'dashboard'), array('title' => __('Export CSV'), 'icon' => 'icon-download', 'class' => 'btn btn-primary btn-sm pull-right marginleft', 'escape' => false));
}
// if (isCompany() || isAdminDealer() || isSuparDealer()) :
//   echo $this->Html->link(__('Inventory Management'), array('controller' => 'analytics', 'action' => 'inventory_management'), array('icon' => 'icon-calendar', 'class' => 'btn btn-primary btn-sm'));
// endif;
$this->end();

echo $this->Html->script('user/moment');
echo $this->Html->script('user/chart');
// echo $this->Html->script('user/highcharts');
echo $this->Html->script('user/daterangepicker');
?>
<!-- <style>
  .box50 {
    border-bottom: 1px solid;
    /* display: flex;
    align-items: center;
    padding-left: 80px; */
  }

  .bordercls {
    border-right: 1px solid;
  }

  .card-body {
    flex: 1 1 auto;
    padding: 1.5rem;
    color: var(--cui-card-color, unset);
  }

  .col {
    flex: 1 0 0%;
  }

  .vr {
    display: flex;
    flex: 0 0 1px;
    width: 1px;
    padding: 0 !important;
    margin: 0;
    color: var(--cui-vr-color, inherit);
    background-color: currentColor;
    border: 0;
    opacity: .25;
  }

  .my-class {
    color: black !important;
  }

  .panel.da-common-block {
    /* background: #eac1c4; */
    border-radius: 5px;
    transition: all 300ms;
    transform: translateY(5px) scale(0.95);
    border: 1px solid transparent;
    box-shadow: none;
  }

  .panel.da-common-block:hover {
    box-shadow: 5px 5px 11px #c7c7c7;
    transform: translateY(-5px) scale(1);
    border-color: #000;
  }
  .box50-img {
    background-size: contain;
    background-image: url('<?php echo Router::url('/', true) ?>/img/dashboard-station.png');
    height: 50px;
    width: 80px;
    position: absolute;
    left: 80px;
    right: auto;
    background-repeat: no-repeat;
  }
  .box50-img.img2{
    background-image: url('<?php echo Router::url('/', true) ?>/img/dashboard-transaction.png');
    left: 30px;
  }
</style> -->
<style>
  #dasboard-card * {
    margin: 0;
    padding: 0;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
  }

  #dasboard-card a {
    background-color: transparent;
  }

  #dasboard-card a:active,
  #dasboard-card a:hover {
    outline: 0;
  }

  #dasboard-card .row {
    margin-right: -15px;
    margin-left: -15px;
  }

  #dasboard-card .col-lg-3,
  #dasboard-card .col-md-6,
  #dasboard-card .col-xs-3 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
  }

  #dasboard-card .col-xs-3 {
    float: left;
    width: 20%;
  }

  #dasboard-card .col-xs-9 {
    width: 75%;
    float: left;
  }

  #dasboard-card .clearfix:after {
    clear: both;
  }

  #dasboard-card .clearfix:before,
  #dasboard-card .clearfix:after {
    display: table;
    content: " ";
  }

  #dasboard-card .panel {
    margin-bottom: 10px;
    background-color: #fff;
    border: 1px solid transparent;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
  }

  #dasboard-card .panel-footer {
    display: flex;
    padding: 10px 15px;
    background-color: #f5f5f5;
    border-top: 1px solid #ddd;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
  }

  #dasboard-card .panel-heading {
    background-color: #fff;
    padding: 25px;
    /* height: 100px;
    background-color: turquoise;
    padding: 10px 15px;
    border-bottom: 1px solid transparent;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px; */
  }

  #dasboard-card .panel-green {
    /* border: 2px dashed #398439; */
    border: 2px solid #3984397a;
    box-shadow: 5px 5px 11px #3984397a;
  }

  #dasboard-card .panel-green .panel-heading {
    background-color: #398439;
  }

  #dasboard-card .green {
    color: #398439;
  }

  #dasboard-card .blue {
    color: #fff;
  }

  #dasboard-card .red {
    color: #ce7f7f;
  }

  #dasboard-card .panel-primary {
    /* border: 2px dashed #337ab7; */
    border: 2px solid #fff;
    box-shadow: 5px 5px 11px #fff;
  }

  #dasboard-card .panel-primary .panel-heading {
    background-color: #fff;
  }

  #dasboard-card .yellow {
    color: #ffcc00;
  }

  #dasboard-card .panel-yellow {
    border: 2px dashed #ffcc00;
  }

  #dasboard-card .panel-yellow .panel-heading {
    background-color: #ffcc00;
  }

  #dasboard-card .panel-red {
    border: 2px dashed #ce7f7f;
  }

  #dasboard-card .panel-red .panel-heading {
    background-color: #ce7f7f;
  }

  #dasboard-card .huge {
    font-size: 30px;
  }

  #dasboard-card .panel-heading {
    color: #fff;
    box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;
  }

  #dasboard-card .pull-left {
    float: left !important;
  }

  #dasboard-card .pull-right {
    float: right !important;
  }

  #dasboard-card .text-right {
    text-align: right;
  }

  #dasboard-card .under-number {
    font-size: 15px;
    margin-top: 0px;
    color: black;
  }

  @media (min-width: 992px) {
    #dasboard-card .col-md-6 {
      float: left;
      width: 50%;
    }
  }

  @media (min-width: 1200px) {
    #dasboard-card .col-lg-3 {
      float: left;
      width: 30%;
    }
  }

  #dasboard-card .box50-img {
    width: 5rem;
    height: 5rem;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
    box-shadow: 0 5px 10px #c4cdeb;
    background: linear-gradient(to bottom right, #6259ca 0%, rgba(98, 89, 202, 0.6) 100%) !important;
  }

  #dasboard-card .box50-img-second {
    width: 5rem;
    height: 5rem;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
    box-shadow: 0 5px 10px #c4cdeb;
    background: linear-gradient(to bottom right, #f1bf64 0%, #f71d36 100%) !important;
  }

  #dasboard-card .vr {
    display: flex;
    flex: 0 0 1px;
    width: 1px;
    padding: 0 !important;
    margin: 0;
    color: var(--cui-vr-color, inherit);
    background-color: currentColor;
    border: 0;
    opacity: .25;
  }

  .my-class-color {
    color: black !important;
    /* font-weight: bold; */
    font-weight: normal;
  }

  .my-class {
    font-size: 25px;
    color: black !important;
    /* font-weight: bold; */
    font-weight: normal;
  }

  /* #dasboard-card .box50-img.img2{
    background-image: url('<?php echo Router::url('/', true) ?>/img/dashboard-transaction.png');
    left: 30px;
  } */
</style>
<div class="row">
  <div class="col-md-12 col-sm-12 form-group row">
    <div class="box box-primary">
      <div class="box-body">

        <?php

        echo $this->Form->create('Analytic', array('url' => array('controller' => 'users', 'action' => 'dashboard'), 'id' => 'analyticForm', 'inputDefaults' => array('class' => 'form-control', 'div' => array('class' => 'form-group'))));

        // if ((!isCompany()) && ($sessionData['user_type'] != 'Region')) :

        //   echo $this->Form->input('company_id', array('onchange' => 'getResion(this.value)', 'id' => 'analyCompId', 'label' => __('Financial Institution: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
        // endif;

        // echo $this->Form->input('station', array('onchange' => 'formSubmit()','type' => 'select', 'id' => 'analyStationId', 'label' => __('DynaCore Station ID: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
        // echo "<label for='analyBranchId' >&nbsp;</label><br>";
        // echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary'));
        // echo $this->Html->link(__('Reset Filter'), array('controller' => 'users', 'action' => 'dashboard', 'all'), array('title' => __('Reset Filter'), 'icon' => 'fa-refresh', 'class' => 'btn btn-default marginleft', 'escape' => false));
        echo $this->Form->end();
        ?>
      </div>
    </div>

  </div>
</div>
<?php if (isCompany()) : ?>
  <div id="dasboard-card">
    <div class="row" style="margin-left: 5px;">
      <div class="col-lg-3 col-md-6">
        <div class="panel">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-8 text-left pl-2 pr-2">
                <h6 class="under-number">Total DynaCore Stations</h6>
                <div class='huge'><?php echo $this->Html->link(isset($stationCount) ? round($stationCount,0) : 0, array('controller' => 'users', 'action' => 'dashboard_data_station', 'all'), array('class' => 'my-class-color')) ?></div>
                <div class="col-md-6" style="margin-top: 15px;">
                  <div class="fs-5 fw-semibold" style="font-size: 15px !important;color: black;">Active</div>
                  <div class="text-uppercase text-medium-emphasis small" style="font-size: 18px;"><i class="fa icon-primitive-dot" style="color: green;"></i><?php echo $this->Html->link(isset($activestationCount) ? $activestationCount : 0, array('controller' => 'users', 'action' => 'dashboard_data_station', 'active'), array('class' => 'my-class')) ?></div>
                </div>
                <div class="vr"></div>
                <div class="col-md-6" style="margin-top: 15px;">
                  <div class="fs-5 fw-semibold" style="font-size: 15px !important;color: black;">Inactive</div>
                  <div class="text-uppercase text-medium-emphasis small" style="font-size: 18px;"><i class="fa icon-primitive-dot" style="color: red;"></i><?php echo $this->Html->link(isset($inactivestationCount) ? $inactivestationCount : 0, array('controller' => 'users', 'action' => 'dashboard_data_station', 'inactive'), array('class' => 'my-class')) ?></div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="box50-img">
                  <?php echo $this->Html->image('dashboard-station.svg', array('alt' => 'CakePHP', 'style' => 'width:30px')); ?>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="panel-footer">
              <div class="clearfix"></div>
            </div> -->
          </a>
        </div>
      </div>

      <!-- ********************************************************************************************************* -->
      <div class="col-lg-3 col-md-6">
        <div class="panel">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-8 text-left pl-2 pr-2">
                <h6 class="under-number">Avg Transactions per Teller</h6>
                <div class='huge'><?php echo $this->Html->link(!is_nan($totalAvg) ? number_format(round($totalAvg,0),0) : 0, array('controller' => 'users', 'action' => 'dashboard_data_transaction', 'all'), array('class' => 'my-class-color')) ?></div>
                <div class="col-md-6" style="margin-top: 15px;">
                  <div class="fs-5 fw-semibold" style="font-size: 15px !important;color: black;">Tellers Above Avg</div>
                  <div class="text-uppercase text-medium-emphasis small" style="font-size: 18px;"><i class="fa icon-arrow-up5" style="color: green;"></i><?php echo $this->Html->link(isset($avgCount['aboveavg']) ? $avgCount['aboveavg'] : 0, array('controller' => 'users', 'action' => 'dashboard_data_transaction', 'above'), array('class' => 'my-class')) ?></div>
                </div>
                <div class="vr"></div>
                <div class="col-md-6" style="margin-top: 15px;">
                  <div class="fs-5 fw-semibold" style="font-size: 15px !important;color: black;">Tellers Below Avg</div>
                  <div class="text-uppercase text-medium-emphasis small" style="font-size: 18px;"><i class="fa icon-arrow-down5" style="color: red;"></i><?php echo $this->Html->link(isset($avgCount['belowavg']) ? $avgCount['belowavg'] : 0, array('controller' => 'users', 'action' => 'dashboard_data_transaction', 'below'), array('class' => 'my-class')) ?></div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="box50-img-second">
                  <?php echo $this->Html->image('dashboard-transaction.svg', array('alt' => 'CakePHP', 'style' => 'width:30px')); ?>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="panel-footer">
            </div> -->
          </a>
        </div>
      </div>
      <!-- ******************************************************************************************************** -->
      <div class="col-lg-3 col-md-6">
        <div class="panel">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-8 text-left pl-2 pr-2">
                <h6 class="under-number">Total Transactions</h6>
                <div class='huge' style="font-size: 20px !important;"><?php echo $this->Html->link(isset($tranctionDataCount) ? number_format(round($tranctionDataCount,0),0) .'  [$'.number_format($tranctionDataAmount,2).']' : 0  , array('controller' => 'users', 'action' => 'allTransactionData', 'byHour'), array('class' => 'my-class-color')) ?></div><br>
                <h6 class="under-number">Total Deposits</h6>
                <div class='huge' style="font-size: 20px !important;"><?php echo $this->Html->link(isset($depositeCount) ? number_format(round($depositeCount,0),0) .'  [$'.number_format($DepositeData,2).']' : 0, array('controller' => 'users', 'action' => 'allTransactionData', 'byHour'), array('class' => 'my-class-color')) ?></div><br>
                <h6 class="under-number">Total Withdrawals</h6>
                <div class='huge' style="font-size: 20px !important;"><?php echo $this->Html->link(isset($withdrawCount) ? number_format(round($withdrawCount,0),0) .'  [$'.number_format($withdrawData,2).']' : 0, array('controller' => 'users', 'action' => 'allTransactionData', 'byHour'), array('class' => 'my-class-color')) ?></div><br>
                <h6 class="under-number">Total Inventory</h6>
                <div class='huge' style="font-size: 20px !important;"><?php echo $this->Html->link(isset($InventoryCount) ? number_format(round($InventoryCount,0),0) .'  [$'.number_format($InventoryData,2).']' : 0, array('controller' => 'users', 'action' => 'allTransactionData', 'byHour'), array('class' => 'my-class-color')) ?></div>
              </div>
              <div class="col-xs-3">
                <div class="box50-img-second">
                  <?php echo $this->Html->image('dashboard-transaction.svg', array('alt' => 'CakePHP', 'style' => 'width:30px')); ?>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="panel-footer">
            </div> -->
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<div class="row">
  <div class="panel-heading">

    <h5 class="panel-title  graphTitle h4" style="margin-left: 15px;"><?php

                                                                      echo getReportFilter($this->Session->read('Dashboard.Filter'));

                                                                      ?></h5>
  </div>
</div>
<?php if (!isSupportDealer() && !isCompany()) : ?>

  <div class="col-md-3 procesedFiles">
    <div class="panel panel-flat">
      <div class="panel-body p-b-10">
        <div class="row">
          <div class="col-md-8 col-xs-8">
            <div class="text-size-huge text-regular text-blue-dark text-semibold no-padding no-margin m-t-5 m-b-10"><?php echo $totalPFiles; ?></div>
            <span class="text-muted"><?php echo __('Total Processed Files'); ?></span>
          </div>
          <div class="col-md-4 col-xs-4">
            <i class="icon-magazine icon-4x icon-light"></i>
          </div>
        </div>
      </div>

    </div>
  </div>


  <div class="col-md-3 errors">
    <div class="panel panel-flat">
      <div class="panel-body p-b-10">
        <div class="row">
          <div class="col-md-8 col-xs-8">
            <div class="text-size-huge text-regular text-danger-dark text-semibold no-padding no-margin m-t-5 m-b-10"><?php echo $totalErros; ?></div>
            <span class="text-muted"><?php echo __('Total Error/Warnings'); ?></span>
          </div>
          <div class="col-md-4 col-xs-4">
            <i class="icon-warning icon-4x icon-light"></i>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="col-md-3 notification">
    <div class="panel panel-flat">
      <div class="panel-body p-b-10">
        <div class="row">
          <div class="col-md-8 col-xs-8">
            <div class="text-size-huge text-regular text-success-dark text-semibold no-padding no-margin m-t-5 m-b-10"> 
              <?php  echo $tickets['New'] + $tickets['Closed'] + $tickets['Open'];//!empty($tickets['New']) ? $tickets['New'] : 0 + !empty($tickets['Open']) ? $tickets['Open'] : 0 + !empty($tickets['Closed']) ? $tickets['Closed'] : 0; ?></div>
            <span class="text-muted"><?php echo __('Notification Events'); ?></span>
          </div>
          <div class="col-md-4 col-xs-4">
            <i class="icon-notification2 icon-4x icon-light"></i>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php if (!isDealer() && !isCompany()) : ?>

    <div class="col-md-3 messages">
      <div class="panel panel-flat">
        <div class="panel-body p-b-10">
          <div class="row">
            <div class="col-md-8 col-xs-8">
              <div class="text-size-huge text-regular text-amber-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo $totalUnIdentiMsg; ?></div>
              <span class="text-muted"><?php echo __('Unidentified Message'); ?></span>
            </div>
            <div class="col-md-4 col-xs-4">
              <i class="icon-envelop3 icon-4x icon-light"></i>
            </div>
          </div>
        </div>

      </div>
    </div>



  <?php endif; ?>


  <?php if (isDealer()) : ?>

    <div class="col-md-3 clients">
      <div class="panel panel-flat">
        <div class="panel-body p-b-10">
          <div class="row">
            <div class="col-md-8 col-xs-8">
              <div class="text-size-huge text-regular text-amber-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo !empty($totalClients) ? $totalClients : '0'; ?></div>
              <span class="text-muted"><?php echo __('No. of Client'); ?></span>
            </div>
            <div class="col-md-4 col-xs-4">
              <i class="icon-user icon-4x icon-light"></i>
            </div>
          </div>
        </div>

      </div>
    </div>

  <?php endif; ?>
  <?php if (isDealer() || isSuparAdmin()) : ?>

<div class="col-md-3 clients">
  <div class="panel panel-flat">
  <a href="<?php echo $this->webroot.'users/allTickets/new';?>" class="my-class-color">
    <div class="panel-body p-b-10">
      <div class="row">
        <div class="col-md-8 col-xs-8">
          <div class="text-size-huge text-regular text-blue-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo !empty($tickets['New']) ? $tickets['New'] : '0'; ?></div>
          <span class="text-muted"><?php echo __('Total New Ticket'); ?></span>
        </div>
        <div class="col-md-4 col-xs-4">
          <i class="icon-ticket icon-4x icon-light"></i>
        </div>
      </div>
    </div>
  </a>
  </div>
</div>

<?php endif; ?>
<?php if (isDealer() || isSuparAdmin()) : ?>

<div class="col-md-3 clients">
  <div class="panel panel-flat">
    <a href="<?php echo $this->webroot.'users/allTickets/open';?>" class="my-class-color">
      <div class="panel-body p-b-10">
        <div class="row">
          <div class="col-md-8 col-xs-8">
            <div class="text-size-huge text-regular text-amber-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo !empty($tickets['Open']) ? $tickets['Open'] : '0'; ?></div>
            <span class="text-muted"><?php echo __('Total Open Ticket'); ?></span>
          </div>
          <div class="col-md-4 col-xs-4">
            <i class="icon-ticket icon-4x icon-light"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>

<?php endif; ?>
<?php if (isDealer() || isSuparAdmin()) : ?>

<div class="col-md-3 clients">
  <div class="panel panel-flat">
  <a href="<?php echo $this->webroot.'users/allTickets/closed';?>" class="my-class-color">
    <div class="panel-body p-b-10">
      <div class="row">
        <div class="col-md-8 col-xs-8">
          <div class="text-size-huge text-regular text-success-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo !empty($tickets['Closed']) ? $tickets['Closed'] : '0'; ?></div>
          <span class="text-muted"><?php echo __('Total Closed Ticket'); ?></span>
        </div>
        <div class="col-md-4 col-xs-4">
          <i class="icon-ticket icon-4x icon-light"></i>
        </div>
      </div>
    </div>
</a>
  </div>
</div>

<?php endif; ?>

  <?php if (isCompanyAdmin()) : ?>

    <div class="col-md-3 transactions">
      <div class="panel panel-flat">
        <div class="panel-body p-b-10">
          <div class="row">
            <div class="col-md-8 col-xs-8">
              <div class="text-size-huge text-regular text-danger-dark text-semibold no-padding no-margin m-t-5 m-b-10"> <?php echo $totalTrans; ?></div>
              <span class="text-muted"><?php echo __('Number of Transactions'); ?></span>
            </div>
            <div class="col-md-4 col-xs-4">
              <i class="icon-coin-dollar icon-4x icon-light"></i>
            </div>
          </div>
        </div>

      </div>
    </div>


  <?php endif; ?>
  </div>
<?php endif; ?>

<?php if (isDealer() || isSuparAdmin()) : ?>
  <!-- <div class="row">
    <div class="col-md-12">

      <div class="panel panel-flat">
        <div class="panel-heading">
          <h5 class="panel-title">
            <?php echo __('Tickets'); ?>
          </h5>
        </div>
        <div class="panel-body">
          <div class="nav-tabs-custom" id="ticketTable">
            <?php //echo $this->element('ticketAjaxTable') ?>
          </div>
        </div>
      </div>


    </div>
  </div> -->
<?php endif; ?>

<div class="row">

  <div class="col-md-6 col-sm-6 col-xs-12">
    <div>


      <div class="header-content">
        <!-- <div  class="page-title">Number of Transactions</div>
  <div class="elements">
    <a id="export2"><i class="fa icon-download position-left"></i> Export</a>
  </div> -->

        <div id="container"></div>
      </div>


      <!-- <div class="panel-body text-center" id="chart-div4">
        <div class="display-inline-block" id="c3-pie-chart"></div>

      </div> -->
    </div>
  </div>
  <div class="col-md-6 col-sm-6 col-xs-12">

    <div>

      <div class="header-content">

        <div id="containerPie"></div>
      </div>


      <!-- <div class="panel-body" id="chart-div3">

        <div class="chart" id="google-bar"></div>
      </div> -->
    </div>
  </div>
</div>
&nbsp; &nbsp;
<?php if (isDealer()):?>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">

    <div>

      <div class="header-content">

        <div id="container_drive"></div>
      </div>


      <!-- <div class="panel-body" id="chart-div3">

        <div class="chart" id="google-bar"></div>
      </div> -->
    </div>
  </div>
</div>
<?php endif;?>
</div>
<?php
echo $this->Html->script('/app/webroot/js/charts/d3/d3.min');
echo $this->Html->script('/app/webroot/js/charts/c3/c3.min');
echo $this->Html->script('/app/webroot/js/charts/jsapi');
echo $this->Html->script('user/chart');
echo $this->Html->script('https://www.gstatic.com/charts/loader.js');


?>

<?php
// echo $this->Html->script('user/chart');

?>
<script type="text/javascript">
  'use strict';
  <?php if (isDealer()):?>
  google.charts.load('current', {
    'packages': ['corechart']
  });
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable(
      <?php echo $errorData_Arr; ?>
    );
    var chart = new google.visualization.PieChart(document.getElementById('containerPie'));
    chart.draw(data, options);
  }
  <?php endif; ?>
</script>
</script>

<script type="text/javascript">
  jQuery(document).ready(function() {
    var data = '<?php echo $temp; ?>';
    var xAxisDates = '<?php echo $xAxisDates; ?>';

    var tickInterval = '<?php echo $tickInterval; ?>';
    // var options = {
    //   name: 'Errors',
    //   title: 'Errors',
    //   xTitle: 'Error Date',
    //   yTitle: 'No. of Errors',
    //   id: '#container3'
    // };
    // // pieChart(pieData, pieTitle, pieName, '#containerPie');

    // var pieData = '<?php echo $pie_data; ?>';
    // // pieChart_details(pieData, '#c3-pie-chart');
    var startDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.start_date') ?>', 'YYYY-MM-DD');
    var endDate = moment('<?php echo $this->Session->read('Report.DashboardErrors.end_date') ?>', 'YYYY-MM-DD');
    //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');

    var data1 = '<?php echo $newchartdata; ?>';
    var deposite_data = '<?php echo $depositechartdata; ?>';
    var withdraws_data = '<?php echo $withdrawschartdata; ?>';
    var inventory_data = '<?php echo $Inventorychartdata; ?>';
      // var data2 = '  [[1638950400000,100],[1638864000000,212],[1638777600000,252],[1638518400000,486],[1638432000000,307],[1638345600000,242],[1638259200000,194],[1638172800000,231],[1637913600000,158],[1637740800000,247],[1637654400000,130],[1637568000000,156],[1637308800000,306],[1637222400000,243],[1637136000000,203],[1637049600000,167],[1636963200000,211],[1636704000000,212],[1636531200000,109],[1636444800000,91],[1636358400000,132],[1636095600000,176],[1636009200000,176],[1635922800000,246],[1635836400000,177],[1635750000000,245],[1635490800000,284],[1635404400000,52],[1635318000000,139],[1635231600000,116],[1635145200000,164],[1634886000000,286],[1634799600000,175],[1634713200000,128],[1634626800000,114],[1634540400000,190],[1634281200000,129],[1634194800000,113],[1634108400000,80],[1634022000000,87],[1633676400000,202],[1633590000000,91],[1633503600000,500],[1633417200000,400],[1633330800000,700],[1633071600000,388],[1632985200000,199],[1632898800000,115],[1632812400000,3]]';
    var options1 = {
      name: 'Transactions',
      title: '<?php echo __('Transaction Details'); ?>',
      xTitle: '<?php echo __('Transaction Date'); ?>',
      yTitle: '<?php echo __('Number of Transactions'); ?>',
      id: '#container',
    };

    var options2 = {
      name: 'Deposits',
    };
    var options3 = {
      name: 'Withdrawals',
    };
    var options4 = {
      name: 'Inventory',
    };
    // console.log(xAxisDates);
    lineChart1(data1,deposite_data, withdraws_data, inventory_data, xAxisDates, tickInterval, options1, options2, options3, options4);

    var data_hardware = '<?php echo $errorData_Arr; ?>';
    var tranction_drive_Arr = '<?php echo $transaction_drive_json; ?>';
    // drawChart3(pieData,'piechart');
    pieChartnew(data_hardware, 'Type of Errors', 'Error', '#container_drive');
    // container_drive containerPie
    pieChartnew(tranction_drive_Arr, 'Transactions by Location Type', 'Total Transancation', '#containerPie');
    // drawBar(data_hardware);

    //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');


    /* var data2 = '<?php echo $sentTemp; ?>';
     var options2 = {
         name: 'Transaction',
         title: 'Transaction Denom',
         xTitle: 'Transaction Time',
         yTitle: 'No. Of Denom',
         id: '#container2'
     };
    // alert(data2);

     var xAxisDatesTime = '<?php echo $xAxisDatesTime; ?>';
     multiLineChart(data2, xAxisDatesTime, tickInterval, options2);*/
    var extraParams = {
      //                pieCatTitle: pieCatTitle,
      //                pieClientTitle: pieClientTitle,
      charts: {
        0: {
          type: 'multiLine',
          name: 'Transaction',
          title: 'Transaction',
          data: 'transactionDetails',
          xTitle: 'Transaction Date',
          yTitle: 'Transaction',
          id: '#container1'
        }
      }

    };
    addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart', '', extraParams);
    //            addDateRange(startDate, endDate, "users/dashboard", 'multiLineChart');
  });
</script>


<script type="text/javascript">
  jQuery(document).ready(function() {
    var startDate = moment().subtract('days', 7);
    var endDate = moment();
    addDateRange(startDate, endDate, 'dashboardData');


  });

  // function formSubmit(){
  //       $("#daterange").val('');
  //       $('#analyticForm').submit();
  //   }
</script>