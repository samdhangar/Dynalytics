<?php
App::uses('AppModel', 'Model');

/**
 * State Model
 *
 * @property Country $Country
 * @property City $City
 * @property Customer $Customer
 * @property Order $Order
 * @property Taluka $Taluka
 * @property User $User
 * @property Vendor $Vendor
 * @property Village $Village
 */
class State extends AppModel
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
     * @var array
     */
    public $belongsTo = array(
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country_id',
            'counterCache' => array(
                'state_count' => array('State.status' => 'active')
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
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter State Name',
                'last' => true,
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('name', 'country_id', 'status'), false),
                'message' => 'State Name already Exist'
            )
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
            'foreignKey' => 'state_id',
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
            'foreignKey' => 'state_id',
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

    function getStateList($countryId = 0)
    {
        $conditions = array('State.status' => 'active');
        if (!empty($countryId)) {
            $conditions['State.country_id'] = $countryId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, name', 'conditions' => $conditions));
       
    }
    function getStateList2($conditions)
    {
        return $this->find('list', array('contain' => false, 'fields' => 'id, name', 'conditions' => $conditions));
       
    }
}
