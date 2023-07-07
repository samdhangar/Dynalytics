<aside class="left-side sidebar-offcanvas">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <?php
                echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo')), array('class' => 'img-circle'));

                ?>
            </div>
            <div class="pull-left info">
                <p>
                    <?php echo $this->Html->link($this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name'), array('controller' => 'users', 'action' => 'dashboard')); ?>
                </p>
                <span title="<?php echo $this->Session->read('Auth.User.email'); ?>"><?php echo cropDetail($this->Session->read('Auth.User.email'), 27); ?></span>
            </div>
        </div>        
        <ul class="sidebar-menu">
            <li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'users', 'action' => 'dashboard'), array('icon' => 'fa-home')) ?></li>	

            		
            <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('users', 'admins', 'dealers', 'companies','all_users')) ?>">
                <?php echo $this->Html->link('User Management', 'javascript:void(0)', array('hasSubMenu' => true, 'span' => true, 'icon' => 'fa-user')); ?>
                <ul class="treeview-menu">
                    <?php if (isSuparAdmin() || isAdminAdmin()): ?>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('admins')) ?>">
                            <?php echo $this->Html->link('Super Admins', array('controller' => 'admins', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!(isSuparCompany() || isCompanyAdmin())): ?>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('dealers')) ?>">
                            <?php
                            $name = __('Dealers');
                            if (isAdminDealer()) {
//                            $name = __('Users');
                            }
                            echo $this->Html->link($name, array('controller' => 'dealers', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));

                            ?>
                        </li>
                    <?php endif; ?>
                    <li class="<?php echo $this->Html->getActiveOpenClass(array('companies')) ?>">
                        <?php
                        $name = __('Companies');
                        if (isAdminDealer()) {
//                            $name = __('Clients');
                        }
                        echo $this->Html->link($name, array('controller' => 'companies', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));

                        ?>
                    </li>
                    <?php if (isSuparAdmin() || isAdminAdmin()): ?>
                        <?php
                        $name = __('All Users');
                        echo $this->Html->link($name, array('controller' => 'users', 'action' => 'all_users'), array('icon' => 'fa fa-angle-double-right'));
                        ?>
                        <?php endif; ?>
                </ul>
            </li>
            <?php if (isAdmin()): ?>
                <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('countries', 'states', 'cities')) ?>">
                    <?php echo $this->Html->link('Address Management', 'javascript:void(0)', array('hasSubMenu' => true, 'span' => true, 'icon' => 'fa-location-arrow')); ?>
                    <ul class="treeview-menu">                        
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('countries')) ?>">
                            <?php echo $this->Html->link('Country', array('controller' => 'countries', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('states')) ?>">
                            <?php echo $this->Html->link('State', array('controller' => 'states', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('cities')) ?>">
                            <?php echo $this->Html->link('City', array('controller' => 'cities', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (isSuparAdmin()): ?>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('analytics')) ?>">
                    <?php echo $this->Html->link(__('Analytics Management'), array('controller' => 'analytics', 'action' => 'index'), array('icon' => 'fa-line-chart')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                    <?php echo $this->Html->link(__('Faq Management'), array('controller' => 'faqs', 'action' => 'index'), array('icon' => 'fa-question')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('subscriptions')); ?>">
                    <?php echo $this->Html->link(__('Subscriptions'), array('controller' => 'subscriptions', 'action' => 'index'), array('icon' => 'fa-question')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('billing')); ?>">
                    <?php echo $this->Html->link(__('Billing'), array('controller' => 'billing', 'action' => 'index'), array('icon' => 'fa-question')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('email_templates')); ?>">
                    <?php echo $this->Html->link(__('Email Management'), array('controller' => 'email_templates', 'action' => 'index'), array('icon' => 'fa-envelope')) ?>
                </li>
            <?php endif; ?>
            <?php if (isSuparCompany() || isCompanyAdmin()): ?>
<!--                <li class="<?php echo $this->Html->getActiveOpenClass(array('company_branches')); ?>">
                    <?php echo $this->Html->link(__('Branch Management'), array('controller' => 'company_branches', 'action' => 'index'), array('icon' => 'fa-tree')) ?>
                </li>-->
            <?php endif; ?>
            <?php if (!isSuparAdmin()): ?>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                    <?php echo $this->Html->link(__('Help'), array('controller' => 'faqs', 'action' => 'lists'), array('icon' => 'fa-lightbulb-o')) ?>
                </li>
            <?php endif; ?>
            <?php if ($this->Session->read('Auth.User.role') == 'Admin' && $this->Session->read('Auth.User.user_type') == SUPAR_ADM): ?>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('site_configs')); ?>">
                    <?php echo $this->Html->link(__('Site Management'), array('controller' => 'site_configs', 'action' => 'index'), array('icon' => 'fa-cog')) ?>
                </li>
            <?php endif; ?>
        </ul>
    </section>
</aside>
