<?php 
echo $this->Html->script('ckeditor/ckeditor');            
$this->assign('pagetitle', __('%s Page',$title)); 
$this->Custom->addCrumb(__('Page Management'),array('action'=>'index'));
$this->Custom->addCrumb(__('%s Page',$title));
$this->start('top_links');
    echo $this->Html->link(__('Back'),array('action'=>'index'),array('icon'=>'back','class'=>'btn btn-default','escape'=>false));
$this->end();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php
                echo $this->Form->create('Page', array('id'=>'PageAddEditForm','novalidate'=>'novalidate','inputDefaults'=>array('class' => 'form-control', 'div' => array('class' => 'form-group')),'class'=>'form-with-action'));
                echo $this->Form->input('id', array('type'=>'hidden'));
                echo $this->Form->input('name', array('label'=>'Page Name','placeholder' => 'Page name', ));
                echo $this->Form->input('title', array('label'=>'Page Title','placeholder' => 'Page title'));
                echo $this->Form->input('url', array('label'=>'Page Url','placeholder' => 'Page url', 'class' => 'form-control','between'=>'<div class="input-group form-group"><span class="input-group-addon">'.DEFAULT_PAGES.'</span>','after'=>'</div><div class="error" generated="true" for="PageUrl" style="margin-bottom:10px;margin-top:-10px"></div>'));
                echo $this->Form->input('body', array('label'=>'Contant','placeholder' => 'Contant', 'class' => 'editor form-control'));
                echo $this->Ck->load('PageBody','full','300px');
                echo $this->Form->input('meta_keyword', array('label'=>'Meta Keyword','placeholder' => 'Meta Keyword'));
                echo $this->Form->input('meta_description', array('label'=>'Meta Description','placeholder' => 'Meta Description'));
                echo $this->Form->input('status', array('label'=>'Page Status','placeholder' => 'Page Status','options'=>array('active'=>'Active','inactive'=>'Inactive'),'empty'=>'Select Status'));
                ?>
                <div class='form-action'>
                    <?php 
                        echo $this->Form->submit('Save', array('class' => 'btn btn-primary', 'div' => false));
                        echo '&nbsp;&nbsp;';
                        echo $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default'));
                    ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
       jQuery("#PageAddEditForm").validate({
            rules: {
                "data[Page][name]": {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                "data[Page][url]": {
                    required: true,
                    minlength: 3,
                    maxlength: 40
                },
                "data[Page][title]": {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                "data[Page][meta_title]": {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                "data[Page][meta_keyword]": {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                "data[Page][meta_description]": {
                    required: true,
                    minlength: 3,
                    maxlength: 40
                },
                "data[Page][body]": {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                "data[Page][name]": {
                    required: "Please enter page name.",
                    minlength: "Please enter page name between 3 to 30 characters.",
                    maxlength: "Please enter page name between 3 to 30 characters."
                },
                "data[Page][url]": {
                    required: "Please enter page url.",
                    minlength: "Please enter page url between 3 to 40 characters.",
                    maxlength: "Please enter page url between 3 to 40 characters."
                },
                "data[Page][title]": {
                    required: "Please enter page title.",
                    minlength: "Please enter page title between 3 to 30 characters.",
                    maxlength: "Please enter page title between 3 to 30 characters."
                },
                "data[Page][meta_title]": {
                    required: "Please enter page meta title.",
                    minlength: "Please enter page meta title between 3 to 30 characters.",
                    maxlength: "Please enter page meta title between 3 to 30 characters."
                },
                "data[Page][meta_keyword]": {
                    required: "Please enter page meta keyword.",
                    minlength: "Please enter page meta keyword between 3 to 30 characters.",
                    maxlength: "Please enter page meta keyword between 3 to 30 characters."
                },
                "data[Page][meta_description]": {
                    required: "Please enter page meta description.",
                    minlength: "Please enter page meta description between 3 to 40 characters.",
                    maxlength: "Please enter page meta description between 3 to 40 characters."
                },
                "data[Page][body]": {
                    required: "Please enter page body.",
                    minlength: "Please enter page description at least 15 characters.",
                }
            }
        });
    });
</script>