<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class TellerActivityReport extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'teller_activity_report';
    
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
         'ActivityReport' => array(
            'className' => 'ActivityReport',
            'foreignKey' => 'activity_report_id'
        ),
        'TellerSetup' => array(
            'className' => 'TellerSetup',
            'foreignKey' => 'teller_id'
        )
        
//        'Station' => array(
//            'className' => 'Station',
//            'foreignKey' => 'station'
//        )
    );

}
