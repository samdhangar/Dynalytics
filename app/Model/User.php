<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author securemetasys
 */
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel
{
    var $name = 'User';
    var $userRole = array('Admin' => 'Admin', 'Company' => 'Company', 'Dealer' => 'Dealer');
    var $userType = array(SUPAR_ADM => SUPAR_ADM, 'Admin' => 'Admin', 'Support' => 'Support');

//    var $virtualFields = array(
//        'name' => 'CONCAT(User.first_name, " ",User.last_name)',
//    );
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['name'] = sprintf(
            'CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias
        );
    }
    public $actsAs = array(
        'Upload.Upload' => array(
            'photo' => array(
                'thumbnailMethod' => 'php',
                'thumbnailSizes' => array(
                    'thumb' => '200w',
                ),
            )
        ),
        'Containable'
    );
    public $belongsTo = array(
        'created_by' => array(
            'className' => 'User',
            'foreignKey' => 'created_by'
        ),
        'parent_id' => array(
            'className' => 'User',
            'foreignKey' => 'parent_id',
            'counterCache' => array(
                'sub_dealer_count' => array('User.status IN' => array('active', 'pending', 'inactive'), 'User.role' => 'Dealer', 'User.user_type <> ' => SUPAR_ADM),
                'sub_company_count' => array('User.status IN' => array('active', 'pending', 'inactive'), 'User.role' => 'Company', 'User.user_type <> ' => SUPAR_ADM)
            )
        ),
        'updated_by' => array(
            'className' => 'User',
            'foreignKey' => 'updated_by'
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id',
            'counterCache' => array(
                'dealer_company_count' => array('User.status IN' => array('active', 'pending', 'inactive'), 'User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)
            )
        ),
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country_id'
        ),
        'State' => array(
            'className' => 'State',
            'foreignKey' => 'state_id'
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id'
        ),
        'Subscription' => array(
            'className' => 'Subscription',
            'foreignKey' => 'subscription_id'
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'DealerCompany' => array(
            'className' => 'DealerCompany',
            'foreignKey' => 'dealer_id'
        ),
        'CompanyBranch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'company_id'
        ),
        'Station' => array(
            'className' => 'Station',
            'foreignKey' => 'company_id'
        )
    );
    public $validate = array(
        'first_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a first name.'
            ),
            'minLength' => array(
                'rule' => array('minLength', 2),
                'message' => 'Please enter at least two characters first name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 50),
                'message' => 'Please enter first name between 2 to 50 characters.'
            )
        ),
        'last_name' => array(
            'minLength' => array(
                'rule' => array('minLength', 2),
                'message' => 'Please enter at least two characters last name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 50),
                'message' => 'Please enter last name between 2 to 50 characters.'
            )
        ),
