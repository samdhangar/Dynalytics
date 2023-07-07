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
                    <?php $noOfFields = 80; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideActivityReport.activity_report_id', __('Activity Report Id'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideActivityReport.station', __('DynaCore Station ID'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideActivityReport.side', __('Side'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('SideActivityReport.type', __('Type'));

                        ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_1', __('Denom 1')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_2', __('Denom 2')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_5', __('Denom 5')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_10', __('Denom 10')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_20', __('Denom 20')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_50', __('Denom 50')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.denom_100', __('Denom 100')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.coin', __('Coin')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.non_cash_dispence_total', __('Non cash Dispence Total')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.total', __('Total')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.machine_total', __('Machine Total')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.check_cashing', __('Check Cashing')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.credit_card_advance', __('Credit Card Advance')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.number_of_transactions', __('No. Of Transaction')); ?>
                    </th>
                    <!--
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.meta', __('Meta')); ?>
                    </th>
                    -->
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.message', __('message')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('SideActivityReport.created_date', __('Created Date')); ?>
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
                            echo isset($act['SideActivityReport']['activity_report_id']) ? $act['SideActivityReport']['activity_report_id'] : '';
                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['station']) ? $act['SideActivityReport']['station'] : '';
                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($act['SideActivityReport']['side']) ? $act['SideActivityReport']['side'] : '';

                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($act['SideActivityReport']['type']) ? $act['SideActivityReport']['type'] : '';

                            ?>
                        </td>

                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_1']) ? $act['SideActivityReport']['denom_1'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_2']) ? $act['SideActivityReport']['denom_2'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_5']) ? $act['SideActivityReport']['denom_5'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_10']) ? $act['SideActivityReport']['denom_10'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_20']) ? $act['SideActivityReport']['denom_20'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_50']) ? $act['SideActivityReport']['denom_50'] : '' ;

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['denom_100']) ? $act['SideActivityReport']['denom_100'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['coin']) ? $act['SideActivityReport']['coin'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['non_cash_dispence_total']) ? $act['SideActivityReport']['non_cash_dispence_total'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['total']) ? $act['SideActivityReport']['total'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['machine_total']) ? $act['SideActivityReport']['machine_total'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['check_cashing']) ? $act['SideActivityReport']['check_cashing'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['credit_card_advance']) ? $act['SideActivityReport']['credit_card_advance'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['number_of_transactions']) ? $act['SideActivityReport']['number_of_transactions'] : '';

                            ?>
                        </td>
                        <!--
                        <td class="table-text">
                            <?php
                            echo isset($act['SideActivityReport']['meta']) ? $act['SideActivityReport']['meta'] : '';

                            ?>
                        </td>
                        --> 
                        <td class="table-text">
                            <?php
                            echo isset($act['SideActivityReport']['message']) ? $act['SideActivityReport']['message'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['SideActivityReport']['created_date']) ? showdatetime($act['SideActivityReport']['created_date']) : '';

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