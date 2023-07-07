<?php

App::uses('AppModel', 'Model');

/**
 * BranchDealer Model
 *
 * @property Branch $Branch
 * @property Admin $Admin
 */
class BranchDealer extends AppModel
{
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
        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'CreatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'created_by',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'UpdatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'updated_by',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getAssigneDealers($dealId = null)
    {
        $conditions = array();
        if (!empty($dealId)) {
            $conditions = array(
                'BranchDealer.dealer_id' => $dealId
            );
        }
        return $this->find('list', array('fields' => 'branch_id, id', 'contain' => false, 'conditions' => $conditions));
    }

    function saveBranchDealer($indata = array(), $oldData = array())
    {
        $sessData = getMySessionData();
        if (!empty($indata)) {
            /**
             * check if dealer change for the branch
             */
            $saveData = array(
                'dealer_id' => $indata['dealer_id'],
                'branch_id' => $indata['branch_id'],
                'status' => 'Sent',
                'created_by' => $sessData['id']
            );
            if (!empty($saveData)) {
                $this->create();
                $this->save($saveData);
            }
        }
    }

    function updateBranchDealer($data = array(), $branchDealerId)
    {
        $sessData = getMySessionData();
        $this->id = $branchDealerId;
        $data['updated_by'] = $sessData['id'];
        $this->save($data);
    }
    
    public function getDealerBranch($dealId = null)
    {
        $conditions = array(
            'BranchDealer.dealer_id' => $dealId
        );
        return $this->find('list', array('fields' => 'branch_id, id', 'contain' => false, 'conditions' => $conditions));
    }
    
    public function getAssignedBranch($dealId = null)
    {
        $conditions = array();
        if(!empty($dealId)){
            $conditions['NOT'] = array(
                'BranchDealer.dealer_id' => $dealId
            );
        }
        //get assign branch list if user is supar admin
        if(isSuparAdmin() || isAdminAdmin()){
            $conditions = array();
            $conditions = array(
                'BranchDealer.dealer_id' => $dealId
            );
        }
        return $this->find('list', array('fields' => 'branch_id, id', 'contain' => false, 'conditions' => $conditions));
    }
}