//        'phone_no' => array(
//            'numeric' => array(
//                'rule' => 'numeric',
//                'allowEmpty' => true,
//                'message' => 'Please enter valid phone no.'
//            ),
//            '10-digit' => array(
//                'rule' => array('phone', '/^[0-9]( ?[0-9]){8} ?[0-9]$/'),
//                'message' => 'Please enter 10 digit phone no.'
//            )
//        ),
        'address' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 150),
                'message' => 'Please enter address within 150 characters.'
            )
        ),
        'email' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter Email Address',
                'last' => true,
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Please Enter a valid Email Address'
            ),
            'isUnique' => array(
                'rule' => array('isUniqueUser'),
                'message' => 'Email Address already Exist.'
            )
        ),
        'password' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter Password.',
                'last' => false
            ),
            'minLength' => array(
                'rule' => array('minLength', 6),
                'message' => 'Please enter at least six characters password'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 15),
                'message' => 'Please enter password between 6 to 15 characters.'
            )
        ),
        'confirm_password' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'on' => 'create',
                'message' => 'Please enter Confirm Password.',
                'last' => false
            ),
            'identicalFieldValues' => array(
                'rule' => array('identicalFieldValues', 'password'),
                'message' => 'Please re-enter your password twice so that the values match',
                'last' => true
            )
        ),
        'status' => array(
            'inList' => array(
                'rule' => array('inList', array('active', 'inactive', 'pending', 'deleted')),
                'message' => 'Please choose status from options.'
            ),
        ),
        'photo' => array(
            'isUnderPhpSizeLimit' => array(
                'rule' => 'isUnderPhpSizeLimit',
                'message' => 'File exceeds upload filesize limit',
            ),
            'isValidExtension' => array(
                'rule' => array('isValidExtension', array('jpg', 'png', 'jpeg', 'gif'), false),
                'message' => 'Please upload valid Image File',
            )
        )
    );

    function identicalFieldValues($field = array(), $compare_field = null)
    {
        foreach ($field as $key => $value) {
            $v1 = $value;
            $v2 = $this->data[$this->name][$compare_field];
            if ($v1 !== $v2) {
                return FALSE;
            } else {
                continue;
            }
        }
        return TRUE;
    }

    function beforeSave($options = array())
    {
        if (!empty($this->data['User']['password'])) {
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha1'));
            $this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);
        }
        return true;
    }

    function afterSave($created, $options = array())
    {

        parent::afterSave($created, $options);
    }

    public function isUniqueUser($email = null)
    {
        $user = array();
        if (!empty($email)) {
            // $user = $this->find($email);
            $user  = $this->find('first', array(
                        'conditions' => array(
                            'status !=' => 'deleted',
                            'email' => $email['email'],
                        ),
                        'contain' => false,
                    ) );
            if(!empty($user)){
                if($user['User']['id'] == $this->data[$this->alias]['id']){
                    return true;
                }
            }
            if (empty($user)) {
                return true;
            }
            return false;
        }
    }

    public function getParentsList($parentId = 0, $type = 'Dealer')
    {
        return $this->find('list', array(
                'conditions' => array(
                    'status' => 'active',
                    'parent_id' => 0,
                    'role' => $type,
                    'user_type' => SUPAR_ADM
                ),
                'contain' => false,
                'fields' => 'id, first_name'
        ));
    }

    public function getUserList()
    {
        return $this->find('list', array(
                'contain' => false,
                'fields' => 'id, first_name'
        ));
    }

    public function getCompanyDealersList($companyId = null)
    {
        $conditions = array(
            'User.role' => $this->userRole['Company'],
            'NOT' => array('User.dealer_id' => 0)
        );
        if (!empty($companyId)) {
            $conditions['User.id'] = $companyId;
        }
        $this->virtualFields['dealer_name'] = 'Dealer.first_name';
//        $this->virtualFields['dealer_name'] = 'CONCAT(Dealer.first_name, " ", Dealer.last_name)';
        $companyList = $this->find('list', array('fields' => 'dealer_id, dealer_name', 'contain' => 'Dealer', 'conditions' => $conditions));
        return $companyList;
    }

    public function getSuparCompanyListFromDeal($dealId = null, $fields = 'User.id, User.first_name')
    {
        $conditions = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
            'NOT' => array('User.dealer_id' => 0)
        );
        if (!empty($dealId)) {
            $conditions['User.dealer_id'] = $dealId;
        }
        $companyList = $this->find('list', array('fields' => $fields, 'contain' => false, 'conditions' => $conditions));
        return $companyList;
    }
	
	public function getStationListFromCompanyID($company_id = null, $fields = 'User.id, User.first_name')
    {
        $conditions = array(
            'User.role' => DEALER,
            'User.user_type' => SUPAR_ADM,
            'NOT' => array('User.dealer_id' => 0)
        );
        if (!empty($dealId)) {
            $conditions['User.dealer_id'] = $dealId;
        }
        $companyList = $this->find('list', array('fields' => $fields, 'contain' => false, 'conditions' => $conditions));
        return $companyList;
    }

    public function getDealerList($dealerId = null, $otherConditions = array())
    {
        if (!empty($otherConditions)) {
            $conditions = $otherConditions;
        }
        $conditions['User.status'] = 'active';
        $conditions['User.role'] = $this->userRole['Dealer'];

        if (!empty($dealerId)) {
            $conditions['User.dealer_id'] = $dealerId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $conditions));
    }

    function getMySupportDealers($companyId = null, $otherConditions = array())
    {
        if (!empty($otherConditions)) {
            $conditions = $otherConditions;
        }
//        $conditions['DealerCompany.']
        $supportDealers = array();
        $supportDealers = ClassRegistry::init('DealerCompany')->find('list', array(
            'conditions' => $conditions
        ));
        $conditions['User.status'] = 'active';
        $conditions['User.role'] = DEALER;

        $conditions['User.user_type'] = SUPPORT;
        $conditions['User.id'] = $supportDealers;

        if (!empty($dealerId)) {
            $conditions['User.dealer_id'] = $dealerId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $conditions));
    }

    public function getSuparCompanyList($otherConditions = array())
    {
        $conditions = $otherConditions;
        $conditions['User.status'] = 'active';
        $conditions['User.role'] = COMPANY;
        $conditions['User.parent_id'] = 0;
        $conditions['User.user_type'] = SUPAR_ADM;
        return $this->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $conditions));
    }

    public function getSuparDealerList($parentId = null, $userType = SUPAR_ADM)
    {
        $conditions = array(
            'User.status' => 'active',
            'User.role' => $this->userRole['Dealer'],
            'User.user_type' => $userType
        );
        if (!empty($parentId)) {
            $conditions['created_by'] = $parentId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $conditions));
    }

    function getCompanyList($dealerId = null)
    {
        $conditions = array(
            'User.status' => 'active',
            'User.role' => $this->userRole['Company'],
            'User.user_type' => SUPAR_ADM
        );
        if (!empty($dealerId)) {
            $conditions['dealer_id'] = $dealerId;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $conditions));
    }

    function assignClientBranchToDealer($dealId = 0, $insertData = array())
    {
        $sentArr = array('clients' => array(), 'branches' => array());
        /**
         * Assign Clients
         */
        $this->DealerCompany = ClassRegistry::init('DealerCompany');
        $oldData = $this->DealerCompany->find('list', array(
            'fields' => 'company_id, id',
            'contain' => false,
            'conditions' => array('dealer_id' => $dealId)
            )
        );
        if (!empty($dealId) && !empty($insertData['dealer_company_id'])) {
            $clients = $insertData['dealer_company_id'];

            foreach ($clients as $clientId) {
                $saveData = array(
                    'dealer_id' => $dealId,
                    'company_id' => $clientId,
//                    'id' => $oldId
                );
                if (isset($oldData[$clientId])) {
                    $this->DealerCompany->id = $saveData['id'] = $oldData[$clientId];
                } else {
                    $this->DealerCompany->create();
                }
                $this->DealerCompany->save($saveData);
                if (isset($oldData[$clientId])) {
                    unset($oldData[$clientId]);
                } else {
                    $sentArr['clients'][$this->DealerCompany->id] = $clientId;
                }
            }
        }
        /**
         * Delete old assigned clients
         */
        if (!empty($oldData)) {
            $this->DealerCompany->deleteAll(array('DealerCompany.id' => array_values($oldData)));
        }
        /**
         * Assign Branches
         */
        $this->BranchDealer = ClassRegistry::init('BranchDealer');
        $oldData = $this->BranchDealer->find('list', array(
            'fields' => 'branch_id, id',
            'contain' => false,
            'conditions' => array('dealer_id' => $dealId)
            )
        );
        $sessData = getMySessionData();
        if (!empty($dealId) && !empty($insertData['dealer_branch_id'])) {
            $branches = $insertData['dealer_branch_id'];

            foreach ($branches as $branchId) {
                $saveData = array(
                    'dealer_id' => $dealId,
                    'branch_id' => $branchId,
                    'status' => 'Accept',
                    'created_by' => $sessData['id'],
                    'updated_by' => $sessData['id'],
//                    'id' => $oldId
                );
                if (isset($oldData[$branchId])) {
                    $this->BranchDealer->id = $saveData['id'] = $oldData[$branchId];
                } else {
                    $this->BranchDealer->create();
                }
                $this->BranchDealer->save($saveData);
                if (isset($oldData[$branchId])) {
                    unset($oldData[$branchId]);
                } else {
                    $sentArr['branches'][$this->BranchDealer->id] = $branchId;
                }
            }
        }
        /**
         * Delete old assigned clients
         */
        if (!empty($oldData)) {
            $this->BranchDealer->deleteAll(array('BranchDealer.id' => array_values($oldData)));
        }
        return $sentArr;
    }

    function assignClientToDealer($dealId = 0, $clients = array())
    {
        if (!empty($dealId) && !empty($clients)) {
            $this->DealerCompany = ClassRegistry::init('DealerCompany');
            $oldData = $this->DealerCompany->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => array('dealer_id' => $dealId)
                )
            );
            foreach ($clients as $clientId => $oldId) {
                $saveData = array(
                    'dealer_id' => $dealId,
                    'company_id' => $clientId,
                    'id' => $oldId
                );
                if (empty($oldId)) {
                    $this->DealerCompany->create();
                }
                $this->DealerCompany->save($saveData);
                $lastInId = $this->DealerCompany->id;
                if (isset($oldData[$lastInId])) {
                    unset($oldData[$lastInId]);
                }
            }
            if (!empty($oldData)) {
                $this->DealerCompany->deleteAll(array('DealerCompany.id' => $oldData));
            }
        } else {
            $oldData = $this->DealerCompany->find('list', array('fields' => 'id, id', 'contain' => false, 'conditions' => array('dealer_id' => $dealId)));
            if (!empty($oldData)) {
                $this->DealerCompany->deleteAll(array('DealerCompany.id' => $oldData));
            }
        }
    }

    function assignBranchToAdmin($admId = 0, $branches = array())
    {
        $this->BranchAdmin = ClassRegistry::init('BranchAdmin');
        $this->CompanyBranch = ClassRegistry::init('CompanyBranch');
        if (!empty($admId) && !empty($branches)) {
            $oldData = $this->BranchAdmin->find('list', array('fields' => 'id, id', 'contain' => false, 'conditions' => array('admin_id' => $admId)));
            $assignBranchList = $this->BranchAdmin->find('list', array('fields' => 'branch_id, branch_id', 'contain' => false, 'conditions' => array('admin_id' => $admId)));
            foreach ($branches as $branchId => $oldId) {
                $saveData = array(
                    'admin_id' => $admId,
                    'branch_id' => $branchId,
                    'id' => $oldId
                );
                if (empty($oldId)) {
                    $this->BranchAdmin->create();
                }
                $this->BranchAdmin->save($saveData);
                $lastInId = $this->BranchAdmin->id;
                if (isset($oldData[$lastInId])) {
                    unset($oldData[$lastInId]);
                }
                if (isset($assignBranchList[$branchId])) {
                    unset($assignBranchList[$branchId]);
                }
            }
            if (!empty($oldData)) {
                $this->BranchAdmin->deleteAll(array('BranchAdmin.id' => $oldData));
                $this->CompanyBranch->updateAll(array('CompanyBranch.admin_id' => 0), array('CompanyBranch.id' => $assignBranchList));
            }
        } else {
            if (!empty($admId) && empty($branches)) {
                //remove all branches
                $this->BranchAdmin->deleteAll(array('BranchAdmin.admin_id' => $admId));
                $this->CompanyBranch->updateAll(array('CompanyBranch.admin_id' => 0), array('CompanyBranch.admin_id' => $admId));
            }
        }
    }
