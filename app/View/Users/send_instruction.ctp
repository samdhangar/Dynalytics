<style>
</style>
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
$pageTitle = 'Onboarding' . ' - ' . 'Support';
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
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <?php echo $this->Form->create('User', array('id' => 'UploadBranches', 'type' => 'file', 'action' => 'upload_branch', 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>
                <div class="box-body box-content">
                    <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
                    <div class="row no-margin">
                        <?php echo $this->Form->input('email', array('novalidate' => true, 'multiple' => 'multiple', 'id' => 'sendEmails')); ?>
                        <?php echo $this->Form->end(); ?>
                        <div class="form-action">
                            <button type="button" id="confirmbtn" class="btn btn-default">Confirm</button>
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
    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"> </script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#confirmbtn').on('click', function() {
            loader('show');
            var emails = $('#sendEmails').attr('value');
            var valid = validateEmailList(emails);
            if(valid == true){
                var formData = new FormData();
                formData.append('formData', emails);
                jQuery.ajax({
                type: "POST",
                url: BaseUrl + "/companies/send_instruction/",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loader('hide');
                    console.log(response);
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
                        window.location = BaseUrl + 'companies/complete_process';
                    }
                },
                error: function(e) {
                    loader('hide');
                }
            });
            }else{
                loader('hide');
                $(alertMessage).removeClass('alert-primary');
                $(alertMessage).addClass('bg-danger');
                $(defaultMsg).html("Warning!")
                $(almessage).html("Please Enter valid email.");
                $(".notification-message2").css("display", "block");
                removealertmessage();
            }
        });
    });
    function validateEmailList(emails) {
                var emails = emails.split(',')
                var valid = true;
                var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                for (var i = 0; i < emails.length; i++) {
                    if (emails[i] === "" || !regex.test(emails[i].replace(/\s/g, ""))) {
                        valid = false;
                    }
                }
                return valid;
            }
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