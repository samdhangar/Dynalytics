<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
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
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['selected_dates'])) : ?>
                        <strong>
                            <?php echo __('Selected Dates:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['selected_dates']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 26; ?>
                    <th> <?php echo __('#'); ?></th>
                     <th> <?php echo (__('Region')); ?> </th> 
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
						<th> <?php echo $this->Paginator->sort('FileProccessingDetail.branch_id', __('Branch Name')); ?> </th>
                    <?php }?>
					<th> <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?> </th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.trans_datetime', __('Date')); ?></th> 
                     <!-- <th><?php echo $this->Paginator->sort('TransactionDetail.trans_datetime', __('Time By Hour')); ?></th>  -->
                    <th><?php echo $this->Paginator->sort('TransactionDetail.total_transaction', __('Number of transaction')); ?></th> 
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($transactions as $transaction): ?> 

                    <tr>
                        <td align="center" style="width: 3%;"> 
                            <?php echo $startNo++; ?>
                        </td>
						<td> 
                            <?php echo isset($transaction['regions']['name']) ? $transaction['regions']['name'] : ''; ?>
                        </td>
							<td class="table-text"> 
								<?php echo isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''; ?>
							</td> 
                         <td> 
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''; ?>
                        </td> 
                         <td align="center"> 
                            <?php echo isset($transaction['TransactionDetail']['trans_datetime']) ? date("m/d/Y",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''; ?>
                        </td> 
                         <!-- <td align="center"> 
                            <?php echo isset($transaction['TransactionDetail']['trans_datetime']) ? date("H",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''; ?>
                        </td>  -->
                        <td> 
                            <?php //echo isset($transaction['TransactionDetail']['total_transaction']) ? $transaction['TransactionDetail']['total_transaction'] : ''; ?>
                            <?php echo $this->Html->link($transaction['TransactionDetail']['total_transaction'],array('controller' => 'analytics', 'action' => 'transaction_details2',$transaction['FileProccessingDetail']['company_id'],$transaction['FileProccessingDetail']['station'],strtotime($transaction['TransactionDetail']['trans_datetime']),date("H",strtotime($transaction['TransactionDetail']['trans_datetime'])),6)) ?></span>
                        </td> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot> 
            </tfoot>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
