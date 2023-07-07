<?php
$this->assign('pagetitle', __('Cities'));
$this->Custom->addCrumb(__('Cities'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Add city'), array('action' => 'add'), array('icon' => 'cc icon-plus3', 'title' => __('Add city'), 'class' => 'btn btn-sm btn-success btn-labeled', 'escape' => false));
$this->end();
//generate search panel
$searchPanelArray = array(
    'name' => 'City',
    'options' => array(
        'id' => 'CitySearchForm',
        'url' => $this->Html->url(array('action' => 'index'), true),
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
    'searchDivClass' => 'col-md-3 col-sm-3 col-xs-12',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'StateSearchBtn',
            'class' => 'btn btn-success',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the cities'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'country_id',
            'options' => array(
                'type' => 'select',
                'label' => __('Country'),
                'empty' => __('Select Country'),
                'onchange' => 'getStates(this.value,"CityStateId")'
            )
        ),
        array(
            'name' => 'state_id',
            'options' => array(
                'type' => 'select',
                'id' => 'CityStateId',
                'label' => __('State'),
                'empty' => __('Select State')
            )
        ),
        array(
            'name' => 'name',
            'options' => array(
                'type' => 'text',
                'label' => __('City'),
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
                              <?php $columns = 8; ?>

                              <th width="5%">
                                  <?php
                                  echo __('#');
                                  ?>
                              </th>
                              <th width="12%"><?php echo $this->Paginator->sort('country_id'); ?></th>
                              <th width="12%"><?php echo $this->Paginator->sort('state_id'); ?></th>
                              <th width="<?php echo (isAdmin()) ? '15%' : '31%' ?>25%"><?php echo $this->Paginator->sort('name', __('City')); ?></th>
                              <?php
                              if (isAdmin()):
                                  $columns++;
                                  ?>
                                  <th width="16%"><?php echo $this->Paginator->sort('user_id', __('Added By')); ?></th>
                              <?php endif; ?>
                              <th width="15%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                              <th width="15%"><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>
                              <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php if (empty($cities)) { ?>
                              <tr>
                                  <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No City found.') ?></td>
                              </tr>
                          <?php } else { ?>
                              <?php foreach ($cities as $city): ?>
                                  <tr>

                                      <td>
                                          <?php echo $startNo++; ?>
                                      </td>
                                      <td>
                                          <?php
          //                                echo $this->Html->link($city['Country']['name'], array('controller' => 'countries', 'action' => 'view', $city['Country']['id']));
                                          echo $city['Country']['name'];
                                          ?>
                                      </td>
                                      <td>
                                          <?php
          //                                echo $this->Html->link($city['State']['name'], array('controller' => 'states', 'action' => 'view', $city['State']['id']));
                                          echo $city['State']['name'];
                                          ?>
                                      </td>
                                      <td><?php echo h($city['City']['name']); ?>&nbsp;</td>
                                      <?php if (isAdmin()): ?>
                                          <td>
                                              <?php
                                              $model = 'city';
                                              if (empty(${$model}[ucfirst($model)]['user_id'])):
                                                  echo ' - ';
                                              else:
                                                  echo ${$model}['AddedBy']['first_name'] . ' ' . ${$model}['AddedBy']['last_name'] . ' (' . $this->Custom->showStatus(${$model}['AddedBy']['role']) . ')';

                                              endif;
                                              ?>
                                          </td>
                                      <?php endif; ?>
                                      <td><?php echo showdatetime($city['City']['created']); ?>&nbsp;</td>
                                      <td><?php echo showdatetime($city['City']['updated']); ?>&nbsp;</td>
                                      <td class="actions text-center">
                                          <?php
                                          $sessionData = getMySessionData();
          //                                if ((isAdmin()) || $this->Session->read('Auth.User.id') == $city['City']['user_id']) {
                                          if ((isAdmin()) || $sessionData['id'] == $city['City']['user_id']) {
                                              echo $this->Html->link('', array('action' => 'edit', encrypt($city['City']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this City')));
                                              if (isAdmin()) {
                                                  echo $this->Html->link('', array('action' => 'delete', encrypt($city['City']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this City')), __('Are you sure you want to delete City?'));
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
