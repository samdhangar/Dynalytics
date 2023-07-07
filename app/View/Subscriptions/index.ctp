<?php
$this->assign('pagetitle', __('Subscriptions'));
$this->Custom->addCrumb(__('Subscriptions'));
$this->start('top_links');
echo $this->Html->link(__('Add Subscription'), array('action' => 'add'), array('icon' => 'cc icon-plus3 fa-plus', 'title' => __('Add Subscription'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();
//generate search panel
$searchPanelArray = array(
    'name' => 'Subscription',
    'options' => array(
        'id' => 'SubscriptionSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-3'
            )
        )
    ),
    'searchDivClass' => 'col-md-6',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'SubscriptionSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the Subscriptions'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'name',
            'options' => array(
                'type' => 'text',
                'label' => __('Name'),
                'placeholder' => __('Enter name')
            )
        ),
        array(
            'name' => 'charge',
            'options' => array(
                'type' => 'text',
                'label' => __('Charge'),
                'placeholder' => __('Charge')
            )
        ),
        array(
            'name' => 'type',
            'options' => array(
                'type' => 'select',
                'label' => __('Subscription Type'),
                'empty' => __('Select Subscription type'),
                'options' => array(
                    COMPANY => 'Financial Institution',
                    DEALER => DEALER
                )
            )
        ),
        array(
            'name' => 'status',
            'options' => array(
                'type' => 'select',
                'label' => __('Status'),
                'empty' => __('Select Status'),
                'options' => array(
                    'active' => __('Active'),
                    'inactive' => __('InActive')
                )
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

        <?php echo $this->Form->create('Subscription', array('class' => 'deleteAllForm', 'url' => array('controller' => $this->params['controller'], 'action' => 'delete'), 'id' => 'UserEditProfileForm', 'data-confirm' => __('Are you sure you want to delete selected Subscription ?'))); ?>

        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover user-list" id="datatable">
          <thead>
            <tr>

                <?php $columns = 10; ?>
                <th width="5%">
                    <?php
                    echo __('#');

                    ?>
                </th>
                <th width="20%"><?php echo $this->Paginator->sort('name'); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('setup_cost',__('Total Setup Cost')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('branch_cost',__('Branch Cost')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('charge',__('Charge')); ?></th>
                <th width="12%"><?php echo $this->Paginator->sort('type',__('Type')); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('status', __('Status')); ?></th>
                <th width="12%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                <th width="12%"><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>
                <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
            </tr>
          </thead>
            <tbody>
              <?php if (empty($subscriptions)) { ?>
                  <tr>
                      <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No Subscription found.') ?></td>
                  </tr>
              <?php } else { ?>
                  <?php foreach ($subscriptions as $subscription): ?>
                      <tr>

                          <td>
                              <?php echo $startNo++; ?>
                          </td>
                          <td class="table-text"><?php echo ($subscription['Subscription']['name']); ?></td>
                          <td><?php echo $this->Custom->showAmount($subscription['Subscription']['setup_cost']); ?></td>
                          <td><?php echo $this->Custom->showAmount($subscription['Subscription']['branch_cost']); ?></td>
                          <td><?php echo $this->Custom->showAmount($subscription['Subscription']['charge']); ?></td>
                          <td class="table-text"><?php 
                          if($subscription['Subscription']['type'] == 'Company'){
                            echo "Financial Institution";
                          }else{
                            echo ($subscription['Subscription']['type']); 
                          }
                          ?></td>
                          <td>
                              <?php echo $this->Custom->getToggleButton($subscription['Subscription']['status'], 'subStatusChange', array('data-uid' => encrypt($subscription['Subscription']['id']), 'data-id' => 'userStatus_' . $subscription['Subscription']['id'])); ?>
                          </td>
                          <td><?php echo showdatetime($subscription['Subscription']['created']); ?></td>
                          <td><?php echo showdatetime($subscription['Subscription']['updated']); ?></td>
                          <td class="actions text-center">
                              <?php
                              $sessionData = getMySessionData();
                              echo $this->Html->link('', array('action' => 'edit', encrypt($subscription['Subscription']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Subscription')));
                              echo $this->Html->link('', array('action' => 'delete', encrypt($subscription['Subscription']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Subscription')), __('Are you sure you want to delete Subscription?'));

                              ?>

                          </td>
                      </tr>
                  <?php endforeach; ?>
              <?php } ?>
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
        validateSearch("SubscriptionSearchForm", ["SubscriptionStatus", "SubscriptionName",'SubscriptionCharge','SubscriptionType']);

        jQuery('.subStatusChange').on('click', function () {
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
