<?php $this->assign('pagetitle', __('States'));
$this->Custom->addCrumb(__('States'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Add State'), array('action' => 'add'), array('icon' => 'cc icon-plus3', 'title' => __('Add State'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();
$searchPanelArray = array(
    'name' => 'State',
    'options' => array(
        'id' => 'StateSearchForm',
        'url' => $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'col-md-3 col-sm-3 col-xs-12'
            )
        )
    ),
    'searchDivClass' => 'col-md-6 col-sm-6 col-xs-12',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'StateSearchBtn',
            'class' => 'btn btn-success',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the Stations'), 'class' => 'btn btn-default marginleft')),


    'fields' => array(
        array(
            'name' => 'country_id',
            'options' => array(
                'type' => 'select',
                'label' => 'Country',
                'empty' => __('Select Country')
            )
        ),
        array(
            'name' => 'name',
            'options' => array(
                'type' => 'text',
                'label' => 'States',
                'placeholder' => __('Type to filter...')
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
                        <table class="table table-hover user-list" id="datatable">
                            <thead>

                                 <tr>

                    <?php $columns = 7; ?>
                    <th width="5%">
                                <?php
                                echo __('#');

                                ?>
                            </th>
                    <th width="20%"><?php echo $this->Paginator->sort('country_id'); ?></th>
                    <th width="<?php echo ((isAdmin())) ? '20%' : '35%' ?>"><?php echo $this->Paginator->sort('name', __('State')); ?></th>
                    <?php if (isAdmin()): ?>
                        <?php $columns++; ?>
                        <th width="20%"><?php echo $this->Paginator->sort('user_id', __('Added By')); ?></th>
                    <?php endif; ?>
                    <th width="12%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                    <th width="12%"><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>
                    <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                            </thead>
                            <tbody>
                <?php if (empty($states)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($states as $state): ?>
                        <tr>

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                            <td class="table-text">
                                <?php
                                //echo $this->Html->link($state['Country']['name'], array('controller' => 'countries', 'action' => 'view', $state['Country']['id']));
                                echo $state['Country']['name'];

                                ?>
                            </td>
                            <td class="table-text"><?php echo h($state['State']['name']); ?>&nbsp;</td>
                            <?php if (isAdmin()): ?>
                                <td class="table-text">
                                    <?php
                                    $model = 'state';
                                    if (empty(${$model}[ucfirst($model)]['user_id'])):
                                        echo ' - ';
                                    else:
                                        echo ${$model}['AddedBy']['first_name'] . ' ' . ${$model}['AddedBy']['last_name'] . ' (' . ${$model}['AddedBy']['role'] . ' ' . ${$model}['AddedBy']['user_type'] . ')';

                                    endif;

                                    ?>
                                </td>
                            <?php endif; ?>
                            <td><?php echo showdatetime($state['State']['created']); ?></td>
                            <td><?php echo showdatetime($state['State']['updated']); ?></td>
                            <td class="actions text-center">
                                <?php
                                $sessionData = getMySessionData();
//                                if ((isAdmin()) || $this->Session->read('Auth.User.id') == $state['State']['user_id']) {
                                if ((isAdmin()) || $sessionData['id'] == $state['State']['user_id']) {
                                    echo $this->Html->link('', array('action' => 'edit', encrypt($state['State']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this State')));
                                    if (isAdmin()) {
                                        echo $this->Html->link('', array('action' => 'delete', encrypt($state['State']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this State')), __('Are you sure you want to delete State?'));
                                    }
                                } else {
                                    echo ' - ';
                                }

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
        validateSearch("StateSearchForm", ["StateCountryId", "StateName"]);
    });
</script>
