<div class="col-md-6">
    <?php
    $label_first = 'Name';
    $place_holder_first = 'Enter Name';
    echo $this->Form->input('first_name', array('placeholder' => $place_holder_first, 'label' => $label_first));
    echo $this->Form->input('phone_no', array('required' => false,'placeholder' => 'Phone No', 'class' => 'form-control phoneNumber'));

    if ($from == 'Edit' && $this->request->data['User']['role'] != DEALER) {
        echo $this->Form->input('is_pwd_change', array('hiddenField' => false, 'type' => 'checkbox', 'label' => __('Want to reset password?'), 'class' => 'chkbox', 'title' => __('Click here to make reset password'), 'div' => array('class' => 'form-group checkboxDiv')));
    }

    ?>
</div> 
<div class="col-md-6">
    <?php
    echo $this->Form->input('email', array('placeholder' => 'Email'));
    $label_first = $pageDetailWithRole['singularTitle'] . ' Type';
    $place_holder_first = 'Select ' . $pageDetailWithRole['singularTitle'] . ' Type';

    echo $this->Form->input('user_type', array('div' => array('class' => 'form-group clearBoth col-md-12 no-padding'), 'label' => __($label_first), 'empty' => __($place_holder_first)));

    ?>
</div>