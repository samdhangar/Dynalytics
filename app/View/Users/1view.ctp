<?php
if ($pageDetailWithRole['role'] == COMPANY):
    $this->assign('pagetitle', __('%s', $user['User']['first_name']));
else:
    $this->assign('pagetitle', __('%s Detail', $pageDetailWithRole['singularTitle']));
endif;
$this->Custom->addCrumb(__('%s', $pageDetailWithRole['pageTitle']), array('controller' => $pageDetailWithRole['usedController'], 'action' => 'index'));

$formParamter = $formParamter = getNamedParameter($this->params['named']);
if ($formParamter) {
    $this->Custom->addCrumb(__('Sub %s', $pageDetailWithRole['pageTitle']), array('controller' => $pageDetailWithRole['usedController'], 'action' => 'index', $formParamter));
}
$this->Custom->addCrumb(__('%s Detail', $pageDetailWithRole['singularTitle']));
$this->start('top_links');
if (!empty($user)) {
//    echo $this->Html->link(__('Reset Password'), array('controller' => $this->params['controller'], 'action' => 'password_reset', $user['User']['id']), array('icon' => 'fa-lock', 'class' => 'btn btn-primary pull-right', 'escape' => false, 'title' => __('Click here reset password')));
//    echo $this->Html->link(__('Edit'), array('controller' => $this->params['controller'], 'action' => 'edit', $user['User']['id']), array('icon' => 'fa-edit', 'class' => 'btn btn-primary pull-right', 'escape' => false, 'title' => __('Click here to edit')));
}
echo $this->Html->link(__('Back'), array('controller' => $pageDetailWithRole['usedController'], 'action' => 'index', $formParamter), array('icon' => 'back', 'class' => 'btn btn-default pull-right', 'escape' => false));
$this->end();
//debug($companyUsers);exit;

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="overflow-hide-break">
                <div class="box-body userViewPage">
                    <?php if (empty($user)) { ?>
                        <?php echo $this->Html->showInfo(__('Invalid User.'), array('type' => 'warning')) ?>
                    <?php } else { ?>
                        <div class="col-xs-3">
                            <?php echo $this->Html->image(getUserPhoto($user['User']['id'], $user['User']['photo'], false, 200), array('class' => 'thumbnail img-responsive')) ?>
                        </div>
                        <div class="col-xs-9">
                            <div class="row">
                                <div class="col-md-5 col-sm-6 no-padding">
                                    <dl class="dl-horizontal">

                                        <?php if ($pageDetailWithRole['role'] == COMPANY): ?>

                                            <dt><?php echo __("Dealer Name:") ?> :</dt>
                                            <dd><?php echo $user['Dealer']['name']; ?></dd>

                                            <dt><?php echo __('Company Name'); ?> :</dt>
                                            <dd><?php echo $user['User']['first_name']; ?></dd>

                                            <dt><?php echo __('Contact Name'); ?> :</dt>
                                            <dd><?php echo $user['User']['last_name']; ?></dd>

                                        <?php else: ?>
                                            <dt><?php echo __('Name'); ?> :</dt>
                                            <dd><?php echo $user['User']['name']; ?></dd>
                                        <?php endif; ?>

                                        <dt><?php echo __("Email") ?> :</dt>
                                        <dd><?php echo $user['User']['email']; ?></dd>

                                        <dt><?php echo __("Phone") ?> :</dt>
                                        <dd><?php echo showPhoneNo($user['User']['phone_no']); ?></dd>


                                        <?php if ($pageDetailWithRole['role'] == ADMIN): ?>
                                            <dt><?php echo __("Admin Type") ?> :</dt>
                                            <dd><?php echo $user['User']['user_type']; ?></dd>
                                        <?php endif; ?>

                                        <?php if ($pageDetailWithRole['role'] != ADMIN): ?>
                                            <dt><?php echo __("Membership") ?> :</dt>
                                            <dd><?php
                                                echo getMemberShipType($user['User']['membership'], true);

                                                ?></dd>
                                        <?php endif; ?>



                                        <dt><?php echo __("Added on") ?> :</dt>
                                        <dd><?php echo showdatetime($user['User']['created']); ?></dd>

                                        <dt><?php echo __("Last Login") ?> :</dt>
                                        <dd><?php echo showdatetime($user['User']['last_login_time'], __('N/A')); ?></dd>
                                    </dl>                        
                                </div>
                                <div class="col-md-7 col-sm-6">
                                    <dl class="dl-horizontal">

                                        <?php
                                        echo $this->Custom->displayAddress($userDetailArr['addressArr']);

                                        ?>
                                    </dl>                        
                                </div>
                            </div>

                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ($pageDetailWithRole['role'] != ADMIN): ?>
                <?php if ($pageDetailWithRole['role'] == COMPANY && !empty($companyBranches)): ?>
                    <div class="branchDiv">
                        <div class="box-footer clearfix">
                            <div class="parentNameDiv">
                                <label><?php echo BRANCH; ?>&nbsp;&nbsp;</label>
                            </div>
                            <?php echo $this->element('paginationtop', array('paginateModel' => 'CompanyBranch')); ?>
                        </div>
                        <div class="box-body table-responsive no-padding">
                            <?php
                            $startNo = (int) $this->Paginator->counter('{:start}');

                            ?>
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <?php $colCount = 9; ?>
                                        <th>
                                            <?php
                                            echo __('Sr. No.');

                                            ?>
                                        </th>
                                        <th><?php echo $this->Paginator->sort('name', __('Branch Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('contact_name', __('Contact Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('city'); ?></th>
                                        <th><?php echo $this->Paginator->sort('state'); ?></th>
                                        <th><?php echo $this->Paginator->sort('country'); ?></th>
                                        <th><?php echo $this->Paginator->sort('email'); ?></th>
                                        <th><?php echo $this->Paginator->sort('phone'); ?></th>
                                        <th><?php echo $this->Paginator->sort('branch_status'); ?></th>
                                        <th><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>

                                    </tr>			
                                </thead>		
                                <tbody>
                                    <?php if (empty($companyBranches)) { ?>
                                        <tr>
                                            <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No Company Branch found.') ?></td>
                                        </tr>
                                    <?php } else { ?>

                                        <?php foreach ($companyBranches as $companyBranch): ?>
                                            <tr>
                                                <td>
                                                    <?php echo $startNo++; ?>
                                                </td>

                                                <td><?php echo $companyBranch['CompanyBranch']['name']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['contact_name']; ?></td>
                                                <td><?php echo $companyBranch['City']['name']; ?></td>
                                                <td><?php echo $companyBranch['State']['name']; ?></td>
                                                <td><?php echo $companyBranch['Country']['name']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['email']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['phone']; ?></td>
                                                <td>
                                                    <?php
                                                    echo $this->Custom->getToggleButton($companyBranch['CompanyBranch']['branch_status'], 'branchStatusChange', array('data-uid' => $companyBranch['CompanyBranch']['id'], 'data-id' => 'userStatus_' . $companyBranch['CompanyBranch']['id']));

                                                    ?>
                                                </td>
                                                <td><?php echo showdate($companyBranch['CompanyBranch']['created']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php } ?>			
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer clearfix">
                            <?php echo $this->element('pagination', array('paginateModel' => 'CompanyBranch')); ?>

                        </div>
                    </div>
                <?php endif; ?>
                <!---------------------------User Table--------------------------->
                <?php if (!empty($companyUsers)): ?>
                    <div class="box-footer clearfix">
                        <div class="parentNameDiv">
                            <label>

                                <?php
                                echo __('User');

                                ?>
                            </label>


                            <?php
                            echo $this->element('paginationtop', array('paginateModel' => 'User'));
                            ?>

                        </div>
                    </div>

                    <div class="box-body table-responsive no-padding usersDiv">
                        <?php
                        if ($pageDetailWithRole['role'] == DEALER):
                            $startNo = (int) $this->Paginator->counter('{:start}');
                        else:
                            $startNo = 1;
                        endif;

                        ?>
                        <table class="table table-hover table-bordered">
                            <thead>

                                <tr>
                                    <?php $colCount = 8; ?>
                                    <th>
                                        <?php
                                        echo __('Sr. No.');

                                        ?>
                                    </th>
                                    <?php
                                    if (empty($parentId)):
                                        $colCount++;

                                        ?>
                                        <th>
                                            <?php
                                            $title = __('Branch Name');
                                            if ($pageDetailWithRole['role'] == DEALER):
                                                $title = __('Name');
                                            endif;
                                            echo $this->Paginator->sort('first_name', $title);

                                            ?>
                                        </th>

                                    <?php endif; ?>
                                    <?php if ($pageDetailWithRole['role'] != DEALER): ?>
                                        <th><?php echo __('Dealer Name'); ?></th>
                                    <?php endif; ?>
                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo __('Email');
                                        else:
                                            echo $this->Paginator->sort('email', __('Email'));
                                        endif;

                                        ?>
                                    </th>

                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo __('Phone No');
                                        else:
                                            echo $this->Paginator->sort('phone_no', __('Phone No'));
                                        endif;

                                        ?>
                                    </th>

                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo __('Status');
                                        else:
                                            echo $this->Paginator->sort('status', __('Status'));
                                        endif;

                                        ?>
                                    </th>

                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo __('Last Login');
                                        else:
                                            echo $this->Paginator->sort('last_login_time', __('Last Login'));
                                        endif;

                                        ?>
                                    </th>

                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo __('Added On');
                                        else:
                                            echo $this->Paginator->sort('created', __('Added On'));
                                        endif;

                                        ?>
                                    </th>
                                </tr>			
                            </thead>		
                            <tbody>

                                <?php if (empty($companyUsers)) { ?>
                                    <tr>
                                        <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No Company Branch found.') ?></td>
                                    </tr>
                                <?php } else { ?>

                                    <?php foreach ($companyUsers as $user): ?>
                                        <tr>
                                            <td>
                                                <?php echo $startNo++; ?>
                                            </td>
                                            <?php if (empty($parentId)): ?>
                                                <td>
                                                    <?php
                                                    echo ($pageDetailWithRole['role'] == COMPANY) ? $user['User']['first_name'] : $user['User']['name'];

                                                    ?>
                                                </td>
                                            <?php endif; ?>

                                            <?php if ($pageDetailWithRole['role'] != DEALER): ?>
                                                <td>
                                                    <?php
                                                    echo $user['Dealer']['first_name'];

                                                    ?>
                                                </td>
                                            <?php endif; ?>
                                            <td><?php echo $user['User']['email']; ?></td>
                                            <td><?php echo $user['User']['phone_no']; ?></td>
                                            <td>
                                                <?php
                                                echo $this->Custom->getToggleButton($user['User']['status'], 'userStatusChange', array('data-uid' => $user['User']['id'], 'data-id' => 'userStatus_' . $user['User']['id']));

                                                ?>
                                            </td>
                                            <td><?php echo showdatetime($user['User']['last_login_time'], __('N/A')); ?></td>
                                            <td><?php echo showdatetime($user['User']['created'], __('N/A')); ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php } ?>			
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <?php
                        echo $this->element('pagination', array('paginateModel' => 'User'));

                        ?>

                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.branchStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + 'company_branches/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'active' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        alert(response.message);
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });

        jQuery('.userStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'active' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        alert(response.message);
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        })
    });

</script>