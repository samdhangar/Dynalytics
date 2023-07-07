<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
        
        <?php $uri_get = (str_split($this->request->here())); 
            $uri_get = $uri_get['65'];
        ?>

    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 26; ?>
                    <th class="text_align"> <?php echo __('#'); ?></th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
						<th class="text_align"> <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?> </th>
                    <?php }?>
					<th class="text_align"> <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?> </th>
                    <th style="text-align: center">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date/Time'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TransactionDetail.teller_name', __('Teller Name'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('Transactiontype.id', __('Transaction Type'));

                        ?>
                    </th>

                    <?php 
                        if ($uri_get == 0) { ?>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('inventory_snapshot_value', __('Inventory Snapshot'));?>
                    </th>
                    <?php } ?>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_100', __('Denom $100'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_50', __('Denom $50'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_20', __('Denom $20'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_10', __('Denom $10'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_5', __('Denom $5'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_2', __('Denom $2'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_1', __('Denom $1'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('coin', __('Coins'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('total_amount', __('Total Amount'));

                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactionsDetails)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($transactionsDetails as $transaction):?> 
                    <tr>
                        <td align="center" style="width: 3%;"> 
                            <?php echo $startNo++; ?>
                        </td>
							<td class="table-text"> 
								<?php echo isset($temp_companydata[$transaction['FileProccessingDetail']['branch_id']]) ? $temp_companydata[$transaction['FileProccessingDetail']['branch_id']] : ''; ?>
							</td> 
                         <td> 
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''; ?>
                        </td> 
                         <td align="center" style="width: 12%;"> 
                            <?php echo isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y h:i A",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''; ?>
                        </td> 
                        
                        <td> 
                            <?php echo isset($transaction['TransactionDetail']['teller_name']) ? $transaction['TransactionDetail']['teller_name'] : '-'; ?>
                        </td> 

                        <td style="width: 13%;"> 
                            <?php echo isset($transaction['TransactionType']['text']) ? str_replace("Online ","",$transaction['TransactionType']['text']) : '-'; ?>
                        </td> 

                        <?php
                                if ($transaction['TransactionDetail']['match_with_prev'] == 'Yes') {
                                    $set_class = 'not_Diff_color';
                                  }else {
                                      $set_class = 'diff_color';
                                  }
                        ?>
                        
                        <?php 
                        if ($uri_get == 0) { ?>
                        <td class = <?php echo isset($set_class) ? $set_class : 'text_right'; ?>>
                            <?php echo ($transaction['TransactionDetail']['inventory_snapshot_value'])? GetNumberFormat(($transaction['TransactionDetail']['inventory_snapshot_value']),'$'):0; ?>
                        </td>
                        <?php } ?>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_100'])?($transaction['TransactionDetail']['denom_100']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_50'])?($transaction['TransactionDetail']['denom_50']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_20'])?($transaction['TransactionDetail']['denom_20']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_10'])?($transaction['TransactionDetail']['denom_10']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_5'])?($transaction['TransactionDetail']['denom_5']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_2'])?($transaction['TransactionDetail']['denom_2']):0; ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_1'])?($transaction['TransactionDetail']['denom_1']):0; ?>
                        </td>
                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['coin'])?($transaction['TransactionDetail']['coin']):0; ?>
                        </td>
                        <td class="text_right">
                            <?php 
                                if($transaction['TransactionDetail']['trans_type_id'] == 1 || $transaction['TransactionDetail']['trans_type_id'] == 11 ) {
                                echo "(".(($transaction['TransactionDetail']['total_amount']) ? GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0).")";
                            }else { 
                             echo ($transaction['TransactionDetail']['total_amount']) ? GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0; 
                            }
                             ?>

                        </td>
                        <!-- <?php
                            if (!empty($compare_value)) {
                                if (ceil($compare_value) == ceil($transaction['TransactionDetail']['inventory_snapshot_value'])) {
                                    $set_class = 'text_right';
                                  }else {
                                      $set_class = 'diff_color';
                                  }
                            }
                        ?> -->

                        <?php
                            $dataCompare_transtype = $transaction['TransactionType']['total_text'];
                            $dataCompare_total = $transaction['TransactionDetail']['total_amount'];
                            $dataCompare_inventory = $transaction['TransactionDetail']['inventory_snapshot_value'];
                            if ($dataCompare_transtype == 'Online Mix Dispense' || $dataCompare_transtype == 'Online Nominated Dispense' || $dataCompare_transtype == 'Reconciled Rejects') {
                                $compare_value = $dataCompare_inventory - $dataCompare_total;
                            }else{
                                $dataCompare_machine = $transaction['TransactionDetail']['machine_total'];
                                $compare_value = $dataCompare_inventory + $dataCompare_machine;
                            }
                        ?>
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