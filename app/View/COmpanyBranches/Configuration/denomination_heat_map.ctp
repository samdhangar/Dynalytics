<?php
$this->assign('pagetitle', __('Denomination Heat Map'));
$this->Custom->addCrumb(__('Denomination Heat Map'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('Denomination Heat Map'), array('action' => 'addconfig'), array('icon' => 'cc icon-plus3 add', 'title' => __('Denomination Heat Map'), 'class' => 'btn btn-sm btn-success ', 'escape' => false));
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
                       
                        <th width="10%"><?php echo $this->Paginator->sort('branch_name', __('Branch Name')); ?></th>
                          <th width="10%"><?php echo $this->Paginator->sort('branch_name', __('Machine')); ?></th>
                        <th width="8%"><?php echo $this->Paginator->sort('created', __('$1  H|L')); ?></th> 
                         <th width="8%"><?php echo $this->Paginator->sort('created', __('$2  H|L')); ?></th> 
                          <th width="8%"><?php echo $this->Paginator->sort('created', __('$5  H|L')); ?></th> 
                           <th width="8%"><?php echo $this->Paginator->sort('created', __('$10  H|L')); ?></th> 
                            <th width="8%"><?php echo $this->Paginator->sort('created', __('$20  H|L')); ?></th> 
                             <th width="8%"><?php echo $this->Paginator->sort('created', __('$50  H|L')); ?></th>
                              <th width="8%"><?php echo $this->Paginator->sort('created', __('$100  H|L')); ?></th>  
                        <th width="8%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                            </thead>
                            <tbody>

              <?php if (empty($Configuration)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No Configuration found.') ?></td>
                    </tr>
                <?php } else { ?>

                    <?php foreach ($Configuration as $country): ?>
                         
                        <tr>

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                          <td class="table-text">
                                <?php echo h($country['Configuration']['name']); ?>
                            </td>
                                <td class="table-text">
                                <?php echo h($country['CompanyBranches']['name']); ?>
                            </td>
 
                          
                            <td>
                                <?php echo $country['Configuration']['machine_id']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['1_lower']; ?>|<?php echo $country['Configuration']['1_upper']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['2_lower']; ?>|<?php echo $country['Configuration']['2_upper']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['5_lower']; ?>|<?php echo $country['Configuration']['5_upper']; ?>
                            </td>
                              <td>
                                <?php echo $country['Configuration']['10_lower']; ?>|<?php echo $country['Configuration']['10_upper']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['20_lower']; ?>|<?php echo $country['Configuration']['20_upper']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['50_lower']; ?>|<?php echo $country['Configuration']['50_upper']; ?>
                            </td>
                             <td>
                                <?php echo $country['Configuration']['100_lower']; ?>|<?php echo $country['Configuration']['100_upper']; ?>
                            </td>

                               <td class="actions text-center">
                                <?php
                                $sessionData = getMySessionData();
                                     echo $this->Html->link('', array('action' => 'edit', encrypt($country['Configuration']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Config')));
                                     if($country['Configuration']['Is_default']!=1){
                                         echo $this->Html->link('', array('action' => 'delete', encrypt($country['Configuration']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Congig')), __('Are you sure you want to delete Configuration?'));
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
