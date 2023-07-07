<?php // debug($dbGrowth);exit;       ?>
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

                    <th>
                        <?php echo $this->Paginator->sort('DatabaseGrowth.table_name', __('Table Name'));?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('DatabaseGrowth.check_date', __('Check Date'));?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('DatabaseGrowth.size', __('Size')); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dbGrowth)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php
                foreach ($dbGrowth as $act):
                    ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo !empty($act['DatabaseGrowth']['table_name']) ? $act['DatabaseGrowth']['table_name'] : '';
                            ?>

                        </td>
                        <td>
                            <?php
                            echo !empty($act['DatabaseGrowth']['check_date']) ? showdate($act['DatabaseGrowth']['check_date']) : '';
                            ?>

                        </td>
                        <td>
                            <?php
                            echo !empty($act['DatabaseGrowth']['size']) ? $act['DatabaseGrowth']['size'] : '';
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