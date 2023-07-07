<?php //debug($activity);exit;?>
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
                    <?php $noOfFields = 8; ?>
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
                        echo $this->Paginator->sort('Manager.name', __('Manager Name'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('ActivityReport.station', __('DynaCore Station ID'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('ActivityReport.bill_count', __('No. of Activity'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('ActivityReport.message', __('Message'));
                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('ActivityReport.trans_datetime', __('Transaction Date'));
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
                            <?php
                            $branchName = isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';
                            echo $branchName;
                            ?>
                        </td>
						<?php }?>
                        <td class="table-text">
                            <?php
                            echo isset($act['Manager']['name']) ? $act['Manager']['name'] : '';
                            ?>
                        </td>
                        <td>
                            <?php
                            
                            echo isset($act['ActivityReport']['station']) ? $act['ActivityReport']['station'] : '';;
                            ?>
                        </td>
                        <td align="center">
                            <?php
                            echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : '';
                            ?>
                        </td>
                        <td>
                            <?php
                            $noOfActivity =  isset($act['ActivityReport']['bill_count']) ? $act['ActivityReport']['bill_count'] : '';
                            echo $this->Html->link($noOfActivity,array('controller' => 'analytics','action' => 'activity_report_view' , encrypt($act['ActivityReport']['id'])));
                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($act['ActivityReport']['message']) ? $act['ActivityReport']['message'] : '';
                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($act['ActivityReport']['trans_datetime']) ? showdate($act['ActivityReport']['trans_datetime']) : '';
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