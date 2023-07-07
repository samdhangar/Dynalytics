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
$search_role = $pageDetailWithRole['pageTitle'];
if (!empty($parentId)) {
    $search_role = 'Users';
}

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="overflow-hide-break">
                <div class="box-body userViewPage">
                    <?php
                    if ($pageDetailWithRole['role'] == COMPANY):
                        echo $this->element('user/company_view');
                    elseif ($pageDetailWithRole['role'] == DEALER):
                        echo $this->element('user/dealer_view');
                    else:
                        echo $this->element('user/admin_view');
                    endif;

                    ?>
                </div>
            </div>
        </div>
        <?php
        $searchPanelArray = array();
        if ($pageDetailWithRole['role'] != ADMIN):
            $searchPanelArray = array(
                'name' => 'CompanyBranch',
                'options' => array(
                    'id' => 'UserSearchForm',
                    'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'view', encrypt($id)), true),
                    'autocomplete' => 'off',
                    'novalidate' => 'novalidate',
                    'inputDefaults' => array(
                        'dir' => 'ltl',
                        'class' => 'form-control',
                        'required' => false,
                        'div' => array(
                            'class' => ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support') ? 'form-group col-md-3' : 'form-group col-md-2'
                        )
                    )
                ),
                'searchDivClass' => 'col-md-6',
                'search' => array(
                    'title' => 'Search Branch',
                    'options' => array(
                        'id' => Inflector::camelize($pageDetailWithRole['pageTitle']) . 'SearchBtn',
                        'class' => 'btn btn-primary margin-right10',
                        'div' => false
                    )
                ),
                'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'view', encrypt($id), 'all', 'CompanyBranch'), array('escape' => false, 'title' => __('Display the all the %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-default')),
                'fields' => array(
                    array(
                        'name' => 'name',
                        'options' => array(
                            'type' => 'text',
                            'placeholder' => __('Branch Name/Contact Name')
                        )
                    ),
                    array(
                        'name' => 'email',
                        'options' => array(
                            'type' => 'text',
                            'placeholder' => __('Enter email address')
                        )
                    )
                )
            );
            if ($pageDetailWithRole['role'] != DEALER && $user['User']['user_type'] != 'Support'):
                $searchPanelArray['fields'] = array_merge($searchPanelArray['fields'], array(array(
                        'name' => 'status',
                        'options' => array(
                            'type' => 'select',
                            'options' => $userStatus,
                            'empty' => __('Select status')
                        )
                )));
            endif;

            if ($pageDetailWithRole['role'] == COMPANY || ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support')):
                $title = BRANCH;

                if ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support'):
                    $title = __('List of branches supported');
                endif;

                ?>
                <div class="branchDiv box">
                    <div class="box-footer clearfix">
                        <div class="parentNameDiv">
                            <label class="h3"><?php echo $title; ?></label><hr>
                        </div>
                        <?php
                        if ($pageDetailWithRole['role'] == COMPANY || ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support')) {
                            if (!($pageDetailWithRole['role'] == DEALER && isDealer())):
                                echo $this->CustomForm->setSearchPanel($searchPanelArray);
                            endif;
                        }

                        ?>
                        <?php 
                        echo $this->element('paginationtop', array('paginateModel' => 'CompanyBranch'));
                        ?>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <?php
                        $startNo = (int) $this->Paginator->counter('{:start}');

                        ?>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <?php $colCount = 9; ?>
                                    <th width="4%">
                                        <?php
                                        echo __('Sr. No.');

                                        ?>
                                    </th>
                                    <?php if ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support'): ?>
                                        <th><?php echo $this->Paginator->sort('Company.first_name', __('Company Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('name', __('Branch Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('email', 'Branch Email'); ?></th>
                                        <th><?php echo $this->Paginator->sort('phone', 'Branch Phone'); ?></th>
                                        <th><?php echo $this->Paginator->sort('Company.station_count', 'No. of Station'); ?></th>
                                    <?php else:

                                        ?>
                                        <th><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('contact_name', __('Contact Name')); ?></th>
                                        <th><?php echo $this->Paginator->sort('city'); ?></th>
                                        <th><?php echo $this->Paginator->sort('state'); ?></th>
                                        <!--<th><?php echo $this->Paginator->sort('country'); ?></th>-->
                                        <th><?php echo $this->Paginator->sort('email'); ?></th>
                                        <th><?php echo $this->Paginator->sort('phone'); ?></th>
                                        <th><?php echo $this->Paginator->sort('branch_status'); ?></th>
                                    <?php endif; ?>

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
                                            <?php if ($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support'):

                                                ?>
                                                <td><?php echo $companyBranch['Company']['first_name']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['name']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['email']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['phone']; ?></td>
                                                <td><?php echo $companyBranch['Company']['station_count']; ?></td>
                                            <?php else:

                                                ?>

                                                <td><?php echo $companyBranch['CompanyBranch']['name']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['contact_name']; ?></td>
                                                <td><?php echo $companyBranch['City']['name']; ?></td>
                                                <td><?php echo $companyBranch['State']['name']; ?></td>
                                                <!--<td><?php // echo $companyBranch['Country']['name'];                                                                                                    ?></td>-->
                                                <td><?php echo $companyBranch['CompanyBranch']['email']; ?></td>
                                                <td><?php echo $companyBranch['CompanyBranch']['phone']; ?></td>
                                                <td>
                                                    <?php
                                                    echo $this->Custom->getToggleButton($companyBranch['CompanyBranch']['branch_status'], 'branchStatusChange', array('data-uid' => $companyBranch['CompanyBranch']['id'], 'data-id' => 'userStatus_' . $companyBranch['CompanyBranch']['id']));

                                                    ?>
                                                </td>
                                            <?php endif; ?>
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
                <?php
            endif;

            if (!($pageDetailWithRole['role'] == DEALER && $user['User']['user_type'] == 'Support')):
                $place_holder_name = 'Name';
                if ($pageDetailWithRole['role'] == COMPANY) {
                    $place_holder_name = 'Branch Name/Dealer Name';
                }

                $searchPanelArray = array(
                    'name' => 'User',
                    'options' => array(
                        'id' => 'UserSearchForm',
                        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'view', encrypt($id)), true),
                        'autocomplete' => 'off',
                        'novalidate' => 'novalidate',
                        'inputDefaults' => array(
                            'dir' => 'ltl',
                            'class' => 'form-control',
                            'required' => false,
                            'div' => array(
                                'class' => 'form-group col-md-2'
                            )
                        )
                    ),
                    'searchDivClass' => 'col-md-6',
                    'search' => array(
                        'title' => 'Search User',
                        'options' => array(
                            'id' => Inflector::camelize($pageDetailWithRole['pageTitle']) . 'SearchBtn',
                            'class' => 'btn btn-primary margin-right10',
                            'div' => false
                        )
                    ),
                    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'view', encrypt($id), 'all', 'User'), array('escape' => false, 'title' => __('Display the all the %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-default')),
                    'fields' => array(
                        array(
                            'name' => 'name',
                            'options' => array(
                                'type' => 'text',
                                'placeholder' => __('%s', $place_holder_name)
                            )
                        ),
                        array(
                            'name' => 'email',
                            'options' => array(
                                'type' => 'text',
                                'placeholder' => __('Enter email address')
                            )
                        ),
                        array(
                            'name' => 'status',
                            'options' => array(
                                'type' => 'select',
                                'options' => $userStatus,
                                'empty' => __('Select status')
                            )
                        )
                    )
                );

                ?>
                <div class="userDiv box">
                    <div class="box-footer clearfix">
                        <div class="parentNameDiv">
                            <label class="h3"><?php echo __('User'); ?></label><hr>
                        </div>
                        <?php
                        echo $this->CustomForm->setSearchPanel($searchPanelArray);
                        echo $this->element('paginationtop', array('paginateModel' => 'User'));

                        ?>
                    </div>
                    <div class="box-body table-responsive no-padding usersDiv">
                        <?php
                        $startNo = (int) $this->Paginator->counter('{:start}');

                        ?>
                        <table class="table table-hover table-bordered">
                            <thead>

                                <tr>
                                    <?php $colCount = 8; ?>
                                    <th width="4%">
                                        <?php
                                        echo __('Sr. No.');

                                        ?>
                                    </th>
                                    <?php
                                    if (isDealer() && $pageDetailWithRole['role'] == COMPANY):
                                        $colCount++;
                                        echo '<th>' . __('Branch Name') . '</th>';
                                    endif;

                                    ?>
                                    <?php
                                    if (empty($parentId)):
                                        $colCount++;

                                        ?>
                                        <th>
                                            <?php
                                            if ($pageDetailWithRole['role'] == DEALER):
                                                echo $this->Paginator->sort('first_name', __('Name'));
                                            else:
                                                if (isDealer()):
                                                    echo __('Support Engineer Name');
                                                else:
                                                    echo __('User Name');
                                                endif;
                                            endif;

                                            ?>
                                        </th>

                                    <?php endif; ?>
                                    <?php if (!isDealer() && $pageDetailWithRole['role'] != DEALER): ?>
                                        <th><?php echo $this->Paginator->sort('Dealer.first_name',__('Dealer Name')); ?></th>
                                    <?php endif; ?>
                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo $this->Paginator->sort('email', __('Email'));
                                        else:
                                            echo $this->Paginator->sort('email', __('Email'));
                                        endif;

                                        ?>
                                    </th>

                                    <th>
                                        <?php
                                        if ($pageDetailWithRole['role'] != DEALER):
                                            echo $this->Paginator->sort('phone_no', __('Phone No'));
                                        else:
                                            echo $this->Paginator->sort('phone_no', __('Phone No'));
                                        endif;

                                        ?>
                                    </th>
                                    <?php if (!(isDealer() && $pageDetailWithRole['role'] == COMPANY)): ?>
                                        <th>
                                            <?php
                                            if ($pageDetailWithRole['role'] != DEALER):
                                                echo $this->Paginator->sort('status', __('Status'));
                                            else:
                                                echo $this->Paginator->sort('status', __('Status'));
                                            endif;

                                            ?>
                                        </th>

                                        <th>
                                            <?php
                                            if ($pageDetailWithRole['role'] != DEALER):
                                                echo $this->Paginator->sort('last_login_time', __('Last Login'));
                                            else:
                                                echo $this->Paginator->sort('last_login_time', __('Last Login'));
                                            endif;

                                            ?>
                                        </th>

                                        <th>
                                            <?php
                                            if ($pageDetailWithRole['role'] != DEALER):
                                                echo $this->Paginator->sort('created', __('Added On'));
                                            else:
                                                echo $this->Paginator->sort('created', __('Added On'));
                                            endif;

                                            ?>
                                        </th>
                                    <?php endif; ?>
                                </tr>			
                            </thead>		
                            <tbody>
                                <?php if (empty($companyUsers)) { ?>
                                    <tr>
                                        <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No any users found.') ?></td>
                                    </tr>
                                <?php } else { ?>

                                    <?php foreach ($companyUsers as $user): ?>
                                        <tr>
                                            <td>
                                                <?php echo $startNo++; ?>
                                            </td>
                                            <?php
                                            if (isDealer() && $pageDetailWithRole['role'] == COMPANY):
                                                echo '<td>';
                                                $colCount++;
                                                if (isset($branchDealer[$user['User']['id']])) {
                                                    echo $branchDealer[$user['User']['id']];
                                                }
                                                echo '</td>';
                                            endif;

                                            ?>
                                            <?php if (empty($parentId)): ?>
                                                <td>
                                                    <?php
                                                    echo ($pageDetailWithRole['role'] == COMPANY) ? $user['User']['first_name'] : $user['User']['name'];

                                                    ?>
                                                </td>
                                            <?php endif; ?>
                                            <?php if (!isDealer() && $pageDetailWithRole['role'] != DEALER): ?>
                                                <td>
                                                    <?php
                                                    echo $user['Dealer']['first_name'];

                                                    ?>
                                                </td>
                                            <?php endif; ?>
                                            <td><?php echo $user['User']['email']; ?></td>
                                            <td><?php echo $user['User']['phone_no']; ?></td>
                                            <?php if (!(isDealer() && $pageDetailWithRole['role'] == COMPANY)): ?>
                                                <td>
                                                    <?php
                                                    echo $this->Custom->getToggleButton($user['User']['status'], 'userStatusChange', array('data-uid' => $user['User']['id'], 'data-id' => 'userStatus_' . $user['User']['id']));

                                                    ?>
                                                </td>
                                                <td><?php echo showdatetime($user['User']['last_login_time'], __('N/A')); ?></td>
                                                <td><?php echo showdatetime($user['User']['created'], __('N/A')); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } ?>			
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif;

            ?>
            <div class="box-footer clearfix">
                <?php
                echo $this->element('pagination', array('paginateModel' => 'User'));

                ?>

            </div>
        <?php endif; ?>

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