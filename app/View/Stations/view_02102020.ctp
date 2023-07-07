<?php
	$this->assign('pagetitle', __('Station Detail').' <small>'.__('Stations').'</small>');
	$this->Custom->addCrumb(__('Stations'),array('action'=>'index'));
	$this->Custom->addCrumb(__('Station Detail'));
	$this->start('top_links');
		echo $this->Html->link(__('Delete'), array('action' => 'delete', $station['Station']['id']), array('icon'=>'fa-trash-o','title' => __('Click here to delete this Station'),'class'=>'btn btn-danger','escape'=>false),__('Are you sure? You want to delete this Station?'));
		echo $this->Html->link(__('Edit'), array('action' => 'edit', $station['Station']['id']), array('icon'=>'fa-edit','title' => __('Click here to edit this Station'),'class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Add Station'),array('action'=>'add'),array('icon'=>'fa-plus','title' => 'Click here to add Station','class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Back'),array('action'=>'index'),array('icon'=>'fa-angle-double-left','class'=>'btn btn-default','escape'=>false));
	$this->end();
?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <div class="box-body">
            <dl class="dl-horizontal">
                		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($station['Station']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Company'); ?></dt>
		<dd>
			<?php echo $this->Html->link($station['Company']['id'], array('controller' => 'users', 'action' => 'view', $station['Company']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Branch'); ?></dt>
		<dd>
			<?php echo $this->Html->link($station['Branch']['name'], array('controller' => 'company_branches', 'action' => 'view', $station['Branch']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($station['Station']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('File Processed Count'); ?></dt>
		<dd>
			<?php echo h($station['Station']['file_processed_count']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Detail'); ?></dt>
		<dd>
			<?php echo h($station['Station']['detail']); ?>
			&nbsp;
		</dd>
            </dl>
        </div>
    </div>
</div>