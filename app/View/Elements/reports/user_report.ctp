<?php // debug($activity);exit; ?>
<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');?>
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
                            <?php echo __('DynaCore Station ID') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['station']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['user'])) : ?>
                        <strong>
                            <?php echo __('Teller:') ?>
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
                    <th>
                        <?php
                        echo __('#');

                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name'));
                        ?>
                    </th>
					<?php }?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('UserReport.station', __('DynaCore Station ID'));
                        ?>
                    </th>
                     <th>
                        <?php
                            echo $this->Paginator->sort('UserReport.teller_name', __('Teller Name'));
                        ?>
                    </th>
                    <th>
                        <?php
                            echo $this->Paginator->sort('UserReport.role', __('DynaCore Groups'));
                        ?>
                    </th>
                    
                    <th> 
                        <?php
                        echo $this->Paginator->sort('UserReport.updated_date', __('Updated Date/Time'));
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
                            echo isset($act['FileProccessingDetail']['station']) ? $temp_station[$act['FileProccessingDetail']['station']] : '';
                            ?>
                        </td>
                        <td> 
                            <?php
                                echo !empty($act['UserReport']['user_name']) ? $act['UserReport']['user_name'] : '-';
                            ?>
                        </td>
                        <td> 
                            <?php
                                echo !empty($act['UserReport']['role']) ? $act['UserReport']['role'] : '-';
                            ?>
                        </td>
                      
                      <td> 
                            <?php
                            echo isset($act['UserReport']['updated_date']) ? showdatetime($act['UserReport']['updated_date']) : '';
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