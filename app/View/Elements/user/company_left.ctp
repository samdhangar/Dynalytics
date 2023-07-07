<?php
$sessData = getMySessionData();

$recentMenu = $this->requestAction(
    'analytics/getRecent_report'
);
$action_arr = array();
foreach ($recentMenu as $key => $value) {
    $action_arr[] = $value['RecentReports']['action_name'];
}
?>
<aside class="sidebar" style="margin-top:19px">
    <div class="left-aside-container">
        <div class="user-profile-container">
            <div class="user-profile clearfix">
                <div class="admin-user-thumb">
                    <?php
                    echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo')), array('class' => 'img-circle user'));

                    ?>
                </div>
                <div class="admin-user-info">
                    <ul class="user-info">
                        <li><a href="#" style="font-family: 'Raleway' !important" class="text-semibold text-size-large"><?php echo $this->Session->read('Auth.User.first_name'); ?></a></li>
                        <li><a href="#" style="font-family: 'Raleway' !important" class="text-semibold text-size-large"><?php echo getLoginRole(); ?></a></li>
                    </ul>
                    <div class="logout-icon">
                        <?php echo $this->Html->link('', array('controller' => 'users', 'action' => 'logout'), array('icon' => 'icon-exit2')) ?>
                    </div>
                </div>

            </div>

        </div>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active fadeIn" id="menu">
                <ul class="sidebar-accordion">
                    <li class="<?php echo $this->Html->getActiveClass('users', 'dashboard') ?>">
                        <?php echo $this->Html->link(__('Dashboard'), array('controller' => 'users', 'action' => 'dashboard'), array('icon' => 'icon-display4')) ?>
                    </li>
                    <?php if (!empty($sessData['is_display_billing'])) { ?>

                        <li class="<?php echo $this->Html->getActiveOpenClass(array('billing')); ?>">
                            <?php echo $this->Html->link(__('Billing'), array('controller' => 'invoices', 'action' => 'index'), array('icon' => ' icon-file-empty')) ?>
                        </li>
                    <?php } ?>
                    <?php if (($sessData['user_type'] == 'Admin' || $sessData['user_type'] == 'Super') && $sessData['role'] == 'Company') { ?>
                        <!-- <li class="<?php echo $this->Html->getActiveOpenClass(array('Machine')); ?>">
                        <?php echo $this->Html->link(__('Machine'), array('controller' => 'machine', 'action' => 'index'), array('icon' => ' icon-file-empty')) ?>
                    </li> -->
                    <?php } ?>
                    <?php if (($sessData['user_type'] == 'Admin' || $sessData['user_type'] == 'Super') && $sessData['role'] == 'Company') { ?>
                        <li class="  <?php echo $this->Html->getActiveOpenClass(array('Configuration')) ?>">

                            <?php echo $this->Html->link('Configuration', 'javascript:void(0)', array('hasSubMenu' => true, 'class' => $this->Html->getActiveOpenClass(array('Configuration')), 'span' => true, 'icon' => 'icon-cog')); ?>
                            <ul style="">
                                <li class="<?php echo $this->Html->getActiveClass('Configuration', 'bill_activity') ?>">
                                    <?php echo $this->Html->link(__('Denomination Heat Map Configuration'), array('controller' => 'DenominationHeatMap', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                                <li class="<?php echo $this->Html->getActiveClass('Configuration', 'bill_activity') ?>">
                                    <?php echo $this->Html->link(__('Transactions Heat Maps'), array('controller' => 'TransactionHeatMap', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>

                            </ul>
                        </li>


                        <li class="  <?php echo $this->Html->getActiveOpenClass(array('Setting', 'companies', 'regions', 'stations', 'company_branches')) ?>">

                            <?php echo $this->Html->link('Setting', 'javascript:void(0)', array('hasSubMenu' => true, 'class' => $this->Html->getActiveOpenClass(array('Setting', 'companies', 'regions', 'stations', 'company_branches')), 'span' => true, 'icon' => 'icon-cog')); ?>
                            <ul style="">
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('companies')) ?>">
                                    <?php echo $this->Html->link(__('DynaLytics User Management'), array('controller' => 'companies', 'action' => 'index')); ?>
                                </li>
                                <?php if (isCompany()) : ?>
                                    <li class="<?php echo $this->Html->getActiveOpenClass(array('regions')); ?>">
                                        <?php echo $this->Html->link(__('Region Management'), array('controller' => 'regions', 'action' => 'index')) ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveOpenClass(array('stations')); ?>">
                                        <?php echo $this->Html->link(__('DynaCore Station Management'), array('controller' => 'stations', 'action' => 'index')) ?>
                                    </li>

                                    <li class="<?php echo $this->Html->getActiveOpenClass(array('Location')); ?>">
                                        <?php echo $this->Html->link(__('Location Management'), array('controller' => 'Location', 'action' => 'index')) ?>
                                    </li>
                                <?php endif; ?>
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('company_branches')); ?>">
                                    <?php echo $this->Html->link(__('Branch Management'), array('controller' => 'company_branches', 'action' => 'index')) ?>
                                </li>
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('company_branches')); ?>">
                                    <?php echo $this->Html->link(__('Add Bulk data'), array('controller' => 'users', 'action' => 'bulk_users')) ?>
                                </li>

                            </ul>
                        </li>

                    <?php } ?>
                    <li class=" <?php echo $this->Html->get_active_open($action_arr,'popular_reports') ?>">
                        <?php echo $this->Html->link('Popular Reports', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'recentMenu', 'class' => 'acc-parent', 'span' => true, 'icon' => 'icon-history')); ?>
                        <ul id="recent_menu_UL" style="">
                            <?php
                            foreach ($recentMenu as $key => $value) { ?>
                                <li class="<?php echo $this->params['action'] == $value['RecentReports']['action_name'] ? 'active' : ''; ?>">
                                    <?php echo $this->Html->link(__($value['RecentReports']['report_name']), array('controller' => $value['RecentReports']['controller_name'], 'action' => $value['RecentReports']['action_name']), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                            <?php }
                            ?>
                        </ul>
                    </li>
                     <li class="<?php echo $this->Html->get_active_open(array('bill_activity','bill_adjustment','inventory_management','note_count','special_notes_reconciliation'),'inventory',$action_arr) ?>">
                        <?php echo $this->Html->link('Inventory', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'reportMenu', 'class' => $this->Html->get_active_open_class(array('bill_activity','bill_adjustment','inventory_management','note_count','special_notes_reconciliation'),'inventory'), 'span' => true, 'icon' => 'icon-stats-dots')); ?>
                        <ul id="inventory_menu_UL" style="display: none;">
                            <li class="<?php echo $this->params['action'] == 'bill_activity' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Bill Inventory Report'), array('controller' => 'analytics', 'action' => 'bill_activity'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'bill_adjustment' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Inventory Management Report'), array('controller' => 'analytics', 'action' => 'bill_adjustment'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'inventory_management' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Inventory Report'), array('controller' => 'analytics', 'action' => 'inventory_management'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'note_count' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Note Count'), array('controller' => 'analytics', 'action' => 'note_count'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'special_notes_reconciliation' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Special notes Reconciliation'), array('controller' => 'analytics', 'action' => 'special_notes_reconciliation'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                        </ul>
                    </li>

                    <li class="<?php echo $this->Html->get_active_open(array('transaction_details','hour_by_transaction','heat_map','transaction_map'),'transactions',$action_arr) ?>">
                        <?php echo $this->Html->link('Transactions', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'reportMenu', 'class' => $this->Html->get_active_open_class(array('transaction_details','hour_by_transaction','heat_map','transaction_map'),'transactions',$action_arr), 'span' => true, 'icon' => 'icon-stats-growth2')); ?>
                        <ul id="transactions_menu_UL" style="">
                            <li class="<?php echo $this->params['action'] == 'transaction_details' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Transaction Details'), array('controller' => 'analytics', 'action' => 'transaction_details'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>

                            <li class="<?php echo $this->params['action'] == 'hour_by_transaction' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Transactions By Hours'), array('controller' => 'analytics', 'action' => 'hour_by_transaction'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'heat_map' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Denomination Heat Map'), array('controller' => 'analytics', 'action' => 'heat_map'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'transaction_map' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Transaction Map'), array('controller' => 'analytics', 'action' => 'transaction_map'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                        </ul>
                    </li>

                    <li class="<?php echo $this->Html->get_active_open(array('teller_activity','side_log','bill_count','userReport'),'teller_activity',$action_arr) ?>">
                        <?php echo $this->Html->link('Teller Activity', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'reportMenu', 'class' => $this->Html->get_active_open_class(array('teller_activity','side_log','bill_count','userReport'),'teller_activity',$action_arr), 'span' => true, 'icon' => 'icon-user')); ?>
                        <ul id="teller_activity_menu_UL">
                            <li class="<?php echo $this->params['action'] == 'teller_activity' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Teller Activity Report'), array('controller' => 'analytics', 'action' => 'teller_activity'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'side_log' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Active Teller Sign On'), array('controller' => 'analytics', 'action' => 'side_log'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'bill_count' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Audit trail Report'), array('controller' => 'analytics', 'action' => 'bill_count'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'userReport' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('DynaCore User Report'), array('controller' => 'analytics', 'action' => 'userReport'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                        </ul>
                    </li>

                    <li class="  <?php echo $this->Html->get_active_open(array('user_activity','out_of_balance'),'productivity',$action_arr) ?>">
                        <?php echo $this->Html->link('Productivity', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'reportMenu', 'class' => $this->Html->get_active_open_class(array('null'),'productivity',$action_arr), 'span' => true, 'icon' => 'icon-stats-bars3')); ?>
                        <ul id="productivity_menu_UL">
                        <li class="<?php echo $this->params['action'] == '' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Out of Balance'), array('controller' => 'analytics', 'action' => 'out_of_balance'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <li class="<?php echo $this->params['action'] == 'userReport' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('User Activity'), array('controller' => 'analytics', 'action' => 'user_activity'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                        </ul>
                    </li>

                   <li class="  <?php echo $this->Html->get_active_open(array('index','error_warning'),'performance',$action_arr) ?>">
                        <?php echo $this->Html->link('Performance', 'javascript:void(0)', array('hasSubMenu' => true, 'id' => 'reportMenu', 'class' => $this->Html->get_active_open_class(array('index','error_warning'),'performance',$action_arr), 'span' => true, 'icon' => 'icon-stats-growth')); ?>
                        <ul id="performance_menu_UL">
                            <li class="<?php echo $this->params['action'] == 'index' ? 'active' : ''; ?>">
                                <?php echo $this->Html->link(__('Log File Processing'), array('controller' => 'analytics', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                            </li>
                            <?php if (isSuparCompany() || isCompanyAdmin()) : ?>
                                <li class="<?php echo $this->params['action'] == 'error_warning' ? 'active' : ''; ?>">
                                    <?php echo $this->Html->link(__('Error/Warning'), array('controller' => 'analytics', 'action' => 'error_warning'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </li>
                </ul>
                </li>


                <?php if ((isSuparCompany() || isCompanyAdmin()) && !$this->Session->check('Auth.User.BranchDetail')) : ?>
                    <!--                <li class="<?php echo $this->Html->getActiveOpenClass(array('monitoring')) ?>">
                <?php echo $this->Html->link(__('Monitoring'), array('controller' => 'monitoring', 'action' => 'index'), array('icon' => 'fa-line-chart')) ?>                                                                                                </li>-->

                    <!--   <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('Performance_Management')) ?>">
                    <?php echo $this->Html->link('Performance Management', 'javascript:void(0)', array('hasSubMenu' => true,  'span' => true, 'icon' => 'icon-chart')); ?>
                    <ul class="treeview-menu">                        
                        <li class="<?php echo $this->Html->getActiveClass('Performance_Management', 'inventory_by_teller') ?>">
                            <?php echo $this->Html->link(__('Inventory By Teller'), array('controller' => 'analytics', 'action' => 'inventory_by_teller'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                         <li class="<?php echo $this->Html->getActiveClass('Performance_Management', 'inventory_by_hours') ?>">
                            <?php echo $this->Html->link(__('Inventory By Hours'), array('controller' => 'analytics', 'action' => 'inventory_by_hours'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li> 

                    </ul>
                </li>
 -->

                    <!-- <li class="<?php echo $this->Html->getActiveOpenClass(array('users')); ?>">
                    <?php echo $this->Html->link(__('Configurations'), array('controller' => 'users', 'action' => 'configuration'), array('icon' => 'icon-history')); ?>
                </li> -->
                <?php endif; ?>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                    <?php echo $this->Html->link(__('Help'), array('controller' => 'faqs', 'action' => 'lists'), array('icon' => 'icon-bubbles2')) ?>
                </li>

                </ul>
                </section>
</aside>
<script>
    jQuery(document).ready(function() {
        
    });
</script>