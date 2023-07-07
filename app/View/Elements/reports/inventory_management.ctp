<?php // debug($activity);exit;      ?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($companyDetail) && !isCompany()): ?>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <strong>
                        <?php echo __('Company Name:') ?>
                    </strong>
                    <span>
                        <?php echo isset($companyDetail['User']['first_name']) ? $companyDetail['User']['first_name'] : ''; ?>
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
                    <th class="text_align">
                        <?php
                        echo __('#');

                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th class="text_align"> 
                        <?php
                       echo $this->Paginator->sort('FileProccessingDetail.branch_id', __('Branch'));
                        // echo __('Branch');

                        ?>
                    </th>
					<?php }?>
                    <th class="text_align"> 
                        <?php
                        echo $this->Paginator->sort('Inventory.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.starting_inventory', __('Starting Inventory')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_100', __('$100')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_50', __('$50')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_20', __('$20')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_10', __('$10')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_5', __('$5')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_2', __('$2')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total_denom_1', __('$1')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.net_adjustments', __('Net Adjustment')); ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('Inventory.total', __('Total')); ?>
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
                            echo isset($act['Inventory']['station']) ? $temp_station[$act['Inventory']['station']] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : '';

                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['Inventory']['starting_inventory']) ? GetNumberFormat($act['Inventory']['starting_inventory'],'$') : '';

                            ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_100']) ? $act['Inventory']['total_denom_100'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_100']*100)?($act['Inventory']['total_denom_100'])*100:0,'$');
                            ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php
                                echo isset($act['Inventory']['denom_50']) ? $act['Inventory']['total_denom_50'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_50']*50)?($act['Inventory']['total_denom_50'])*50:0,'$');
                            ?>
                        </td>
                        
                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_20']) ? $act['Inventory']['total_denom_20'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_20']*20)?($act['Inventory']['total_denom_20'])*20:0,'$');
                            ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_10']) ? $act['Inventory']['total_denom_10'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_10']*10)?($act['Inventory']['total_denom_10'])*10:0,'$');
                            ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_5']) ? $act['Inventory']['total_denom_5'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_5']*5)?($act['Inventory']['total_denom_5'])*5:0,'$');
                            ?>
                        </td>

                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_2']) ? $act['Inventory']['total_denom_2'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_2']*2)?($act['Inventory']['total_denom_2'])*2:0,'$');
                            ?>
                        </td
                        >

                        <td class="dis_count text_right">
                            <?php
                            echo isset($act['Inventory']['denom_1']) ? $act['Inventory']['total_denom_1'] : '';

                            ?>
                        </td>
                        <td class="dis_amount text_right" style="display: none;">
                            <?php 
                                echo GetNumberFormat(($act['Inventory']['denom_1']*1)?($act['Inventory']['total_denom_1'])*1:0,'$');
                            ?>
                        </td>

                        <td class="text_right">
                            <?php
                            echo isset($act['Inventory']['net_adjustments']) ? GetNumberFormat($act['Inventory']['net_adjustments'],'$') : '';

                            ?>
                        </td>
                        <td class="text_right">
                            <?php
                            echo isset($act['Inventory']['total']) ? GetNumberFormat($act['Inventory']['total'],'$') : '';

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