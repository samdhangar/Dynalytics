<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class BillsActivityReport extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'bills_activity_report';
    

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
        'ActivityReport' => array(
            'className' => 'ActivityReport',
            'foreignKey' => 'activity_report_id'
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