function assignBranchToAdminNew($admId = 0, $branches = array())
    {
        $this->BranchAdmin = ClassRegistry::init('BranchAdmin');
        $this->CompanyBranch = ClassRegistry::init('CompanyBranch');
        if (!empty($admId) && !empty($branches)) {
            $oldData = $this->BranchAdmin->find('list', array('fields' => 'id, id', 'contain' => false, 'conditions' => array('admin_id' => $admId)));
            $assignBranchList = $this->BranchAdmin->find('list', array('fields' => 'branch_id, branch_id', 'contain' => false, 'conditions' => array('admin_id' => $admId)));
            
            if (!empty($oldData)) {
                $this->BranchAdmin->deleteAll(array('BranchAdmin.id' => $oldData));
                 
            } 
        } 
    }

    function getCompanyAdmins($id = null, $userRole = '')
    {
        $conditions = array(
            'NOT' => array(
                $this->alias . '.id' => $id,
                'AND' => array(
                    $this->alias . '.role' => ADMIN,
                    $this->alias . '.user_type' => SUPAR_ADM
                ),
                $this->alias . '.status' => 'deleted',
                $this->alias . '.user_type' => SUPAR_ADM
            ),
            $this->alias . '.parent_id' => $id,
            $this->alias . '.role' => COMPANY
        );
        if (!empty($userRole)) {
            $conditions = array(
                'NOT' => array(
                    $this->alias . '.id' => $id,
                    'AND' => array(
                        $this->alias . '.role' => ADMIN,
                        $this->alias . '.user_type' => SUPAR_ADM
                    ),
                    $this->alias . '.status' => 'deleted'
                ),
                $this->alias . '.user_type' => SUPAR_ADM,
                $this->alias . '.role' => COMPANY
            );
        }
        return $this->find('list', array('fields' => 'id, first_name', 'contain' => false, 'conditions' => $conditions));
    }

    function getUserDetail($userId = null)
    {
        $responseArr = array(
            'address' => '',
            'country' => '',
            'state' => '',
            'city' => '',
            'dealer' => '',
            'user' => array()
        );
        if ($this->exists($userId)) {
            $conditions = array(
                'User.id' => $userId
            );
            $contain = array(
                'Country' => array('fields' => 'id, name'),
                'State' => array('fields' => 'id, name'),
                'City' => array('fields' => 'id, name'),
                'Dealer' => array('fields' => 'id, first_name, last_name, name, email, phone_no')
            );
            $userDetail = $this->find('first', array('contain' => $contain, 'conditions' => $conditions));
            $addressArr = array(
                'address' => $userDetail['User']['address'],
                'country' => $userDetail['Country']['name'],
                'state' => $userDetail['State']['name'],
                'city' => $userDetail['City']['name'],
                'pincode' => $userDetail['User']['pincode']
            );
            $responseArr = array(
                'addressArr' => $addressArr,
                'dealer' => $userDetail['Dealer'],
                'User' => $userDetail
            );
        }
        return $responseArr;
    }

    function getMyCompanyList($userId = null, $userRole = null, $fields = 'User.id, User.first_name', $isCompany = false)
    {
        $sessData = getMySessionData();
        if (!empty($userId) && !empty($userRole)) {
            $conditions = array(
                'User.status' => 'active',
                'User.role' => $this->userRole['Company']
            );
            if ($userRole == ADMIN) {
                $conditions['User.user_type'] = SUPAR_ADM;
                $conditions['User.parent_id'] = 0;
                if (!isSuparAdmin()) {
                    $conditions['User.created_by'] = $userId;
                }
            }
            if ($userRole == DEALER) {
                $conditions['User.parent_id'] = 0;
                $conditions['User.dealer_id'] = $userId;
                $conditions['User.user_type'] = SUPAR_ADM;
            }
            if (!empty($isCompany) && in_array($userRole, array(SUPAR_ADM, ADMIN))) {
                $conditions = array(
                    'User.status' => 'active',
                    'User.role' => COMPANY,
                    'User.parent_id' => $userId
                );
            }
            return $this->find('list', array('contain' => false, 'fields' => $fields, 'conditions' => $conditions));
        }
    }

    function getCountOfMyClients($conditions = array())
    {
        $totalClient = 0;
        $sessData = getMySessionData();
        $conditions['User.status'] = 'active';
        $conditions['User.role'] = COMPANY;
        if ($sessData['role'] == ADMIN) {
            $conditions['User.user_type'] = SUPAR_ADM;
            $conditions['User.parent_id'] = 0;
            $conditions['User.created_by'] = $sessData['id'];
        }
        if ($sessData['role'] == DEALER) {
            $conditions['User.parent_id'] = 0;
            $conditions['User.dealer_id'] = $sessData['id'];
            $conditions['User.user_type'] = SUPAR_ADM;
        }
        if (!empty($isCompany) && in_array($sessData['role'], array(SUPAR_ADM, ADMIN))) {
            $conditions = array(
                'User.status' => 'active',
                'User.role' => COMPANY,
                'User.parent_id' => $sessData['id']
            );
        }
        $totalClient = $this->find('count', array('contain' => false, 'conditions' => $conditions));
        return $totalClient;
    }

    function getMySupportPerson()
    {
        $sessionData = getMySessionData();
        $userId = $sessionData['id'];
        $userLists = $this->find('list', array(
            'fields' => 'id, first_name',
            'conditions' => array(
                'NOT' => array(
                    'User.id' => $userId,
                    'AND' => array(
                        'User.role' => 'Admin',
                        'User.user_type' => 'Super'
                    ),
                    'User.status' => 'deleted'
                ),
                'User.parent_id' => $userId,
                'User.role' => 'Dealer',
                'User.user_type' => SUPPORT
            )
        ));
        return $userLists;
    }

    function getMyDealerList($userId = null, $userRole = null, $fields = 'User.id, User.first_name', $otherCondin = array())
    {
        $sessionData = getMySessionData();
        if (!empty($userId) && !empty($userRole)) {
            $conditions = array(
                'User.status' => 'active',
                'User.role' => $this->userRole['Dealer']
            );
//            $logUserType = CakeSession::read('Auth.User.user_type');
            $logUserType = $sessionData['user_type'];
            if ($userRole == ADMIN) {
                $conditions['User.user_type'] = SUPAR_ADM;
                $conditions['User.parent_id'] = 0;
                if ($logUserType != SUPAR_ADM) {
                    $conditions['User.created_by'] = $userId;
                }
            }
            if ($userRole == DEALER) {
                if ($logUserType == SUPAR_ADM) {
                    $conditions['User.parent_id'] = $userId;
                } elseif ($logUserType == ADMIN) {
//                    $conditions['User.parent_id'] = CakeSession::read('Auth.User.parent_id');
                    $conditions['User.parent_id'] = $sessionData['parent_id'];
                    $conditions['User.user_type'] = ADMIN;
                }
            }
            $conditions = array_merge($conditions, $otherCondin);
            return $this->find('list', array('contain' => false, 'fields' => $fields, 'conditions' => $conditions));
        }
    }

    function getMyBranchUsers($id = null)
    {
        $conditions = array(
            'NOT' => array(
                $this->alias . '.id' => $id,
                'AND' => array(
                    $this->alias . '.role' => ADMIN,
                    $this->alias . '.user_type' => SUPAR_ADM
                ),
                $this->alias . '.status' => 'deleted',
                $this->alias . '.user_type' => SUPAR_ADM
            ),
            $this->alias . '.parent_id' => $id,
            $this->alias . '.role' => COMPANY,
            $this->alias . '.user_type' => BRANCH
        );
        return $this->find('list', array('fields' => 'id, first_name', 'contain' => false, 'conditions' => $conditions));
    }

    function getMailDetails($uId = null)
    {
        $options = array(
            'contain' => false,
            'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
            'conditions' => array(
                'User.id' => $uId
            )
        );
        $userDetails = $this->find('first', $options);
        return !empty($userDetails) ? $userDetails['User'] : $userDetails;
    }

    function getParentId($userId = null)
    {
        $userDetail = $this->find('first', array('contain' => false, 'fields' => 'id, email, parent_id, dealer_id', 'conditions' => array('User.id' => $userId)));
        if (!empty($userDetail['User']['parent_id'])) {
            $userDetail = $this->getParentId($userDetail['User']['parent_id']);
        }
        return !empty($userDetail['User']) ? $userDetail['User'] : $userDetail;
    }

    function getLoginBranchUserId()
    {
        $sessionData = getMySessionData();
        if (CakeSession::check('Auth.User.BranchDetail.admin_id')) {
            return CakeSession::read('Auth.User.BranchDetail.admin_id');
        } else {
//            return CakeSession::check('Auth.User.id') ? CakeSession::read('Auth.User.id') : 0;
            return $sessionData['id'] ? $sessionData['id'] : 0;
        }
    }

    function getLoginDealerUserId()
    {
        $sessionData = getMySessionData();
        if (CakeSession::check('Auth.User.DealerDetail.id')) {
            return CakeSession::read('Auth.User.DealerDetail.id');
        } else {
//            return CakeSession::check('Auth.User.id') ? CakeSession::read('Auth.User.id') : 0;
            return $sessionData['id'] ? $sessionData['id'] : 0;
        }
    }

    function getAllSupportDealers($fields = 'id, first_name')
    {
        $conditions = array(
            'User.status' => 'active',
            'User.role' => DEALER,
            'User.user_type' => SUPPORT
        );
        return $this->find('list', array('contain' => false, 'fields' => $fields, 'conditions' => $conditions));
    }

    public function getCompanyFromBranchDetail($companyId)
    {
        $conditions = array(
            'contain' => false,
            'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
            'conditions' => array(
                'User.id' => $companyId
            )
        );
        $companyDetails = $this->find('first', $conditions);
        return isset($companyDetails['User']) ? $companyDetails['User'] : $companyDetails;
    }

    public function getAllAdminOfCompanyByCompanyId($companyId = null)
    {
        if (empty($companyId)) {
            return false;
        }
        return $this->find('list', array(
                'conditions' => array(
                    'User.parent_id' => $companyId,
                    'User.role' => 'Company',
                    'User.user_type' => 'Admin'
                ),
                'fields' => array('id', 'email'),
                'contain' => false
        ));
    }

    public function setDashboardData($reqData = array(), $isAjax = false)
    {
        $sessionData = getMySessionData();
        $responseArr = array(
            'totalClients' => 0,
            'totalTrans' => 0,
            'totalErros' => 0,
            'totalPFiles' => 0,
            'totalUnIdentiMsg' => 0,
        );
        $startDate = !empty($reqData['start_date']) ? $reqData['start_date'] : date('Y-m-d', strtotime('-7 days'));
        $endDate = !empty($reqData['end_date']) ? $reqData['end_date'] : date('Y-m-d');
        $totalTrans = $totalErros = $totalPFiles = 0;

        if (!isSuparAdmin()) {
//            $conditions['created_by'] = $this->Session->read('Auth.User.id');
            $conditions['created_by'] = $sessionData ['id'];
        }
        $totalClients = 0;
        $conditions = array('created >= ' => $startDate . ' 00:00:00', 'created <= ' => $endDate . ' 23:59:59');
        if (isAdminDealer() || isSuparDealer()) {
            $totalClients = $this->getCountOfMyClients($conditions);
        }
        //29-01-2016 default 7 day filter
        $conditions = array('FileProccessingDetail.file_date >= ' => $startDate, 'FileProccessingDetail.file_date <= ' => $endDate);
        $totalPFiles = ClassRegistry::init('FileProccessingDetail')->getCountProcessedFiles($conditions);
        $conditions = array('TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00', 'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59');
        $totalTrans = ClassRegistry::init('TransactionDetail')->getCountTransaction($conditions);
        unset($conditions['created_by']);
        unset($conditions['created_date >= ']);
        unset($conditions['created_date <= ']);

        //29-01-2016 default 7 day filter
        $conditions = array('entry_timestamp >= ' => $startDate . ' 00:00:00', 'entry_timestamp <= ' => $endDate . ' 23:59:59');

//        $totalErros = ClassRegistry::init('ErrorDetail')->getCountErroredFiles($conditions);
        $totalErros = ClassRegistry::init('ErrorTicket')->getCountErrorTickets($startDate,$endDate);

        $conditions = array('datetime >= ' => $startDate . ' 00:00:00', 'datetime <= ' => $endDate . ' 23:59:59');
        $totalUnIdentiMsg = ClassRegistry::init('Message')->getCountMessages($conditions);
        $responseArr = array(
            'totalClients' => $totalClients,
            'totalTrans' => $totalTrans,
            'totalErros' => $totalErros,
            'totalPFiles' => $totalPFiles,
            'totalUnIdentiMsg' => $totalUnIdentiMsg,
        );
        if ($isAjax) {
            if (!isCompany()) {
                $responseArr = array(
                    'status' => 'success',
                    'transactions' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalTrans' => $totalTrans
                    ),
                    'clients' => array(
                        'url' => Router::url(array('controller' => 'companies', 'action' => 'index'), true),
                        'totalClients' => $totalClients
                    ),
                    'errors' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalErros' => $totalErros
                    ),
                    'files' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalPFiles' => $totalPFiles
                    ),
                    'messages' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalUnIdentiMsg' => $totalUnIdentiMsg
                    )
                );
            } else {
                $responseArr = array(
                    'status' => 'success',
                    'transactions' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalTrans' => $totalTrans
                    ),
                    'clients' => array(
                        'url' => Router::url(array('controller' => 'companies', 'action' => 'index'), true),
                        'totalClients' => $totalClients
                    ),
                    'errors' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalErros' => $totalErros
                    ),
                    'files' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalPFiles' => $totalPFiles
                    ),
                    'messages' => array(
                        'url' => Router::url(array('action' => 'dashboard'), true),
                        'totalUnIdentiMsg' => $totalUnIdentiMsg
                    )
                );
            }
        }

        return $responseArr;
    }

    function saveDealerCompany($companyId = 0, $dealerId = 0, $oldDealerId = 0)
    {
        if (!empty($companyId) && !empty($dealerId)) {
            $this->DealerCompany = ClassRegistry::init('DealerCompany');
            $dealerCount = $this->DealerCompany->find('first', array(
                'contain' => false,
                'conditions' => array(
                    'DealerCompany.company_id' => $companyId,
                    'DealerCompany.dealer_id' => $dealerId,
                )
            ));
            if (empty($dealerCount)) {
                $this->DealerCompany->save(array(
                    'company_id' => $companyId,
                    'dealer_id' => $dealerId,
                ));
            }
            if (!empty($oldDealerId) && ($dealerId != $oldDealerId)) {
                $dealerCount = $this->DealerCompany->find('first', array(
                    'contain' => false,
                    'conditions' => array(
                        'DealerCompany.company_id' => $companyId,
                        'DealerCompany.dealer_id' => $oldDealerId,
                    )
                ));
                $this->DealerCompany->delete($dealerCount['DealerCompany']['id']);
            }
        }
    }

    function getDealerListHaveCompany()
    {
        $dealerCompany = ClassRegistry::init('DealerCompany')->find('list', array('fields' => 'dealer_id, dealer_id', 'contain' => false));
        $dealerList = $this->find('list', array(
            'fields' => 'id, id',
            'contain' => false,
            'conditions' => array(
//                'NOT' => array('User.dealer_id' => 0),
//                'User.dealer_id <>' => 0,
                'User.id' => $dealerCompany,
                'User.role' => DEALER,
                'User.user_type' => SUPAR_ADM
            )
        ));
        return $dealerList;
    }

    function deleteChildUser($userId = null)
    {
        if (!empty($userId)) {
            $getParentList = $this->find('list', array(
                'fields' => 'id, id',
                'conditions' => array(
                    'User.id' => $userId,
                    'User.user_type' => SUPAR_ADM
                )
            ));
            if (!empty($getParentList)) {
                $this->updateAll(
                    array(
                    'User.status' => '"deleted"',
                    ), array(
                    'User.parent_id' => $getParentList
                    )
                );
            }
        }
    }

    function getDealerAssignMailDetail($branchList = array())
    {
        $this->BranchDealer = ClassRegistry::init('BranchDealer');
        $branchDealers = $this->BranchDealer->find('all', array(
            'contain' => array(
                'Dealer' => array(
                    'fields' => array(
                        'id', 'first_name', 'last_name', 'email'
                    )
                ),
                'Branch' => array(
                    'fields' => array(
                        'id', 'name', 'email'
                    ),
                    'Company' => array(
                        'fields' => array(
                            'id', 'first_name', 'last_name', 'email'
                        )
                    )
                )
            ),
            'conditions' => array(
                'BranchDealer.id' => array_keys($branchList)
            )
        ));
        if (!empty($branchDealers)) {
            $branchDealers = Hash::combine($branchDealers, '{n}.BranchDealer.id', '{n}');
            return $branchDealers;
        }
        return false;        
    }
}
