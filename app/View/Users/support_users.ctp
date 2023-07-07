<?php
$formParamter = '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
$this->assign('pagetitle', __('Support Person'));
$this->Custom->addCrumb(__('Company'), array('controller' => 'companies', 'action' => 'index'));
$this->Custom->addCrumb(__('Support Persons'));

$this->start('top_links');

$this->end();
//generate search panel

$searchPanelArray = array(
    'name' => 'User',
    'options' => array(
        'id' => 'UserSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'support_users', encrypt($companyId)), true),
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
    'searchDivClass' => 'col-md-8',
    'search' => array(
        'title' => 'Search Support Person',
        'options' => array(
            'id' => 'SupportSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'support_users', encrypt($companyId), 'all'), array('escape' => false, 'title' => __('Display the all the support persons'), 'class' => 'btn btn-default')),
    'fields' => array(
        array(
            'name' => 'name',
            'options' => array(
                'type' => 'text',
                'placeholder' => __('Enter name')
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

echo $this->CustomForm->setSearchPanel($searchPanelArray);

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">           
            <div class="box-footer clearfix">

                <?php if (!empty($parentId) && $pageDetailWithRole['role'] == DEALER): ?>   
                    <div><label><?php echo DEALER . ' Name :'; ?></label> <?php echo $parentDealer['User']['first_name']; ?></div>
                <?php endif; ?>

                <?php if (!empty($parentId) && $pageDetailWithRole['role'] == COMPANY): ?>   
                    <div><label><?php echo COMPANY . ' Name :'; ?></label> <?php echo $userLists[$parentId]; ?></div>
                <?php endif; ?>
                <?php echo $this->element('paginationtop'); ?>
            </div>
            <?php echo $this->Form->create('User', array('class' => 'deleteAllForm', 'url' => array('controller' => $this->params['controller'], 'action' => 'delete'), 'id' => 'UserEditProfileForm', 'data-confirm' => __('Are you sure you want to delete selected %s ?', $pageDetailWithRole['role']))); ?>
            <div class="box-body table-responsive no-padding">
                <?php
                $startNo = (int) $this->Paginator->counter('{:start}');

                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $fieldCount = 7; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');

                                ?>
                            </th>
                            <th>
                                <?php
                                echo $this->Paginator->sort('first_name', __('Dealer Name'));

                                ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('email'); ?></th>
                            <th><?php echo $this->Paginator->sort('phone_no'); ?></th>
                            <th><?php echo $this->Paginator->sort('user_type', __('Type')); ?></th>
                            <th width="12%"><?php echo $this->Paginator->sort('last_login_time', __('Last Login')); ?></th>
                            <th width="12%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                        </tr>			
                    </thead>		
                    <tbody>
                        <?php if (empty($users)) { ?>
                            <tr>
                                <td colspan='<?php echo $fieldCount; ?>' class='text-warning'><?php echo __('No any support person found.') ?></td>
                            </tr>
                        <?php } else { ?>

                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $company_name = ($this->params['controller'] == 'companies') ? $user['User']['first_name'] : $user['User']['name'];
                                        echo $company_name;

                                        ?>
                                    </td>
                                    
                                    <td><?php echo $user['User']['email']; ?></td>
                                    <td><?php echo $user['User']['phone_no']; ?></td>
                                    <td><?php echo $user['User']['user_type']; ?></td>
                                    <td><?php echo showdatetime($user['User']['last_login_time'], __('N/A')); ?></td>
                                    <td><?php echo showdatetime($user['User']['created'], __('N/A')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>			
                    </tbody>
                </table>

            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('pagination'); ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('#UserUserType').append('<option value="Region">Region Admin</option>');

        validateSearch("UserSearchForm", ["UserName", "UserEmail", "UserStatus", "UserUserType", "UserDealerId"]);
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
        });

    });
</script>