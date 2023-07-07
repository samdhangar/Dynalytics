<?php // debug($bills);exit;?>
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
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.activity_report_id', __('Activity Report Id'));

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
                        echo $this->Paginator->sort('BillsActivityReport.bill_count', __('No. Of Bill Activity'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillType.bill_type', __('Bill type'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_100', __('Denom 100'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_50', __('Denom 50'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_20', __('Denom 20'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_10', __('Denom 10'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_5', __('Denom 5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom_1', __('Denom 1'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.coin', __('Coin'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.total', __('Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_1', __('Cass 1'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_2', __('Cass 2'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_3', __('Cass 3'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_4u', __('Cass 4u'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_4l', __('Cass 4l'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_5', __('Cass 5'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_1_total', __('Cass 1 Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_2_total', __('Cass 2 Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_3_total', __('Cass 3 Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_4u_total', __('Cass 4u Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_4l_total', __('Cass 4l Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.cass_5_total', __('Cass 5 Total'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.message', __('Message'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ActivityReport.trans_datetime', __('Activity Date Time'));

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
                        <td>
                            <?php 
                            echo isset($bill['BillsActivityReport']['activity_report_id']) ? $bill['BillsActivityReport']['activity_report_id'] : '';
//                            if(isset($bill['BillsActivityReport']['activity_report_id'])){
//                                echo $this->Html->link($bill['BillsActivityReport']['activity_report_id'],array('controller' => 'analytics','action' => 'activity_report_view' , encrypt($bill['BillsActivityReport']['activity_report_id'])));
//                            }
                            ?>
                        </td>
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
                            <?php echo $bill['BillsActivityReport']['bill_count']; ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($bill['BillType']['bill_type']) ? $bill['BillType']['bill_type'] : ''; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_100']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_50']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_20']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_10']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_5']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['denom_1']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['coin']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_1']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_2']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_3']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_4u']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_4l']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_5']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_1_total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_2_total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_3_total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_4u_total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_4l_total']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillsActivityReport']['cass_5_total']; ?>
                        </td>
                        <td onclick="getErrorMessage(this)" class="errorMessage" data-action="analytics/getActivityDetail/" data-id="<?php echo encrypt($bill['BillsActivityReport']['id']) ?>">
                            <?php echo cropDetail($bill['BillsActivityReport']['message'], 50); ?>
                        </td>
                        <td>
                            <?php echo isset($bill['ActivityReport']['trans_datetime']) ? showdatetime($bill['ActivityReport']['trans_datetime']) : ''; ?>
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