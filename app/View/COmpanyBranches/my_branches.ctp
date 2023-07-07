<?php
/**
 * get request named paramater
 */
$formParamter = '';
if (!empty($this->params['named'])) {
    $formParamter = getNamedParameter($this->params['named']);
}
/**
 * display page title
 */
$pageTitle = __('Branches');
$breadCrumb = array(
    array(
        'title' => __('Branches'),
        'link' => ''
    )
);
if (!empty($parentId) && !empty($parentDetails)) {
    $pageTitle = $parentDetails['first_name'] . ' - ' . $pageTitle;
    $breadCrumb[0]['title'] = $pageTitle;
    $breadCrumb[1] = $breadCrumb[0];
    $breadCrumb[0] = array(
        'title' => COMPANY,
        'link' => Router::url(array('controller' => 'companies', 'action' => 'index'), true)
    );
}
$this->assign('pagetitle', __($pageTitle));
/**
 * display breadcrumbs
 */
foreach ($breadCrumb as $breadCrum):
    $this->Custom->addCrumb(__($breadCrum['title']), $breadCrum['link']);

endforeach;


$this->start('top_links');
echo $this->Html->link(__('Back'), $this->request->referer(), array('icon' => 'back', 'title' => __('Click here to go back'), 'class' => 'btn btn-default', 'escape' => false));
$this->end();
?>
<div class="panel panel-flat" style="float:left; width:100%;">
    <div class="panel-body col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('CompanyBranch', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));
                    echo $this->Form->input('name', array('label' => __('Name'), 'placeholder' => __('Branch Name/ Contact Name'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    echo $this->Form->input('email', array('label' => __('Email'), 'placeholder' => __('email'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    echo $this->Form->input('branch_status', array('label' => __('Status'), 'required' => false, 'empty' => __('Select status '), 'options' => array('active' => __('Active'), 'inactive' => __('Inactive')), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    ?>

                    <label>&nbsp</label>
                    <div class="col-md-3 form-group">
                        <?php
                        echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                        echo $this->Html->link(__('Reset Search'), array('action' => 'my_branches', 'all', $formParamter), array('title' => __('reset search'), 'class' => 'btn btn-default marginleft'));
                        ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <?php if (!empty($parentId)): ?>
            <div class="parentNameDiv">
                <label><?php echo COMPANY; ?>&nbsp;:&nbsp;</label>
                <?php echo $parentDetails['first_name']; ?>
            </div>
        <?php endif; ?>
        <?php echo $this->element('paginationtop'); ?>
    </div>
    <?php echo $this->Form->create('Country', array('class' => 'deleteAllForm', 'url' => array('controller' => $this->params['controller'], 'action' => 'delete'), 'id' => 'UserEditProfileForm', 'data-confirm' => __('Are you sure you want to delete selected Branch ?'))); ?>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $colCount = 12; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');
                        ?>
                    </th>
                    <?php
                    if (empty($parentId)):
                        $colCount++;
                        ?>

                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('name', __('Branch Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('contact_name', __('Contact Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('city'); ?></th>
                    <th><?php echo $this->Paginator->sort('state'); ?></th>
                    <th><?php echo $this->Paginator->sort('country'); ?></th>
                    <th><?php echo $this->Paginator->sort('email'); ?></th>
                    <th><?php echo $this->Paginator->sort('phone'); ?></th>
                    <th><?php echo $this->Paginator->sort('branch_status'); ?></th>
                    <th><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($companyBranches)) { ?>
                    <tr>
                        <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No Company Branch found.') ?></td>
                    </tr>
                <?php } else { ?>

                    <?php foreach ($companyBranches as $companyBranch): ?>
                        <tr>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link($companyBranch['CompanyBranch']['name'], array('controller' => 'company_branches', 'action' => 'view', encrypt($companyBranch['CompanyBranch']['id']), $formParamter), array('title' => __('Click here to view this Company Branch'))); ?>
                            </td>
                            <td class="table-text"><?php echo $companyBranch['CompanyBranch']['contact_name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['City']['name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['State']['name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['Country']['name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['CompanyBranch']['email']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['CompanyBranch']['phone']; ?></td>
                            <td class="table-text">
                                <?php
                                $class = '';
                                if ($companyBranch['CompanyBranch']['branch_status'] == 'active'):
                                    $class = 'label-success';
                                elseif ($companyBranch['CompanyBranch']['branch_status'] == 'inactive'):
                                    $class = 'label-danger';
                                endif;
                                ?>
                                <label class="label <?php echo $class; ?>"><?php echo ucfirst($companyBranch['CompanyBranch']['branch_status']); ?></label>
                            </td>
                            <td><?php echo showdate($companyBranch['CompanyBranch']['created']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
