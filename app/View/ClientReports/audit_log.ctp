<?php
$this->assign('pagetitle', __('Audit Log'));
$this->Custom->addCrumb(__('Audit Log'));
$this->start('top_links');
//echo $this->Html->link(__('Add Subscription'), array('action' => 'add'), array('icon' => 'cc icon-plus3 fa-plus', 'title' => __('Add Subscription'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();
//generate search panel
$searchPanelArray = array(
    'name' => 'AuditLog',
    'options' => array(
        'id' => 'AuditLogSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'audit_log'), true),
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
    'searchDivClass' => 'col-md-9',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'UserSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'title' => __('Search Audit Log'),
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset'), array('controller' => $this->params['controller'], 'action' => 'audit_log', 'all', '', ''), array('escape' => false, 'title' => __('Display the all Audit Log'), 'class' => 'btn btn-default')),
    'fields' => array(
        array(
            'name' => 'type',
            'options' => array(
                'label' => __('Type'),
                'type' => 'select',
                'empty' => __('Select Type'),
                'div' => array(
                    'class' => 'form-group col-md-3'
                )
            )
        ),
        // array(
        //     'name' => 'created',
        //     'options' => array(
        //         'label' => __('Log Date'),
        //         'type' => 'text',
        //         'placeholder' => __('Select log date'),
        //         'class' => 'form-control startDate'
        //     )
        // )
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
                <th width="20%"><?php echo $this->Paginator->sort('User Name'); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('type'); ?></th>
                <th width="10%"><?php echo $this->Paginator->sort('created', __('Log Date')); ?></th>
            </tr>
          </thead>
            <tbody>
                <?php if (empty($auditlogs)) { ?>
                    <tr>
                        <td colspan='10' class='text-warning'><?php echo __('No Audit Log found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($auditlogs as $auditlog): ?>
                        <tr>
                            <td> <?php echo $startNo++; ?> </td>
                            <td><?php echo !empty($auditlog['User']['name']) ? h($auditlog['User']['name']) : ''; ?>
                                &nbsp;</td>

                            <td><?php echo !empty($auditlog['AuditLog']['type']) ? $auditlog['AuditLog']['type'] : ''; ?>
                                &nbsp;</td>

                            <td><?php echo !empty($auditlog['AuditLog']['created']) ? showdatetime($auditlog['AuditLog']['created']) : ''; ?>
                                &nbsp;</td>
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
      <div class="box-footer clearfix">



<?php echo $this->element('pagination'); ?>
</div>
</div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.startDate').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });

        jQuery('.viewAuditLog').on('click', function (e) {
            e.preventDefault();
            var title = $(this).data('reqtitle');
            var Url = $(this).attr("href");
            jQuery.ajax({
                url: Url,
                type: 'post',
                success: function (response) {
                    jQuery('#appendModelContent').html('');
                    jQuery('#appendModelContent').append(response);
                    $('#commonModel').modal('show');
                },
                error: function (e) {

                }
            });
        });
    });
</script>
