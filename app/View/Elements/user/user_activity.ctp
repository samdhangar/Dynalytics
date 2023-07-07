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
                    <?php if (!empty($filter_criteria['branch'])) : ?>
                        <strong>
                            <?php echo __('Branch:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['branch']; ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_criteria['station'])) : ?>
                        <strong>
                            <?php echo __('DynaCore Station ID:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['station']; ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filter_criteria['user'])) : ?>
                        <strong>
                            <?php echo __('Teller:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['user']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['date'])) : ?>
                        <strong>
                            <?php echo __('Selected Dates:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['date']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
        
        <?php $uri_get = (str_split($this->request->here())); 
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
                    <th class="text_align"> <?php echo __('Transaction'); ?></th>
					<th class="text_align"> <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('Transaction Time')); ?> </th>
                    <th style="text-align: center">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Total Amount'));

                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TransactionDetail.teller_name', __('Type'));

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
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('coin', __('Coins'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('status', __('Status'));

                        ?>
                    </th>
                  
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif;?>
                <?php  foreach ($transactions as $transaction):?> 
                    <tr>
                        <td align="center" style="width: 3%;"> 
                            <?php echo $startNo++; ?>
                        </td>
                        <td align="center" style="width: 3%;"> 
                            <?php echo isset($transaction['TransactionDetail']['trans_number']) ? $transaction['TransactionDetail']['trans_number'] : 0?>
                        </td>
                        <?php
                            $style =  '';
                            if(in_array($transaction['TransactionDetail']['trans_type_id'], [1,11,5,13,14])){
                                $style = "color:#000 !important";
                            }elseif(in_array($transaction['TransactionDetail']['trans_type_id'], [2,4,19])){
                                $style = "color:red !important";
                            }
                        ?>
                        <td align="center" style="width: 12%; "> 
                            <?php echo isset($transaction['TransactionDetail']['trans_datetime']) ? date("m-d-Y h:i A",strtotime($transaction['TransactionDetail']['trans_datetime'])) : ''; ?>
                        </td>
                        <td align="center" style="width: 12%; <?php echo $style;?>"> 
                            <?php echo isset($transaction['TransactionDetail']['total_amount']) ? number_format($transaction['TransactionDetail']['total_amount'],2) : 0; ?>
                        </td>
                        <td style="width: 13%;"> 
                            <?php echo isset($transaction['TransactionType']['text']) ? str_replace("Online ","",$transaction['TransactionType']['text']) : '-'; ?>
                        </td> 
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_100'])?($transaction['TransactionDetail']['denom_100']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_100'])*100,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_50'])?($transaction['TransactionDetail']['denom_50']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_50'])*50,'$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_20'])?($transaction['TransactionDetail']['denom_20']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_20'])*20,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_10'])?($transaction['TransactionDetail']['denom_10']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_10'])*10,'$'); ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_5'])?($transaction['TransactionDetail']['denom_5']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_5'])*5,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_2'])?($transaction['TransactionDetail']['denom_2']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_2'])*2,'$'); ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php echo ($transaction['TransactionDetail']['denom_1'])?($transaction['TransactionDetail']['denom_1']):0; ?>
                        </td>
                        <td class=" dis_amount text_right" style="display: none;">
                            <?php echo GetNumberFormat(($transaction['TransactionDetail']['denom_1'])*1,'$'); ?>
                        </td>

                        <td class="text_right">
                            <?php echo ($transaction['TransactionDetail']['coin'])?($transaction['TransactionDetail']['coin']):0; ?>
                        </td>
                        <td align="center" style="width: 5%;">
                            <?php if($transaction['TransactionDetail']['status'] == "C"){?>
                                <i class="fa icon-check " aria-hidden="true" style="color:#22e322db"></i>
                                <?php }else{ ?> <i class="fa icon-check " aria-hidden="true" style="color:red"></i>
                                <?php } ?>
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