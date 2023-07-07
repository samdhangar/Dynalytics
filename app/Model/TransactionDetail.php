<?php
App::uses('AppModel', 'Model');

class TransactionDetail extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'transaction_details';
    public $belongsTo = array(
        'TransactionCategory' => array(
            'className' => 'TransactionCategory',
            'foreignKey' => 'transaction_category'
        ),
        'TransactionType' => array(
            'className' => 'TransactionType',
            'foreignKey' => 'trans_type_id'
        ),
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id',
            array(
                'className' => 'Branch',
                'foreignKey' => 'branch_id'
            )
        )
    );

    function getCountTransaction($conditions = array())
    {
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        }
        if (isDealer()) {
            $sessData = getMySessionData();
            $conditions['FileProccessingDetail.company_id'] = ClassRegistry::init('User')->getMyCompanyList($sessData['id'], $sessData['role']);
        }
        return $this->find('count', array('contain' => array('FileProccessingDetail'=>array('id','file_date','company_id')), 'conditions' => $conditions));
    }
}
