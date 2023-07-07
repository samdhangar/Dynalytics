<?php
$label_first = "Business Name";
$place_holder_first = "Enter business name";
$label_second = "Contact Name";
$place_holder_second = "Enter contact name";
if (isDealer() || !empty($parentId)):
    $label_first = "Name";
    $place_holder_first = "Enter name";
    $label_second = "Name";
    $place_holder_second = "Enter name";
endif;
$id = '';
$photo = '';
if (isset($this->request->data['User']['id'])) {
    $id = $this->request->data['User']['id'];
    $photo = isset($this->request->data['User']['photo']) ? $this->request->data['User']['photo'] : '';
}

?>


<div class="row">

    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">

            <div class="panel-body">

                 <?php echo $this->Form->create('User', array('id' => 'UserEditProfileForm', 'type' => 'file', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>
<div class="box-body box-content">
<?php
echo $this->Form->input('id', array('type' => 'hidden')); ?>



<div class="row no-margin">
    <?php if (isDealer() || !empty($parentId)) { ?>
        <div class="col-md-6">
            <?php
            if ((isSuparAdmin() || isAdminAdmin()) && !empty($parentId)):
                echo $this->Form->input('parent_id', array('label' => __($pageDetailWithRole['singularTitle'] . ' Name'), 'type' => 'select', 'onchange' => 'setPageTitle(this.value)', 'empty' => __('Select ' . $pageDetailWithRole['singularTitle'])));
            endif;
            $label_first = 'Name';
            $place_holder_first = 'Enter Name';
            echo $this->Form->input('first_name', array('placeholder' => $place_holder_first, 'label' => $label_first));
            echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));

            if ($from == 'Edit' && isset($this->request->data['User']['role']) && $this->request->data['User']['role'] != DEALER) {
                echo $this->Form->input('is_pwd_change', array('hiddenField' => false, 'type' => 'checkbox', 'label' => __('Want to reset password?'), 'class' => 'chkbox', 'title' => __('Click here to make reset password'), 'div' => array('class' => 'form-group checkboxDiv')));
            }

            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $this->Form->input('email', array('placeholder' => 'Email'));
            $label_first = $pageDetailWithRole['singularTitle'] . ' Type';
            $place_holder_first = 'Select ' . $pageDetailWithRole['singularTitle'] . ' Type';

            echo $this->Form->input('user_type', array('div' => array('class' => 'form-group clearBoth col-md-12 no-padding'), 'label' => __($label_first), 'empty' => __($place_holder_first)));

            ?>
        </div>
        <div class="col-md-12 col-sm-12 branchSelectDiv" style="display: <?php echo (isset($this->request->data['User']['user_type']) && $this->request->data['User']['user_type'] == SUPPORT) ? 'block' : 'none' ?>;">
            <label class="col-md-12 col-sm-12 no-padding">
                <?php echo __('Assign Clients'); ?>

            </label>
            <?php
            if (empty($clients)) {
                echo '<br>' . __('No any company yet.');
            }
            $totalDi = 0;
            $count = 0;
            echo $this->Form->input('dealer_company_id', array('label' => __('Client'), 'div' => array('class' => 'required form-group multiSelctUser'), 'id' => 'DealerMulticompanyId', 'multiple' => true, 'options' => $clients));
            echo $this->Form->input('dealer_branch_id', array('label' => __('Branch'), 'div' => array('class' => 'required form-group multiSelctUser'), 'id' => 'DealerMultibranchId', 'multiple' => true, 'options' => $branches));

            ?>
        </div>
    <?php } else { ?>
        <div class="col-md-6">
            <?php
            echo $this->Form->input('first_name', array('placeholder' => $place_holder_first, 'label' => $label_first));
            echo $this->Form->input('last_name', array('required' => false, 'placeholder' => $place_holder_second, 'label' => $label_second));
            echo $this->Form->input('email', array('placeholder' => __('Enter email'), 'label' => __('Email')));
            echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));
            echo $this->Form->input('user_type', array('type' => 'hidden','value' => 'Super'));
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $this->Form->input('subscription_id', array('label' => __('Membership'), 'type' => 'select', 'div' => array('class' => 'form-group required no-padding clearBoth'), 'id' => 'UserSubscriptionId', 'empty' => __('Select membership')));
            echo $this->element('user/logoDiv', array('id' => $id, 'photo' => $photo, 'logoTitle' => __('Business Logo')));

            ?>
        </div>
    <?php } ?>

</div>


