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
                    <th>
                        <?php
                        echo $this->Paginator->sort('TellerUserReport.teller_id', __('Teller Id'));
                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name'));
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
                    <th>
                        <?php
                        echo $this->Paginator->sort('TellerUserReport.trans_limit', __('Transaction Limit'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('TellerUserReport.daily_limit', __('Daily Limit'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('TellerUserReport.deposit_limit', __('Deposit Limit'));
                        ?>
                    </th>
                    
                    
                </tr>
            </thead>
            <tbody>
                <?php if (empty($TellerUserReports)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($TellerUserReports as $TellerUserReport): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['TellerUserReport']['teller_id']) ? $TellerUserReport['TellerUserReport']['teller_id'] : ''; ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($TellerUserReport['FileProccessingDetail']['Branch']['name']) ? $TellerUserReport['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['FileProccessingDetail']['station']) ? $TellerUserReport['FileProccessingDetail']['station'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['FileProccessingDetail']['file_date']) ? showdate($TellerUserReport['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['TellerUserReport']['trans_limit']) ? $TellerUserReport['TellerUserReport']['trans_limit'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['TellerUserReport']['daily_limit']) ? $TellerUserReport['TellerUserReport']['daily_limit'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($TellerUserReport['TellerUserReport']['deposit_limit']) ? $TellerUserReport['TellerUserReport']['deposit_limit'] : ''; ?>
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