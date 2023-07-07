<?php
$type = empty($edit) ? 'Add' : 'Edit';
if (empty($parentId) || isCompany()) {
    $this->assign('pagetitle', __('%s Branch', $type));
} else {
    if (!empty($this->request->data['CompanyBranch']['admin_id']) && !empty($admins[$this->request->data['CompanyBranch']['admin_id']])) {
        $titlePage = $admins[$this->request->data['CompanyBranch']['admin_id']];
    } else {
        $titlePage = $parentDetail['first_name'];
    }
    $this->assign('pagetitle', '<span class="pageTitleSpan">' . __('%s', $titlePage) . '</span>' . __(' - %s Branch', $type));
}
$this->Custom->addCrumb(__('Branches'), array('action' => 'index'));
$this->Custom->addCrumb(__('%s Branch', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <?php
        echo $this->Form->create('CompanyBranch', array('id' => 'CompanyBranchForm', 'type' => 'file', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group'))));
        echo $this->Form->input('id', array('type' => 'hidden'));

        ?>
        <div class="box-body box-content">
            <div class="row no-margin">
                <div class="col-md-6">
                    <?php
                    $label_name = __('Financial Institution');
                    if (isSuparCompany() || isCompanyAdmin() || isSuparAdmin()):
                        $label_name = __('Branch Admin');
                    endif;
                    if (isSuparAdmin()) {
                        echo $this->Form->input('company_id', array('onchange' => 'getBranchAdmins(this.value)', 'id' => 'companyId', 'type' => 'select', 'label' => __('Company'), 'empty' => __('Select Company'), 'div' => array('class' => 'form-group')));
                    }
                    echo $this->Form->input('admin_id', array('id' => 'CompBranchAdmId', 'type' => 'select', 'label' => $label_name, 'empty' => __('Select %s', $label_name), 'div' => array('class' => 'form-group')));
                    echo $this->Form->input('dealer_id', array('id' => 'CompBranchDealId', 'type' => 'select', 'label' => __('Branch Dealer'), 'empty' => __('Select Branch Dealer'), 'div' => array('class' => 'form-group')));
                    echo $this->Form->input('regions', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-12 no-leftpadding'), 'label' => __('Select Region'), 'empty' => __('Select Region'), 'div' => array('class' => 'form-group')));
                    echo $this->Form->input('name', array('label' => __('Branch Name'), 'placeholder' => 'Branch Name'));
                    echo $this->Form->input('contact_name', array('placeholder' => 'Contact Name'));

                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    // echo $this->Form->input('email', array('placeholder' => 'Email'));
                    echo $this->Form->input('phone', array('label' => 'Phone', 'placeholder' => 'Phone No.',  'div' => array('class' => 'form-group phoneNumber')));

                    // echo $this->Form->input('phone', array('placeholder' => 'Phone No.', 'class' => 'form-control phoneNumber'));
                    echo $this->Form->input('address', array('label' => 'Address 1', 'placeholder' => 'Address 1', 'type' => 'text'));
                    echo $this->Form->input('address2', array('label' => 'Address 2', 'placeholder' => 'Address 2', 'type' => 'text', 'div' => array('class' => 'form-group')));
                   
                    echo $this->Form->input('country', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-leftpadding'), 'onchange' => 'getStates(this.value,"UserStateId")', 'empty' => __('Select country')));
        
                    echo $this->Form->input('state', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-leftpadding'), 'onchange' => 'getCities(this.value,"UserCityId")', 'id' => 'UserStateId', 'empty' => __('Select state')));

                    echo $this->Form->input('city', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-padding'), 'id' => 'UserCityId', 'empty' => __('Select city')));
                    echo $this->Form->input('zipcode', array('label' => 'Zip Code', 'div' => array('class' => 'form-group required col-md-3 no-rightpadding'), 'placeholder' => 'Zip Code'));

                    if($type=='Add'){
                        // echo $this->Form->input('ftpuser', array('readonly' => true, 'value' => isset($ftp_username) ? $ftp_username : $this->request->data['CompanyBranch']['ftpuser'], 'placeholder' => 'FTP Username', 'label' => __('Ftp Username'), 'div' => array('class' => 'required form-group clearBoth col-md-12 no-padding')));
                        //  echo $this->Form->input('ftp_pass', array('readonly' => true, 'value' => isset($ftp_password) ? $ftp_password : $this->request->data['CompanyBranch']['ftp_pass'], 'placeholder' => 'FTP Password', 'label' => __('Ftp Password'), 'div' => array('class' => 'required form-group clearBoth col-md-12 no-padding')));
                    }
                    ?>
                </div>
            </div>
            <div class="form-action">
                <?php echo $this->Form->submit(__('Save'), array('div' => false, 'class' => 'btn btn-primary')); ?>
                &nbsp;&nbsp;
                <?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>

        <?php
        $arrValidation = array(
            'Rules' => array(
                'name' => array(
                    'required' => 1,
                    'minlength' => 2,
                    'maxlength' => 50,
                ),
                'address' => array(
                    'required' => 1,
                    'maxlength' => 150,
                ),
                'address2' => array(
                    'maxlength' => 150,
                ),
                'city' => array(
                    'required' => 1
                ),
                'state' => array(
                    'required' => 1
                ),
                'country' => array(
                    'required' => 1
                ),
                'zipcode' => array(
                    'maxlength' => 5,
                    'required' => 1,
                    'matchNumber' => 5
                ),
                'contact_name' => array(
                    'required' => 1,
                    'minlength' => 2,
                    'maxlength' => 50,
                    'alphabates' => 1
                ),
                // 'email' => array(
                //     'required' => 1,
                //     'email' => 1
                // ),
                // 'phone' => array(
                //     'required' => 1,
                //     'matchNumber' => 10
                // ),
                // 'ftpuser' => array(
                //     'required' => 1
                // ),
                // 'ftp_pass' => array(
                //     'required' => 1
                // )
            ),
            'Messages' => array(
                'name' => array(
                    'minlength' => __('Please enter branch name with minimum 2 character.'),
                    'maxlength' => __('Please enter branch name between 2 to 50 character.'),
                    'required' => __('Please enter branch name'),
                ),
                'address' => array(
                    'required' => __('Please enter address '),
                    'maxlength' => __('Pl'),
                ),
                'address2' => array(
                    'maxlength' => __('Pl'),
                ),
                'country' => array(
                    'required' => __('Please select country')
                ),
                'state' => array(
                    'required' => __('Please select state')
                ),
                'city' => array(
                    'required' => __('Please select city')
                ),
                'zipcode' => array(
                    'maxlength' => __('Please enter valid zip code'),
                    'required' => __('Please enter zip code'),
                    'matchNumber' => __('Please enter valid zip code')
                ),
                'contact_name' => array(
                    'minlength' => __('Please enter contact name with minimum 2 character.'),
                    'maxlength' => __('Please enter contact name between 2 to 50 character.'),
                    'required' => __('Please enter contact name'),
                    'alphabates' => __('Please enter alphabates only')
                ),
                // 'email' => array(
                //     'required' => __('Please enter email address'),
                //     'email' => __('Please enter valid email address')
                // ),
                // 'phone' => array(
                //     'required' => __('Please enter phone no'),
                //     'matchNumber' => __('Please enter valid phone no')
                // ),
                // 'ftpuser' => array(
                //     'required' => __('Please enter ftp username')
                // ),
                // 'ftp_pass' => array(
                //     'required' => __('Please enter ftp password')
                // )
        ));
        echo $this->Form->setValidation($arrValidation);
        echo $this->Form->end();

        ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        /*if ($('#CompBranchAdmId').val() != '') {
         jQuery("#CompanyBranchContactName").val(jQuery(this).find('option[value=' + jQuery(this).val() + ']').html());
         } else {
         jQuery("#CompanyBranchContactName").val('');
         }*/

        jQuery('#CompBranchAdmId').on('change', function () {
<?php
if ($this->params['controller'] == 'company_branches'):

    ?>
                if (jQuery(this).val() != '') {
                    jQuery("#CompanyBranchContactName").val(jQuery(this).find('option[value=' + jQuery(this).val() + ']').html());
                } else {
                    jQuery("#CompanyBranchContactName").val('');
                }
    <?php
endif;

?>
            jQuery('span.pageTitleSpan').html(jQuery(this).find('option[value=' + jQuery(this).val() + ']').html());

        });
    });

    function getBranchAdmins(companyId)
    {
        loader('show');
        jQuery.ajax({
            url: BaseUrl + "/users/getCompanyBranchAdmins/" + companyId + "/html",
            type: 'post',
            data: {data: jQuery('#companyId').val()},
            success: function (response) {
                loader('hide');
                jQuery('#CompBranchAdmId').html(response);
            },
            error: function (e) {
                loader('hide');
            }
        });
    }
</script>