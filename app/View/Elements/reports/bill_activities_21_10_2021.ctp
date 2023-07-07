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
                        echo $this->Paginator->sort('BillsActivityReport.station', __('Station'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date'));

                        ?>
                    </th>
                    
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_100', __('Dispensable $100'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_50', __('Dispensable $50'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_20', __('Dispensable $20'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_10', __('Dispensable $10'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_5', __('Dispensable $5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_2', __('Dispensable $2'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Dispensable $1'));

                        ?>
                    </th>
                       <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_100', __('Op Cassette $100'));
 

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_50', __('Op Cassette $50'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_20', __('Op Cassette $20'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_10', __('Op Cassette $10'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_5', __('Op Cassette $5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_2', __('Op Cassette $2'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_1', __('Op Cassette $1'));

                        ?>
                    </th>

                      <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_100', __('Reject Cassette $100'));
 

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_50', __('Reject Cassette $50'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_20', __('Reject Cassette $20'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_10', __('Reject Cassette $10'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_5', __('Reject Cassette $5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_2', __('Reject Cassette $2'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_1', __('Reject Cassette $1'));

                        ?>
                    </th>
                     <th>
                        <?php
                        echo 'Total Inventory $100';
 

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $50';

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $20';

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $10';

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $5';

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $2';

                        ?>
                    </th>
                    <th>
                        <?php
                        echo 'Total Inventory $1';

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
                            <?php echo $bill['BillsActivityReport']['station']; ?>
                        </td>
                     <td>
                            <?php echo isset($bill['FileProccessingDetail']['file_date']) ? showdate($bill['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        
                           <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_100'])?($bill['BillsActivityReport']['denom1_100']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_50'])?($bill['BillsActivityReport']['denom1_50']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_20'])?($bill['BillsActivityReport']['denom1_20']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_10'])?($bill['BillsActivityReport']['denom1_10']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_5'])?($bill['BillsActivityReport']['denom1_5']):0; ?>
                        </td>
                        <td>
                            <?php echo (($bill['BillsActivityReport']['denom1_2']))?($bill['BillsActivityReport']['denom1_2']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom1_1'])?($bill['BillsActivityReport']['denom1_1']):0; ?>
                        </td>
                          
                       
                           <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_100'])?($bill['BillsActivityReport']['denom2_100']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_50'])?($bill['BillsActivityReport']['denom2_50']):0; ?>
                        </td>
                         <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_20'])?($bill['BillsActivityReport']['denom2_20']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_10'])?($bill['BillsActivityReport']['denom2_10']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_5'])?($bill['BillsActivityReport']['denom2_5']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_2'])?($bill['BillsActivityReport']['denom2_2']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom2_1'])?($bill['BillsActivityReport']['denom2_1']):0; ?>
                        </td>
                         <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_100'])?($bill['BillsActivityReport']['denom3_100']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_50'])?($bill['BillsActivityReport']['denom3_50']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_20'])?($bill['BillsActivityReport']['denom3_20']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_10'])?($bill['BillsActivityReport']['denom3_10']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_5'])?($bill['BillsActivityReport']['denom3_5']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_2'])?($bill['BillsActivityReport']['denom3_2']):0; ?>
                        </td>
                        <td>
                            <?php echo ($bill['BillsActivityReport']['denom3_1'])?($bill['BillsActivityReport']['denom3_1']):0; ?>
                        </td>  
                          <td>
                            <?php echo (($bill['BillsActivityReport']['denom1_100']+$bill['BillsActivityReport']['denom2_100']+$bill['BillsActivityReport']['denom3_100'])) ?>
                        </td>
                       <td>
                             <?php echo (($bill['BillsActivityReport']['denom1_50']+$bill['BillsActivityReport']['denom2_50']+$bill['BillsActivityReport']['denom3_50'])) ?>
                        </td>
                        <td>
                             <?php echo (($bill['BillsActivityReport']['denom1_20']+$bill['BillsActivityReport']['denom2_20']+$bill['BillsActivityReport']['denom3_20'])) ?>
                        </td>
                        <td>
                             <?php echo (($bill['BillsActivityReport']['denom1_10']+$bill['BillsActivityReport']['denom2_10']+$bill['BillsActivityReport']['denom3_10'])) ?>
                        </td>
                        <td>
                            <?php echo (($bill['BillsActivityReport']['denom1_5']+$bill['BillsActivityReport']['denom2_5']+$bill['BillsActivityReport']['denom3_5'])) ?>
                        </td>
                        <td>
                             <?php echo (($bill['BillsActivityReport']['denom1_2']+$bill['BillsActivityReport']['denom2_2']+$bill['BillsActivityReport']['denom3_2'])) ?>
                        </td>
                        <td>
                             <?php echo (($bill['BillsActivityReport']['denom1_1']+$bill['BillsActivityReport']['denom2_1']+$bill['BillsActivityReport']['denom3_1'])) ?>
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