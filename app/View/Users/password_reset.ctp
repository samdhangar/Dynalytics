<?php
$this->assign('pagetitle', __('Reset Password'));
$this->Custom->addCrumb(__('%s Detail', $pageDetailWithRole['singularTitle']), array('controller' => $this->params['controller'], 'action' => 'index'));
$this->Custom->addCrumb($user['User']['name'], array('controller' => $this->params['controller'], 'action' => 'view', $user['User']['id']));
$this->Custom->addCrumb(__('Reset Password'));
$this->start('top_links');
echo $this->Html->link('Back', array('controller' => $this->params['controller'], 'action' => 'index'), array('icon' => 'back', 'class' => 'btn btn-default pull-right', 'escape' => false));
$this->end();

?>
<?php
echo $this->Form->create('User', array('id' => 'UserChangePasswordForm', 'class' => 'form-signin', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'form-group'))));

?>
<div class="row page-start">
    <div class='col-md-12'>
        <div class='box box-primary'>           
            <div class='box-body pad'>                
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo $this->Form->input('password', array('type' => 'password', 'placeholder' => 'New Password'));
                        echo $this->Form->input('confirm_password', array('type' => 'password', 'placeholder' => 'Confirm Password'));

                        ?>
                        <div class="form-action">
                            <?php
                            echo $this->Form->submit(__('Reset Password'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                            echo $this->Html->link('Cancel', array('controller' => $this->params['controller'], 'action' => 'index'), array('class' => 'btn btn-default', 'icon' => 'cancel'));

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Form->setValidation(array(
    'Rules' => array(
        'password' => array(
            'required' => 1
        ),
        'confirm_password' => array(
            'required' => 1,
            'equalTo' => '#UserPassword'
        )
    ),
    'Messages' => array(
        'password' => array(
            'required' => __("Please enter password")
        ),
        'confirm_password' => array(
            'required' => __('Please enter confirm password.'),
            'equalTo' => __('Please enter same as above password.')
        )
    )
));

?>
<?php echo $this->Form->end(); ?>
<script type='text/javascript'>
    jQuery(document).ready(function () {
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
                    required: "Please enter new password",
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

