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
                    <?php if (!empty($filter_criteria['user'])) : ?>
                        <strong>
                            <?php echo __('Teller:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['user']; ?>
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

                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('total_amount', __('Total Amount'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_100', __('Denomination $100'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_50', __('Denomination $50'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_20', __('Denomination $20'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_10', __('Denomination $10'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_5', __('Denomination $5'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_2', __('Denomination $2'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_1', __('Denomination $1'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('coin', __('Coins'));

                        ?>
                    </th>
                    <?php 
                        if ($uri_get == 0) { ?>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('inventory_snapshot_value', __('Inventory Snapshot'));?>
                    </th>
                    <?php } ?>
                    <th class="text_align">
                        <?php echo  __('View File'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactionsDetails)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No Out of Balance record found.'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($transactionsDetails as $key => $transaction):?> 
                    <tr>
                        <td align="center" style="width: 3%;"> 
                            <?php echo $startNo++; ?>
                        </td>
							<td class="table-text"> 
								<?php echo isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''; ?>
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

                        <td class="text_right">
                            <?php 
                                if($transaction['TransactionDetail']['trans_type_id'] == 1 || $transaction['TransactionDetail']['trans_type_id'] == 11 ) {
                                echo "(".(($transaction['TransactionDetail']['total_amount']) ? GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0).")";
                            }else { 
                             echo ($transaction['TransactionDetail']['total_amount']) ? GetNumberFormat(($transaction['TransactionDetail']['total_amount']),'$'):0; 
                            }
                             ?>

                        </td>
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_100'])?($transaction['TransactionDetail']['denom_100']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_100'])*100,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_50'])?($transaction['TransactionDetail']['denom_50']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_50'])*50,'$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_20'])?($transaction['TransactionDetail']['denom_20']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_20'])*20,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_10'])?($transaction['TransactionDetail']['denom_10']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_10'])*10,'$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_5'])?($transaction['TransactionDetail']['denom_5']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_5'])*5,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_2'])?($transaction['TransactionDetail']['denom_2']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_2'])*2,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_1'])?($transaction['TransactionDetail']['denom_1']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_1'])*1,'$'); ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['coin'])?($transaction['TransactionDetail']['coin']):0; ?>
                        </td>
                        <?php 
                        if ($uri_get == 0) { ?>
                        <td class = <?php echo isset($set_class) ? $set_class : 'text_right'; ?>>
                            <?php echo ($transaction['TransactionDetail']['inventory_snapshot_value'])? GetNumberFormat(($transaction['TransactionDetail']['inventory_snapshot_value']),'$'):0; ?>
                        </td>
                        <?php } ?>
                       
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
                        <?php if(!empty($transaction['TransactionDetail']['trans_line_no'])){?>
                          <td class="text_align">
                                            <?php echo $this->Html->link("Show Transaction",array('controller'=>'analytics','action'=>'show_transaction',base64_encode($transaction['FileProccessingDetail']['filename']),$transaction['TransactionDetail']['trans_line_no']),array('class'=>'btn btn-default btn-sm'))?>
                                        </td>
                                        <?php }else{?>
                                            <td></td>
                                        <?php } ?>
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