<?php
$this->assign('pagetitle', __('Users'));
$this->Custom->addCrumb(__('Users'));
$this->start('top_links');
// echo $this->Html->link(__('Add Subscription'), array('action' => 'add'), array('icon' => 'cc icon-plus3 fa-plus', 'title' => __('Add Subscription'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();
$nameLabel = __('Name');
$emailLabel = __('Email');
if (isDealer()) {
    $nameLabel = __('Name contains');
    $emailLabel = __('Email contains');
}
//generate search panel
$searchPanelArray = array(
    'name' => 'User',
    'options' => array(
        'id' => 'UserAllSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'all_users'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-3'
            )
        )
    ),
    'searchDivClass' => 'col-md-6',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'UserSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'all_users', 'all'), array('escape' => false, 'title' => __('Display the all the Subscriptions'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'name',
            'options' => array(
                'label' => $nameLabel,
                'type' => 'text',
                'placeholder' => __('Enter name')
            )
        ),
        array(
            'name' => 'email',
            'options' => array(
                'label' => $emailLabel,
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


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
    </div>

    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div class="dataTables_filter" style="width:100%;">
            <?php echo $this->CustomForm->setSearchPanel($searchPanelArray); ?>
        </div>
      </div>

    </div>

    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div style="float:none;" class="dataTables_filter">

    <?php echo $this->element('paginationtop'); ?>

  </div>
</div>

</div>

    <div class="table-responsive">

        <?php echo $this->Form->create('Subscription', array('class' => 'deleteAllForm', 'url' => array('controller' => $this->params['controller'], 'action' => 'delete'), 'id' => 'UserEditProfileForm', 'data-confirm' => __('Are you sure you want to delete selected Subscription ?'))); ?>

        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover user-list" id="datatable">
          <thead>
            <tr>

                <?php $columns = 10; ?>
                <th width="5%">
                    <?php
                    echo __('#');

                    ?>
                </th>
                <th width="20%"><?php echo $this->Paginator->sort('name'); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('email',__('Email')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('phone_no',__('Phone No')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('role',__('Role/Type')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('status', __('Status')); ?></th>
                <th width="12%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
            </tr>
          </thead>
            <tbody>
              <?php if (empty($users)) { ?>
                  <tr>
                      <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No users found.') ?></td>
                  </tr>
              <?php } else { ?>
                  <?php foreach ($users as $user): ?>
                      <tr>

                          <td>
                              <?php echo $startNo++; ?>
                          </td>
                          <td class="table-text"><?php echo ($user['User']['first_name']); ?></td>
                          <td><?php echo ($user['User']['email']); ?></td>
                          <td><?php echo ($user['User']['phone_no']); ?></td>
                          <td><?php echo ($user['User']['role']).'/'.$user['User']['user_type']; ?></td>
                          <td>
                          <?php echo $this->Custom->getToggleButton($user['User']['status'], 'userStatusChange', array('data-uid' => $user['User']['id'], 'data-id' => 'userStatus_' . $user['User']['id'])); ?>
                          </td>
                          <td><?php echo showdatetime($user['User']['created']); ?></td>
                          <td class="actions text-center">
                              <?php
                              $sessionData = getMySessionData();
                              echo $this->Html->link('', array('action' => 'edit', encrypt($user['User']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this User')));
                              echo $this->Html->link('', array('action' => 'delete', encrypt($user['User']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this User')), __('Are you sure you want to delete Subscription?'));
                              echo $this->Html->link(__(''), array('action' => 'password_reset', encrypt($user['User']['id'])), array('icon' => 'icon-key fa-key', 'class' => 'no-hover-text-decoration', 'title' => __('Reset password')), __('Do you want to reset password?'));
                              ?>

                          </td>
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

<script type="text/javascript">
    jQuery(document).ready(function () {
        //$('#UserUserType').append('<option value="Region">Region Admin</option>');
        validateSearch("UserAllSearchForm", ["UserName", "UserEmail", "UserStatus", "UserUserType", "UserDealerId"]);
        jQuery('.userStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm("<?php echo __('Are you sure ? want to change status as ') ?>" + status)){
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + "<?php echo $this->params['controller']; ?>/change_status/" + uId + "/" + status,
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
                        //alert(response.message);
                        $(almessage).html(response.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });

    });
</script>
