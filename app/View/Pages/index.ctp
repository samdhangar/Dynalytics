<?php
$this->assign('pagetitle', __('Page Management'));
$this->Custom->addCrumb('Page Management');
$this->start('top_links');
    echo $this->Html->link(__('Add Page'), array('action' => 'add'), array('icon' => 'add', 'title' => __('Add New Page'), 'class' => 'btn btn-primary', 'escape' => false));
$this->end();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('Page',array('id'=>'PageSearchForm','inputDefaults'=>array('data-toggle'=>'tooltip','class'=>'form-control','div'=>array('class'=>'col-md-2')),'novalidate'=>'novalidate'));
                        echo $this->Form->input('url',array('placeholder'=>__('Page Url'),'title'=>__('Page Url'),'label'=>false));
                        echo $this->Form->input('name',array('placeholder'=>__('Page Name'),'title'=>__('Page Name'),'label'=>false));
                        echo $this->Form->input('title',array('placeholder'=>__('Page Title'),'title'=>__('Page Title'),'label'=>false));
                        echo $this->Form->input('status',array('empty'=>'Page Status','title'=>'Page Status','label'=>false,'options'=>array('active'=>'Active','inactive'=>'Inactive'),'class'=>'form-control','div'=>array('class'=>'col-md-2')));
                    ?>
                        <div class="col-md-4">
                            <?php
                                echo $this->Form->submit('Search',array('class'=>'btn btn-primary margin-right10','div'=>false));
                                echo $this->Html->link("View All Pages",array('controller'=>'pages','action'=>'index','all'),array('title'=>'Display the all the users','class'=>'btn btn-default'));
                            ?>
                        </div>
                    <?php echo $this->Form->end();?>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-footer clearfix">
                <?php echo $this->element('paginationtop'); ?>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo $this->Paginator->sort('url'); ?></th>
                            <th><?php echo $this->Paginator->sort('name'); ?></th>
                            <th><?php echo $this->Paginator->sort('title'); ?></th>
                            <th><?php echo $this->Paginator->sort('meta_title'); ?></th>
                            <th><?php echo $this->Paginator->sort('meta_keyword'); ?></th>
                            <th><?php echo $this->Paginator->sort('status'); ?></th>
                            <th><?php echo $this->Paginator->sort('created',__('Added On')); ?></th>
                            <th class="actions text-center"><a><?php echo __('Actions'); ?></a></th>
                        </tr>
                    </thead>
                    <?php if (empty($pages)) { ?>
                        <tr>
                            <td colspan="7">
                                <?php echo __("No Page Found"); ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($pages as $key => $page) { ?>
                            <tr>
                                <td><?php echo $page['Page']['url']; ?></td>
                                <td><?php echo $page['Page']['name']; ?></td>
                                <td><?php echo $page['Page']['title']; ?></td>
                                <td><?php echo $page['Page']['meta_title']; ?></td>
                                <td><?php echo $page['Page']['meta_keyword']; ?></td>
                                <td><?php echo ucwords($page['Page']['status']); ?></td>
                                <td><?php echo showdatetime($page['Page']['created']); ?></td>
                                <td class='actions text-center'>
                                    <?php
                                        echo $this->Html->link('', array('action' => 'view', $page['Page']['url'],'admin'=>false), array('icon'=>'view','target'=>'_blank','title' => 'Click here to view this page'));
                                        echo $this->Html->link('', array('action' => 'edit', $page['Page']['id']), array('icon'=>'edit','title' => 'Click here to edit this page'));
                                        echo $this->Html->link('', array('action' => 'delete', $page['Page']['id']), array('icon'=>'delete','title' => 'Click here to delete this age'),'Do you want to really delete this page ?');
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
       jQuery("#PageSearchFormmm").validate({
            rules: {
                "data[Page][name]": {
                    required:false,
                },
                "data[Page][price]": {
                    required:false,
                    digits:true
                },
                "data[Page][interval]": {
                    required:false,
                },
                "data[Page][space]": {
                    required:false,
                    digits:true
                },
                "data[Page][status]": {
                    required:false,
                },
            },
            messages: {
                "data[Page][name]": {
                    required:"false",
                },
                "data[Page][price]": {
                    required:"false",
                    digits:"Please enter digits only"
                },
                "data[Page][interval]": {
                    required:"false",
                },
                "data[Page][space]": {
                    required:"false",
                    digits:"Please enter digits only"
                },
                "data[Page][status]": {
                    required:"false",
                },
            }
        });
    });
</script>