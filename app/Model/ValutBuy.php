<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class ValutBuy extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'vault_buys';
    
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'Manager' => array(
            'className' => 'User',
            'foreignKey' => 'manager_id'
        )
        
//        'Station' => array(
//            'className' => 'Station',
//            'foreignKey' => 'station'
//        )
    );

}
