<?php // debug($activity);exit;?>
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
                    <?php $noOfFields = 20; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                    <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));
                    ?>
                        
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_1', __('Denom 1'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_2', __('Denom 2'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_5', __('Denom 5'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_10', __('Denom 10'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_20', __('Denom 20'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_50', __('Denom 50'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.denom_100', __('Denom 100'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.coin', __('Coin'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.total', __('Total'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TransactionVaultBuy.trans_datetime', __('Transaction Date'));
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activity)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($activity as $act): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                       <td class="table-text">
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';?>
                        </td>
						<?php }?>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_1']) ? $act['TransactionVaultBuy']['denom_1'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_2']) ? $act['TransactionVaultBuy']['denom_2'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_5']) ? $act['TransactionVaultBuy']['denom_5'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_10']) ? $act['TransactionVaultBuy']['denom_10'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_20']) ? $act['TransactionVaultBuy']['denom_20'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_50']) ? $act['TransactionVaultBuy']['denom_50'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['denom_100']) ? $act['TransactionVaultBuy']['denom_100'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['coin']) ? $act['TransactionVaultBuy']['coin'] : '';?>
                        </td>
                        <td>
                            <?php echo isset($act['TransactionVaultBuy']['total']) ? $act['TransactionVaultBuy']['total'] : '';?>
                        </td>
                        <td> 
                            <?php
                            echo isset($act['TransactionVaultBuy']['trans_datetime']) ? showdatetime($act['TransactionVaultBuy']['trans_datetime']) : '';
                            ?>
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