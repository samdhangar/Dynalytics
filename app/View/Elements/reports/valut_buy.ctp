<?php // debug($activity);exit;     ?>
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
                        echo $this->Paginator->sort('Manager.first_name', __('Manager Name'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <?php $noOfFields++; ?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('VaultBuy.trans_datetime', __('Transaction Time'));

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
                            <?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td class="table-text">
                            <?php echo isset($act['Manager']['first_name']) ? $act['Manager']['first_name'] : ''; ?>
                        </td>
                        <td> 
                            <?php
                            echo isset($act['ValutBuy']['station']) ? $act['ValutBuy']['station'] : '';

                            ?>
                        </td>
                        <td> 
                            <?php
                            echo isset($act['ValutBuy']['trans_datetime']) ? showdatetime($act['ValutBuy']['trans_datetime']) : '';

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