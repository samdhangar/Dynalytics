<?php
App::uses('AppModel', 'Model');

class History extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    
    public $belongsTo = array(
        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'ref_id',
            'conditions' => array('History.ref_model' => 'CompanyBranch'),
            'fields' => '',
            'order' => ''
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'ref_id',
            'conditions' => array('History.ref_model' => 'Dealer'),
            'fields' => '',
            'order' => ''
        ),
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'ref_id',
            'conditions' => array('History.ref_model' => 'Company'),
            'fields' => '',
            'order' => ''
        ),
        'Admin' => array(
            'className' => 'User',
            'foreignKey' => 'ref_id',
            'conditions' => array('History.ref_model' => 'Admin'),
            'fields' => '',
            'order' => ''
        ),
        'CreatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    
    function saveHistoryData($inData = array())
    {
        $sesData = getMySessionData();
        $inData['created_by'] = $sesData['id'];
        $this->create();
        $this->save($inData);
    }

}
