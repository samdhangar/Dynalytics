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
class Location extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $useTable = 'location';
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
    );

    function getlocationList()
    {
        $conditions = array('Location.status' => 'active');
        return $this->find('list', array('fields' => 'id, name', 'conditions' => $conditions));
    }
 

}
