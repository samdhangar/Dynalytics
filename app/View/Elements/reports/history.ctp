<div class="box box-primary">
            <div class="box-footer clearfix">
                <?php
                echo $this->element('paginationtop');
                ?>
            </div>
            <div class="box-body table-responsive no-padding">
                <?php
                $startNo = (int) $this->Paginator->counter('{:start}');
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $noOfFields = 20; ?>
                            <th>
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <?php
                            if (!isCompany() && empty($companyDetail) && ($this->action != 'inventory_management')):
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
                                echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID'));
                                ?>
                            </th>
                            <th>
                                <?php
                                echo $this->Paginator->sort('Manager.name', __('Manager'));
                                ?>
                            </th>
                            <th>
                                <?php
                                echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));
                                ?>
                            </th>
                            <th>
                                <?php
                                echo $this->Paginator->sort('HistoryReport.report_datetime', __('Report Datetime'));
                                ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($HistoryReport)): ?>
                            <tr>
                                <td colspan="<?php echo $noOfFields; ?>">
                                    <?php echo __('No data available for selected period'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($HistoryReport as $act): ?> 
                            <tr>
                                <td>
                                    <?php echo $startNo++; ?>
                                </td>
                                <?php if (!isCompany() && empty($companyDetail) && ($this->action != 'inventory_management')): ?>
                                    <td class="table-text">
                                        <?php echo isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                                    </td>
                                <?php endif; ?>
								<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                <td class="table-text">
                                    <?php
                                    echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';
                                    ?>
                                </td>
								<?php }?>
                                <td>
                                    <?php
                                    echo isset($act['HistoryReport']['station']) ? $act['HistoryReport']['station'] : '';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($act['Manager']['name']) ? $act['Manager']['name'] : '';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : '';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo isset($act['HistoryReport']['report_datetime']) ? showdatetime($act['HistoryReport']['report_datetime']) : '';
                                    ?>
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