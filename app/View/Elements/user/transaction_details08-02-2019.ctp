<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 26; ?>
                    <th> <?php echo __('Sr. No.'); ?></th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
						<th> <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?> </th>
                    <?php }?>
					<th> <?php echo $this->Paginator->sort('Copmany.station_count', __('DynaCore Station ID')); ?> </th>
<!--                    <th><?php echo $this->Paginator->sort('TransactionDetail.created_date', __('Date')); ?></th>-->
                    <th><?php echo $this->Paginator->sort('TransactionDetail.transaction_category', __('Transaction Category')); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.transaction_type', __('Transaction Type')); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.total_transaction', __('No. of transaction')); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.transaction_datetime', __('Transaction Datetime')); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom1', 'Denom 1'); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom5', 'Denom 5'); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom10', 'Denom 10'); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom20', 'Denom 20'); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom50', 'Denom 50'); ?></th>
                    <th><?php echo $this->Paginator->sort('total_denom100', 'Denom 100'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.coin', 'Coin'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.total_amount_requested', 'Amount requested'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.total_amount', 'Total Amount'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.other_cash_deposited', 'Other cash deposited'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.machine_total', 'Machine Total'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.non_cash_total', 'Non Cash total'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.balance_due', 'Balance Due'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.trans_limit_exceeded', 'Trans Limit Exceeded'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.trans_limit_override', 'Trans limit Override'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.override_manager_id', 'Override manager id'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.messages', 'Message'); ?></th>
                    <th><?php echo $this->Paginator->sort('TransactionDetail.status', 'Status'); ?></th>
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
                        <td align="center">
                            <?php echo $startNo++; ?>
                        </td>
						
							<td class="table-text"> 
								<?php echo isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''; ?>
							</td>
					
                        <td> 
                            <?php echo isset($transaction['FileProccessingDetail']['Company']['station_count']) ? $transaction['FileProccessingDetail']['Company']['station_count'] : ''; ?>
                        </td>
<!--                        <td align="center"> 
                            <?php echo isset($transaction['FileProccessingDetail']['file_date']) ? showdate($transaction['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>-->
                        <td class="table-text"> 
                            <?php echo isset($transaction['TransactionCategory']['text']) ? $transaction['TransactionCategory']['text'] : ''; ?>
                        </td>
                        <td class="table-text"> 
                            <?php echo isset($transaction['TransactionType']['text']) ? $transaction['TransactionType']['text'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_transaction']) ? $transaction['TransactionDetail']['total_transaction'] : ''; ?>
                        </td>
                        <td align="center"> 
                            <?php echo isset($transaction['TransactionDetail']['trans_datetime']) ? showdatetime($transaction['TransactionDetail']['trans_datetime']) : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom1']) ? $transaction['TransactionDetail']['total_denom1'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom5']) ? $transaction['TransactionDetail']['total_denom5'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom10']) ? $transaction['TransactionDetail']['total_denom10'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom20']) ? $transaction['TransactionDetail']['total_denom20'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom50']) ? $transaction['TransactionDetail']['total_denom50'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_denom100']) ? $transaction['TransactionDetail']['total_denom100'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['coin']) ? $transaction['TransactionDetail']['coin'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_amount_requested']) ? $transaction['TransactionDetail']['total_amount_requested'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['total_amount']) ? $transaction['TransactionDetail']['total_amount'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['other_cash_deposited']) ? $transaction['TransactionDetail']['other_cash_deposited'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['machine_total']) ? $transaction['TransactionDetail']['machine_total'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['non_cash_total']) ? $transaction['TransactionDetail']['non_cash_total'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['balance_due']) ? $transaction['TransactionDetail']['balance_due'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['trans_limit_exceeded']) ? $transaction['TransactionDetail']['trans_limit_exceeded'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['trans_limit_override']) ? $transaction['TransactionDetail']['trans_limit_override'] : ''; ?>
                        </td>
                        <td class="table-text">  
                            <?php echo isset($transaction['OverrideManagerDetail']['first_name']) ? $transaction['OverrideManagerDetail']['first_name'] : ''; ?>
                            <?php echo isset($transaction['OverrideManagerDetail']['last_name']) ? ' ' . $transaction['OverrideManagerDetail']['last_name'] : ''; ?>
                        </td>
                        <td class="table-text">  
                            <?php echo isset($transaction['TransactionDetail']['messages']) ? $transaction['TransactionDetail']['messages'] : ''; ?>
                        </td>
                        <td class="table-text"> 
                            <?php echo isset($transaction['TransactionDetail']['status']) ? showTransStatus($transaction['TransactionDetail']['status']) : ''; ?>
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
