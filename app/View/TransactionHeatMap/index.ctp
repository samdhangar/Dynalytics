<?php
$this->assign('pagetitle', __('Transaction Heat Maps'));
$this->Custom->addCrumb(__('Transaction Heat Maps'));
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
                            <div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('TransactionHeatMap', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));
                    echo $this->Form->input('name', array('label' => __('Transaction Heat Map Name'), 'placeholder' => __('Transaction Heat Map Name'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                       echo $this->Form->input('branch_id', array('id' => 'analyBranchId', 'label' => __('Branch Name: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                     
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
                    <th rowspan="2" width="5%">
                            <?php
                                echo __('#');
                            ?>
                        </th>
                        <th rowspan="2" width="<?php echo (isAdmin()) ? '20%' : '20%' ?>"><?php echo $this->Paginator->sort('name', __('Transaction Heat Map Name')); ?></th>
                       
                        
                            <th width="10%" rowspan="2"><?php echo $this->Paginator->sort('user_id', __('Branch Name')); ?></th>
                         
                        <th width="15%" rowspan="2"><?php echo $this->Paginator->sort('created', __('DynaCore Station ID')); ?></th>
                        <th width="6%" colspan="2" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>  <?php echo  __('Total Transactions'); ?></center>

                            </th>
                        <th rowspan="2" width="15%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                 <tr>

                    <?php $columns = 7; ?>
                   
                        <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                         
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
                                     <?php    if($country['TransactionHeatMap']['branch_id']==0){
                                  echo "All";
                                }else{
                                   echo $country['CompanyBranches']['name'];
                                  } ?>
                                 

                                </td>
                            
                            <td>
                                <?php if($country['TransactionHeatMap']['machine_id']==0){
                                    echo "All";
                                } else{
                                   echo $country['TransactionHeatMap']['machine_id'];
                                  } ?>
                                
                            </td>
                            <td style="border: 1px solid;  border-color: #ddd; !important">
                              <center>   <?php echo $country['TransactionHeatMap']['trans_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['TransactionHeatMap']['trans_upper']; ?></center> 
                                 
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
