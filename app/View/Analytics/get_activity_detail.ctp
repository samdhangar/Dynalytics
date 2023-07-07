<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <?php echo __('Bill Activity Detail'); ?>
    </h4>
</div>
<div class="modal-body table-responsive">
    <?php
    if (empty($bill)):
        echo __('Invalid Bill Activity');
    else:

        ?>
        <table class="table table-bordered">
            <tr>
                <td>
                    <?php echo __('Company Name :'); ?>
                </td>
                <td>
                    <?php echo isset($bill['FileProccessingDetail']['Company']['first_name']) ? $bill['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Branch Name :'); ?>
                </td>
                <td>
                    <?php echo isset($bill['FileProccessingDetail']['Branch']['name']) ? $bill['FileProccessingDetail']['Branch']['name'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('DynaCore Station ID:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['station']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Bill Type:'); ?>
                </td>
                <td>
                    <?php echo isset($bill['BillType']['bill_type'])?$bill['BillType']['bill_type']:''; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 100:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_100']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 50:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_50']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 20:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_20']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 10:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_10']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 5:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_5']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Denom 1:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['denom_1']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Coin:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['coin']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Total:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['total']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 1:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_1']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 2:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_2']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 3:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_3']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 4u:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_4u']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 4l:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_4l']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Cass 5:'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['cass_5']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Message :'); ?>
                </td>
                <td>
                    <?php echo $bill['BillsActivityReport']['message']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Activity Date :'); ?>
                </td>
                <td>
                    <?php echo showdatetime($bill['BillsActivityReport']['created_date']); ?>
                </td>
            </tr>
        </table>
    <?php
    endif;

    ?>
</div>
<div class="modal-footer clearfix">
</div>