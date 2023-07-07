<label class="assignBranchLbl">Assign Branch </label>
<?php
foreach ($branchList as $branchId => $branchName):
    echo $this->Form->input('User.admins.' . $branchId, array('hiddenField' => false, 'type' => 'checkbox', 'label' => $branchName, 'class' => 'chkbox', 'title' => __('Click here to assign this branch to company'), 'div' => array('class' => 'checkboxDiv')));
endforeach;

?>