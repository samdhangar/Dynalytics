<?php
$this->assign('pagetitle', __('Client Reports'));
$this->Custom->addCrumb(__('Client Reports'));
//generate search panel
$searchPanelArray = array(
    'name' => 'ClientReport',
    'options' => array(
        'id' => 'ClientReportSearchForm',
        'url' => $this->Html->url(array('action' => 'index'), true),
        'autocomplete' => 'off',
        'novalidate' => 'novalidate',
        'inputDefaults' => array(
            'dir' => 'ltl',
            'class' => 'form-control',
            'required' => false,
            'div' => array(
                'class' => 'form-group col-md-3'
            )
        )
    ),
    'searchDivClass' => 'col-md-12',
    'search' => array(
        'title' => 'Search',
        'options' => array(
            'id' => 'ClientReportSearchBtn',
            'class' => 'btn btn-primary margin-right10',
            'div' => false
        )
    ),
    'reset' => $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('escape' => false, 'title' => __('Display the all the countries'), 'class' => 'btn btn-default marginleft')),
    'fields' => array(
        array(
            'name' => 'company_id',
            'options' => array(
                'type' => 'select',
                'label' => __('Company'),
                'empty' => __('Select Company'),
                'id' => 'clientReportCompId',
                'onchange' => 'getCompanyBranches(this.value)'
            )
        ),
        array(
            'name' => 'branch_id',
            'options' => array(
                'type' => 'select',
                'label' => __('Branch Name'),
                'empty' => __('Select Branch'),
                'id' => 'clientReportBranchId',
                'onchange' => 'getBranchStations(this.value)'
            )
        ),
        array(
            'name' => 'station_id',
            'options' => array(
                'type' => 'select',
                'label' => __('DynaCore Station ID'),
                'empty' => __('Select Station'),
                'id' => 'clientReportStationId',
                'onchange' => 'getStationFiles(this.value)'
            )
        ),
        array(
            'name' => 'file_id',
            'options' => array(
                'type' => 'select',
                'label' => __('File Process Date'),
                'id' => 'clientReportFileId',
                'empty' => __('Select File Process Date')
            )
        )
    )
);

?>



