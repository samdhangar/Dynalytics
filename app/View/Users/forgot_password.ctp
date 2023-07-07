<?php echo $this->Form->create('User', array('id' => 'UserForgotpasswordForm', 'class' => 'form-signin', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'form-group')))) ?>
<div class="form-box" id="login-box" style="margin-top: 0">    
    <div class="header bg-danger"><?php echo __("Forgot Password"); ?></div>	
    <div class="body bg-gray">			
        <?php echo $this->Form->input('email', array('dir' => 'ltl', 'label' => 'Email Address', 'placeholder' => 'Email Address')); ?>
    </div>
    <div class="footer text-center bg-gray">
        <?php
        echo $this->Form->submit(' Reset Password ', array('class' => 'btn btn-primary btn-block', 'escape' => false, 'div' => false));
        echo $this->Html->link('Already have an account?', array('controller' => 'users', 'action' => 'login'), array('class' => 'text-center new-account'));

        ?>
    </div>
</div>
<?php
echo $this->Form->setValidation(array(
    'Rules' => array(
        'email' => array(
            'required' => 1,
            'email' => 1
        )
    ),
    'Messages' => array(
        'email' => array(
            'required' => __("Please enter Email Address"),
            'email' => __("Please enter valid Email Address"),
        )
    )
));

?>
<?php echo $this->Form->end(); ?>