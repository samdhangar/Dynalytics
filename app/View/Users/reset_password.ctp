<div class="form-box" id="login-box" style="margin-top: 0">
    <div class="header bg-danger"><?php echo __("Reset Password"); ?></div>	
    <?php echo $this->Form->create('User', array('id' => 'UserResetPasswordForm', 'class' => 'form-signin','inputDefaults'=>array('dir'=>'ltl','class'=>'form-control', 'div' => array('class' => 'form-group')))); ?>
    <div class="body bg-gray">			
        <?php
        echo $this->Form->input('password', array('dir'=>'ltl','type' => 'password', 'placeholder' => 'Password'));
        echo $this->Form->input('confirm_password', array('dir'=>'ltl','type' => 'password', 'placeholder' => 'Confirm Password'));
        ?>
    </div>
    <div class="footer text-center bg-gray">
        <?php echo $this->Form->submit('Reset Password', array('class' => 'btn btn-primary btn-block')); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script type='text/javascript'>
    jQuery(document).ready(function () {
        jQuery("#UserResetPasswordForm").validate({
            rules: {
                "data[User][password]": {
                    required: true,
                    minlength:6,
                    maxlength:15
                },
                "data[User][confirm_password]": {
                    required: true,
                    equalTo: "#UserPassword"
                }
            },
            messages: {
                "data[User][password]": {
                    required: "Please enter password.",
                    minlength: "Please enter password at least 6 characters.",
                    maxlength: "Please enter password between 6 to 15 characters."
                },
                "data[User][confirm_password]": {
                    required: "Please enter confirm password.",
                    equalTo: "Please enter the same password as above."
                }
            }
        });
    });
</script>
