<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class Specialnotesreconciliation extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'special_notes_reconciliation';
    
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id',
            array(
                'className' => 'Branch',
                'foreignKey' => 'branch_id',
            )
        )
    );

}
