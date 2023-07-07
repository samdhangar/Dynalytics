<?php
$this->assign('pagetitle', __('Email Template'));
$this->Custom->addCrumb(__('Email Template'));
$startNo = (int) $this->Paginator->counter('{:start}');
?>

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
                </div>



                <div class="dataTables_wrapper no-footer">
                  <div class="datatable-header">
                    <div style="float:none;" class="dataTables_filter">

                <?php echo $this->element('paginationtop'); ?>

              </div>
            </div>

          </div>

                <div class="table-responsive">
                    <table class="table table-hover user-list" id="datatable">
                      <thead>
                          <tr>
              <th>
                                  <?php
                                  echo __('Sr. No.');

                                  ?>
                              </th>
                              <th><?php echo $this->Paginator->sort('name'); ?></th>
                              <th><?php echo $this->Paginator->sort('subject'); ?></th>
                              <th><?php echo $this->Paginator->sort('updated', __('Last Updated')); ?></th>
                              <th class="actions text-center"><?php echo 'Actions'; ?></th>
                          </tr>
                      </thead>
                        <tbody>
                          <?php if (empty($EmailTemplates)) { ?>
                              <tr>
                                  <td colspan="6">
                                      <?php echo __("No Email Template added yet!"); ?>
                                  </td>
                              </tr>
                          <?php } else { ?>
                              <?php foreach ($EmailTemplates as $EmailTemplate) { ?>
                                  <tr>
                    <td>
                                              <?php echo $startNo++; ?>
                                          </td>
                                      <td><?php echo $EmailTemplate['EmailTemplate']['name']; ?></td>
                                      <td><?php echo $EmailTemplate['EmailTemplate']['subject']; ?></td>
                                      <td><?php echo $EmailTemplate['EmailTemplate']['updated']; ?></td>
                                      <td class="actions text-center">
                                          <?php
                                          echo $this->Html->link('', array('controller' => 'email_templates', 'action' => 'view', encrypt($EmailTemplate['EmailTemplate']['id'])), array('icon' => 'icon-eye2 view', 'title' => 'Click here to view this email template', 'class' => 'action'));
                                          echo $this->Html->link('', array('controller' => 'email_templates', 'action' => 'edit', encrypt($EmailTemplate['EmailTemplate']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => 'Click here to edit this email_template', 'class' => 'action'));

                                          ?>
                                      </td>
                                  </tr>
                                  <?php
                              }
                          }

                          ?>
        </tbody>
                    </table>
                </div>
                  <div class="box-footer clearfix">



    <?php echo $this->element('pagination'); ?>
</div>

</div>

            </div>
