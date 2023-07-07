<?php
if($pageDetailWithRole['role'] != DEALER):
//if (!isDealer()):
    echo $this->Form->input('address', array('placeholder' => 'Address 1', 'label' => 'Address 1', 'type' => 'text'));
    echo $this->Form->input('address2', array('placeholder' => 'Address 2', 'label' => 'Address 2', 'type' => 'text','div' => array('class' => array('form-group'))));
    echo $this->Form->input('country_id', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-leftpadding'), 'onchange' => 'getStates(this.value,"UserStateId")', 'empty' => __('Select country')));
    echo $this->Form->input('state_id', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-leftpadding'), 'onchange' => 'getCities(this.value,"UserCityId")', 'id' => 'UserStateId', 'empty' => __('Select state')));
    echo $this->Form->input('city_id', array('type' => 'select', 'div' => array('class' => 'form-group required col-md-3 no-padding'), 'id' => 'UserCityId', 'empty' => __('Select city')));
    echo $this->Form->input('pincode', array('label' => 'Zip Code', 'div' => array('class' => 'form-group required col-md-3 no-rightpadding'), 'placeholder' => 'Zip Code'));
endif;

?>