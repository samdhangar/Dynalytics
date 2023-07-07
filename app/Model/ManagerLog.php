<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class ManagerLog extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'manager_log';
    
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'Manager' => array(
            'className' => 'User',
            'foreignKey' => 'manager_id'
        )
    );

}
