<?php
App::uses('AppModel', 'Model');

/**
 * City Model
 *
 * @property Country $Country
 * @property State $State
 * @property Customer $Customer
 * @property Order $Order
 * @property Taluka $Taluka
 * @property User $User
 * @property Vendor $Vendor
 * @property Village $Village
 */
class City extends AppModel
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
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter State Name',
                'last' => true,
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('name', 'country_id', 'state_id', 'status'), false),
                'message' => 'State Name already Exist'
            )
        )
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country_id',
            'counterCache' => array(
                'city_count' => array('City.status' => 'active')
            )
        ),
        'State' => array(
            'className' => 'State',
            'foreignKey' => 'state_id',
            'counterCache' => array(
                'city_count' => array('City.status' => 'active')
            )
        ),
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'city_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function getCityList($stateId = 0)
    {
        $conditions = array('City.status' => 'active');
        if (!empty($stateId)) {
            $conditions['City.state_id'] = $stateId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, name', 'conditions' => $conditions));
    }

    function getCityList2($conditions)
    {
        return $this->find('list', array('contain' => false, 'fields' => 'id, name', 'conditions' => $conditions));
    }
}
