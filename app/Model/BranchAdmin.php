<?php
App::uses('AppModel', 'Model');

/**
 * BranchAdmin Model
 *
 * @property Branch $Branch
 * @property Admin $Admin
 */
class BranchAdmin extends AppModel
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
        'Admin' => array(
            'className' => 'User',
            'foreignKey' => 'admin_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getAssignedAdmins($admId = null)
    {
        $conditions = array();
        if (!empty($admId)) {
            $conditions = array(
                'BranchAdmin.admin_id' => $admId
            );
        }
        return $this->find('list', array('fields' => 'branch_id, id', 'contain' => false, 'conditions' => $conditions));
    }
    public function getAssignedAdminsId($admId = null)
    {
        $conditions = array();
        if (!empty($admId)) {
            $conditions = array(
                'BranchAdmin.admin_id' => $admId
            );
        }
        return $this->find('list', array('fields' => 'branch_id', 'contain' => false, 'conditions' => $conditions));
    }

    public function getAssignedAdminsName($admId = null)
    {
        $conditions = array();
        if (!empty($admId)) {
            $conditions = array(
                'BranchAdmin.admin_id' => $admId
            );
        }
        return $this->find('list', array('fields' => 'branch_id', 'contain' => false, 'conditions' => $conditions)
    );
/*  if(!empty($branchId)){
            $branchAdmins = $this->find('list',array(
                'fields' => 'admin_id, admin_id',
                'conditions' => array(
                    'BranchAdmin.branch_id' => $branchId
                )
            ));
            $conditions = ClassRegistry::init('User')->find('all',array(
                'contain' => false,
                'fields' => array('id','first_name','last_name','email','phone_no'),
                'conditions' => array(
                    'User.id' => $branchAdmins
                )
            ));
        }*/


    }

    function setBranchAdmin($branchId = null, $adminId = null)
    {
        if(!empty($branchId) && !empty($adminId)){
            $conditions = array(
                'BranchAdmin.branch_id' => $branchId,
                'BranchAdmin.admin_id' => $adminId
            );
            $adminBranchDetail = $this->find('count', array('contain' => false, 'conditions' => $conditions));
            $saveData = array(
                'branch_id' => $branchId,
                'admin_id' => $adminId
            );
            if (empty($adminBranchDetail)) {
                $this->create();
                $this->save($saveData);
            }
        }else{
            if(!empty($branchId)){
                $this->deleteAll(array('BranchAdmin.branch_id'=>$branchId));
            }
        }
    }
    
    function getBranchAdminsEmailDetails($branchId = null)
    {
        $users = array();
        if(!empty($branchId)){
            $branchAdmins = $this->find('list',array(
                'fields' => 'admin_id, admin_id',
                'conditions' => array(
                    'BranchAdmin.branch_id' => $branchId
                )
            ));
            $users = ClassRegistry::init('User')->find('all',array(
                'contain' => false,
                'fields' => array('id','first_name','last_name','email','phone_no'),
                'conditions' => array(
                    'User.id' => $branchAdmins
                )
            ));
        }
        return $users;
    }
}
