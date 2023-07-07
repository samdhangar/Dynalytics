<?php
App::uses('AppModel', 'Model');

/**
 * Station Model
 *
 * @property Company $Company
 * @property Branch $Branch
 */
class Station extends AppModel
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
    public $useTable = 'stations';


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id',
            'counterCache' => 'station_count'
        ),
        'CompanyBranch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id',
        ),
        'Location' => array(
            'className' => 'Location',
            'foreignKey' => 'location_category',
        )
    );
    public $hasMany = array(
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => false,
        )
    );
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter station name',
                'last' => true,
            ),
        ),
		'station_code' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter station code',
                'last' => true,
            )
        ),
        'serial_no' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter serial no',
                'last' => true,
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Serial no already used.',
            ),
        )
    );

    function getStationList($companyId = null)
    {
   /*     $conditions = array();
        if (!empty($companyId)) {
            $conditions = array(
                'Station.company_id' => $companyId
            );
        }
        return $this->find('list', array('fields' => 'name, name', 'contain' => false, 'conditions' => $conditions ,   'order' => array('Station.name' => 'desc')));*/
    }
    function getStation($conditions)
    {
        $stationList = $this->find('first', array(
            'fields' => 'id, name',
            'contain' => false,
            'conditions' => $conditions
        ));

    return $stationList;
    }
    function getDealerStationList($dealerId = null, $fields = 'Station.id, Station.name')
    {
        $conditions = array();
        $companyIdList = array();
        if (empty($dealerId)) {
            $dealerId = getDealerId();
        }
        if (empty($dealerId)) {
            return array();
        } else {
            $companyIdList = ClassRegistry::init('User')->getSuparCompanyListFromDeal($dealerId, 'User.id, User.id');
        }
        if (!empty($companyIdList)) {
            $conditions = array(
                'Station.company_id' => $companyIdList
            );
        }
        return $this->find('list', array('fields' => $fields, 'contain' => false, 'conditions' => $conditions));
    }

    function getDealerStations($dealerId = null)
    {
        $conditions = array();
        $companyIdList = $companyList = array();
        if (empty($dealerId)) {
            $dealerId = getDealerId();
        }
        if (empty($dealerId)) {
            return array();
        } else {
            $companyList = ClassRegistry::init('User')->getSuparCompanyListFromDeal($dealerId);
            $companyIdList = array_keys($companyList);
        }
        if (!empty($companyIdList)) {
            $conditions = array(
                'Station.company_id' => $companyIdList
            );
        }
        $stations = $this->find('all', array('contain' => false, 'conditions' => $conditions));
        $retStations = array();
        $branchList = ClassRegistry::init('CompanyBranch')->find('list', array(
            'fields' => array(
                'id', 'name'
            )
        ));
        foreach ($stations as $station) {
            $retStations[$station['Station']['id']]['Station'] = $station['Station'];
            $retStations[$station['Station']['id']]['Branch'] = isset($branchList[$station['Station']['branch_id']]) ? $branchList[$station['Station']['branch_id']] : '';
            $retStations[$station['Station']['id']]['Company'] = isset($companyList[$station['Station']['company_id']]) ? $companyList[$station['Station']['company_id']] : '';
        }
        return $retStations;
    }
}
