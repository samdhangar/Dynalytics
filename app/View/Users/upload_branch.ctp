<?php
$formParamter = '';
$startNo = (int) $this->Paginator->counter('{:start}');
$analyticParams = isset($this->params['named']['type']) ? 'type:' . $this->params['named']['type'] : '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
$pageTitle = __('Financial Institue');
$breadCrumb = array(
    array(
        'title' => __('Financial Institue'),
        'link' => ''
    )
);
// if (!empty($parentId) && !empty($parentDetails)) {
$pageTitle = 'Onboarding Setup' . ' - ' . 'Branches';
$breadCrumb[0]['title'] = $pageTitle;
$breadCrumb[1] = $breadCrumb[0];
$breadCrumb[0] = array(
    'title' => 'Financial Institue',
    'link' => Router::url(array('controller' => 'companies', 'action' => 'index'), true)
);
// }
$this->assign('pagetitle', __($pageTitle));
/**
 * display breadcrumbs
 */
foreach ($breadCrumb as $breadCrum) :
    $this->Custom->addCrumb(__($breadCrum['title']), $breadCrum['link']);
endforeach;

$this->start('top_links');
    echo $this->Html->link(__('Download Sample CSV File'), array('action' => 'downloadSamplefile',base64_encode("Branch_Sample")), array('class' => 'btn btn-sm btn-success btn'));
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
                            <?php echo $this->Html->link(__('Skip'), array('action' => 'upload_stations',), array('class' => 'btn btn-default')); ?>
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
<script>
    jQuery(document).ready(function() {
        jQuery('#confirmbtn').on('click', function() {
            loader('show');
            var file = $("#bulk_branch").prop('files');
            var file = file[0];
            var formData = new FormData();
            formData.append('formData', file);
            jQuery.ajax({
                type: "POST",
                url: BaseUrl + "/companies/upload_branch/",
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
                        window.location = BaseUrl + 'companies/upload_stations/';
                    }
                },
                error: function(e) {
                    loader('hide');
                }
            });

        });
    });
   
</script>