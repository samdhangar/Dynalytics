<?php
$this->assign('pagetitle', __(' Help Page'));
$this->Custom->addCrumb(' Help Page');
$this->start('top_links');
echo $this->Html->link(__('Add Help Page'), array('controller' => $this->params['controller'], 'action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('Add New Faq'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();

//generate search panel
$searchPanelArray = array(
    'name' => 'Help',
    'options' => array(
        'id' => 'HelpSearchForm',
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
            'div' => false,
            'style' => 'margin-top:20px',
        ),
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the help pages'), 'class' => 'btn btn-default marginleft', 'style' => 'margin-top:20px')),
    'fields' => array(
        array(
            'name' => 'Title',
            'options' => array(
                'type' => 'text',
                'label' => __('Title'),
                'placeholder' => __('Enter title'),
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
                'empty' => __('Select status'),
                'options' => array('Active' => __('Active'), 'Inactive' => __('Inactive'))
            )
        )
    )
);


?>



<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">List of all <?php echo $this->fetch('pagetitle'); ?></h5>
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
                    <th width="18%"><?php echo $this->Paginator->sort('Title'); ?></th>
                    <th width="20%"><?php echo $this->Paginator->sort('Description'); ?></th>
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
                <?php if (empty($helps)) { ?>
                    <tr>
                        <td colspan="8">
                            <?php echo __("No Help Pages Found"); ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php
                    foreach ($helps as $key => $help) {

                    ?>
                        <tr>

                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <td>
                                <?php echo $this->Custom->cropDetail($help['Help']['title'], 30); ?>
                            </td>
                            <td>
                                <?php echo $this->Custom->cropDetail($help['Help']['description'], 50, array('html' => true)); ?>
                            </td>
                            <td title="<?php echo __('Status is %s', $help['Help']['status']) ?>">
                                <?php

                                echo $this->Custom->getToggleButton($help['Help']['status'], 'userStatusChange', array('data-uid' => $help['Help']['id'], 'data-id' => 'userStatus_' . $help['Help']['id']));

                                ?>
                            </td>
                            <td><?php echo showdatetime($help['Help']['created']); ?></td>
                            <td class="actions text-center">
                                    <input type="hidden" id="element_id" value= <?php echo $help['Help']['id']?>>
                                <?php
                                echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'view', encrypt($help['Help']['id'])), array('class' => 'viewFaq','icon' => 'icon-eye2 view', 'title' => 'Click here to view this Help-Page'));
                                echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'edit', encrypt($help['Help']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => 'Click here to edit this Faq'));

                                echo $this->Html->link('', array('controller' => $this->params['controller'], 'action' => 'delete', encrypt($help['Help']['id'])), array('icon' => 'icon-trash delete', 'title' => 'Click here to delete this Faq'), 'Do you want to really delete this Help page ?');

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
    jQuery(document).ready(function() {
        validateSearch("HelpSearchForm", ["HelpTitle", "HelpStatus"]);

        jQuery('.userStatusChange').on('click', function() {
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function(response) {
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
                    error: function(e) {
                        loader('hide');
                    }
                });
            }
        });
    });
</script>