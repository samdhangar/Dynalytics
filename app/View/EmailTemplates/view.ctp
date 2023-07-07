<?php
$this->assign('pagetitle', __('Email Detail'));
$this->Custom->addCrumb('Email Template', array('controller' => 'email_templates', 'action' => 'index'));
$this->Custom->addCrumb('Email Detail');
$this->start('top_links');
echo $this->Html->link(__('Edit'), array('controller' => 'email_templates', 'action' => 'edit', encrypt($emailTemplate['EmailTemplate']['id'])), array('icon' => 'fa-edit', 'title' => 'Click here to edit this email template', 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->link(__('Back'), array('controller' => 'email_templates', 'action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default marginleft', 'escape' => false));
$this->end();

?>



<!--Page Container-->


            <div class="row">

                <div class="col-md-12 col-sm-12">
                    <div class="panel panel-flat">

                        <div class="panel-body">

                          <div class="col-md-12">
                              <div class="col-md-6 col-sm-6 center-block" style="float: none;overflow: hidden;padding-top: 15px">
                                  <div class="col-md-2">
                                      From
                                  </div>
                                  <div class="col-md-10">
                                      <?php echo Configure::read('Site.FromName') . ' < ' . Configure::read('Site.FromEmail') . ' > ' ?>
                                  </div>
                                  <div class="col-md-2">
                                      Subject
                                  </div>
                                  <div class="col-md-10">
                                      <?php echo $emailTemplate['EmailTemplate']['subject'] ?>
                                  </div>
                                  <div class="col-md-2">
                                      To
                                  </div>
                                  <div class="col-md-10">
                                      <?php echo __('{FIRST_NAME} {LAST_NAME}') ?>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <?php echo $body ?>
                              </div>


    </div>




                        </div>
                    </div>



                </div>

            </div>

        <!--Rightbar Chat-->

        <!--/Rightbar Chat-->
