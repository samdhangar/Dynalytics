<?php
App::uses('AppModel', 'Model');

class ErrorDetail extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'error_detail';
    public $belongsTo = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
			
        ),
         'ErrorType' => array(
            'className' => 'ErrorType',
            'foreignKey' => 'error_type_id'
        ) 
    );
    public $hasMany = array(
        'Ticket' => array(
            'className' => 'Ticket',
            'foreignKey' => 'error_detail_id'
        )
    );

    function getCountErroredFiles($conditions = array())
    {
        return $this->find('count', array('contain' => false, 'conditions' => $conditions));
    }
    function getCountErroredFilesNew($conditions = array())
    {
        return $this->find('count', array( 'conditions' => $conditions));
    }
    function getAllErrors($conditions = array())
    {
        $errors = $this->find('all', array(
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id')
                )
            ),
            'conditions' => $conditions
        ));
        return $errors;
    }
    
    function getClientError($conditions)
    {
        
    }
}
