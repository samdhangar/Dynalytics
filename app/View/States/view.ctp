<?php
$this->assign('pagetitle', __('State Detail') . ' <small>' . __('States') . '</small>');
$this->Custom->addCrumb(__('States'), array('action' => 'index'));
$this->Custom->addCrumb(__('State Detail'));
$this->start('top_links');
echo $this->Html->link(__('Delete'), array('action' => 'delete', $state['State']['id']), array('icon' => 'fa-trash-o', 'title' => __('Click here to delete this State'), 'class' => 'btn btn-danger', 'escape' => false), __('Are you sure? You want to delete this State?'));
echo $this->Html->link(__('Edit'), array('action' => 'edit', $state['State']['id']), array('icon' => 'fa-edit', 'title' => __('Click here to edit this State'), 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->link(__('Add State'), array('action' => 'add'), array('icon' => 'fa-plus', 'title' => 'Click here to add State', 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->link(__('Back'), array('action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default', 'escape' => false));
$this->end();
 
?>
<div class="box box-primary">
    <div class="overflow-hide-break">
        <div class="box-body">
            <dl class="dl-horizontal">
                <dt><?php echo __('Id'); ?></dt>
                <dd>
                    <?php echo h($state['State']['id']); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('Country'); ?></dt>
                <dd>
                    <?php echo $this->Html->link($state['Country']['name'], array('controller' => 'countries', 'action' => 'view', $state['Country']['id'])); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('State Code'); ?></dt>
                <dd>
                    <?php echo h($state['State']['state_code']); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('Name'); ?></dt>
                <dd>
                    <?php echo h($state['State']['name']); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('Status'); ?></dt>
                <dd>
                    <?php echo $this->Custom->showStatus($state['State']['status']); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('Created'); ?></dt>
                <dd>
                    <?php echo h($state['State']['created']); ?>
                    &nbsp;
                </dd>
                <dt><?php echo __('Updated'); ?></dt>
                <dd>
                    <?php echo h($state['State']['updated']); ?>
                    &nbsp;
                </dd>
            </dl>
        </div>
    </div>
</div>