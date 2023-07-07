<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MachineError
 *
 * @author securemetasys002
 */
App::uses('AppModel', 'Model');

class MachineError extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $belongsTo = array(
        'MachineType' => array(
            'className' => 'MachineType',
            'foreignKey' => 'machine_type_id'
        ),
        'ErrorType' => array(
            'className' => 'ErrorType',
            'foreignKey' => 'error_type_id'
        ),
    );

}
