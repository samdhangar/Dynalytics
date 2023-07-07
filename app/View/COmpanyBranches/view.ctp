<?php
/**
 * get request named paramater
 */
$formParamter = '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
/**
 * display page title
 */
$pageTitle = __('Branches');
$breadCrumb = array(
    array(
        'title' => __('Branches'),
        'link' => Router::url(array('controller' => 'company_branches', 'action' => 'index'), true)
    )
);
if (!empty($formParamter)) {
    $pageTitle = $companyBranch['Company']['first_name'] . ' - Branches';
    $breadCrumb[1] = array(
        'title' => __($pageTitle),
        'link' => Router::url(array('controller' => 'company_branches', 'action' => 'index', $formParamter), true)
    );
}
$pageTitle = __($companyBranch['CompanyBranch']['name'] . ' - Detail');
$breadCrumb[] = array(
    'title' => $pageTitle,
    'link' => ''
);
$this->assign('pagetitle', __($pageTitle));
/**
 * display breadcrumbs
 */
foreach ($breadCrumb as $breadCrum):
    $this->Custom->addCrumb(__($breadCrum['title']), $breadCrum['link']);

endforeach;

$this->start('top_links');
echo $this->Html->link(__('Back'), $this->request->referer(), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="panel panel-flat" style="float:left; width:100%;">
    <div class="panel-body col-xs-12">
        <div class="box box-primary">
            <div class="overflow-hide-break">
                <div class="box-body branchDetailViewPage">
                    <?php if (empty($companyBranch)) { ?>
                        <?php echo $this->Html->showInfo(__('Invalid branch.'), array('type' => 'warning')) ?>
                    <?php } else { ?>

                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <dl class="dl-horizontal">
                                    <dt><?php echo __('Company Name:'); ?></dt>
                                    <dd>
                                        <?php echo $companyBranch['Company']['first_name']; ?>
                                    </dd>
                                    <dt><?php echo __('Branch Name:'); ?></dt>
                                    <dd>
                                        <?php echo ($companyBranch['CompanyBranch']['name']); ?>
                                        &nbsp;
                                    </dd>
                                    <dt><?php echo __('Contact Name:'); ?></dt>
                                    <dd>
                                        <?php echo ($companyBranch['CompanyBranch']['contact_name']); ?>
                                        &nbsp;
                                    </dd>
                                    <dt><?php echo __('Email:'); ?></dt>
                                    <dd>
                                        <?php echo ($companyBranch['CompanyBranch']['email']); ?>
                                        &nbsp;
                                    </dd>
                                    <dt><?php echo __('Phone No:'); ?></dt>
                                    <dd>
                                        <?php echo ($companyBranch['CompanyBranch']['phone']); ?>
                                        &nbsp;
                                    </dd>
                                    <dt><?php echo __('Status:'); ?></dt>
                                    <dd>
                                        <?php echo $this->Custom->showStatus($companyBranch['CompanyBranch']['branch_status']); ?>
                                    </dd>

                                </dl>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <dl class="dl-horizontal">
                                    <dt><?php echo __('Address:'); ?></dt>
                                    <dd>
                                        <?php
                                        echo $this->Custom->displayAddress($companyBranch['addressArr']);

                                        ?>
                                    </dd>
                                  
                                    

                                    <dt><?php echo __('Added On:'); ?></dt>
                                    <dd>
                                        <?php echo showdate($companyBranch['CompanyBranch']['created']); ?>

                                    </dd>

                                    <dt><?php echo __('Updated On:'); ?></dt>
                                    <dd>
                                        <?php echo showdate($companyBranch['CompanyBranch']['updated_date']); ?>

                                    </dd>
                                </dl>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-flat"  style="float:left; width:100%;">
    <div class="panel-body col-xs-12">
        <div class="box box-primary">
            <div class="overflow-hide-break">
                <div class="box-footer clearfix">
                    <div class="parentNameDiv">
                        <label class="h3"><?php echo __('Branch Admins'); ?>&nbsp;&nbsp;</label><hr>
                    </div>
                    <?php echo $this->element('paginationtop', array('paginateModel' => 'BranchAdmin')); ?>
                </div>
                <div class="box-body table-responsive no-padding">
                    <?php
                    if (isset($this->request->params['paging']['BranchAdmin']['current'])) {
                        $current = $this->request->params['paging']['BranchAdmin']['current'];
                        $page = $this->request->params['paging']['BranchAdmin']['page'];
                        $startNo =  (($current * $page) - $current) + 1;
                    }

                    $colCount = 6;

                    ?>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __('Sr. No.') ?></th>
                                <th><?php echo $this->Paginator->sort('Admin.first_name', __('User name')) ?></th>
                                <th><?php echo $this->Paginator->sort('Admin.email', __('Email')) ?></th>
                                <th><?php echo $this->Paginator->sort('Admin.phone_no', __('Phone')) ?></th>
                                <th><?php echo $this->Paginator->sort('Admin.user_type', __('User Type')); ?></th>
                                <th><?php echo $this->Paginator->sort('BranchAdmin.created', __('Added On')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $startNo = 1;
                            if (empty($branchAdmins)) {

                                ?>
                                <tr>
                                    <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No Branch Admin found.') ?></td>
                                </tr>
                                <?php
                            } else {
                                foreach ($branchAdmins as $key => $branchAdmin):

                                    ?>
                                    <tr>
                                        <td><?php echo $startNo++; ?></td>
                                        <td><?php echo $branchAdmin['Admin']['first_name']; ?></td>
                                        <td><?php echo $branchAdmin['Admin']['email']; ?></td>
                                        <td><?php echo $branchAdmin['Admin']['phone_no']; ?></td>
                                        <td><?php echo $branchAdmin['Admin']['user_type']; ?></td>
                                        <td><?php echo showdate($branchAdmin['BranchAdmin']['created']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer clearfix">
                    <?php echo $this->element('multiplePagination', array('extraParamter' => encrypt($companyBranch['CompanyBranch']['id']),'paginateModel' => 'CompanyBranch')); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-flat"  style="float:left; width:100%;">
    <div class="panel-body col-xs-12">
        <div class="box box-primary">
            <div class="overflow-hide-break">
                <div class="box-footer clearfix">
                    <div class="parentNameDiv">
                        <label class="h3"><?php echo __('File Processing Detail'); ?>&nbsp;&nbsp;</label><hr>
                    </div>
                    <?php echo $this->element('paginationtop', array('paginateModel' => 'FileProccessingDetail')); ?>
                </div>
                <div class="box-body table-responsive no-padding">
                    <?php
                    if (isset($this->request->params['paging']['FileProccessingDetail']['current'])) {
                        $current = $this->request->params['paging']['FileProccessingDetail']['current'];
                        $page = $this->request->params['paging']['FileProccessingDetail']['page'];
                        $startNo =  (($current * $page) - $current) + 1;
                    }

                    ?>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __('Sr. No.') ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.station', __('Station No')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.file_date', __('File Date')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.processing_endtime', __('Processed Datetime')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.error_message', __('Error/Warnings')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.transaction_number', __('Total Transactions')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.total_cash_deposit', __('Total Debit')) ?></th>
                                <th><?php echo $this->Paginator->sort('FileProccessingDetail.total_cash_withdrawal', __('Total Deposits')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//                                $startNo = 1;
                            $colCount = 8;
                            if (empty($fileProccessingDetails)) {

                                ?>
                                <tr>
                                    <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No File Processing Detail found.') ?></td>
                                </tr>
                                <?php
                            } else {
                                foreach ($fileProccessingDetails as $key => $fileProccessingDetail):

                                    ?>
                                    <tr>
                                        <td><?php echo $startNo++; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['FileProccessingDetail']['station']) ? $fileProccessingDetail['FileProccessingDetail']['station'] : ''; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['FileProccessingDetail']['file_date']) ? showdate($fileProccessingDetail['FileProccessingDetail']['file_date']) : ''; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['FileProccessingDetail']['processing_endtime']) ? $fileProccessingDetail['FileProccessingDetail']['processing_endtime'] : ''; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['ErrorDetail'][0]['error_message']) ? $fileProccessingDetail['ErrorDetail'][0]['error_message'] : ''; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['FileProccessingDetail']['transaction_number']) ? $fileProccessingDetail['FileProccessingDetail']['transaction_number'] : ''; ?></td>
                                        <td><?php echo isset($fileProccessingDetail['TransactionDetail'][0]['total_cash_deposit']) ? showAmount($fileProccessingDetail['TransactionDetail'][0]['total_cash_deposit']) : showAmount('0.00'); ?></td>
                                        <td><?php echo isset($fileProccessingDetail['TransactionDetail'][0]['total_cash_withdrawal']) ? showAmount($fileProccessingDetail['TransactionDetail'][0]['total_cash_withdrawal']) : showAmount('0.00'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer clearfix">
                    <?php echo $this->element('multiplePagination', array('extraParamter' => encrypt($companyBranch['CompanyBranch']['id']), 'paginateModel' => 'FileProccessingDetail')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
