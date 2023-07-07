<?php
$formParamter = '';
$startNo = (int) $this->Paginator->counter('{:start}');
$analyticParams = isset($this->params['named']['type']) ? 'type:' . $this->params['named']['type'] : '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
if (empty($parentId)):
    if (empty($parentId) && ($pageDetailWithRole['role'] != ADMIN ) && (isSuparDealer() || isSuparCompany())) {

        if (empty($parentId) && $pageDetailWithRole['role'] == COMPANY) {
            if (isDealer()) {
                $this->assign('pagetitle', __('My Financial Institution'));
                $this->Custom->addCrumb(__('My Financial Institution'));
            } else {
                $this->assign('pagetitle', __('%s', $pageDetailWithRole['pageTitle']));
                $this->Custom->addCrumb(__('%s ', $pageDetailWithRole['pageTitle']));
            }
        } else {
            $this->assign('pagetitle', __('DynaLytics  Users'));
            $this->Custom->addCrumb(__('DynaLytics  Users'));
        }
    } else {
        if (isDealer() && $pageDetailWithRole['role'] == COMPANY) {
            $this->assign('pagetitle', __('My Financial Institution'));
            $this->Custom->addCrumb(__('My Financial Institution'));
        } else {
            $this->assign('pagetitle', __('%s', $pageDetailWithRole['pageTitle']));
            $this->Custom->addCrumb(__('%s ', $pageDetailWithRole['pageTitle']));
        }
    }
else :
    $this->assign('pagetitle', __('%s', $pageDetailWithRole['singularTitle'] . ' DynaLytics Users'));
    $this->Custom->addCrumb(__('%s ', $pageDetailWithRole['pageTitle']), array('controller' => $this->params['controller'], 'action' => 'index'));
    $this->Custom->addCrumb(__('DynaLytics Users'));
endif;

$this->start('top_links');
$parentId = empty($parentId) ? '' : $parentId;
//if (!(isDealer() && ($pageDetailWithRole['role'] == COMPANY))) {

