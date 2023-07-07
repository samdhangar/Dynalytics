<?php
App::uses('AppModel', 'Model');

/**
 * Country Model
 *
 * @property City $City
 * @property Customer $Customer
 * @property Order $Order
 * @property State $State
 * @property Taluka $Taluka
 * @property User $User
 * @property Vendor $Vendor
 * @property Village $Village
 */
class TransactionHeatMap extends AppModel
{
     public $useTable = 'transaction_heat_maps'; 
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    public $actsAs = array(
        'Containable'
    );
  
//The Associations below have been created with all possible keys, those that are not needed can be removed
    /**
     * belongsTo associations
     *


    /**
     * hasMany associations
     *
     * @var array
     */
    public $belongsTo = array(
        'CompanyBranches' => array(
            'className' => 'CompanyBranches',
            'foreignKey' => 'branch_id'
        ),
        'Station' => array(
            'className' => 'Station',
            'foreignKey' => 'machine_id'
        )
    );

    function getRegionList($conditions)
    {
        return $this->find('list', array('fields' => 'id, name', 'conditions' => $conditions));
    }

    function deleteAddressHierarchy($id = null, $deleteFor = 'transaction_heat_maps')
    {
        $sessionData = getMySessionData();
        $deleteArray = array('transaction_heat_maps', 'company_branches');
        if (empty($id)) {
            return false;
        }
        $fields = array(
            'Is_deleted' => "'0'",
            'branch_status'=>"'deleted'"
        );
        if (isAdmin()) {
//            $conditions[ucfirst($deleteFor) . '.user_id'] = CakeSession::read('Auth.User.id');
          // $conditions[ucfirst($deleteFor) . '.user_id'] = $sessionData['id'];
        }
        $deleteStart = 0;
        foreach ($deleteArray as $className) {
            if ($deleteStart == 1) {
                $conditions[ucfirst($className) . '.transaction_heat_maps'] = $id;
                $res = ClassRegistry::init($className)->updateAll($fields, $conditions);
            }
            if ($className == $deleteFor) {
                $deleteStart = 1;
            }
        }
    }
 

}
