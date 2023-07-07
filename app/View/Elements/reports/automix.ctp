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
                    <?php $noOfFields = 20; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <?php
                    if (!isCompany() && empty($companyDetail)):
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
                        <?php echo $this->Paginator->sort('Branch.name', __('Branch Name')); ?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('AutomixSetting.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date'));

                        ?>
                    </th>
                    <th>

                        <?php echo $this->Paginator->sort('AutomixSetting.denom_weighting', __('Denom weighting')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.tier_type', __('Tier Type')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.tier_text', __('Tier Text')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_100', __('Demon 100')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_50', __('Demon 50')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_20', __('Demon 20')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_10', __('Demon 10')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_5', __('Demon 5')); ?>
                    </th>
                    <th>
                        <?php echo $this->Paginator->sort('AutomixSetting.denom_1', __('Demon 1')); ?>
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
                        <?php if (!isCompany() && empty($companyDetail)): ?>
                            <td class="table-text">
                                <?php echo isset($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
							<td class="table-text">
								<?php echo isset($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : ''; ?>
							</td>
						<?php }?>
                        <td>
                            <?php echo $act['AutomixSetting']['station']; ?>
                        </td>
                        <td>
                            <?php echo isset($act['FileProccessingDetail']['file_date']) ? showdate($act['FileProccessingDetail']['file_date']) : ''; ?>
                        </td>
                        <td>    
                            <?php echo $act['AutomixSetting']['denom_weighting']; ?> 
                        </td>
                        <td>    
                            <?php echo $act['AutomixSetting']['tier_type']; ?>
                        </td>
                        <td class="table-text">
                            <?php echo $act['AutomixSetting']['tier_text']; ?>
                        </td>
                        <td>    
                            <?php echo $act['AutomixSetting']['denom_100']; ?>
                        </td>
                        <td>
                            <?php echo $act['AutomixSetting']['denom_50']; ?>
                        </td>
                        <td>    
                            <?php echo $act['AutomixSetting']['denom_20']; ?>
                        </td>
                        <td>
                            <?php echo $act['AutomixSetting']['denom_10']; ?>
                        </td>
                        <td>    
                            <?php echo $act['AutomixSetting']['denom_5']; ?>
                        </td>
                        <td>
                            <?php echo $act['AutomixSetting']['denom_1']; ?>
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