<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');
        ?>
        <?php if (!isCompany() && !empty($companyDetail)): ?>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <strong>
                        <?php echo __('Company Name:') ?>
                    </strong>
                    <span>
                        <?php echo $companyDetail['Company']['first_name']; ?>
                    </span>

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
                    <?php $noOfFields = 20; ?>
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
                        echo $this->Paginator->sort('BillAdjustment.manager_id', __('Manager Name'));
                        ?>
                    </th>
<!--                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillAdjustment.datetime', __('Datetime'));
                        ?>
                    </th>-->
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillAdjustment.adjustment_type', __('Adjustment Type'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillAdjustment.adjustment_value', __('Adjustment Value'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillAdjustment.new_value_total', __('New Value'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillAdjustment.datetime', __('Bill Adjustment Date Time'));
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bills)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($bills as $bill): ?>   
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (!isCompany() && empty($companyDetail)): ?>
                            <td class="table-text">
                                <?php echo isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text"> 
                            <?php echo isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td>
                            <?php echo $bill['FileProccessingDetail']['station']; ?>
                        </td>

                        <td class="table-text">
                            
                            <?php echo isset($bill['Manager']['name']) ? $bill['Manager']['name'] : $bill['BillAdjustment']['manager_id']; ?>
                        </td>
<!--                        <td>
                            <?php echo isset($bill['BillAdjustment']['datetime']) ? showdatetime($bill['BillAdjustment']['datetime']) : ''; ?>
                        </td>-->
                        <td class="table-text">
                            <?php echo $bill['BillAdjustment']['adjustment_type']; ?>
                        </td>
                        <td>
                            <?php echo GetNumberFormat(($bill['BillAdjustment']['adjustment_value']),'$'); ?>
                        </td>
                        <td>
                            <?php echo GetNumberFormat(($bill['BillAdjustment']['new_value_total']),'$'); ?>
                        </td>
                        <td>
                            <?php echo showdatetime($bill['BillAdjustment']['datetime']); ?>
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