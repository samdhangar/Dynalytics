<?php
$this->assign('pagetitle', __('Machine'));
$this->Custom->addCrumb(__('Machine'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
//echo $this->Html->link(__('Add Machine'), array('action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('Add Machine'), 'class' => 'btn btn-sm btn-success ', 'escape' => false));
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
                        <th width="<?php echo (isAdmin()) ? '30%' : '30%' ?>"><?php echo $this->Paginator->sort('name', __('Machine')); ?></th>
                       
                        <th width="15%"><?php echo $this->Paginator->sort('branch_name', __('Branch Name')); ?></th>
                        <th width="15%"><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                        <th width="15%"><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>
                     <!--    <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th> -->
                </tr>
                            </thead>
                            <tbody>
              <?php if (empty($machine)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>

                    <?php foreach ($machine as $country): ?>
                         
                        <tr>

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                            <td class="table-text">
                                <?php echo h($country['Machine']['name']); ?>
                            </td>
                             <td class="table-text">
                                <?php echo h($country['CompanyBranches']['name']); ?>
                            </td>

                            <?php if (isAdmin()): ?>
                                <td class="table-text">

                                  <?php
                                  if (empty($country['Machine']['created_date'])):
                                      echo ' - ';
                                  else:
                                      echo $country['Machine']['updated_on'] ;

                                  endif;

                                  ?>

                                </td>
                            <?php endif; ?>
                            <td>
                                <?php echo showdatetime($country['Machine']['created_date']); ?>
                            </td>
                            <td>
                                <?php echo showdatetime($country['Machine']['updated_on']); ?>
                                 
                            </td>

                             <!--  <td class="actions text-center">
                                <?php
                              /*  $sessionData = getMySessionData();
                                     echo $this->Html->link('', array('action' => 'edit', encrypt($country['Machine']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Machine')));
                                         echo $this->Html->link('', array('action' => 'delete', encrypt($country['Machine']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Region')), __('Are you sure you want to delete Machine?'));*/
                                
                                ?>
                            </td> -->
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
