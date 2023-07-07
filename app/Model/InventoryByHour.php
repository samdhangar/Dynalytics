<?php
App::uses('AppModel', 'Model');

/**
 * Inventory by Model
 *
 * @property Company $Company
 */
class  InventoryByHour extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'inventory_by_hours';
    
    /*public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        )
        
    );*/

}
