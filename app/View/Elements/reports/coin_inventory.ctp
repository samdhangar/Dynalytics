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
                    <th><?php echo __('Sr. No.'); ?></th>
                    <?php
                    if (!isCompany() && empty($companyDetail) && ($this->action != 'inventory_management')):
                        $noOfFields++;

                        ?>
                        <th><?php echo $this->Paginator->sort('Company.first_name', __('Company Name')); ?></th>
                    <?php endif; ?>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
						<th><?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?></th>
                    <?php }?>
					<th><?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?></th>
                    <th><?php echo $this->Paginator->sort('CoinInventory.coin_adjusted_1', __('Coin Adjusted 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CoinInventory.new_coin_total_1', __('New Coin Total 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('CoinInventory.coin_adjusted_2', __('Coin adjusted 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('CoinInventory.new_coin_total_2', __('New Coin Total 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($CoinInventorys)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($CoinInventorys as $act): ?> 
                    <tr>
                        <td><?php echo $startNo++; ?></td>
                        <?php if (!isCompany() && empty($companyDetail) && ($this->action != 'inventory_management')): ?>
                            <td class="table-text">
                                <?php echo isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                            <?php
                        endif;

                        ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
							<td>
								<?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
							</td>
						<?php }?>	
                        <td><?php echo $act['FileProccessingDetail']['station']; ?></td>
                        <td><?php echo $act['CoinInventory']['coin_adjusted_1']; ?></td>
                        <td><?php echo $act['CoinInventory']['new_coin_total_1']; ?></td>
                        <td><?php echo $act['CoinInventory']['coin_adjusted_2']; ?></td>
                        <td><?php echo $act['CoinInventory']['new_coin_total_2']; ?></td>
                        <td>
                            <?php echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot></tfoot>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>