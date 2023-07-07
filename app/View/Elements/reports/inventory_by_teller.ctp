<?php // debug($activity);exit;    ?>
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
                    <?php $noOfFields = 16; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>

                    <th> 
                        <?php
                        echo $this->Paginator->sort('BillsActivityReport.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('FileProccessingDetail.filename', __('File Name')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.coin', __('Coin')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.starting_inventory', __('Starting Inventory')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.net_adjustments', __('Net Adjustment')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.total', __('Total')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.message', __('message')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_1', __('Denom 1')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_2', __('Denom 2')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_5', __('Denom 5')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_10', __('Denom 10')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_20', __('Denom 20')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_50', __('Denom 50')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.denom_100', __('Denom 100')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('Inventory.created_date', __('Created Date')); ?>
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
                        <td>
                            <?php
                            echo isset($act['Inventory']['station']) ? $act['Inventory']['station'] : '';

                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($act['FileProccessingDetail']['filename']) ? $act['FileProccessingDetail']['filename'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['coin']) ? $act['Inventory']['coin'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['starting_inventory']) ? $act['Inventory']['starting_inventory'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['net_adjustments']) ? $act['Inventory']['net_adjustments'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['total']) ? $act['Inventory']['total'] : '';

                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($act['Inventory']['message']) ? $act['Inventory']['message'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_1']) ? $act['Inventory']['denom_1'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_2']) ? $act['Inventory']['denom_2'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_5']) ? $act['Inventory']['denom_5'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_10']) ? $act['Inventory']['denom_10'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_20']) ? $act['Inventory']['denom_20'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_50']) ? $act['Inventory']['denom_50'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['denom_100']) ? $act['Inventory']['denom_100'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['Inventory']['created_date']) ? date('m/d/y h:i:s a',  strtotime($act['Inventory']['created_date'])) : '';

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