<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 64; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <?php
                    if (!isCompany() && empty($companyDetail)):
                        $noOfFields++;

                        ?>
                        <th> 
                            <?php
                            echo $this->Paginator->sort('Company.first_name', __('Company Name'));

                            ?>
                        </th>
                    <?php endif; ?>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                        ?>
                    </th>
					<?php }?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));

                        ?>
                    </th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.trans_datetime', __('Transaction Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.no_of_deposits', __('No. of deposits')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.no_of_withdrawals', __('No. of withdrawals')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_100', __('W Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_50', __('W Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_20', __('W Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_10', __('W Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_5', __('W Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_2', __('W Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_denom_1', __('W Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.w_coin', __('W Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_100', __('D Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_50', __('D Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_20', __('D Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_10', __('D Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_5', __('D Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_2', __('D Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_denom_1', __('D Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.d_coin', __('D Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_100', __('Bw Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_50', __('Bw Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_20', __('Bw Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_10', __('Bw Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_5', __('Bw Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_2', __('Bw Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_denom_1', __('Bw Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bw_coin', __('Bw Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_100', __('Bd Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_50', __('Bd Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_20', __('Bd Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_10', __('Bd Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_5', __('Bd Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_2', __('Bd Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_denom_1', __('Bd Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bd_coin', __('Bd Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_100', __('V Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_50', __('V Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_20', __('V Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_10', __('V Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_5', __('V Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_2', __('V Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_denom_1', __('V Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.v_coin', __('V Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_100', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_50', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_20', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_10', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_5', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_2', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_denom_1', __('Bv Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.bv_coin', __('Bv Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.withdrawals_amt', __('Withdrawals Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.batch_withdrawals_amt', __('Batch Withdrawals Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.buy_amt', __('Buy Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.batch_deposit_amt', __('Batch Deposit Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.deposit_amt', __('Deposit Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.sell_amt', __('Sell Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.vault_buys_amt', __('Valut Buy Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.batch_vault_buys_amt', __('Batch Valut Buys Amount')); ?></th>
                    <th><?php echo $this->Paginator->sort('CurrentTellerTransactions.message', __('Message')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($TellerTransactions)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($TellerTransactions as $act): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (!isCompany() && empty($companyDetail)): ?>
                            <td class="table-text">
                                <?php echo isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>

						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text">
                            <?php
                            echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';

                            ?>
                        </td>
						<?php }?>
                        <td>
                            <?php
                            echo $act['FileProccessingDetail']['station'];

                            ?>
                        </td>
                        <td>
                            <?php
                            echo showdate($act['FileProccessingDetail']['file_date']);

                            ?>
                        </td>
                        <td><?php echo showdatetime($act['CurrentTellerTransactions']['trans_datetime']); ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['no_of_deposits']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['no_of_withdrawals']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['w_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['d_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bw_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bd_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['v_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_100']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_50']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_20']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_10']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_5']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_2']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_denom_1']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['bv_coin']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['withdrawals_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['batch_withdrawals_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['buy_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['batch_deposit_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['deposit_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['sell_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['vault_buys_amt']; ?></td>
                        <td><?php echo $act['CurrentTellerTransactions']['batch_vault_buys_amt']; ?></td>
                        <td class="table-text"><?php echo $act['CurrentTellerTransactions']['message']; ?></td>
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