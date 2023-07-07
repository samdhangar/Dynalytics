<?php // debug($activity);exit;       ?>
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
        <table class="table table-hover user-list" id="datatable">
            <thead>
                <tr>
                    <?php $noOfFields = 20; ?>
                    <th>
                        <?php
                        echo __('#');
                        ?>
                    </th>


                    <?php
                    echo '<th>' . $this->Paginator->sort('Company.first_name', __('Financial Institution Name')) . '</th>';
					if (!$this->Session->check('Auth.User.BranchDetail.id')) {
                    echo '<th>' . $this->Paginator->sort('Branch.name', __('Branch Name')) . '</th>';
					}
                    echo '<th>' . $this->Paginator->sort('FileProccessingDetail.filename', __('File Name')) . '</th>';
                    echo '<th>' . $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')) . '</th>';
                    echo '<th>' . $this->Paginator->sort('FileProccessingDetail.transaction_number', __('No. of Transaction')) . '</th>';
                    echo '<th>' . $this->Paginator->sort('Message.message', __('Message')) . '</th>';
                    echo '<th>' . $this->Paginator->sort('Message.datetime', __('Message Date')) . '</th>';
                    ?>

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
                <?php
                foreach ($activity as $act):
                    ?>
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo !empty($act['FileProccessingDetail']['Company']['first_name']) ? $act['FileProccessingDetail']['Company']['first_name'] : '';
                            ?>

                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text">
                            <?php
                            echo !empty($act['FileProccessingDetail']['Branch']['name']) ? $act['FileProccessingDetail']['Branch']['name'] : '';
                            ?>

                        </td>
						<?php }?>
                        <td class="table-text">
                            <?php
                            echo !empty($act['FileProccessingDetail']['filename']) ? $act['FileProccessingDetail']['filename'] : '';
                            ?>

                        </td>
                        <td>
                            <?php
                            echo !empty($act['FileProccessingDetail']['file_date']) ? $act['FileProccessingDetail']['file_date'] : '';
                            ?>

                        </td>
                        <td>
                            <?php
                            echo !empty($act['FileProccessingDetail']['transaction_number']) ? $act['FileProccessingDetail']['transaction_number'] : '';
                            ?>

                        </td>
                        <td class="table-text">
                            <?php
                            echo cropDetail($act['Message']['message'], 50);
                            ?>

                        </td>
                        <td>
                            <?php
                            echo showdatetime($act['Message']['datetime']);
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