if (!empty($parentId)) {
    echo $this->Html->link(__('Add DynaLytics User'), array('controller' => $this->params['controller'], 'action' => 'add', encrypt($parentId)), array('icon' => 'cc icon-plus3 add', 'title' => __('Add DynaLytics User'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
} else {
    if (empty($parentId) && ($pageDetailWithRole['role'] != ADMIN ) && (isSuparCompany() || isDealer())) {
        $getMySessionData = getMySessionData();
        if (isDealer() && ($pageDetailWithRole['role'] == COMPANY)) {
            if($getMySessionData['role'] != "Dealer"){
                echo $this->Html->link(__('Add Financial Institution'), array('controller' => $this->params['controller'], 'action' => 'add', encrypt($parentId)), array('icon' => 'cc icon-plus3 add', 'title' => __('Add %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-success ', 'escape' => false));
            }
           
        } else {
            echo $this->Html->link(__('Add DynaLytics User'), array('controller' => $this->params['controller'], 'action' => 'add', encrypt($parentId)), array('icon' => 'cc icon-plus3 add', 'title' => __('Add %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-success ', 'escape' => false));
        }
    } else {

        if (!($pageDetailWithRole['role'] == COMPANY && isDealer())) {
            echo $this->Html->link(__('Add %s', $pageDetailWithRole['singularTitle']), array('controller' => $this->params['controller'], 'action' => 'add', $parentId), array('icon' => 'cc icon-plus3 add', 'title' => __('Add %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-success ', 'escape' => false));
        }
    }
}
//}
$search_role = $pageDetailWithRole['pageTitle'];
if (!empty($parentId)) {
    $search_role = 'DynaLytics Users';
}
if ((isAdminDealer() || isSuparDealer()) || (isSuparCompany() || isCompanyAdmin() && $pageDetailWithRole['role'] == COMPANY)) {
    echo $this->Html->link(__('Export DynaLytics  %s', $search_role), array('action' => 'export',$formParamter), array('icon' => 'icon-download', 'title' => __('Export DynaLytics  %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-primary marginleft', 'escape' => false));
}
$this->end();
//generate search panel
$nameLabel = __('Name');
$emailLabel = __('Email');
if (isDealer()) {
    $nameLabel = __('Name contains');
    $emailLabel = __('Email contains');
}
$searchPanelArray = array(
    'name' => 'User',
    'options' => array(
        'id' => 'UserSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index', $analyticParams, $formParamter), true),
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
        'title' => 'Search ' . $search_role,
        'options' => array(
            'id' => Inflector::camelize($pageDetailWithRole['pageTitle']) . 'SearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('controller' => $this->params['controller'], 'action' => 'index', 'all', $analyticParams, $formParamter), array('escape' => false, 'title' => __('Display the all the %s', $pageDetailWithRole['pageTitle']), 'class' => 'btn btn-default marginleft')),
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

if ($pageDetailWithRole['role'] == COMPANY && isCompany()) {
    $searchPanelArray['fields'][] = array(
        'name' => 'user_type',
        'options' => array(
            'label' => __('User Role'),
            'type' => 'select',
            'options' => $userTypes,
            'empty' => __('Select %s Type', $pageDetailWithRole['singularTitle'])
        )
    );
    $searchPanelArray['searchDivClass'] = 'col-md-4';
}



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
        <table class="table table-hover table-bordered" id="datatable">
          <thead>
              <tr>
                  <?php $fieldCount = 9; ?>



                  <th width="5%" class="text_align">
                      <?php
                      echo __('#');

                      ?>
                  </th>
                  <th class="text_align">
                      <?php
                      if ($pageDetailWithRole['role'] == COMPANY && !empty($parentId)) {
                          $title = __('Branch Name');
                      }

                      $title = $pageDetailWithRole['pageTitle'];

                      if (trim($pageDetailWithRole['pageTitle']) == 'Users' || isDealer()) {
//                                    $title = __('User Name');
                          $title = __('Financial Institution Name');
                          if(isCompany()){
                              $title = __('Name');
                          }
                      }
                      echo ($pageDetailWithRole['role'] == COMPANY) ? $this->Paginator->sort('first_name', $title) : $this->Paginator->sort('name');

                      ?>
                  </th>

                  <?php
                  if ($pageDetailWithRole['role'] == COMPANY && !(isSuparCompany() || isCompanyAdmin()) && !(isDealer())):
                      $fieldCount++;

                      ?>
                      <th class="text_align"><?php echo $this->Paginator->sort('User.Dealer.name', __('Dealer name')); ?></th>

                  <?php endif; ?>
                  <th class="text_align"><?php echo $this->Paginator->sort('email'); ?></th>

                  <th class="text_align"><?php echo $this->Paginator->sort('phone_no', __('Phone Number')); ?></th>

                  <?php
                  if ((in_array($pageDetailWithRole['role'], array(DEALER)) && (trim($pageDetailWithRole['pageTitle']) == 'Users' || trim($pageDetailWithRole['pageSubTitle']) == 'Users')) || ($pageDetailWithRole['role'] == ADMIN)):

                      $fieldCount++;

                      ?>
                      <th><?php echo $this->Paginator->sort('user_type', __('Type')); ?></th>
                  <?php endif; ?>
                  <th class="text_align"><?php echo $this->Paginator->sort('status', __('Status')); ?></th>

                  <?php
                  if ($pageDetailWithRole['role'] == COMPANY && isCompany()):
                      $fieldCount++;

                      ?>
                      <th class="text_align"><?php echo $this->Paginator->sort('status', __('User Role')); ?></th>
                  <?php endif; ?>

                  <?php
                  if ($pageDetailWithRole['role'] == 'Dealer' && empty($parentId) && (isAdmin())):
                      $fieldCount++;

                      ?>

                      <th class="text_align"><?php echo $this->Paginator->sort('sub_dealer_count', __('No. of Users')); ?></th>
                  <?php endif; ?>

                  <?php
                  if ($pageDetailWithRole['role'] == 'Company' && empty($parentId) && !(isCompanyAdmin() || isSuparCompany())):
                      $fieldCount = $fieldCount + 2;
                      $no_of = 'No. of Users ';
                      if ($pageDetailWithRole['role'] == COMPANY) {
                          $no_of = 'No. of Support Users ';
                      }

                      ?>
                      <th class="text_align"><?php echo $this->Paginator->sort('company_branch_count', __('No. of Branches')); ?></th>
                      <th class="text_align"><?php echo $this->Paginator->sort('sub_company_count', __('%s', $no_of)); ?></th>
                  <?php endif; ?>
                  <?php
                  if ($pageDetailWithRole['role'] == 'Dealer' && empty($parentId) && (isAdmin())):
                      $fieldCount++;

                      ?>
                      <th class="text_align"><?php echo $this->Paginator->sort('dealer_company_count', __('No. of Company')); ?></th>
                  <?php endif; ?>
                  <?php
                  if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && (isSuparAdmin() || isAdminAdmin())):
                      $fieldCount++;

                      ?>
                      <th class="text_align">
                          <?php echo $this->Paginator->sort('station_count', __('No. of Station')); ?>
                      </th>
                  <?php endif; ?>
                  <?php if (!(isDealer() && $pageDetailWithRole['role'] == COMPANY)): ?>
                      <th width="12%" class="text_align"><?php echo $this->Paginator->sort('last_login_time', __('Last Login')); ?></th>
                  <?php endif; ?>
                  <th width="12%" class="text_align"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                  <!-- <?php if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && !isCompany()): ?>
                      <th width="20%">
                          <?php echo __('FTP Detail'); ?>
                      </th>
                  <?php endif; ?> -->
                  <?php
                  if (($this->params['controller'] != 'analytics')):
                      $fieldCount++;

                      ?>
                      <th class="actions text-center"><?php echo __('Actions'); ?></th>
                  <?php endif; ?>

              </tr>
          </thead>
            <tbody>
              <?php if (empty($users)) { ?>
                  <tr>
                      <td colspan='<?php echo $fieldCount; ?>' class='text-warning'><?php echo __('No %s found.', $pageDetailWithRole['pageTitle']) ?></td>
                  </tr>
              <?php } else { ?>

                  <?php
                  foreach ($users as $user):
                      $user['User']['name'] = $user['User']['first_name'] . " " . $user['User']['last_name'];
 
                      ?>
                      <tr>



                          <td class="text_right">
                              <?php echo $startNo++; ?>
                          </td>
                          <?php
                          if (!empty($formParamter)) {
                              $parentDealer = (!empty($userLists[$user['User']['parent_id']]) ? $userLists[$user['User']['parent_id']] : 'N/A');
                          } else {
                              $parentDealer = isset($userLists[$user['User']['created_by']]) ? $userLists[$user['User']['created_by']] : 'N/A';
                          }

                          ?>
                          <td class="table-text">
                              <?php
                              $target = '';
                              if (!empty($analyticParams)) {
                                  $target = array('target' => '_blank');
                              }
              //                                        $company_name = ($this->params['controller'] == 'companies') ? $user['User']['first_name'] : $user['User']['name'];
                              $company_name = $user['User']['first_name'];

                              if (isSuparAdmin() && !empty($analyticParams)) {

                                  if ($pageDetailWithRole['role'] == COMPANY) {
                                      echo $this->Html->link($company_name, array('action' => 'set_company_session', encrypt($user['User']['id']), $formParamter), $target);
                                  } else {
                                      echo $this->Html->link($company_name, array('action' => 'set_dealer_session', encrypt($user['User']['id']), $formParamter), $target);
                                  }
                              } else {
                                if($user['User']['user_type']=='Region'){
                                  echo $company_name;
                                }else{ 
                                    echo $this->Html->link($company_name, array('action' => 'view', encrypt($user['User']['id']), $formParamter), $target);
                                }
                              }
 
                              ?>
                          </td>
                          <?php
                          if ($pageDetailWithRole['role'] == COMPANY && !(isSuparCompany() || isCompanyAdmin()) && !(isDealer())):

                              ?>
                              <td>
                                  <?php
                                  $dealer_name = (!empty($userLists[$user['User']['dealer_id']]) ? $userLists[$user['User']['dealer_id']] : '-');
                                  if (isset($companyDealers[$user['User']['id']]) && !empty($companyDealers[$user['User']['id']])) {
                                      $dealer_name = $companyDealers[$user['User']['id']];
                                  }
                                  if (isSuparAdmin() && !empty($analyticParams)) {
                                      echo $this->Html->link($dealer_name, array('action' => 'set_dealer_session', encrypt($user['User']['dealer_id']), $formParamter), $target);
                                  } else { 
                                      echo $this->Html->link($dealer_name, array('controller' => 'dealers', 'action' => 'view', encrypt($user['User']['dealer_id']), $formParamter), $target);
                                  }

                                  ?>
                              </td>
                          <?php endif; ?>
                          <td><?php echo $user['User']['email']; ?></td>
                          <td><?php echo $user['User']['phone_no']; ?></td>

                          <?php if ((in_array($pageDetailWithRole['role'], array(DEALER)) && (trim($pageDetailWithRole['pageTitle']) == 'Users' || trim($pageDetailWithRole['pageSubTitle']) == 'Users')) || ($pageDetailWithRole['role'] == ADMIN)): ?>

                              <td><?php echo $user['User']['user_type']; ?></td>
                          <?php endif; ?>
                          <td>
                              <?php echo $this->Custom->getToggleButton($user['User']['status'], 'userStatusChange', array('data-uid' => $user['User']['id'], 'data-id' => 'userStatus_' . $user['User']['id'])); ?>
                              <!--<input data-size='mini' data-uid='<?php echo $user['User']['id']; ?>' class="userStatusChange" id='userStatus_<?php echo $user['User']['id']; ?>' <?php echo ($user['User']['status'] == 'active') ? 'checked' : ''; ?> data-toggle="toggle" data-on="active" data-onstyle="success" data-offstyle="danger" data-off="pending" type="checkbox">-->
                          </td>

                          <?php
                          if ($pageDetailWithRole['role'] == COMPANY && isCompany()):
                              $fieldCount++;
                              $usr_role = ($user['User']['user_type'] == ADMIN) ? $user['User']['user_type'] : $user['User']['user_type'] . ' ' . ADMIN;
                              echo '<td>' . $usr_role . '</td>';
                          endif;

                          ?>

                          <?php if (!isAdminDealer() && !isSuparDealer()) { ?>
                              <?php if ($pageDetailWithRole['role'] == 'Dealer' && empty($parentId)): ?>
                                  <td class="text_right">
                                      <?php
                                      echo $this->Html->link($user['User']['sub_dealer_count'], array('action' => 'index', $pageDetailWithRole['singularTitle'] => encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view list of child ' . $pageDetailWithRole['singularTitle'])));

                                      ?>
                                  </td>
                              <?php endif; ?>
                          <?php } ?>
                          <?php if ($pageDetailWithRole['role'] == 'Company' && empty($parentId) && !(isCompanyAdmin() || isSuparCompany())): ?>
                              <td class="text_right">
                                  <?php
                                  if (!empty($user['User']['company_branch_count'])) {
              //                                                $user['User']['company_branch_count'] = $user['User']['company_branch_count'] - 1;
                                      $user['User']['company_branch_count'] = $user['User']['company_branch_count'];
                                  }
                                  if (isDealer() && $pageDetailWithRole['role'] == COMPANY) {
                                      echo $user['User']['company_branch_count'];
                                  } else {
                                      echo $this->Html->link($user['User']['company_branch_count'], array('controller' => 'company_branches', 'action' => 'index', COMPANY => encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view branche of %s ', $user['User']['first_name'])));
                                  }

                                  ?>
                              </td>
                              <td class="text_right">
                                  <?php
                                  if (isDealer() && $pageDetailWithRole['role'] == COMPANY) {
              //                                                echo $this->Html->link($supportCount[$user['User']['id']], array('controller' => DEALER,'action' => 'index',encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view users of %s ', $user['User']['first_name'])));
              //                                                echo $this->Html->link($supportCount[$user['User']['id']], array('controller' => 'dealers', 'action' => 'support_users', encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view users of %s ', $user['User']['first_name'])));
                                      echo $supportCount[$user['User']['id']];
                                  } else {
                                      echo $this->Html->link($user['User']['sub_company_count'], array('action' => 'index', $pageDetailWithRole['role'] => encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view users of %s ', $user['User']['first_name'])));
                                  }

                                  ?>
                              </td>
                          <?php endif; ?>
                          <?php if ($pageDetailWithRole['role'] == 'Dealer' && empty($parentId) && (isAdmin())): ?>
                              <td class="text_right">
                                  <?php
                                  echo $this->Html->link($user['User']['dealer_company_count'], array('controller' => 'companies', 'action' => 'index', 0, encrypt($user['User']['id'])), array('class' => 'no-hover-text-decoration', 'title' => __('Click here to view list of child ' . $pageDetailWithRole['singularTitle'])));

                                  ?>
                              </td>
                          <?php endif; ?>
                          <?php if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && (isSuparAdmin() || isAdminAdmin())): ?>
                              <td class="text_right">
                                  <?php echo $user['User']['station_count']; ?>
                              </td>
                          <?php endif;

                          ?>
                          <?php if (!(isDealer() && $pageDetailWithRole['role'] == COMPANY)): ?>
                              <td><?php echo showdatetime($user['User']['last_login_time'], __('N/A')); ?></td>
                          <?php endif; ?>
                          <td><?php echo showdatetime($user['User']['created'], __('N/A')); ?></td>
                          <!-- <?php if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && !isCompany()): ?>
                              <td>
                                  <?php
                                  echo '<b>' . __('Username: ') . '</b>' . (isset($user['CompanyBranch'][0]['ftpuser']) ? $user['CompanyBranch'][0]['ftpuser'] : '');
                                  echo '<br><b>' . __('Password: ') . '</b>' . (isset($user['CompanyBranch'][0]['ftp_pass']) ? $user['CompanyBranch'][0]['ftp_pass'] : '');

                                  ?>
                              </td>
                          <?php endif; ?> -->
                          <?php if (($this->params['controller'] != 'analytics')): ?>
                              <td class="actions text-center">
                                  <span class='text-left'>
                                      <?php
                                      echo $this->Html->link(__(''), array('action' => 'edit', encrypt($user['User']['id']), $formParamter), array('icon' => 'icon-pencil5 edit', 'class' => 'no-hover-text-decoration', 'title' => __('Edit %s', $search_role)));
              //                                            if (in_array($this->Session->read('Auth.User.user_type'), array('Admin', SUPAR_ADM))):
                                      if ($user['User']['role'] != DEALER) {
                                          echo $this->Html->link(__(''), array('action' => 'password_reset', encrypt($user['User']['id'])), array('icon' => 'icon-key fa-key', 'class' => 'no-hover-text-decoration', 'title' => __('Reset password')), __('Do you want to reset password?'));
                                      }elseif($user['User']['role'] == DEALER && $user['User']['user_type'] == "Super"){
                                        echo $this->Html->link(__(''), array('action' => 'password_reset', encrypt($user['User']['id'])), array('icon' => 'icon-key fa-key', 'class' => 'no-hover-text-decoration', 'title' => __('Reset password')), __('Do you want to reset password?'));
                                      }elseif($user['User']['role'] == DEALER && $user['User']['user_type'] == "Support"){
                                        echo $this->Html->link(__(''), array('action' => 'password_reset', encrypt($user['User']['id'])), array('icon' => 'icon-key fa-key', 'class' => 'no-hover-text-decoration', 'title' => __('Reset password')), __('Do you want to reset password?'));
                                      }
              //                                            endif;
                                      $sessionData = getMySessionData();
              //                                                if ($this->Session->read('Auth.User.id') != $user['User']['id']) {
                                      if ($sessionData['id'] != $user['User']['id']) {
                                          echo $this->Html->link(__(''), array('action' => 'delete', encrypt($user['User']['id'])), array('icon' => 'icon-trash delete', 'class' => 'no-hover-text-decoration', 'title' => __('Delete %s', $search_role)), __('Are you sure you want to delete %s ?', $search_role));
                                          if (!(isDealer() && ($pageDetailWithRole['role'] == COMPANY)) && in_array($pageDetailWithRole['role'], array('Dealer', 'Company')) && $user['User']['user_type'] == SUPAR_ADM):
                                              echo $this->Html->link(__(''), array('action' => 'add', encrypt($user['User']['id']), $formParamter), array('icon' => 'icon-user-plus fa-user', 'class' => 'no-hover-text-decoration', 'title' => __('Add user to %s', $search_role)));
                                          endif;
                                      }
                                      if ((isSuparAdmin() || isSuparDealer()) && $pageDetailWithRole['role'] == COMPANY && empty($parentId)):
                                          echo $this->Html->link(__(''), array('controller' => 'company_branches', 'action' => 'add', encrypt($user['User']['id'])), array('icon' => 'icon-plus-circle2 fa-plus', 'class' => 'no-hover-text-decoration', 'title' => __('Click here to add branch')));
                                      endif;
                                      if (isAdmin() && ($pageDetailWithRole['role'] != ADMIN)):
                                      //echo $this->Html->link(__(''), array('action' => 'user_dashboard', encrypt($user['User']['id'])), array('icon' => 'fa-dashboard', 'class' => 'no-hover-text-decoration', 'title' => __('Click here to view dashboard of %s', $search_role)));
                                      endif;

                                      ?>
                                  </span>
                              </td>
                          <?php endif; ?>
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
        validateSearch("UserSearchForm", ["UserName", "UserEmail", "UserStatus", "UserUserType", "UserDealerId"]);
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
