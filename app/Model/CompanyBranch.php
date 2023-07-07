<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class CompanyBranch extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id',
            'counterCache' => array(
                'company_branch_count' => array('CompanyBranch.branch_status IN ' => array('active', 'pending', 'inactive')),
            )
        ),
        'Admin' => array(
            'className' => 'User',
            'foreignKey' => 'admin_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'State' => array(
            'className' => 'State',
            'foreignKey' => 'state',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'regiones',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    public $hasMany = array(
        'Station' => array(
            'className' => 'Station',
            'foreignKey' => 'branch_id',
        ),
        'BranchDealer' => array(
            'className' => 'BranchDealer',
            'foreignKey' => 'branch_id',
        )
    );
    public $validate = array(
        // 'email' => array(
        //     'notempty' => array(
        //         'rule' => array('notEmpty'),
        //         'message' => 'Please enter Email Address',
        //         'last' => true,
        //     ),
        //     'email' => array(
        //         'rule' => array('email'),
        //         'message' => 'Please Enter a valid Email Address'
        //     ),
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'message' => 'Email Address already Exist.'
        //     )
        // ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter Branch Name',
                'last' => true,
            )
        )
    );

    function afterSave($created, $options = array())
    {
        
        parent::afterSave($created, $options);
        if (!empty($this->data['CompanyBranch']['id']) || !empty($this->id)) {
            /**
             * Check the directory if nor exist than create branch directory
             * @param type $companyId
             * @param type $getAll
             * @return array
             */
            $ftpUser = $this->field('ftpuser');
            
            $ftpPathOld = $this->field('ftp_path');
            $SiteConfig = ClassRegistry::init('SiteConfig');
            $data = $SiteConfig->find('first',array('conditions'=>array('key'=>'Site.TextparsingUrl')));
            $url = $data['SiteConfig']['value'];
            $url = $url."/createfolderapi.php?folder=$ftpUser";
            $ch = curl_init();
            $curlConfig = array(
                CURLOPT_URL            => $url,
                CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => $data
            );

            curl_setopt_array($ch, $curlConfig);
            $result = curl_exec($ch);
            curl_close($ch);

            if (!empty($ftpUser)) {
                $ftpPath = addBranchDirectory($ftpUser, true);
                if ($ftpPathOld != $ftpPath) {
                    $this->saveField('ftp_path', $ftpPath);
                }
            }
        }
    }

    function getBranchList($companyId = null, $getAll = false)
    {
        $branchList = array();
        if ($getAll) {
            $branchLists['all'] = __('All Branch');
            $sesData = getMySessionData();
            $companyId = ClassRegistry::init('User')->getMyCompanyList($sesData['id'], $sesData['role'], 'User.id, User.id');
        }
        $conditions = array();
        if (!empty($companyId)) {
			if(is_array($companyId))
			{
				$conditions = array(
					'CompanyBranch.company_id IN' => $companyId
				);
			}
			else
			{
				$conditions = array(
					'CompanyBranch.company_id' => $companyId
				);
			}
        }

        $branchList = $this->find('list', array('fields' => 'id, name', 'order' => 'name ASC', 'contain' => false, 'conditions' => $conditions));

        if ($getAll) {
            foreach ($branchList as $branchId => $branchName) {
                $branchLists[$branchId] = $branchName;
            }
            $branchList = $branchLists;
        }
        return $branchList;
    }

    function getMyBranchLists($companyId = null)
    { 
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                'CompanyBranch.regiones' => $companyId,
                // 'CompanyBranch.company_id' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
            
            
        }
        return $branchLists;
    }
    function getMyBranchLists1($companyId = null)
    { 
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                // 'CompanyBranch.regiones' => $companyId,
                'CompanyBranch.company_id' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
            
            
        }
        return $branchLists;
    }

    function getMyBranchList2($conditions)
    { 
        $branchLists = array();
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'contain' => false,
                'conditions' => $conditions
            ));
 
        return $branchLists;
    }
    function getMyBranchExist($conditions)
    { 
        // $branchLists = array();
            $branchLists = $this->find('first', array(
                'fields' => 'id, name',
                'contain' => false,
                'conditions' => $conditions
            ));
 
        return $branchLists;
    }

    function getAllBranch($companyId = null)
    { 
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                'CompanyBranch.regiones' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
        }
        return $branchLists;
    }
    function getAllBranch1($companyId = null)
    { 
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                'CompanyBranch.company_id' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
            if(empty($branchLists)){
                $conditions = array(
                    'CompanyBranch.regiones' => $companyId,
                    'CompanyBranch.branch_status' => 'active',
                );
                $branchLists = $this->find('list', array(
                    'fields' => 'id, name',
                    'order' => 'name ASC',
                    'contain' => false,
                    'conditions' => $conditions
                ));
            }
        }
        return $branchLists;
    }
     function getMyBranchListsAdmin($companyId = null)
    {
       
        $branchLists = array();
        if (!empty($companyId)) {
			if(is_array($companyId))
			{
				$conditions = array(
					'CompanyBranch.company_id IN' => $companyId,
					'CompanyBranch.is_list_display' => '1',
					
				);
			}
			else
			{
				$conditions = array(
					'CompanyBranch.company_id' => $companyId,
					'CompanyBranch.is_list_display' => '1',
					
				);
			}
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
        }
        return $branchLists;
    }
   
       function getMyBranchLists2($companyId = null)
    {
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                'CompanyBranch.regiones' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
        }
        return $branchLists;
    }

     function getMyBranchListsList($companyId = null)
    {
        $branchLists = array();
        if (!empty($companyId)) {
            $conditions = array(
                'CompanyBranch.id' => $companyId,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = $this->find('list', array(
                'fields' => 'id, name',
                'order' => 'name ASC',
                'contain' => false,
                'conditions' => $conditions
            ));
        }
        return $branchLists;
    }

    function getStationList($branchId = null, $otherConditions = array())
    {
       
        $conditions = array();
        if (!empty($otherConditions)) {
            $conditions = $otherConditions;
        }
        if (!empty($branchId)) {
            $conditions['Station.branch_id'] = $branchId;
            $conditions['Station.status'] = 'active';
        }
     
        $stationdata = ClassRegistry::init('Station')->find('all', array('fields' => 'id,name,station_code', 'contain' => false, 'conditions' => $conditions  , 'order' => array('Station.name' => 'ASC')));
        $stationdata_arr = array();
        foreach($stationdata as $key=>$value){
            $stationdata_arr[$value['Station']['id']] = $value['Station']['name'].'('.$value['Station']['station_code'].')';

        }
        return $stationdata_arr;
        // return ClassRegistry::init('Station')->find('list', array('fields' => 'station_code, name', 'contain' => false, 'conditions' => $conditions  , 'order' => array('Station.name' => 'ASC')));
    }

     function getStationListAll($companyId = null, $otherConditions = array())
    {
       
        $conditions = array();
        if (!empty($otherConditions)) {
            $conditions = $otherConditions;
        }
        if (!empty($companyId)) {
			if(is_array($companyId))
			{
				$conditions['Station.company_id IN'] = $companyId;
			}
			else
			{
				$conditions['Station.company_id'] = $companyId;
			}
        }
     
        
        return ClassRegistry::init('Station')->find('list', array('fields' => 'name, name', 'contain' => false, 'conditions' => $conditions));
    }

    function getBranchDetail($branchId = null)
    {
        $responseArr = array(
            'addressArr' => array(),
            'Company' => array(),
            'CompanyBranch' => array()
        );
        if ($this->exists($branchId)) {
            $conditions = array(
                'CompanyBranch.id' => $branchId
            );
            $contain = array(
                'Country' => array('fields' => 'id, name'),
                'State' => array('fields' => 'id, name'),
                'City' => array('fields' => 'id, name'),
                'Company' => array('fields' => 'id, first_name, last_name, name, email, phone_no')
            );
            $branchDetail = $this->find('first', array('contain' => $contain, 'conditions' => $conditions));
            $responseArr['addressArr'] = array(
                'address' => $branchDetail['CompanyBranch']['address'],
                'country' => $branchDetail['Country']['name'],
                'state' => $branchDetail['State']['name'],
                'city' => $branchDetail['City']['name'],
                'pincode' => ''
            );
            $responseArr['Company'] = $branchDetail['Company'];
            $responseArr['CompanyBranch'] = $branchDetail['CompanyBranch'];
        }
        return $responseArr;
    }

    function createBranchFromCompany($companyData = array())
    {
        $ftpDetail = getBranchFtpDetail(true, true);
        $sessData = getMySessionData();
        $saveData = array(
            'company_id' => $companyData['id'],
            'name' => $companyData['first_name'] . ' Branch',
            'contact_name' => $companyData['last_name'] . ' Branch',
//            'email' => getRandomEmailId($companyData['email']),
            'email' => $companyData['email'],
            'phone' => $companyData['phone_no'],
            'branch_status' => 'active',
            'ftpuser' => $ftpDetail['ftp_username'],
            'ftp_pass' => $ftpDetail['ftp_password'],
            'ftp_path' => $ftpDetail['ftp_path'],
            'created_by' => $sessData['id'],
            'city' => $companyData['city_id'],
            'state' => $companyData['state_id'],
            'country' => $companyData['country_id'],
            'status' => 1,
            'is_list_display' => 0,
        );
        if (!empty($companyData['address'])) {
            $saveData['address'] = $companyData['address'];
        }
        if (!empty($companyData['address1'])) {
            $saveData['address2'] = $companyData['address1'];
        }
        $this->create();
        $this->save($saveData);
        return $this->id;
    }

    function getBrancheListForReports($companyId = null)
    {
        $logUserId = ClassRegistry::init('User')->getLoginBranchUserId();
        $branches = $this->getBranchList($logUserId);
        if (isCompany()) {
            $branches = $this->getMyBranchLists(getCompanyId());
        }
        if (!empty($companyId)) {
            $branches = $this->getMyBranchLists($companyId);
        }
        return $branches;
    }

    function deleteCompanyBranches($companyId = null)
    {
        if (!empty($companyId)) {
            $branchList = $this->find('list', array(
                'fields' => 'id, id',
                'conditions' => array(
                    'CompanyBranch.company_id' => $companyId
                )
            ));
            $this->updateAll(
                array(
                'CompanyBranch.branch_status' => '"deleted"',
                'CompanyBranch.status' => 0
                ), array(
                'CompanyBranch.company_id' => $companyId
                )
            );
            ClassRegistry::init('Station')->updateAll(
                array(
                'Station.status' => '"deleted"',
                ), array(
                'Station.branch_id' => $branchList
                )
            );
        }
    }
}
