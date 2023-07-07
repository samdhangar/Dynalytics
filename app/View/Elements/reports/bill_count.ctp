    <div class="box box-primary">
        <div class="box-footer clearfix">
            <?php
            echo $this->element('paginationtop'); ?>
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
                        <?php $noOfFields = 30; ?>
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
                                echo $this->Paginator->sort('Company.first_name', __('Company Name'));
                                ?>
                            </th>
                        <?php endif; ?>
                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                            <th class="text_align">
                                <?php
                                echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name'));
                                ?>
                            </th>
                        <?php } ?>
                        <th class="text_align">
                            <?php
                            echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID'));
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

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._1_assumed_count', __('Assumed 1'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._1_assumed_count', __('Assumed $1'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._1_actual_count', __('Actual 1'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._1_actual_count', __('Actual $1'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._2_assumed_count', __('Assumed 2'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._2_assumed_count', __('Assumed $2'));
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._2_actual_count', __('Actual 2'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._2_actual_count', __('Actual $2'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._5_assumed_count', __('Assumed 5'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._5_assumed_count', __('Assumed $5'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._5_actual_count', __('Actual 5'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._5_actual_count', __('Actual $5'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._10_assumed_count', __('Assumed 10'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._10_assumed_count', __('Assumed $10'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._10_actual_count', __('Actual 10'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._10_actual_count', __('Actual $10'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._20_assumed_count', __('Assumed 20'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._20_assumed_count', __('Assumed $20'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._20_actual_count', __('Actual 20'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._20_actual_count', __('Actual $20'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._50_assumed_count', __('Assumed 50'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._50_assumed_count', __('Assumed $50'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._50_actual_count', __('Actual 50'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._50_actual_count', __('Actual $50'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>

                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._100_assumed_count', __('Assumed 100'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._100_assumed_count', __('Assumed $100'));
                            ?>
                        </th>
                        <th class="dis_count text_align">
                            <?php
                            echo $this->Paginator->sort('BillCount._100_actual_count', __('Actual 100'));
                            ?>
                        </th>
                        <th class="dis_amount text_align" style="display: none;">
                            <?php
                            echo $this->Paginator->sort('BillCount._100_actual_count', __('Actual $100'));
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference';
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Assumed Total';
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Actual Total';
                            ?>
                        </th>
                        <th class="text_align">
                            <?php
                            echo 'Difference Total';
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
                                <td class="table-text">
                                    <?php echo isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                                </td>
                            <?php endif; ?>
                            <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                                <td class="table-text">
                                    <?php echo isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''; ?>
                                </td>
                            <?php } ?>
                            <td>
                                <?php echo isset($bill['FileProccessingDetail']['station']) ? $temp_station[$bill['FileProccessingDetail']['station']] : ''; ?>
                            </td>
                            <td>
                                <?php echo date("m/d/Y", strtotime($bill['FileProccessingDetail']['file_date'])); ?>
                            </td>
                            <td>
                                <?php echo showtime($bill['BillCount']['count_time']); ?>
                            </td>

                            <td class="dis_amount text_right" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_1_assumed_count'] * 1), '$'); ?>
                            </td>
                            <td class="dis_count text_right">
                                <?php echo ($bill['BillCount']['_1_assumed_count']); ?>
                            </td>

                            <td class="dis_amount text_right" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_1_actual_count'] * 1), '$'); ?>
                            </td>
                            <td class="dis_count text_right">
                                <?php echo ($bill['BillCount']['_1_actual_count']); ?>
                            </td>
                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_1_actual_count'] * 1 - $bill['BillCount']['_1_assumed_count'] * 1) == 0) ? '' : 'background-color:red; color:white;'; ?>display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_1_actual_count'] * 1 - $bill['BillCount']['_1_assumed_count'] * 1)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_1_actual_count'] - $bill['BillCount']['_1_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo (($bill['BillCount']['_1_actual_count'] - $bill['BillCount']['_1_assumed_count'])); ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_2_assumed_count'] * 2), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_2_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_2_actual_count']), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_2_actual_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_2_actual_count'] *2 - $bill['BillCount']['_2_assumed_count'] *2) == 0) ? '' : 'background-color:red; color:white;'; ?>display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_2_actual_count'] *2 - $bill['BillCount']['_2_assumed_count'] *2)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_2_actual_count'] - $bill['BillCount']['_2_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>">
                                <?php echo $bill['BillCount']['_2_actual_count'] - $bill['BillCount']['_2_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_5_assumed_count']*5), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_5_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_5_actual_count']*5), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_5_actual_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_5_actual_count']*5 - $bill['BillCount']['_5_assumed_count']*5) == 0) ? '' : 'background-color:red; color:white;'; ?>;display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_5_actual_count']*5 - $bill['BillCount']['_5_assumed_count']*5)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_5_actual_count'] - $bill['BillCount']['_5_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo $bill['BillCount']['_5_actual_count'] - $bill['BillCount']['_5_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_10_assumed_count']*10), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_10_assumed_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_10_actual_count']*10), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_10_actual_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_10_actual_count']*10 - $bill['BillCount']['_10_assumed_count']*10) == 0) ? '' : 'background-color:red; color:white;'; ?>; display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_10_actual_count']*10 - $bill['BillCount']['_10_assumed_count']*10)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_10_actual_count'] - $bill['BillCount']['_10_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo $bill['BillCount']['_10_actual_count'] - $bill['BillCount']['_10_assumed_count']; ?>
                            </td>


                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_20_assumed_count']*20), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_20_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_20_actual_count']*20), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_20_actual_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_20_actual_count']*20 - $bill['BillCount']['_20_assumed_count']*20) == 0) ? '' : 'background-color:red; color:white;'; ?>;display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_20_actual_count']*20 - $bill['BillCount']['_20_assumed_count']*20)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_20_actual_count'] - $bill['BillCount']['_20_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo $bill['BillCount']['_20_actual_count'] - $bill['BillCount']['_20_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_50_assumed_count']*50), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_50_assumed_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_50_actual_count']*50), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_50_actual_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_50_actual_count']*50 - $bill['BillCount']['_50_assumed_count']*50) == 0) ? '' : 'background-color:red; color:white;'; ?>;display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_50_actual_count']*50 - $bill['BillCount']['_50_assumed_count']*50)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_50_actual_count'] - $bill['BillCount']['_50_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo $bill['BillCount']['_50_actual_count'] - $bill['BillCount']['_50_assumed_count']; ?>
                            </td>

                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_100_assumed_count']*100), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_100_assumed_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="display: none;">
                                <?php echo GetNumberFormat(($bill['BillCount']['_100_actual_count']*100), '$'); ?>
                            </td>
                            <td class="text_right dis_count">
                                <?php echo $bill['BillCount']['_100_actual_count']; ?>
                            </td>
                            <td class="text_right dis_amount" style="<?php echo (($bill['BillCount']['_100_actual_count']*100 - $bill['BillCount']['_100_assumed_count']*100) == 0) ? '' : 'background-color:red; color:white;'; ?>;display: none;">
                                <?php echo GetNumberFormat((($bill['BillCount']['_100_actual_count']*100 - $bill['BillCount']['_100_assumed_count']*100)), '$'); ?>
                            </td>
                            <td class="text_right dis_count" style="<?php echo (($bill['BillCount']['_100_actual_count'] - $bill['BillCount']['_100_assumed_count']) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php echo $bill['BillCount']['_100_actual_count'] - $bill['BillCount']['_100_assumed_count']; ?>
                            </td>

                            <td class="text_right">
                                <?php echo GetNumberFormat((((($bill['BillCount']['_1_assumed_count'] * 1) + ($bill['BillCount']['_2_assumed_count'] * 2) + ($bill['BillCount']['_5_assumed_count'] * 5) + ($bill['BillCount']['_10_assumed_count'] * 10) + ($bill['BillCount']['_20_assumed_count'] * 20) + ($bill['BillCount']['_50_assumed_count'] * 50) + ($bill['BillCount']['_100_assumed_count'] * 100)))), '$'); ?>
                            </td>
                            <td class="text_right">
                                <?php echo GetNumberFormat((((($bill['BillCount']['_1_actual_count'] * 1) + ($bill['BillCount']['_2_actual_count'] * 2) + ($bill['BillCount']['_5_actual_count'] * 5) + ($bill['BillCount']['_10_actual_count'] * 10) + ($bill['BillCount']['_20_actual_count'] * 20) + ($bill['BillCount']['_50_actual_count'] * 50) + ($bill['BillCount']['_100_actual_count'] * 100)))), '$'); ?>
                            </td>
                            <td class="text_right" style="<?php echo (((($bill['BillCount']['_1_actual_count'] * 1) + ($bill['BillCount']['_2_actual_count'] * 2) + ($bill['BillCount']['_5_actual_count'] * 5) + ($bill['BillCount']['_10_actual_count'] * 10) + ($bill['BillCount']['_20_actual_count'] * 20) + ($bill['BillCount']['_50_actual_count'] * 50) + ($bill['BillCount']['_100_actual_count'] * 100)) - (($bill['BillCount']['_1_assumed_count'] * 1) + ($bill['BillCount']['_2_assumed_count'] * 2) + ($bill['BillCount']['_5_assumed_count'] * 5) + ($bill['BillCount']['_10_assumed_count'] * 10) + ($bill['BillCount']['_20_assumed_count'] * 20) + ($bill['BillCount']['_50_assumed_count'] * 50) + ($bill['BillCount']['_100_assumed_count'] * 100))) == 0) ? '' : 'background-color:red; color:white;'; ?>;">
                                <?php
                                $total = (($bill['BillCount']['_1_actual_count'] * 1) + ($bill['BillCount']['_2_actual_count'] * 2) + ($bill['BillCount']['_5_actual_count'] * 5) + ($bill['BillCount']['_10_actual_count'] * 10) + ($bill['BillCount']['_20_actual_count'] * 20) + ($bill['BillCount']['_50_actual_count'] * 50) + ($bill['BillCount']['_100_actual_count'] * 100)) - (($bill['BillCount']['_1_assumed_count'] * 1) + ($bill['BillCount']['_2_assumed_count'] * 2) + ($bill['BillCount']['_5_assumed_count'] * 5) + ($bill['BillCount']['_10_assumed_count'] * 10) + ($bill['BillCount']['_20_assumed_count'] * 20) + ($bill['BillCount']['_50_assumed_count'] * 50) + ($bill['BillCount']['_100_assumed_count'] * 100));
                                echo  GetNumberFormat(($total), '$')


                                ?>
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