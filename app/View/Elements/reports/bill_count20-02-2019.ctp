<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');
        ?>
        <?php if (!empty($companyDetail)): ?>
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
                    <?php $noOfFields = 21; ?>
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
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));
                        ?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.station', __('Station'));
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('Manager.name', __('Manager Name'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._1_assumed_count', __('Assumed Count 1'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._1_actual_count', __('Actual Count 1'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._2_assumed_count', __('Assumed Count 2'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._2_actual_count', __('Actual Count 2'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._5_assumed_count', __('Assumed Count 5'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._5_actual_count', __('Actual Count 5'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._10_assumed_count', __('Assumed Count 10'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._10_actual_count', __('Actual Count 10'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._20_assumed_count', __('Assumed Count 20'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._20_actual_count', __('Actual Count 20'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._50_assumed_count', __('Assumed Count 50'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._50_actual_count', __('Actual Count 50'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._100_assumed_count', __('Assumed Count 100'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount._100_actual_count', __('Actual Count 100'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount.coin_assumed_count', __('Assumed Count Coin'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillCount.coin_actual_count', __('Actual Count Coin'));
                        ?>
                    </th>
<!--                    <th>
                        <?php
                        echo $this->Paginator->sort('BillCount.created_date', __('Bill Count Date Time'));
                        ?>
                    </th>-->
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
                            <?php echo isset($bill['FileProccessingDetail']['station']) ? $bill['FileProccessingDetail']['station'] : ''; ?>
                        </td>
                        <td>
                            <?php echo showdate($bill['FileProccessingDetail']['file_date']); ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($bill['Manager']['name']) ? $bill['Manager']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_1_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_1_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_2_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_2_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_5_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_5_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_10_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_10_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_20_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_20_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_50_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_50_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_100_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['_100_actual_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['coin_assumed_count']; ?>
                        </td>
                        <td>
                            <?php echo $bill['BillCount']['coin_actual_count']; ?>
                        </td>
<!--                    <td>
                            <?php echo showdatetime($bill['BillCount']['created_date']); ?>
                        </td>-->
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