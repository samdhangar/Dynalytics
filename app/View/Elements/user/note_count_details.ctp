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
                    <th class="text_align"> <?php echo __('#'); ?></th>
                    <th class="text_align"> <?php echo $this->Paginator->sort('regions.name', __('Region')); ?> </th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                        <th class="text_align"> <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?> </th>
                    <?php } ?>
                    <th class="text_align"> <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?> </th>
                    <th class="text_align"> <?php echo $this->Paginator->sort('TransactionDetail.trans_type_id', __('Transaction type')); ?> </th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_100', __('$100')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_50', __('$50')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_20', __('$20')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_10', __('$10')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_5', __('$5')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_2', __('$2')); ?></th>
                    <th class="text_align"><?php echo $this->Paginator->sort('TransactionDetail.denom_1', __('$1')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)) : ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($transactions as $transaction) : ?>

                    <tr>
                        <td class="text_align" style="width: 3%;" rowspan="3">
                            <?php echo $startNo++; ?>
                        </td>
                        <td class="text_align" rowspan="3">
                            <?php echo isset($transaction['regions']['name']) ? $transaction['regions']['name'] : ''; ?>
                        </td>
                        <td class="text_align" rowspan="3">
                            <?php echo isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
                        <td rowspan="3">
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? $temp_station[$transaction['FileProccessingDetail']['station']] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? "Notes Deposited" : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_100']) ? $transaction[0]['deposit_denom_100'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_50']) ? $transaction[0]['deposit_denom_50'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_20']) ? $transaction[0]['deposit_denom_20'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_10']) ? $transaction[0]['deposit_denom_10'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_5']) ? $transaction[0]['deposit_denom_5'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_2']) ? $transaction[0]['deposit_denom_2'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['deposit_denom_1']) ? $transaction[0]['deposit_denom_1'] : ''; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? "Notes Dispensed" : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_100']) ? $transaction[0]['withdrawal_denom_100'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_50']) ? $transaction[0]['withdrawal_denom_50'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_20']) ? $transaction[0]['withdrawal_denom_20'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_10']) ? $transaction[0]['withdrawal_denom_10'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_5']) ? $transaction[0]['withdrawal_denom_5'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_2']) ? $transaction[0]['withdrawal_denom_2'] : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php echo isset($transaction[0]['withdrawal_denom_1']) ? $transaction[0]['withdrawal_denom_1'] : ''; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo isset($transaction['FileProccessingDetail']['station']) ? "Total Notes" : ''; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_100 = $transaction[0]['deposit_denom_100'] +  $transaction[0]['withdrawal_denom_100'];
                            $withdrawal_denom_100 = $withdrawal_denom_100 + $deposit_denom_100?>
                            <?php echo $transaction[0]['deposit_denom_100'] +  $transaction[0]['withdrawal_denom_100'] ; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_50 = $transaction[0]['deposit_denom_50'] +  $transaction[0]['withdrawal_denom_50'];
                            $withdrawal_denom_50 = $withdrawal_denom_50 + $deposit_denom_50?>
                            <?php echo $transaction[0]['deposit_denom_50'] +  $transaction[0]['withdrawal_denom_50'] ; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_20 = $transaction[0]['deposit_denom_20'] +  $transaction[0]['withdrawal_denom_20'];
                            $withdrawal_denom_20 = $withdrawal_denom_20 + $deposit_denom_20?>
                            <?php echo $transaction[0]['deposit_denom_20'] +  $transaction[0]['withdrawal_denom_20'] ; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_10 = $transaction[0]['deposit_denom_10'] +  $transaction[0]['withdrawal_denom_10'];
                            $withdrawal_denom_10 = $withdrawal_denom_10 + $deposit_denom_10?>
                            <?php echo $transaction[0]['deposit_denom_10'] +  $transaction[0]['withdrawal_denom_10'] ; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_5 = $transaction[0]['deposit_denom_5'] +  $transaction[0]['withdrawal_denom_5'];
                            $withdrawal_denom_5 = $withdrawal_denom_5 + $deposit_denom_5?>
                            <?php echo $transaction[0]['deposit_denom_5'] +  $transaction[0]['withdrawal_denom_5'] ; ?>
                        </td>
                        <td class="text_right">
                            <?php $deposit_denom_2 = $transaction[0]['deposit_denom_2'] +  $transaction[0]['withdrawal_denom_2'];
                            $withdrawal_denom_2 = $withdrawal_denom_2 + $deposit_denom_2?>
                            <?php echo $transaction[0]['deposit_denom_2'] +  $transaction[0]['withdrawal_denom_2'] ; ?>
                        </td>
                        <td class="text_right">
                        <?php $deposit_denom_1 = $transaction[0]['deposit_denom_1'] +  $transaction[0]['withdrawal_denom_1'];
                            $withdrawal_denom_1 = $withdrawal_denom_1 + $deposit_denom_1?>
                            <?php echo $transaction[0]['deposit_denom_1'] +  $transaction[0]['withdrawal_denom_1'] ; ?>
                        </td>
                    </tr>
                  
                <?php endforeach; ?>
                <tr>    
                    <!-- <td></td>
                    <td></td>
                    <td></td>
                    <td></td> -->
                    <td colspan="5" class="text_align" style="color: #333;font-weight: bold;">Grand Total</td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_100) ? $withdrawal_denom_100 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_50) ? $withdrawal_denom_50 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_20) ? $withdrawal_denom_20 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_10) ? $withdrawal_denom_10 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_5) ? $withdrawal_denom_5 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_2) ? $withdrawal_denom_2 : 0;?></td>
                    <td class="text_right" style="color: #333;font-weight: bold;"><?php echo !empty($withdrawal_denom_1) ? $withdrawal_denom_1 : 0;?></td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>