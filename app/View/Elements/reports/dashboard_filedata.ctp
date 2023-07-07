<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop',array('navId'=>isset($navId)?$navId:'')); ?>
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
                    <?php if (!isCompany()): $noOfFields++; ?>
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
                        <?php echo $this->Paginator->sort('FileProccessingDetail.station', __('Station No')); ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('file_date', __('File Date'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('filename', __('File Name'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('processing_starttime', __('Process Start Time'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('transaction_number', __('No. Of Transactions'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('processing_counter', __('No. Of time processed'));

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
                <?php foreach ($processFiles as $processFile): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (!isCompany()): ?>
                            <td>
                                <?php echo isset($processFile['Company']['first_name']) ? $processFile['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text"> 
                            <?php echo isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td> 
                            <?php echo isset($processFile['FileProccessingDetail']['station']) ? $processFile['FileProccessingDetail']['station'] : ''; ?>
                        </td>
                        <td> 
                            <?php echo showdate($processFile['FileProccessingDetail']['file_date']); ?>
                        </td>
                        <td class="table-text"> 
                            <?php echo $processFile['FileProccessingDetail']['filename']; ?>
                        </td>
                        <td> 
                            <?php echo showdatetime($processFile['FileProccessingDetail']['processing_starttime']); ?>
                        </td>
                        <td>  
                            <?php echo $processFile['FileProccessingDetail']['transaction_number']; ?>
                        </td>
                        <td> 
                            <?php echo $processFile['FileProccessingDetail']['processing_counter']; ?>
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
