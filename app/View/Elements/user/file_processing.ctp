<?php // debug($processFiles);exit;       ?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop',array('navId'=>'file_processed_data')); ?>
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
    <div class="box-body table-responsive no-padding" id="file_processed_data">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 30; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <th>
                     <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID')); ?>
                    </th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
					<th> 
							<?php
							echo $this->Paginator->sort('CompanyBranch.name', __('Branch Name'));
							?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('file_date', __('Date'));

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('no_of_file_received', __('No. File received for processing'));
                        echo $this->Paginator->sort('processing_counter', __('No. File received for processing'));

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('fileProcessed', __('File Processed'));
                        echo $this->Paginator->sort('processing_counter', __('File Processed'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_errors', __('No. of Errors'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_transaction', __('No. of transactions'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_deposit', __('No. of deposits'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_withdrawal', __('No. of withdrawals'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_report', __('No. of reports'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_automix', __('No. of Automix Settings'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_billactivity', __('No. of Bill Activity Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_billadjustment', __('No. of Bill Adjustment Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_billcount', __('No. of Bill Count Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_billhistory', __('No. of Bill History Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_coininventory', __('No. of Coin Inventory'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_currTellerTrans', __('No. of Current Teller Transaction'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_historyReport', __('No. of History Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_mgrSetup', __('No. of Manager Setup'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_netCashUsage', __('No. of Net Cash Usage Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_sideActivity', __('No. of Side Activity Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_tellerActivity', __('No. of Teller Activity Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_vaultBuy', __('No. of Valut Buy Report'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('no_of_teller_setup', __('No. of Teller Setup'));

                        ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('total_cash_deposit', __('Total Cash Deposited')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('total_cash_requested', __('Total Cash Requested')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('total_cash_withdrawal', __('Total Cash Withdrawal')); ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('processing_starttime', __('First Process Time'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('processing_endtime', __('Last Process Time'));

                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($processFiles)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php 
                if(isTestMode()){
                                debug($processFiles); exit;
                            }
                foreach ($processFiles as $processFile): ?> 
				<?php //echo "<pre>";print_r($processFile['TransactionDetail']);exit;?>
                    <tr>
                        <td align="center" style="width: 3%;">
                            <?php echo "#"; ?>
                        </td>
                        <td> 
                            <?php echo isset($processFile['FileProccessingDetail']['station']) ? $processFile['FileProccessingDetail']['station'] : ''; ?>
                        </td>
						 <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td> 
                            <?php echo isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td align="center"> 
                            <?php echo showdate($processFile['FileProccessingDetail']['file_date']); ?>
                        </td>
                        <td> 
                            <?php // echo $processFile['FileProccessingDetail']['no_of_file_received']; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['processing_counter'])?$processFile['FileProccessingDetail']['processing_counter']:0; ?>
                        </td>
                        <td> 
                            <?php // echo $processFile['FileProccessingDetail']['fileProcessed']; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['processing_counter'])?$processFile['FileProccessingDetail']['processing_counter']:0; ?>
                        </td>
                        <td> 
                            <?php 
                            
                            //echo isset($processFile['ErrorDetail'][0]['no_of_errors']) ? $processFile['ErrorDetail'][0]['no_of_errors'] : 0; 
                            echo isset($processFile['FileProccessingDetail']['no_of_errors']) ? $processFile['FileProccessingDetail']['no_of_errors'] : 0; 
							?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['TransactionDetail'][0]['no_of_transaction']) ? $processFile['TransactionDetail'][0]['no_of_transaction'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_transaction']) ? $processFile['FileProccessingDetail']['no_of_transaction'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['TransactionDetail'][0]['no_of_deposit']) ? $processFile['TransactionDetail'][0]['no_of_deposit'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_deposit']) ? $processFile['FileProccessingDetail']['no_of_deposit'] : 0; ?>

                        </td>
                        <td>
                            <?php //echo isset($processFile['TransactionDetail'][0]['no_of_withdrawal']) ? $processFile['TransactionDetail'][0]['no_of_withdrawal'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_withdrawal']) ? $processFile['FileProccessingDetail']['no_of_withdrawal'] : 0; ?>
                        </td>
                        <td> 
                            <?php //echo isset($processFile['TransactionDetail'][0]['no_of_report']) ? $processFile['TransactionDetail'][0]['no_of_report'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_report']) ? $processFile['FileProccessingDetail']['no_of_report'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['AutomixSetting'][0]['no_of_automix']) ? $processFile['AutomixSetting'][0]['no_of_automix'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_automix']) ? $processFile['FileProccessingDetail']['no_of_automix'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['BillsActivityReport'][0]['no_of_billactivity']) ? $processFile['BillsActivityReport'][0]['no_of_billactivity'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_billactivity']) ? $processFile['FileProccessingDetail']['no_of_billactivity'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['BillAdjustment'][0]['no_of_billadjustment']) ? $processFile['BillAdjustment'][0]['no_of_billadjustment'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_billadjustment']) ? $processFile['FileProccessingDetail']['no_of_billadjustment'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['BillCount'][0]['no_of_billcount']) ? $processFile['BillCount'][0]['no_of_billcount'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_billcount']) ? $processFile['FileProccessingDetail']['no_of_billcount'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['BillHistory'][0]['no_of_billhistory']) ? $processFile['BillHistory'][0]['no_of_billhistory'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_billhistory']) ? $processFile['FileProccessingDetail']['no_of_billhistory'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['CoinInventory'][0]['no_of_coininventory']) ? $processFile['CoinInventory'][0]['no_of_coininventory'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_coininventory']) ? $processFile['FileProccessingDetail']['no_of_coininventory'] : 0; ?>
                        </td>
                        <td> 
                            <?php //echo isset($processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans']) ? $processFile['CurrentTellerTransactions'][0]['no_of_currTellerTrans'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_currTellerTrans']) ? $processFile['FileProccessingDetail']['no_of_currTellerTrans'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['HistoryReport'][0]['no_of_historyReport']) ? $processFile['HistoryReport'][0]['no_of_historyReport'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_historyReport']) ? $processFile['FileProccessingDetail']['no_of_historyReport'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['ManagerSetup'][0]['no_of_mgrSetup']) ? $processFile['ManagerSetup'][0]['no_of_mgrSetup'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_mgrSetup']) ? $processFile['FileProccessingDetail']['no_of_mgrSetup'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage']) ? $processFile['NetCashUsageActivityReport'][0]['no_of_netCashUsage'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_netCashUsage']) ? $processFile['FileProccessingDetail']['no_of_netCashUsage'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['SideActivityReport'][0]['no_of_sideActivity']) ? $processFile['SideActivityReport'][0]['no_of_sideActivity'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_sideActivity']) ? $processFile['FileProccessingDetail']['no_of_sideActivity'] : 0; ?>
                        </td>
                        <td> 
                            <?php //echo isset($processFile['TellerActivityReport'][0]['no_of_tellerActivity']) ? $processFile['TellerActivityReport'][0]['no_of_tellerActivity'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_tellerActivity']) ? $processFile['FileProccessingDetail']['no_of_tellerActivity'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['ValutBuy'][0]['no_of_vaultBuy']) ? $processFile['ValutBuy'][0]['no_of_vaultBuy'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_vaultBuy']) ? $processFile['FileProccessingDetail']['no_of_vaultBuy'] : 0; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['TellerSetup'][0]['no_of_teller_setup']) ? $processFile['TellerSetup'][0]['no_of_teller_setup'] : 0; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['no_of_teller_setup']) ? $processFile['FileProccessingDetail']['no_of_teller_setup'] : 0; ?>
                        </td>
                        <td> 
                            <?php //echo isset($processFile['TransactionDetail'][0]['total_cash_deposit']) ? $processFile['TransactionDetail'][0]['total_cash_deposit'] : ''; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['total_cash_deposit']) ? $processFile['FileProccessingDetail']['total_cash_deposit'] : ''; ?>
                        </td>
                        <td> 
                            <?php //echo isset($processFile['TransactionDetail'][0]['total_cash_requested']) ? $processFile['TransactionDetail'][0]['total_cash_requested'] : ''; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['total_cash_requested']) ? $processFile['FileProccessingDetail']['total_cash_requested'] : ''; ?>
                        </td>
                        <td>
                            <?php //echo isset($processFile['TransactionDetail'][0]['total_cash_withdrawal']) ? $processFile['TransactionDetail'][0]['total_cash_withdrawal'] : ''; ?>
                            <?php echo isset($processFile['FileProccessingDetail']['total_cash_withdrawal']) ? $processFile['FileProccessingDetail']['total_cash_withdrawal'] : ''; ?>
                        </td>
                        <td align="center"> 
                            <?php echo showdatetime($processFile['FileProccessingDetail']['processing_starttime']); ?>
                        </td>
                        <td align="center"> 
                            <?php echo showdatetime($processFile['FileProccessingDetail']['processing_endtime']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
