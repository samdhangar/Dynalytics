<?php
$formParamter = '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
if (isCompanyAdmin() || empty($parentId) || (($pageDetailWithRole['role'] == DEALER && isDealer()))):
    $this->assign('pagetitle', __('%s %s', $from, $pageDetailWithRole['singularTitle']));
else:
    $this->assign('pagetitle', '<span class="pageTitleSpan">' . __('%s', $parents[$parentId]) . '</span>' . __(' - %s User', $from));
endif;
$this->Custom->addCrumb(__('%s ', $pageDetailWithRole['pageTitle']), array('action' => 'index'));
if ((!empty($formParamter) || !empty($parentId)) && !isCompanyAdmin()):
    if (!($pageDetailWithRole['role'] == DEALER && isDealer()) && isset($parents[$parentId])) {
        if ($pageDetailWithRole['role'] == COMPANY):
            $this->Custom->addCrumb(__('%s', $parents[$parentId]), array('action' => 'index', $pageDetailWithRole['role'] => encrypt($parentId)));
        else:
            $this->Custom->addCrumb(__('%s - Users ', $parents[$parentId]), array('action' => 'index', $pageDetailWithRole['role'] => encrypt($parentId)));
        endif;
    }

endif;
if (empty($parentId) || isCompanyAdmin()):
    $this->Custom->addCrumb(__('%s %s', $from, $pageDetailWithRole['singularTitle']));
else:
    if ($pageDetailWithRole['role'] == COMPANY):
        $this->Custom->addCrumb(__('%s', $from));
    else:
        $this->Custom->addCrumb(__('%s User', $from));
    endif;
endif;
$this->start('top_links');
$backPams = $formParamter;
if (empty($backPams) && !empty($parentId)) {
    $backPams = $pageDetailWithRole['singularTitle'] . ':' . encrypt($parentId);
}
echo $this->Html->link(__('Back'), array('action' => 'index', $backPams), array('icon' => 'back', 'class' => 'btn btn-default', 'escape' => false));
$this->end();


/**
 * set logo title,image
 */
$logoTitle = __('Profile Picture');
if ($pageDetailWithRole['role'] == DEALER && empty($parentId) ) {
    $logoTitle = __('Business Logo');
}
if ($pageDetailWithRole['role'] == COMPANY && empty($parentId)) {
    $logoTitle = __('Company Logo');
}

echo $this->Html->script('lib/bootstrap-multiselect');
echo $this->Html->css('bootstrap/bootstrap-multiselect');

