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
                    <?php $noOfFields = 11; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                        <?php echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name')); ?>
                    </th>
					<?php }?>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.station', __('DynaCore Station ID')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Manager.name', __('Manager')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.action', __('Action')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.trans_limit', __('Transaction Limit')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.daily_limit', __('Daily Limit')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.deposit_limit', __('Deposit Limit')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.text', __('Text')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('ManagerSetup.datetime', __('Setup Date')); ?>
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
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td>
                            <?php echo isset($act['ManagerSetup']['station']) ? $act['ManagerSetup']['station'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($act['Manager']['name']) ? $act['Manager']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo $act['ManagerSetup']['action']; ?>
                        </td>
                        <td>
                            <?php echo $act['ManagerSetup']['trans_limit']; ?>
                        </td>
                        <td>
                            <?php echo $act['ManagerSetup']['daily_limit']; ?>
                        </td>
                        <td>
                            <?php echo $act['ManagerSetup']['deposit_limit']; ?>
                        </td>
                        <td class="table-text">
                            <?php echo $act['ManagerSetup']['text']; ?>
                        </td>
                        <td>
                            <?php echo showdatetime($act['ManagerSetup']['datetime']); ?>
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