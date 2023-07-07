<style>
</style>
<?php
$formParamter = '';
$startNo = (int) $this->Paginator->counter('{:start}');
$analyticParams = isset($this->params['named']['type']) ? 'type:' . $this->params['named']['type'] : '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
$this->assign('pagetitle', __('Complete process of add Bulk data'));
$count_amount_flag = 0;
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Complete process of add Bulk data'));
} else {
    $this->Custom->addCrumb(__('Complete process of add Bulk data'));
}
?>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="panel panel-flat">
            <div class="panel-body">
                <h1>Bulk Data Added Successfully !!</h1>
                <table class="table table-hover table-bordered" style="width: 60% !important;">
                    <thead>
                        <tr>
                            <td>Total No of Users:</td>
                            <td><?php echo str_pad($users_count, 2, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                        <tr>
                            <td>Total No of Regions:</td>
                            <td><?php echo str_pad($region_count, 2, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                        <tr>
                            <td >Total No of Branchs:</td>
                            <td><?php echo str_pad($branches_count, 2, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                        <tr>
                            <td>Total No of Stations:</td>
                            <td><?php echo str_pad($station_count,2, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                    </thead>
                </table>
                <div class="form-action">
                    <?php echo $this->Html->link(__('Complete'), array('controller'=> 'users','action' => 'dashboard'), array('class' => 'btn btn-default','style' => 'margin-top:15px')); ?>
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
                url: BaseUrl + "/companies/upload_users/",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loader('hide');
                    var response1 = JSON.parse(response);
                    console.log(response1.message);
                    if (response1.status == 'fail') {
                        $(alertMessage).removeClass('alert-primary');
                        $(alertMessage).addClass('bg-danger');
                        $(defaultMsg).html("Warning!")
                        $(almessage).html(response1.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                    } else {
                        $(almessage).html(response1.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                        window.location = BaseUrl + 'companies/';
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