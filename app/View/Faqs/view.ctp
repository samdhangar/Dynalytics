<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?php echo $this->Custom->cropDetail($faq['Faq']['question'], 60); ?></h4>
</div>
<div class="modal-body table-responsive">
    <table class="table table-bordered">
        <tr>
            <td >
                Question
            </td>
            <td >
                <?php echo $faq['Faq']['question']; ?>
            </td>
        </tr>
        <tr>
            <td >
                Answer
            </td>
            <td >
                <?php echo $faq['Faq']['answer']; ?>
            </td>
        </tr>
        <tr>
            <td  class="table-text">
                For (User role):
            </td>
            <td >
                <?php echo $faq['Faq']['user_role']; ?>
            </td>
        </tr>
        <tr>
            <td  class="table-text">
                Display order no:
            </td>
            <td >
                <?php echo $faq['Faq']['order_no']; ?>
            </td>
        </tr>
        <tr>
            <td  class="table-text">
                Status:
            </td>
            <td >
                <?php echo $faq['Faq']['status']; ?>
            </td>
        </tr>
    </table>
</div>

<div class="modal-footer">
										<button class="btn btn-default" data-dismiss="modal"><i class="icon-cross2 position-left"></i> Close</button>

									</div>
