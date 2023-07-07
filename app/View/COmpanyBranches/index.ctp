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
echo $this->Html->link(__('Add Branch'), array('action' => 'add', encrypt($parentId)), array('icon' => 'cc icon-plus3 add', 'title' => __('Add Company Branch'), 'class' => 'btn btn-success', 'escape' => false));
echo $this->Html->link(__('Export Branch'), array('action' => 'export', encrypt($parentId)), array('icon' => 'icon-download', 'title' => __('Export Branch'), 'class' => 'btn btn-primary marginleft', 'escape' => false));
$this->end();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('CompanyBranch', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));
                    echo $this->Form->input('name', array('label' => __('Branch Name'), 'placeholder' => __('Branch Name/ Contact Name'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    // echo $this->Form->input('email', array('label' => __('Email'), 'placeholder' => __('email'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                   
                    echo $this->Form->input('regiones', array('id' => 'analyRegionId', 'label' => __('Region: '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));

                     echo $this->Form->input('branch_status', array('label' => __('Status'), 'required' => false, 'empty' => __('Select status '), 'options' => array('active' => __('Active'), 'inactive' => __('Inactive')), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                    ?>

                    <!-- <label>&nbsp</label> -->
                    <div class="col-md-3 form-group">
                        <?php
                         echo "<label for='analyBranchId' >&nbsp;</label><br>";
                        echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                        echo $this->Html->link(__('Reset Search'), array('action' => 'index', 'all', $formParamter), array('title' => __('reset search'), 'class' => 'btn btn-default'));
                        ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary">           
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
                        echo $this->Form->input('delete.all', array('hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'checkAll'));
                        ?> 
                    </th>
                    <th>
                        <?php
                        echo __('Sr. No.');
                        ?>
                    </th>
                    <?php
                    if (empty($parentId)):
                        $colCount++;
                        ?>
                                                                            <!--<th><?php echo $this->Paginator->sort('company_id', __('User Name')); ?></th>-->

                    <?php endif; ?>

                    <th><?php echo $this->Paginator->sort('name', __('Region Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Branch Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('contact_name', __('Contact Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('city'); ?></th>
                    <th><?php echo $this->Paginator->sort('state'); ?></th>
                    <!--<th><?php echo $this->Paginator->sort('country'); ?></th>-->
                    <!-- <th><?php echo $this->Paginator->sort('email'); ?></th> -->
                    <th><?php echo $this->Paginator->sort('phone'); ?></th>
                    <th><?php echo $this->Paginator->sort('branch_status'); ?></th>
                    <th><?php echo $this->Paginator->sort('created', __('Added On')); ?></th>
                    <th class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>			
            </thead>		
            <tbody>
                <?php if (empty($companyBranches)) { ?>
                    <tr>
                        <td colspan='<?php echo $colCount; ?>' class='text-warning'><?php echo __('No Company Branch found.') ?></td>
                    </tr>
                <?php } else { ?>

                    <?php foreach ($companyBranches as $companyBranch): ?>
                       
                        <tr class="<?php echo (($companyBranch['CompanyBranch']['is_list_display'] == 0) ? ' bg-primary ' : ''); ?>">
                            <td>
                                <?php 
                                echo $this->Form->input('delete.id.', array('value' => encrypt($companyBranch['CompanyBranch']['id']), 'hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'deleteRow', 'required' => false));
                                ?> 
                            </td>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <?php if (empty($parentId)): ?>
                                                            <!--                                <td>
                                <?php echo $this->Html->link($companyBranch['Admin']['first_name'], array('controller' => 'companies', 'action' => 'view', encrypt($companyBranch['Company']['id'])), array('title' => __('Click here to view this Company'))); ?>
                                                                                    </td>-->
                            <?php endif; ?>
                             <td class="table-text">
                                <?php echo $companyBranch['Region']['name']; ?>
                                 
                            </td>
                            <td class="table-text">
                                <?php 
                                 echo $this->Html->link($companyBranch['CompanyBranch']['name'], array('controller' => 'company_branches', 'action' => 'view', encrypt($companyBranch['CompanyBranch']['id']), $formParamter), array('title' => __('Click here to view this Company Branch'))); ?>
                            </td>
                           
                            <td class="table-text"><?php echo $companyBranch['CompanyBranch']['contact_name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['City']['name']; ?></td>
                            <td class="table-text"><?php echo $companyBranch['State']['name']; ?></td>
                            <!--<td class="table-text"><?php echo $companyBranch['Country']['name']; ?></td>-->
                            <!-- <td class="table-text"><?php echo $companyBranch['CompanyBranch']['email']; ?></td> -->
                            <td><?php echo $companyBranch['CompanyBranch']['phone']; ?></td>
                            <td class="table-text">
                                <?php
                                echo $this->Custom->getToggleButton($companyBranch['CompanyBranch']['branch_status'], 'userStatusChange', array('data-uid' => $companyBranch['CompanyBranch']['id'], 'data-id' => 'userStatus_' . $companyBranch['CompanyBranch']['id']));
                                ?>
                            </td>
                            <td><?php echo showdate($companyBranch['CompanyBranch']['created']); ?></td>
                            <td class="actions text-center">

                                <?php echo $this->Html->link(__(''), array('action' => 'edit', encrypt($companyBranch['CompanyBranch']['id']),$formParamter), array('icon' => 'icon-pencil5 edit', 'class' => 'no-hover-text-decoration', 'title' => __('Click here to edit this Company Branch'))); ?>
                                <?php echo $this->Html->link(__(''), array('action' => 'delete', encrypt($companyBranch['CompanyBranch']['id']),$formParamter), array('icon' => 'icon-trash delete', 'class' => 'no-hover-text-decoration', 'class' => 'no-hover-text-decoration', 'title' => __('Click here to delete this Company Branch')), __('Are you sure you want to delete Company Branch?')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>			
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <div class="deleteMultiple">
            <?php
            echo $this->Html->image('arrow_ltr.png');
            echo '<div>' . __(' With selected ');
            echo $this->Form->submit(__('Delete'), array('icon' => 'fa-trash', 'class' => 'btn btn-danger btn-xs disabled', 'id' => 'DeleteBtn'));
            echo '</div>';
            ?>
        </div>
        <?php echo $this->element('pagination'); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("CompanyBranchIndexForm", ["CompanyBranchName", "CompanyBranchEmail", "CompanyBranchBranchStatus"]);
        jQuery('.userStatusChange').on('click', function () {
            
            var status = ($(this).hasClass('off')) ? 'active' : 'inactive';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure you want to change status as ') ?>' + status + ' ?')) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'active' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        // alert(response.message);
                        $(almessage).html(response.message);
                        $(".notification-message2").css("display", "block");
                        removealertmessage();
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });
    });
</script>