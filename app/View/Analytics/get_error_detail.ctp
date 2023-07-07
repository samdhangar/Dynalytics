<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <?php echo __('Error Detail'); ?>
    </h4>
</div>
<div class="modal-body table-responsive">
    <div class="row">
        <?php if (!empty($companyDetail)): ?>
        <div class="box-footer clearfix">
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
        </div>
        <?php endif; ?>
        <div class="box-body table-responsive no-padding">
            <?php
            $startNo = 1;

            ?>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <?php $noOfFields = 6; ?>
                        <th>
                            <?php
                            echo __('Sr. No.');

                            ?>
                        </th>
                        <?php
                        if (empty($companyDetail)):
                            $noOfFields++;

                            ?>
                            <th> 
                                <?php
                                echo __('Company Name');

                                ?>
                            </th>
                        <?php endif; ?>
                        <th> 
                            <?php
                            echo __('Branch Name');

                            ?>
                        </th>
                        <th> 
                            <?php
                            echo __('Dealer Name');

                            ?>
                        </th>
                        <th>
                            <?php
                            echo __('Error Message');

                            ?>
                        </th>
                        <th>
                            <?php
                            echo __('Date error occurred');

                            ?>
                        </th>
                        <th>
                            <?php
                            echo __('Date time reported');

                            ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($errors)): ?>
                        <tr>
                            <td colspan="<?php echo $noOfFields; ?>">
                                <?php echo __('No data available for selected period'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($errors as $error): ?> 
                        <tr>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <?php if (empty($companyDetail)): ?>
                                <td>
                                    <?php echo isset($error['FileProccessingDetail']['Company']['first_name']) ? $error['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                                </td>
                            <?php endif; ?>
                            <td> 
                                <?php echo isset($error['FileProccessingDetail']['Branch']['name']) ? $error['FileProccessingDetail']['Branch']['name'] : ''; ?>
                            </td>
                            <td> 
                                <?php echo isset($error['FileProccessingDetail']['Company']['Dealer']['name']) ? $error['FileProccessingDetail']['Company']['Dealer']['name'] : ''; ?>
                            </td>
                            <td>
                                <?php echo $error['ErrorDetail']['error_message']; ?>
                            </td>
                            <td>
                                <?php echo showdatetime($error['ErrorDetail']['entry_timestamp']); ?>
                            </td>
                            <td>
                                <?php echo showdatetime($error['ErrorDetail']['start_date']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>
</div>