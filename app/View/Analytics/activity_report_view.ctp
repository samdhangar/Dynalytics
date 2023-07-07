<?php
// debug($activityReport);exit;
$this->assign('pagetitle', __('Activity Report Detail Page'));
$this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
$this->Custom->addCrumb(__('# %s',$activityReport['ActivityReport']['id']),null,array('title'=>__('Activity Report Id')));
//$this->start('top_links');
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body table-responsive userViewPage activityReport">
                <div class="col-xs-12 col-sm-10 detailBox">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 innerBox">
                            <div class="dl-horizontal">
                                <ul>
                                    <li>
                                        <span class="col-xs-12">
                                            <div class="row">
                                                <?php
                                                echo __('Activity Detail');
                                                ?>
                                            </div>
                                        </span>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="innreicons">
                                        <i><?php echo __('Branch Name:'); ?></i>
                                    </li>
                                    <li>
                                        <?php
                                        echo $activityReport['FileProccessingDetail']['Branch']['name'];
                                        ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="innreicons">
                                        <i><?php echo __('DynaCore Station ID:'); ?></i>
                                    </li>
                                    <li>
                                        <?php
                                        echo $activityReport['ActivityReport']['station'];
                                        ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="innreicons">
                                        <i><?php echo __('Date:'); ?></i>
                                    </li>
                                    <li>
                                        <?php
                                        echo showdate($activityReport['FileProccessingDetail']['file_date']);
                                        ?>
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12 innerBox">

                            <ul class="dl-horizontal">
                                <li>
                                    <?php
                                    echo __('Activity Reports');
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Bills Activity:'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo $this->Html->link(__('Click here to go'),array('controller'=>'analytics','action'=>'bill_activity',encrypt($activityReport['ActivityReport']['id'])),array('escape'=>false));
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Inventory:'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo $this->Html->link(__('Click here to go'),array('controller'=>'analytics','action'=>'inventory_management',encrypt($activityReport['ActivityReport']['id'])),array('escape'=>false));
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Net Cash Usage Activity:'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo $this->Html->link(__('Click here to go'),array('controller'=>'analytics','action'=>'net_cash_usage',encrypt($activityReport['ActivityReport']['id'])),array('escape'=>false));
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Side Activity :'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo $this->Html->link(__('Click here to go'),array('controller'=>'analytics','action'=>'side_activity',encrypt($activityReport['ActivityReport']['id'])),array('escape'=>false));
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Teller Activity:'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo $this->Html->link(__('Click here to go'),array('controller'=>'analytics','action'=>'teller_activity',encrypt($activityReport['ActivityReport']['id'])),array('escape'=>false));
                                    ?>
                                </li>
                            </ul>
                            <ul>
                                <li class="innreicons">
                                    <i><?php echo __('Verificationrequired:'); ?></i>
                                </li>
                                <li>
                                    <?php
                                    echo __('Click here to go');
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
