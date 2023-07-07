<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">
        <?php echo __('Transaction Detail'); ?>
    </h4>
</div>
<div class="modal-body table-responsive">
    <?php
    if (empty($transaction)):
        echo __('Invalid Transaction');
    else:

        ?>
        <table class="table table-bordered">
            <tr>
                <td>
                    <?php echo __('Message :'); ?>
                </td>
                <td>
                    <?php echo $transaction['TransactionDetail']['messages']; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Error Message :'); ?>
                </td>
                <td>
                    <?php echo $transaction['TransactionDetail']['error_messages']; ?>
                </td>
            </tr>
        </table>
    <?php
    endif;

    ?>
</div>
<div class="modal-footer clearfix">
</div>