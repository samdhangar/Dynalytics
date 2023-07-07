<?php
$this->assign('pagetitle', __('Location Types'));
$this->Custom->addCrumb(__('Location Types'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Add Location Type'), array('action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('Add Location Type'), 'class' => 'btn btn-sm btn-success ', 'escape' => false));
$this->end(); 
//generate search panel
 
?>
 
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
                    </div>

                    <div class="dataTables_wrapper no-footer">
                      <div class="datatable-header">
                        <div class="dataTables_filter" style="width:100%;">
                           <div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('Location', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));
                    echo $this->Form->input('name', array('label' => __('Location Type Name'), 'type' => __('text'), 'placeholder' => __('Location Type Name'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                      
                     
                    ?>

                    <label>&nbsp</label>
                    <div class="col-md-3 form-group">
                        <?php
                         echo "<label for='analyBranchId' >&nbsp;</label><br>";
                        echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                          echo $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('title' => __('reset search'), 'class' => 'btn btn-default'));
                        ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <th width="<?php echo (isAdmin()) ? '30%' : '60%' ?>"><?php echo $this->Paginator->sort('name', __('Location Type Name')); ?></th>
                        <?php
                        if (isAdmin()):
                            $columns++;

                            ?>
                        <?php endif; ?>
                        <th width="15%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                        <th width="15%"><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>
                        <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                            </thead>
                            <tbody>
              <?php if (empty($location)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($location as $locations): ?>
                         
                        <tr>

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                            <td class="table-text">
                                <?php echo h($locations['Location']['name']); ?>
                            </td>

                            <?php if (isAdmin()): ?>
                                <td class="table-text">

                                  <?php
                                  if (empty($locations['Location']['created'])):
                                      echo ' - ';
                                  else:
                                      echo $locations['Location']['updated'] ;

                                  endif;

                                  ?>

                                </td>
                            <?php endif; ?>
                            <td>
                                <?php echo showdatetime($locations['Location']['created']); ?>
                            </td>
                            <td>
                                <?php echo showdatetime($locations['Location']['updated']); ?>
                                 
                            </td>

                              <td class="actions text-center">
                                <?php
                                $sessionData = getMySessionData();
                                     echo $this->Html->link('', array('action' => 'edit', encrypt($locations['Location']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Location')));
                                    echo $this->Html->link('', array('action' => 'delete', encrypt($locations['Location']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Location')), __('Are you sure you want to delete Location?'));
                                
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
        validateSearch("CountrySearchForm", ["CountryName"]);
    });
</script>
