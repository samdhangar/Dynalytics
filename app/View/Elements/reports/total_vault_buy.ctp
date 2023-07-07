<?php // debug($activity);exit;?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($companyDetail)): ?>
            <!--            <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <strong>
            <?php echo __('Company Name:') ?>
                                </strong>
                                <span>
            <?php echo $companyDetail['Company']['first_name']; ?>
                                </span>
            
                            </div>
                        </div>-->
        <?php endif; ?>
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
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                    <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));
                    ?>
                        
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_1', __('Denom 1'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_2', __('Denom 2'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_5', __('Denom 5'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_10', __('Denom 10'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_20', __('Denom 20'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_50', __('Denom 50'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.denom_100', __('Denom 100'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.coin', __('Coin'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.total', __('Total'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('TotalVaultBuy.created_date', __('Created Date'));
                        ?>
                    </th>
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
                <?php foreach ($activity as $act): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                       <td class="table-text">
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';?>
                        </td>
						<?php }?>
                        <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_1']) ? $act['TotalVaultBuy']['denom_1'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_2']) ? $act['TotalVaultBuy']['denom_2'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_5']) ? $act['TotalVaultBuy']['denom_5'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_10']) ? $act['TotalVaultBuy']['denom_10'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_20']) ? $act['TotalVaultBuy']['denom_20'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_50']) ? $act['TotalVaultBuy']['denom_50'] : ''; ?>
                            </td>
                            <td>
                                <?php echo isset($act['TotalVaultBuy']['denom_100']) ? $act['TotalVaultBuy']['denom_100'] : ''; ?>
                            </td>
							<td>
								<?php echo isset($act['TotalVaultBuy']['coin']) ? $act['TotalVaultBuy']['coin'] : '';?>
							</td>
							<td>
								<?php echo isset($act['TotalVaultBuy']['total']) ? $act['TotalVaultBuy']['total'] : '';?>
							</td>
							<td> 
                            <?php
                            echo isset($act['TotalVaultBuy']['created_date']) ? showdatetime($act['TotalVaultBuy']['created_date']) : '';
                            ?>
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