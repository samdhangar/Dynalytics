<?php echo $this->Form->create('CompanyBranch', array('inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'form-group')))); ?>
<div class="form-box" id="login-box" style="margin-top: 0">
    <div class="header bg-danger"><?php echo __("Accept Branch") ?></div>	
    <div class="body bg-gray">			
        <label>Branch Name:</label> <label><?php echo $brnachDealerData['Branch']['name'] ?></label> <br/>
        <label>Company Name:</label> <label><?php echo $brnachDealerData['Branch']['Company']['name'] ?></label> <br/>
        <?php echo $this->Form->hidden('decision'); ?>
        <?php echo $this->Form->input('note', array('label' => 'Note<span style="color:red">*</span>', 'type' => 'textarea', 'escape' => false, 'div' => array('id' => 'note', 'class' => 'form-group'))); ?>
    </div>
    <div class="footer text-center bg-gray" style="overflow: hidden;">
        <div class="col-md-6">
            <?php echo $this->Form->submit('Accept ', array('class' => 'btn btn-block btn-success', 'id' => 'accept', 'escape' => false, 'div' => false)); ?>
        </div>
        <div class="col-md-6">
            <?php echo $this->Form->submit('Reject ', array('class' => 'btn btn-block btn-danger', 'id' => 'reject', 'escape' => false, 'div' => false)); ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#note').css('display', 'none');
        $('#CompanyBranchNote').val('');
        $('#reject').on('click', function (e) {
            $('#note').slideDown();
            $('#CompanyBranchDecision').val(0);
            if ($('#CompanyBranchNote').val().length <= 0) {
                e.preventDefault();
            }
        });
        $('#accept').on('click', function (e) {
            $('#note').slideUp();
            $('#CompanyBranchDecision').val(1);
        });
    });
</script>