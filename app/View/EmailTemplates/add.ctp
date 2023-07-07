<?php
$type = empty($edit) ? 'Add' : 'Edit';
$this->assign('pagetitle', __('%s Email Templete', $type));
$this->Custom->addCrumb('Email Template', array('controller' => 'email_templates', 'action' => 'index'));
$this->Custom->addCrumb(__('%s Email Templete', $type));
$this->start('top_links');
echo $this->Html->link(__('Back'), array('controller' => 'email_templates', 'action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>



            <div class="row">

                <div class="col-md-8 col-sm-12">
                    <div class="panel panel-flat">

                        <div class="panel-body">

                             <?php echo $this->Form->create('EmailTemplate', array('class' => 'form-validate', 'enctype' => "multipart/form-data", 'id' => 'EmailTemplateAdminAddForm')); ?>
    <div class="box-body box-content">
        <?php

        echo $this->Form->input('name', array('label' => __('Template Name'), 'placeholder' => __('Template Name'), 'class' => 'form-control', 'div' => array('class' => 'form-group required')));
        echo $this->Form->input('subject', array('label' => __('Email Subject'), 'placeholder' => __('Email Subject'), 'class' => 'form-control', 'div' => array('class' => 'form-group required')));
        echo $this->Form->input('body', array('label' => __('Email Body'), 'placeholder' => 'Email Body', 'row' => 10, 'class' => 'editor form-control', 'div' => array('class' => 'form-group required')));




        ?>
        <div class="form-action">
            <?php if (!empty($drip_mail_id)) {
                echo $this->Form->submit(__('Next', true), array('class' => 'btn btn-primary', 'div' => false));
                echo '&nbsp;&nbsp;';
                echo $this->Form->button(__('Skip'), array('class' => 'btn btn-default', 'div' => false, 'onclick' => "javascript:addDripemails($drip_mail_id);"));
            } else {
                echo $this->Form->submit(__('Save'), array('class' => 'btn btn-primary', 'div' => false));
                echo '&nbsp;&nbsp;';
                echo $this->Html->link(__('Cancel'), array('controller' => $this->params['controller'], 'action' => 'index'), array('class' => 'btn btn-default'));
            }
 ?>
        </div>
    </div>
    <?php
    echo $this->Form->setValidation(array(
        'Rules' => array(
            'name' => array(
                'required' => 1
            ),
            'subject' => array(
                'required' => 1
            )
        ),
        'Messages' => array(
            'name' => array(
                'required' => __("Please enter a Template name.")
            ),
            'subject' => array(
                'required' => __("Please enter a Email Subject.")
            )
        )
    ));

    ?>
    <?php echo $this->Form->end(); ?>



                        </div>
                    </div>



                </div>


                <div class="col-md-4 col-sm-12">
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo __('Email Snippets'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            $arrReplacementVariable = array('FIRST_NAME', 'LAST_NAME', 'EMAIL', 'SITE_NAME', 'SITE_URL', 'SITE_SUPPORT_EMAIL','ACC_TYPE','USER_NAME','USER_PWD');
                            foreach ($arrReplacementVariable as $k => $variable) {
                                echo $this->Form->input('', array('onclick' => 'javascript:select();', 'value' => '{' . $variable . '}', 'readonly' => 'readonly', 'class' => 'form-control snippetvar'));
                            }

                            ?>
                        </div>
                    </div>
                </div>


            </div>

        <!--Rightbar Chat-->

        <!--/Rightbar Chat-->

  

 <script type="text/javascript">
    jQuery(document).ready(function () {
        CKEDITOR.replace('EmailTemplateBody');
    });
 </script>





    <?php echo $this->Form->end(); ?>
