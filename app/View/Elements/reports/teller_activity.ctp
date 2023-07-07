<?php // debug($activity);exit; ?>
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
                    <?php if (!empty($filter_criteria['user'])) : ?>
                        <strong>
                            <?php echo __('Teller Name:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['user']; ?>
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
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 63; ?>
                    <th class="text_align">
                        <?php
                        echo __('#');

                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name'));
                        ?>
                    </th>
					<?php }?>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.station', __('DynaCore Station ID'));
                        ?>
                    </th>
                     <th class="text_align">
                        <?php
                            echo $this->Paginator->sort('TellerActivityReport.teller_name', __('Teller Name'));
                        ?>
                    </th>
                    <th class="text_align"> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));
                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.number_of_deposits', __('Number Of Deposits'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.number_of_withdrawals', __('Number Of Withdrawals'));

                        ?>
                    </th>
                   <th class="text_align">
                        <?php
                        echo 'Total Transaction';

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.deposit_total', __('Deposit Total'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.Withdrawal_total', __('Withdrawal Total'));

                        ?>
                    </th>
                   
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.net_total', __('Net Total'));

                        ?>
                    </th>
                    
<!--                    <th> 
                        <?php
                        echo $this->Paginator->sort('TellerActivityReport.created_date', __('Create Date'));
                        ?>
                    </th>-->
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
                <?php foreach ($activity as $act): 
                    ?> 
                
                    <tr>
                        <td style="width: 3%;">
                            <?php echo $startNo++; ?>
                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text">
                            <?php
                            echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';
                            ?>
                        </td>
						<?php }?>
                        <td>
                            <?php
                            echo isset($act['TellerActivityReport']['station']) ? $temp_station[$act['TellerActivityReport']['station']] : '';
                            ?>
                        </td>
                        <td> 
                            <?php
                                echo !empty($act['TellerActivityReport']['teller_name']) ? $act['TellerActivityReport']['teller_name'] : '-';
                            ?>
                        </td>
                        <td> 
                            <?php
                            echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : '';
                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['TellerActivityReport']['number_of_deposits']) ? $act['TellerActivityReport']['number_of_deposits'] : '';

                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['TellerActivityReport']['number_of_withdrawals']) ? $act['TellerActivityReport']['number_of_withdrawals'] : '';
                            

                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            $totalTransaction = $act['TellerActivityReport']['number_of_withdrawals']+$act['TellerActivityReport']['number_of_deposits'];
                            // echo $totalTransaction;
                            echo $this->Html->link($totalTransaction,array('controller' => 'analytics', 'action' => 'transaction_details2',$act['FileProccessingDetail']['company_id'],$act['FileProccessingDetail']['station'],$act['TellerActivityReport']['teller_name'],$act['TellerActivityReport']['file_processing_detail_id'],1));

                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['TellerActivityReport']['deposit_total']) ? GetNumberFormat(($act['TellerActivityReport']['deposit_total']),'$') : '';
                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['TellerActivityReport']['Withdrawal_total']) ? GetNumberFormat(($act['TellerActivityReport']['Withdrawal_total']),'$') : '';
                            ?>
                        </td>
                        
                        <td class="text_right">
                            <?php
                            echo isset($act['TellerActivityReport']['net_total']) ? GetNumberFormat(($act['TellerActivityReport']['net_total']),'$')  : '';
                            ?>
                        </td>
                      
<!--                        <td> 
                            <?php
                            echo isset($act['TellerActivityReport']['created_date']) ? showdatetime($act['TellerActivityReport']['created_date']) : '';
                            ?>
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