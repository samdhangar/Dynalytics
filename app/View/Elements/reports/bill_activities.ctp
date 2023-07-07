<?php  ?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($filter_criteria)) : ?>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Filter Criteria') ?>
                    </strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Region:') ?>
                    </strong>
                    <span>
                        <?php echo $filter_criteria['region']; ?>
                    </span>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['branch'])) : ?>
                        <strong>
                            <?php echo __('Branch:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['branch']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['station'])) : ?>
                        <strong>
                            <?php echo __('DynaCore Station ID:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['station']; ?>
                        </span>
                    <?php endif; ?>

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
                    <th class="text_align">
                        <?php
                        echo __('#');

                        ?>
                    </th>
                    <?php
                    if (!isCompany() && empty($companyDetail)) :
                        $noOfFields++;

                    ?>
                        <th class="text_align">
                            <?php
                            echo $this->Paginator->sort('FileProccessingDetail.Company.first_name', __('Company Name'));

                            ?>
                        </th>
                    <?php endif; ?>

                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                        <th class="text_align">
                            <?php
                            echo $this->Paginator->sort('FileProccessingDetailBranch.name', __('Branch Name'));
                            // echo $this->Paginator->paginate('FileProccessingDetail.Branch.name', __('Branch Name'));

                            ?>
                        </th>
                    <?php } ?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Time'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $100';


                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $50';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $20';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $10';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $5';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $2';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Total Inventory $1';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo 'Grand Total';

                        ?>
                    </th>

                     <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_100', __('Dispensable $100'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_50', __('Dispensable $50'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_20', __('Dispensable $20'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_10', __('Dispensable $10'));

                        ?>
                    </th>

            
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_5', __('Dispensable $5'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_2', __('Dispensable $2'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Dispensable $1'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Dispensable Total'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_100', __('Ops Cassette $100'));


                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_50', __('Ops Cassette $50'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_20', __('Ops Cassette $20'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_10', __('Ops Cassette $10'));

                        ?>
                    </th>
                    
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_5', __('Ops Cassette $5'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_2', __('Ops Cassette $2'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom2_1', __('Ops Cassette $1'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Ops Cassette Total'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_100', __('Reject Cassette $100'));


                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_50', __('Reject Cassette $50'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_20', __('Reject Cassette $20'));

                        ?>
                    </th>
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_10', __('Reject Cassette $10'));

                        ?>
                    </th>
                    
                    
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_5', __('Reject Cassette $5'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_2', __('Reject Cassette $2'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom3_1', __('Reject Cassette $1'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.denom1_1', __('Reject Total'));

                        ?>
                    </th>

                </tr>
            </thead>
            <tbody>
                <?php if (empty($bills)) : ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($bills as $bill) : ?>
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (!isCompany() && empty($companyDetail)) : ?>
                            <td>
                                <?php echo isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>

                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                            <td class="table-text">
                                <?php echo isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''; ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo $temp_station[$bill['FileProccessingDetail']['station']]; ?>
                        </td>
                        <td>
                            <?php echo isset($bill['FileProccessingDetail']['file_date']) ? showdate($bill['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        <td>
                            <?php echo date("h:m:s a", strtotime($bill['BillsActivityReport']['entry_timestamp'])); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])); ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])) * 100), '$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])) * 50), '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])) * 20), '$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])) * 10), '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])) * 5), '$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])) * 2), '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])) ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])) * 1), '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (((($bill['BillsActivityReport']['denom1_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100']))) + ((($bill['BillsActivityReport']['denom1_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50']))) + ((($bill['BillsActivityReport']['denom1_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20']))) + ((($bill['BillsActivityReport']['denom1_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10']))) + ((($bill['BillsActivityReport']['denom1_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5']))) + ((($bill['BillsActivityReport']['denom1_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2']))) + ((($bill['BillsActivityReport']['denom1_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])))); ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat((((($bill['BillsActivityReport']['denom1_100'] + $bill['BillsActivityReport']['denom2_100'] + $bill['BillsActivityReport']['denom3_100'])) * 100) + ((($bill['BillsActivityReport']['denom1_50'] + $bill['BillsActivityReport']['denom2_50'] + $bill['BillsActivityReport']['denom3_50'])) * 50) + ((($bill['BillsActivityReport']['denom1_20'] + $bill['BillsActivityReport']['denom2_20'] + $bill['BillsActivityReport']['denom3_20'])) * 20) + ((($bill['BillsActivityReport']['denom1_10'] + $bill['BillsActivityReport']['denom2_10'] + $bill['BillsActivityReport']['denom3_10'])) * 10) + ((($bill['BillsActivityReport']['denom1_5'] + $bill['BillsActivityReport']['denom2_5'] + $bill['BillsActivityReport']['denom3_5'])) * 5) + ((($bill['BillsActivityReport']['denom1_2'] + $bill['BillsActivityReport']['denom2_2'] + $bill['BillsActivityReport']['denom3_2'])) * 2) + ((($bill['BillsActivityReport']['denom1_1'] + $bill['BillsActivityReport']['denom2_1'] + $bill['BillsActivityReport']['denom3_1'])) * 1)), '$'); ?>
                        </td>

                          <!-- ---Dispennsble ------ -->
                        

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_100']) ? ($bill['BillsActivityReport']['denom1_100']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_100'] * 100) ? ($bill['BillsActivityReport']['denom1_100']) * 100 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_50']) ? ($bill['BillsActivityReport']['denom1_50']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_50'] * 50) ? ($bill['BillsActivityReport']['denom1_50']) * 50 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_20']) ? ($bill['BillsActivityReport']['denom1_20']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_20'] * 20) ? ($bill['BillsActivityReport']['denom1_20']) * 20 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_10']) ? ($bill['BillsActivityReport']['denom1_10']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_10'] * 10) ? ($bill['BillsActivityReport']['denom1_10']) * 10 : 0, '$'); ?>
                        </td>
                        

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_5']) ? ($bill['BillsActivityReport']['denom1_5']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_5'] * 5) ? ($bill['BillsActivityReport']['denom1_5']) * 5 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo (($bill['BillsActivityReport']['denom1_2'])) ? ($bill['BillsActivityReport']['denom1_2']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_2'] * 2) ? ($bill['BillsActivityReport']['denom1_2']) * 2 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom1_1']) ? ($bill['BillsActivityReport']['denom1_1']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom1_1'] * 1) ? ($bill['BillsActivityReport']['denom1_1']) * 1 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ((($bill['BillsActivityReport']['denom1_100'] + ($bill['BillsActivityReport']['denom1_50']) + ($bill['BillsActivityReport']['denom1_20']) + ($bill['BillsActivityReport']['denom1_10']) + ($bill['BillsActivityReport']['denom1_5']) + $bill['BillsActivityReport']['denom1_2']) + ($bill['BillsActivityReport']['denom1_1']))); ?>
                        </td>

                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom1_100'] * 100) + ($bill['BillsActivityReport']['denom1_50'] * 50) + ($bill['BillsActivityReport']['denom1_20'] * 20) + ($bill['BillsActivityReport']['denom1_10'] * 10) + ($bill['BillsActivityReport']['denom1_5'] * 5) + $bill['BillsActivityReport']['denom1_2'] * 2) + ($bill['BillsActivityReport']['denom1_1'] * 1)), '$') ?>
                        </td>


                        <!-- ---OPs casses ------ -->
                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_100']) ? ($bill['BillsActivityReport']['denom2_100']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_100'] * 100) ? ($bill['BillsActivityReport']['denom2_100']) * 100 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_50']) ? ($bill['BillsActivityReport']['denom2_50']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_50'] * 50) ? ($bill['BillsActivityReport']['denom2_50']) * 50 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_20']) ? ($bill['BillsActivityReport']['denom2_20']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_20'] * 20) ? ($bill['BillsActivityReport']['denom2_20']) * 20 : 0, '$'); ?>
                        </td>
                    
                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_10']) ? ($bill['BillsActivityReport']['denom2_10']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_10'] * 10) ? ($bill['BillsActivityReport']['denom2_10']) * 10 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_5']) ? ($bill['BillsActivityReport']['denom2_5']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_5'] * 5) ? ($bill['BillsActivityReport']['denom2_5']) * 5 : 0, '$'); ?>
                        </td>
                        
                        
                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_2']) ? ($bill['BillsActivityReport']['denom2_2']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_2'] * 2) ? ($bill['BillsActivityReport']['denom2_2']) * 2 : 0, '$'); ?>
                        </td>
                        

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom2_1']) ? ($bill['BillsActivityReport']['denom2_1']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom2_1'] * 1) ? ($bill['BillsActivityReport']['denom2_1']) * 1 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ((($bill['BillsActivityReport']['denom2_100'] + ($bill['BillsActivityReport']['denom2_50']) + ($bill['BillsActivityReport']['denom2_20']) + ($bill['BillsActivityReport']['denom2_10']) + ($bill['BillsActivityReport']['denom2_5']) + $bill['BillsActivityReport']['denom2_2']) + ($bill['BillsActivityReport']['denom2_1']))); ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom2_100'] * 100) + ($bill['BillsActivityReport']['denom2_50'] * 50) + ($bill['BillsActivityReport']['denom2_20'] * 20) + ($bill['BillsActivityReport']['denom2_10'] * 10) + ($bill['BillsActivityReport']['denom2_5'] * 5) + $bill['BillsActivityReport']['denom2_2'] * 2) + ($bill['BillsActivityReport']['denom2_1'] * 1)), '$'); ?>
                        </td>

                    <!-- ---Rejected casses ------ -->

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_100']) ? ($bill['BillsActivityReport']['denom3_100']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_100'] * 100) ? ($bill['BillsActivityReport']['denom3_100']) * 100 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_50']) ? ($bill['BillsActivityReport']['denom3_50']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_50'] * 50) ? ($bill['BillsActivityReport']['denom3_50']) * 50 : 0, '$');  ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_20']) ? ($bill['BillsActivityReport']['denom3_20']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_20'] * 20) ? ($bill['BillsActivityReport']['denom3_20']) * 20 : 0, '$');  ?>
                        </td>
                        

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_10']) ? ($bill['BillsActivityReport']['denom3_10']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_10'] * 10) ? ($bill['BillsActivityReport']['denom3_10']) * 10 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_5']) ? ($bill['BillsActivityReport']['denom3_5']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_5'] * 5) ? ($bill['BillsActivityReport']['denom3_5']) * 5 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_2']) ? ($bill['BillsActivityReport']['denom3_2']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_2'] * 2) ? ($bill['BillsActivityReport']['denom3_2']) * 2 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($bill['BillsActivityReport']['denom3_1']) ? ($bill['BillsActivityReport']['denom3_1']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($bill['BillsActivityReport']['denom3_1'] * 1) ? ($bill['BillsActivityReport']['denom3_1']) * 1 : 0, '$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ((($bill['BillsActivityReport']['denom3_100'] + ($bill['BillsActivityReport']['denom3_50']) + ($bill['BillsActivityReport']['denom3_20']) + ($bill['BillsActivityReport']['denom3_10']) + ($bill['BillsActivityReport']['denom3_5']) + $bill['BillsActivityReport']['denom3_2']) + ($bill['BillsActivityReport']['denom3_1']))); ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(((($bill['BillsActivityReport']['denom3_100'] * 100) + ($bill['BillsActivityReport']['denom3_50'] * 50) + ($bill['BillsActivityReport']['denom3_20'] * 20) + ($bill['BillsActivityReport']['denom3_10'] * 10) + ($bill['BillsActivityReport']['denom3_5'] * 5) + $bill['BillsActivityReport']['denom3_2'] * 2) + ($bill['BillsActivityReport']['denom3_1'] * 1)), '$'); ?>
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