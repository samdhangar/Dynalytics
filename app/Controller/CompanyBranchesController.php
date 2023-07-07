<?php
App::uses('AppController', 'Controller');

App::import('Vendor', 'PHPExcel');
if (!class_exists('PHPExcel')) {
    throw new CakeException('Vendor class PHPExcel not found!');
}

/**
 * CompanyBranches Controller
 *
 * @property CompanyBranch $CompanyBranch
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class CompanyBranchesController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Auth', 'Session');

    /**
     * beforefilter method
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
        $this->Auth->allow('acceptBranch');
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
        $respoConditions = $this->__getIndexConditions($this->params);
        $parentId = $respoConditions['parentId'];
        $parentDetails = $respoConditions['parentDetails'];
        $conditions = $respoConditions['conditions'];
        if ($all == "all") {
            $this->Session->write('CompanyBranchSearch', '');
        }
         $sessionData = getMySessionData();
        $this->loadModel('Region'); 
         $conditions2['company_id']=$sessionData['id'];
        if(!isCompany() && isset($this->request->data['Analytic']['company_id'])){
            //$conditions2['company_id']=$this->request->data['Analytic']['company_id'];
        }   
        $regiones = $this->Region->getRegionList($conditions2);
        
        $this->set(compact('regiones')); 
        if (empty($this->request->data['CompanyBranch']) && $this->Session->check('CompanyBranchSearch')) {
            $this->request->data['CompanyBranch'] = $this->Session->read('CompanyBranchSearch');
        }
        if (!empty($this->request->data['CompanyBranch'])) {
            $this->request->data['CompanyBranch'] = array_filter($this->request->data['CompanyBranch']);
            $this->request->data['CompanyBranch'] = array_map('trim', $this->request->data['CompanyBranch']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['CompanyBranch']['name'])) {
                    $conditions['OR'] = array(
                        'CompanyBranch.name LIKE ' => '%' . $this->request->data['CompanyBranch']['name'] . '%',
                        'CompanyBranch.contact_name LIKE ' => '%' . $this->request->data['CompanyBranch']['name'] . '%',
                    );
                }
                if (isset($this->request->data['CompanyBranch']['company_id'])) {
                    $conditions['CompanyBranch.company_id'] = $this->request->data['CompanyBranch']['company_id'];
                }
                if (isset($this->request->data['CompanyBranch']['email'])) {
                    $conditions['CompanyBranch.email LIKE '] = '%' . $this->request->data['CompanyBranch']['email'] . '%';
                }
                if (isset($this->request->data['CompanyBranch']['branch_status'])) {
                    $conditions['CompanyBranch.branch_status'] = $this->request->data['CompanyBranch']['branch_status'];
                }
                if (isset($this->request->data['CompanyBranch']['regiones'])) {
                    $conditions['CompanyBranch.regiones'] = $this->request->data['CompanyBranch']['regiones'];
                }
            }
            $this->Session->write('CompanyBranchSearch', $this->request->data['CompanyBranch']);
        }

        $sessionData = getMySessionData();
        if (!isAdmin()) {
            // echo "EHERE";exit;
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            $current_parent_id = '';
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
            }
            $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];

            $conditions['CompanyBranch.company_id'] = $company_id;
        }
        $this->AutoPaginate->setPaginate(array(
            'order' => ' CompanyBranch.id DESC',
            'conditions' => $conditions
        ));

       /*  $this->AutoPaginate->setPaginate(array(
             'contain' => array( 
                'Region' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' CompanyBranch.id DESC',
            'conditions' => $conditions
        ));*/
        $this->loadModel('CompanyBranch');
        $this->set('companyBranches', $this->paginate('CompanyBranch'));
        $this->set(compact('parentId', 'parentDetails')); 
    }

    public function my_branches($all = null)
    {
        $respoConditions = $this->__getIndexConditions($this->params);
        $sessData = getMySessionData();
        $dealerId = $sessData['id'];
        $parentId = $respoConditions['parentId'];
        $parentDetails = $respoConditions['parentDetails'];
        $conditions = $respoConditions['conditions'];

        $this->loadModel('BranchDealer');
        $branchDealer = $this->BranchDealer->find('list', array(
            'conditions' => array('dealer_id' => $dealerId, 'status' => 'Accept'),
            'fields' => array('branch_id', 'branch_id')
        ));

        $conditions['CompanyBranch.id'] = $branchDealer;
        unset($conditions['CompanyBranch.company_id']);

        if ($all == "all") {
            $this->Session->write('DealerBranchSearch', '');
        }
        if (empty($this->request->data['CompanyBranch']) && $this->Session->check('DealerBranchSearch')) {
            $this->request->data['CompanyBranch'] = $this->Session->read('DealerBranchSearch');
        }
        if (!empty($this->request->data['CompanyBranch'])) {
            $this->request->data['CompanyBranch'] = array_filter($this->request->data['CompanyBranch']);
            $this->request->data['CompanyBranch'] = array_map('trim', $this->request->data['CompanyBranch']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['CompanyBranch']['name'])) {
                    $conditions['OR'] = array(
                        'CompanyBranch.name LIKE ' => '%' . $this->request->data['CompanyBranch']['name'] . '%',
                        'CompanyBranch.contact_name LIKE ' => '%' . $this->request->data['CompanyBranch']['name'] . '%',
                    );
                }
                if (isset($this->request->data['CompanyBranch']['company_id'])) {
                    $conditions['CompanyBranch.company_id'] = $this->request->data['CompanyBranch']['company_id'];
                }
                if (isset($this->request->data['CompanyBranch']['email'])) {
                    $conditions['CompanyBranch.email LIKE '] = '%' . $this->request->data['CompanyBranch']['email'] . '%';
                }
                if (isset($this->request->data['CompanyBranch']['branch_status'])) {
                    $conditions['CompanyBranch.branch_status'] = $this->request->data['CompanyBranch']['branch_status'];
                }
            }
            $this->Session->write('DealerBranchSearch', $this->request->data['CompanyBranch']);
        }
        $this->AutoPaginate->setPaginate(array(
            'order' => ' CompanyBranch.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('CompanyBranch');
        $this->set('companyBranches', $this->paginate('CompanyBranch'));
        $this->set(compact('parentId', 'parentDetails'));
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {
        $id = decrypt($id);
        if (!$this->CompanyBranch->exists($id)) {
            $this->Message->setWarning(__('Invalid company branch'), array('action' => 'index'));
        }
        $this->loadModel('BranchAdmin');
//        $branchAdmins = $this->BranchAdmin->find('all', array(
        $this->AutoPaginate->setPaginate(array(
            'conditions' => array('branch_id' => $id),
            'fields' => array('BranchAdmin.id', 'BranchAdmin.created', 'BranchAdmin.admin_id', 'Admin.email', 'Admin.phone_no', 'Admin.user_type'),
            'contain' => array(
                'Admin' => array(
                    'fields' => array('Admin.first_name')
                )
            )
        ));

        $this->set('branchAdmins', $this->paginate('BranchAdmin'));

        $this->loadModel('FileProccessingDetail');
        $this->loadModel('TransactionDetail');
        $this->TransactionDetail->virtualFields['total_cash_deposit'] = "sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.other_cash_deposited,0))";
        $this->TransactionDetail->virtualFields['total_cash_withdrawal'] = "sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
        $this->AutoPaginate->setPaginate(array(
            'conditions' => array(
                'FileProccessingDetail.branch_id' => $id
            ),
            'fields' => array(
                'FileProccessingDetail.station',
                'FileProccessingDetail.file_date',
                'FileProccessingDetail.processing_endtime',
                'FileProccessingDetail.transaction_number',
                'FileProccessingDetail.total_deposit',
            ),
            'contain' => array(
                'ErrorDetail' => array(
                    'fields' => array(
                        'ErrorDetail.error_message'
                    )
                ),
                'TransactionDetail' => array(
                    'fields' => array(
                        'total_cash_deposit',
                        'total_cash_withdrawal'
                    )
                )
            )
        ));
        $this->set('fileProccessingDetails', $this->paginate('FileProccessingDetail'));
        $this->set('companyBranch', $this->CompanyBranch->getBranchDetail($id));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add($parentId = null)
    {
      

        $sessionData = getMySessionData();
        $parentId = decrypt($parentId);
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if ($this->request->is(array('post', 'put'))) {
            $this->CompanyBranch->create();
            $this->loadModel('User');

            $requestData = $this->request->data;
            
            if(isset($this->request->data['CompanyBranch']['regions'])){
                    $this->request->data['CompanyBranch']['regiones']=$this->request->data['CompanyBranch']['regions'];
                }
            if (!empty($this->request->data['CompanyBranch']['admin_id'])) {
                $this->User->id = $this->request->data['CompanyBranch']['admin_id'];
                $this->request->data['CompanyBranch']['company_id'] = $this->User->field('parent_id');
                //27-01-2016
                if (isSuparDealer() || isAdminDealer()) {
                    $this->request->data['CompanyBranch']['company_id'] = $this->request->data['CompanyBranch']['admin_id'];
                    $this->request->data['CompanyBranch']['admin_id'] = 0;
                }
            } else {
                $this->request->data['CompanyBranch']['admin_id'] = 0;
                if (isSuparCompany()) {
                    $this->request->data['CompanyBranch']['company_id'] = $sessionData['id'];
                }
                if (isCompanyAdmin()) {
                    $this->request->data['CompanyBranch']['company_id'] = $company_id;
                }
            }
            $this->request->data['CompanyBranch']['status'] = 1;
            $this->request->data['CompanyBranch']['email'] = date("ymdHis")."@addontechnologies.com";
            $this->request->data['CompanyBranch']['created_by'] = $this->request->data['CompanyBranch']['updated_by'] = $sessionData['id'];
            /**
             * reset ftp detail
             */
            if ($this->Session->check('Auth.User.ftp_detail')) {
                $this->request->data['CompanyBranch']['ftpuser'] = $this->Session->read('Auth.User.ftp_detail.ftp_username');
                $this->request->data['CompanyBranch']['ftp_pass'] = $this->Session->read('Auth.User.ftp_detail.ftp_password');
                $this->Session->delete('Auth.User.ftp_detail');
            }
            if ($this->CompanyBranch->save($this->request->data)) {
                $companyId = isset($this->request->data['CompanyBranch']['company_id']) ? $this->request->data['CompanyBranch']['company_id'] : 0;
                if (isSuparCompany() || isSuparAdmin()) {
                    //save branch admins
                    ClassRegistry::init('BranchAdmin')->setBranchAdmin($this->CompanyBranch->id, $this->request->data['CompanyBranch']['admin_id']);
                }
                //notify company supar admins
               
                $this->request->data['CompanyBranch']['company_id'] = $this->request->data['CompanyBranch']['admin_id']; //27-01-2016 

                if (!empty($this->request->data['CompanyBranch']['dealer_id'])) {
                    $branchDealer = array(
                        'dealer_id' => $this->request->data['CompanyBranch']['dealer_id'],
                        'branch_id' => $this->CompanyBranch->id
                    );
                    ClassRegistry::init('BranchDealer')->saveBranchDealer($branchDealer);
                    $dealerDetail = ClassRegistry::init('User')->getMailDetails($this->request->data['CompanyBranch']['dealer_id']);
                    if (isCompany()) {
                        $companyId = getCompanyId();
                    }
                    $companyDetail = ClassRegistry::init('User')->getCompanyFromBranchDetail($companyId);

                    $dealerNotifyData = array(
                        'Dealer' => $dealerDetail,
                        'Company' => $companyDetail,
                        'Branch' => array('name' => $this->request->data['CompanyBranch']['name']),
                        'notify_link' => Router::url(array('controller' => 'company_branches', 'action' => 'acceptBranch', encrypt(ClassRegistry::init('BranchDealer')->id)), true)
                    );

                    $this->SendEmail->sendBranchDealerNotifyEmail($dealerNotifyData);
                }
                if (!empty($companyId)) {
                    // $userDetails = ClassRegistry::init('User')->getMailDetails($companyId);
                    $options = array(
                        'contain' => false,
                        'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                        'conditions' => array(
                            'User.company_parent_id' => $companyId
                        )
                    );
                    $userDetails1 = $this->User->find('all', $options);
                    $userDetails = $this->User->find('all', array(
                        'contain' => false,
                        'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                        'conditions' => array(
                            'User.id' => $companyId
                        )));
                    $branchDetail = $this->CompanyBranch->find('first', array('contain' => ['Country','State','City'], 'conditions' => array('CompanyBranch.id' => $this->CompanyBranch->id)));
                    $address = !empty($branchDetail['CompanyBranch']['address']) ? $branchDetail['CompanyBranch']['address'].' , ' : '';
                    $address1 = !empty($branchDetail['CompanyBranch']['address2']) ? $branchDetail['CompanyBranch']['address2'].' , ' : '';
                    $country = !empty($branchDetail['Country']['name']) ? $branchDetail['Country']['name'].' , ' : '';
                    $state = !empty($branchDetail['State']['name']) ? $branchDetail['State']['name'].' , ' : '';
                    $city = !empty($branchDetail['City']['name']) ? $branchDetail['City']['name'].' , ' : '';
                    $zipcode = !empty($branchDetail['CompanyBranch']['zipcode']) ? $branchDetail['CompanyBranch']['zipcode'].'.' : '';
                    $branchDetail['CompanyBranch']['final_address'] = $address.''.$address1.''.$city.''.$state.''.$country.''.$zipcode;
                    $companyAdmins = $this->User->getAllAdminOfCompanyByCompanyId($sessionData['id']);
                    $finalUserDatas = array_merge($userDetails, $userDetails1);
                    foreach ($finalUserDatas as $key => $finalUserData) {
                        $arrData = array(
                            'User' => $finalUserData['User'],
                            'Branch' => $branchDetail['CompanyBranch']
                        );
                        $this->SendEmail->sendBranchNotifyEmail($arrData, 'add', $companyAdmins);
                    }
                   
                }
                $this->Message->setSuccess(__('The company branch has been saved.'));
                if (isDealer()) {
                    return $this->redirect(array('controller' => 'companies', 'action' => 'index'));
                }
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The company branch could not be saved. Please try again.'));
            }
            $this->request->data = $requestData;
        }
        $regions = $countries = $states = $cities = array();
        $countries = $this->CompanyBranch->Country->getCountryList();
          
         $this->loadModel('Region');
       
       $conditions['company_id']=$company_id;
         
        $regions = $this->Region->getRegionList($conditions);
      
          
        if (!empty($this->request->data['CompanyBranch']['country'])) {
            $states = $this->CompanyBranch->State->getStateList($this->request->data['CompanyBranch']['country']);
        }
        if (!empty($this->request->data['CompanyBranch']['state'])) {
            $cities = $this->CompanyBranch->City->getCityList($this->request->data['CompanyBranch']['state']);
        }
        if (empty($parentId)) {
//            $parentId = ((isSuparCompany() || isCompanyAdmin()) ? $this->Session->read('Auth.User.id') : $this->Session->read('Auth.User.parent_id'));
            $parentId = ((isSuparCompany() || isCompanyAdmin()) ? $sessionData['id'] : $sessionData['parent_id']);
        }
        if (empty($this->request->data['CompanyBranch']['admin_id']) && !empty($parentId)) {
            $this->request->data['CompanyBranch']['admin_id'] = $parentId;
        }
        $companies = array();
        if (isSuparAdmin()) {
            $companies = ClassRegistry::init('User')->getCompanyList();
            if (empty($this->request->data['CompanyBranch']['company_id']) && !empty($parentId)) {
                $this->request->data['CompanyBranch']['company_id'] = $parentId;
            }
            $admins = array();
            if (!empty($this->request->data['CompanyBranch']['company_id'])) {
                $admins = ClassRegistry::init('User')->getMyBranchUsers($this->request->data['CompanyBranch']['company_id']);
            }
        } else {
//            $admins = ClassRegistry::init('User')->getMyCompanyList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'));
            $admins = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role']);
            if (isCompany()) {
                $admins = $this->CompanyBranch->Company->getMyBranchUsers($parentId);
            }
        }
        $parentDetail = array();
        if (!empty($parentId)) {
            $parentDetail = ClassRegistry::init('User')->getMailDetails($parentId);
        }
        $ftpDetail = getBranchFtpDetail();
        $ftp_username = $ftpDetail['ftp_username'];
        $ftp_password = $ftpDetail['ftp_password'];
        $this->Session->write('Auth.User.ftp_detail', $ftpDetail);
        $dealers = ClassRegistry::init('User')->getDealerList(null, array('User.user_type' => SUPPORT));
        $this->set(compact('dealers'));
        $this->set(compact('parentDetail', 'companies', 'regions', 'countries', 'states', 'cities', 'admins', 'parentId', 'ftp_password', 'ftp_username'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $sessionData = getMySessionData();
        $id = decrypt($id);
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];

        $namedParam = getNamedParameter($this->request->params['named']);
        if (!$this->CompanyBranch->exists($id)) {
            $this->Message->setWarning(__('Invalid company branch'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->loadModel('User');
            $parentDetail = $this->User->getParentId($this->request->data['CompanyBranch']['admin_id']);
            $requestData = $this->request->data;

               if(isset($this->request->data['CompanyBranch']['regions'])){
                    $this->request->data['CompanyBranch']['regiones']=$this->request->data['CompanyBranch']['regions'];
                }
            if (!empty($this->request->data['CompanyBranch']['admin_id'])) {
                $this->User->id = $this->request->data['CompanyBranch']['admin_id'];
                $parentId = $this->request->data['CompanyBranch']['company_id'] = $parentDetail['id'];
                  if (isSuparDealer() || isAdminDealer()) {
                    $this->request->data['CompanyBranch']['company_id'] = $this->request->data['CompanyBranch']['admin_id'];
                    $this->request->data['CompanyBranch']['admin_id'] = 0;
                }
            } else {
                $this->request->data['CompanyBranch']['admin_id'] = 0;
                if (isSuparCompany()) { 
                    $this->request->data['CompanyBranch']['company_id'] = $sessionData['id'];
                }
                if (isCompanyAdmin()) { 
                    $parentDetail = $this->User->getParentId($sessionData['id']);
                    $this->request->data['CompanyBranch']['company_id'] = $parentDetail['id'];
                }
            }

            if (!empty($this->request->data['CompanyBranch']['admin_id'])) {
                if (empty($this->request->data['CompanyBranch']['contact_name']) || $this->request->data['CompanyBranch']['contact_name'] == 'Select Branch Admin') {
                    ClassRegistry::init('User')->id = $this->request->data['CompanyBranch']['admin_id'];
                    $this->request->data['CompanyBranch']['contact_name'] = ClassRegistry::init('User')->field('last_name');
                }
            }
            $options = array(
                'contain' => array('BranchDealer' => array('fields' => array('id', 'dealer_id'))),
                'conditions' => array('CompanyBranch.id' => $this->request->data['CompanyBranch']['id']));
            $oldData = $this->CompanyBranch->find('first', $options);
            $this->request->data['CompanyBranch']['email'] = !empty($oldData['CompanyBranch']['email']) ? $oldData['CompanyBranch']['email'] : '';
            $branchDealerId = isset($oldData['BranchDealer'][0]['id']) ? $oldData['BranchDealer'][0]['id'] : '';
            $oldDealerId = isset($oldData['BranchDealer'][0]['dealer_id']) ? $oldData['BranchDealer'][0]['dealer_id'] : '';
            if (isset($this->request->data['CompanyBranch']['ftpuser'])) {
                unset($this->request->data['CompanyBranch']['ftpuser']);
            }
            if (isset($this->request->data['CompanyBranch']['ftp_pass'])) {
                unset($this->request->data['CompanyBranch']['ftp_pass']);
            }
            if ($this->CompanyBranch->save($this->request->data)) {
                $companyId = isset($this->request->data['CompanyBranch']['company_id']) ? $this->request->data['CompanyBranch']['company_id'] : 0;
                
                if (!empty($this->request->data['CompanyBranch']['dealer_id']) && ($oldDealerId != $this->request->data['CompanyBranch']['dealer_id'])) {
                    /*
                     * sent notification to dealer
                     */
//                    if ($branchDealerId) {
                        $branchDealer = array(
                            'dealer_id' => $this->request->data['CompanyBranch']['dealer_id'],
                            'branch_id' => $this->CompanyBranch->id,
                            'note' => '',
                            'status' => 'Sent'
                        );
                        ClassRegistry::init('BranchDealer')->updateBranchDealer($branchDealer, $branchDealerId);
//                        if($response){
                            $dealerDetail = $this->User->getMailDetails($this->request->data['CompanyBranch']['dealer_id']);
                            if (isCompany()) {
                                $companyId = getCompanyId();
                            }
                            $companyDetail = $this->User->getCompanyFromBranchDetail($companyId);
                            $dealerNotifyData = array(
                                'Dealer' => $dealerDetail,
                                'Company' => $companyDetail,
                                'Branch' => array('name' => $this->request->data['CompanyBranch']['name']),
                                'notify_link' => Router::url(array('controller' => 'company_branches', 'action' => 'acceptBranch', encrypt(ClassRegistry::init('BranchDealer')->id)), true)
                            );
                            $this->SendEmail->sendBranchDealerNotifyEmail($dealerNotifyData);
//                        }
//                    }
                }
                /**
                 * save History Data
                 */
                if (!empty($oldData)) {
                    $histData = array(
                        'ref_id' => $this->CompanyBranch->id,
                        'ref_model' => 'CompanyBranch',
                        'content' => json_encode($oldData)
                    );
                    ClassRegistry::init('History')->saveHistoryData($histData);
                }

                if (isSuparCompany() || isSuparAdmin()) {
                    //save branch admins
                    ClassRegistry::init('BranchAdmin')->setBranchAdmin($this->CompanyBranch->id, $this->request->data['CompanyBranch']['admin_id']);
                }
                //notify company supar admins
                if (!empty($companyId)) {
                    $options = array(
                        'contain' => false,
                        'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                        'conditions' => array(
                            'User.company_parent_id' => $companyId
                        )
                    );
                    $userDetails1 = $this->User->find('all', $options);
                    $userDetails = $this->User->find('all', array(
                        'contain' => false,
                        'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                        'conditions' => array(
                            'User.id' => $companyId
                        )));
                    $branchDetail = $this->CompanyBranch->find('first', array('contain' => ['Country','State','City'], 'conditions' => array('CompanyBranch.id' => $this->CompanyBranch->id)));
                    $address = !empty($branchDetail['CompanyBranch']['address']) ? $branchDetail['CompanyBranch']['address'].' , ' : '';
                    $address1 = !empty($branchDetail['CompanyBranch']['address2']) ? $branchDetail['CompanyBranch']['address2'].' , ' : '';
                    $country = !empty($branchDetail['Country']['name']) ? $branchDetail['Country']['name'].' , ' : '';
                    $state = !empty($branchDetail['State']['name']) ? $branchDetail['State']['name'].' , ' : '';
                    $city = !empty($branchDetail['City']['name']) ? $branchDetail['City']['name'].' , ' : '';
                    $zipcode = !empty($branchDetail['CompanyBranch']['zipcode']) ? $branchDetail['CompanyBranch']['zipcode'].'.' : '';
                    $branchDetail['CompanyBranch']['final_address'] = $address.''.$address1.''.$city.''.$state.''.$country.''.$zipcode;
                    $companyAdmins = $this->User->getAllAdminOfCompanyByCompanyId($sessionData['id']);
                    $finalUserDatas = array_merge($userDetails, $userDetails1);
                    foreach ($finalUserDatas as $key => $finalUserData) {
                        $arrData = array(
                            'User' => $finalUserData['User'],
                            'Branch' => $branchDetail['CompanyBranch']
                        );
                        $this->SendEmail->sendBranchNotifyEmail($arrData, 'edit', $companyAdmins);
                    }
                }
                $this->Message->setSuccess(__('The company branch has been updated.'));
                return $this->redirect(array('action' => 'index', $namedParam));
            } else {
                $this->request->data = $requestData;
                $this->Message->setWarning(__('The company branch could not be updated. Please try again.'));
            }
        } else {
            $options = array(
                'contain' => false,
                'conditions' => array('CompanyBranch.' . $this->CompanyBranch->primaryKey => $id));
            $this->request->data = $this->CompanyBranch->find('first', $options);
            $parentId = $this->request->data['CompanyBranch']['company_id'];
            if (!empty($this->request->data['CompanyBranch']['admin_id'])) {
                if (empty($this->request->data['CompanyBranch']['contact_name']) || $this->request->data['CompanyBranch']['contact_name'] == 'Select Branch Admin') {
                    ClassRegistry::init('User')->id = $this->request->data['CompanyBranch']['admin_id'];
                    $this->request->data['CompanyBranch']['contact_name'] = ClassRegistry::init('User')->field('last_name');
                }
            }
        }
        $regions = $countries = $states = $cities = array();
        $countries = $this->CompanyBranch->Country->getCountryList();
        if (!empty($this->request->data['CompanyBranch']['country'])) {
            $states = $this->CompanyBranch->State->getStateList($this->request->data['CompanyBranch']['country']);
        }
        if (!empty($this->request->data['CompanyBranch']['state'])) {
            $cities = $this->CompanyBranch->City->getCityList($this->request->data['CompanyBranch']['state']);
        }
        if (empty($parentId)) {
            $parentId = ((isSuparCompany() || isCompanyAdmin()) ? $sessionData['id'] : $sessionData['parent_id']);
        }
        $admins = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role']);
        $companies = array();
        if (isSuparAdmin()) {
            $companies = ClassRegistry::init('User')->getCompanyList();
            if (empty($this->request->data['CompanyBranch']['company_id']) && !empty($parentId)) {
                $this->request->data['CompanyBranch']['company_id'] = $parentId;
            }
            $admins = array();
            if (!empty($this->request->data['CompanyBranch']['company_id'])) {
                $admins = ClassRegistry::init('User')->getMyBranchUsers($this->request->data['CompanyBranch']['company_id']);
            }
        } else {
//            $admins = ClassRegistry::init('User')->getMyCompanyList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'));
            $admins = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role']);
            if (isCompany()) {
                $admins = $this->CompanyBranch->Company->getMyBranchUsers($parentId);
            }
        }
        $parentDetail = array();
        if (!empty($parentId)) {
            $parentDetail = ClassRegistry::init('User')->getMailDetails($parentId);
        }
       
        $this->request->data['CompanyBranch']['regions']=$this->request->data['CompanyBranch']['regiones'];
         $this->loadModel('Region');
       $conditions['company_id']=$company_id;
         
        $regions = $this->Region->getRegionList($conditions);
    
        $this->set(compact('companies', 'parentDetail', 'admins', 'regions', 'countries', 'states', 'cities', 'parentId'));
        $this->set('edit', 1);
        $dealers = ClassRegistry::init('User')->getDealerList(null, array('User.user_type' => SUPPORT));
        $this->set(compact('dealers'));
        $selectedDealer = ClassRegistry::init('BranchDealer')->find('first', array(
            'contain' => false,
            'fields' => 'BranchDealer.dealer_id',
            'conditions' => array(
                'BranchDealer.dealer_id' => array_keys($dealers),
                'BranchDealer.branch_id' => $id
            )
        ));
        if (!empty($selectedDealer) && empty($this->request->data['CompanyBranch']['dealer_id'])) {
            $this->request->data['CompanyBranch']['dealer_id'] = $selectedDealer['BranchDealer']['dealer_id'];
        }
        $this->render('add');

    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        $this->loadModel('User');
        $sessionData = getMySessionData();
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
       
        if ($this->request->is(array('post', 'put'))) {
            $this->CompanyBranch->updateAll(array('branch_status' => "'deleted'"), array('CompanyBranch.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The branch has been deleted.'), $this->referer());
        }

        $id = decrypt($id);
        $this->CompanyBranch->id = $id;
        if (!$this->CompanyBranch->exists()) {
            $this->Message->setWarning(__('Invalid company branch'), array('action' => 'index'));
        }

        if ($this->CompanyBranch->saveField('branch_status', 'deleted')) {
            //27-01-2016 Send mail to user 
            if (!empty($id)) {
                $isSuparCompany = isSuparCompany();
                $isCompanyAdmin = isCompanyAdmin();
                $companyId = '';
                if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                    $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
                    $companyId = $current_parent_id['User']['company_parent_id'];
                }elseif (!empty($isSuparCompany) && empty($isCompanyAdmin)) {
                    $companyId = $sessionData['id'];
                }
                $options = array(
                    'contain' => false,
                    'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                    'conditions' => array(
                        'User.company_parent_id' => $companyId
                    )
                );
                $userDetails1 = $this->User->find('all', $options);
                $userDetails = $this->User->find('all', array(
                    'contain' => false,
                    'fields' => 'id, first_name, last_name, name, email, phone_no, role, user_type, status',
                    'conditions' => array(
                        'User.id' => $companyId
                    )));
                $branchDetail = $this->CompanyBranch->find('first', array('contain' => ['Country','State','City'], 'conditions' => array('CompanyBranch.id' => $this->CompanyBranch->id)));
                $address = !empty($branchDetail['CompanyBranch']['address']) ? $branchDetail['CompanyBranch']['address'].' , ' : '';
                $address1 = !empty($branchDetail['CompanyBranch']['address2']) ? $branchDetail['CompanyBranch']['address2'].' , ' : '';
                $country = !empty($branchDetail['Country']['name']) ? $branchDetail['Country']['name'].' , ' : '';
                $state = !empty($branchDetail['State']['name']) ? $branchDetail['State']['name'].' , ' : '';
                $city = !empty($branchDetail['City']['name']) ? $branchDetail['City']['name'].' , ' : '';
                $zipcode = !empty($branchDetail['CompanyBranch']['zipcode']) ? $branchDetail['CompanyBranch']['zipcode'].'.' : '';
                $branchDetail['CompanyBranch']['final_address'] = $address.''.$address1.''.$city.''.$state.''.$country.''.$zipcode;
                $companyAdmins = $this->User->getAllAdminOfCompanyByCompanyId($sessionData['id']);
                $finalUserDatas = array_merge($userDetails, $userDetails1);
                foreach ($finalUserDatas as $key => $finalUserData) {
                    $arrData = array(
                        'User' => $finalUserData['User'],
                        'Branch' => $branchDetail['CompanyBranch']
                    );
                    $this->SendEmail->sendBranchNotifyEmail($arrData, 'delete', $companyAdmins);
                }
            }
            $this->Message->setSuccess(__('The company branch has been deleted.'));
        } else {
            $this->Message->setWarning(__('The company branch could not be deleted. Please try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    function change_status($branchId = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of branch'));
        if ($this->CompanyBranch->exists($branchId) && !empty($status)) {
            $this->CompanyBranch->id = $branchId;
            $this->CompanyBranch->saveField('branch_status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __('Branch status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }

    public function export($parentId = null)
    {
        $sessionData = getMySessionData();
        if (!empty($parentId)) {
            $params['named'][COMPANY] = $parentId;
        }
        $respoConditions = $this->__getIndexConditions($params);
        $conditions = $respoConditions['conditions'];
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions['CompanyBranch.company_id'] = $company_id;
        $this->layout = false;
        $success = 0;
        $error = 0;
        $countries = $this->CompanyBranch->Country->getCountryList();
        $states = $this->CompanyBranch->State->getStateList();
        $cities = $this->CompanyBranch->City->getCityList();
        if ($this->Session->read('CompanyBranchSearch')) {
            $appCondition = $this->Session->read('CompanyBranchSearch');
        }
        if (!empty($appCondition)) {
            $appCondition = array_map('trim', array_filter($appCondition));
            if (!empty($appCondition)) {
                $appCondition = array_map('trim', $appCondition);
                if (!empty($appCondition)) {
                    if (isset($appCondition['name'])) {
                        $conditions['OR'] = array(
                            'CompanyBranch.name LIKE ' => '%' . $appCondition['name'] . '%',
                            'CompanyBranch.contact_name LIKE ' => '%' . $appCondition['name'] . '%',
                        );
                    }
                    if (isset($appCondition['company_id'])) {
                        $conditions['CompanyBranch.company_id'] = $appCondition['company_id'];
                    }
                    if (isset($appCondition['email'])) {
                        $conditions['CompanyBranch.email LIKE '] = '%' . $appCondition['email'] . '%';
                    }
                    if (isset($appCondition['branch_status'])) {
                        $conditions['CompanyBranch.branch_status'] = $appCondition['branch_status'];
                    }
                }
            }
        }
        $allUsers = ClassRegistry::init('User')->getUserList();
        $users = $this->CompanyBranch->find('all', array('fields' => array('CompanyBranch.*'), 'contain' => false, 'order' => 'CompanyBranch.id DESC', 'conditions' => $conditions));
        $message = __('No any branch found');
        if (!empty($users)) {
            $objPHPExcel = new PHPExcel();
            $sheetName = date('Y_m_d_H_i_s') . '_Branch';
//            $objPHPExcel->getProperties()->setCreator($this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name'));
            $objPHPExcel->getProperties()->setCreator($sessionData['first_name'] . ' ' . $sessionData['last_name']);
            $objPHPExcel->getProperties()->setTitle($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setSubject($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setDescription($sheetName . " Spreadsheet");
            //set header
            $objPHPExcel->setActiveSheetIndex(0);
            $excelHeader = array(
                'company_id' => 'Company Name',
                'name' => 'Branch Name',
                'contact_name' => 'Contact Name',
                'country' => 'Country',
                'city' => 'City',
                'state' => 'State',
                // 'email' => 'Email',
                'phone' => 'Phone No',
                'branch_status' => 'Branch Status',
                'created' => 'Added on'
                //	'address' => 'Address',
                //    'ftpuser' => 'FTP User',
                //    'ftp_pass' => 'FTP Password',
            );

            $excelHeaderWidth = array(
                'company_id' => '15',
                'name' => '15',
                'contact_name' => '15',
                'country' => '10',
                'city' => '10',
                'state' => '10',
                // 'email' => '33',
                'phone' => '15',
                'branch_status' => 15,
                'created' => '20'
                //'address' => '40',
                //'ftpuser' => '10',
                //'ftp_pass' => '25',
            );
            if (isCompany()) {
                unset($excelHeader['company_id']);
                unset($excelHeaderWidth['company_id']);
                unset($excelHeader['country']);
                unset($excelHeaderWidth['country']);
            }
            $col = 'A';
            foreach ($excelHeader as $key => $header) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($excelHeaderWidth[$key]);
                $objPHPExcel->getActiveSheet()->setCellValue($col . '1', $header);
                $col++;
            }
            $appCount = 2;
            foreach ($users as $user) {
                $col = 'A';
                foreach ($excelHeader as $key => $value) {
                    if ($key == 'country') {
                        $user['CompanyBranch'][$key] = isset($countries[$user['CompanyBranch'][$key]]) ? $countries[$user['CompanyBranch'][$key]] : 'a';
                    }
                    if ($key == 'state') {
                        $user['CompanyBranch'][$key] = isset($states[$user['CompanyBranch'][$key]]) ? $states[$user['CompanyBranch'][$key]] : '';
                    }
                    if ($key == 'city') {
                        $user['CompanyBranch'][$key] = isset($cities[$user['CompanyBranch'][$key]]) ? $cities[$user['CompanyBranch'][$key]] : '';
                    }
                    if ($key == 'company_id') {
                        $user['CompanyBranch'][$key] = isset($allUsers[$user['CompanyBranch'][$key]]) ? $allUsers[$user['CompanyBranch'][$key]] : '';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($col . $appCount, $user['CompanyBranch'][$key]);
                    $arr[$key] = $user['CompanyBranch'][$key];
                    $col++;
                }
                $appCount++;
            }
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $sheetName = $sheetName . '.xlsx';
            $filename = TMP . "export" . DS . $sheetName;
            $objWriter->save($filename);
            if (is_file($filename)) {
                $this->response->file($filename, array('download' => true, 'name' => $sheetName));
                return $this->response;
                exit;
            }
            $message = __('Unable to export branches.Please try again');
        }
        $this->Message->setWarning($message, $this->referer());
    }

    function __getIndexConditions($namedParams = array())
    {
        $sessionData = getMySessionData();
        $responseArr = array(
            'parentId' => 0,
            'parentDetails' => array(),
            'conditions' => array('NOT' => array('CompanyBranch.branch_status' => 'deleted'))
//            'conditions' => array('NOT' => array('CompanyBranch.is_list_display' => 0, 'CompanyBranch.branch_status' => 'deleted'))
        );
        if (!empty($namedParams['named'][COMPANY])) {
            $responseArr['parentId'] = decrypt($namedParams['named'][COMPANY]);
            $fields = 'id, first_name, last_name, email, phone_no';
            $responseArr['parentDetails'] = ClassRegistry::init('User')->find('first', array('fields' => $fields, 'contain' => false, 'conditions' => array('User.id' => $responseArr['parentId'])));
            if (!empty($responseArr['parentDetails']['User'])) {
                $responseArr['parentDetails'] = $responseArr['parentDetails']['User'];
            }
            $responseArr['conditions']['CompanyBranch.company_id'] = $responseArr['parentId'];
        }
        if (isSuparCompany()) {
//            $responseArr['conditions']['company_id'] = $this->Session->read('Auth.User.id');
            $responseArr['conditions']['CompanyBranch.company_id'] = $sessionData['id'];
        }
        if (!isSuparAdmin()) {
            $fields = 'User.id, User.id';
//            $companies = ClassRegistry::init('User')->getMyCompanyList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], $fields);
            $responseArr['conditions']['CompanyBranch.company_id'] = $companies;
        }
        if (isCompany()) {
            $responseArr['conditions']['CompanyBranch.company_id'] = $sessionData['id'];
        }
        return $responseArr;
    }

    /**
     * get the list of the branches with the option tag
     * @param type $companyId
     */
    function get_branches($companyId = null)
    {
        $this->layout = false;
        $branchList = $this->CompanyBranch->getAllBranch1($companyId);
        $addData = $branchList;
        $addComboTitle = __('Select All');
        $addDataTitle = __('No any branch');
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
    }

    function get_branches_list($companyId = null)
    {
        $this->layout = false;
        $branchList = $this->CompanyBranch->getMyBranchLists($companyId);
         
        return $branchList;
    }

     function get_region($companyId = null)
    {
        $this->layout = false;
          $this->loadModel('Region');
       $conditions['company_id']=$companyId;
        $branchList = $this->Region->getRegionList($conditions);

       // $branchList = $this->CompanyBranch->getMyBranchLists($companyId);
        $addData = $branchList;
        $addComboTitle = __('Select All');
        $addDataTitle = __('No any branch');
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
    }
    
    /**
     * Get assgin branch
     * @param type $companyId
     */
    function get_assign_branches($companyId = null)
    {
        $this->layout = false;
        $branchList = $this->CompanyBranch->getMyBranchLists($companyId);
        $this->set(compact('branchList'));
        $this->render('/Elements/user/get_assign_branches');
    }

    /**
     * get the list of the stations with the option tag
     * @param type $branchId
     */
    function get_stations($branchId = null)
    {
        $this->layout = false;
        $addData = array();
        if($branchId != NULL)
        {
            $stationList = $this->CompanyBranch->getStationList($this->request->data);
            // foreach($stationList as $key=>$value){
            //     $stationList[$key]= $value.'('.$key.')';
            //     }
            $addData = $stationList;
        }
        $addComboTitle = __('Select All');
        $addDataTitle = __('No any Station');
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
    }

    /**
     * get the list of file having station id is this
     * @param type $stationId
     */
    function get_files($stationId = null)
    {
        $this->layout = false;
        $fileDateList = ClassRegistry::init('FileProccessingDetail')->getFileDateList($this->request->data);
        $addData = $fileDateList;
        $addComboTitle = __('Select File Date');
        $addDataTitle = __('No any File Processed');
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
    }

    public function acceptBranch($branchDealerId = null)
    {
        $sessData = getMySessionData();
        $this->layout = 'login';
        if (empty($branchDealerId)) {
            $this->Message->setWarning(__('Invalid link, Please try again.'), '/');
        }
        $this->BranchDealer = ClassRegistry::init('BranchDealer');
        $branchDealerId = decrypt($branchDealerId);

        $brnachDealerData = $this->BranchDealer->find('first', array(
            'conditions' => array(
                'BranchDealer.id' => $branchDealerId,
                'BranchDealer.status' => 'Sent'
            ),
            'fields' => array('id', 'branch_id', 'dealer_id'),
            'contain' => array(
                'Branch' => array(
                    'fields' => array('name'),
                    'Company' => array(
                        'fields' => array('first_name as name')
                    )
                )
            ),
            'recursive' => -1
        ));
        if (empty($brnachDealerData)) {
            $this->Message->setWarning(__('Invalid link, Please try again.'), '/');
        }

        if ($this->request->is('post')) {
            $this->BranchDealer->id = $branchDealerId;
            $status = ($this->request->data['CompanyBranch']['decision']) ? 'Accept' : 'Reject';
            $error = false;
            $data = array(
                'BranchDealer' => array(
                    'status' => $status,
                    'note' => ''
                )
            );
            if (!$this->request->data['CompanyBranch']['decision']) {
                if (empty($this->request->data['CompanyBranch']['note'])) {
                    $error = true;
                } else {
                    $data['BranchDealer']['note'] = $this->request->data['CompanyBranch']['note'];
                }
            }
            if ($error) {
                $this->Message->setWarning(__('Please enter Note.'));
            } else {
                $data['BranchDealer']['updated_by'] = $sessData['id'];
                if ($this->BranchDealer->save($data)) {

                    $dealerDetail = ClassRegistry::init('User')->getMailDetails($brnachDealerData['BranchDealer']['dealer_id']);
                    $companyDetail = ClassRegistry::init('User')->getCompanyFromBranchDetail($brnachDealerData['Branch']['company_id']);

                    $companyNotifyData = array(
                        'Dealer' => $dealerDetail,
                        'Company' => $companyDetail,
                        'Branch' => array('name' => $brnachDealerData['Branch']['name']),
                        'BranchDealer' => array(
                            'status' => __($status),
                            'note' => $data['BranchDealer']['note']
                        )
                    );
                    $this->SendEmail->sendNotificationEmailToCompany($companyNotifyData);
                    $this->Message->setSuccess(__('You have successfully ' . $status . ' the Branch.'), '/');
                } else {
                    $this->Message->setWarning(__('Unable to ' . $status . ' the Branch, Please try again.'));
                }
            }
        }
        $this->set(compact('brnachDealerData'));
    }
    // to get list of station on base of branch on inventory management view
    function get_branchstations($branchId = null)
    {
        $this->layout = false;
        
        $stationList = $this->CompanyBranch->getStationList($branchId);
        $addData = $stationList;
        $addComboTitle = __('Select Station');
        $addDataTitle = __('No any Station');
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
    }
}
