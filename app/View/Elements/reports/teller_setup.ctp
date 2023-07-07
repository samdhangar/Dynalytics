<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($companyDetail)): ?>
            <!--            <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <strong>
            <?php echo __('Company Name:') ?>
                                </strong>
                                <span>
            <?php echo $companyDetail['Company']['first_name']; ?>
                                </span>
            
                            </div>
                        </div>-->
        <?php endif; ?>
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
                        echo $this->Paginator->sort('TellerSetup.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));

                        ?>  
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerSetup.teller_id', __('Teller Id'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerSetup.action', __('Action'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerSetup.trans_limit', __('Transaction Limit'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerSetup.daily_limit', __('Daily Limit'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerSetup.deposit_limit', __('Deposit Limit'));

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
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td> 
						<?php }?>						
                        <td>
                            <?php echo isset($act['TellerSetup']['station']) ? $act['TellerSetup']['station'] : ''; ?>
                        </td>
                        <td align="center">
                            <?php echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>   
                        <td>
                            <?php echo isset($act['TellerSetup']['teller_id']) ? $act['TellerSetup']['teller_id'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['TellerSetup']['action']) ? $act['TellerSetup']['action'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['TellerSetup']['trans_limit']) ? $act['TellerSetup']['trans_limit'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['TellerSetup']['daily_limit']) ? $act['TellerSetup']['daily_limit'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['TellerSetup']['deposit_limit']) ? $act['TellerSetup']['deposit_limit'] : ''; ?>
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