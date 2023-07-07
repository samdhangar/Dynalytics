<style>
</style>
<?php
$formParamter = '';
$startNo = (int) $this->Paginator->counter('{:start}');
$analyticParams = isset($this->params['named']['type']) ? 'type:' . $this->params['named']['type'] : '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
$this->assign('pagetitle', __('Add Bulk Station'));
$count_amount_flag = 0;
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Add Bulk Station'));
} else {
    $this->Custom->addCrumb(__('Add Bulk Station'));
}
$this->start('top_links');
    echo $this->Html->link(__('Download Sample CSV File'), array('action' => 'downloadSamplefile',base64_encode("Station_Sample")), array('class' => 'btn btn-sm btn-success btn', 'full_base' => true));
$this->end();
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <?php echo $this->Form->create('User', array('id' => 'UploadBranches', 'type' => 'file', 'action' => 'upload_branch', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>
                <div class="box-body box-content">
                    <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
                    <div class="row no-margin">
                        <?php echo $this->Form->input('bulk_branch', array('label' => __('Upload a file'), 'id' => 'bulk_branch', 'type' => 'file', 'div' => array('class' => 'form-group required')));  ?>
                        <?php echo $this->Form->end(); ?>
                        <div class="form-action">
                            <!-- <?php echo $this->Form->submit(__('Next'), array('action' => 'upload_stations','class' => 'btn btn-primary margin-right10', 'div' => false)); ?> -->
                            &nbsp;&nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-flat" id="rowhide">
    <div class="panel-body">
        <div class="table-responsive">
            <div id="dvCSV">

            </div>
        </div>
        <div style="margin-top: 5px;">
            <button type="button" id="confirmbtn" class="btn btn-default">Confirm</button>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
    // $(document).ready(function(){
    jQuery(document).ready(function() {
        jQuery('#confirmbtn').on('click', function() {
            loader('show');
            var file = $("#bulk_branch").prop('files');
            var file = file[0];
            var formData = new FormData();
            formData.append('formData', file);
            jQuery.ajax({
                type: "POST",
                url: BaseUrl + "/users/bulk_station/",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loader('hide');
                    var response1 = JSON.parse(response);
                    console.log(response1.message);
                    if(response1.status == 'fail'){
                        $(alertMessage).removeClass('alert-primary');
                        $(alertMessage).addClass('bg-danger');
                        $(defaultMsg).html("Warning!")
                        $(almessage).html(response1.message);
                        $(".notification-message2").css("display", "block");
                         removealertmessage();
                    }else{
                        $(almessage).html(response1.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                        window.location = BaseUrl + 'users/completed_bulk';
                    }
                },
                error: function(e) {
                    loader('hide');
                }
            });

        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#rowhide").hide();
        $("#bulk_branch").bind("change", function() {
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.xlsx)$/;
            if (regex.test($("#bulk_branch").val().toLowerCase())) {
                if (typeof(FileReader) != "undefined") {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var table = $("<table class = 'table table-hover table-bordered' />");
                        var rows = e.target.result.split("\n");
                        for (var i = 0; i < rows.length; i++) {
                            var row = $("<tr />");
                            var cells = rows[i].split(",");
                            if (cells.length > 1) {
                                for (var j = 0; j < cells.length; j++) {
                                    if (i == 0) {
                                        var cell = $("<th />");
                                    } else {
                                        var cell = $("<td />");

                                    }
                                    cell.html(cells[j]);
                                    row.append(cell);
                                }
                                table.append(row);
                            }
                        }
                        $("#rowhide").show();
                        $("#dvCSV").html('');
                        $("#dvCSV").append(table);
                    }
                    reader.readAsText($("#bulk_branch")[0].files[0]);
                } else {
                    alert("This browser does not support HTML5.");
                }
            } else {
                    $(alertMessage).removeClass('alert-primary');
                    $(alertMessage).addClass('bg-danger');
                    $(defaultMsg).html("Warning!")
                    $(almessage).html("Please upload a valid file.");
                    $(".notification-message2").css("display", "block");
                    removealertmessage();

            }
        });
    });
</script>