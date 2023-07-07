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

class TicketConfig extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $validate = array(
        'machine_error_id' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please select machine error',
                'last' => true,
            ),
            'isUniqueMachine' => array(
                'rule' => 'isUniqueMachine',
                'message' => 'Ticket config already exits for this station and machine error.'
            )
        )
    );
    public $belongsTo = array(
//        'MachineType' => array(
//            'className' => 'MachineType',
//            'foreignKey' => 'machine_type_id'
//        ),
//        'ErrorType' => array(
//            'className' => 'ErrorType',
//            'foreignKey' => 'error_type_id'
//        ),
        'MachineError' => array(
            'className' => 'MachineError',
            'foreignKey' => 'machine_error_id'
        ),
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id'
        ),
        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id'
        )
    );

    function isUniqueMachine()
    {
        $countMachine = $this->find('count', array(
            'contain' => false,
            'conditions' => array(
                'dealer_id' => $this->data['TicketConfig']['dealer_id'],
                'branch_id' => $this->data['TicketConfig']['branch_id'],
                'company_id' => $this->data['TicketConfig']['company_id'],
                'station' => $this->data['TicketConfig']['station']
            )
        ));

        return !empty($countMachine) ? false : true;
    }
}
