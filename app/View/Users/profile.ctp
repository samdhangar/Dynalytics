<?php
$this->assign('pagetitle', __('My Profile'));
$this->Custom->addCrumb(__('My Profile'));
$this->start('top_links');
if (!empty($this->request->data['User'])) {
    echo $this->Html->link(__('Change Password'), array('controller' => $this->params['controller'], 'action' => 'change_password'), array('icon' => 'fa-lock', 'class' => 'btn btn-sm btn-primary', 'escape' => false, 'title' => __('Click here to change password')));
    echo $this->Html->link(__('Edit My Profile'), 'javascript:void(0)', array('icon' => 'fa-edit', 'class' => 'edit-profile btn btn-default btn-sm btn-sm marginleft', 'escape' => false, 'title' => __('Click here to edit my profile')));
}
echo $this->Html->link(__('My Profile'), 'javascript:void(0)', array('icon' => 'fa-user', 'class' => 'profile hidden btn btn-default btn-sm marginleft', 'escape' => false, 'title' => __('Click here to edit my profile')));

$this->end();

?>

<div class="row">
<?php echo $this->Form->create('User', array('class' => 'form-validate','id' => 'UserEditProfileForm', 'url' => array('controller' => 'users', 'action' => 'profile'), 'type' => 'file', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'form-group')))); ?>
    <div class="col-md-12 col-sm-12">
        <div class="panel-flat">

            <div class="panel-body">



<div class="box-body box-content  overflow-hide-break">

  <div class="userProfilePage userViewPage profile <?php echo isset($validationErrors) ? 'hidden' : '' ?>">
      <?php  if (empty($this->request->data['User'])) { ?>
          <?php echo $this->Html->showInfo(__('Invalid User.'), array('type' => 'warning')) ?>
      <?php } else { ?>
          <?php
          $userDetailArr['addressArr'] = array(
              'address' => $this->request->data['User']['address'],
              'city' => $user_address['City'],
              'state' => $user_address['State'],
              'country' => $user_address['Country'],
              'pincode' => $this->request->data['User']['pincode']
          );

          if ($this->request->data['User']['role'] == COMPANY):
              echo $this->element('user/company_view', array('user' => $this->request->data, 'userDetailArr' => $userDetailArr));
          elseif ($this->request->data['User']['role'] == DEALER):
              echo $this->element('user/dealer_view', array('user' => $this->request->data, 'userDetailArr' => $userDetailArr));
          else:
              echo $this->element('user/admin_view', array('user' => $this->request->data, 'userDetailArr' => $userDetailArr));
          endif;

          ?>
      <?php } ?>
  </div>



  <div class="edit-profile <?php echo isset($validationErrors) ? '' : 'hidden' ?>">
      <div class="col-md-6 col-sm-6 no-padding">
          <?php 
          echo $this->Form->input('id', array('type' => 'hidden'));
          $label = isCompany() ? __('Company Name') : __('First name');
          if (isDealer()) {
              $label = __('Business Name');
          }
          echo $this->Form->input('first_name', array('required' => true, 'label' => $label, 'placeholder' => $label));

          if (isDisplayFields()) {
              $label = isCompany() ? __('Contact Name') : __('Last name');
              if (isDealer()) {
                  $label = __('Contact Name');
              }
              echo $this->Form->input('last_name', array('required' => false, 'label' => $label, 'placeholder' => $label));
          }
          echo $this->Form->input('email', array('required' => false, 'placeholder' => __('Email')));
          if (isCompany() && empty($this->request->data['User']['parent_id'])) {
              echo $this->Form->input('file_send_email', array('required' => false, 'placeholder' => __('File Send Email')));
          }
          echo $this->Form->input('phone_no', array(
              'placeholder' => 'Phone Number',
              'class' => 'form-control phoneNumber',
              'label' => __('Phone No'),
              'type' => 'text',
              'div' => array('class' => 'required form-group')));

          ?>
      </div>
      <div class="col-md-6 col-sm-6 no-rightpadding">
          <?php 
          if (isDisplayFields() && !isDealer()) {
              echo $this->element('address');
          }
