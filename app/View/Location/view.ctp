<?php
	$this->assign('pagetitle', __('Country Detail').' <small>'.__('Countries').'</small>');
	$this->Custom->addCrumb(__('Countries'),array('action'=>'index'));
	$this->Custom->addCrumb(__('Country Detail'));
	$this->start('top_links');
		echo $this->Html->link(__('Delete'), array('action' => 'delete', $country['Country']['id']), array('icon'=>'fa-trash-o','title' => __('Click here to delete this Country'),'class'=>'btn btn-danger','escape'=>false),__('Are you sure? You want to delete this Country?'));
		echo $this->Html->link(__('Edit'), array('action' => 'edit', $country['Country']['id']), array('icon'=>'fa-edit','title' => __('Click here to edit this Country'),'class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Add Country'),array('action'=>'add'),array('icon'=>'fa-plus','title' => 'Click here to add Country','class'=>'btn btn-primary','escape'=>false));
		echo $this->Html->link(__('Back'),array('action'=>'index'),array('icon'=>'fa-angle-double-left','class'=>'btn btn-default','escape'=>false));
	$this->end();
?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <div class="box-body">
            <dl class="dl-horizontal">
                		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($country['Country']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($country['Country']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country Code'); ?></dt>
		<dd>
			<?php echo h($country['Country']['country_code']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency Symbol'); ?></dt>
		<dd>
			<?php echo h($country['Country']['currency_symbol']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo $this->Custom->showStatus($country['Country']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($country['Country']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Updated'); ?></dt>
		<dd>
			<?php echo h($country['Country']['updated']); ?>
			&nbsp;
		</dd>
            </dl>
        </div>
    </div>
</div>