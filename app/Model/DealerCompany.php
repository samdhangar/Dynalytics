<?php
App::uses('AppModel', 'Model');

class DealerCompany extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'created';
    public $actsAs = array(
        'Containable'
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getDealerClients($dealId = null)
    {
        $conditions = array(
            'DealerCompany.dealer_id' => $dealId
        );
        return $this->find('list', array('fields' => 'company_id, id', 'contain' => false, 'conditions' => $conditions));
    }
    public function getDealerClientsId($dealId = null)
    {
        $conditions = array(
            'DealerCompany.dealer_id' => $dealId
        );
        return $this->find('list', array('fields' => 'company_id, company_id', 'contain' => false, 'conditions' => $conditions));
    }
    public function getAssignedClients($dealId = null)
    {
        $conditions = array();
        if(!empty($dealId)){
            $conditions['NOT'] = array(
                'DealerCompany.dealer_id' => $dealId
            );
        }
        //get assign branch list if user is supar admin
        if(isSuparAdmin() || isAdminAdmin()){
            $conditions = array();
            $conditions = array(
                'DealerCompany.dealer_id' => $dealId
            );
        }
        return $this->find('list', array('fields' => 'company_id, id', 'contain' => false, 'conditions' => $conditions));
    }
}
