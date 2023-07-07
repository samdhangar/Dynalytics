<?php
$this->assign('pagetitle', __('General Setting'));
$this->Custom->addCrumb(__('General Setting'));

?>


            <div class="row">

                <div class="col-md-12 col-sm-12">
                    <div class="panel panel-flat">
                      <?php echo $this->Form->create('SiteConfig', array('id' => 'GeneralSettingForm', 'class' => 'form-horizontal form-with-action', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'col-md-12 form-group')))); ?>

                        <div class="panel-body">


                          <div class="row">
                              <div class="col-md-6">
                                  <?php
                                  echo $this->Form->input('Site.Name', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.Name]', 'label' => __('Site Name'), 'value' => $arrConfigs['Site.Name']['value']));

                                  echo $this->Form->input('Site.Url', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.Url]', 'label' => __('Site Url'), 'value' => $arrConfigs['Site.Url']['value']));
                                  echo $this->Form->input('Site.FromName', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.FromName]', 'label' => __('Site Email Name'), 'value' => $arrConfigs['Site.FromName']['value']));
                                  echo $this->Form->input('Site.FromEmail', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.FromEmail]', 'label' => __('Site Email Address'), 'value' => $arrConfigs['Site.FromEmail']['value']));
                                  echo $this->Form->input('Site.filterOption', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.filterOption]','options'=>getReportFilter(), 'label' => __('Site Default Filter'), 'value' => $arrConfigs['Site.filterOption']['value']));
                                  echo $this->Form->input('Site.TextparsingUrl', array('class' => 'form-control', 'div' => array('class' => 'form-group required'),'name' => 'data[Site.TextparsingUrl]', 'label' => __('Site TextParsing Script Url'), 'value' => $arrConfigs['Site.TextparsingUrl']['value']));
                                  ?>
                              </div>
                              <div class="col-md-6">
                                  <?php
                                  echo $this->Form->input('Site.Address1', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.Address1]', 'label' => __('Site Address 1'), 'value' => $arrConfigs['Site.Address1']['value']));
                                  echo $this->Form->input('Site.Address2', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.Address2]', 'label' => __('Site Address 2'), 'value' => $arrConfigs['Site.Address2']['value']));
                                  echo $this->Form->input('Site.State', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.State]', 'label' => __('Site State'), 'value' => $arrConfigs['Site.State']['value']));
                                  echo $this->Form->input('Site.Country', array('class' => 'form-control','div' => array('class' => 'form-group required'),'name' => 'data[Site.Country]', 'label' => __('Site Country'), 'value' => $arrConfigs['Site.Country']['value']));
                                  echo $this->Form->input('Site.SupportPhone', array('class' => 'form-control', 'div' => array('class' => 'form-group required'),'name' => 'data[Site.SupportPhone]', 'label' => __('Site Support Phone'), 'value' => $arrConfigs['Site.SupportPhone']['value']));
                                  

                                  ?>
                              </div>





                          </div>








        <div class="form-action">
            <?php echo $this->Form->submit(__('Save'), array('div' => false, 'class' => 'btn btn-primary')); ?>
            &nbsp;&nbsp;
            <?php echo $this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-default')); ?>
        </div>
    </div>

    <?php echo $this->Form->end(); ?>



                        </div>
                    </div>



                </div>

            </div>

        <!--Rightbar Chat-->

        <!--/Rightbar Chat-->



 <script type="text/javascript">
    jQuery(document).ready(function () {
        //jQuery('.tokeninput').tokenfield();
        jQuery("#GeneralSettingForm").validate({
            rules: {
                "data[Site.Name]": {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                "data[Site.Url]": {
                    required: true,
                    url: true
                },
                "data[Site.AdminEmail]": {
                    required: true,
                    email: true
                },
                "data[Site.Apikey]": {
                    required: true
                },
                "data[Site.Timeout]": {
                    required: true,
                    number: true,
                    positiveNumber: true
                },
                "data[Site.TrailDay]": {
                    required: true,
                    number: true,
                    positiveNumber: true
                },
                "data[Site.TrailSpace]": {
                    required: true,
                    number: true,
                    positiveNumber: true
                },
                "data[Site.FromEmail]": {
                    required: true,
                    email: true
                },
                "data[Site.FromName]": {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                "data[Site.ContactEmail]": {
                    required: true,
                    email: true
                },
                "data[Site.SupportEmail]": {
                    required: true,
                    email: true
                },
                "data[Site.SupportPhone]": {
                    required: true,
                },
                "data[Site.TextparsingUrl]": {
                    required: true,
                    url: true
                }
            },
            messages: {
                "data[Site.Name]": {
                    required: "Please enter site name.",
                    minlength: "Please enter site name between 2 to 100 characters.",
                    maxlength: "Please enter site name between 2 to 100 characters."
                },
                "data[Site.Url]": {
                    required: "Please enter site url.",
                    url: "Please enter valid site url."
                },
                "data[Site.AdminEmail]": {
                    required: "Please enter administrator email address.",
                    email: "Please enter valid email address."
                },
                "data[Site.Apikey]": {
                    required: "Please enter site api key.",
                },
                "data[Site.Timeout]": {
                    required: "Please enter session time out of admin/user.",
                    number: "Please enter numberic time.",
                    positiveNumber: "Time can not be in negative."
                },
                "data[Site.TrailDay]": {
                    required: "Please enter day for trail plan.",
                    number: "Please enter numeric value.",
                    positiveNumber: "Please enter postive number."
                },
                "data[Site.TrailSpace]": {
                    required: "Please enter space for trail plan",
                    number: "Please enter numeric value.",
                    positiveNumber: "Please enter postive number."
                },
                "data[Site.FromEmail]": {
                    required: "Please enter email address for site generated mail.",
                    email: "Please enter valid email address."
                },
                "data[Site.FromName]": {
                    required: "Please enter name for the sent mail name.",
                    minlength: "Please enter name between 2 to 100 characters.",
                    maxlength: "Please enter name between 2 to 100 characters."
                },
                "data[Site.ContactEmail]": {
                    required: "Please enter contact email address of site.",
                    email: "Please enter valid email address."
                },
                "data[Site.SupportEmail]": {
                    required: "Please enter support email address of site.",
                    email: "Please enter valid email address."
                },
                "data[Site.SupportPhone]": {
                    required: "Please enter support phone number.",
                },
                "data[Site.TextparsingUrl]": {
                    required: "Please enter Text Parsing Script URL.",
                    url: "Please enter valid Text Parsing url."
                }
            }
        });
    });
 </script>
