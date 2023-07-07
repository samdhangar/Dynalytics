<?php // debug($activity);exit; ?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');?>
        <?php if (!empty($filter_criteria)) : ?>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Filter Criteria') ?>
                    </strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <strong>
                        <?php echo __('Region:') ?>
                    </strong>
                    <span>
                        <?php echo $filter_criteria['region']; ?>
                    </span>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['branch'])) : ?>
                        <strong>
                            <?php echo __('Branch:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['branch']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['station'])) : ?>
                        <strong>
                            <?php echo __('DynaCore Station ID:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['station']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
       
                        <table class="table table-hover table-bordered" id="datatable">
                            <thead>

                              <tr>
                                  <?php $fieldCount = 11; ?>
                                  <th width="2%" class="text_align">
                                      <?php echo __('#') ?>
                                  </th>
                                  
                   <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                  <th width="10%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                                      ?>
                                  </th>
                   <?php }?>
                                 
                                  <th width="10%" class="text_align">
                                      <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?>
                                  </th>
                                   <th width="11%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorDetail.entry_timestamp', __('Date'));

                                      ?>
                                  </th>
                                   <th width="11%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorDetail.entry_timestamp', __('Time'));

                                      ?>
                                  </th>
                                   <th width="10%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.transaction_type', __('Type of Transaction'));

                                      ?>
                                  </th>
                                    <th width="10%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.severity', __('Severity'));

                                      ?>
                                  </th>
                                   <th width="10%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.error_code', __('Error Code'));

                                      ?>
                                  </th>
                                 

                                  <th width="15%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.error_message', __('Description'));

                                      ?>
                                  </th>
                                  <th width="15%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.error_line_no', __('Error Line No.'));

                                      ?>
                                  </th>
                                  <th width="15%" class="text_align">
                                      <?php
                                      echo $this->Paginator->sort('ErrorTypes.error_line_no', __('View File.'));

                                      ?>
                                  </th>
                                
                              </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($result_table)):

                                    ?>
                                    <tr>
                                        <td colspan="<?php echo $fieldCount; ?>">
                                            <?php echo __('No Any Error/Warning'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                endif;
                                $startNo = (int) $this->Paginator->counter('{:start}');
                                
                                foreach ($result_table as $ticket):
                                    ?>
                                    <tr>
                                        <td class="text_align" style="width: 3%;">
                                            <?php echo $startNo++; ?>
                                        </td>
                                       
                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                        <td class="text_align">
                                            <?php echo isset($ticket['FileProccessingDetail']['branch_id']) ? $temp_companydata[$ticket['FileProccessingDetail']['branch_id']] : ''; ?>
                                        </td>
                        <?php }?>

                                           <td class="text_align">

                                            <?php

                                            echo $temp_station[($ticket['FileProccessingDetail']['station'])];
                                          
                                            ?>
                                        </td>
                                        <td class="text_align">
                                            <?php echo date("m/d/Y", strtotime($ticket['ErrorDetail']['entry_timestamp'])); ?>
                                        </td>
                                         <td class="text_align">
                                            <!-- <?php echo date("h:m:s a", strtotime($ticket['ErrorDetail']['entry_timestamp'])); ?> -->
                                            <?php echo date("g A", strtotime($ticket['ErrorDetail']['entry_timestamp'])); ?>
                                        </td>
                                         <td class="text_align">
                                             <?php echo !empty($ticket['ErrorDetail']['transaction_type']) ?  $ticket['ErrorDetail']['transaction_type'] : '-';?>
                                                  
                                        </td>
                                       <td class="text_align">
                                            <?php echo isset($ticket['ErrorType']['error_level']) ?  $ticket['ErrorType']['error_level'] : '';?>
                                                  
                                        </td>
                                          <td class="text_align">
                                            <?php echo isset($ticket['ErrorType']['error_code']) ?  $ticket['ErrorType']['error_code'] : '';?>
                                        </td>
                                        <td class="text_align">
                                        <?php echo isset($ticket['ErrorDetail']['error_message']) ?  $ticket['ErrorDetail']['error_message'] : '';?>
                                        </td>
                                        <td class="text_align">
                                        <?php echo isset($ticket['ErrorDetail']['error_line_no']) ?  $ticket['ErrorDetail']['error_line_no'] : '';?>
                                        </td>
                                        <td class="text_align">
                                            <?php echo $this->Html->link("Show Error",array('controller'=>'analytics','action'=>'show_error',base64_encode($ticket['FileProccessingDetail']['filename']),$ticket['ErrorDetail']['error_line_no']),array('class'=>'btn btn-default btn-sm'))?>
                                        </td>
                                      
                                      
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>