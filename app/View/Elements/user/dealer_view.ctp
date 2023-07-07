<?php
$leftTitle = __('User Information');
$first_name_lbl = __('User Name');
$sec_name_lbl = __('');
if (empty($user['User']['parent_id'])) {
    $leftTitle = __('Dealer Information');
    $first_name_lbl = __('Business Name');
    $sec_name_lbl = __('Contact Name');
}

?>
<?php if (empty($user['User']['parent_id'])): ?>
 <div class="col-lg-3 col-sm-4">
            <!-- User thumbnail -->
            <div class="thumbnail">
              <div class="thumb thumb-rounded thumb-slide">
               <?php echo $this->Html->image(getUserPhoto($user['User']['id'], $user['User']['photo'], false, 200), array('class' => 'thumbnail img-responsive')) ?>        
              </div>        
              <div class="caption text-center">
                <h3 class="no-margin"> <?php echo $user['User']['first_name']; ?>
                     <?php if (empty($user['User']['parent_id'])): ?>
                         <?php echo $user['User']['last_name']; ?>
                        <?php endif; ?></h3>
              </div>
            </div>
          </div>
    
<?php endif; ?>
 
         
          <div class="col-lg-12 col-sm-8">
            <div class="panel panel-flat bg-slate-dark">
            <div class="panel panel-flat" style="margin-bottom: 0px;">
              <div class="panel-heading"><label for="analyBranchId" ><b><span style="font-size:20px;"> <?php echo $leftTitle; ?></span></b></label>
                <h5 class="panel-title"></h5>
              </div>          
              <div class="panel-body">
                <table class="table table-borderless table-striped">
                <tbody>
                <tr>
                <td><strong>Name :</strong></td>
                <td><a href="javascript:void(0)"><?php echo $user['User']['first_name']; ?>
                     <?php if (empty($user['User']['parent_id'])): ?>
                         <?php echo $user['User']['last_name']; ?>
                        <?php endif; ?></a></td>
                </tr>
                 <?php if (empty($user['User']['parent_id'])): ?>

                <tr>
                <td><strong>Email :</strong></td>
                <td><a href="javascript:void(0)"><?php echo $user['User']['email']; ?></a></td>
                </tr>
                <tr>
                <td><strong>Phone :</strong></td>
                <td><a href="javascript:void(0)"><?php echo showPhoneNo($user['User']['phone_no']); ?></a></td>
                </tr>
                <?php endif; ?> 
               
                <?php if (!empty($user['User']['parent_id'])): ?>
                <tr>
                <td><strong>Role :</strong></td>
                <td><a href="javascript:void(0)" ><?php echo getLoginRole($user['User']['role'], $user['User']['user_type']); ?></a></td>
                </tr>
                <?php endif; ?>
                <?php if (empty($user['User']['parent_id'])): ?>
                <tr>
                <td><strong>Address :</strong></td>
                <td><a href="javascript:void(0)" ><?php echo $this->Custom->displayAddress($userDetailArr['addressArr']); ?></a></td>
                </tr>
                <tr>
                <td><strong>Membership :</strong></td>
                <td><a href="javascript:void(0)" ><?php echo isset($subscriptions[$user['User']['subscription_id']]) ? $subscriptions[$user['User']['subscription_id']] : ''; ?></a></td>
                </tr>
                <tr>
                <?php else: ?>
                <td><strong>User Type :</strong></td>
                <td><a href="javascript:void(0)" ><?php echo getLoginRole($user['User']['role'], $user['User']['user_type']); ?></a></td>
                </tr>
                <tr>
                <td><strong>Phone :</strong></td>
                <td><a href="javascript:void(0)" ><?php echo showPhoneNo($user['User']['phone_no']); ?></a></td>
                </tr>
                <?php endif; ?>
                </tbody>
                </table>
              </div>
            </div>
          </div>
        