<div class="form-action">
<?php echo $this->Form->submit(__('Save %s', $pageDetailWithRole['singularTitle']), array('class' => 'btn btn-primary margin-right10', 'div' => false)); ?>
&nbsp;&nbsp;
<?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default marginleft')); ?>
</div>
</div>
<?php
$arrValidation = array(
    'Rules' => array(
        'first_name' => array(
            'minlength' => 2,
            'maxlength' => 50,
            'required' => 1,
            'alphabates' => 1
        ),
        'email' => array(
            'required' => 1,
            'email' => 1
        ),
        // 'phone_no' => array(
        //     'required' => 1,
        //     'matchNumber' => 10
        // )
    ),
    'Messages' => array(
        'first_name' => array(
            'minlength' => __('Please enter ' . strtolower($label_first) . ' with minimum 2 character.'),
            'maxlength' => __('Please enter ' . strtolower($label_first) . ' between 2 to 50 character.'),
            'required' => __('Please enter ' . strtolower($label_first)),
            'alphabates' => __('Please enter alphabates only')
        ),
        'email' => array(
            'required' => __('Please enter email address'),
            'email' => __('Please enter valid email address')
        ),
        // 'phone_no' => array(
        //     'required' => __('Please enter phone no'),
        //     'matchNumber' => __('Please enter valid phone no')
        // )
    )
);
/**
 * Add Super Dealer Condition
 */
if (empty($parentId) && isAdmin()) :
    $arrValidation['Rules']['last_name'] = array(
        'minlength' => 2,
        'maxlength' => 50,
        'required' => 1,
        'alphabates' => 1
    );
    $arrValidation['Messages']['last_name'] = array(
        'minlength' => __('Please enter ' . strtolower($label_second) . ' with minimum 2 character.'),
        'maxlength' => __('Please enter ' . strtolower($label_second) . ' between 2 to 50 character.'),
        'required' => __('Please enter ' . strtolower($label_second)),
        'alphabates' => __('Please enter alphabates only')
    );
    $arrValidation['Rules']['subscription_id'] = array(
        'required' => 1
    );
    $arrValidation['Messages']['subscription_id'] = array(
        'required' => __('Please select membership')
    );
    $arrValidation['Rules']['photo'] = array(
        'accept' => 'jpg|jpeg|png|bmp|gif'
    );
    $arrValidation['Messages']['photo'] = array(
        'accept' => __('Please choose files having jpg, jpeg, png, bmp, gif extension.')
    );
elseif (isDealer() || !empty($parentId)):
    $arrValidation['Rules']['user_type'] = array(
        'required' => 1
    );
    $arrValidation['Messages']['user_type'] = array(
        'required' => __('Please select type')
    );
endif;
echo $this->Form->setValidation($arrValidation);
echo $this->Form->end();

?>


<script type="text/javascript">
    jQuery(document).ready(function () {
<?php if ($pageDetailWithRole['role'] != ADMIN): ?>
            jQuery('#UserEditProfileForm').submit(function (e) {
                if (jQuery("#UserUserType").val() == 'Support') {
                    console.log(jQuery('#DealerMulticompanyId').val());
                    if (jQuery(".branchSelectDiv").length != 0 && jQuery('#DealerMulticompanyId').val() == null || jQuery('#DealerMultibranchId').val() == null) {
                        e.preventDefault();
                        if (jQuery('.branchSelectDiv .errorDV').length < 1)
                            jQuery('.branchSelectDiv ').append('<div class="col-md-12 no-padding"><span class="errorDV">Please assign clients/branches</span><div>');

                    } else {
                        jQuery('.branchSelectDiv .errorDV').html('&nbsp;');
                    }
                }
            });
<?php endif; ?>

        $('#DealerMulticompanyId').multiselect({
            maxHeight: 200,
            dropUp: true,
            onChange: function (element, checked) {
                var data = jQuery('#DealerMulticompanyId').val();
                console.log(data);
                loader('show');
                jQuery.ajax({
                    url: BaseUrl + "/users/getBranches/multiselect/html",
                    type: 'post',
                    data: {data: jQuery('#DealerMulticompanyId').val()},
                    success: function (response) {
                        loader('hide');
                        jQuery('#DealerMultibranchId').multiselect('destroy');
                        jQuery('#DealerMultibranchId').html(response);
                        jQuery('#DealerMultibranchId').multiselect({maxHeight: 200, dropUp: true});
                    },
                    error: function () {
                        loader('hide');
                    }
                });
            }
        });
        jQuery('#DealerMultibranchId').multiselect();
<?php if ($from == 'Edit'): ?>


            var tmp = '<?php echo json_encode($delerClients); ?>';
            tmp = $.parseJSON(tmp);
            var tmparrayDealer = [];
            $.each(tmp, function (k, v) {
                tmparrayDealer.push(k);
            });

            $('#DealerMulticompanyId').multiselect('select', tmparrayDealer);

            var tmpComp = '<?php echo json_encode($dealerBranches); ?>';
            tmpComp = $.parseJSON(tmpComp);
            var tmparrayCompany = [];
            $.each(tmpComp, function (k, v) {
                tmparrayCompany.push(k);
            });

            $('#DealerMultibranchId').multiselect('select', tmparrayCompany);
<?php endif; ?>



        jQuery('#UserUserType').on('change', function () {
            if (jQuery("#UserUserType").val() == '' || jQuery("#UserUserType").val() == '<?php echo ADMIN ?>') {
                jQuery('.branchSelectDiv').slideUp();
            } else {
                jQuery('.branchSelectDiv').slideDown();
            }
        }).trigger('change');
    });
</script>



            </div>
        </div>



    </div>

</div>

<!--Rightbar Chat-->

<!--/Rightbar Chat-->
