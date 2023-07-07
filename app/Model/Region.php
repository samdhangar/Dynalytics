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
class Region extends AppModel
{
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
    public $hasMany = array(
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'country_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'State' => array(
            'className' => 'State',
            'foreignKey' => 'country_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'country_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );

    function getRegionList($conditions)
    {
        return $this->find('list', array('fields' => 'id, name', 'conditions' => $conditions));
    }

    function deleteAddressHierarchy($id = null, $deleteFor = 'Region')
    {
        $sessionData = getMySessionData();
        $deleteArray = array('Region', 'company_branches');
        if (empty($id)) {
            return false;
        }
        $fields = array(
            'status' => "'0'",
            'branch_status'=>"'deleted'"
        );
        if (isAdmin()) {
//            $conditions[ucfirst($deleteFor) . '.user_id'] = CakeSession::read('Auth.User.id');
          // $conditions[ucfirst($deleteFor) . '.user_id'] = $sessionData['id'];
        }
        $deleteStart = 0;
        foreach ($deleteArray as $className) {
            if ($deleteStart == 1) {
                $conditions[ucfirst($className) . '.regiones'] = $id;
                $res = ClassRegistry::init($className)->updateAll($fields, $conditions);
            }
            if ($className == $deleteFor) {
                $deleteStart = 1;
            }
        }
    }
 

}
