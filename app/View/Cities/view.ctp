<?php
	$this->assign('pagetitle', __('City Detail').' <small>'.__('Cities').'</small>');
	$this->Custom->addCrumb(__('Cities'),array('action'=>'index'));
	$this->Custom->addCrumb(__('City Detail'));
	$this->start('top_links');
		echo $this->Html->link(__('Delete'), array('action' => 'delete', $city['City']['id']), array('icon'=>'fa-trash-o','title' => __('Click here to delete this City'),'class'=>'btn btn-danger','escape'=>false),__('Are you sure? You want to delete this City?'));
		echo $this->Html->link(__('Edit'), array('action' => 'edit', $city['City']['id']), array('icon'=>'fa-edit','title' => __('Click here to edit this City'),'class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Add City'),array('action'=>'add'),array('icon'=>'fa-plus','title' => 'Click here to add City','class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Back'),array('action'=>'index'),array('icon'=>'fa-angle-double-left','class'=>'btn btn-default','escape'=>false));
	$this->end();
?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <div class="box-body">
            <dl class="dl-horizontal">
                		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($city['City']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country'); ?></dt>
		<dd>
			<?php echo $this->Html->link($city['Country']['name'], array('controller' => 'countries', 'action' => 'view', $city['Country']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('State'); ?></dt>
		<dd>
			<?php echo $this->Html->link($city['State']['name'], array('controller' => 'states', 'action' => 'view', $city['State']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('City Code'); ?></dt>
		<dd>
			<?php echo h($city['City']['city_code']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($city['City']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo $this->Custom->showStatus($city['City']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($city['City']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Updated'); ?></dt>
		<dd>
			<?php echo h($city['City']['updated']); ?>
			&nbsp;
		</dd>
            </dl>
        </div>
    </div>
</div>