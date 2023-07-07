<?php echo $this->Form->create('User', array('inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'form-group')))); ?>
<div class="login-container" id="login-box" style="margin-top: 0">
    <div class="page-content"  ">
        <div class="panel panel-body login-form border-left border-left-lg border-left-info"  >


    <div class="text-center m-b-20">

        <div><img src="<?php echo $this->webroot;?>img/login_icon.png" /></div>
                        <h5>Sign in to your account</h5>
                    </div>
                    <div class="form-group has-feedback has-feedback-left">
                         <?php echo $this->Form->input('email', array('placeholder' => 'E-mail Address', 'label' => false)) ?>
                        <div class="form-control-feedback">
                            <i class="icon-user text-muted"></i>
                        </div>
                    </div>
       <div class="form-group has-feedback has-feedback-left">
                        <?php echo $this->Form->input('password', array('placeholder' => 'Password', 'label' => false)) ?>
                        <div class="form-control-feedback">
                            <i class="icon-lock text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group">
						<button type="submit" class="btn btn-info btn-labeled btn-labeled-right btn-block">
              <b><i class="icon-enter"></i></b> Sign in</button>
					</div>



</div>

<div class="footer text-size-mini">
				© 2018 DynaLytics •&nbsp;&nbsp;&nbsp;Version - 1.1.0
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
        'password' => array(
            'required' => 1
        )
    ),
    'Messages' => array(
        'email' => array(
            'required' => __("Please enter Email Address"),
            'email' => __("Please enter valid Email Address"),
        ),
        'password' => array(
            'required' => __("Please enter password")
        )
    )
));

?>
<?php echo $this->Form->end(); ?>
