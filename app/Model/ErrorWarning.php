 <?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */ 
class ErrorWarning extends AppModel
{
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'error_detail';
    
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
         'ActivityReport' => array(
            'className' => 'ErrorWarning',
            'foreignKey' => 'error_type_id'
         ),
        
       'Station' => array(
           'className' => 'Station',
           'foreignKey' => 'station'
       )
    );

}
