<?php
$this->assign('pagetitle', __('Faqs'));
$this->Custom->addCrumb('Faqs');
$this->start('top_links');
echo $this->Html->link(__('Add Faq'), array('controller' => $this->params['controller'], 'action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('Add New Faq'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();

//generate search panel
$searchPanelArray = array(
    'name' => 'Faq',
    'options' => array(
        'id' => 'FaqSearchForm',
        'url' => $this->Html->url(array('action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-2'
            )
        )
    ),
    'searchDivClass' => 'col-md-5',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'StateSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the faqs'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'user_role',
            'options' => array(
                'type' => 'select',
                'label' => __('User Role'),
                'empty' => __('Select user role')
            )
        ),
        array(
            'name' => 'question',
            'options' => array(
                'type' => 'text',
                'label' => __('Question'),
                'placeholder' => __('Enter question'),
                'div' => array(
                    'class' => 'form-group col-md-3'
                )
            )
        ),
        array(
            'name' => 'status',
            'options' => array(
                'type' => 'select',
                'label' => __('Status'),
                'empty' => __('Select faq status'),
                'options' => array('Active' => __('Active'), 'Inactive' => __('Inactive'))
            )
        )
    )
);


?>



<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
    </div>

    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div class="dataTables_filter" style="width:100%;">
            <?php echo $this->CustomForm->setSearchPanel($searchPanelArray); ?>
        </div>
      </div>

    </div>

    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div style="float:none;" class="dataTables_filter">

    <?php echo $this->element('paginationtop'); ?>

  </div>
</div>

</div>

    <div class="table-responsive">

        <?php echo $this->Form->create('Country', array('class' => 'deleteAllForm', 'url' => array('controller' => $this->params['controller'], 'action' => 'delete'), 'id' => 'UserEditProfileForm', 'data-confirm' => __('Are you sure you want to delete selected Faq ?'))); ?>

        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover user-list" id="datatable">
          <thead>
              <tr>

                  <th width="5%">
<?php
echo __('#');

?>
                  </th>
                  <th width="15%"><?php echo $this->Paginator->sort('user_role'); ?></th>
                  <th width="18%"><?php echo $this->Paginator->sort('question'); ?></th>
                  <th width="20%"><?php echo $this->Paginator->sort('answer'); ?></th>
                  <th width="12%">
<?php echo $this->Paginator->sort('order_no', __('Display Order')); ?>
                  </th>
                  <th width="5%">
<?php echo $this->Paginator->sort('status'); ?>
                  </th>
                  <th width="15%">
<?php echo $this->Paginator->sort('created', __('Added On')); ?>
                  </th>
                  <th width="15%" class="actions text-center">
          <?php echo __('Actions'); ?>
                  </th>
              </tr>
          </thead>
            <tbody>
              <?php if (empty($faqs)) { ?>
                                      <tr>
                                          <td colspan="8">
                                      <?php echo __("No Faq Found"); ?>
                                          </td>
                                      </tr>
              <?php } else { ?>
                                              <?php
                                              foreach ($faqs as $key => $faq) {

                                                  ?>
                                          <tr>

                                              <td>
                                                  <?php echo $startNo++; ?>
                                              </td>
                                              <td align="center">
                                                  <?php echo getAllUserRoleTypes($faq['Faq']['user_role']); ?>
                                              </td>
                                              <td align="center">
                                                  <?php echo $this->Custom->cropDetail($faq['Faq']['question'], 30); ?>
                                              </td>
                                              <td align="center">
                                                  <?php echo $this->Custom->cropDetail($faq['Faq']['answer'], 50, array('html' => true)); ?>
                                              </td>
                                              <td>
                      <?php echo $faq['Faq']['order_no']; ?>
                                              </td>
                                              <td title="<?php echo __('Status is %s', $faq['Faq']['status']) ?>">




<?php

          echo $this->Custom->getToggleButton($faq['Faq']['status'], 'userStatusChange', array('data-uid' => $faq['Faq']['id'], 'data-id' => 'userStatus_' . $faq['Faq']['id']));

          ?>






                                              </td>
                                              <td><?php echo showdatetime($faq['Faq']['created']); ?></td>
                                              <td class="actions text-center">
                                          <?php
                                          echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'view', encrypt($faq['Faq']['id'])), array('class' => 'viewFaq', 'data-target' => "#smsModel", 'data-toggle' => 'modal', 'icon' => 'icon-eye2 view', 'title' => 'Click here to view this Faq'));
                                          echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'edit', encrypt($faq['Faq']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => 'Click here to edit this Faq'));

                                          echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'delete', encrypt($faq['Faq']['id'])), array('icon' => 'icon-trash delete', 'title' => 'Click here to delete this Faq'), 'Do you want to really delete this Faq ?');

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
<?php echo $this->Form->end(); ?>
</div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("FaqSearchForm", ["FaqUserRole", "FaqQuestion", "FaqStatus"]);

        jQuery('.userStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'active' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        alert(response.message);
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });
    });
</script>
