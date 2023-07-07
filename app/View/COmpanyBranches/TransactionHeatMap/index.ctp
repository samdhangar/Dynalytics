<?php
$this->assign('pagetitle', __('Transaction Heat Map'));
$this->Custom->addCrumb(__('Transaction Heat Map'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Add Transaction'), array('action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('Add Transaction'), 'class' => 'btn btn-sm btn-success ', 'escape' => false));
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
                        <th width="<?php echo (isAdmin()) ? '20%' : '20%' ?>"><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
                       
                        
                            <th width="10%"><?php echo $this->Paginator->sort('user_id', __('Branch')); ?></th>
                         
                        <th width="15%"><?php echo $this->Paginator->sort('created', __('Machine')); ?></th>
                        <th width="10%"><?php echo $this->Paginator->sort('updated', __('Total Transaction   Lower|| Upper')); ?></th>
                        <th width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                            </thead>
                            <tbody> 
                                
              <?php if (empty($TransactionHeatMap)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No State found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($TransactionHeatMap as $country): ?>
                         
                        <tr>

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                            <td class="table-text">
                                <?php echo h($country['TransactionHeatMap']['name']); ?>
                            </td>

                            
                                <td class="table-text">

                                  <?php
                                      echo $country['CompanyBranches']['name'] ;
 
                                  ?>

                                </td>
                            
                            <td>
                                <?php echo $country['TransactionHeatMap']['machine_id']; ?>
                            </td>
                            <td>
                                <?php echo $country['TransactionHeatMap']['trans_lower']; ?>||<?php echo $country['TransactionHeatMap']['trans_upper']; ?>
                                 
                            </td>

                              <td class="actions text-center">
                                <?php
                                $sessionData = getMySessionData();
                                     echo $this->Html->link('', array('action' => 'edit', encrypt($country['TransactionHeatMap']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Region')));
                                       if($country['TransactionHeatMap']['Is_default']!=1){
                                         echo $this->Html->link('', array('action' => 'delete', encrypt($country['TransactionHeatMap']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Region')), __('Are you sure you want to delete Region?'));
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
        validateSearch("CountrySearchForm", ["CountryName"]);
    });
</script>