$id = '';
$photo = '';
if (isset($this->request->data['User']['id'])) {
    $id = $this->request->data['User']['id'];
    $photo = isset($this->request->data['User']['photo']) ? $this->request->data['User']['photo'] : '';
}
if ($pageDetailWithRole['role'] == DEALER ) {
    echo $this->element('dealers/form');
} elseif ($pageDetailWithRole['role'] == COMPANY) {
    echo $this->element('company/form');
} else {

    ?>



    <div class="row">

        <div class="col-md-12 col-sm-12">
            <div class="panel panel-flat">

                <div class="panel-body">

    <?php echo $this->Form->create('User', array('id' => 'UserEditProfileForm', 'type' => 'file', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>

<div class="box-body box-content">
<?php echo $this->Form->input('id', array('type' => 'hidden')); ?>



<div class="row no-margin">
    <?php
    if ($pageDetailWithRole['role'] == DEALER && isDealer()) :
        $label_first = 'Name';
        $label_second = 'Name';
        echo $this->element('user/dealer_add');
    else:

        ?>
        <div class="col-md-6">
            <?php
            if ($pageDetailWithRole['role'] == 'Company' && $isDisplayDealer):
                echo $this->Form->input('dealer_id', array('onchange' => (($pageDetailWithRole['role'] == 'Company' && !empty($parentId)) ? 'getCompanyFromDealer(this.value)' : ''), 'id' => 'UserDealerId', 'label' => __('Dealer'), 'type' => 'select', 'empty' => __('Select Dealer'), 'div' => array('class' => 'form-group')));
            endif;
            //for the add sub dealers from admin,supar admin
            if ((isSuparAdmin() || isAdminAdmin()) && !empty($parentId)):
                echo $this->Form->input('parent_id', array('label' => __($pageDetailWithRole['singularTitle'] . ' Name'), 'type' => 'select', 'onchange' => 'setPageTitle(this.value)', 'empty' => __('Select ' . $pageDetailWithRole['singularTitle'])));
            endif;
            if ($pageDetailWithRole['role'] == COMPANY && $isDisplayCompanyParent):
                echo $this->Form->input('parent_id', array('label' => __(SUPAR_ADM . ' ' . $pageDetailWithRole['singularTitle']), 'type' => 'select', 'empty' => __('Select ' . $pageDetailWithRole['singularTitle'])));
            endif;

            $label_first = 'First Name';
            $place_holder_first = 'First Name';
            if ($pageDetailWithRole['role'] == DEALER) {
                $label_first = 'Business Name';
                $place_holder_first = 'Business Name';
            }
            if ($pageDetailWithRole['role'] == COMPANY) {
                if (empty($parentId)):
                    $label_first = (!isCompany()) ? 'Company Name' : 'Name';
                    $place_holder_first = (!isCompany()) ? 'Company Name' : 'Name';
                else:
                    $label_first = (!isCompany()) ? 'User Name' : 'Name';
                    $place_holder_first = (!isCompany()) ? 'User Name' : 'Name';
                endif;
            }
            if ($pageDetailWithRole['role'] == DEALER && isDealer()) {
                $label_first = 'Name';
                $place_holder_first = 'Enter Name';
            }
            echo $this->Form->input('first_name', array('placeholder' => $place_holder_first, 'label' => $label_first));
            $label_second = 'Last Name';
            $place_holder_second = 'Last Name';
            if ($pageDetailWithRole['role'] == COMPANY) {
                $label_second = 'Contact Name';
                $place_holder_second = 'Contact Name';
            }
            if ($pageDetailWithRole['role'] == DEALER) {
                $label_second = 'Contact Name';
                $place_holder_second = 'Contact Name';
            }
            if ($isDisplayFields && !isCompany()) {
                echo $this->Form->input('last_name', array('required' => false, 'placeholder' => $place_holder_second, 'label' => $label_second));
            }
            $label = __('Email');
            if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && !isCompany()) {
                $label = __('Company Super Admin Email');
            }
            if (!($pageDetailWithRole['role'] == COMPANY && !isCompany() && !empty($parentId))) {
                echo $this->Form->input('email', array('placeholder' => $label, 'label' => $label));
            }
            if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && !isCompany()) {
                echo $this->Form->input('file_send_email', array('label' => __('File Send Email'), 'placeholder' => __('File Send Email')));
            }
            if (!($pageDetailWithRole['role'] == COMPANY && !isCompany() && !empty($parentId))) {
                if (!isCompany()) {
                    echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));
                }
            }
            if ($from == 'Edit' && $this->request->data['User']['role'] != DEALER) {
                echo $this->Form->input('is_pwd_change', array('hiddenField' => false, 'type' => 'checkbox', 'label' => __('Want to reset password?'), 'class' => 'chkbox', 'title' => __('Click here to make reset password'), 'div' => array('class' => 'form-group checkbox')));
            }

            if (!isDealer() && $pageDetailWithRole['role'] != DEALER && (empty($this->params['pass']) && $pageDetailWithRole['role'] != COMPANY)):
                if ($isDisplayFields) {
                    echo $this->element('user/logoDiv', array('id' => $id, 'photo' => $photo, 'logoTitle' => $logoTitle));
                }
            endif;

            ?>
        </div>
        <div class="col-md-6">
            <?php
            if (!isCompany() && !(in_array($pageDetailWithRole['role'], array(COMPANY, DEALER)) && !empty($parentId))) {
                echo $this->element('address');
            }
            if ($pageDetailWithRole['role'] == DEALER || $pageDetailWithRole['role'] == COMPANY) {
                if (!isCompany() && empty($parentId)) {
                    if ($pageDetailWithRole['role'] == COMPANY && isDealer() || !isDealer()) {
                        echo $this->Form->input('subscription_id', array('label' => __('Membership'), 'type' => 'select', 'div' => array('class' => 'form-group required no-padding clearBoth'), 'id' => 'UserSubscriptionId', 'empty' => __('Select membership')));
                    }
                }
            }
            if (!in_array($pageDetailWithRole['role'], array(DEALER, COMPANY))) {
                echo $this->Form->input('communication_type', array('type' => 'select', 'div' => array('class' => 'form-group required no-padding clearBoth'), 'id' => 'UserCommunicationTypeId', 'empty' => __('Select communication type')));
            }
            if ($pageDetailWithRole['role'] == COMPANY) {
                $label_type = (!empty($parentId)) ? 'User Type' : $pageDetailWithRole['singularTitle'] . ' Type';
                $place_holder_type = (!empty($parentId)) ? 'Select user type' : 'Select ' . $pageDetailWithRole['singularTitle'] . ' Type';
            } else {
                $label_type = $pageDetailWithRole['singularTitle'] . ' Type';
                $place_holder_type = 'Select ' . $pageDetailWithRole['singularTitle'] . ' Type';
            }
            if (!empty($isDisplayUserType)):
                echo $this->Form->input('user_type', array('div' => array('class' => 'form-group clearBoth col-md-12 no-padding'), 'label' => __($label_type), 'empty' => __($place_holder_type)));
            else:
                echo $this->Form->input('user_type', array('type' => 'hidden', 'value' => SUPAR_ADM));
            endif;

            /**
             * set email and phone no on right side for not company login company user add
             */
            if ($pageDetailWithRole['role'] == COMPANY && !isCompany() && !empty($parentId)) {
                echo $this->Form->input('email', array('placeholder' => $label, 'label' => $label));
                echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));
            }

            if (isDealer() || $pageDetailWithRole['role'] == DEALER || !(empty($this->params['pass']) && $pageDetailWithRole['role'] != COMPANY) && (!isCompany())):
                if ($isDisplayFields) {
                    echo $this->element('user/logoDiv', array('id' => $id, 'photo' => $photo, 'logoTitle' => $logoTitle));
                }
            endif;

            if (isCompany()) {
                echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));
            }

            ?>


        </div>
    <?php endif; ?>


    <?php if (isSuparAdmin() && $pageDetailWithRole['role'] == ADMIN): ?>
        <div class="col-md-12 col-sm-12 branchSelectDiv" style="display: none;">
            <label class="col-md-12 col-sm-12 no-padding">
                <?php echo __('Assign Dealer / Company:'); ?>

            </label>
            <?php echo $this->Form->input('multidealer_id', array('label' => __('Dealer'), 'div' => array('class' => 'required form-group multiSelctUser'), 'id' => 'UserMultidealerId', 'multiple' => true, 'options' => $multidealer)); ?>
            <?php echo $this->Form->input('multicompany_id', array('label' => __('Company'), 'div' => array('class' => 'required form-group multiSelctUser'), 'id' => 'UserMulticompanyId', 'multiple' => true, 'options' => $multicompany)); ?>
        </div>
    <?php endif; ?>
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
        'last_name' => array(
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
        // ),
        'address' => array(
            'maxlength' => 150,
            'required' => 1
        ),
        'address2' => array(
            'maxlength' => 150,
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
        'user_type' => array(
            'required' => 1
        ),
        'photo' => array(
            'accept' => 'jpg|jpeg|png|bmp|gif'
        ),
        'subscription_id' => array(
            'required' => 1,
        )
    ),
    'Messages' => array(
        'first_name' => array(
            'minlength' => __('Please enter ' . strtolower($label_first) . ' with minimum 2 character.'),
            'maxlength' => __('Please enter ' . strtolower($label_first) . ' between 2 to 50 character.'),
            'required' => __('Please enter ' . strtolower($label_first)),
            'alphabates' => __('Please enter alphabates only')
        ),
        'last_name' => array(
            'minlength' => __('Please enter ' . strtolower($label_second) . ' with minimum 2 character.'),
            'maxlength' => __('Please enter ' . strtolower($label_second) . ' between 2 to 50 character.'),
            'required' => __('Please enter ' . strtolower($label_second)),
            'alphabates' => __('Please enter alphabates only')
        ),
        'email' => array(
            'required' => __('Please enter email address'),
            'email' => __('Please enter valid email address')
        ),
        // 'phone_no' => array(
        //     'required' => __('Please enter phone no'),
        //     'matchNumber' => __('Please enter valid phone no')
        // ),
        'address' => array(
            'maxlength' => __('Please enter address having maximum 150 characters.'),
            'required' => __('Please enter address')
        ),
        'address2' => array(
            'maxlength' => __('Please enter address having maximum 150 characters.'),
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
        'user_type' => array(
            'required' => __('Please select type')
        ),
        'photo' => array(
            'accept' => __('Please choose files having jpg, jpeg, png, bmp, gif extension.')
        ),
        'subscription_id' => array(
            'required' => __('Please select membership'),
        )
    )
);
if ($pageDetailWithRole['role'] == COMPANY && empty($parentId) && !isCompany()) :
    $arrValidation['Rules']['file_send_email'] = array(
        'required' => 1,
        'email' => 1,
    );
    $arrValidation['Messages']['file_send_email'] = array(
        'required' => __('Please enter file send mail'),
        'email' => __('Please enter valid file send mail'),
    );
endif;
if ($pageDetailWithRole['role'] != 'Company'):
    $arrValidation['Rules']['communication_type'] = array(
        'required' => 1
    );
    $arrValidation['Messages']['communication_type'] = array(
        'required' => __('Please select communication type')
    );
endif;
if ((isSuparAdmin() || isAdminAdmin()) && !empty($parentId)):
    $arrValidation['Rules']['parent_id'] = array(
        'required' => 1
    );
    $arrValidation['Messages']['parent_id'] = array(
        'required' => __('Please select %s.', $pageDetailWithRole['singularTitle'])
    );
endif;
echo $this->Form->setValidation($arrValidation);
echo $this->Form->end();

?>






                </div>
            </div>



        </div>

    </div>

<!--Rightbar Chat-->

<!--/Rightbar Chat-->

<script type="text/javascript">
    jQuery(document).ready(function () {
        //remove 'Email Address already Exist.' message
        jQuery('#UserEmail').focusout(function () {
            jQuery('.error-message').remove();
        });

        //set selected value of multiselection delaer
        var tmp = '<?php echo $multiSelectedDealerList; ?>';
        tmp = $.parseJSON(tmp);
        var tmparrayDealer = [];
        $.each(tmp, function (k, v) {
            tmparrayDealer.push(v);
        });


        var tmpComp = '<?php echo $multiSelectedCompanyList; ?>';
        tmpComp = $.parseJSON(tmpComp);
        var tmparrayCompany = [];
        $.each(tmpComp, function (k, v) {
            tmparrayCompany.push(v);
        });


        $('#UserMultidealerId').multiselect({
            maxHeight: 200,
            dropUp: true,
            onChange: function (element, checked) {
                var data = jQuery('#UserMultidealerId').val();
                loader('show');
                jQuery.ajax({
                    url: BaseUrl + "/users/getCompanies/multiselect/html",
                    type: 'post',
                    data: {data: jQuery('#UserMultidealerId').val()},
                    success: function (response) {
                        loader('hide');
                        jQuery('#UserMulticompanyId').multiselect('destroy');
                        jQuery('#UserMulticompanyId').html(response);
                        jQuery('#UserMulticompanyId').multiselect({maxHeight: 200, dropUp: true});
                    },
                    error: function () {
                        loader('hide');
                    }
                });
            }
        });
        jQuery('#UserMulticompanyId').multiselect();
        $('#UserMultidealerId').multiselect('select', tmparrayDealer);
        $('#UserMulticompanyId').multiselect('select', tmparrayCompany);

        jQuery('.chkbox:checkbox').on('change', function () {
            jQuery('.passwordDiv').slideToggle();
            jQuery('.branchSelectDiv .errorDV').remove();
        });
    });







    jQuery('#UserUserType').on('change', function () {
        if (jQuery("#UserUserType").val() == '' || jQuery("#UserUserType").val() == '<?php echo ADMIN ?>') {
            //        if((jQuery( "#UserUserType option:selected" ).text() == 'Company Admin') || (jQuery( "#UserUserType option:selected" ).text() == ' ')){
            jQuery('.branchSelectDiv').slideUp();
            $('.branchSelectDiv .chkbox').attr('checked', false);
            //            jQuery('.branchSelectDiv').slideUp();
        } else {
            jQuery('.branchSelectDiv').slideDown();
        }

        if (jQuery("#UserUserType").val() == '' || jQuery("#UserUserType").val() == '<?php echo SUPPORT ?>') {

        }

    }).trigger('change');




</script>
<?php
}

?>
<script type="text/javascript">
jQuery(document).ready(function () {

});
function getCompanyFromDealer(dealId, multi)
{
    if (typeof dealId != undefined) {
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/users/getCompanies/" + dealId + "/html",
            type: 'post',
            success: function (response) {
                loader('hide');
<?php if (isSuparAdmin() && $pageDetailWithRole['role'] == ADMIN): ?>
                    jQuery('#UserCompanyId').html(response);
<?php else: ?>
                    jQuery('#UserParentId').html(response);
<?php endif; ?>
            },
            error: function () {
                loader('hide');
            }
        });
    }
}
function setPageTitle(selVal)
{
    if (selVal !== undefined && selVal !== "") {
        jQuery('span.pageTitleSpan').html(jQuery('#UserParentId option[value=' + selVal + ']').html());
    }
}
</script>
