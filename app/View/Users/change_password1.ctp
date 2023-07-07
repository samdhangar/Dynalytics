<?php
if (!empty($profile)) {
    $this->assign('pagetitle', __('Change Password'));
    $this->Custom->addCrumb(__('My Profile'), array('action' => 'view'));
    $this->Custom->addCrumb(__('Change Password'));
    $this->start('top_links');
        echo $this->Html->link(__('Edit Profile'), array('action' => 'edit'), array('icon' => 'edit', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Click here to edit profile'));
        echo $this->Html->link('Back', array('action' => 'index'), array('icon' => 'back', 'class' => 'btn btn-default', 'escape' => false));
    $this->end();
} else {
    $this->assign('pagetitle', __('Reset Password'));
    $this->Custom->addCrumb(__('Employees'), array('action' => 'index'));
    $this->Custom->addCrumb(__('Reset Password'));
    $this->start('top_links');
        echo $this->Html->link(__('Edit'), array('action' => 'edit'), array('icon' => 'edit', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Click here to edit profile'));
        echo $this->Html->link('Back', array('action' => 'index'), array('icon' => 'back', 'class' => 'btn btn-default', 'escape' => false));
    $this->end();
}
?>
<div class='row'>
    <div class='col-md-12'>
        <div class='box box-primary'>           
            <div class='box-body pad'>                
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo $this->Form->create('User', array('id' => 'UserChangePasswordForm', 'class' => 'form-signin','inputDefaults'=>array('dir'=>'ltl','class'=>'form-control', 'div' => array('class' => 'form-group'))));
                        if (!empty($profile)) {
                            echo $this->Form->input('old_password', array('type' => 'password', 'placeholder' => 'Old Password'));
                        }
                        echo $this->Form->input('new_password', array('type' => 'password', 'placeholder' => 'New Password'));
                        echo $this->Form->input('confirm_password', array('type' => 'password', 'placeholder' => 'Confirm Password'));
                        ?>
                        <div class="form-action">
                            <?php
                            echo $this->Form->submit(__('Change Password'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                            echo $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default','icon'=>'cancel'));
                            ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!-- /.col-->
</div><!-- ./row -->
<script type='text/javascript'>
    jQuery(document).ready(function() {
        jQuery("#UserChangePasswordForm").validate({
            rules: {
                "data[User][old_password]": {
                    required: true
                },
                "data[User][new_password]": {
                    required: true,
                    minlength: 6,
                    maxlength: 15
                },
                "data[User][confirm_password]": {
                    required: true,
                    equalTo: "#UserNewPassword"
                }
            },
            messages: {
                "data[User][old_password]": {
                    required: "Please enter old password"
                },
                "data[User][new_password]": {
                    required: "Please enter new password.",
                    minlength: "Please enter password at least 6 characters.",
                    maxlength: "Please enter password between 6 to 15 characters."
                },
                "data[User][confirm_password]": {
                    required: "Please enter confirm password",
                    equalTo: "Please enter the same password as above."
                }
            }
        });
    });
</script>
