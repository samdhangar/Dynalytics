<?php // debug($activity);exit;   ?>
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
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideLog.teller_id', __('Teller Id'));

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
                        echo $this->Paginator->sort('SideLog.side_type', __('Side Type'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideLog.message', __('Message'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideLog.logon_datetime', __('Logon Time'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideLog.logoff_datetime', __('Logoff Time'));

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
                        <td>
                            <?php echo isset($act['SideLog']['teller_id']) ? $act['SideLog']['teller_id'] : ''; ?>
                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text">
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td class="table-text">
                            <?php echo isset($act['SideLog']['side_type']) ? $act['SideLog']['side_type'] : ''; ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($act['SideLog']['message']) ? $act['SideLog']['message'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['SideLog']['logon_datetime']) ? showdatetime($act['SideLog']['logon_datetime']) : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act['SideLog']['logoff_datetime']) ? showdatetime($act['SideLog']['logoff_datetime']) : ''; ?>
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