//                echo $this->Form->input('address', array('type' => 'textarea', 'rows' => 4, 'required' => false));

          ?>
          <?php if (isDisplayFields()): ?>
              <label class="form-group" style="margin-bottom: 10px">
                  <?php
                  if (isAdmin()) {
                      echo __('Profile Picture');
                  } else {
                      if(isDealer()){
                          echo __('Business Logo');
                      }
                      if(isCompany()){
                          echo __('Company Logo');
                      }
                  }

                  ?>
              </label>
              <div class="form-group row">
                  <?php
                  if (empty($this->request->data['User']['id'])) {
                      $this->request->data['User']['id'] = 0;
                  }
                  if (empty($this->request->data['User']['photo'])) {
                      $this->request->data['User']['photo'] = '';
                  }
                  echo "<div id='UserProfileImageId' class='col-md-4'>" . $this->Html->image(getUserPhoto($this->request->data['User']['id'], $this->request->data['User']['photo']), array('class' => 'thumbnail img-responsive', 'style' => 'max-width: 250px')) . "</div>";
                  echo $this->Form->input('photo', array('class' => '', 'required' => false, 'label' => false, 'type' => 'file', 'before' => '<label for="UserPhoto" class=""><i class="">&nbsp;</i>' . __('Browse Photo') . '</label>', 'div' => array('class' => 'col-md-10 form-group')));

                  ?>
                  <div for="userProfileImage" generated="true" class="error" style="display: none">
                      <span class="errorDV"> </span>
                  </div>
              </div>
          <?php endif; ?>
      </div>
      <div class="form-action edit-profile <?php echo isset($validationErrors) ? '' : 'hidden' ?>">
          <?php
          echo $this->Form->submit(__('Save'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
          echo $this->Html->link('Cancel', array('controller' => $this->params['controller'], 'action' => 'profile'), array('icon' => 'cancel', 'class' => 'btn btn-default marginleft'));

          ?>
      </div>
  </div>
</div>
</div>
<?php
echo $this->Form->setValidation(array(
    'Rules' => array(
        'email' => array(
            'required' => 1,
            'email' => 1
        ),
        'first_name' => array(
            'minlength' => 2,
            'maxlength' => 50,
            'required' => 1
        ),
        'last_name' => array(
            'minlength' => 2,
            'maxlength' => 50
        ),
        'photo' => array(
            'accept' => "jpg|jpeg|gif|png",
        ),
        'address' => array(
            'required' => 1,
            'minlength' => 2,
            'maxlength' => 500
        ),
        'country_id' => array(
            'required' => 1
        ),
        'state_id' => array(
            'required' => 1
        ),
        'city_id' => array(
            'required' => 1
        ),
        'pincode' => array(
            'maxlength' => 5,
            'required' => 1,
            'matchNumber' => 5
        ),
        'phone_no' => array(
            'required' => 1,
            'matchNumber' => 10
        )
    ),
    'Messages' => array(
        'email' => array(
            'required' => __("Please enter Email Address"),
            'email' => __("Please enter valid Email Address"),
        ),
        'first_name' => array(
            'minlength' => __('Please enter first name with minimum 2 characters.'),
            'maxlength' => __('Please enter first name with maximum 2 characters.'),
            'required' => __('Please enter first name.')
        ),
        'last_name' => array(
            'minlength' => __('Please enter last name with minimum 2 characters.'),
            'maxlength' => __('Please enter last name with maximum 2 characters.')
        ),
        'photo' => array(
            'accept' => __('Please select valid photo with "jpg|jpeg|gif|png" extension'),
        ),
        'address' => array(
            'required' => __('Please enter address.'),
            'minlength' => __('Please enter address having minimum 2 characters.'),
            'maxlength' => __('Please enter address having maximum 500 characters.')
        ),
        'country_id' => array(
            'required' => __('Please select country')
        ),
        'state_id' => array(
            'required' => __('Please select state')
        ),
        'city_id' => array(
            'required' => __('Please select city')
        ),
        'pincode' => array(
            'maxlength' => __('Please enter valid zip code'),
            'required' => __('Please enter zip code'),
            'matchNumber' => __('Please enter valid zip code')
        ),
        'phone_no' => array(
            'required' => __('Please enter Mobile number'),
            'matchNumber' => __("Please enter valid Mobile number.")
        )
    )
));

?>




            </div>
        </div>



    </div>
<?php echo $this->Form->end(); ?>
</div>

<!--Rightbar Chat-->

<!--/Rightbar Chat-->

<script type='text/javascript'>
    jQuery(document).ready(function () {
        jQuery('a.profile').on('click', function () {
            jQuery('.edit-profile').addClass('hidden');
            jQuery('.profile,a.edit-profile').removeClass('hidden');
            jQuery(this).addClass('hidden');
        });
        jQuery('a.edit-profile').on('click', function () {
            jQuery('.profile').addClass('hidden');
            jQuery('a.profile').removeClass('hidden');
            jQuery('.edit-profile').removeClass('hidden');
            jQuery(this).addClass('hidden');
        });
    });
</script>
