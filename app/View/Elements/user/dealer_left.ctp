 
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
            <li><?php echo $this->Html->link(__('Dashboard'), array('controller' => 'users', 'action' => 'dashboard'), array('icon' => 'icon-display4')) ?></li>

            			
            <?php if (!isSupportDealer()): ?>
                <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('users', 'admins', 'dealers', 'companies')) ?>">
                    <?php echo $this->Html->link('User Management', 'javascript:void(0)', array('hasSubMenu' => true,  'class' =>$this->Html->getActiveOpenClass(array('users', 'admins', 'dealers', 'companies')), 'span' => true, 'icon' => 'icon-user')); ?>
                    <ul class="treeview-menu">
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('dealers')) ?>">
                            <?php
                            $name = __('Users');
                            echo $this->Html->link($name, array('controller' => 'dealers', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));

                            ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveOpenClass(array('companies')) ?>">
                            <?php
                            $name = __('Financial Institutions');
                            echo $this->Html->link($name, array('controller' => 'companies', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right'));

                            ?>
                        </li>
                    </ul>
                </li>
    <!--                <li class="<?php echo $this->Html->getActiveOpenClass(array('company_branches')); ?>">
                <?php echo $this->Html->link(__('Branch Management'), array('controller' => 'company_branches', 'action' => 'index'), array('icon' => 'fa-tree')) ?>
                </li>-->
                <li class="<?php echo $this->Html->getActiveOpenClass(array('billing')); ?>">
                    <?php echo $this->Html->link(__('Billing'), array('controller' => 'invoices', 'action' => 'index'), array('icon' => ' icon-file-empty')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('history')); ?>">
                    <?php echo $this->Html->link(__('Historical Errors/Warnings'), array('controller' => 'history', 'action' => 'index'), array('icon' => ' icon-calendar')) ?>
                </li>

                    <!--            <li class="<?php echo $this->Html->getActiveOpenClass(array('monitoring')) ?>">
                <?php echo $this->Html->link(__('Monitoring'), array('controller' => 'monitoring', 'action' => 'index'), array('icon' => 'fa-line-chart')) ?>
                    </li>-->
                <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('analytics')) ?>">
                    <?php echo $this->Html->link('Performance Management', 'javascript:void(0)', array('hasSubMenu' => true, 'class' =>$this->Html->getActiveOpenClass(array('analytics')), 'span' => true, 'icon' => 'icon-stats-growth')); ?>
                    <ul class="treeview-menu">                        
                        <li class="<?php echo $this->Html->getActiveClass('analytics', 'issue_report') ?>">
                            <?php echo $this->Html->link(__('Issue Report'), array('controller' => 'analytics', 'action' => 'issue_report'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveClass('analytics', 'error_warning') ?>">
                            <?php echo $this->Html->link(__('Error/Warning'), array('controller' => 'analytics', 'action' => 'error_warning'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveClass('analytics', 'user_performance') ?>">
                            <?php echo $this->Html->link(__('Users Performance Report'), array('controller' => 'analytics', 'action' => 'user_performance'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveClass('analytics', 'client_issue') ?>">
                            <?php echo $this->Html->link(__('Client issue Report'), array('controller' => 'analytics', 'action' => 'client_issue'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <!-- <li class="<?php echo $this->Html->getActiveClass('analytics', 'inventory_management') ?>">
                            <?php echo $this->Html->link(__('Inventory Management'), array('controller' => 'analytics', 'action' => 'inventory_management'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li> -->
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (isSupportDealer()): ?>
                <li class="<?php echo $this->Html->getActiveOpenClass(array('company_branches')); ?>">
                    <?php echo $this->Html->link(__('Branch Management'), array('controller' => 'company_branches', 'action' => 'my_branches'), array('icon' => 'icon-office')) ?>
                </li>
                <li class="<?php echo $this->Html->getActiveClass('analytics', 'error_warning') ?>">
                            <?php echo $this->Html->link(__('Error/Warning'), array('controller' => 'analytics', 'action' => 'error_warning'), array('icon' => 'icon-stats-bars3')); ?>
                        </li>
            <?php endif; ?>
            <?php if (isSuparDealer()): ?>
                <li class="treeview <?php echo $this->Html->getActiveOpenClass(array('machine_errors','ticket_configs')) ?>">
                    <?php echo $this->Html->link('Configuration', 'javascript:void(0)', array('hasSubMenu' => true, 'span' => true, 'icon' => 'icon-cogs')); ?>
                    <ul class="treeview-menu">                        
                        <li class="<?php echo $this->Html->getActiveClass('machine_errors', 'index') ?>">
                            <?php echo $this->Html->link(__('Error Reporting'), array('controller' => 'machine_errors', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                        <li class="<?php echo $this->Html->getActiveClass('ticket_configs', 'index') ?>">
                            <?php echo $this->Html->link(__('Ticket Configs'), array('controller' => 'ticket_configs', 'action' => 'index'), array('icon' => 'fa fa-angle-double-right')); ?>
                        </li>
                    </ul>
                </li>
				<li class="<?php echo $this->Html->getActiveOpenClass(array('Setting')) ?>">
					<?php echo $this->Html->link('Setting', 'javascript:void(0)', array('hasSubMenu' => true, 'class' =>$this->Html->getActiveOpenClass(array('Setting')), 'span' => true, 'icon' => 'icon-cog')); ?>
					<ul style="">
						<li class="<?php echo $this->Html->getActiveOpenClass(array('stations')); ?>">
							<?php echo $this->Html->link(__('DynaCore Station Management'), array('controller' => 'stations', 'action' => 'index')) ?>
						</li>
					</ul>
				</li>
            <?php endif; ?>
            <?php // if (!isSupportDealer()):  ?>
            <li class="<?php echo $this->Html->getActiveOpenClass(array('faqs')); ?>">
                <?php echo $this->Html->link(__('Help'), array('controller' => 'faqs', 'action' => 'lists'), array('icon' => 'icon-bubbles2')) ?>
            </li>
            <?php // endif;  ?>
        </ul>
    </section>
</aside>
