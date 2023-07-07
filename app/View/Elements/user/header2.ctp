<?php
$logUserName = $this->Session->read('Auth.User.first_name');
?>
<header class="header">
    <?php
    $actionArr = array('controller' => 'users', 'action' => 'dashboard');

    echo $this->Html->link($this->Html->image('logo_w.png', array('class' => 'img-responsive')), $actionArr, array('escape' => false, 'class' => 'logo'));
    ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">	
                <?php if (isSuparCompany() || isCompanyAdmin()): ?>
                    <li>
<?php
                        $branchId = '';
                        if ($this->Session->check('Auth.User.BranchDetail.id')) {
                            $branchId = $this->Session->read('Auth.User.BranchDetail.id');
                        }else{
                            $branchId = 'all';
                        }
                        echo $this->Form->input('my_branch_list_id', array('value' => $branchId, 'onchange' => 'setBranchSession(this.value)', 'label' => __('Branch:'), 'class' => 'form-control', 'div' => 'input select branchSelDiv'));
                        ?>
                    </li>
                <?php endif; ?>
                    <?php if($this->Session->check('Auth.User.companyDetail')): ?>
                <li>
                    <?php 
                    echo $this->Html->link('<span>'.$this->Session->read('Auth.User.companyDetail.first_name').' &nbsp;<i class="fa fa-close"></i></span>',array('controller'=>'users','action'=>'remove_session'),array('escape'=>false));
                    ?>
                </li>
                <?php endif; ?>
                    <?php if($this->Session->check('Auth.User.dealerDetail')): ?>
                <li>
                    <?php 
                    echo $this->Html->link('<span>'.$this->Session->read('Auth.User.dealerDetail.first_name').' &nbsp;<i class="fa fa-close"></i></span>',array('controller'=>'users','action'=>'remove_session'),array('escape'=>false));
                    ?>
                </li>
                <?php endif; ?>
                <li class="dropdown user user-menu">					
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">						
                        <?php
							echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo')), array('class' => 'img-circle'));
						?>
                        <span><?php echo $logUserName ?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- Admin image -->
                        <li class="user-header bg-danger">
                            <?php //echo getLoginRole(); ?>
							<?php 
								echo $this->Html->image(getUserPhoto($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.photo'), false, false), array('class' => 'img-circle')); 
							?>

                            <p>
                                <?php echo $logUserName; ?>
                                <small>
                                    <?php
                                    $lastLoginDate = str_replace('k', 'sup', showdatetime($this->Session->read('Auth.User.last_login_time'), 'N/A', 'd<k>S</k>M,Y H:i:s'));
                                    ?>
                                    <?php echo ($lastLoginDate == 'N/A') ? __('First Time Login') : __('Last login: ') . $lastLoginDate ?>
                                </small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-12 text-center">
                                <?php echo $this->Html->link('Change Password', array('controller' => 'users', 'action' => 'change_password'), array('class' => 'no-hover-text-decoration')); ?>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?php echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'profile'), array('class' => 'btn btn-default btn-flat')); ?>
                            </div>
                            <div class="pull-right">
                                <?php echo $this->Html->link('Sign out', array('controller' => 'users', 'action' => 'logout'), array('class' => 'btn btn-default btn-flat')) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>