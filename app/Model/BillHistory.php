<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class BillHistory extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'bills_history';
    

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'Manager' => array(
            'className' => 'ManagerInfo',
            'foreignKey' => 'manager_id'
        ),
        'BillType' => array(
            'className' => 'BillType',
            'foreignKey' => 'bill_type_id'
        ),
//        'Station' => array(
//            'className' => 'Station',
//            'foreignKey' => 'station'
//        )
    );

}
