<?php  ?>
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
       <style type="text/css">
           th{
            font-size: 12px;
           }
       </style>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 28; ?>
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
                            echo $this->Paginator->sort('FileProccessingDetail.Company.first_name', __('Company Name'));

                            ?>
                        </th>
                    <?php endif; ?>
                     
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th> 
                        <?php
                         echo $this->Paginator->sort('FileProccessingDetailBranch.name', __('Branch Name'));
                      // echo $this->Paginator->paginate('FileProccessingDetail.Branch.name', __('Branch Name'));

                        ?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date'));

                        ?>
                    </th>
                    
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_100', __('Denom $100'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_50', __('Denom $50'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_20', __('Denom $20'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_10', __('Denom $10'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_5', __('Denom $5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_2', __('Denom $2'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Denom $1'));

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
                            <td>
                                <?php echo isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
                       
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text"> 
                            <?php echo isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td>
                            <?php echo $bill['Inventory']['station']; ?>
                        </td>
                     <td>
                            <?php echo isset($bill['FileProccessingDetail']['file_date']) ? showdate($bill['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        
                           <td>
                            <?php echo ($bill['Inventory']['denom_100'])?($bill['Inventory']['denom_100']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['Inventory']['denom_50'])?($bill['Inventory']['denom_50']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['Inventory']['denom_20'])?($bill['Inventory']['denom_20']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['Inventory']['denom_10'])?($bill['Inventory']['denom_10']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['Inventory']['denom_5'])?($bill['Inventory']['denom_5']):0; ?>
                        </td>
                        <td>
                            <?php echo (($bill['Inventory']['denom_2']))?($bill['Inventory']['denom_2']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['Inventory']['denom_1'])?($bill['Inventory']['denom_1']):0; ?>
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