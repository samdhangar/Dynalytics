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
class Country extends AppModel
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
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter Country Name',
                'last' => true,
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('name', 'status'), false),
                'message' => 'Country Name already Exist'
            )
        )
    );
//The Associations below have been created with all possible keys, those that are not needed can be removed
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'AddedBy' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
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

    function getCountryList()
    {
        return $this->find('list', array('fields' => 'id, name', 'conditions' => array('Country.status' => 'active')));
    }
    function getCountryList2($conditions)
    {
        return $this->find('list', array('fields' => 'id, name', 'conditions' => $conditions));
    }
    function deleteAddressHierarchy($id = null, $deleteFor = 'Country')
    {
        $sessionData = getMySessionData();
        $deleteArray = array('Country', 'State', 'City');
        if (empty($id)) {
            return false;
        }
        $fields = array(
            'status' => "'deleted'"
        );
        if (isAdmin()) {
//            $conditions[ucfirst($deleteFor) . '.user_id'] = CakeSession::read('Auth.User.id');
            $conditions[ucfirst($deleteFor) . '.user_id'] = $sessionData['id'];
        }
        $deleteStart = 0;
        foreach ($deleteArray as $className) {
            if ($deleteStart == 1) {
                $conditions[ucfirst($className) . '.' . $deleteFor . '_id'] = $id;
                $res = ClassRegistry::init($className)->updateAll($fields, $conditions);
            }
            if ($className == $deleteFor) {
                $deleteStart = 1;
            }
        }
    }
 

}
