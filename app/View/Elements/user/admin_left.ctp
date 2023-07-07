<?php $analyticParams = isset($this->params['named']['type']) ? 'type:' . $this->params['named']['type'] : ''; ?>
<aside class="sidebar" style="margin-top:19px">
    <div class="left-aside-container">
        <div class="user-profile-container">
            <div class="user-profile clearfix">
                <div class="admin-user-thumb">
                    <?php  echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo')), array('class' => 'img-circle user')); ?>
                </div>
                <div class="admin-user-info">
                    <ul class="user-info">
                        <li>
                            <a href="#" style="font-family: 'Raleway' !important" class="text-semibold text-size-large"><?php echo $this->Session->read('Auth.User.first_name');; ?></a>
                        </li>
                        <li>
                            <a href="#" style="font-family: 'Raleway' !important" class="text-semibold text-size-large"><?php echo getLoginRole(); ?></a>
                        </li>
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
                    <li class="<?php echo $this->Html->getActiveOpenClass(array('users')) ?>">
                        <?php echo $this->Html->link(__('Dashboard'), array('controller' => 'users', 'action' => 'dashboard'), array('icon' => 'icon-display4')) ?>
                    </li>
                    <?php if (isSuparAdmin()): ?>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('invoices')); ?>">
                            <?php echo $this->Html->link(__('Invoice Management'), array('controller' => 'invoices', 'action' => 'index'), array('icon' => 'icon-file-text')) ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('index')); ?>">
                            <?php echo $this->Html->link(__('Client Reports'), array('controller' => 'client_reports', 'action' => 'index'), array('icon' => 'icon-stats-bars2')) ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('subscriptions')); ?>">
                            <?php echo $this->Html->link(__('Subscription Management'), array('controller' => 'subscriptions', 'action' => 'index'), array('icon' => 'icon-calculator4')) ?>
                        </li>
                    <?php endif; ?>
                    <li class="treeview <?php echo (!empty($analyticParams)) ? '' : $this->Html->getActiveOpenClass(array('admins', 'dealers', 'companies')) ?>">
                    <?php echo $this->Html->link('User Management', 'javascript:void(0)', array('hasSubMenu' => true, 'class' =>$this->Html->getActiveOpenClass(array('admins', 'dealers', 'companies')), 'span' => true, 'icon' => 'icon-user')); ?>
                        <ul class="treeview-menu">
                            <?php if (!isSupportAdmin()): ?>
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('admins')) ?>">
                                    <?php echo $this->Html->link('Super Admins', array('controller' => 'admins', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                            <?php endif; ?>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('dealers')) ?>">
                                <?php
                                $name = __('Dealers');
                                echo $this->Html->link($name, array('controller' => 'dealers', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));
                                
                                ?>
                            </li>
                            <li class="<?php echo (!empty($this->params['pass'][0]) && $this->params['pass'][0] == 'analytics') ? '' : $this->Html->getActiveOpenClass(array('companies')); ?>">
                                <?php
                                $name = __('Financial Institutions');
                                echo $this->Html->link($name, array('controller' => 'companies', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));
                                
                                ?>
                            </li>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('users'),array('all_users', 'edit'), 'all') ?>">
                                <?php
                                $name = __('All Users');
                                echo $this->Html->link($name, array('controller' => 'users', 'action' => 'all_users'), array('icon' => 'fa fa-angle-double-right'));
                                ?>
                            </li>
                        </ul>
                        <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('countries', 'states', 'cities')) ?>">
                            <?php echo $this->Html->link('Address Management', 'javascript:void(0)', array('hasSubMenu' => true, 'class'=> $this->Html->getActiveOpenClass(array('countries', 'states', 'cities')), 'span' => true, 'icon' => 'icon-office')); ?>
                            <ul class="treeview-menu">
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('countries')) ?>">
                                    <?php echo $this->Html->link(__('Country'), array('controller' => 'countries', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('states')) ?>">
                                    <?php echo $this->Html->link(__('State'), array('controller' => 'states', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                                <li class="<?php echo $this->Html->getActiveOpenClass(array('cities')) ?>">
                                    <?php echo $this->Html->link(__('City'), array('controller' => 'cities', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                </li>
                            </ul>
                        </li>
                        <?php if (isSuparAdmin() || isAdminAdmin()): ?>
                            <li class="treeview <?php echo (!empty($analyticParams)) ? 'active' : $this->Html->getActiveOpenClass(array('analytics')); ?>">
                                <?php echo $this->Html->link('Reports', 'javascript:void(0)', array('hasSubMenu' => true, 'class'=> $this->Html->getActiveOpenClass(array('analytics')), 'span' => true, 'icon' => ' icon-stats-growth')); ?>
                                <ul class="treeview-menu">
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'index') ?>">
                                        <?php echo $this->Html->link(__('Log File Processing'), array('controller' => 'analytics', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'database_growth') ?>">
                                        <?php echo $this->Html->link(__('Database Growth'), array('controller' => 'analytics', 'action' => 'database_growth'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'denom_usage') ?>">
                                        <?php echo $this->Html->link(__('Denom Usage'), array('controller' => 'analytics', 'action' => 'denom_usage'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <!--                        <li class="<?php echo $this->Html->getActiveClass('analytics', 'inventory') ?>">
                                        <?php echo $this->Html->link(__('Inventory'), array('controller' => 'analytics', 'action' => 'inventory'), array('icon' => 'fa fa-angle-double-right')); ?>
                                        </li>-->
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'transaction_details') ?>">
                                        <?php echo $this->Html->link(__('No. of Transactions'), array('controller' => 'analytics', 'action' => 'transaction_details'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'errors') ?>">
                                        <?php echo $this->Html->link(__('No. of Errors'), array('controller' => 'analytics', 'action' => 'errors'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'availability_report') ?>">
                                        <?php echo $this->Html->link(__('Availability report'), array('controller' => 'analytics', 'action' => 'availability_report'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <?php if(isSuparAdmin()):?>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'unidentify_messages') ?>">
                                        <?php echo $this->Html->link(__('Unidentify Messages'), array('controller' => 'analytics', 'action' => 'unidentify_messages'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <?php endif;?>
                                    <li class="<?php echo (!empty($analyticParams) && $this->params['controller'] == 'companies') ? 'active' : ''; ?>">
                                        <?php echo $this->Html->link(__('Financial Institutions'), array('controller' => 'companies', 'action' => 'index', 'type' => 'analytics'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo (!empty($analyticParams) && $this->params['controller'] == 'dealers') ? 'active' : ''; ?>">
                                        <?php echo $this->Html->link(__('Dealer'), array('controller' => 'dealers', 'action' => 'index', 'type' => 'analytics'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                    <li class="<?php echo $this->Html->getActiveClass('analytics', 'note_count') ?>">
                                        <?php echo $this->Html->link(__('Note Count'), array('controller' => 'analytics', 'action' => 'note_count'), array('icon' => 'fa fa-angle-double-right')); ?>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if (isSuparAdmin()): ?>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('notmatchedserialno')); ?>">
                                <?php echo $this->Html->link(__('Not Matched Serial No'), array('controller' => 'notmatchedserialno', 'action' => 'index'), array('icon' => 'icon-info22')) ?>
                            </li>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('audit_log')); ?>">
                                <?php echo $this->Html->link(__('Audit Log'),array('controller' => 'client_reports', 'action' => 'audit_log'), array('icon' => 'icon-book ')) ?>
                            </li>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                                <?php echo $this->Html->link(__('Faq Management'), array('controller' => 'faqs', 'action' => 'index'), array('icon' => ' icon-question7')) ?>
                            </li>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('helps')); ?>">
                                <?php echo $this->Html->link(__('Help Management'), array('controller' => 'helps', 'action' => 'index'), array('icon' => ' fa icon-bubbles2')) ?>
                            </li>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('email_templates')); ?>">
                                <?php echo $this->Html->link(__('Email Management'), array('controller' => 'email_templates', 'action' => 'index'), array('icon' => 'icon-envelope')) ?>
                            </li>
                        <?php endif; ?>
                        <?php if (!isSuparAdmin()): ?>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                                <?php echo $this->Html->link(__('Help'), array('controller' => 'faqs', 'action' => 'lists'), array('icon' => 'icon-bubbles2')) ?>
                            </li>
                        <?php endif; ?>
                        <?php if (isSuparAdmin()): ?>
                            <li class="<?php echo $this->Html->getActiveOpenClass(array('site_configs')); ?>">
                                <?php echo $this->Html->link(__('Site Management'), array('controller' => 'site_configs', 'action' => 'index'), array('icon' => 'icon-cog2')) ?>
                            </li>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>