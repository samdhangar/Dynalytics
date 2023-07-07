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
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th><?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?></th>
					<?php }?>
                    <th><?php echo $this->Paginator->sort('NetCashUsageActivityReport.station', __('DynaCore Station ID')); ?></th>
                    <th><?php echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('activity_report_id', __('Activity Report Id')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_100', __('Denom 100')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_50', __('Denom 50')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_20', __('Denom 20')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_10', __('Denom 10')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_5', __('Denom 5')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_2', __('Denom 2')); ?></th>
                    <th><?php echo $this->Paginator->sort('denom_1', __('Denom 1')); ?></th>
                    <th><?php echo $this->Paginator->sort('coin', __('Coin')); ?></th>
                    <th><?php echo $this->Paginator->sort('net_total', __('Net Total')); ?></th>
                    <th><?php echo $this->Paginator->sort('message', __('Message')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($NetCashes)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($NetCashes as $act): ?> 
                    <tr>
                        <td><?php echo $startNo++; ?></td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
							<td class="table-text"><?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?></td>
                        <?php }?>
						<td><?php echo isset($act['NetCashUsageActivityReport']['station']) ? $act['NetCashUsageActivityReport']['station'] : ''; ?></td>
                        <td><?php echo isset($act['FileProccessingDetail']['file_date'])?showdate($act['FileProccessingDetail']['file_date']):''; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['activity_report_id']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_100']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_50']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_20']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_10']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_5']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_2']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['denom_1']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['coin']; ?></td>
                        <td><?php echo $act['NetCashUsageActivityReport']['net_total']; ?></td>
                        <td class="table-text"><?php echo $act['NetCashUsageActivityReport']['message']; ?></td>
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