<div class=" panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Errors</h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'ErrorDetail')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 6; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="50%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($errors)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Errors found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($errors as $error): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td class="table-text">
                                        <?php echo isset($error['FileProccessingDetail']['filename']) ? $error['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($error['FileProccessingDetail']['processing_counter']) ? $error['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($error['FileProccessingDetail']['transaction_number']) ? $error['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'ErrorDetail')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Warnings </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'ErrorDetail')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 6; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="50%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($warnings)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Warnings found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($warnings as $warning): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($warning['FileProccessingDetail']['filename']) ? $warning['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($warning['FileProccessingDetail']['processing_counter']) ? $warning['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($warning['FileProccessingDetail']['transaction_number']) ? $warning['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'ErrorDetail')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Manager Setups </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'ManagerSetup')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 6; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                            <th width="30%">
                                <?php echo __('Setup Date'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mgrSetups)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Manager Setups found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($mgrSetups as $mgrSetup): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrSetup['FileProccessingDetail']['filename']) ? $mgrSetup['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrSetup['FileProccessingDetail']['processing_counter']) ? $mgrSetup['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrSetup['FileProccessingDetail']['transaction_number']) ? $mgrSetup['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($mgrSetup['ManagerSetup']['datetime']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'ManagerSetup')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Manager Logs </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'ManagerLog')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 6; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="10%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Logon TIme'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Logoff TIme'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mgrLogs)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Manager Log found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($mgrLogs as $mgrLog): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrLog['FileProccessingDetail']['filename']) ? $mgrLog['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrLog['FileProccessingDetail']['processing_counter']) ? $mgrLog['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($mgrLog['FileProccessingDetail']['transaction_number']) ? $mgrLog['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($mgrLog['ManagerLog']['logon_datetime']); ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($mgrLog['ManagerLog']['logoff_datetime']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'ManagerLog')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Side Logs </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'SideLog')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 7; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="10%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Side Type'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Logon Time'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Logoff Time'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sideLogs)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Side Log found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($sideLogs as $sideLog): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($sideLog['FileProccessingDetail']['filename']) ? $sideLog['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($sideLog['FileProccessingDetail']['processing_counter']) ? $sideLog['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($sideLog['FileProccessingDetail']['transaction_number']) ? $sideLog['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo $sideLog['SideLog']['side_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($sideLog['SideLog']['logon_datetime']); ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($sideLog['SideLog']['logoff_datetime']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'SideLog')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Transactions </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'TransactionDetail')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 7; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="10%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Transaction Category'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Transaction Type'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Tranasaction Date'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Tranasactions found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($transaction['FileProccessingDetail']['filename']) ? $transaction['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($transaction['FileProccessingDetail']['processing_counter']) ? $transaction['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($transaction['FileProccessingDetail']['transaction_number']) ? $transaction['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo $transaction['TransactionCategory']['text']; ?>
                                    </td>
                                    <td>
                                        <?php echo $transaction['TransactionType']['text']; ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($transaction['TransactionDetail']['trans_datetime']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'TransactionDetail')); ?>
            </div>
        </div>


  </div>
  <div class="panel-flat col-md-6 col-sm-6">

        <div class="panel box box-primary">
            <div class="box-footer clearfix">
                <h4> Activity Reports </h4>
                <?php echo $this->element('paginationtop', array('paginateModel' => 'ActivityReport')); ?>
            </div>
            <div class="box-body table-responsive no-padding" style="height: 400px;overflow: auto !important">
                <?php
                $startNo = 1;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php $columns = 8; ?>
                            <th width="5%">
                                <?php
                                echo __('Sr. No.');
                                ?>
                            </th>
                            <th width="25%">
                                <?php echo __('File Name'); ?>
                            </th>
                            <th width="20%">
                                <?php echo __('Processing Counter'); ?>
                            </th>
                            <th width="10%">
                                <?php echo __('No. Of Transaction'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Transaction Date'); ?>
                            </th>
                            <th width="15%">
                                <?php echo __('Created On'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($activityReports)) { ?>
                            <tr>
                                <td colspan='<?php echo $columns ?>' class='text-warning'><?php echo __('No Activity Report found.') ?></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($activityReports as $activityReport): ?>
                                <tr>
                                    <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($activityReport['FileProccessingDetail']['filename']) ? $activityReport['FileProccessingDetail']['filename'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($activityReport['FileProccessingDetail']['processing_counter']) ? $activityReport['FileProccessingDetail']['processing_counter'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($activityReport['FileProccessingDetail']['transaction_number']) ? $activityReport['FileProccessingDetail']['transaction_number'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($activityReport['ActivityReport']['trans_datetime']); ?>
                                    </td>
                                    <td>
                                        <?php echo showdatetime($activityReport['ActivityReport']['created_date']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('multiplePagination', array('paginateModel' => 'ActivityReport')); ?>
            </div>
        </div>
    </div>





</div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("ClientReportSearchForm", ["clientReportBranchId", "clientReportStationId"]);
    });

    function getCompanyBranches(companyId)
    {
        if (typeof companyId != undefined && companyId != '') {
            loader('show');
            jQuery.ajax({
                url: BaseUrl + "company_branches/get_branches/" + companyId,
                type: 'post',
                success: function (response) {
                    loader('hide');
                    jQuery('#clientReportBranchId').html(response);
                },
                error: function (e) {
                    loader('hide');
                }
            });
        }

    }
    function getBranchStations(branchId)
    {
        if (typeof branchId != undefined && branchId != '') {


            loader('show');
            jQuery.ajax({
                url: BaseUrl + "company_branches/get_stations/" + branchId,
                type: 'post',
                data: {data: jQuery('#clientReportBranchId').val()},
                success: function (response) {
                    loader('hide');
                    jQuery('#clientReportStationId').html(response);
                },
                error: function (e) {
                    loader('hide');
                }
            });
        }
    }
    function getStationFiles(stationId)
    {
        if (typeof stationId != undefined && stationId != '') {
            loader('show');
            jQuery.ajax({
                url: BaseUrl + "company_branches/get_files/" + stationId,
                type: 'post',
                data: {data: jQuery('#clientReportStationId').val()},
                success: function (response) {
                    loader('hide');
                    jQuery('#clientReportFileId').html(response);
                },
                error: function (e) {
                    loader('hide');
                }
            });
        }
    }
</script>
