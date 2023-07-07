<?php

$pageTitle = __('Upload DynaCore Stations');
$breadCrumb = array(
    array(
        'title' => __('Upload DynaCore Stations'),
        'link' => ''
    )
);
// if (!empty($parentId) && !empty($parentDetails)) {
$pageTitle = 'Upload DynaCore Stations';
$breadCrumb[0]['title'] = $pageTitle;
$breadCrumb[1] = $breadCrumb[0];
$breadCrumb[0] = array(
    'title' => 'DynaCore Stations',
    'link' => Router::url(array('controller' => 'stations', 'action' => 'index'), true)
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
    // echo $this->Html->link(__('Download Sample CSV File'), array('action' => 'downloadSamplefile',base64_encode("Branch_Sample")), array('class' => 'btn btn-sm btn-success btn'));
$this->end();
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <?php echo $this->Form->create('DynacoreStationFile', array('id' => 'DynacoreStationFiles', 'type' => 'file', ['controllers' => 'station','action' => 'upload_station'], 'inputDefaults' => array('dir' => 'ltl', 'class' => 'form-control', 'div' => array('class' => 'required form-group')))); ?>
                <div class="box-body box-content">
                    <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
                    <div class="row no-margin">
                        <?php echo $this->Form->input('name', array('label' => __('Upload a file'), 'id' => 'name', 'type' => 'file', 'div' => array('class' => 'form-group required')));  ?>
                        <?php echo $this->Form->end(); ?>
                        <div class="form-action">
                            <?php echo $this->Form->submit(__('Submit'), array('action' => 'upload_stations','class' => 'btn btn-primary margin-right10', 'div' => false)); ?>
                            &nbsp;&nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>