<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
        <?php if (!empty($filter_criteria)) : ?>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Filter Criteria') ?>
                    </strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
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
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['selected_dates'])) : ?>
                        <strong>
                            <?php echo __('Selected Dates:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['selected_dates']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
        <?php
        $uri_get = (str_split($this->request->here()));
        $uri_get = $uri_get['65'];
        ?>

    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 26; ?>
                    <th class="text_align"> <?php echo __('#'); ?></th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                        <th class="text_align"> <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?> </th>
                    <?php } ?>
                    <th class="text_align"> <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?> </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TransactionDetail.teller_name', __('Teller Name'));

                        ?>
                    </th>
                    <th style="text-align: center" class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date/Time'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('Transactiontype.id', __('Transaction Type'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('total_amount', __('Total Amount'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_100', __('$100'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_50', __('$50'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_20', __('$20'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_10', __('$10'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_5', __('$5'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_2', __('$2'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('denom_1', __('$1'));

                        ?>
                    </th>
                 
                </tr>
            </thead>
            <tbody>
                <?php if (empty($specialNotes)) : ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php
                $rowCount = 1;
                $tmpCount = 0;
                $totalRecord = count($specialNotes);
                ?>
                <?php foreach ($specialNotes as $kt => $transaction) : ?>
                    <tr>

                        <?php
                        if ($tmpCount <= $kt) {
                            $rowCount = 1;
                            for ($row = $tmpCount; $row <= $totalRecord; $row++) {
                                if ($specialNotes[$row]['Specialnotesreconciliation']['trans_datetime'] == $specialNotes[$row + 1]['Specialnotesreconciliation']['trans_datetime']) {
                                    $rowCount++;
                                } else {
                                    $tmpCount = $row + 1;
                                    break;
                                }
                            }


                        ?>
                            <td class="text_align" rowspan=<?php echo $rowCount; ?> style="width: 3%;">
                                <?php echo  $startNo++; ?>
                            </td>
                            <td class="text_align" rowspan=<?php echo $rowCount; ?>>
                                <?php

                                echo isset($transaction['FileProccessingDetail']['Branch']['name']) ? $transaction['FileProccessingDetail']['Branch']['name'] : '';
                                ?>
                            </td>
                            <?php


                            ?>
                            <td class="text_align" rowspan=<?php echo $rowCount; ?>>
                                <?php
                                if ($compareData == $transaction['Specialnotesreconciliation']['trans_datetime']) {
                                } else {
                                    echo isset($temp_station[$transaction['FileProccessingDetail']['station']]) ? $temp_station[$transaction['FileProccessingDetail']['station']] : '';
                                }
                                ?>
                            </td>
                            <td class="text_align" rowspan=<?php echo $rowCount; ?>>
                                <?php
                                if ($compareData == $transaction['Specialnotesreconciliation']['trans_datetime']) {
                                    # code...
                                } else {
                                    echo isset($transaction['Specialnotesreconciliation']['teller_name']) ? $transaction['Specialnotesreconciliation']['teller_name'] : '-';
                                }
                                ?>
                            </td>

                            <td class="text_align" style="width: 12%;" rowspan=<?php echo $rowCount; ?>>
                                <?php
                                if ($compareData == $transaction['Specialnotesreconciliation']['trans_datetime']) {
                                    # code...
                                } else {
                                    echo isset($transaction['Specialnotesreconciliation']['trans_datetime']) ? date("m-d-Y h:i A", strtotime($transaction['Specialnotesreconciliation']['trans_datetime'])) : '';
                                }
                                ?>
                            </td>
                        <?php  } ?>
                        <td style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>" class="text_right">
                            <?php echo isset($transaction['Specialnotesreconciliation']['transaction_category']) ? $transaction['Specialnotesreconciliation']['transaction_category'] : '-'; ?>
                        </td>
                        <td class="text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php
                            if ($transaction['Specialnotesreconciliation']['trans_type_id'] == 1 || $transaction['Specialnotesreconciliation']['trans_type_id'] == 11) {
                                echo "(" . (($transaction['Specialnotesreconciliation']['total_amount']) ? GetNumberFormat(($transaction['Specialnotesreconciliation']['total_amount']), '$') : 0) . ")";
                            } else {
                                echo ($transaction['Specialnotesreconciliation']['total_amount']) ? GetNumberFormat(($transaction['Specialnotesreconciliation']['total_amount']), '$') : 0;
                            }
                            ?>

                        </td>
                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo isset($transaction['Specialnotesreconciliation']['denom_100']) ? ($transaction['Specialnotesreconciliation']['denom_100']) : 0; ?>
                        </td>

                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_100']*100)?($transaction['Specialnotesreconciliation']['denom_100'])*100:0,'$');?>
                        </td>

                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_50']) ? ($transaction['Specialnotesreconciliation']['denom_50']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_50']*50)?($transaction['Specialnotesreconciliation']['denom_50'])*50:0,'$');?>
                        </td>
                        
                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_20']) ? ($transaction['Specialnotesreconciliation']['denom_20']) : 0; ?>
                        </td>

                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_20']*20)?($transaction['Specialnotesreconciliation']['denom_20'])*20:0,'$');?>
                        </td>

                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_10']) ? ($transaction['Specialnotesreconciliation']['denom_10']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_10']*10)?($transaction['Specialnotesreconciliation']['denom_10'])*10:0,'$');?>
                        </td>

                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_5']) ? ($transaction['Specialnotesreconciliation']['denom_5']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_5']*5)?($transaction['Specialnotesreconciliation']['denom_5'])*5:0,'$');?>
                        </td>
                        
                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_2']) ? ($transaction['Specialnotesreconciliation']['denom_2']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_2']*2)?($transaction['Specialnotesreconciliation']['denom_2'])*2:0,'$');?>
                        </td>

                        <td class="dis_count text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : '' ?>">
                            <?php echo ($transaction['Specialnotesreconciliation']['denom_1']) ? ($transaction['Specialnotesreconciliation']['denom_1']) : 0; ?>
                        </td>
                        <td class="dis_amount text_right" style="background-color: <?php echo ($transaction['Specialnotesreconciliation']['transaction_category'] == ' Differences') ? '#ea1414bd' : ''?>;display:none">
                            <?php echo GetNumberFormat(($transaction['Specialnotesreconciliation']['denom_1']*1)?($transaction['Specialnotesreconciliation']['denom_1'])*1:0,'$');?>
                        </td>

                       
                        <?php

                        $compareData = $transaction['Specialnotesreconciliation']['trans_datetime'];
                        ?>
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