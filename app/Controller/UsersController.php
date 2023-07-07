<?php

/**
 * Description of UsersController
 *
 * @author Securemetasys
 */
App::uses('AppController', 'Controller');
App::import('Controller', 'Analytics'); // mention at top
App::import('Vendor', 'PHPExcel');
App::uses('AuthComponent', 'Controller/Component');
if (!class_exists('PHPExcel')) {
    throw new CakeException('Vendor class PHPExcel not found!');
}

class UsersController extends AppController
{
    public $components = array('Auth', 'SendEmail');
    public $type = 'company_user';
    public $usedController = 'users';
    public $pageTitle = ' Company User ';
    public $pageSubTitle = ' Company User ';
    public $subType = 'sub_company';

    public function authenticate($data) {
        $this->loadModel('User');
        $user = $this->User->find('first', array(
            'conditions' => array('User.email' => $data['User']['email'], 'User.status' => 'active'),
        ));
        if($user['User']['password'] !== AuthComponent::password($data['User']['password'])) {
            return false;
        }
        unset($user['User']['password']);  // don't forget this part
        $this->Auth->login($user['User']);
        return $user;
        // the reason I return the user is so I can pass it to Authcomponent::login if desired
    }

    public function beforeFilter()
    {

        $this->_checkLogin();
        $this->Auth->allow(array('login', 'email_read'));
        $this->set('genders', $this->User->getGender());
        $this->set('userTypes', $this->User->getTypes($this->type));
        $this->set('userStatus', $this->User->getStatuses());
        if ($this->type == COMPANY) {
            $this->set('subscriptions', ClassRegistry::init('Subscription')->getSubscriptionList(COMPANY));
        } elseif ($this->type == DEALER) {
            $this->set('subscriptions', ClassRegistry::init('Subscription')->getSubscriptionList(DEALER));
        } else {
            $this->set('subscriptions', ClassRegistry::init('Subscription')->getSubscriptionList());
        }
        //        $this->set('memberships', getMemberShipType());
        $this->set('communicationTypes', getCommunicationType());

        $pageDetailWithRole = array(
            'pageTitle' => $this->pageTitle,
            'pageSubTitle' => $this->pageSubTitle,
            'usedController' => $this->usedController,
            'role' => $this->type,
            'breadCrumbName' => $this->breadcrumb,
            'singularTitle' => Inflector::singularize(trim($this->pageTitle))
        );
        $this->set('userRole', $this->User->userRole);
        $this->set(compact('pageDetailWithRole'));
        parent::beforeFilter();
    }
    function __getDateRanges($timeRange = null)
    {
        if (empty($timeRange)) {
            $timeRange = Configure::read('Site.filterOption');
        }


        //        $timeRange = 'last_6months';
        $retArray = array(
            'xAxisDates' => array(),
            'tickInterval' => 1,
            'start_date' => date('Y-m-d', strtotime('-6 days')),
            'end_date' => date('Y-m-d'),
            'from' => $timeRange
        );
        if ($timeRange == 'last_10days') {
            $retArray['xAxisDates'] = date_range(date('Y-m-d', strtotime('-9 days')), date('Y-m-d'), '+1 day');
        } elseif ($timeRange == 'last_months') {
            $retArray['tickInterval'] = 4;
            $startDate = date('Y-m-d', strtotime('-1 month'));
            //            $startDate = date('Y-m-01', strtotime('-1 month'));
            //            $endDate = date('Y-m-t', strtotime('-1 month'));//28-01-2016
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_3months') {
            $retArray['tickInterval'] = 13;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_12months') {
            $retArray['tickInterval'] = 52;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif($timeRange == "last_18days"){
            $retArray['tickInterval'] = 5;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }else {
            $retArray['xAxisDates'] = [date('Y-m-d')];
        }

        // if ($timeRange == 'last_7days') {
        //     $retArray['xAxisDates'] = date_range(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'), '+1 day');
        // } elseif ($timeRange == 'last_15days') {
        //     $retArray['tickInterval'] = 2;
        //     $startDate = date('Y-m-d', strtotime('-14 days'));
        //     $endDate = date('Y-m-d');
        //     $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        //     $retArray['start_date'] = $startDate;
        //     $retArray['end_date'] = $endDate;
        // } elseif ($timeRange == 'last_months') {
        //     $retArray['tickInterval'] = 4;
        //     $startDate = date('Y-m-d', strtotime('-1 month'));
        //     //            $startDate = date('Y-m-01', strtotime('-1 month'));
        //     //            $endDate = date('Y-m-t', strtotime('-1 month'));//28-01-2016
        //     $endDate = date('Y-m-d');
        //     $retArray['start_date'] = $startDate;
        //     $retArray['end_date'] = $endDate;
        //     $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        // } elseif ($timeRange == 'last_3months') {
        //     $retArray['tickInterval'] = 13;
        //     $startDate = date('Y-m-01', strtotime('-3 month'));
        //     $startDate = date('Y-m-d', strtotime('-3 month'));
        //     $endDate = date('Y-m-t', strtotime('-1 month'));
        //     $endDate = date('Y-m-d');
        //     $retArray['start_date'] = $startDate;
        //     $retArray['end_date'] = $endDate;
        //     $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        // } elseif ($timeRange == 'last_6months') {
        //     $retArray['tickInterval'] = 26;
        //     $startDate = date('Y-m-01', strtotime('-6 month'));
        //     $startDate = date('Y-m-d', strtotime('-6 month'));
        //     $endDate = date('Y-m-t', strtotime('-1 month'));
        //     $endDate = date('Y-m-d');
        //     $retArray['start_date'] = $startDate;
        //     $retArray['end_date'] = $endDate;
        //     $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        // } else {
        //     $retArray['xAxisDates'] = [date('Y-m-d')];
        // }
        return $retArray;
    }
    function getQuarter($date,$title)
    {
        $time=strtotime($date);
        $m = date("m",$time);
        $y=date("Y",$time);
        if($title == 'last_3months'){
            switch($m) {
                case $m >= 1 && $m <= 3:
                    $start = '01/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('03/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 4 && $m <= 6:
                    $start = '04/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('06/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 7 && $m <= 9:
                    $start = '07/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('09/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 10 && $m <= 12:
                    $start = '10/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('12/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
            }
        }elseif ($title == 'last_12months') {
            $start = '01/01/'.$y;
            $end = date('m/d/'.$y);
        }elseif ($title == "last_months") {
            $start = date("m/d/".$y, strtotime("first day of previous month"));
            $end = date("m/d/".$y, strtotime("last day of previous month"));
        }elseif ($title == "last_18days") {
            $start = date('m/01/Y'); // hard-coded '01' for first day
            $end  = date("m/d/Y");
        }
        $start=date_create($start);
        $start = date_format($start,"Y-m-d");
        $end=date_create($end);
        $end = date_format($end,"Y-m-d");
        return array(
                'start' => $start,
                'end' => $end,
        );
       
    }
    function __getConditions($sessionName = null, $reqData = array(), $model = '')
    {
        $from = !empty($reqData['from']) ? $reqData['from'] : '';

        if (empty($reqData['from']) && $this->Session->check('Report.' . $sessionName)) {
            $from = $this->Session->read('Report.' . $sessionName . '.from');
        }

        $dateRange = $this->__getDateRanges($from);
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];
        $type = '';
        if (isset($reqData['from'])) {
            if ($reqData['from'] == 'customrange') {
                $type = 'customrange';
                $startDate_new = $reqData['start_date'];
                // echo '<pre><b>' . __FILE__ . ' (Line:'. __LINE__ .')</b><br>';
                // print_r($startDate_new);echo '<br>';exit;
                $endDate_new = $reqData['end_date'];
                $dateRange['start_date'] = $reqData['start_date'];
                $dateRange['end_date'] = $reqData['end_date'];
            }
        }
        $retArr = array(
            'from' => $dateRange['from'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'xAxisDates' => $dateRange['xAxisDates'],
            'tickInterval' => $dateRange['tickInterval'],
            'conditions' => array(
                'created_date >= ' => $startDate,
                'created_date <= ' => $endDate
            )
        );

        if (!empty($sessionName) && $type != 'customrange') {
            if ($this->Session->check('Report.' . $sessionName) && empty($reqData)) {
                $reqData = $this->Session->read('Report.' . $sessionName);
            }
            if (!empty($reqData)) {
                $retArr['from'] = !empty($reqData['from']) ? $reqData['from'] : $retArr['from'];
                $dateRange = $this->__getDateRanges($retArr['from']);
                $startDate = $retArr['start_date'] = !empty($dateRange['start_date']) ? $dateRange['start_date'] : $retArr['start_date'];
                $endDate = $retArr['end_date'] = !empty($dateRange['end_date']) ? $dateRange['end_date'] : $retArr['end_date'];
                $retArr['xAxisDates'] = $dateRange['xAxisDates'];
                $retArr['tickInterval'] = $dateRange['tickInterval'];
                $retArr['conditions'] = array(
                    'created_date >= ' => $startDate,
                    'created_date <= ' => $endDate
                );
            }
        } else if ($type == 'customrange') {
            if ($this->Session->check('Report.' . $sessionName) && empty($reqData)) {
                $reqData = $this->Session->read('Report.' . $sessionName);
            }
            if (!empty($reqData)) {
                $retArr['from'] = !empty($reqData['from']) ? $reqData['from'] : $retArr['from'];
                $dateRange = $this->__getDateRanges($retArr['from']);
                $startDate = $retArr['start_date'] = $startDate_new;
                $endDate = $retArr['end_date'] = $endDate_new;
                $retArr['xAxisDates'] =  date_range($startDate_new, $endDate_new, '+1 day');
                $total = sizeof($retArr['xAxisDates']);
                $ticket = ceil($total / 7);
                $retArr['tickInterval'] = $ticket;
                $retArr['conditions'] = array(
                    'created_date >= ' => $startDate_new,
                    'created_date <= ' => $endDate_new
                );
            }
        }
        if (!empty($model)) {
            $retArr['conditions'][$model . '.created_date >= '] = $retArr['conditions']['created_date >= '];
            $retArr['conditions'][$model . '.created_date <= '] = $retArr['conditions']['created_date <= '];
            unset($retArr['conditions']['created_date >= ']);
            unset($retArr['conditions']['created_date <= ']);
        }
        return $retArr;
    }

    function login()
    {
        $this->layout = "login";
        if ($this->Session->read('Auth.User.id')) {
            return $this->redirect($this->Auth->loginRedirect);
        }
        if ($this->request->is('post')) {
            if ($this->authenticate($this->request->data)) {
                $status = $this->Session->read('Auth.User.status');
                if ($status == 'active') {
                    // Process After Login
                    $this->User->id = $this->Session->read('Auth.User.id');
                    $this->User->saveField('last_login_time', date('Y-m-d H:i:s'));
                    $this->__setUserSession();
                    $this->loadModel('AuditLog');
                    $this->AuditLog->addUserLog('login');
                    $this->Message->setSuccess(__("Login Successfully."));
                    return $this->redirect($this->Auth->loginRedirect);
                } else {
                    $this->Auth->logout();
                    $this->Session->destroy();
                    $this->Message->setWarning(__('Your account is not active'));
                }
            } else {
                $this->Message->setWarning(__('Invalid Username/Password'));
            }

            // if ($this->Auth->login()) {
            //     $status = $this->Session->read('Auth.User.status');
            //     if ($status == 'active') {
            //         // Process After Login
            //         $this->User->id = $this->Session->read('Auth.User.id');
            //         $this->User->saveField('last_login_time', date('Y-m-d H:i:s'));
            //         $this->__setUserSession();
            //         $this->loadModel('AuditLog');
            //         $this->AuditLog->addUserLog('login');
            //         $this->Message->setSuccess(__("Login Successfully."));
            //         return $this->redirect($this->Auth->loginRedirect);
            //     } else {
            //         $this->Auth->logout();
            //         $this->Session->destroy();
            //         $this->Message->setWarning(__('Your account is not active'));
            //     }
            // } else {
            //     $this->Message->setWarning(__('Invalid Username/Password'));
            // }
        }
        $this->set('title_for_layout', Configure::read('Site.Name'));
    }

    function __setUserSession()
    {
        $userId = $this->Session->read('Auth.User.id');
        if ($this->User->exists($userId)) {
            $this->User->id = $userId;
            $userDetail = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $userId)));
        
            $this->Session->write('Auth.User', $userDetail['User']);
            if (isCompany()) {
                $this->loadModel('BranchAdmin');
                $assignedBranches = $this->BranchAdmin->find('list', array(
                    'contain' => array('Branch' => array('fields' => array('id', 'name'))),
                    'conditions' => array('BranchAdmin.admin_id' => $userDetail['User']['id']),
                    'fields' => array('branch_id', 'Branch.name')
                ));
                $this->Session->write('Auth.User.assign_branches', $assignedBranches);
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $userId)));
                $this->Session->write('Auth.User.company_parent_id', $current_parent_id['User']['company_parent_id']);
            }
            if (isDealer()) {
                $this->loadModel('DealerCompany');
                $fromUsers = $this->User->getCompanyList($userDetail['User']['id']);
                $assignedCompanies = $this->DealerCompany->find('list', array(
                    'contain' => array('Company' => array('fields' => array('id', 'first_name'))),
                    'conditions' => array('DealerCompany.dealer_id' => $userDetail['User']['id']),
                    'fields' => array('company_id', 'Company.first_name')
                ));
                $assignedCompanies = $assignedCompanies + $fromUsers;
                $this->Session->write('Auth.User.assign_companies', $assignedCompanies);
            }
        }
    }

    function logout()
    {
        $this->loadModel('AuditLog');
        $this->AuditLog->addUserLog('logout');
        $this->Auth->logout();
        $this->Session->destroy();
        $this->redirect($this->Auth->logoutRedirect);
    }

    public function profile()
    {
        if ($this->request->is(array('post', 'put'))) {
            if ($this->User->save($this->request->data)) {
                $this->__setUserSession();
                $this->Message->setSuccess(__('Profile Updated Successfully.'), array('action' => 'profile'));
            }
            if (!empty($this->User->validationErrors)) {
                $this->set('validationErrors', $this->User->validationErrors);
            }
            $this->Message->setWarning(__('Unable to update the detail.'));
        }
        if (empty($this->request->data)) {
            $userDetailArr = $this->User->getUserDetail($this->Session->read('Auth.User.id'));
            $this->request->data = $record = $userDetailArr['User'];

            $this->User->recursive = -1;
            $this->set('dealer', $userDetailArr['User']['Dealer']);

            $this->User->Country->id = $record['User']['country_id'];
            $country = $this->User->Country->field('name');

            $this->User->State->id = $record['User']['state_id'];
            $state = $this->User->State->field('name');

            $this->User->City->id = $record['User']['city_id'];
            $city = $this->User->City->field('name');

            $user_address = array(
                'Country' => $country,
                'State' => $state,
                'City' => $city
            );
            $this->set('user_address', $user_address);
        }
        $countries = $states = $cities = array();
        $countries = $this->User->Country->getCountryList();
        if (!empty($this->request->data['User']['country_id'])) {
            $states = $this->User->State->getStateList($this->request->data['User']['country_id']);
        }
        if (!empty($this->request->data['User']['state_id'])) {
            $cities = $this->User->City->getCityList($this->request->data['User']['state_id']);
        }
        $this->set(compact('countries', 'states', 'cities'));
    }

    // public function add($parentId = 0)
    // {
    //     $sessionData = getMySessionData();
    //     $this->__checkPrevents('add');

    //     $parentId = decrypt($parentId);
    //     if ($this->request->is('post')) {
    //         $this->User->create();

    //         $this->request->data['User']['role'] = $this->type;

    //         if (!isset($this->request->data['User']['regions'])) {
    //             $this->request->data['User']['regiones'] = $this->request->data['CompanyBranch']['regions'];
    //         }


    //         $this->request->data['User']['status'] = 'active';
    //         if (empty($this->request->data['User']['dealer_id'])) {
    //             $this->request->data['User']['dealer_id'] = 0;
    //         }
    //         if (empty($parentId)) {
    //             $this->request->data['User']['parent_id'] = 0;
    //             if (empty($parentId)) {
    //                 if ((isAdminDealer() || isSupportDealer() || isSuparDealer()) && $this->type == DEALER) {
    //                     $this->request->data['User']['parent_id'] = $sessionData['id'];
    //                 }
    //             }
    //         } else {
    //             if (empty($this->request->data['User']['parent_id'])) {
    //                 $this->request->data['User']['parent_id'] = $parentId;
    //             } else {
    //                 $parentId = $this->request->data['User']['parent_id'];
    //             }
    //         }

    //         if ($this->type == COMPANY) {
    //             if (isSuparDealer()) {
    //                 $this->request->data['User']['dealer_id'] = $sessionData['id'];
    //             }
    //             if (isSuparCompany() || isCompanyAdmin()) {
    //                 $this->request->data['User']['parent_id'] = $sessionData['id'];
    //                 $this->request->data['User']['dealer_id'] = $sessionData['dealer_id'];
    //             }
    //         }
    //         $this->request->data['User']['updated_by'] = $this->request->data['User']['created_by'] = $sessionData['id'];
    //         $this->request->data['User']['password'] = getrandompassword();
    //         $this->request->data['User']['confirm_password'] = $this->request->data['User']['password'];
    //         if (!isDealer() && $this->type == COMPANY && empty($this->request->data['User']['parent_id'])) {
    //             $this->request->data['User']['is_display_billing'] = 1;
    //         }

    //         $regions = array('id' => null, 'region_id' => $this->request->data['User']['regions'], 'admin_id' => '3');



    //         if (isset($this->request->data['User']['regions'])) {
    //             $this->request->data['User']['regiones'] = $this->request->data['User']['regions'];
    //         }
    //         if ($sessionData['user_type'] == 'Super' && $sessionData['role'] == 'Admin') {
    //             $this->request->data['User']['user_type'] = $sessionData['user_type'];
    //         }
    //         if ($this->User->save($this->request->data)) {
    //             $this->Session->write('lastcompanyId', $this->User->id);
    //             $lastID = $this->User->id; //used for assigning dealer and company to support admin
    //             if ($this->type == COMPANY && !empty($this->request->data['User']['dealer_id'])) {
    //                 $this->User->saveDealerCompany($lastID, $this->request->data['User']['dealer_id']);
    //             }
    //             /**
    //              * Assign Client and branches to the support dealers
    //              */
    //             if (((isDealer() && !isSupportDealer()) || isAdmin()) && $this->type == DEALER && $this->request->data['User']['user_type'] == SUPPORT) {
    //                 $sentMailUsers = $this->User->assignClientBranchToDealer($this->User->id, $this->request->data['User']);
    //             }

    //             if (!isset($this->request->data['User']['admins']) && isset($this->request->data['User']['regions'])) {
    //                 $this->request->data['User']['admins'] = $this->request->data['User']['regions'];
    //             }

    //             if ((isSuparCompany() || isSuparAdmin() || isAdminAdmin()) && $this->type == COMPANY && (isCompany() || !empty($parentId)) && isset($this->request->data['User']['admins'])) {
    //                 //assign Branch to dealer
    //                 $this->User->assignBranchToAdmin($this->User->id, $this->request->data['User']['admins']);
    //             }
    //             //sent mail to user for the account created successfully.
    //             $this->request->data['User']['acc_type'] = $this->type;
    //             if ($this->type == COMPANY) {
    //                 $this->SendEmail->sendCompanyCreatedEmail($this->request->data['User']);
    //             } else {
    //                 $this->SendEmail->sendAccountCreatedEmail($this->request->data['User']);
    //             }
    //             //sent branch added mail to company
    //             if ($this->type == COMPANY && $this->request->data['User']['user_type'] == SUPAR_ADM) {
    //                 $this->request->data['User']['id'] = $lastID;
    //                 $this->CompanyBranch = ClassRegistry::init('CompanyBranch');
    //                 $branchId = $this->CompanyBranch->createBranchFromCompany($this->request->data['User']);
    //                 if (!empty($lastID)) {
    //                     $userDetails = $this->User->getMailDetails($lastID);
    //                     $branchDetail = $this->CompanyBranch->find('first', array('contain' => false, 'conditions' => array('CompanyBranch.id' => $branchId)));
    //                     $arrData = array(
    //                         'User' => $userDetails,
    //                         'Branch' => $branchDetail['CompanyBranch']
    //                     );
    //                     $this->SendEmail->sendBranchNotifyEmail($arrData, 'add');
    //                 }
    //             }

    //             //Assigning support admin to dealer and company
    //             if (isset($this->request->data['User']['multidealer_id']) && isset($this->request->data['User']['multicompany_id'])) {
    //                 $reqData = array(
    //                     'admin_id' => $lastID,
    //                     'multidealer_id' => $this->request->data['User']['multidealer_id'],
    //                     'multicompany_id' => $this->request->data['User']['multicompany_id']
    //                 );

    //                 ClassRegistry::init('AdminUser')->saveSupportAdminUsers($reqData);
    //             }
    //             $this->Message->setSuccess(__('The ' . $this->type . ' has been saved.'));

    //             if (!empty($parentId)) {
    //                 return $this->redirect(array('action' => 'upload_branch', $this->type => encrypt($parentId)));
    //             }
    //             return $this->redirect(array('action' => 'upload_branch'));
    //         } else {
    //             $this->Message->setWarning(__('The ' . $this->type . ' could not be saved. Please, try again.'));
    //         }
    //     }

    //     $this->request->data['User']['parent_id'] = $parentId;
    //     $isAdmin = ($this->type == $this->User->userRole['Admin']);
    //     $parents = $this->User->getParentsList($parentId);
    //     if ($this->type == DEALER) {
    //         $fields = 'id, name';
    //         //            $parents = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
    //         $parents = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
    //     }

    //     if ($this->type == COMPANY) {
    //         $conditions = array();
    //         if (isSuparDealer()) {
    //             //                $conditions['User.dealer_id'] = $this->Session->read('Auth.User.id');
    //             $conditions['User.dealer_id'] = $sessionData['id'];
    //         }
    //         $parents = $this->User->getSuparCompanyList($conditions);
    //     }

    //     $dealCondition = array();
    //     if (isAdminDealer() || isAdminAdmin()) {
    //         //            $dealCondition['User.created_by'] = $this->Session->read('Auth.User.id');
    //         $dealCondition['User.created_by'] = $sessionData['id'];
    //     }
    //     if ($this->type == COMPANY) {
    //         if (isAdminAdmin() || isSuparAdmin()) {
    //             //                $dealCondition['User.user_type'] = SUPAR_ADM;
    //             $dealCondition['User.user_type != '] = SUPPORT;
    //         }
    //     }
    //     if (isSuparAdmin() && $this->type == ADMIN) {
    //         $dealCondition = array(
    //             'User.user_type' => SUPAR_ADM

    //         );
    //     }
    //     if (isSuparAdmin() && $this->type == COMPANY) {
    //         $dealCondition = array(
    //             'User.user_type' => SUPAR_ADM,
    //             'User.role' => DEALER
    //         );
    //     }
    //     $dealers = $this->User->getDealerList(0, $dealCondition);

    //     if (isSuparAdmin() && $this->type == ADMIN) {
    //         $this->set('multidealer', $dealers);
    //     }

    //     if (isSupportAdmin() && $this->type == COMPANY) {
    //         $fields = 'User.id, User.name';
    //         //            $dealers = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
    //         $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
    //     }
    //     if (isSuparDealer() || isAdminDealer()) {
    //         $dealers = array();
    //     }
    //     $countries = $states = $cities = array();
    //     $countries = $this->User->Country->getCountryList();
    //     if (!empty($this->request->data['User']['country_id'])) {
    //         $states = $this->User->State->getStateList($this->request->data['User']['country_id']);
    //     }
    //     if (!empty($this->request->data['User']['state_id'])) {
    //         $cities = $this->User->City->getCityList($this->request->data['User']['state_id']);
    //     }
    //     $this->set('from', 'Add');
    //     //set flag for display user type or not
    //     if (empty($parentId)) {
    //         if ((isAdminDealer() || isSupportDealer()) && $this->type == DEALER) {
    //             //                $parentId = $this->Session->read('Auth.User.id');
    //             $parentId = $sessionData['id'];
    //         }
    //     }

    //     $this->loadModel('Region');

    //     if (isSuparDealer()) {
    //         unset($conditions['User.dealer_id']);
    //         $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
    //     }
    //     $conditions['company_id'] = $sessionData['id'];

    //     $regions = $this->Region->getRegionList($conditions);

    //     $isDisplayUserType = $this->__getUserTypeStatus($parentId);

    //     $isDisplayDealer = $this->__getDealerStatus($parentId);
    //     $isDisplayCompanyParent = $this->__getParentCompanyStatus($parentId);
    //     $this->set(compact('isDisplayUserType', 'regions', 'isDisplayDealer', 'isDisplayCompanyParent'));

    //     $this->set(compact('namedParamter', 'countries', 'states', 'cities', 'parentId', 'isAdmin', 'parents', 'dealers'));


    //     $multiSelectedCompanyList = $multiSelectedDealerList = $multicompany = array();
    //     $multiSelectedCompanyList = json_encode($multiSelectedCompanyList);
    //     $multiSelectedDealerList = json_encode($multiSelectedDealerList);
    //     $this->set(compact('multiSelectedCompanyList', 'multiSelectedDealerList', 'multicompany'));
    // }
    public function add($parentId = 0)
    {
        $sessionData = getMySessionData();
        $this->__checkPrevents('add');
        $parentId = decrypt($parentId);
        if ($this->request->is('post')) {

            $this->User->create();

            $this->request->data['User']['role'] = $this->type;
            if (!isset($this->request->data['User']['regions'])) {
                $this->request->data['User']['regiones'] = $this->request->data['CompanyBranch']['regions'];
            }

            $this->request->data['User']['status'] = 'active';
            if (empty($this->request->data['User']['dealer_id'])) {
                $this->request->data['User']['dealer_id'] = 0;
            }
            if (empty($parentId)) {
                $this->request->data['User']['parent_id'] = 0;
                if (empty($parentId)) {
                    if ((isAdminDealer() || isSupportDealer() || isSuparDealer()) && $this->type == DEALER) {
                        $this->request->data['User']['parent_id'] = $sessionData['id'];
                    }
                }
            } else {
                if (empty($this->request->data['User']['parent_id'])) {
                    $this->request->data['User']['parent_id'] = $parentId;
                } else {
                    $parentId = $this->request->data['User']['parent_id'];
                }
            }

            if ($this->type == COMPANY) {
                if (isSuparDealer()) {
                    $this->request->data['User']['dealer_id'] = $sessionData['id'];
                }
                if (isSuparCompany() || isCompanyAdmin()) {
                    $isSuparCompany = isSuparCompany();
                    $isCompanyAdmin = isCompanyAdmin();
                    $this->request->data['User']['parent_id'] = $sessionData['id'];
                    $this->request->data['User']['company_parent_id'] = $sessionData['id'];
                    $this->request->data['User']['dealer_id'] = $sessionData['dealer_id'];
                    if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                        $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
                        $this->request->data['User']['company_parent_id'] = $current_parent_id['User']['company_parent_id'];
                    }
                  
                }
            }
            $this->request->data['User']['updated_by'] = $this->request->data['User']['created_by'] = $sessionData['id'];
            $this->request->data['User']['password'] = getrandompassword();
            $this->request->data['User']['confirm_password'] = $this->request->data['User']['password'];
            if (!isDealer() && $this->type == COMPANY && empty($this->request->data['User']['parent_id'])) {
                $this->request->data['User']['is_display_billing'] = 1;
            }

            $regions = array('id' => null, 'region_id' => $this->request->data['User']['regions'], 'admin_id' => '3');



            if (isset($this->request->data['User']['regions'])) {
                $this->request->data['User']['regiones'] = $this->request->data['User']['regions'];
            }
            $this->request->data['User']['is_setup'] = 0;
            if ($this->User->save($this->request->data)) {
                $this->Session->write('lastcompanyId', $this->User->id);
                $lastID = $this->User->id; //used for assigning dealer and company to support admin
                if ($this->type == COMPANY && !empty($this->request->data['User']['dealer_id'])) {
                    $this->User->saveDealerCompany($lastID, $this->request->data['User']['dealer_id']);
                }
                /**
                 * Assign Client and branches to the support dealers
                 */
                if (((isDealer() && !isSupportDealer()) || isAdmin()) && $this->type == DEALER && $this->request->data['User']['user_type'] == SUPPORT) {
                    $sentMailUsers = $this->User->assignClientBranchToDealer($this->User->id, $this->request->data['User']);
                }

                if (!isset($this->request->data['User']['admins']) && isset($this->request->data['User']['regions'])) {
                    $this->request->data['User']['admins'] = $this->request->data['User']['regions'];
                }

                if ((isSuparCompany() || isSuparAdmin() || isAdminAdmin()) && $this->type == COMPANY && (isCompany() || !empty($parentId)) && isset($this->request->data['User']['admins'])) {
                    //assign Branch to dealer
                    $this->User->assignBranchToAdmin($this->User->id, $this->request->data['User']['admins']);
                }
                //sent mail to user for the account created successfully.
                $this->request->data['User']['acc_type'] = $this->type;
                if ($this->type == COMPANY) {
                    $this->SendEmail->sendCompanyCreatedEmail($this->request->data['User']);
                } else {
                    $this->SendEmail->sendAccountCreatedEmail($this->request->data['User']);
                }
                //sent branch added mail to company
                // if ($this->type == COMPANY && $this->request->data['User']['user_type'] == SUPAR_ADM) {
                //     $this->request->data['User']['id'] = $lastID;
                //     $this->CompanyBranch = ClassRegistry::init('CompanyBranch');
                //     $branchId = $this->CompanyBranch->createBranchFromCompany($this->request->data['User']);
                //     if (!empty($lastID)) {
                //         $userDetails = $this->User->getMailDetails($lastID);
                //         $branchDetail = $this->CompanyBranch->find('first', array('contain' => false, 'conditions' => array('CompanyBranch.id' => $branchId)));
                //         $arrData = array(
                //             'User' => $userDetails,
                //             'Branch' => $branchDetail['CompanyBranch']
                //         );
                //         $this->SendEmail->sendBranchNotifyEmail($arrData, 'add');
                //     }
                // }

                //Assigning support admin to dealer and company
                if (isset($this->request->data['User']['multidealer_id']) && isset($this->request->data['User']['multicompany_id'])) {
                    $reqData = array(
                        'admin_id' => $lastID,
                        'multidealer_id' => $this->request->data['User']['multidealer_id'],
                        'multicompany_id' => $this->request->data['User']['multicompany_id']
                    );

                    ClassRegistry::init('AdminUser')->saveSupportAdminUsers($reqData);
                }
                $this->Message->setSuccess(__('The ' . $this->type . ' has been saved.'));
                if (!empty($parentId)) {
                    return $this->redirect(array('action' => 'upload_branch', $this->type => encrypt($parentId)));
                }
                // if ($sessionData['role'] == 'Admin' && $sessionData['user_type'] == 'Super' && (!empty($this->request->data['User']['user_type']) && $this->request->data['User']['user_type'] != 'Admin')) {
                //     return $this->redirect(array('action' => 'upload_branch'));   
                // }else{
                //     return $this->redirect(array('action' => 'index'));
                // }
                if(!empty($this->type) && ($this->type == "Dealer")){
                    // return $this->redirect(array('action' => 'dealers'));
                    return $this->redirect(Router::url(array('controller' =>'dealers', 'action' => 'index'), true));
                }elseif (!empty($this->type) && ($this->type == "Admin")) {
                    return $this->redirect(Router::url(array('controller' =>'admins', 'action' => 'index'), true));
                }elseif (!empty($this->type) && ($this->type == "Company")) {
                    return $this->redirect(Router::url(array('controller' =>'companies', 'action' => 'index'), true));
                }else{
                    return $this->redirect(array('action' => 'upload_branch'));   
                }
            } else {
                $this->Message->setWarning(__('The ' . $this->type . ' could not be saved. Please, try again.'));
            }
        }

        $this->request->data['User']['parent_id'] = $parentId;
        $isAdmin = ($this->type == $this->User->userRole['Admin']);
        $parents = $this->User->getParentsList($parentId);
        if ($this->type == DEALER) {
            $fields = 'id, name';
            //            $parents = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
            $parents = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
        }

        if ($this->type == COMPANY) {
            $conditions = array();
            if (isSuparDealer()) {
                //                $conditions['User.dealer_id'] = $this->Session->read('Auth.User.id');
                $conditions['User.dealer_id'] = $sessionData['id'];
            }
            $parents = $this->User->getSuparCompanyList($conditions);
        }

        $dealCondition = array();
        if (isAdminDealer() || isAdminAdmin()) {
            //            $dealCondition['User.created_by'] = $this->Session->read('Auth.User.id');
            $dealCondition['User.created_by'] = $sessionData['id'];
        }
        if ($this->type == COMPANY) {
            if (isAdminAdmin() || isSuparAdmin()) {
                //                $dealCondition['User.user_type'] = SUPAR_ADM;
                $dealCondition['User.user_type != '] = SUPPORT;
            }
        }
        if (isSuparAdmin() && $this->type == ADMIN) {
            $dealCondition = array(
                'User.user_type' => SUPAR_ADM

            );
        }
        if (isSuparAdmin() && $this->type == COMPANY) {
            $dealCondition = array(
                'User.user_type' => SUPAR_ADM,
                'User.role' => DEALER
            );
        }
        $dealers = $this->User->getDealerList(0, $dealCondition);

        if (isSuparAdmin() && $this->type == ADMIN) {
            $this->set('multidealer', $dealers);
        }

        if (isSupportAdmin() && $this->type == COMPANY) {
            $fields = 'User.id, User.name';
            //            $dealers = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
            $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
        }
        if (isSuparDealer() || isAdminDealer()) {
            $dealers = array();
        }
        $countries = $states = $cities = array();
        $countries = $this->User->Country->getCountryList();
        if (!empty($this->request->data['User']['country_id'])) {
            $states = $this->User->State->getStateList($this->request->data['User']['country_id']);
        }
        if (!empty($this->request->data['User']['state_id'])) {
            $cities = $this->User->City->getCityList($this->request->data['User']['state_id']);
        }
        $this->set('from', 'Add');
        //set flag for display user type or not
        if (empty($parentId)) {
            if ((isAdminDealer() || isSupportDealer()) && $this->type == DEALER) {
                //                $parentId = $this->Session->read('Auth.User.id');
                $parentId = $sessionData['id'];
            }
        }

        $this->loadModel('Region');

        if (isSuparDealer()) {
            unset($conditions['User.dealer_id']);
            $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
        }
        $conditions['company_id'] = $sessionData['id'];
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $conditions['company_id'] = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
        $regions = $this->Region->getRegionList($conditions);

        $isDisplayUserType = $this->__getUserTypeStatus($parentId);
        $isDisplayDealer = $this->__getDealerStatus($parentId);
        $isDisplayCompanyParent = $this->__getParentCompanyStatus($parentId);
        $this->set(compact('isDisplayUserType', 'regions', 'isDisplayDealer', 'isDisplayCompanyParent'));

        $this->set(compact('namedParamter', 'countries', 'states', 'cities', 'parentId', 'isAdmin', 'parents', 'dealers'));


        $multiSelectedCompanyList = $multiSelectedDealerList = $multicompany = array();
        $multiSelectedCompanyList = json_encode($multiSelectedCompanyList);
        $multiSelectedDealerList = json_encode($multiSelectedDealerList);
        $this->set(compact('multiSelectedCompanyList', 'multiSelectedDealerList', 'multicompany'));
    }

    function edit($id = null)
    {
        $sessionData = getMySessionData();
        $this->__checkPrevents('edit');
        $id = decrypt($id);
        $namedParam = getNamedParameter($this->request->params['named']);
        $namedParamArr = getNamedParameter($this->request->params['named'], true);
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['User']['id'] = $id;
            //            $this->request->data['User']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['User']['updated_by'] = $sessionData['id'];

            if (isset($this->request->data['User']['regions'])) {
                if (isset($this->request->data['CompanyBranch']['regions'])) {
                    $this->request->data['User']['regiones'] = $this->request->data['CompanyBranch']['regions'];
                } else {
                    $this->request->data['User']['regiones'] = $this->request->data['User']['regions'];
                }
            }
            if (empty($this->request->data['User']['dealer_id'])) {
                $this->request->data['User']['dealer_id'] = 0;
            }
            if ($this->type == COMPANY) {
                if (isSuparDealer()) {
                    //                    $this->request->data['User']['dealer_id'] = $this->Session->read('Auth.User.id');
                    $this->request->data['User']['dealer_id'] = $sessionData['id'];
                }
                if (isSuparCompany() || isCompanyAdmin()) {
                    //                    $parentId = $this->request->data['User']['parent_id'] = $this->Session->read('Auth.User.id');
                    //                    $this->request->data['User']['dealer_id'] = $this->Session->read('Auth.User.dealer_id');
                    $parentId = $this->request->data['User']['parent_id'] = $sessionData['id'];
                    $this->request->data['User']['dealer_id'] = $sessionData['dealer_id'];
                }
                if (isAdmin()) {
                }
            }
            if (empty($this->request->data['User']['is_pwd_change'])) {
                unset($this->request->data['User']['password']);
                unset($this->request->data['User']['confirm_password']);
            }
            if (!empty($namedParam)) {
                if (empty($this->request->data['User']['parent_id'])) {
                    $this->request->data['User']['parent_id'] = !empty($namedParamArr['value']) ? $namedParamArr['value'] : 0;
                } else {
                    $namedParam = $this->type . ':' . encrypt($this->request->data['User']['parent_id']);
                }
            }
            if (!empty($this->request->data['User']['is_pwd_change'])) {
                $this->request->data['User']['password'] = getrandompassword();
                $this->request->data['User']['confirm_password'] = $this->request->data['User']['password'];
            } else {
                if (isset($this->request->data['User']['password'])) {
                    unset($this->request->data['User']['password']);
                }
                if (isset($this->request->data['User']['confirm_password'])) {
                    unset($this->request->data['User']['confirm_password']);
                }
            }
            $oldData = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $id)));

            /*if(isset($this->request->data['User']['regions'])){
                    $this->request->data['User']['regiones']=$this->request->data['User']['regions'];
                }*/
            if ($this->User->save($this->request->data)) {

                $lastID = $this->User->id;
                if ($this->type == COMPANY && !empty($this->request->data['User']['dealer_id'])) {
                    $oldDealerId = isset($oldData['User']['dealer_id']) ? $oldData['User']['dealer_id'] : 0;
                    $this->User->saveDealerCompany($lastID, $this->request->data['User']['dealer_id'], $oldDealerId);
                }
                /**
                 * save History Data
                 */

                if (!empty($oldData)) {
                    $histData = array(
                        'ref_id' => $lastID,
                        'ref_model' => $this->type,
                        'content' => json_encode($oldData)
                    );
                    ClassRegistry::init('History')->saveHistoryData($histData);
                }
                /**
                 * Reset password
                 * if select checkbox
                 */
                if (!empty($this->request->data['User']['is_pwd_change'])) {
                    $role = $this->User->field('role');
                    $this->request->data['User']['acc_type'] = $role;
                    //sent mail to user for the account created successfully.
                    $this->SendEmail->sendPasswordResetEmail($this->request->data['User']);
                }
                /**
                 * Assign Client and branches to the support dealers
                 */
                if (((isDealer() && !isSupportDealer()) || isAdmin()) && $this->type == DEALER && $this->request->data['User']['user_type'] == SUPPORT) {
                    $sentMailUsers = $this->User->assignClientBranchToDealer($this->User->id, $this->request->data['User']);
                    if (!empty($sentMailUsers['branches'])) {
                        $mailData = $this->User->getDealerAssignMailDetail($sentMailUsers['branches']);
                        if (!empty($mailData)) {
                            $this->SendEmail->sendBranchDealerEmail($mailData);
                        }
                    }
                }


                if ((isSuparCompany() || isSuparAdmin() || isAdminAdmin()) && $this->type == COMPANY) {
                    //assign Branch to dealer
                    $this->request->data['User']['admins'] = empty($this->request->data['User']['admins']) ? array() : $this->request->data['User']['admins'];
                    //if blank array of admins than delete all branches
                    if ($this->request->data['User']['user_type'] == 'Branch') {
                        $this->User->assignBranchToAdmin($this->User->id, $this->request->data['User']['admins']);
                    } else {
                        $this->User->assignBranchToAdminNew($this->User->id, $this->request->data['User']['admins']);
                    }
                }

                //ToDO: remove record then insert all 
                if (isset($this->request->data['User']['multidealer_id']) && isset($this->request->data['User']['multicompany_id'])) {
                    $reqData = array(
                        'admin_id' => $lastID,
                        'multidealer_id' => $this->request->data['User']['multidealer_id'],
                        'multicompany_id' => $this->request->data['User']['multicompany_id']
                    );
                    ClassRegistry::init('AdminUser')->saveSupportAdminUsers($reqData);
                }

                $this->Message->setSuccess(__("Information has been updated successfully."));
                if (!empty($namedParam)) {
                    $this->redirect(array('action' => 'index', $namedParam));
                }
                // $this->redirect(array('action' => 'all_users'));
                if(!empty($this->type) && ($this->type == "Dealer")){
                    // return $this->redirect(array('action' => 'dealers'));
                    return $this->redirect(Router::url(array('controller' =>'dealers', 'action' => 'index'), true));
                }elseif (!empty($this->type) && ($this->type == "Admin")) {
                    return $this->redirect(Router::url(array('controller' =>'admins', 'action' => 'index'), true));
                }elseif (!empty($this->type) && ($this->type == "Company")){
                    return $this->redirect(Router::url(array('controller' =>'companies', 'action' => 'index'), true));
                    // return $this->redirect(array('action' => 'upload_branch'));   
                }else{
                    $this->redirect(array('action' => 'all_users'));
                }
                // return $this->redirect(Router::url( $this->referer(), true ));

            }
            $this->Message->setWarning(__("Unable to update the detail."));
        }
        $parentId = 0;
        $createdBies = $dealers = $parents = array();
        if (empty($this->request->data)) {
            $this->request->data = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $id)));
            $parentId = $this->request->data['User']['parent_id'];
            $parents = $this->User->getParentsList($parentId, $this->type);
            $dealers = $this->User->getDealerList();
        }

        $this->request->data['User']['regions'] = $this->request->data['User']['regiones'];

        if ($this->type == COMPANY) {
            $conditions = array();
            if (isSuparDealer()) {
                //                $conditions['User.dealer_id'] = $this->Session->read('Auth.User.id');
                $conditions['User.dealer_id'] = $sessionData['id'];
            }
            $parents = $this->User->getSuparCompanyList($conditions);
        }

        if ($this->type == DEALER) {
            $logUserId = 0;
            if (isSupportAdmin()) {
                //                $logUserId = $this->Session->read('Auth.User.id');
                $logUserId = $sessionData['id'];
            }
            if (isAdminDealer()) {
                $parents = $this->User->getSuparDealerList($logUserId, ADMIN);
            } else {
                $parents = $this->User->getSuparDealerList($logUserId);
            }
        }
        $isAdmin = ($this->type == $this->User->userRole['Admin']);
        $countries = $states = $cities = array();
        $countries = $this->User->Country->getCountryList();
        if (!empty($this->request->data['User']['country_id'])) {
            $states = $this->User->State->getStateList($this->request->data['User']['country_id']);
        }
        if (!empty($this->request->data['User']['state_id'])) {
            $cities = $this->User->City->getCityList($this->request->data['User']['state_id']);
        }
        $dealCondition = array();
        if (isAdminDealer() || isAdminAdmin()) {
            $dealCondition['User.created_by'] = $sessionData['id'];
        }
        if ($this->type == COMPANY) {
            if (isAdminAdmin() || isSuparAdmin()) {
                $dealCondition['User.user_type != '] = SUPPORT;
            }
        }
        if (isSuparAdmin() && $this->type == ADMIN) {
            $dealCondition = array(
                'User.user_type' => SUPAR_ADM
            );
        }
        if (isSuparAdmin() && $this->type == COMPANY) {
            $dealCondition = array(
                'User.user_type' => SUPAR_ADM,
                'User.role' => DEALER
            );
        }
        $dealers = $this->User->getDealerList(0, $dealCondition);

        $this->loadModel('AdminUser');
        $multicompany = array();
        $multiSelectedCompanyList = $multiSelectedDealerList = json_encode(array());
        if (isSuparAdmin() && $this->type == ADMIN) {
            $multiSelecteDealer = $this->AdminUser->find('all', array(
                'conditions' => array(
                    'AdminUser.admin_id' => $id
                )
            ));
            $multiSelectedDealerList = $multiSelectedCompanyList = array();
            foreach ($multiSelecteDealer as $key => $multiSelectList) {
                $multiSelectedDealerList[] = $multiSelectList['AdminUser']['dealer_id'];
                $multiSelectedCompanyList[] = $multiSelectList['AdminUser']['company_id'];
            }
            $multicompany = array();
            if (!empty($multiSelectedDealerList)) {
                $multicompany = $this->getCompanies('multiselect', false, array_unique($multiSelectedDealerList));
            }
            $multiSelectedDealerList = json_encode(array_unique($multiSelectedDealerList));
            $multiSelectedCompanyList = json_encode(array_unique($multiSelectedCompanyList));
            $this->set('multidealer', $dealers);
        }

        if (isSupportAdmin() && $this->type == COMPANY) {
            $fields = 'User.id, User.name';
            //            $dealers = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), $fields);
            $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], $fields);
        }
        if (isSuparDealer() || isAdminDealer()) {
            $dealers = array();
        }
        $this->set('from', 'Edit');

        //$this->request->data['CompanyBranch']['regions']=$this->request->data['CompanyBranch']['regiones'];
        $this->loadModel('Region');
        $conditions['company_id'] = $sessionData['id'];

        $regions = $this->Region->getRegionList($conditions);


        $this->set(compact('parents', 'namedParam', 'regions', 'countries', 'states', 'cities', 'parentId', 'isAdmin', 'createdBies', 'dealers'));

        //set flag for display user type or not
        if (empty($parentId)) {
            if (isAdminDealer() || isSupportDealer()) {
                //                $parentId = $this->Session->read('Auth.User.id');
                $parentId = $sessionData['id'];
            }
        }
        $isDisplayUserType = $this->__getUserTypeStatus($parentId);
        $isDisplayDealer = $this->__getDealerStatus();
        $isDisplayCompanyParent = $this->__getParentCompanyStatus($parentId);
        $this->set(compact('isDisplayUserType', 'isDisplayDealer', 'isDisplayCompanyParent'));
        $this->render('add');
    }

    function index($all = null, $dealerId = null)
    {
        $sessionData = getMySessionData();
        $this->__checkPrevents('index');
        $resConditions = $this->__getUserConditions($this->request->params['named']);
        $parentId = $resConditions['parentId'];
        $parentDealer = $resConditions['parentDealer'];
        $conditions = $resConditions['conditions'];
        if ($all == "all") {
            $this->Session->write($this->type . 'Search', '');
        }
        if (empty($this->request->data['User']) && $this->Session->read($this->type . 'Search')) {
            $this->request->data['User'] = $this->Session->read($this->type . 'Search');
        }
        if (!empty($dealerId)) {
            $dealerId = decrypt($dealerId);
            $this->request->data['User']['dealer_id'] = $dealerId;
        }
        if (!empty($this->request->data['User'])) {
            $this->request->data['User'] = array_filter($this->request->data['User']);
            $this->request->data['User'] = array_map('trim', $this->request->data['User']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['User']['name'])) {
                    $conditions['OR'] = array(
                        'User.first_name LIKE ' => '%' . $this->request->data['User']['name'] . '%',
                        'User.last_name LIKE ' => '%' . $this->request->data['User']['name'] . '%'
                    );
                }
                if (isset($this->request->data['User']['email'])) {
                    $conditions['User.email LIKE '] = '%' . $this->request->data['User']['email'] . '%';
                }
                if (isset($this->request->data['User']['user_type'])) {
                    $conditions['User.user_type'] = $this->request->data['User']['user_type'];
                }
                if (isset($this->request->data['User']['dealer_id'])) {
                    if (empty($parentId) && empty($all) && empty($this->request->params['named']) && empty($dealerId)) {
                        unset($this->request->data['User']['dealer_id']);
                    } else {
                        $conditions['User.dealer_id'] = $this->request->data['User']['dealer_id'];
                    }
                }
                if (isset($this->request->data['User']['status'])) {
                    $conditions['User.status'] = $this->request->data['User']['status'];
                }
            }
            $this->Session->write($this->type . 'Search', $this->request->data['User']);
        }
        //listing data  dealer(admin)
        if (isAdminDealer()) {
            if ($this->type == DEALER) {
                //                $conditions['User.parent_id'] = $this->Session->read('Auth.User.id');
                $conditions['User.parent_id'] = $sessionData['id'];
            }
            if ($this->type == COMPANY) {

                unset($conditions['User.created_by']);
                //                $conditions['User.dealer_id'] = $this->Session->read('Auth.User.id');
                $conditions['User.dealer_id'] = $sessionData['id'];
            }
        }
        $contains = false;
        if ($this->type == COMPANY) {
            $contains = array(
                'CompanyBranch' => array(
                    'fields' => array(
                        'id', 'name', 'ftpuser', 'ftp_pass'
                    ),
                    'conditions' => array('is_list_display' => 0)
                )
            );
            // unset($conditions['User.parent_id']);
            // $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            // if(!empty($current_parent_id['User']['company_parent_id'])){
            //     $conditions['User.company_parent_id'] = $current_parent_id['User']['company_parent_id'];
            // }else{
            //     $conditions['User.company_parent_id'] = $sessionData['id'];
            // }
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            $current_parent_id = '';
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
                unset($conditions['User.parent_id']);
                $conditions['User.company_parent_id'] = $current_parent_id;
            }elseif (!empty($isSuparCompany) && empty($isCompanyAdmin)) {
                unset($conditions['User.parent_id']);
                $conditions['User.company_parent_id'] = $sessionData['id'];
            }
            // $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        }
        $fields = array(
            'User.id, User.dealer_id, User.sub_dealer_count, User.sub_dealer_count, User.sub_company_count,' .
                'User.dealer_company_count, User.company_branch_count, User.station_count, User.first_name, User.last_name,' .
                'User.email, User.file_send_email, User.phone_no, User.photo, User.address, User.address2, User.country_id,' .
                'User.state_id, User.city_id, User.pincode, User.last_login_time, User.role, User.user_type, User.status,' .
                'User.is_display_billing, User.parent_id, User.subscription_id, User.created_by, User.updated_by, User.created, User.updated'
        );
        $this->AutoPaginate->setPaginate(array(
            'fields' => $fields,
            'order' => ' User.id DESC',
            'conditions' => $conditions,
            'contain' => $contains,
        ));

        $no_of_support_user = $this->paginate('User');
        //27-01-2016 displaying no of support admin count
        if (isDealer()) {
            $this->loadModel('DealerCompany');
            foreach ($no_of_support_user as $key => $usr) {
                $support_count[$usr['User']['id']] = $this->DealerCompany->find('count', array('conditions' => array('company_id' => $usr['User']['id'])));
            }
            if (!empty($support_count)) {
                $this->set('supportCount', $support_count);
            }
        }
        $this->set('userLists', $this->User->getUserList());
        $this->set('users', $this->paginate('User'));
        $dealCondition = array();
        if ($this->type == COMPANY) {
            if (isAdminDealer() || isSuparDealer()) {
                //                $dealCondition['User.parent_id'] = $this->Session->read('Auth.User.id');
                $dealCondition['User.parent_id'] = $sessionData['id'];
            }
        }
        $dealers = $this->User->getDealerList(0, $dealCondition);
        if ($this->type == DEALER) {
            //            $dealers = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), 'id, name');
            $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], 'id, first_name');
        }

        if ($this->type == COMPANY) {
            $this->loadModel('DealerCompany');
            $dealerCompanyList = $this->DealerCompany->find('all', array(
                'contain' => array(
                    'Dealer' => array('id', 'first_name')
                )
            ));
            $companyDealers = array();
            foreach ($dealerCompanyList as $value) {
                $temp1 = '';
                if (isset($companyDealers[$value['DealerCompany']['company_id']])) {
                    $temp1 = $companyDealers[$value['DealerCompany']['company_id']] . ', ';
                }
                $companyDealers[$value['DealerCompany']['company_id']] = $temp1 . $value['Dealer']['first_name'];
            }
            $this->set(compact('companyDealers'));
        }

        $this->set(compact('parentId', 'parentDealer', 'dealers'));
    }
    /*
     * Password reset
     */

    function password_reset($userId = null)
    {
        $userId = decrypt($userId);
        $user = $this->User->find(
            'first',
            array(
                'fields' => 'id, first_name, last_name, name, email, password, role, user_type',
                'contain' => false,
                'conditions' => array('User.id' => $userId)
            )
        );
        if (empty($user)) {
            $this->Message->setWarning(__('Invalid User'), $this->referer());
        }
        if ($user['User']['role'] == DEALER && $user['User']['user_type'] != "Super") {
            $this->Message->setWarning(__('Dealer does not have permission to reset the password.'), $this->referer());
        }
        if ($this->request->is(['post', 'put'])) {
            if (!empty($user)) {
                $user['User']['reset_key'] = '';
                $user['User']['password'] = $this->request->data['User']['password'];
                $user['User']['confirm_password'] = $this->request->data['User']['confirm_password'];
                unset($user['User']['photo']);
                if ($this->User->save($user)) {
                    $this->Message->setSuccess(__('Password has been changed successfully.'));
                    $this->redirect(Router::url(array('controller' => $this->usedController, 'action' => 'index'), true));
                }
                $this->Message->setWarning(__('Unable to reset the password, Please try again.'));
            }
        }
        //reset password
        $user['User']['password'] = getrandompassword();
        $user['User']['confirm_password'] = $user['User']['password'];
        $this->User->validator()->remove('last_name');
        if ($this->User->save($user['User'])) {
            //sent mail to user for the account created successfully.
            $user['User']['acc_type'] = $user['User']['role'];
            $this->SendEmail->sendPasswordResetEmail($user['User']);
            $this->Message->setSuccess(__('Password has been changed successfully.'));
        }else{
            $this->Message->setWarning(__('Unable to change password.'));
        }
        return $this->redirect($this->referer());
        $this->set(compact('user'));
    }

    function dashboard()
    {
        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        // $repConditions = ClassRegistry::init('Analytic')->getConditions('DashboardErrors', $this->request->data['Filter'], 'ErrorDetail');
        $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
    // echo '<pre><b></b><br>';
    // print_r($repConditions);echo '<br>';exit;
        $from = $repConditions['from'];
        $xAxisDates1 = $xAxisDates = $repConditions['xAxisDates'];
        $tickInterval = $repConditions['tickInterval'];
        $startDate = $repConditions['start_date'];
        $endDate = $repConditions['end_date'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (!empty($all)) {
            $this->Session->delete('Report.TellerSetupFilter');
            $this->Session->delete('Report.DashboardCondition');
        }
        $startDate = $this->request->data['Filter']['start_date'] . ' 00:00:00';
        $endDate = $this->request->data['Filter']['end_date'] . ' 23:59:59';
        /**
         * tickets for the dealer admin
         * @param type $id
         */
        $tickets = array(
            'New' => array(),
            'Open' => array(),
            'Closed' => array()
        );
        // echo '<pre><b></b><br>';
        // print_r($this->request->data);echo '<br>';exit;
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Dashboard.Filter', $this->request->data['Filter']);
        }
        if (isDealer() || isSuparAdmin()) {
            // $ticketData = ClassRegistry::init('Ticket')->getTickets($startDate, $endDate);
            $ticketData = ClassRegistry::init('Ticket')->getTicketCount($startDate, $endDate);
            // $dealers = $ticketData['dealers'];
            $tickets = $ticketData['tickets'];
            $this->set(compact('dealers'));
        }

        $this->set(compact('tickets'));
        if ($this->request->is('ajax') && !isCompany()) {
            $responseArr = $this->User->setDashboardData($this->request->data['Filter'], 'ajax');
            if (isDealer() || isSuparAdmin()) {
                $responseArr['ticketTable'] = $this->render('/Elements/ticketAjaxTable')->body();
                $responseArr['totalTicketTable'] = count($tickets['New']) + count($tickets['Open']) + count($tickets['Closed']);
            }
            echo json_encode($responseArr);
            exit;
        }
        if (!empty($this->request->data['Filter'])) {
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Dashboard.Filter', $this->request->data['Filter']);
        }
        /**
         * get the two line chart data for the company
         */
        $sessData = getMySessionData();

        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        $companies_new = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        if(($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Super') || ($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessData['id']);
            $companies = [];
            if(!empty($company_list)){
                $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM, 'User.id IN' =>$company_list )));
            }
            if(!empty($company_list)){
                $companies_data = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, id', 'conditions' => array('User.status' => 'active', 'User.id IN' =>$company_list )));
                $conditions['FileProccessingDetail.company_id IN'] = $companies_data;
            }
        }
        $this->set(compact('companies'));
        /* echo "<pre>";
     print_r($this->Session->read('DashboardReportCondition'));
        die();*/
        /// DashboardCondition
        if($from != 'all'){
            $conditions['FileProccessingDetail.file_date >= '] = $startDate;
            $conditions['FileProccessingDetail.file_date <= '] = $endDate;
        }
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_id;
        }
        if (!empty($sessData['BranchDetail']['id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $sessData['BranchDetail']['id'];
        }
        
        if (!empty($sessData['assign_branches'])) {
            $assignedBranches = $sessData['assign_branches'];
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
                $conditions['FileProccessingDetail.branch_id'] = $branches;

            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        $this->loadModel('Region');
       

        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
        }

        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('DashboardFilter', $this->request->data['Analytic']);
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if ($sessionData['role'] == 'Company' and $sessionData['user_type'] == 'Branch') {
            $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsName($sessionData['id']);

            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsList($branchidListd);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        $this->set(compact( 'stations','branches'));
        $conditions['FileProccessingDetail.company_id'] = $company_id;
        $sessData = getMySessionData();
        if (!empty($sessData['BranchDetail']['id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $sessData['BranchDetail']['id'];
        }
        if (!empty($sessData['assign_branches'])) {
            $assignedBranches = $sessData['assign_branches'];
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id'] = $branchLists;
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        
        if (isset($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $this->TransactionDetail2 = ClassRegistry::init('TellerActivityReport');
        if ($conditions['FileProccessingDetail.company_id'] == '0' || $conditions['FileProccessingDetail.company_id'] == "") {
            $conditions['FileProccessingDetail.company_id'] = $companies_new;
        }
        $chartData_new = $this->TransactionDetail2->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(number_of_deposits) as Total_Deposits', 'SUM(number_of_withdrawals) as Total_Withdrawals'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',

            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id')
                )
            )
        ));
        $this->loadModel('Location');
        $this->loadModel('Station');
        $this->loadModel('TransactionDetail');
        $location_list = $this->Location->getlocationList();
        $get_location_station = array();
        foreach ($location_list as $key => $value) {
            $companies_station['Station.location_category'] = $key;
            $get_location_station[$value] = $this->Station->find('list', array('conditions' => $companies_station));
        }
        $this->loadModel('TransactionDetail');
        if (($sessData['role'] == 'Admin') && ($sessData['user_type'] == 'Super')) {
            unset($conditions['FileProccessingDetail.company_id']);
        }
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        if(($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Super') || ($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Support')){
            unset($conditions['FileProccessingDetail.company_id']);
        }
        $new_transancation = $conditions;
        foreach ($get_location_station as $key => $value) :
            $new_transancation['FileProccessingDetail.station'] = [];
            foreach ($value as $key_main => $values) {
                $new_transancation['FileProccessingDetail.station'][] = $key_main;
            }
            $tranction_drive[$key] =  $this->TransactionDetail->find('count', array('conditions' => $new_transancation));
        endforeach;


        $j = 0;
        foreach ($tranction_drive as $key => $data) {
            $transaction_drive_Arr[$j]['name'] = $key;
            $transaction_drive_Arr[$j]['y'] = $data;
            $j++;
        }
        $transaction_drive_json = json_encode($transaction_drive_Arr, JSON_NUMERIC_CHECK);
        $this->set(compact('transaction_drive_json'));

        $pie_data2 = array();
        $pie_data2[0]['name'] = 'Total Deposits';
        $pie_data2[0]['name2'] = 'Total Deposits2';
        $pie_data2[0]['totalcount'] = $chartData_new[0][0]['Total_Deposits'];
        $pie_data2[1]['name'] = 'Total Withdrawals';
        $pie_data2[1]['name2'] = 'Total Deposits22';
        $pie_data2[1]['totalcount'] = $chartData_new[0][0]['Total_Withdrawals'];
        $pie_data = json_encode($pie_data2, JSON_NUMERIC_CHECK);
        $this->set(compact('pie_data'));
        $temp = $temp1 = $temp2 = array();
        $ackConditions = array(
            'Ticket.is_acknowledge' => 1
        );
        if (isset($conditions['FileProccessingDetail.company_id'])) {
            $ackConditions['Ticket.company_id'] = $conditions['FileProccessingDetail.company_id'];
        }
        if (isset($conditions['FileProccessingDetail.branch_id'])) {
            $ackConditions['Ticket.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }
        $errorDetailIds = ClassRegistry::init('Ticket')->find('list', array(
            'contain' => false,
            'fields' => 'error_detail_id, error_detail_id',
            'conditions' => $ackConditions
        ));
        $conditions['ErrorDetail.id'] = $errorDetailIds;
        $resConditions = array(
            'Ticket.error_warning_status' => 'completed'
        );
        $errorDetailIds = ClassRegistry::init('Ticket')->find('list', array(
            'contain' => false,
            'fields' => 'error_detail_id, error_detail_id',
            'conditions' => $resConditions
        ));
        $this->set(compact('tickInterval'));
        $sendArr = $sendArr1 = $sendArr2 = array();
        foreach ($xAxisDates as $key => $date) :
            /**
             * For the No of error occurred over date period Line
             */
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error acknowledged by dealers Line
             */
            if (isset($temp1[$date])) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error resolved by dealer Line
             */
            if (isset($temp2[$date])) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = array(
            array(
                'name' => 'No. of error occurred over date period',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'No. of error acknowledged by dealers',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'No. of error resolved by dealer',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp = json_encode($temp);
        $conditions2 = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        //$this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))'; 
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        array_pop($conditions);
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $this->TransactionDetail = ClassRegistry::init('Ticket');
        $chartDataHardwareError2 = $this->TransactionDetail->find('all', array(
            'joins' => array(
                array(

                    'table' => 'error_detail',
                    'alias' => 'ErrorDetail',
                    'type' => 'LEFT',
                    'fields' => array('id'),
                    'conditions' => array(
                        'Ticket.error_detail_id = ErrorDetail.id'
                    )
                ),
                array(

                    'table' => 'error_types',
                    'alias' => 'ErrorTypes',
                    'type' => 'LEFT',
                    'fields' => array('id'),
                    'conditions' => array(
                        'ErrorTypes.id = ErrorDetail.error_type_id'
                    )
                ),
                array(

                    'table' => 'hardware_error_type',
                    'alias' => 'HardwareErrorType',
                    'type' => 'RIGHT',
                    'fields' => array('name'),
                    'conditions' => array(
                        'HardwareErrorType.id = ErrorTypes.error_type'
                    )
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'COUNT(Ticket.id) as total', 'HardwareErrorType.name'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'ErrorTypes.error_type',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id')
                )
            )
        ));
        $chartDataHardwareError = json_encode($chartDataHardwareError2, JSON_NUMERIC_CHECK);
        $this->set(compact('chartDataHardwareError'));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        $sendArr = $sendArr1 = $sendArr2 = array();
        foreach ($xAxisDates1 as $key => $date) :
            /**
             * For the No of error occurred over date period Line
             */
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }

            /**
             * For the No of error acknowledged by dealers Line
             */
            if (isset($temp1[$date])) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error resolved by dealer Line
             */
            if (isset($temp2[$date])) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
        endforeach;

        $temp1 = array(
            array(
                'name' => 'Debit Transaction',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Credit Transaction',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Total Sum of Transaction',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp1 = json_encode($temp1);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $temp = $oldTemp;


        $this->set(compact('temp', 'temp1', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.DashboardErrors', $this->request->data['Filter']);
        }
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $dashboardArr = $this->Analytic->getDashboard($conditions);
        $this->AutoPaginate->setPaginate($dashboardArr['paginate']);
        $processFiles = $this->paginate('FileProccessingDetail');
        $this->Session->write('Report.DashboardReportCondition', $conditions);
        $this->Session->write('Report.DashboardCondition', $conditions);
        $this->set(compact('processFiles'));
        /**
         * For the date wise filter data
         */
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $htmlDataTable = $this->render('/Elements/reports/dashboard_filedata')->body();
            $options = array(
                'name' => __('Errors'),
                'title' => __('Errors'),
                'xTitle' => __('Error Date'),
                'yTitle' => __('No. of Errors'),
                'id' => '#container',
            );
            $optionstransactionDetails = array(
                'id' => '#container1',
                'name' => __('Transaction'),
                'title' => __('Transaction'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction')
            );

            echo json_encode(array(
                'options' => $options,
                'data' => $temp,
                'xAxisDates' => $xAxisDates,
                'tickInterval' => $tickInterval,
                'htmlData' => 'dashboard',
                'optionstransactionDetails' => $optionstransactionDetails,
                'transactionDetails' => $temp1,
                'chartDataHardwareError' => $chartDataHardwareError2,
                'pie_data' => $pie_data2
            ));
            exit;
        }
          
        $respDashboard = $this->User->setDashboardData($this->request->data['Filter']);
        $startDate = $this->request->data['Filter']['start_date'] . ' 00:00:00';
        $endDate = $this->request->data['Filter']['end_date'] . ' 23:59:59';
        $totalClients = $respDashboard['totalClients'];
        $totalTrans = $respDashboard['totalTrans'];
        $totalErros = $respDashboard['totalErros'];
        $totalPFiles = $respDashboard['totalPFiles'];
        $totalUnIdentiMsg = $respDashboard['totalUnIdentiMsg'];
        $this->set(compact('totalTrans', 'totalErros', 'totalPFiles', 'totalClients', 'totalUnIdentiMsg'));

        $this->loadModel('Analytic');
        $conditions2 = array_merge($conditions2,$conditions);
        $conditions2['FileProccessingDetail.company_id'] = $company_id;
        if(($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Super') || ($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessData['id']);
            $conditions2['FileProccessingDetail.company_id'] = $company_list;
        }elseif (($sessData['role'] == 'Admin') && ($sessData['user_type'] == 'Super')) {
            unset($conditions2['FileProccessingDetail.company_id']);
        }
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions2);
        $this->loadModel('TransactionDetail');

        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $withdrawCondition = $transactionDetailArray2['chart']['deposite'];
        $withdrawCondition['conditions']['TransactionDetail.trans_type_id IN'] = [1,11];
        $withdrawCondition['group'] = 'FileProccessingDetail.file_date';
        $withdrawData = $this->TransactionDetail->find('all', $withdrawCondition);
        $i = 0;
        $data3 = [];
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }
        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);
       
        $depositeCondition = $transactionDetailArray2['chart']['DepositeChart'];
        $depositeCondition['conditions']['TransactionDetail.trans_type_id'] = 2;
        if(!empty($conditions['FileProccessingDetail.branch_id'])){
            $depositeCondition['conditions']['FileProccessingDetail.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }
        $depositeCondition['group'] = 'FileProccessingDetail.file_date';
        $depositeCount = $this->TransactionDetail->find('all', $depositeCondition);
        $j = 0;
        $data4 = [];
        foreach ($depositeCount as $key => $data) {
            $data4[$j][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data4[$j][1] = $data['TransactionDetail']['transaction_count'];
            $j++;
        }
        $depositechartdata = json_encode($data4, JSON_NUMERIC_CHECK);
       
        $withdrawsCondition = $transactionDetailArray2['chart']['DepositeChart'];
        $withdrawsCondition['conditions']['TransactionDetail.trans_type_id IN'] = [1, 11];
        $withdrawsCondition['group'] = 'FileProccessingDetail.file_date';
        $withdrawsCount = $this->TransactionDetail->find('all', $withdrawsCondition);
        $k = 0;
        $withdraws_data = [];
        foreach ($withdrawsCount as $key => $data) {
            $withdraws_data[$k][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $withdraws_data[$k][1] = $data['TransactionDetail']['transaction_count'];
            $k++;
        }
        $withdrawschartdata = json_encode($withdraws_data, JSON_NUMERIC_CHECK);

        $InventoryCondition = $transactionDetailArray2['chart']['DepositeChart'];
        $InventoryCondition['conditions']['TransactionDetail.trans_type_id NOT IN'] = [1, 11, 2];
        $InventoryCondition['group'] = 'FileProccessingDetail.file_date';
    
        $InventoryCount = $this->TransactionDetail->find('all', $InventoryCondition);
        $h = 0;
        $Inventory_data = [];
        foreach ($InventoryCount as $key => $data) {
            $Inventory_data[$h][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $Inventory_data[$h][1] = $data['TransactionDetail']['transaction_count'];
            $h++;
        }
        $Inventorychartdata = json_encode($Inventory_data, JSON_NUMERIC_CHECK);
        $this->set(compact('newchartdata', 'depositechartdata', 'withdrawschartdata', 'Inventorychartdata'));
        $this->loadModel('ErrorDetail');
        $erorDatacondition = array(
            'FileProccessingDetail.company_id' => $company_id,
            'ErrorDetail.entry_timestamp >= ' => $startDate,
            'ErrorDetail.entry_timestamp <= ' => $endDate
        );
        if(($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Super') || ($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessData['id']);
            $erorDatacondition['FileProccessingDetail.company_id'] = $company_list;
        }
        $errorData = $this->ErrorDetail->find('all', array('conditions' => $erorDatacondition));
     
        $arrydata = array();
        $arrydata2 = array();
        $i = 0;
        foreach ($errorData as $key => $data) {
            $arrydata[$data['ErrorType']['error_code']][$i] = $data['ErrorDetail']['error_message'];
            $i++;
        }
        $j = 0;
        foreach ($arrydata as $key => $data) {
            $arrydata2[$j]['name'] = reset($data);
            $arrydata2[$j]['y'] = count($data);
            $j++;
        }
        $errorData_Arr = array();
        $errorData_Arr = json_encode($arrydata2, JSON_NUMERIC_CHECK);
        $this->set(compact('errorData_Arr'));
        $this->loadModel('Station');
        $stationCondition = array(
            'Station.company_id =' => $company_id,
            'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
            'CompanyBranch.regiones' => !empty($this->request->data['Analytic']['regiones']) ? $this->request->data['Analytic']['regiones'] : '',
            'Station.branch_id' => !empty($this->request->data['Analytic']['branch_id']) ? $this->request->data['Analytic']['branch_id'] : ''
        );
        if(empty($this->request->data['Analytic']['regiones'])){
            unset($stationCondition['CompanyBranch.regiones']);
        }
        if(empty($this->request->data['Analytic']['branch_id'])){
            unset($stationCondition['Station.branch_id']);
        }
        $stationCount = $this->Station->find('count', array('conditions' => $stationCondition, 'contain' =>['CompanyBranch']));
        $stationCondition_active = array(
            'Station.company_id' => $company_id,
            'Station.status' => 'active',
            'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
            'file_processed_count >' => 0,
            'CompanyBranch.regiones' => !empty($this->request->data['Analytic']['regiones']) ? $this->request->data['Analytic']['regiones'] : '',
            'Station.branch_id' => !empty($this->request->data['Analytic']['branch_id']) ? $this->request->data['Analytic']['branch_id'] : ''
        );
        if(empty($this->request->data['Analytic']['regiones'])){
            unset($stationCondition_active['CompanyBranch.regiones']);
        }
        if(empty($this->request->data['Analytic']['branch_id'])){
            unset($stationCondition_active['Station.branch_id']);
        }
        $activestationCount = $this->Station->find('count', array('conditions' => $stationCondition_active, 'contain' =>['CompanyBranch']));

        $stationCondition_Inactive = array(
            'Station.company_id' => $company_id,
            'Station.status' => 'inactive',
            'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
            'file_processed_count <=' => 0,
            'CompanyBranch.regiones' => !empty($this->request->data['Analytic']['regiones']) ? $this->request->data['Analytic']['regiones'] : '',
            'Station.branch_id' => !empty($this->request->data['Analytic']['branch_id']) ? $this->request->data['Analytic']['branch_id'] : ''
        );
        if(empty($this->request->data['Analytic']['regiones'])){
            unset($stationCondition_Inactive['CompanyBranch.regiones']);
        }
        if(empty($this->request->data['Analytic']['branch_id'])){
            unset($stationCondition_Inactive['Station.branch_id']);
        }
        $inactivestationCount = $this->Station->find('count', array('conditions' => $stationCondition_Inactive,'contain' =>['CompanyBranch']));
        $this->set(compact('stationCount', 'activestationCount', 'inactivestationCount'));
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions2);
        $this->loadModel('TransactionDetail');

        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $tranctionCount = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $tranctionDataCount = $this->TransactionDetail->find('count', $transactionDetailArray2['paginateCount']);
      
        $tranctionDataAmount = $this->TransactionDetail->find('first', $transactionDetailArray2['paginateCount']);
        $tranctionDataAmount  = !empty($tranctionDataAmount[0]) ? $tranctionDataAmount[0]['total_amount'] : 0;
        // Deposite Data
        $depositeCondition = $transactionDetailArray2['chart']['deposite'];
        $depositeCondition['conditions']['TransactionDetail.trans_type_id'] = 2;
        $depositeCount = $this->TransactionDetail->find('count', $depositeCondition);
        $DepositeData = $this->TransactionDetail->find('first', $depositeCondition);
        $DepositeData  = !empty($DepositeData[0]) ? $DepositeData[0]['total_amount'] : 0;
        // End Deposite Data
        
        // Withdraw Data
        $withdrawCondition = $transactionDetailArray2['chart']['deposite'];
        $withdrawCondition['conditions']['TransactionDetail.trans_type_id IN'] = [1,11];
        $withdrawCount = $this->TransactionDetail->find('count', $withdrawCondition);
        $withdrawData = $this->TransactionDetail->find('first', $withdrawCondition);
        $withdrawData  = !empty($withdrawData[0]) ? $withdrawData[0]['total_amount'] : 0;
      
        $transactionDetailArray2['paginateCount']['conditions']['TransactionDetail.trans_type_id NOT IN'] = [1,2,11];
        $InventoryCount = $this->TransactionDetail->find('count', $transactionDetailArray2['paginateCount']);
        $InventoryData = $this->TransactionDetail->find('first', $transactionDetailArray2['paginateCount']);
        $InventoryData  = !empty($InventoryData[0]) ? $InventoryData[0]['total_amount'] : 0;
        // End Withdraw Data
        $total_Count = array();
        foreach ($tranctionCount as $key => $value) {
            $total_Count[] = $value['TransactionDetail']['transaction_count'];
        }
        $total_Count = array_sum($total_Count);
        $filter_conditions['TransactionDetail.trans_datetime >= '] = $startDate;
        $filter_conditions['TransactionDetail.trans_datetime <= '] = $endDate;
        $filter_conditions['FileProccessingDetail.company_id ='] = $company_id;
        // $filter_conditions['FileProccessingDetail.branch_id ='] = $this->request->data['Analytic']['branch_id'];
     
        if(!empty($conditions['FileProccessingDetail.branch_id'])){
            $filter_conditions['FileProccessingDetail.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }elseif (!empty($this->request->data['Analytic']['branch_id'])) {
            $filter_conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $filter_conditions['FileProccessingDetail.branch_id'] = $branchLists;
        }
        $tellerCount = $this->TransactionDetail->find('count', array('conditions' => $filter_conditions, 'fields' => 'DISTINCT teller_name '));
        $totalAvg = $tellerCount > 0 ? round($total_Count / $tellerCount, 2) : 0;
        $tellerNames = $this->TransactionDetail->find('all', array('conditions' => $filter_conditions, 'fields' => 'DISTINCT teller_name '));
        $avgCount = array();
        $avgCount['belowavg'] = 0;
        $avgCount['aboveavg'] = 0;
        foreach ($tellerNames as $key => $value) {
            $Tellerconditions  = array(
                'TransactionDetail.teller_name' => $value['TransactionDetail']['teller_name'],
                'TransactionDetail.trans_datetime >=' =>  $startDate,
                'TransactionDetail.trans_datetime <=' =>  $endDate,
                'FileProccessingDetail.company_id =' => $company_id
            );
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $conditions_regiones = array(
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'CompanyBranch.branch_status' => 'active',
                );
                $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                    'fields' => 'id, id',
                    'contain' => false,
                    'conditions' => $conditions_regiones
                ));
                $Tellerconditions['FileProccessingDetail.branch_id'] = $branchLists;
            }
            if(!empty($conditions['FileProccessingDetail.branch_id'])){
                $Tellerconditions['FileProccessingDetail.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
            }elseif (!empty($this->request->data['Analytic']['branch_id'])) {
                $Tellerconditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            }
            $Telleravg = $this->TransactionDetail->find('count', array('conditions' => $Tellerconditions));
            if ($totalAvg > $Telleravg) {
                $avgCount['belowavg']++;
            } elseif ($totalAvg < $Telleravg) {
                $avgCount['aboveavg']++;
            }
        }
        $this->set(compact('avgCount', 'totalAvg', 'tranctionDataCount','DepositeData', 'withdrawData','tranctionDataAmount', 'withdrawCount', 'depositeCount','InventoryCount','InventoryData'));
    }

    function dashboard1()
    {
        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
        $from = $repConditions['from'];
        $xAxisDates1 = $xAxisDates = $repConditions['xAxisDates'];
        $tickInterval = $repConditions['tickInterval'];
        $startDate = $repConditions['start_date'];
        $endDate = $repConditions['end_date'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }

        if (!empty($all)) {
            $this->Session->delete('Report.TellerSetupFilter');
            $this->Session->delete('Report.DashboardCondition');
        }
        $startDate = $this->request->data['Filter']['start_date'] . ' 00:00:00';
        $endDate = $this->request->data['Filter']['end_date'] . ' 23:59:59';
        $tickets = array(
            'New' => array(),
            'Open' => array(),
            'Closed' => array()
        );
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Dashboard.Filter', $this->request->data['Filter']);
        }
        if (!empty($this->request->data['Filter'])) {
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Dashboard.Filter', $this->request->data['Filter']);
        }
        if (isDealer() || isSuparAdmin()) {
            $ticketData = ClassRegistry::init('Ticket')->getTicketCount($startDate, $endDate);
            $tickets = $ticketData['tickets'];
            $this->set(compact('dealers'));
        }
        $this->set(compact('tickets'));
        if ($this->request->is('ajax') && !isCompany()) {
            $responseArr = $this->User->setDashboardData($this->request->data['Filter'], 'ajax');
            if (isDealer() || isSuparAdmin()) {
                $responseArr['ticketTable'] = $this->render('/Elements/ticketAjaxTable')->body();
                $responseArr['totalTicketTable'] = count($tickets['New']) + count($tickets['Open']) + count($tickets['Closed']);
            }
            echo json_encode($responseArr);
            exit;
        }

        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];

        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        $companies_new = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        if(($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Super') || ($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessionData['id']);
            $companies = [];
            if(!empty($company_list)){
                $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM, 'User.id IN' =>$company_list )));
            }
            if(!empty($company_list)){
                $companies_data = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, id', 'conditions' => array('User.status' => 'active', 'User.id IN' =>$company_list )));
                $conditions['FileProccessingDetail.company_id IN'] = $companies_data;
            }
        }
        $this->set(compact('companies'));
        if($from != 'all'){
            $conditions['FileProccessingDetail.file_date >= '] = $startDate;
            $conditions['FileProccessingDetail.file_date <= '] = $endDate;
        }
        if (isCompany()) {
            if (!empty($sessionData)) {
                $stations = ClassRegistry::init('Station')->getStationList($company_id);
            }
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_id;
        }
        if (!empty($sessionData['BranchDetail']['id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $sessionData['BranchDetail']['id'];
        }
        if (!empty($sessionData['assign_branches'])) {
            $assignedBranches = $sessionData['assign_branches'];
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $conditions_regiones = array(
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'CompanyBranch.branch_status' => 'active',
                );
                $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                    'fields' => 'id, id',
                    'contain' => false,
                    'conditions' => $conditions_regiones
                ));
                $conditions['FileProccessingDetail.branch_id'] = $branchLists;

            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        $this->TransactionDetail2 = ClassRegistry::init('TellerActivityReport');
        if ($conditions['FileProccessingDetail.company_id'] == '0' || $conditions['FileProccessingDetail.company_id'] == "") {
            $conditions['FileProccessingDetail.company_id'] = $companies_new;
        }
        $chartData_new = $this->TransactionDetail2->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(number_of_deposits) as Total_Deposits', 'SUM(number_of_withdrawals) as Total_Withdrawals'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',

            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id')
                )
            )
        ));
        $this->loadModel('Location');
        $this->loadModel('Station');
        $this->loadModel('TransactionDetail');

        $location_list = $this->Location->getlocationList();
        $get_location_station = array();
        foreach ($location_list as $key => $value) {
            $companies_station['Station.location_category'] = $key;
            $get_location_station[$value] = $this->Station->find('list', array('conditions' => $companies_station));
        }
        $this->loadModel('TransactionDetail');
        if (($sessionData['role'] == 'Admin') && ($sessionData['user_type'] == 'Super')) {
            unset($conditions['FileProccessingDetail.company_id']);
        }
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        if(($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Super') || ($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Support')){
            unset($conditions['FileProccessingDetail.company_id']);
        }
        $new_transancation = $conditions;
        foreach ($get_location_station as $key => $value) :
            $new_transancation['FileProccessingDetail.station'] = [];
            foreach ($value as $key_main => $values) {
                $new_transancation['FileProccessingDetail.station'][] = $key_main;
            }
            $tranction_drive[$key] =  $this->TransactionDetail->find('count', array('conditions' => $new_transancation));
        endforeach;


        $j = 0;
        foreach ($tranction_drive as $key => $data) {
            $transaction_drive_Arr[$j]['name'] = $key;
            $transaction_drive_Arr[$j]['y'] = $data;
            $j++;
        }
        $transaction_drive_json = json_encode($transaction_drive_Arr, JSON_NUMERIC_CHECK);
        $this->set(compact('transaction_drive_json'));

        $pie_data2 = array();
        $pie_data2[0]['name'] = 'Total Deposits';
        $pie_data2[0]['name2'] = 'Total Deposits2';
        $pie_data2[0]['totalcount'] = $chartData_new[0][0]['Total_Deposits'];
        $pie_data2[1]['name'] = 'Total Withdrawals';
        $pie_data2[1]['name2'] = 'Total Deposits22';
        $pie_data2[1]['totalcount'] = $chartData_new[0][0]['Total_Withdrawals'];
        $pie_data = json_encode($pie_data2, JSON_NUMERIC_CHECK);
        $this->set(compact('pie_data'));
        $temp = $temp1 = $temp2 = array();
        $ackConditions = array(
            'Ticket.is_acknowledge' => 1
        );
        if (isset($conditions['FileProccessingDetail.company_id'])) {
            $ackConditions['Ticket.company_id'] = $conditions['FileProccessingDetail.company_id'];
        }
        if (isset($conditions['FileProccessingDetail.branch_id'])) {
            $ackConditions['Ticket.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }
        $errorDetailIds = ClassRegistry::init('Ticket')->find('list', array(
            'contain' => false,
            'fields' => 'error_detail_id, error_detail_id',
            'conditions' => $ackConditions
        ));
        $conditions['ErrorDetail.id'] = $errorDetailIds;
        $resConditions = array(
            'Ticket.error_warning_status' => 'completed'
        );
        $errorDetailIds = ClassRegistry::init('Ticket')->find('list', array(
            'contain' => false,
            'fields' => 'error_detail_id, error_detail_id',
            'conditions' => $resConditions
        ));
        $this->set(compact('tickInterval'));
        $sendArr = $sendArr1 = $sendArr2 = array();
        foreach ($xAxisDates as $key => $date) :
            /**
             * For the No of error occurred over date period Line
             */
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error acknowledged by dealers Line
             */
            if (isset($temp1[$date])) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error resolved by dealer Line
             */
            if (isset($temp2[$date])) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = array(
            array(
                'name' => 'No. of error occurred over date period',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'No. of error acknowledged by dealers',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'No. of error resolved by dealer',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp = json_encode($temp);
        $conditions2 = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        array_pop($conditions);
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $this->TransactionDetail = ClassRegistry::init('Ticket');
        $chartDataHardwareError2 = $this->TransactionDetail->find('all', array(
            'joins' => array(
                array(

                    'table' => 'error_detail',
                    'alias' => 'ErrorDetail',
                    'type' => 'LEFT',
                    'fields' => array('id'),
                    'conditions' => array(
                        'Ticket.error_detail_id = ErrorDetail.id'
                    )
                ),
                array(

                    'table' => 'error_types',
                    'alias' => 'ErrorTypes',
                    'type' => 'LEFT',
                    'fields' => array('id'),
                    'conditions' => array(
                        'ErrorTypes.id = ErrorDetail.error_type_id'
                    )
                ),
                array(

                    'table' => 'hardware_error_type',
                    'alias' => 'HardwareErrorType',
                    'type' => 'RIGHT',
                    'fields' => array('name'),
                    'conditions' => array(
                        'HardwareErrorType.id = ErrorTypes.error_type'
                    )
                ),
            ),
            'conditions' => $conditions,
            'fields' => array(
                'COUNT(Ticket.id) as total', 'HardwareErrorType.name'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'ErrorTypes.error_type',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id')
                )
            )
        ));
        $chartDataHardwareError = json_encode($chartDataHardwareError2, JSON_NUMERIC_CHECK);
        $this->set(compact('chartDataHardwareError'));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        $sendArr = $sendArr1 = $sendArr2 = array();
        foreach ($xAxisDates1 as $key => $date) :
            /**
             * For the No of error occurred over date period Line
             */
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }

            /**
             * For the No of error acknowledged by dealers Line
             */
            if (isset($temp1[$date])) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error resolved by dealer Line
             */
            if (isset($temp2[$date])) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
        endforeach;

        $temp1 = array(
            array(
                'name' => 'Debit Transaction',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Credit Transaction',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Total Sum of Transaction',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp1 = json_encode($temp1);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $temp = $oldTemp;


        $this->set(compact('temp', 'temp1', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.DashboardErrors', $this->request->data['Filter']);
        }
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $dashboardArr = $this->Analytic->getDashboard($conditions);
        $this->AutoPaginate->setPaginate($dashboardArr['paginate']);
        $processFiles = $this->paginate('FileProccessingDetail');
        $this->Session->write('Report.DashboardReportCondition', $conditions);
        $this->Session->write('Report.DashboardCondition', $conditions);
        $this->set(compact('processFiles'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $htmlDataTable = $this->render('/Elements/reports/dashboard_filedata')->body();
            $options = array(
                'name' => __('Errors'),
                'title' => __('Errors'),
                'xTitle' => __('Error Date'),
                'yTitle' => __('No. of Errors'),
                'id' => '#container',
            );
            $optionstransactionDetails = array(
                'id' => '#container1',
                'name' => __('Transaction'),
                'title' => __('Transaction'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction')
            );

            echo json_encode(array(
                'options' => $options,
                'data' => $temp,
                'xAxisDates' => $xAxisDates,
                'tickInterval' => $tickInterval,
                'htmlData' => 'dashboard',
                'optionstransactionDetails' => $optionstransactionDetails,
                'transactionDetails' => $temp1,
                'chartDataHardwareError' => $chartDataHardwareError2,
                'pie_data' => $pie_data2
            ));
            exit;
        }
        $respDashboard = $this->User->setDashboardData($this->request->data['Filter']);
        $startDate = $this->request->data['Filter']['start_date'] . ' 00:00:00';
        $endDate = $this->request->data['Filter']['end_date'] . ' 23:59:59';
        $totalClients = $respDashboard['totalClients'];
        $totalTrans = $respDashboard['totalTrans'];
        $totalErros = $respDashboard['totalErros'];
        $totalPFiles = $respDashboard['totalPFiles'];
        $totalUnIdentiMsg = $respDashboard['totalUnIdentiMsg'];
        $this->set(compact('totalTrans', 'totalErros', 'totalPFiles', 'totalClients', 'totalUnIdentiMsg'));
        $this->loadModel('Analytic');
        $conditions2 = array_merge($conditions2,$conditions);
        $conditions2['FileProccessingDetail.company_id'] = $company_id;
        if(($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Super') || ($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessionData['id']);
            $conditions2['FileProccessingDetail.company_id'] = $company_list;
        }elseif (($sessionData['role'] == 'Admin') && ($sessionData['user_type'] == 'Super')) {
            unset($conditions2['FileProccessingDetail.company_id']);
        }
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions2);
        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $withdrawCondition = $transactionDetailArray2['chart']['deposite'];
        $withdrawCondition['conditions']['TransactionDetail.trans_type_id IN'] = [1,11];
        $withdrawCondition['group'] = 'FileProccessingDetail.file_date';
        $withdrawData = $this->TransactionDetail->find('all', $withdrawCondition);
        $i = 0;
        $data3 = [];
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }
        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);

        $this->loadModel('TransactionDetail');

    }
    public function view($id = null, $all = null, $model = null)
    {

        $id = decrypt($id);
        $userDetailArr = $this->User->getUserDetail($id);
        $user = $userDetailArr['User'];
        $isDisplayAdd = false;
        if (!empty($user['User']['user_type']) && $user['User']['user_type'] == SUPAR_ADM || $user['User']['role'] == ADMIN) {
            $isDisplayAdd = true;
        }
        $this->set(compact('userDetailArr', 'user', 'isDisplayAdd'));
        $limit = 20;
        if (!empty($this->request->params['named']['Paginate']) && !empty($this->request->params['named']['Model']) && ($this->request->params['named']['Model'] == 'CompanyBranch')) {
            $limit = $this->request->params['named']['Paginate'];
        }
        if (($this->type == COMPANY && in_array($user['User']['user_type'], array(SUPAR_ADM, ADMIN, REGION, BRANCH))) || ($this->type == DEALER && $user['User']['user_type'] == 'Support')) {
            $conditions = array();
            if (($this->type == DEALER && $user['User']['user_type'] == 'Support')) {
                $branchIds = ClassRegistry::init('BranchDealer')->find('list', array(
                    'conditions' => array(
                        'dealer_id' => $user['User']['id'],
                        'status' => 'Accept'
                    ),
                    'fields' => array('branch_id', 'branch_id')
                ));
                $conditions['CompanyBranch.id'] = $branchIds;
                $conditions['CompanyBranch.status'] = 'active';
            }
            if ($all == "all" && $model == 'CompanyBranch') {
                $this->Session->write('CompanyBranchSearch', '');
            }
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
                    if (isset($this->request->data['CompanyBranch']['branch_id'])) {
                        $conditions['CompanyBranch.id'] = $this->request->data['CompanyBranch']['branch_id'];
                    }
                    if (isset($this->request->data['CompanyBranch']['company_id'])) {
                        $conditions['CompanyBranch.company_id'] = $this->request->data['CompanyBranch']['company_id'];
                    }
                    if (isset($this->request->data['CompanyBranch']['email'])) {
                        $conditions['CompanyBranch.email LIKE '] = '%' . $this->request->data['CompanyBranch']['email'] . '%';
                    }
                    if (isset($this->request->data['CompanyBranch']['status'])) {
                        $conditions['CompanyBranch.branch_status'] = $this->request->data['CompanyBranch']['status'];
                    }
                }
                $this->Session->write('CompanyBranchSearch', $this->request->data['CompanyBranch']);
            }
            if (!($this->type == DEALER && $user['User']['user_type'] == 'Support')) {
                $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsId($id);
                $conditions['CompanyBranch.id'] = $branchidListd;
                $conditions['CompanyBranch.company_id'] = $id;
            }
            $this->loadModel('CompanyBranch');
            $page = $this->pageForPagination('CompanyBranch');
            $this->AutoPaginate->setPaginate(array(
                'order' => ' CompanyBranch.id DESC',
                'conditions' => $conditions,
                'limit' => $limit,
                'page' => $page
            ));
            $this->loadModel('CompanyBranch');
            $this->set('companyBranches', $this->paginate('CompanyBranch'));
        }
        $conditions = array();
        if (!empty($this->request->params['named']['Paginate']) && !empty($this->request->params['named']['Model']) && ($this->request->params['named']['Model'] == 'User')) {
            $limit = $this->request->params['named']['Paginate'];
        }
        if (in_array($user['User']['user_type'], array(SUPAR_ADM, ADMIN, REGION, BRANCH, SUPPORT))) {
            if ($all == "all" && $model == 'User') {
                $this->Session->write($this->type . 'UserViewSearch', '');
            }
            if (empty($this->request->data['User']) && $this->Session->read($this->type . 'UserViewSearch')) {
                $this->request->data['User'] = $this->Session->read($this->type . 'UserViewSearch');
            }
            if (!empty($this->request->data['User'])) {
                $this->request->data['User'] = array_filter($this->request->data['User']);
                $this->request->data['User'] = array_map('trim', $this->request->data['User']);
                if (!empty($this->request->data)) {
                    if (isset($this->request->data['User']['name'])) {
                        $conditions['OR'] = array(
                            'User.first_name LIKE ' => '%' . $this->request->data['User']['name'] . '%',
                            'User.last_name LIKE ' => '%' . $this->request->data['User']['name'] . '%'
                        );
                    }
                    if (isset($this->request->data['User']['email'])) {
                        $conditions['User.email LIKE '] = '%' . $this->request->data['User']['email'] . '%';
                    }
                    if (isset($this->request->data['User']['user_type'])) {
                        $conditions['User.user_type'] = $this->request->data['User']['user_type'];
                    }
                    if (isset($this->request->data['User']['dealer_id'])) {
                        $conditions['User.dealer_id'] = $this->request->data['User']['dealer_id'];
                    }
                    if (isset($this->request->data['User']['status'])) {
                        $conditions['User.status'] = $this->request->data['User']['status'];
                    }
                }
                $this->Session->write($this->type . 'UserViewSearch', $this->request->data['User']);
            }
            $conditions['User.parent_id'] = $id;
            $conditions['User.role'] = $this->type;
            $contains = array(
                'Dealer' => array(
                    'fields' => 'Dealer.id, Dealer.first_name, Dealer.last_name, Dealer.email'
                )
            );
            $fields = array('User.id', 'name', 'last_login_time', 'User.first_name', 'User.last_name', 'User.email', 'phone_no', 'status', 'user_type', 'role', 'created', 'updated');
            $page = $this->pageForPagination('User');
            $userCount = $this->User->find('count', array(
                'conditions' => $conditions,
                'contain' => false
            ));
            if ($userCount) {
                $limit = $userCount;
            }

            if ($this->type == COMPANY) {
                if (!isDealer()) {
                    $this->AutoPaginate->setPaginate(array(
                        'contain' => $contains,
                        'order' => ' User.id DESC',
                        'fields' => $fields,
                        'conditions' => $conditions,
                        'limit' => $limit,
                        'page' => $page
                    ));
                } else {
                    $this->loadModel('DealerCompany');
                    $dealer_ids = $this->DealerCompany->find('all', array(
                        'conditions' => array('company_id' => $id), 'fields' => 'DealerCompany.dealer_id'
                    ));
                    foreach ($dealer_ids as $key => $value) {
                        $dealer_id[] = $value['DealerCompany']['dealer_id'];
                    }
                    $this->loadModel('CompanyBranch');
                    $branchNames = $this->CompanyBranch->find('all', array(
                        'contain' => false,
                        'conditions' => array('CompanyBranch.company_id' => $id, 'NOT' => array('CompanyBranch.status' => 'deleted')),
                        'fields' => array('CompanyBranch.company_id', 'CompanyBranch.name')
                    ));
                    /*
                     * displaying branch name in company view page
                     * 
                     */
                    $branch_Name = array();
                    foreach ($branchNames as $key => $branchName) {
                        $branch_Name[$branchName['CompanyBranch']['company_id']][] = $branchName['CompanyBranch']['name'];
                    }
                    $this->set('branchName', $branch_Name);
                    unset($conditions['User.role']);
                    unset($conditions['User.parent_id']);

                    if (!empty($dealer_id)) {
                        $conditions['User.id'] = $dealer_id;
                    } else {
                        $conditions['User.id'] = null;
                    }
                    if (isDealer()) {
                        $conditions['User.user_type'] = 'Support';
                    }
                    $userCount = $this->User->find('count', array(
                        'conditions' => $conditions,
                        'contain' => false
                    ));
                    if ($userCount) {
                        $limit = $userCount;
                    }
                    $this->AutoPaginate->setPaginate(array(
                        'contain' => $contains,
                        'order' => ' User.id DESC',
                        'fields' => $fields,
                        'conditions' => $conditions,
                        'limit' => $limit,
                        'page' => $page
                    ));
                }
                $companyUsers = $this->paginate('User');
                $dealerList = $this->User->find('list', array(
                    'fields' => 'id, id',
                    'conditions' => $conditions
                ));
                $branchIdList = ClassRegistry::init('BranchDealer')->find('all', array(
                    'contain' => array('Branch' => 'name'),
                    'conditions' => array(
                        'NOT' => array(
                            'BranchDealer.branch_id' => 0
                        ),
                        'BranchDealer.dealer_id' => $dealerList,
                    )
                ));
                $branchDealer = array();
                foreach ($branchIdList as $branchData) {
                    $oldData = isset($branchDealer[$branchData['BranchDealer']['dealer_id']]) ? $branchDealer[$branchData['BranchDealer']['dealer_id']] . ',' : '';
                    $branchDealer[$branchData['BranchDealer']['dealer_id']] = $oldData . $branchData['Branch']['name'];
                }
                $this->set(compact('branchDealer'));
            } else {
                $this->AutoPaginate->setPaginate(array(
                    'contain' => false,
                    'order' => ' User.id DESC',
                    'fields' => array('User.*'),
                    'conditions' => $conditions
                ));
                $companyUsers = $this->paginate('User');
            }
        }
        $this->set(compact('companyUsers', 'id'));
    }
    function change_password($userId = null)
    {
        $sessionData = getMySessionData();
        $userId = decrypt($userId);
        $id = (empty($userId) ? $this->__getUserId() : $userId);
        if (!empty($this->request->data)) {
            $user = $this->User->find('first', array('fields' => 'id, first_name, last_name, email, password', 'conditions' => array('User.id' => $id), 'contain' => false));
            if (!empty($user)) {
                if ($id == $sessionData['id'] && AuthComponent::password($this->request->data['User']['old_password']) != $user['User']['password']) {
                    $this->Message->setWarning(__("Old Password not matched in database."));
                } elseif ($this->request->data['User']['new_password'] != $this->request->data['User']['confirm_password']) {
                    $this->Message->setWarning(__("Provide same confirm password as new password."));
                } elseif ($this->request->data['User']['new_password'] == "") {
                    $this->Message->setWarning(__("New Password Must Not Blank."));
                } else {
                    $user['User']['password'] = $this->request->data['User']['new_password'];
                    $user['User']['confirm_password'] = $this->request->data['User']['confirm_password'];
                    $user['User']['is_setup'] = 1;
                    $this->User->validator()->remove('last_name');
                    if ($this->User->save($user)) {
                        $this->SendEmail->sendPasswordChangedEmail($user['User']);
                        $this->Message->setSuccess(__("Password changed successfully."));
                        if ($user['User']['id'] == $sessionData['id']) {
                            return $this->redirect(array('action' => 'profile'));
                        }
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Message->setWarning(__("Unable to change password, Please try again."));
                    }
                }
            } else {
                $this->Message->setWarning(__("Unable to change password, Please try again."));
            }
        }
    }
    function reset_password($resetKey = null)
    {
        $this->layout = 'login';
        $this->set(compact('resetKey'));
        $user = $this->User->find('first', array('conditions' => array('reset_key' => $resetKey), 'recursive' => "-1"));
        if (empty($user)) {
            $this->Message->setWarning(__('Invalid reset password token.'), '/');
        }
        if (!empty($this->request->data)) {
            if (!empty($user)) {
                $user['User']['reset_key'] = '';
                $user['User']['password'] = $this->request->data['User']['password'];
                $user['User']['confirm_password'] = $this->request->data['User']['confirm_password'];
                if ($this->User->save($user)) {
                    $this->Message->setSuccess(__('Password changed successfully.'), array('controller' => 'users', 'action' => 'change_password'));
                } else {
                    $this->Message->setWarning(__('Unable to reset password, Please try again.'), '/');
                }
            } else {

                $this->Message->setWarning(__('Invalid reset password token.'), '/');
            }
        }
    }

    public function forgot_password()
    {
        $this->layout = 'login';
        if (!empty($this->request->data)) {
            $this->User->recursive = -1;
            $user = $this->User->find('first', array('conditions' => array('email' => $this->request->data['User']['email']), 'fields' => array('id', 'email', 'first_name', 'last_name', 'status', 'reset_key')));
            if (!empty($user)) {
                if ($user['User']['status'] == 'active') {
                    //send mail to the User                 
                    $user['User']['reset_key'] = $this->Common->getActivationCode($user['User']['id'], time());
                    $this->User->save($user);
                    $this->SendEmail->sendForgotPasswordEmail($user['User']);
                    $this->Session->setFlash(__('Please check your email for Password.'), 'default', array(), 'auth');
                } elseif ($user['User']['status'] == 'pending') {
                    $this->Session->setFlash(__('Your account is not varified Yet, Please verify your account.'), 'default', array(), 'auth');
                } elseif ($user['User']['status'] == 'inactive') {
                    $this->Session->setFlash(__('Your account is Inactive, Please Contact Site User.'), 'default', array(), 'auth');
                } elseif ($user['User']['status'] == 'deleted') {
                    $this->Session->setFlash(__('Your account is Deleted, Please Contact Site User.'), 'default', array(), 'auth');
                }
            } else {
                $this->Session->setFlash(__('No matching Email Address Found.'), 'default', array(), 'auth');
            }
        }
    }

    public function delete($id = null)
    {
        $id = decrypt($id);
        $this->__checkPrevents('delete');
        $dealerList = array();
        if ($this->type == DEALER) {
            $dealerList = $this->User->getDealerListHaveCompany();
        }
        $error = $success = 0;
        $saveArray = array();
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $del_id = decrypt($del_id);
                $this->request->data['delete']['id'][$key] = $del_id;
                if (!isset($dealerList[$del_id])) {
                    $saveArray[$del_id] = $del_id;
                    $success++;
                } else {
                    $error++;
                }
            }
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->type == DEALER) {
                $this->request->data['delete']['id'] = $saveArray;
            }
            $userList = array_values($this->request->data['delete']['id']);
            $this->User->updateAll(array('status' => "'deleted'"), array('User.id' => $userList));
            if ($this->type == COMPANY) {
                $dealerList = $this->User->find('list', array(
                    'fields' => 'dealer_id, id',
                    'conditions' => array('User.id' => $userList)
                ));
                foreach ($dealerList as $dealId => $companyId) {
                    $this->User->updateCounterCache(array('dealer_id' => $dealId));
                    /**
                     * Delete from the dealer company
                     */
                    ClassRegistry::init('DealerCompany')->deleteAll(array(
                        'DealerCompany.company_id' => $companyId,
                        'DealerCompany.dealer_id' => $dealId,
                    ));
                }
            }
            /**
             * Delete child Users
             */
            $this->User->deleteChildUser($userList);
            if ($this->type == COMPANY) {
                /**
                 * Delete Company Branches
                 */
                ClassRegistry::init('CompanyBranch')->deleteCompanyBranches($userList);
            }
            $message = __('The %s has been deleted.', $this->type);
            if ($this->type == DEALER) {
                if (!empty($success)) {
                    $message = __("%s %s has been deleted.", $success, $this->type, $error);
                    if (!empty($error)) {
                        $message = __("%s %s couldn't deleted due to company exits.", $error, $this->type);
                    }
                } else {
                    $message = __("No any %s deleted due to company exists.", $this->type);
                }
            }
            $this->Message->setSuccess($message, $this->referer());
        }

        $this->User->id = $id;
        if ($this->type == DEALER && isset($dealerList[$id])) {
            $this->Message->setWarning(__('Unable to delete %s due to company exists.', $this->type), $this->referer());
        }
        if (!$this->User->exists()) {
            $this->Message->setWarning(__('Invalid User'), $this->referer());
        }
        if ($this->User->saveField('status', 'deleted')) {
            if ($this->type == COMPANY) {
                $dealerList = $this->User->find('list', array(
                    'fields' => 'dealer_id, id',
                    'conditions' => array('User.id' => $id)
                ));
                foreach ($dealerList as $dealId => $companyId) {
                    $this->User->updateCounterCache(array('dealer_id' => $dealId));
                    /**
                     * Delete from the dealer company
                     */
                    ClassRegistry::init('DealerCompany')->deleteAll(array(
                        'DealerCompany.company_id' => $companyId,
                        'DealerCompany.dealer_id' => $dealId,
                    ));
                }
            }
            /**
             * Delete child Users
             */
            $this->User->deleteChildUser($id);
            if ($this->type == COMPANY) {
                /**
                 * Delete Company Branches
                 */
                ClassRegistry::init('CompanyBranch')->deleteCompanyBranches($id);
            }
            $this->Message->setSuccess(__('The %s has been deleted.', $this->type));
        } else {
            $this->Message->setWarning(__('The %s could not be deleted. Please, try again.', $this->type));
        }
        // return $this->redirect(array('action' => 'index'));
        return $this->redirect($this->referer());

    }

    private function __getUserId($userId = null)
    {
        $sessionData = getMySessionData();
        if (empty($userId)) {
            //            $userId = $this->Session->read('Auth.User.id');
            $userId = $sessionData['id'];
            $this->set('profile', true);
        }
        return $userId;
    }

    public function getCompanyBranchAdmins($companyId = null, $wantHtml = false)
    {
        $this->layout = false;
        $branchAdmins = $this->User->getMyBranchUsers($companyId);
        $html = '';
        if ($wantHtml) {
            $html .= '<option value="">Select Branch Admin</option>';
            foreach ($branchAdmins as $dealId => $dealName) :
                $html .= '<option value="' . $dealId . '">' . $dealName . '</option>';
            endforeach;

            echo $html;
            exit;
        }
        return ($wantHtml) ? $html : $branchAdmins;
    }

    public function getCompanyDealers($companyId = null, $wantHtml = false)
    {
        $this->layout = false;
        $dealerList = $this->User->getCompanyDealersList($companyId);
        $html = '';
        if ($wantHtml) {
            foreach ($dealerList as $dealId => $dealName) :
                $html .= '<option value="' . $dealId . '">' . $dealName . '</option>';
            endforeach;

            echo $html;
            exit;
        }
        return ($wantHtml) ? $html : $dealerList;
    }

    public function getCompanies($dealId = null, $wantHtml = false, $dealData = array())
    {
        $isEmptyOpt = true;
        if ($dealId == 'multiselect') {
            //
            $isEmptyOpt = false;
            $dealId = $this->request->data;
            if (!empty($dealData)) {
                $dealId = $dealData;
            }
        }
        if (!empty($wantHtml)) {
            $this->layout = false;
        }
        $companyList = $this->User->getSuparCompanyListFromDeal($dealId);
        $html = '';
        if ($wantHtml) {
            if ($isEmptyOpt) {

                $html .= ' <option value="">Select Company</option>';
            }
            foreach ($companyList as $compId => $compName) :
                $html .= '<option value="' . $compId . '">' . $compName . '</option>';
            endforeach;

            echo $html;
            exit;
        }
        return ($wantHtml) ? $html : $companyList;
    }

    public function getBranches($companyId = null, $wantHtml = false, $compData = array())
    {
        $isEmptyOpt = true;
        if ($companyId == 'multiselect') {
            //
            $isEmptyOpt = false;
            $companyId = $this->request->data;
            if (!empty($compData)) {
                $companyId = $compData;
            }
        }
        if (!empty($wantHtml)) {
            $this->layout = false;
        }
        $branchList = ClassRegistry::init('CompanyBranch')->getMyBranchLists1($companyId);
        $html = '';
        if ($wantHtml) {
            if ($isEmptyOpt) {
                $html .= ' <option value="">Select Company</option>';
            }
            foreach ($branchList as $branchId => $branchName) :
                $html .= '<option value="' . $branchId . '">' . $branchName . '</option>';
            endforeach;
            echo $html;
            exit;
        }
        return ($wantHtml) ? $html : $branchList;
    }

    function change_status($userId = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of ' . $this->type));
        if ($this->User->exists($userId) && !empty($status)) {
            $this->User->id = $userId;
            $this->User->saveField('status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __($this->type .
                    ' status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }

    public function export($parent = null)
    {
        if (empty($parent)) {
            $parent = getNamedParameter($this->request->params['named']);
            if (isCompany()) {
                $parent = 'Company:' . encrypt(getCompanyId());
            }
        }
        $sessionData = getMySessionData();
        $this->layout = false;
        $success = 0;
        $error = 0;
        $countries = $this->User->Country->getCountryList();
        $states = $this->User->State->getStateList();
        $cities = $this->User->City->getCityList();
        $resConditions = $this->__getUserConditions();
        $parentId = $resConditions['parentId'];
        $parentDealer = $resConditions['parentDealer'];
        $conditions = $resConditions['conditions'];
        if ($this->Session->read($this->type . 'Search')) {
            $appCondition = $this->Session->read($this->type . 'Search');
        }
        if (!empty($appCondition)) {
            $appCondition = array_map('trim', array_filter($appCondition));
            if (!empty($appCondition)) {
                $appCondition = array_map('trim', $appCondition);
                if (!empty($appCondition)) {
                    if (isset($appCondition['name'])) {
                        $conditions['OR'] = array(
                            'User.first_name LIKE ' => '%' . $appCondition['name'] . '%',
                            'User.last_name LIKE ' => '%' . $appCondition['name'] . '%'
                        );
                    }
                    if (isset($appCondition['email'])) {
                        $conditions['User.email LIKE '] = '%' . $appCondition['email'] . '%';
                    }
                    if (isset($appCondition['user_type'])) {
                        $conditions['User.user_type'] = $appCondition['user_type'];
                    }
                    if (isset($appCondition['status'])) {
                        $conditions['User.status'] = $appCondition['status'];
                    }
                }
            }
        }

        //02-02-2016 
        if ($this->type == COMPANY && isDealer()) {
            unset($conditions['User.created_by']);
            //            $conditions['User.dealer_id'] = $this->Session->read('Auth.User.id');
            $conditions['User.dealer_id'] = $sessionData['id'];
        }
        //02-02-2016 
        if ($this->type == DEALER && isDealer()) {
            //            $conditions['User.parent_id'] = $this->Session->read('Auth.User.id');
            $conditions['User.parent_id'] = $sessionData['id'];
        }
        if($this->type == "Company"){
            unset($conditions['User.parent_id']);
            $conditions['User.company_parent_id'] = $sessionData['id'];
        }
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
            unset($conditions['User.parent_id']);
            $conditions['User.company_parent_id'] = $current_parent_id;
        }elseif(empty($isCompanyAdmin) && empty($isSuparCompany)){

        }elseif (!empty($isSuparCompany) && empty($isCompanyAdmin)) {
            unset($conditions['User.parent_id']);
            $conditions['User.company_parent_id'] = $sessionData['id'];
        }

        $user_role = $this->type;
        if (isCompany()) {
            $user_role = 'User';
        }
        $users = $this->User->find('all', array(
            'fields' => array('User.*'),
            'contain' => array(
                'CompanyBranch' => array(
                    'fields' => array(
                        'id', 'ftpuser', 'ftp_pass'
                    ),
                    'limit' => 1,
                    'order' => 'id ASC'
                )
            ),
            'order' => 'User.id DESC',
            'conditions' => $conditions
        ));
        $support_count = array();
        if (isDealer()) {
            $this->loadModel('DealerCompany');
            foreach ($users as $key => $usr) {
                $support_count[$usr['User']['id']] = $this->DealerCompany->find('count', array('conditions' => array('company_id' => $usr['User']['id'])));
            }
        }
        $message = __('No %s found.', $user_role);
        if (!empty($users)) {
            $objPHPExcel = new PHPExcel();
            $sheetName = date('Y_m_d_H_i_s') . '_' . $this->type;
            $objPHPExcel->getProperties()->setCreator($this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name'));
            $objPHPExcel->getProperties()->setTitle($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setSubject($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setDescription($sheetName . " Spreadsheet");
            //set header
            $objPHPExcel->setActiveSheetIndex(0);
            $first_name = 'First Name';
            $last_name = 'Last Name';
            if ($this->type == COMPANY) {
                $first_name = 'Company Name';
                if (!empty($parent)) {
                    $first_name = 'Name';
                }
                $last_name = 'Contact Name';
            }
            $excelHeader = array(
                'first_name' => $first_name,
                'email' => 'Email',
                'phone_no' => 'Phone No',
                'state_id' => 'State',
                'user_type' => 'Type',
                'status' => 'Status'
            );
            $excelHeaderWidth = array(
                'first_name' => '15',
                'email' => '33',
                'phone_no' => '15',
                'state_id' => '10',
                'user_type' => '12',
                'status' => '12'
            );
            if ($this->type == COMPANY) {
                unset($excelHeader['state_id']);
                unset($excelHeaderWidth['state_id']);
                if (!(!empty($parent) && isCompany())) {
                    unset($excelHeader['user_type']);
                    unset($excelHeaderWidth['user_type']);
                } else {
                    $excelHeader['user_type'] = 'User Role';
                    $excelHeader['last_login_time'] = 'Last Login';
                    $excelHeaderWidth['last_login_time'] = '15';
                }
                if (empty($parent)) {
                    $excelHeader['company_branch_count'] = 'No. Of Branches';
                    $excelHeaderWidth['company_branch_count'] = '10';
                    $excelHeader['sub_company_count'] = 'No. Of Support Users';
                    $excelHeaderWidth['sub_company_count'] = '10';
                }
                $excelHeader['created'] = 'Added On';
                $excelHeaderWidth['created'] = '15';
                if (empty($parent)) {
                    $excelHeader['ftp_user'] = 'Ftp User Name';
                    $excelHeaderWidth['ftp_user'] = '20';
                    $excelHeader['ftp_pass'] = 'Ftp User Password';
                    $excelHeaderWidth['ftp_pass'] = '20';
                }
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
                    if ($key == 'country_id') {
                        $user['User'][$key] = isset($countries[$user['User'][$key]]) ? $countries[$user['User'][$key]] : 'a';
                    }
                    if ($key == 'state_id') {
                        $user['User'][$key] = isset($states[$user['User'][$key]]) ? $states[$user['User'][$key]] : '';
                    }
                    if ($key == 'city_id') {
                        $user['User'][$key] = isset($cities[$user['User'][$key]]) ? $cities[$user['User'][$key]] : '';
                    }
                    if ($key == 'user_type') {
                        $user['User'][$key] = getLoginRole($user['User']['role'], $user['User']['user_type']);
                    }
                    if ($key == 'sub_company_count' && $this->type == COMPANY) {
                        $user['User'][$key] = isset($support_count[$user['User']['id']]) ? $support_count[$user['User']['id']] : 0;
                    }
                    if ($key == 'ftp_user') {
                        $user['User'][$key] = isset($user['CompanyBranch'][0]['ftpuser']) ? $user['CompanyBranch'][0]['ftpuser'] : '';
                    }
                    if ($key == 'ftp_pass') {
                        $user['User'][$key] = isset($user['CompanyBranch'][0]['ftp_pass']) ? $user['CompanyBranch'][0]['ftp_pass'] : '';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($col . $appCount, $user['User'][$key]);
                    $arr[$key] = $user['User'][$key];
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
            $message = __(
                'Unable to export %s.Please try again',
                $this->type
            );
        }
        $this->Message->setWarning($message, $this->referer());
    }

    function __getUserConditions($namedParams = array())
    {
        $sessionData = getMySessionData();
        $resArr = array(
            'conditions' => array(),
            'parentId' => 0,
            'parentDealer' => array()
        );
        //        $resArr['conditions'] = array('NOT' => array('User.id' => $this->Session->read('Auth.User.id'), 'AND' => array('User.role' => 'Admin', 'User.user_type' => SUPAR_ADM), 'User.status' => 'deleted'));
        $resArr['conditions'] = array('NOT' => array('User.id' => $sessionData['id'], 'AND' => array('User.role' => 'Admin', 'User.user_type' => SUPAR_ADM), 'User.status' => 'deleted'));
        if (isAdmin()) {
            $resArr['conditions']['User.user_type'] = SUPAR_ADM;
        }
        if (!isSuparAdmin()) {
            //            $resArr['conditions']['User.created_by'] = $this->Session->read('Auth.User.id');
            $resArr['conditions']['User.created_by'] = $sessionData['id'];
        }
        $params = getNamedParameter($namedParams, true);
        if (empty($params['type'])) {
            //dealer, company conditions
            $resArr['conditions']['User.parent_id'] = 0;
            $resArr['conditions']['User.role'] = $this->type;
            if ($this->type == COMPANY) {
                if (isSuparDealer()) {
                    unset($resArr['conditions']['User.created_by']);
                    //                    $resArr['conditions']['User.dealer_id'] = $this->Session->read('Auth.User.id');
                    $resArr['conditions']['User.dealer_id'] = $sessionData['id'];
                    $resArr['conditions']['User.user_type'] = SUPAR_ADM;
                }
                if (isSuparCompany() || isCompanyAdmin()) {
                    unset($resArr['conditions']['User.created_by']);
                    //                    $resArr['conditions']['User.parent_id'] = $this->Session->read('Auth.User.id');
                    $resArr['conditions']['User.parent_id'] = $sessionData['id'];
                    $resArr['conditions']['NOT']['User.user_type'] = SUPAR_ADM;
                }
            }
            if ($this->type == DEALER) {
                if (isSuparDealer()) {
                    unset($resArr['conditions']['User.created_by']);
                    //                    $resArr['conditions']['User.parent_id'] = $this->Session->read('Auth.User.id');
                    $resArr['conditions']['User.parent_id'] = $sessionData['id'];
                    $resArr['conditions']['NOT']['User.user_type'] = SUPAR_ADM;
                }
            }
            if (isSuparAdmin() || isAdminAdmin()) {
                if ($this->type == ADMIN) {
                    unset($resArr['conditions']['User.user_type']);
                    if (isSuparAdmin()) {
                        unset($resArr['conditions']['User.created_by']);
                    }
                }
            }
        } else {
            //sub dealer, sub company conditions
            $resArr['parentId'] = $params['value'];
            $resArr['conditions']['User.role'] = $params['type'];
            if (isAdmin()) {
                unset($resArr['conditions']['User.created_by']);
            }
            if ($this->type == COMPANY) {
                if (isSuparDealer()) {
                    unset($resArr['conditions']['User.created_by']);
                    //                    $resArr['conditions']['User.dealer_id'] = $this->Session->read('Auth.User.id');
                    $resArr['conditions']['User.dealer_id'] = $sessionData['id'];
                }
            }
            unset($resArr['conditions']['User.user_type']);
            $resArr['conditions']['NOT']['User.user_type'] = SUPAR_ADM;
            $resArr['conditions']['User.parent_id'] = $resArr['parentId'];
            $resArr['parentDealer'] = $this->User->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('User.id' => $resArr['parentId'])));
        }
        if (isSupportAdmin() && $this->type == DEALER) {
            $assignedDealerList = ClassRegistry::init('AdminUser')->getAssignedDealerList();
            $oldCondition = $resArr['conditions'];
            $resArr['conditions'] = array();
            $resArr['conditions']['OR'] = array(
                'User.id' => $assignedDealerList,
                $oldCondition
            );
        }
        if (isSupportAdmin() && $this->type == COMPANY) {
            $assignedCompanyList = ClassRegistry::init('AdminUser')->getAssignedCompanyList();
            $oldCondition = $resArr['conditions'];
            $resArr['conditions'] = array();
            $resArr['conditions']['OR'] = array(
                'User.id' =>
                $assignedCompanyList,
                $oldCondition
            );
        }
        return $resArr;
    }

    function __getUserTypeStatus($parentId = 0)
    {
        $isDisplayUserType = 0;
        /* supar admin */
        if ($this->type == ADMIN) {
            $isDisplayUserType = 1;
        }
        if (($this->type == DEALER || $this->type == COMPANY) && !empty($parentId)) {
            $isDisplayUserType = 1;
        }
        /* End supar admin */
        /* supar dealer */
        if (isSuparDealer() && $this->type == DEALER) {
            $isDisplayUserType = 1;
        }
        /* end supar dealer */
        if ((isSuparCompany() || isCompanyAdmin()) && $this->type == COMPANY) {
            $isDisplayUserType = 1;
        }
        return $isDisplayUserType;
    }

    function __getDealerStatus($parentId = 0)
    {
        $isDisplayDealer = 1;
        if (isSuparDealer() || isAdminDealer()) {
            $isDisplayDealer = 0;
        }
        if ((isSuparCompany() || isCompanyAdmin()) && $this->type == COMPANY
        ) {
            $isDisplayDealer = 0;
        }
        return $isDisplayDealer;
    }

    function __getParentCompanyStatus($parentId = 0)
    {
        $isDisplayParentCompany = 0;
        if (isSuparDealer() && $this->type == COMPANY && !empty($parentId)) {
            $isDisplayParentCompany = 1;
        }
        return $isDisplayParentCompany;
    }

    function user_dashboard($id = 0)
    {
        $id = decrypt($id);
        $user_detail = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $id)));
        $startDate = !empty($this->request->data['start_date']) ? $this->request->data['start_date'] : '';
        $endDate = !empty($this->request->data['end_date']) ? $this->request->data['end_date'] : '';
        $totalTrans = $totalErros = $totalPFiles = 0;
        $conditions = array();
        if (!($user_detail['User']['role'] == ADMIN && $user_detail['User']['user_type'] == SUPAR_ADM)) {
            $conditions['created_by'] = $user_detail['User']['created_by'];
        }
        if (!empty($this->request->data)) {
            $conditions = array('created_date >= ' => $startDate, 'created_date <= ' => $endDate);
        }
        $totalPFiles = ClassRegistry::init('FileProccessingDetail')->getCountProcessedFiles($conditions);
        $totalTrans = ClassRegistry::init('TransactionDetail')->getCountTransaction($conditions);
        unset($conditions['created_by']);
        if (!empty($this->request->data)) {
            $conditions = array('entry_timestamp >= ' => $startDate, 'entry_timestamp <= ' => $endDate);
        }
        $totalErros = ClassRegistry::init('ErrorDetail')->getCountErroredFiles($conditions);

        if ($this->request->is('ajax')) {
            $responseArr = array(
                'status' => 'success',
                'transactions' => array(
                    'url' => Router::url(array('action' => 'dashboard'), true),
                    'totalTrans' => $totalTrans
                ),
                'errors' => array(
                    'url' => Router::url(array('action' => 'dashboard'), true),
                    'totalErros' => $totalErros
                ),
                'files' => array(
                    'url' => Router::url(array('action' => 'dashboard'), true),
                    'totalPFiles' => $totalPFiles
                )
            );
            echo json_encode($responseArr);
            exit;
        }
        $this->set(compact('totalTrans', 'totalErros', 'totalPFiles', 'user_detail'));
        /**
         * tickets for the dealer admin
         * @param type $id
         */
        $tickets = array(
            'New' => array(),
            'Open' => array(),
            'Closed' => array()
        );
        if ($user_detail['User']['role'] == DEALER) {
            $this->Ticket = ClassRegistry::init('Ticket');
            $companyList = $this->User->find('list', array(
                'contain' => false,
                'conditions' => array(
                    'User.dealer_id' => $id,
                    'User.role' => COMPANY,
                    'User.user_type' => SUPAR_ADM
                ),
                'fields' => array('User.id'),
            ));

            $tickets['New'] = $this->Ticket->find('all', array(
                'contain' => array(
                    'Company',
                    'Dealer',
                    'Branch'
                ),
                'conditions' => array(
                    'Ticket.company_id' => $companyList,
                    'Ticket.status' => 'New'
                )
            ));

            $tickets['Open'] = $this->Ticket->find('all', array(
                'contain' => array(
                    'Company',
                    'Dealer',
                    'Branch'
                ),
                'conditions' => array(
                    'Ticket.company_id' => $companyList,
                    'Ticket.status' => 'Open'
                )
            ));
            $tickets['Closed'] = $this->Ticket->find('all', array(
                'contain' => array(
                    'Company',
                    'Dealer',
                    'Branch'
                ),
                'conditions' => array(
                    'Ticket.company_id' => $companyList,
                    'Ticket.status' =>
                    'Closed'
                )
            ));
        }
        $this->set(compact('tickets'));
    }

    function __checkPrevents($action = 'add')
    {
        //prevent dealer to add,edit,delete  company
        if ($this->type == COMPANY && isDealer() && in_array($action, array('add', 'edit', 'delete'))) {
            //25-01-2016 removed as required to add company if login as dealer
            //            $this->Message->setWarning(__('You are not an authorised user to access this.'), $this->referer());
        }
        //prevent dealer to add,edit,delete,index admins
        if ($this->type == ADMIN && !(isAdminAdmin() || isSuparAdmin()) && in_array($action, array('add', 'edit', 'delete', 'index'))) {
            $this->Message->setWarning(__('You are not an authorised user to access this.'), $this->referer());
        }
    }

    function pageForPagination($model)
    {
        $page = 1;
        $sameModel = isset($this->params['named']['Model']) && $this->params['named']['Model'] == $model;
        $pageInUrl = isset($this->params['named']['page']);
        if ($sameModel && $pageInUrl) {
            $page = $this->params['named']['page'];
        }
        $this->passedArgs['page'] = $page;
        return $page;
    }

    function configuration()
    {
        $this->set('title', __('Configuration'));
        $this->render('/Pages/unknown');
    }

    function set_branch_session($branchId = null)
    {
        if (!empty($branchId)) {
            if (ClassRegistry::init('CompanyBranch')->exists($branchId)) {
                $branchDetail = ClassRegistry::init('CompanyBranch')->find('first', array('contain' => false, 'conditions' => array('id' => $branchId)));
                $branchDetail = $branchDetail['CompanyBranch'];
                $this->Session->write('Auth.User.BranchDetail', $branchDetail);
                $this->Message->setSuccess(__('Branch Set successfully'), array('controller' => 'users', 'action' => 'dashboard'));
            } elseif ($branchId == 'all') {
                $this->Session->delete('Auth.User.BranchDetail');
                $this->Message->setSuccess(__('All Branch Set successfully'), array('controller' => 'users', 'action' => 'dashboard'));
            }
        }
        $this->Message->setWarning(__('Unable to set branch'), $this->referer());
    }

    function set_company_session($companyId = null)
    {
        $companyId = decrypt($companyId);
        if (!empty($companyId)) {
            if (ClassRegistry::init('User')->exists($companyId)) {
                $companyDetail = ClassRegistry::init('User')->find('first', array('contain' => false, 'conditions' => array('id' => $companyId)));
                $companyDetail = $companyDetail['User'];
                $this->Session->write('Auth.User.companyDetail', $companyDetail);
                $this->loadModel('BranchAdmin');
                $assignedBranches = $this->BranchAdmin->find('list', array(
                    'contain' => array('Branch' => array('fields' => array('id', 'name'))),
                    'conditions' => array('BranchAdmin.admin_id' => $userDetail['User']['id']),
                    'fields' => array('branch_id', 'Branch.name')
                ));
                $this->Session->write('Auth.User.companyDetail.assign_branches', $assignedBranches);
                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            }
        }
        $this->Message->setWarning(__('Unable to get company data'), $this->referer());
    }

    function set_dealer_session($companyId = null)
    {
        $companyId = decrypt($companyId);
        if (!empty($companyId)) {
            if (ClassRegistry::init('User')->exists($companyId)) {
                $companyDetail = ClassRegistry::init('User')->find('first', array('contain' => false, 'conditions' => array('id' => $companyId)));
                $companyDetail = $companyDetail['User'];
                $this->Session->write('Auth.User.dealerDetail', $companyDetail);
                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            }
        }
        $this->Message->setWarning(__('Unable to get company data'), $this->referer());
    }

    function remove_session()
    {
        if ($this->Session->check('Auth.User.companyDetail')) {
            $this->Session->delete('Auth.User.companyDetail');
            $this->redirect(array('controller' => 'companies', 'action' => 'index', 'type' => 'analytics'));
        } else {
            if ($this->Session->check('Auth.User.dealerDetail')) {
                $this->Session->delete('Auth.User.dealerDetail');
            }
            $this->redirect(array('controller' => 'dealers', 'action' => 'index', 'type' => 'analytics'));
        }
    }

    public function email_read()
    {
        $this->loadModel('Notification');
        $emails = $this->ReadEmail->fetch_by_last_sync();
        if (!empty($emails['emails'])) {
            foreach ($emails['emails'] as $key => $value) {
                $this->Notification->create();
                $data['Notification'] = $value;
                if ($this->Notification->save($data)) {
                    echo "success<br>";
                } else {
                    echo "Not success<br>";
                }
            }
        } else {
            echo "EMAIL NOT FOUND";
        }
        $this->loadModel('SiteConfig');
        $this->SiteConfig->updateSyncDate();
        Cache::delete('siteConfig');
        echo $emails['totalEmails'] . " messages found\n";
        exit;
    }

    function upload_branch()
    {
        $this->loadModel('Region');
        $this->loadModel('Country');
        $this->loadModel('State');
        $this->loadModel('City');
        $this->loadModel('CompanyBranch');
        $sessionData = getMySessionData();
        $lastId = $this->Session->read('lastcompanyId');
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        $regions = $this->Region->getRegionList($conditions);
        $countries = $this->Country->getCountryList();
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $key_main => $row) {
                        if ($key_main != 0) {
                            $data = explode(",", $row);
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if ($bulkData[$key] == 'Region') {
                                        $regionList[$value] = $value;
                                    } elseif ($bulkData[$key] == 'Country') {
                                        $countryList[$value] = $value;
                                        $alldata[$key_main]['country'] = $value;
                                    } elseif ($bulkData[$key] == 'State') {
                                        $stateList[$value] = $value;
                                        $alldata[$key_main]['state'] = $value;
                                    } elseif ($bulkData[$key] == 'City') {
                                        $cityList[$value] = $value;
                                        $alldata[$key_main]['city'] = $value;
                                    } elseif ($bulkData[$key] == 'Email') {
                                        $emailCheck[] = trim($value);
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Region", "Branch Name", "Contact Name", "Email", "Phone", "Address 1", "Address 2", "Country", "State", "City", "Zip");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $duplicates = array_diff_assoc($emailCheck, array_unique($emailCheck));
                    if (!empty($duplicates)) {
                        $dupEmail = implode(" , ", $duplicates);
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The  ' . $dupEmail  . ' emails already exist')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    if (!empty($regionList)) {
                        foreach ($regionList as $key => $value) {
                            $conditions['name'] = $value;
                            $conditions['company_id'] = $lastId;
                            $regionExist = '';
                            $regionExist = $this->Region->getRegionList($conditions);
                            $regiondata = [];
                            if (empty($regionExist)) {
                                $regiondata['Region']['status'] = 'Active';
                                $regiondata['Region']['company_id'] = $lastId;
                                $regiondata['Region']['name'] = $value;
                                $this->Region->create();
                                if ($this->Region->save($regiondata)) {
                                    $regionId = $this->Region->id;
                                    $regionList[$value] = $regionId;
                                }
                            } else {
                                $regionList[$value] = key($regionExist);
                            }
                        }
                    } else {
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The data is not valid!!  Please select a valid file!!')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    $alldata = array_map("unserialize", array_unique(array_map("serialize", $alldata)));
                    $subData = [];
                    if (!empty($alldata)) {
                        foreach ($alldata as $key1 => $value) {
                            $subData = $value;
                            $countryId = '';
                            $stateId = '';
                            foreach ($subData as $key2 => $value_second) {
                                if ($key2 == 'country') {
                                    $country_conditions['name'] = $value_second;
                                    $country_conditions['Country.status'] = 'active';
                                    $countryExist = $this->Country->getCountryList2($country_conditions);
                                    if (empty($countryExist)) {
                                        $countriesData['Country']['name'] = $value_second;
                                        $this->Country->create();
                                        if ($this->Country->save($countriesData)) {
                                            $countryId = $this->Country->id;
                                            $countryList[$value_second] = $countryId;
                                        }
                                    } else {
                                        $countryId = key($countryExist);
                                        $countryList[$value_second] = key($countryExist);
                                    }
                                } elseif ($key2 == 'state') {
                                    $state_conditions['country_id'] = $countryId;
                                    $state_conditions['name'] = $value_second;
                                    $state_conditions['status'] = 'active';
                                    $stateExist = $this->State->getStateList2($state_conditions);
                                    if (empty($stateExist)) {
                                        $stateData['State']['country_id'] = $state_conditions['country_id'];
                                        $stateData['State']['name'] = $value_second;
                                        $this->State->create();
                                        if ($this->State->save($stateData)) {
                                            $stateId = $this->State->id;
                                            $countryId = $this->State->country_id;
                                            $stateList[$value_second] = $stateId;
                                        }
                                    } else {
                                        $stateId = key($stateExist);
                                        $stateList[$value_second] = key($stateExist);
                                    }
                                } elseif ($key2 == 'city') {
                                    $city_conditions['country_id'] = $countryId;
                                    $city_conditions['state_id'] = $stateId;
                                    $city_conditions['name'] = $value_second;
                                    $city_conditions['status'] = 'active';
                                    $cityExist = $this->City->getCityList2($city_conditions);
                                    if (empty($cityExist)) {
                                        $cityData['City']['country_id'] = $city_conditions['country_id'];
                                        $cityData['City']['state_id'] = $city_conditions['state_id'];
                                        $cityData['City']['name'] = $value_second;
                                        $this->City->create();
                                        if ($this->City->save($cityData)) {
                                            $cityId = $this->City->id;
                                            $cityList[$value_second] = $cityId;
                                        }
                                    } else {
                                        $cityId = key($cityExist);
                                        $cityList[$value_second] = key($cityExist);
                                    }
                                }
                            }
                        }
                    }
                    $setFlag = 0;
                    foreach ($file as $keyMain => $value) {
                        if ($keyMain != 0) {
                            $checkData = explode(",", $value);
                            foreach ($checkData as $key => $check) {
                                if (trim($bulkData[$key]) == 'Branch Name') {
                                    $checkbranchData[$keyMain]['branch_name'] = $check;
                                } elseif (trim($bulkData[$key]) == 'City') {
                                    $checkbranchData[$keyMain]['city'] = $cityList[$check];
                                } elseif (trim($bulkData[$key]) == 'Zip') {
                                    $checkbranchData[$keyMain]['zip'] = trim($check);
                                } elseif (trim($bulkData[$key]) == 'Region') {
                                    $checkbranchData[$keyMain]['region'] = trim($check);
                                } elseif (trim($bulkData[$key]) == 'Email') {
                                    $emailCheck[] = trim($check);
                                }
                            }
                        }
                    }
                    $branchExist = array();
                    if (!empty($checkbranchData)) {
                        foreach ($checkbranchData as $key => $branchQuery) {
                            $branchFind['name'] = $branchQuery['branch_name'];
                            $branchFind['regiones'] = $regionList[$branchQuery['region']];
                            $branchFind['city'] = $branchQuery['city'];
                            $branchFind['zipcode'] = $branchQuery['zip'];
                            $branchFind['company_id'] = $lastId;
                            $branchExist[] = $this->CompanyBranch->find('first', array('contain' => false, 'fields' => 'name,id', 'conditions' => $branchFind));
                        }
                    }

                    $emailExist = array();
                    if (!empty($emailCheck)) {
                        foreach ($emailCheck as $key => $email) {
                            $emailFind['email'] = $email;
                            $emailExist[] = $this->CompanyBranch->find('first', array('contain' => false, 'fields' => 'email,id', 'conditions' => $emailFind));
                        }
                    }
                    $branchExist = array_filter($branchExist);
                    $emailExist = array_filter($emailExist);
                    if (empty($branchExist) && empty($emailExist)) {
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $$branchData = [];
                                foreach ($allData as $key => $value) {
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if ($bulkData[$key] == 'Region') {
                                            $branchData['CompanyBranch']['regiones'] = $regionList[$value];
                                        } elseif ($bulkData[$key] == 'Branch Name') {
                                            $branchData['CompanyBranch']['name'] = $value;
                                        } elseif ($bulkData[$key] == 'Contact Name') {
                                            $branchData['CompanyBranch']['contact_name'] = $value;
                                        } elseif ($bulkData[$key] == 'Email') {
                                            $branchData['CompanyBranch']['email'] = $value;
                                        } elseif ($bulkData[$key] == 'Phone') {
                                            $branchData['CompanyBranch']['phone'] = $value;
                                        } elseif ($bulkData[$key] == 'Address 1') {
                                            $branchData['CompanyBranch']['address'] = $value;
                                        } elseif ($bulkData[$key] == 'Address 2') {
                                            $branchData['CompanyBranch']['address2'] = $value;
                                        } elseif ($bulkData[$key] == 'Country') {
                                            $branchData['CompanyBranch']['country'] = $countryList[$value];
                                        } elseif ($bulkData[$key] == 'State') {
                                            $branchData['CompanyBranch']['state'] = $stateList[$value];
                                        } elseif ($bulkData[$key] == 'City') {
                                            $branchData['CompanyBranch']['city'] = $cityList[$value];
                                        } elseif (trim($bulkData[$key]) == 'Zip') {
                                            $branchData['CompanyBranch']['zipcode'] = trim($value);
                                        }
                                    }
                                }
                                $branchData['CompanyBranch']['status'] = 1;
                                $branchData['CompanyBranch']['company_id'] = $conditions['company_id'];
                                $branchData['CompanyBranch']['created_by'] = $sessionData['id'];
                                $branchData['CompanyBranch']['updated_by'] = $sessionData['id'];
                                $this->CompanyBranch->create();
                                if (!empty($branchData)) {
                                    if ($this->CompanyBranch->save($branchData)) {
                                        if (!empty($branchData['CompanyBranch']['company_id'])) {
                                            $userDetails = ClassRegistry::init('User')->getMailDetails($branchData['CompanyBranch']['company_id']);
                                            $branchDetail = $this->CompanyBranch->find('first', array('contain' => false, 'conditions' => array('CompanyBranch.id' => $this->CompanyBranch->id)));
                                            $arrData = array(
                                                'User' => $userDetails,
                                                'Branch' => $branchDetail['CompanyBranch']
                                            );
                                            $companyAdmins = $this->User->getAllAdminOfCompanyByCompanyId($sessionData['id']);
                                            $this->SendEmail->sendBranchNotifyEmail($arrData, 'add', $companyAdmins);
                                        }
                                    } else {
                                        $setFlag = 1;
                                    }
                                }
                            }
                        }
                    } else {
                        $foundedBranch = '';
                        foreach ($branchExist as $key => $value) {
                            if (!empty($value)) {
                                $foundedBranch .=  $value['CompanyBranch']['name'] . ",";
                            }
                        }
                        $foundedBranch = substr_replace($foundedBranch, '', -1);
                        $foundedEmail = '';
                        foreach ($emailExist as $key => $value) {
                            $foundedEmail .= $value['CompanyBranch']['email'] . ", ";
                        }
                        $foundedEmail = substr_replace($foundedEmail, '', -1);
                        if (!empty($foundedBranch)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedBranch . ' Branches already exists')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                        if (!empty($foundedEmail)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedEmail . ' Emails already exists')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                    if ($setFlag != 1) {
                        $responseArr = array(
                            'status' => 'succsess',
                            'message' => __('This CompanyBranches has been saved')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                }
            }
            exit;
        }
    }

    function upload_stations()
    {
        $this->loadModel('CompanyBranch');
        $this->loadModel('Region');
        $this->loadModel('Station');
        $this->loadModel('User');
        $sessionData = getMySessionData();
        $lastId = $this->Session->read('lastcompanyId');
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                // $responseArr = '';
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $key_main => $row) {
                        if ($key_main != 0) {
                            $data = explode(",", $row);
                            // $branchList[]= array();
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if ($bulkData[$key] == 'Branch Name') {
                                        $branchList[$key_main]['branch_name'] = $value;
                                    } elseif (trim($bulkData[$key]) == 'Zip') {
                                        $branchList[$key_main]['zip'] = trim($value);
                                    } elseif (trim($bulkData[$key]) == 'Station Sr no') {
                                        $serialNo[] = $value;
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Branch Name", "Station Code", "Name", "Station Sr no", "City", "Zip");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $notfoundBranch = array();
                    foreach ($branchList as $key => $value) {
                        $branchConditions['name'] = $value['branch_name'];
                        $branchConditions['zipcode'] = trim($value['zip']);
                        $branchConditions['company_id'] = $lastId;
                        $branchConditions['branch_status'] = 'active';
                        $branchExist = $this->CompanyBranch->getMyBranchList2($branchConditions);
                        if (!empty($branchExist)) {
                            $branchList2[$value['zip']] = key($branchExist);
                        } else {
                            $notfoundBranch[] = $value;
                        }
                    }
                    $foundedSrno = array();
                    foreach ($serialNo as $key => $value) {
                        $serialnoCondition['serial_no'] = $value;
                        $serialnoExist = $this->Station->find('first', array('contain' => false, 'fields' => 'name,id,serial_no', 'conditions' => $serialnoCondition));
                        if (!empty($serialnoExist)) {
                            $foundedSrno[] = $serialnoExist;
                        }
                    }
                    $setFlag = 0;
                    if (empty($notfoundBranch) && empty($foundedSrno)) {
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $stationData = [];
                                foreach ($allData as $key => $value) {
                                    $zip = '';
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if (trim($bulkData[$key]) == 'Zip') {
                                            $stationData['Station']['branch_id'] = $branchList2[trim($value)];
                                        } elseif (trim($bulkData[$key]) == 'Station Code') {
                                            $stationData['Station']['station_code'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Name') {
                                            $stationData['Station']['name'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Station Sr no') {
                                            $stationData['Station']['serial_no'] = trim($value);
                                        }
                                    }
                                }
                                $stationData['Station']['status'] = 'Active';
                                $stationData['Station']['company_id'] = $lastId;
                                $stationData['Station']['created_by'] = $sessionData['id'];
                                $stationData['Station']['updated_by'] = $sessionData['id'];
                                $this->Station->create();
                                if ($this->Station->save($stationData)) {
                                } else {
                                    $setFlag = 1;
                                }
                            }
                        }
                        if ($setFlag != 1) {
                            $responseArr = array(
                                'status' => 'succsess',
                                'message' => __('This Stations has been saved')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    } else {
                        $foundedBranch = '';
                        foreach ($notfoundBranch as $key => $value) {
                            if (!empty($value)) {
                                $foundedBranch .=  $value['branch_name'] . ",";
                            }
                        }
                        $foundedBranch = substr_replace($foundedBranch, '', -1);
                        $foundedserialNo = '';
                        foreach ($foundedSrno as $key => $value) {
                            if (!empty($value)) {
                                $foundedserialNo .=  $value['Station']['serial_no'] . ",";
                            }
                        }
                        $foundedserialNo = substr_replace($foundedserialNo, '', -1);
                        if (!empty($foundedBranch)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedBranch . ' Branches not found')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                        if (!empty($foundedserialNo)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedserialNo . ' Serial No already exist')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                }
            }
        }
        // $responseArr = 'The station has been saved.';
        // return json_encode($responseArr);
    }

    function upload_users(Type $var = null)
    {
        $this->loadModel('CompanyBranch');
        $this->loadModel('Station');
        $this->loadModel('User');
        $this->loadModel('Region');
        $sessionData = getMySessionData();
        $lastId = $this->Session->read('lastcompanyId');
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                // $responseArr = '';
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $keyMain => $row) {
                        if ($keyMain != 0) {
                            $data = explode(",", $row);
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if ($bulkData[$key] == 'Branch Name') {
                                        $branchList[$value] = $value;
                                        $checkbranchData[$keyMain]['branch_name'] = trim($value);
                                    } elseif (trim($bulkData[$key]) == 'Region') {
                                        $regionList[trim($value)] = trim($value);
                                        $checkbranchData[$keyMain]['region'] = trim($value);
                                    } elseif (trim($bulkData[$key]) == 'Email') {
                                        $emailCheck[] = trim($value);
                                        $checkbranchData[$keyMain]['email'] = trim($value);
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Name", "Email", "Phone", "User Type", "Branch Name", "Region");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $duplicates = array_diff_assoc($emailCheck, array_unique($emailCheck));
                    if (!empty($duplicates)) {
                        $dupEmail = implode(" , ", $duplicates);
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The  ' . $dupEmail  . ' emails already exist')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    foreach ($regionList as $key => $value) {
                        $conditions['name'] = $value;
                        $regionExist = '';
                        $regionExist = $this->Region->getRegionList($conditions);
                        $regiondata = [];
                        if (empty($regionExist)) {
                            $regiondata['Region']['status'] = 'Active';
                            $regiondata['Region']['company_id'] = $lastId;
                            $regiondata['Region']['name'] = $value;
                            $this->Region->create();
                            if ($this->Region->save($regiondata)) {
                                $regionId = $this->Region->id;
                                $regionList[$value] = $regionId;
                            }
                        } else {
                            $regionList[$value] = key($regionExist);
                        }
                    }
                    $emailExist = array();
                    if (!empty($emailCheck)) {
                        foreach ($emailCheck as $key => $email) {
                            $emailFind['email'] = $email;
                            $emailExist[] = $this->User->find('first', array('contain' => false, 'fields' => 'email,id', 'conditions' => $emailFind));
                        }
                    }
                    $emailExist = array_filter($emailExist);
                    $notfoundBranch = array();
                    foreach ($checkbranchData as $key => $value) {
                        $branchConditions['name'] = $value['branch_name'];
                        $branchConditions['regiones'] = $regionList[$value['region']];
                        $branchConditions['company_id'] = $lastId;
                        $branchConditions['branch_status'] = 'active';
                        $branchExist = $this->CompanyBranch->getMyBranchList2($branchConditions);
                        if (!empty($branchExist)) {
                            $branchList2[$value['email']] = key($branchExist);
                        } else {
                            $notfoundBranch[] = $value['branch_name'];
                        }
                    }
                    if (empty($notfoundBranch) && empty($emailExist)) {
                        $setFlag = 0;
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $userData = [];
                                foreach ($allData as $key => $value) {
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if (trim($bulkData[$key]) == 'Name') {
                                            $userData['User']['first_name'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Email') {
                                            $userData['User']['email'] = $value;
                                            $userData['User']['regions'] = $branchList2[$value];
                                        } elseif (trim($bulkData[$key]) == 'Phone') {
                                            $userData['User']['phone_no'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'User Type') {
                                            $userData['User']['user_type'] = trim($value);
                                        } elseif (trim($bulkData[$key]) == 'Branch Name') {
                                            $userData['User']['branch_id'] = $branchList[$value];
                                        }
                                    }
                                }
                                $userData['User']['role'] = 'Company';
                                $userData['User']['status'] = 'active';
                                $userData['User']['password'] = getrandompassword();
                                $userData['User']['confirm_password'] = $userData['User']['password'];
                                $userData['User']['parent_id'] = $lastId;
                                $userData['User']['created_by'] = $sessionData['id'];
                                $userData['User']['updated_by'] = $sessionData['id'];
                                $this->User->create();
                                if ($this->User->save($userData)) {
                                    if (!empty($userData['User'])) {
                                        $this->SendEmail->sendAccountCreatedEmail($userData['User']);
                                    }
                                } else {
                                    $setFlag = 1;
                                }
                            }
                        }
                        if ($setFlag != 1) {
                            $responseArr = array(
                                'status' => 'succsess',
                                'message' => __('This User has been saved')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    } else {
                        if (!empty($notfoundBranch)) {
                            $notfoundBranch = implode(" , ", $notfoundBranch);

                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $notfoundBranch . ' Branches not found')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                        if (!empty($emailExist)) {
                            $foundedEmail = '';
                            foreach ($emailExist as $key => $value) {
                                $foundedEmail .= $value['User']['email'] . ",";
                            }

                            $foundedEmail = substr_replace($foundedEmail, '', -1);
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedEmail . ' Email already exist')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                }
            }
        }
    }

    function downloadSamplefile($fileName = null)
    {
        $fileName = base64_decode($fileName);
        $this->viewClass = 'Media';
        $params = array(
            'id'        => "$fileName.csv",
            'name'      => $fileName,
            'extension' => 'csv',
            'download'  => true,
            'path'      => WWW_ROOT . SAMPLE_FILE
        );
        $this->set($params);
    }
    function send_instruction(Type $var = null)
    {
        if ($this->request->is('ajax')) {
            $flagCheck = 0;
            $this->autoRender = false;
            if (!empty($this->request->data)) {
                $sendemail_int = $this->request->data['formData'];
                if (!empty($sendemail_int)) {
                    $sendemail_int = explode(",", $sendemail_int);
                    foreach ($sendemail_int as $key => $value) {
                        $result = $this->SendEmail->sendinstructionMail($value);
                      $result = 1;
                        if ($result != 1) {
                            $flagCheck = 1;
                        }
                    }
                    if ($flagCheck != 1) {
                        $responseArr = array(
                            'status' => 'succsess',
                            'message' => __('The Instruction Support mail has been send!!')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                }
            }
        }
    }
    function complete_process(Type $var = null)
    {
        $lastId = $this->Session->read('lastcompanyId');
        $this->loadModel('User');
        $this->loadModel('Region');
        $userDetail = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $lastId)));
        $userCount = $this->User->find('count', array('contain' => false, 'conditions' => array('User.parent_id' => $lastId)));
        $regionCount = $this->Region->find('count', array('contain' => false, 'conditions' => array('Region.company_id' => $lastId)));
        $this->set(compact('userDetail', 'userCount', 'regionCount'));
    }

    function dashboard_data_station($type = null)
    {
        $this->loadModel('CompanyBranch');
        $sessData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        $DashboardFilter = $this->Session->read('DashboardFilter');
        if(!empty($DashboardFilter)){
            $this->request->data['Analytic']['regiones'] = $DashboardFilter['regiones'];
            $this->request->data['Analytic']['branch_id'] = $DashboardFilter['branch_id'];
        }
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        $this->set(compact('regiones','branches',  'stations'));
        $stationCondition = array();
        if (!empty($type)) {
            if ($type == 'all') {
                $stationCondition = array(
                    'Station.company_id' => $company_id,
                    'Station.status' => 'active',
                    'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'Station.branch_id' => $this->request->data['Analytic']['branch_id']
                );
                if(empty($this->request->data['Analytic']['regiones'])){
                    unset($stationCondition['CompanyBranch.regiones']);
                }
                if(empty($this->request->data['Station']['branch_id'])){
                    unset($stationCondition['Station.branch_id']);
                }
            }
            if ($type == 'active') {
                $stationCondition = array(
                    'Station.company_id' => $company_id,
                    'Station.status' => 'active',
                    'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
                    'Station.file_processed_count >' => 0,
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'Station.branch_id' => $this->request->data['Analytic']['branch_id']
                );
                if(empty($this->request->data['Analytic']['regiones'])){
                    unset($stationCondition['CompanyBranch.regiones']);
                }
                if(empty($this->request->data['Analytic']['branch_id'])){
                    unset($stationCondition['Station.branch_id']);
                }
            }
            if ($type == 'inactive') {
                $stationCondition = array(
                    'Station.company_id' => $company_id,
                    'Station.status' => 'active',
                    'Station.updated >' => date('Y-m-d', strtotime("-6 months")),
                    'Station.file_processed_count <=' => 0,
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'Station.branch_id' => $this->request->data['Analytic']['branch_id']
                );
                if(empty($this->request->data['Analytic']['regiones'])){
                    unset($stationCondition['CompanyBranch.regiones']);
                }
                if(empty($this->request->data['Analytic']['branch_id'])){
                    unset($stationCondition['Station.branch_id']);
                }
            }
        } else {
            $this->Message->setWarning(__('Something went wrong'));
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $this->loadModel('Station');
        $this->AutoPaginate->setPaginate(array(
            'conditions' => $stationCondition,
            'contain' => ['CompanyBranch'],
        ));
        $stationsData = $this->paginate('Station');
        $this->set(compact('stationsData','temp_companydata'));
    }
    function dashboard_data_transaction($var = null)
    {
        $this->loadModel('Analytic');
        $this->loadModel('TransactionDetail');
        ini_set('memory_limit', '-1');
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        // echo '<pre><b></b><br>';
        // print_r($this->request->data);echo '<br>';exit;
        // $DashboardFilter = $this->Session->read('DashboardFilter');
        // if(!empty($DashboardFilter) && !empty($DashboardFilter['regiones'])){
        //     $this->request->data['Analytic']['regiones'] = $DashboardFilter['regiones'];
        //     $this->request->data['Analytic']['branch_id'] = $DashboardFilter['branch_id'];
        // }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerActivityFilter');
            $this->Session->delete('Report.TellerActivityReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TellerActivityFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TellerActivityFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $all_condition = $this->Session->read('Report.TellerActivityCondition');
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));

        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        $sessData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
        $this->loadModel('Region');
        $this->loadModel('CompanyBranch');
        $conditions['company_id'] = $company_id;//$sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions);
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $filter_conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            $filter_conditions['FileProccessingDetail.branch_id IN'] = $branchLists;

        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
        }
        $this->set(compact('regiones','branches','sessData', 'stations'));
        if ($var == 'all') {
            $repConditions = $this->Session->check('Report.GlobalFilter') ? $this->Session->read('Report.GlobalFilter') : [];
            if ($this->Session->check('Pagination.limit')) {
                $Paginate = $this->Session->read('Pagination.limit');
            } else {
                $Paginate = 50;
            }
            $page = 1;
            if (isset($this->request->params['named']['Paginate'])) {
                $Paginate = $this->request->params['named']['Paginate'];
            }
            if (isset($this->request->params['named']['page'])) {
                $page = $this->request->params['named']['page'];
            }
            $limit_start = ($Paginate * ($page - 1));
            $limit = "  LIMIT $limit_start , $Paginate";

            $startDate = $repConditions['start_date'] . ' 00:00:00';
            $endDate = $repConditions['end_date'] . ' 23:59:59';
            $sessionData = getMySessionData();
            $conditions = array(
                'TransactionDetail.trans_datetime >=' =>  $startDate,
                'TransactionDetail.trans_datetime <=' =>  $endDate,
                'FileProccessingDetail.company_id' => $company_id,
            );
            if(!empty($filter_conditions['FileProccessingDetail.branch_id'])){
                $conditions['FileProccessingDetail.branch_id'] = $filter_conditions['FileProccessingDetail.branch_id'];
            }
            // $tranctionCount = $this->TransactionDetail->find('count', $conditions);
            $dataCondition = [
                'conditions' => $conditions,
                    'fields' => array('count(TransactionDetail.id) as transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
            ];
            $tranctionCount = $this->TransactionDetail->find('all', $dataCondition);
 
            $total_Count = array();
            foreach ($tranctionCount as $key => $value) {
                $total_Count[] = $value[0]['transaction_count'];
            }
            $total_count = array_sum($total_Count);


            $tellerCount = $this->TransactionDetail->find('count', array('conditions' => $conditions,'fields' => 'DISTINCT teller_name '));
            $totalAvg = round($total_count / $tellerCount, 2);
            $telledata = array();
            $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name'));
            foreach ($tellerNames as $key => $value) {
                $conditions  = array(
                    'TransactionDetail.teller_name' => $value['TransactionDetail']['teller_name'],
                    'TransactionDetail.trans_datetime >=' =>  $startDate,
                    'TransactionDetail.trans_datetime <=' =>  $endDate,
                    'FileProccessingDetail.company_id' => $company_id,
                );
                if(!empty($filter_conditions['FileProccessingDetail.branch_id'])){
                    $conditions['FileProccessingDetail.branch_id'] = $filter_conditions['FileProccessingDetail.branch_id'];
                }
                $Telleravg = $this->TransactionDetail->find('count', array('conditions' => $conditions));
                if ($totalAvg >= $Telleravg) {
                    $telledata['belowavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                    // $avgCount['belowavg'] = $Telleravg;
                } elseif ($totalAvg < $Telleravg) {
                    // $avgCount['aboveavg'] = $Telleravg;
                    $telledata['aboveavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                }
            }
            $countData = count($telledata['aboveavg']);
            foreach ($telledata as $key_main => $values) {
                foreach ($values as $key_sub => $sub_value) {
                    foreach ($sub_value as $key => $value) {
                        if (in_Array($value['TransactionDetail']['trans_type_id'], array(1, 2, 11))) {
                            $cash_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        } else {
                            $admin_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        }
                        $data[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] = $value;
                    }
                }
            }
            foreach ($cash_transaction as $key_main => $cash_transactions) {
                foreach ($cash_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $cash_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['cash_transaction'] = count($value);
                    }
                }
            }
            foreach ($admin_transaction as $key_main => $admin_transactions) {
                foreach ($admin_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $admin_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['admin_transaction'] = count($value);
                    }
                }
            }
           foreach ($all_transaction as $key => $all_transaction_data) {
                foreach ($all_transaction_data as $key_sub => $values) {
                    $cash_transaction = !empty($values['cash_transaction']) ? $values['cash_transaction'] : 0;
                    $admin_transaction = !empty($values['admin_transaction']) ? $values['admin_transaction'] : 0;
                    $all_transaction_count = $cash_transaction + $admin_transaction;
                    if ($totalAvg >= $all_transaction_count) {
                        $all_transaction[$key][$key_sub]['type'] = 'belowavg';
                    } elseif ($totalAvg < $all_transaction_count) {
                        $all_transaction[$key][$key_sub]['type'] = 'aboveavg';
                    }
                    if(count($all_transaction[$key]) > 1){
                        $multi_count = 0;
                        foreach ($all_transaction[$key] as $multi_key => $multi_tra) {
                            $cash_transaction = !empty($multi_tra['cash_transaction']) ? $multi_tra['cash_transaction'] : 0;
                            $admin_transaction = !empty($multi_tra['admin_transaction']) ? $multi_tra['admin_transaction'] : 0;
                            $all_transaction_count = $cash_transaction + $admin_transaction;
                            $multi_count = $multi_count + $all_transaction_count;
                            if ($totalAvg >= $multi_count) {
                                $all_transaction[$key][$multi_key]['type'] = 'belowavg';
                            } elseif ($totalAvg < $multi_count) {
                                $all_transaction[$key][$multi_key]['type'] = 'aboveavg';
                            }
                          
                        }
                   
                    
                    }
                }
           }
            $branchConditions = array(
                'CompanyBranch.company_id' =>$company_id,
                'CompanyBranch.status' => 'active',
            );
            $branchList = $this->CompanyBranch->find('list', array(
                'fields' => 'id, name',
                'conditions' => $branchConditions,
                'contain' => false,
            ));

            $regionesList = $this->CompanyBranch->find('all', array(
                'fields' => 'id, name,regiones',
                'conditions' => $branchConditions,
                'contain' => false,
            ));
            $condition_region['company_id'] = $company_id;
            $regione_set = $this->Region->getRegionList($condition_region);
            $set_region = array();
            foreach ($regionesList as $key => $value) {
                $set_region[$value['CompanyBranch']['id']] = $regione_set[$value['CompanyBranch']['regiones']];
            }
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('all_transaction', 'branchList', 'set_region', 'sessionData', 'option_filter','top_left'));
        }

        if ($var == 'above') {
            $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
            $repConditions = $this->Session->check('Report.GlobalFilter') ? $this->Session->read('Report.GlobalFilter') : [];
            $startDate = $repConditions['start_date'] . ' 00:00:00';
            $endDate = $repConditions['end_date'] . ' 23:59:59';
            $sessionData = getMySessionData();
            if ($this->Session->check('Pagination.limit')) {
                $Paginate = $this->Session->read('Pagination.limit');
            } else {
                $Paginate = 50;
            }
            $page = 1;
            if (isset($this->request->params['named']['Paginate'])) {
                $Paginate = $this->request->params['named']['Paginate'];
            }
            if (isset($this->request->params['named']['page'])) {
                $page = $this->request->params['named']['page'];
            }
            $limit_start = ($Paginate * ($page - 1));
            $limit = "  LIMIT $limit_start , $Paginate";
           
            $conditions = array(
                'FileProccessingDetail.file_date >=' =>  $startDate,
                'FileProccessingDetail.file_date <=' =>  $endDate,
                'FileProccessingDetail.company_id' => $company_id,//$sessData['id'],
            );
            if(!empty($filter_conditions['FileProccessingDetail.branch_id'])){
                $conditions['FileProccessingDetail.branch_id'] = $filter_conditions['FileProccessingDetail.branch_id'];
            }
            $this->loadModel('TransactionDetail');
        
            $dataCondition = [
                'conditions' => $conditions,
                    'fields' => array('count(TransactionDetail.id) as transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
            ];
            $tranctionCount = $this->TransactionDetail->find('all', $dataCondition);
 
            $total_Count = array();
            foreach ($tranctionCount as $key => $value) {
                $total_Count[] = $value[0]['transaction_count'];
            }
            $total_count = array_sum($total_Count);
         
            $tellerCount = $this->TransactionDetail->find('count', array('conditions' => $conditions,'fields' => 'DISTINCT teller_name '));
            $totalAvg = round($total_count / $tellerCount, 2);
            $avgCount = array();
            $this->loadModel('TransactionDetail');
            $telledata = array();
            $tellerNames = $this->TransactionDetail->find('all', array('conditions' => $conditions,'fields' => 'DISTINCT teller_name '));
            foreach ($tellerNames as $key => $value) {
                $conditions  = array(
                    'TransactionDetail.teller_name' => $value['TransactionDetail']['teller_name'],
                    'TransactionDetail.trans_datetime >=' =>  $startDate,
                    'TransactionDetail.trans_datetime <=' =>  $endDate,
                    'FileProccessingDetail.company_id' => $sessData['id'],
                );
                $Telleravg = $this->TransactionDetail->find('count', array('conditions' => $conditions));
                if ($totalAvg >= $Telleravg) {
                    // $telledata['belowavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                    // $avgCount['belowavg'] = $Telleravg;
                } elseif ($totalAvg < $Telleravg) {
                    // $avgCount['aboveavg'] = $Telleravg;
                    $telledata['aboveavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                }
            }
            // foreach ($telledata as $key_main => $values) {
            //     foreach ($values as $key_sub => $sub_value) {
            //         foreach ($sub_value as $key => $value) {
            //             if (in_Array($value['TransactionDetail']['trans_type_id'], array(1, 2, 11))) {

            //                 $cash_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
            //             } else {
            //                 $admin_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
            //             }
            //             $data[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] = $value;
            //         }
            //     }
            // }
            $countData = count($telledata['aboveavg']);
            foreach ($telledata as $key_main => $values) {
                foreach ($values as $key_sub => $sub_value) {
                    foreach ($sub_value as $key => $value) {
                        if (in_Array($value['TransactionDetail']['trans_type_id'], array(1, 2, 11))) {
                            // $cash_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
                            $cash_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        } else {
                            // $admin_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];

                            $admin_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        }
                        $data[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] = $value;
                    }
                }
            }

            foreach ($cash_transaction as $key_main => $cash_transactions) {
                foreach ($cash_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $cash_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['cash_transaction'] = count($value);
                    }
                }
            }
            foreach ($admin_transaction as $key_main => $admin_transactions) {
                foreach ($admin_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $admin_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['admin_transaction'] = count($value);
                    }
                }
            }

            $branchConditions = array(
                'CompanyBranch.company_id' => $company_id,// $sessData['id'],
                'CompanyBranch.status' => 'active',
            );
            $branchList = $this->CompanyBranch->find('list', array(
                'fields' => 'id, name',
                'conditions' => $branchConditions,
                'contain' => false,
            ));

            $regionesList = $this->CompanyBranch->find('all', array(
                'fields' => 'id, name,regiones',
                'conditions' => $branchConditions,
                'contain' => false,
            ));
            $condition_region['company_id'] = $sessData['id'];
            $regione_set = $this->Region->getRegionList($condition_region);
            $set_region = array();
            foreach ($regionesList as $key => $value) {
                $set_region[$value['CompanyBranch']['id']] = $regione_set[$value['CompanyBranch']['regiones']];
            }

            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('all_transaction', 'branchList', 'set_region', 'sessionData', 'option_filter','top_left'));
        }
        if ($var == 'below') {
            if ($this->Session->check('Pagination.limit')) {
                $Paginate = $this->Session->read('Pagination.limit');
            } else {
                $Paginate = 50;
            }
            $page = 1;
            if (isset($this->request->params['named']['Paginate'])) {
                $Paginate = $this->request->params['named']['Paginate'];
            }
            if (isset($this->request->params['named']['page'])) {
                $page = $this->request->params['named']['page'];
            }
            $limit_start = ($Paginate * ($page - 1));
            $limit = "  LIMIT $limit_start , $Paginate";
            $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
            // $startDate = $repConditions['start_date'];
            // $endDate = $repConditions['end_date'];
            $repConditions = $this->Session->check('Report.GlobalFilter') ? $this->Session->read('Report.GlobalFilter') : [];
            $startDate = $repConditions['start_date'] . ' 00:00:00';
            $endDate = $repConditions['end_date'] . ' 23:59:59';
            $sessionData = getMySessionData();
            $conditions = array(
                'FileProccessingDetail.file_date >=' =>  $startDate,
                'FileProccessingDetail.file_date <=' =>  $endDate,
                'FileProccessingDetail.company_id' => $company_id,//$sessData['id'],
            );
            if(!empty($filter_conditions['FileProccessingDetail.branch_id'])){
                $conditions['FileProccessingDetail.branch_id'] = $filter_conditions['FileProccessingDetail.branch_id'];
            }
            $this->loadModel('TransactionDetail');
            $dataCondition = [
                'conditions' => $conditions,
                    'fields' => array('count(TransactionDetail.id) as transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
            ];
            $tranctionCount = $this->TransactionDetail->find('all', $dataCondition);
 
            $total_Count = array();
            foreach ($tranctionCount as $key => $value) {
                $total_Count[] = $value[0]['transaction_count'];
            }
            $total_count = array_sum($total_Count);
            $tellerCount = $this->TransactionDetail->find('count', array('conditions' => $conditions,'fields' => 'DISTINCT teller_name'));
            $totalAvg = round($total_count / $tellerCount, 2);
            $avgCount = array();
            $this->loadModel('TransactionDetail');
            $telledata = array();
            $tellerNames = $this->TransactionDetail->find('all', array('conditions' => $conditions,'fields' => 'DISTINCT teller_name '));
            foreach ($tellerNames as $key => $value) {
                $conditions  = array(
                    'TransactionDetail.teller_name' => $value['TransactionDetail']['teller_name'],
                    'TransactionDetail.trans_datetime >=' =>  $startDate,
                    'TransactionDetail.trans_datetime <=' =>  $endDate,
                    'FileProccessingDetail.company_id' => $company_id,//$sessData['id'],
                );
                $Telleravg = $this->TransactionDetail->find('count', array('conditions' => $conditions));
                if ($totalAvg >= $Telleravg) {
                    $telledata['belowavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                    // $avgCount['belowavg'] = $Telleravg;
                } elseif ($totalAvg < $Telleravg) {
                    // $avgCount['aboveavg'] = $Telleravg;
                    // $telledata['aboveavg'][] = $this->TransactionDetail->find('all', array('conditions' => $conditions));
                }
            }
            // foreach ($telledata as $key_main => $values) {
            //     foreach ($values as $key_sub => $sub_value) {
            //         foreach ($sub_value as $key => $value) {
            //             if (in_Array($value['TransactionDetail']['trans_type_id'], array(1, 2, 11))) {

            //                 $cash_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
            //             } else {
            //                 $admin_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
            //             }
            //             $data[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] = $value;
            //         }
            //     }
            // }
            $countData = count($telledata['belowavg']);
            
            foreach ($telledata as $key_main => $values) {
                foreach ($values as $key_sub => $sub_value) {
                    foreach ($sub_value as $key => $value) {
                        if (in_Array($value['TransactionDetail']['trans_type_id'], array(1, 2, 11))) {
                            // $cash_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];
                            $cash_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        } else {
                            // $admin_transaction[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] =  $value['TransactionDetail']['teller_name'];

                            $admin_transaction[$key_main][$value['TransactionDetail']['teller_name']][$value['FileProccessingDetail']['branch_id']][] =  $value['TransactionDetail']['teller_name'];
                        }
                        $data[$key_main][$value['FileProccessingDetail']['branch_id']][$value['TransactionDetail']['teller_name']][] = $value;
                    }
                }
            }
            foreach ($cash_transaction as $key_main => $cash_transactions) {
                foreach ($cash_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $cash_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['cash_transaction'] = count($value);
                    }
                }
            }
            foreach ($admin_transaction as $key_main => $admin_transactions) {
                foreach ($admin_transactions as $key_sub => $values) {
                    foreach ($values as $key => $value) {
                        $admin_transaction2[][$key_sub][$key] = count($value);
                        $all_transaction[$key_sub][$key]['admin_transaction'] = count($value);
                    }
                }
            }

            $branchConditions = array(
                'CompanyBranch.company_id' => $company_id,//$sessData['id'],
                'CompanyBranch.status' => 'active',
            );
            $branchList = $this->CompanyBranch->find('list', array(
                'fields' => 'id, name',
                'conditions' => $branchConditions,
                'contain' => false,
            ));

            $regionesList = $this->CompanyBranch->find('all', array(
                'fields' => 'id, name,regiones',
                'conditions' => $branchConditions,
                'contain' => false,
            ));
            $condition_region['company_id'] = $sessData['id'];
            $regione_set = $this->Region->getRegionList($condition_region);
            $set_region = array();
            foreach ($regionesList as $key => $value) {
                $set_region[$value['CompanyBranch']['id']] = $regione_set[$value['CompanyBranch']['regiones']];
            }

            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('all_transaction', 'branchList', 'set_region', 'option_filter','top_left'));
        }
    }

    function allTransactionData($var = null)
    {
        if (empty($var)) {
            $var = 'byHour';
        }
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $sessionData = getMySessionData();
        // Comman company id
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        // $DashboardFilter = $this->Session->read('DashboardFilter');
        // if(!empty($DashboardFilter) && !empty($DashboardFilter['regiones'])){
        //     $this->request->data['Analytic']['regiones'] = $DashboardFilter['regiones'];
        //     $this->request->data['Analytic']['branch_id'] = $DashboardFilter['branch_id'];
        // }

        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $regionconditions['company_id'] = $company_id;
        // if (!empty($this->request->data['Analytic']['regiones'])) {
        //     $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
        // }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id ='] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                // if (!empty($branches)) {
                //     $conditions['FileProccessingDetail.branch_id'] = $branches;
                // }
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
                $conditions_regiones = array(
                    'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                    'CompanyBranch.branch_status' => 'active',
                );
                $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                    'fields' => 'id, id',
                    'contain' => false,
                    'conditions' => $conditions_regiones
                ));
                $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
                // $conditions['FileProccessingDetail.branch_id'] = $branches;

            }
            
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station ='] = $this->request->data['Analytic']['station'];
            }
        }
        $regiones = $this->Region->getRegionList($regionconditions);

        $this->set(compact('regiones', 'temp_station','branches','stations'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        if (!empty($allFilesID)) {
            foreach ($allFilesID as $key => $value) {
                array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
            }
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }
        if ($var == 'daily') {
            // Daily Data

            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.DAY,m.regiones order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
           
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($var == 'weekly') {
            // Weekly Data

            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`CompanyBranches`.`regiones`) as m  GROUP BY m.WEEK,m.regiones order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($var == 'monthly') {
            // Monthly Data

            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.MONTHNAME,m.regiones order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));

            // $monthofYear_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($monthofYear_Arr['monthly']);
            // $allData = $this->paginate('TransactionDetail');
            $this->set(compact('allData'));
        } elseif ($var == 'yearly') {
            // Yearly Data
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.YEAR,m.regiones order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($var == 'branchDaily') {
            $dayofweek_Arr = $this->Analytic->getTransactionallData($conditions);
            $this->AutoPaginate->setPaginate($dayofweek_Arr['branchDaily']);
            $allData = $this->paginate('TransactionDetail');
            $this->set(compact('allData'));
        } elseif ($var == 'byHour') {


            $query_string = "SELECT m.HOUR, m.HOURw,m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, `TransactionDetail`.`trans_datetime` as HOURw, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.HOUR,m.regiones order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
     
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            // $byHour_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($byHour_Arr['byHour']);
            // $allData = $this->paginate('TransactionDetail');
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        }
    }

    function dashboard_daily($var = null, $action = null, $userName = null)
    {
      
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
       
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $regionconditions['company_id'] = $company_id;
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $region_id = !empty($this->request->query['regiones_id']) ? $this->request->query['regiones_id'] : '';
        $branch_id = !empty($this->request->query['branch_id']) ? $this->request->query['branch_id'] : '';
        $station_id = !empty($this->request->query['station_id']) ? $this->request->query['station_id'] : '';
        if(!empty($region_id)){
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $region_id,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if(!empty($branch_id)){
            $conditions['FileProccessingDetail.branch_id ='] = $branch_id;
        }
        if(!empty($station_id)){
            $conditions['FileProccessingDetail.station ='] = $station_id;
        }
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }
        if ($action == 'branchDaily') {
            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.DAY order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'stationDaily') {
            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.DAY,m.station order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'userDaily') {

            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`

                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.DAY, m.teller_name order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.DAY,m.station order by m.DAY_NUMBER";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                $total_page = ceil($countData / $Paginate);
                if ($page <= 5) {
                    $page_start = 1;
                } else if ($total_page > 9 && ($total_page - $page) > 3) {
                    $page_start = $page - 5;
                } else if (($total_page - $page) < 3) {
                    $page_start = $total_page - 8;
                } else {
                    $page_start = $page - 5;
                }
                $page_end = $total_page;
                if ($total_page > 9 && $page <= 5) {
                    $page_end = 9;
                } else if ($total_page > 9 && $page > 5) {
                    if ($total_page > ($page + 3)) {
                        $page_end = $page + 3;
                    } else {
                        $page_end = $total_page;
                    }
                }
                if ($page_end == 1) {
                    $page_end = 0;
                }
                $this->set(compact('allData'));
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        }
    }
    function dashboard_weekly($var = null, $action = null, $userName = null)
    {

        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
       
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $regionconditions['company_id'] = $company_id;
        $region_id = !empty($this->request->query['regiones_id']) ? $this->request->query['regiones_id'] : '';
        $branch_id = !empty($this->request->query['branch_id']) ? $this->request->query['branch_id'] : '';
        $station_id = !empty($this->request->query['station_id']) ? $this->request->query['station_id'] : '';
        if(!empty($region_id)){
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $region_id,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if(!empty($branch_id)){
            $conditions['FileProccessingDetail.branch_id ='] = $branch_id;
        }
        if(!empty($station_id)){
            $conditions['FileProccessingDetail.station ='] = $station_id;
        }
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'branchWeekly') {
            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.WEEK,m.branch_id order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            // echo '<pre><b></b><br>';
            // print_r($sql);echo '<br>';exit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($action == 'stationWeekly') {

            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`station`) as m  GROUP BY m.WEEK,m.station order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            // $stationWeekly_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($stationWeekly_Arr['stationWeekly']);
            // $allData = $this->paginate('TransactionDetail');
            // $this->set(compact('allData'));
        } elseif ($action == 'userWeekly') {
                $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                ) AS COUNT,
                (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                `FileProccessingDetail`.`id`,
                `FileProccessingDetail`.`branch_id`,
                `FileProccessingDetail`.`company_id`,
                `FileProccessingDetail`.`file_date`,
                `FileProccessingDetail`.`station`,
                `TransactionDetail`.`teller_name`,
                `CompanyBranches`.`regiones`
            FROM
                `dynalytics`.`transaction_details` AS `TransactionDetail`
            LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
            ON
                (
                    `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                )
            LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
            ON
                (
                    `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                )
            INNER JOIN `dynalytics`.`regions` AS `regions`
            ON
                (
                    `regions`.`id` = `CompanyBranches`.`regiones`
                )";
                $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`TransactionDetail`.`teller_name`) as m  GROUP BY m.WEEK,m.teller_name order by m.WEEK";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                $total_page = ceil($countData / $Paginate);
                if ($page <= 5) {
                    $page_start = 1;
                } else if ($total_page > 9 && ($total_page - $page) > 3) {
                    $page_start = $page - 5;
                } else if (($total_page - $page) < 3) {
                    $page_start = $total_page - 8;
                } else {
                    $page_start = $page - 5;
                }
                $page_end = $total_page;
                if ($total_page > 9 && $page <= 5) {
                    $page_end = 9;
                } else if ($total_page > 9 && $page > 5) {
                    if ($total_page > ($page + 3)) {
                        $page_end = $page + 3;
                    } else {
                        $page_end = $total_page;
                    }
                }
                if ($page_end == 1) {
                    $page_end = 0;
                }
                $this->set(compact('allData'));
                $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
                $this->set(compact('top_left'));
        } elseif ($action == 'userdetails') {
                if (!empty($userName)) {
                    $conditions += array(
                        'TransactionDetail.teller_name' => $userName
                    );
                    $conditions_count = 1;
                    $conditions_new = '';
                    foreach ($conditions as $key => $value) {
                        if ($conditions_count == 1) {
                            $conditions_count++;
                        } else {
                            $conditions_new = $conditions_new . " AND ";
                        }
                        if (is_array($value)) {
                            $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                        } else {
                            if ($key == 'TransactionDetail.teller_name') {
                                $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                            } else {
                                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                            }
                        }
                    }
                    $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                ) AS COUNT,
                (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                `FileProccessingDetail`.`id`,
                `FileProccessingDetail`.`branch_id`,
                `FileProccessingDetail`.`company_id`,
                `FileProccessingDetail`.`file_date`,
                `FileProccessingDetail`.`station`,
                `TransactionDetail`.`teller_name`,
                `CompanyBranches`.`regiones`
            FROM
                `dynalytics`.`transaction_details` AS `TransactionDetail`
            LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
            ON
                (
                    `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                )
            LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
            ON
                (
                    `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                )
            INNER JOIN `dynalytics`.`regions` AS `regions`
            ON
                (
                    `regions`.`id` = `CompanyBranches`.`regiones`
                )";
                    $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`station`) as m  GROUP BY m.WEEK,m.teller_name order by m.WEEK";
                    $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                    $allData = $this->TransactionDetail->query($sql);
                    $countData = count($allData);
                    $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                    $total_page = ceil($countData / $Paginate);
                    if ($page <= 5) {
                        $page_start = 1;
                    } else if ($total_page > 9 && ($total_page - $page) > 3) {
                        $page_start = $page - 5;
                    } else if (($total_page - $page) < 3) {
                        $page_start = $total_page - 8;
                    } else {
                        $page_start = $page - 5;
                    }
                    $page_end = $total_page;
                    if ($total_page > 9 && $page <= 5) {
                        $page_end = 9;
                    } else if ($total_page > 9 && $page > 5) {
                        if ($total_page > ($page + 3)) {
                            $page_end = $page + 3;
                        } else {
                            $page_end = $total_page;
                        }
                    }
                    if ($page_end == 1) {
                        $page_end = 0;
                    }
                    $this->set(compact('allData'));
                    $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
                    $this->set(compact('top_left'));
            }
        }
    }


    function dashboard_monthly($var = null, $action = null, $userName = null)
    {

        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $regionconditions['company_id'] = $company_id;

        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $region_id = !empty($this->request->query['regiones_id']) ? $this->request->query['regiones_id'] : '';
        $branch_id = !empty($this->request->query['branch_id']) ? $this->request->query['branch_id'] : '';
        $station_id = !empty($this->request->query['station_id']) ? $this->request->query['station_id'] : '';
        if(!empty($region_id)){
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $region_id,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if(!empty($branch_id)){
            $conditions['FileProccessingDetail.branch_id ='] = $branch_id;
        }
        if(!empty($station_id)){
            $conditions['FileProccessingDetail.station ='] = $station_id;
        }
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'hourmonthly') {
            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.MONTHNAME order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'stationmonth') {
            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.MONTHNAME,m.station order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'usermonth') {

            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`

                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.MONTHNAME, m.teller_name order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.MONTHNAME,m.station order by m.MONTHNAME";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                $total_page = ceil($countData / $Paginate);
                if ($page <= 5) {
                    $page_start = 1;
                } else if ($total_page > 9 && ($total_page - $page) > 3) {
                    $page_start = $page - 5;
                } else if (($total_page - $page) < 3) {
                    $page_start = $total_page - 8;
                } else {
                    $page_start = $page - 5;
                }
                $page_end = $total_page;
                if ($total_page > 9 && $page <= 5) {
                    $page_end = 9;
                } else if ($total_page > 9 && $page > 5) {
                    if ($total_page > ($page + 3)) {
                        $page_end = $page + 3;
                    } else {
                        $page_end = $total_page;
                    }
                }
                if ($page_end == 1) {
                    $page_end = 0;
                }
                $this->set(compact('allData'));
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        }
    }
    function dashboard_yearly($var = null, $action = null,  $userName = null)
    {

        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $regionconditions['company_id'] = $company_id;
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];

       $region_id = !empty($this->request->query['regiones_id']) ? $this->request->query['regiones_id'] : '';
        $branch_id = !empty($this->request->query['branch_id']) ? $this->request->query['branch_id'] : '';
        $station_id = !empty($this->request->query['station_id']) ? $this->request->query['station_id'] : '';
        if(!empty($region_id)){
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $region_id,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if(!empty($branch_id)){
            $conditions['FileProccessingDetail.branch_id ='] = $branch_id;
        }
        if(!empty($station_id)){
            $conditions['FileProccessingDetail.station ='] = $station_id;
        }
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'branchYearly') {
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.YEAR,m.regiones order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($action == 'stationyearly') {
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.station order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($action == 'useryearly') {

                    $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `TransactionDetail`.`teller_name`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`,`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.teller_name,m.station order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.station order by m.YEAR";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                $total_page = ceil($countData / $Paginate);
                if ($page <= 5) {
                    $page_start = 1;
                } else if ($total_page > 9 && ($total_page - $page) > 3) {
                    $page_start = $page - 5;
                } else if (($total_page - $page) < 3) {
                    $page_start = $total_page - 8;
                } else {
                    $page_start = $page - 5;
                }
                $page_end = $total_page;
                if ($total_page > 9 && $page <= 5) {
                    $page_end = 9;
                } else if ($total_page > 9 && $page > 5) {
                    if ($total_page > ($page + 3)) {
                        $page_end = $page + 3;
                    } else {
                        $page_end = $total_page;
                    }
                }
                if ($page_end == 1) {
                    $page_end = 0;
                }
                $this->set(compact('allData'));
            }
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        }
    }

    function dashboard_hour($var = null, $action = null, $userName = null)
    {

        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $stationData = $this->stations->find('all');
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'FileProccessingDetail.company_id =' => $company_id
        );
        $region_id = !empty($this->request->query['regiones_id']) ? $this->request->query['regiones_id'] : '';
        $branch_id = !empty($this->request->query['branch_id']) ? $this->request->query['branch_id'] : '';
        $station_id = !empty($this->request->query['station_id']) ? $this->request->query['station_id'] : '';
        if(!empty($region_id)){
            $conditions_regiones = array(
                'CompanyBranch.regiones' => $region_id,
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if(!empty($branch_id)){
            $conditions['FileProccessingDetail.branch_id ='] = $branch_id;
        }
        if(!empty($station_id)){
            $conditions['FileProccessingDetail.station ='] = $station_id;
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }

        $regionconditions['company_id'] = $company_id;
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'hourbranch') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.HOUR, m.branch_id order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }

            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
        } elseif ($action == 'stationhour') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.HOUR, m.station order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData =  count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            // $stationhour_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($stationhour_Arr['hourStation']);
            // $allData = $this->paginate('TransactionDetail');
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            $this->set(compact('allData'));
        } elseif ($action == 'userhour') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.HOUR, m.teller_name order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
            $total_page = ceil($countData / $Paginate);
            if ($page <= 5) {
                $page_start = 1;
            } else if ($total_page > 9 && ($total_page - $page) > 3) {
                $page_start = $page - 5;
            } else if (($total_page - $page) < 3) {
                $page_start = $total_page - 8;
            } else {
                $page_start = $page - 5;
            }
            $page_end = $total_page;
            if ($total_page > 9 && $page <= 5) {
                $page_end = 9;
            } else if ($total_page > 9 && $page > 5) {
                if ($total_page > ($page + 3)) {
                    $page_end = $page + 3;
                } else {
                    $page_end = $total_page;
                }
            }
            if ($page_end == 1) {
                $page_end = 0;
            }
            // $stationhour_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($stationhour_Arr['hourStation']);
            // $allData = $this->paginate('TransactionDetail');
            $this->set(compact('allData'));
            $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
            $this->set(compact('top_left'));
            // $userhour_Arr = $this->Analytic->getTransactionallData($conditions);
            // $this->AutoPaginate->setPaginate($userhour_Arr['hourUser']);
            // $allData = $this->paginate('TransactionDetail');
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }

                $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.HOUR, m.teller_name order by m.HOUR";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit);
                $total_page = ceil($countData / $Paginate);
                if ($page <= 5) {
                    $page_start = 1;
                } else if ($total_page > 9 && ($total_page - $page) > 3) {
                    $page_start = $page - 5;
                } else if (($total_page - $page) < 3) {
                    $page_start = $total_page - 8;
                } else {
                    $page_start = $page - 5;
                }
                $page_end = $total_page;
                if ($total_page > 9 && $page <= 5) {
                    $page_end = 9;
                } else if ($total_page > 9 && $page > 5) {
                    if ($total_page > ($page + 3)) {
                        $page_end = $page + 3;
                    } else {
                        $page_end = $total_page;
                    }
                }
                if ($page_end == 1) {
                    $page_end = 0;
                }
                $this->set(compact('allData'));
                $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $countData, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
                $this->set(compact('top_left'));
            }
        }
    }


    function dashboard_inventory(Type $var = null)
    {
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
    }
   
    function bulk_users(Type $var = null) 
    {
        $this->Session->delete('Report.bulk_usersIds');
        $this->loadModel('CompanyBranch');
        $this->loadModel('Station');
        $this->loadModel('User');
        $this->loadModel('Region');
        $sessionData = getMySessionData();
        $lastId = $this->Session->read('lastcompanyId');
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        if ($this->request->is('ajax')) {
            
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $keyMain => $row) {
                        if ($keyMain != 0) {
                            $data = explode(",", $row);
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if (trim($bulkData[$key]) == 'Email') {
                                        $emailCheck[] = trim($value);
                                        $checkbranchData[$keyMain]['email'] = trim($value);
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("First Name","Last Name", "Email", "Phone","Gender","User Type");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $duplicates = array_diff_assoc($emailCheck, array_unique($emailCheck));
                    if (!empty($duplicates)) {
                        $dupEmail = implode(" , ", $duplicates);
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The  ' . $dupEmail  . ' emails already exist')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    $emailExist = array();
                    if (!empty($emailCheck)) {
                        foreach ($emailCheck as $key => $email) {
                            $emailFind['email'] = $email;
                            $emailExist[] = $this->User->find('first', array('contain' => false, 'fields' => 'email,id', 'conditions' => $emailFind));
                        }
                    }
                    $emailExist = array_filter($emailExist);
                    if (empty($emailExist)) {
                        $setFlag = 0;
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $userData = [];
                                foreach ($allData as $key => $value) {
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if (trim($bulkData[$key]) == 'First Name') {
                                            $userData['User']['first_name'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Last Name') {
                                            $userData['User']['last_name'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Email') {
                                            $userData['User']['email'] = $value;
                                        }elseif (trim($bulkData[$key]) == 'Phone') {
                                            $userData['User']['phone_no'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'User Type') {
                                            $userData['User']['user_type'] = trim($value);
                                        }elseif (trim($bulkData[$key]) == 'Gender') {
                                            $userData['User']['gender'] = trim($value);
                                        }
                                    }
                                }
                                $userData['User']['role'] = 'Company';
                                $userData['User']['status'] = 'active';
                                $userData['User']['password'] = getrandompassword();
                                $userData['User']['confirm_password'] = $userData['User']['password'];
                                $userData['User']['parent_id'] = $sessionData['id'];
                                $userData['User']['created_by'] = $sessionData['id'];
                                $userData['User']['updated_by'] = $sessionData['id'];
                                $this->User->create();
                                if ($this->User->save($userData)) {
                                    $users_ids[]=$this->User->id; //contains insert_ids
                                    if (!empty($userData['User'])) {
                                        $this->SendEmail->sendAccountCreatedEmail($userData['User']);
                                    }
                                } else {
                                    $setFlag = 1;
                                }
                            }
                        }
                        if (!empty($users_ids)) {
                            $this->Session->write('Report.bulk_usersIds',$users_ids);
                        }
                        if ($setFlag != 1) {
                            $responseArr = array(
                                'status' => 'succsess',
                                'message' => __('This User has been saved')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }else {
                        if (!empty($emailExist)) {
                            $foundedEmail = '';
                            foreach ($emailExist as $key => $value) {
                                $foundedEmail .= $value['User']['email'] . ",";
                            }

                            $foundedEmail = substr_replace($foundedEmail, '', -1);
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedEmail . ' Email already exist')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                    
                }
            }
        }
    }

    function bulk_regions(Type $var = null) 
    {
        $this->Session->delete('Report.bulk_regionIds');
        $this->loadModel('CompanyBranch');
        $this->loadModel('Station');
        $this->loadModel('User');
        $this->loadModel('Region');
        $sessionData = getMySessionData();
        $lastId = $this->Session->read('lastcompanyId');
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        if ($this->request->is('ajax')) {
            
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $keyMain => $row) {
                        if ($keyMain != 0) {
                            $data = explode(",", $row);
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if (trim($bulkData[$key]) == 'Region') {
                                        $regionCheck[] = trim($value);
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Sr.No","Region");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $duplicates = array_diff_assoc($regionCheck, array_unique($regionCheck));
                    if (!empty($duplicates)) {
                        $dupEmail = implode(" , ", $duplicates);
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The  ' . $dupEmail  . ' reginos already exist')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    $regionExist = array();
                    if (!empty($regionCheck)) {
                        foreach ($regionCheck as $key => $region) {
                            $regionFind['name'] = $region;
                            $regionFind['company_id'] = $sessionData['id'];
                            $regionExist[] = $this->Region->find('first', array('contain' => false, 'fields' => 'name,id', 'conditions' => $regionFind));
                        }
                    }
                    $regionExist = array_filter($regionExist);
                    if (empty($regionExist)) {
                        $setFlag = 0;
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $regionData = [];
                                foreach ($allData as $key => $value) {
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if (trim($bulkData[$key]) == 'Region') {
                                            $regionData['Region']['name'] = trim($value);
                                        }
                                    }
                                }
                                $regionData['Region']['status'] = 'active';
                                $regionData['Region']['company_id'] = $sessionData['id'];
                                $this->Region->create();
                                if ($this->Region->save($regionData)) {
                                    $region_Ids[] =  $this->Region->id;
                                } else {
                                    $setFlag = 1;
                                }
                            }
                        }
                        if (!empty($region_Ids)) {
                            $this->Session->write('Report.bulk_regionIds',$region_Ids);
                        }
                        if ($setFlag != 1) {
                            $responseArr = array(
                                'status' => 'succsess',
                                'message' => __('This Regions has been saved')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }else {
                        if (!empty($regionExist)) {
                            $foundedRegion = '';
                            foreach ($regionExist as $key => $value) {
                                $foundedRegion .= $value['Region']['name'] . ",";
                            }

                            $foundedRegion = substr_replace($foundedRegion, '', -1);
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedRegion . ' Region already exist')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                    
                }
            }
        }
    }
    function bulk_branches(Type $var = null)
    {
        $this->Session->delete('Report.bulk_branchesIds');
        $this->loadModel('Region');
        $this->loadModel('Country');
        $this->loadModel('State');
        $this->loadModel('City');
        $this->loadModel('CompanyBranch');
        $sessionData = getMySessionData();
        $lastId = $sessionData['id'];
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        $regions = $this->Region->getRegionList($conditions);
        $countries = $this->Country->getCountryList();
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $key_main => $row) {
                        if ($key_main != 0) {
                            $data = explode(",", $row);
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if ($bulkData[$key] == 'Region') {
                                        $regionList[$value] = $value;
                                    } elseif ($bulkData[$key] == 'Country') {
                                        $countryList[$value] = $value;
                                        $alldata[$key_main]['country'] = $value;
                                    } elseif ($bulkData[$key] == 'State') {
                                        $stateList[$value] = $value;
                                        $alldata[$key_main]['state'] = $value;
                                    } elseif ($bulkData[$key] == 'City') {
                                        $cityList[$value] = $value;
                                        $alldata[$key_main]['city'] = $value;
                                    } elseif ($bulkData[$key] == 'Email') {
                                        $emailCheck[] = trim($value);
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Region", "Branch Name", "Contact Name", "Email", "Phone", "Address 1", "Address 2", "Country", "State", "City", "Zip");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $duplicates = array_diff_assoc($emailCheck, array_unique($emailCheck));
                    if (!empty($duplicates)) {
                        $dupEmail = implode(" , ", $duplicates);
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The  ' . $dupEmail  . ' emails already exist')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    if (!empty($regionList)) {
                        foreach ($regionList as $key => $value) {
                            $conditions['name'] = $value;
                            $conditions['company_id'] = $lastId;
                            $regionExist = '';
                            $regionExist = $this->Region->getRegionList($conditions);
                            $regiondata = [];
                            if (empty($regionExist)) {
                                $regiondata['Region']['status'] = 'Active';
                                $regiondata['Region']['company_id'] = $lastId;
                                $regiondata['Region']['name'] = $value;
                                $this->Region->create();
                                if ($this->Region->save($regiondata)) {
                                    $regionId = $this->Region->id;
                                    $regionList[$value] = $regionId;
                                    
                                }
                            } else {
                                $regionList[$value] = key($regionExist);
                            }
                        }
                    } else {
                        $responseArr = array(
                            'status' => 'fail',
                            'message' => __('The data is not valid!!  Please select a valid file!!')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                    $alldata = array_map("unserialize", array_unique(array_map("serialize", $alldata)));
                    $subData = [];
                    if (!empty($alldata)) {
                        foreach ($alldata as $key1 => $value) {
                            $subData = $value;
                            $countryId = '';
                            $stateId = '';
                            foreach ($subData as $key2 => $value_second) {
                                if ($key2 == 'country') {
                                    $country_conditions['name'] = $value_second;
                                    $country_conditions['Country.status'] = 'active';
                                    $countryExist = $this->Country->getCountryList2($country_conditions);
                                    if (empty($countryExist)) {
                                        $countriesData['Country']['name'] = $value_second;
                                        $this->Country->create();
                                        if ($this->Country->save($countriesData)) {
                                            $countryId = $this->Country->id;
                                            $countryList[$value_second] = $countryId;
                                        }
                                    } else {
                                        $countryId = key($countryExist);
                                        $countryList[$value_second] = key($countryExist);
                                    }
                                } elseif ($key2 == 'state') {
                                    $state_conditions['country_id'] = $countryId;
                                    $state_conditions['name'] = $value_second;
                                    $state_conditions['status'] = 'active';
                                    $stateExist = $this->State->getStateList2($state_conditions);
                                    if (empty($stateExist)) {
                                        $stateData['State']['country_id'] = $state_conditions['country_id'];
                                        $stateData['State']['name'] = $value_second;
                                        $this->State->create();
                                        if ($this->State->save($stateData)) {
                                            $stateId = $this->State->id;
                                            $countryId = $this->State->country_id;
                                            $stateList[$value_second] = $stateId;
                                        }
                                    } else {
                                        $stateId = key($stateExist);
                                        $stateList[$value_second] = key($stateExist);
                                    }
                                } elseif ($key2 == 'city') {
                                    $city_conditions['country_id'] = $countryId;
                                    $city_conditions['state_id'] = $stateId;
                                    $city_conditions['name'] = $value_second;
                                    $city_conditions['status'] = 'active';
                                    $cityExist = $this->City->getCityList2($city_conditions);
                                    if (empty($cityExist)) {
                                        $cityData['City']['country_id'] = $city_conditions['country_id'];
                                        $cityData['City']['state_id'] = $city_conditions['state_id'];
                                        $cityData['City']['name'] = $value_second;
                                        $this->City->create();
                                        if ($this->City->save($cityData)) {
                                            $cityId = $this->City->id;
                                            $cityList[$value_second] = $cityId;
                                        }
                                    } else {
                                        $cityId = key($cityExist);
                                        $cityList[$value_second] = key($cityExist);
                                    }
                                }
                            }
                        }
                    }
                    $setFlag = 0;
                    foreach ($file as $keyMain => $value) {
                        if ($keyMain != 0) {
                            $checkData = explode(",", $value);
                            foreach ($checkData as $key => $check) {
                                if (trim($bulkData[$key]) == 'Branch Name') {
                                    $checkbranchData[$keyMain]['branch_name'] = $check;
                                } elseif (trim($bulkData[$key]) == 'City') {
                                    $checkbranchData[$keyMain]['city'] = $cityList[$check];
                                } elseif (trim($bulkData[$key]) == 'Zip') {
                                    $checkbranchData[$keyMain]['zip'] = trim($check);
                                } elseif (trim($bulkData[$key]) == 'Region') {
                                    $checkbranchData[$keyMain]['region'] = trim($check);
                                } elseif (trim($bulkData[$key]) == 'Email') {
                                    $emailCheck[] = trim($check);
                                }
                            }
                        }
                    }
                    $branchExist = array();
                    if (!empty($checkbranchData)) {
                        foreach ($checkbranchData as $key => $branchQuery) {
                            $branchFind['name'] = $branchQuery['branch_name'];
                            $branchFind['regiones'] = $regionList[$branchQuery['region']];
                            $branchFind['city'] = $branchQuery['city'];
                            $branchFind['zipcode'] = $branchQuery['zip'];
                            $branchFind['company_id'] = $lastId;
                            $branchExist[] = $this->CompanyBranch->find('first', array('contain' => false, 'fields' => 'name,id', 'conditions' => $branchFind));
                        }
                    }

                    $emailExist = array();
                    if (!empty($emailCheck)) {
                        foreach ($emailCheck as $key => $email) {
                            $emailFind['email'] = $email;
                            $emailExist[] = $this->CompanyBranch->find('first', array('contain' => false, 'fields' => 'email,id', 'conditions' => $emailFind));
                        }
                    }
                    $branchExist = array_filter($branchExist);
                    $emailExist = array_filter($emailExist);
                    if (empty($branchExist) && empty($emailExist)) {
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $$branchData = [];
                                foreach ($allData as $key => $value) {
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if ($bulkData[$key] == 'Region') {
                                            $branchData['CompanyBranch']['regiones'] = $regionList[$value];
                                        } elseif ($bulkData[$key] == 'Branch Name') {
                                            $branchData['CompanyBranch']['name'] = $value;
                                        } elseif ($bulkData[$key] == 'Contact Name') {
                                            $branchData['CompanyBranch']['contact_name'] = $value;
                                        } elseif ($bulkData[$key] == 'Email') {
                                            $branchData['CompanyBranch']['email'] = $value;
                                        } elseif ($bulkData[$key] == 'Phone') {
                                            $branchData['CompanyBranch']['phone'] = $value;
                                        } elseif ($bulkData[$key] == 'Address 1') {
                                            $branchData['CompanyBranch']['address'] = $value;
                                        } elseif ($bulkData[$key] == 'Address 2') {
                                            $branchData['CompanyBranch']['address2'] = $value;
                                        } elseif ($bulkData[$key] == 'Country') {
                                            $branchData['CompanyBranch']['country'] = $countryList[$value];
                                        } elseif ($bulkData[$key] == 'State') {
                                            $branchData['CompanyBranch']['state'] = $stateList[$value];
                                        } elseif ($bulkData[$key] == 'City') {
                                            $branchData['CompanyBranch']['city'] = $cityList[$value];
                                        } elseif (trim($bulkData[$key]) == 'Zip') {
                                            $branchData['CompanyBranch']['zipcode'] = trim($value);
                                        }
                                    }
                                }
                                $branchData['CompanyBranch']['status'] = 1;
                                $branchData['CompanyBranch']['company_id'] = $conditions['company_id'];
                                $branchData['CompanyBranch']['created_by'] = $sessionData['id'];
                                $branchData['CompanyBranch']['updated_by'] = $sessionData['id'];
                                $this->CompanyBranch->create();
                                if (!empty($branchData)) {
                                    if ($this->CompanyBranch->save($branchData)) {
                                        $branch_Ids[] =  $this->CompanyBranch->id;
                                        if (!empty($branchData['CompanyBranch']['company_id'])) {
                                            $userDetails = ClassRegistry::init('User')->getMailDetails($branchData['CompanyBranch']['company_id']);
                                            $branchDetail = $this->CompanyBranch->find('first', array('contain' => false, 'conditions' => array('CompanyBranch.id' => $this->CompanyBranch->id)));
                                            $arrData = array(
                                                'User' => $userDetails,
                                                'Branch' => $branchDetail['CompanyBranch']
                                            );
                                            $companyAdmins = $this->User->getAllAdminOfCompanyByCompanyId($sessionData['id']);
                                            $this->SendEmail->sendBranchNotifyEmail($arrData, 'add', $companyAdmins);
                                        }
                                    } else {
                                        $setFlag = 1;
                                    }
                                }
                            }
                        }
                        if (!empty($branch_Ids)) {
                            $this->Session->write('Report.bulk_branchesIds',$branch_Ids);
                        }
                    } else {
                        $foundedBranch = '';
                        foreach ($branchExist as $key => $value) {
                            if (!empty($value)) {
                                $foundedBranch .=  $value['CompanyBranch']['name'] . ",";
                            }
                        }
                        $foundedBranch = substr_replace($foundedBranch, '', -1);
                        $foundedEmail = '';
                        foreach ($emailExist as $key => $value) {
                            $foundedEmail .= $value['CompanyBranch']['email'] . ", ";
                        }
                        $foundedEmail = substr_replace($foundedEmail, '', -1);
                        if (!empty($foundedBranch)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedBranch . ' Branches already exists')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                        if (!empty($foundedEmail)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedEmail . ' Emails already exists')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                    if ($setFlag != 1) {
                        $responseArr = array(
                            'status' => 'succsess',
                            'message' => __('This CompanyBranches has been saved')
                        );
                        echo json_encode($responseArr);
                        exit;
                    }
                }
            }
            exit;
        }
    }

    function bulk_station()
    {
        $this->Session->delete('Report.bulk_stationIds');
        $this->loadModel('CompanyBranch');
        $this->loadModel('Region');
        $this->loadModel('Station');
        $this->loadModel('User');
        $sessionData = getMySessionData();
        $lastId = $sessionData['id'];
        $conditions['company_id'] = $this->Session->read('lastcompanyId');
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $flag_check = 0;
            if (!empty($this->request->form)) {
                $bulkData = array();
                // $responseArr = '';
                if (is_file($this->request->form['formData']['tmp_name'])) {
                    $file = file($this->request->form['formData']['tmp_name']);
                    foreach ($file as $key_main => $row) {
                        if ($key_main != 0) {
                            $data = explode(",", $row);
                            // $branchList[]= array();
                            foreach ($data as $key => $value) {
                                if (empty($value)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                } else {
                                    if ($bulkData[$key] == 'Branch Name') {
                                        $branchList[$key_main]['branch_name'] = $value;
                                    } elseif (trim($bulkData[$key]) == 'Zip') {
                                        $branchList[$key_main]['zip'] = trim($value);
                                    } elseif (trim($bulkData[$key]) == 'Station Sr no') {
                                        $serialNo[] = $value;
                                    }
                                }
                            }
                        } else {
                            $bulkData = explode(",", $row);
                            $checkFile = array("Branch Name", "Station Code", "Name", "Station Sr no", "City", "Zip");
                            foreach ($bulkData as $key => $value) {
                                $value = trim($value);
                                if (!in_array($value, $checkFile)) {
                                    $responseArr = array(
                                        'status' => 'fail',
                                        'message' => __('The data is not valid!!  Please select a valid file!!')
                                    );
                                    echo json_encode($responseArr);
                                    exit;
                                }
                            }
                        }
                    }
                    $notfoundBranch = array();
                    foreach ($branchList as $key => $value) {
                        $branchConditions['name'] = $value['branch_name'];
                        $branchConditions['zipcode'] = trim($value['zip']);
                        $branchConditions['company_id'] = $lastId;
                        $branchConditions['branch_status'] = 'active';
                        $branchExist = $this->CompanyBranch->getMyBranchList2($branchConditions);
                        if (!empty($branchExist)) {
                            $branchList2[$value['zip']] = key($branchExist);
                        } else {
                            $notfoundBranch[] = $value;
                        }
                    }
                    $foundedSrno = array();
                    foreach ($serialNo as $key => $value) {
                        $serialnoCondition['serial_no'] = $value;
                        $serialnoExist = $this->Station->find('first', array('contain' => false, 'fields' => 'name,id,serial_no', 'conditions' => $serialnoCondition));
                        if (!empty($serialnoExist)) {
                            $foundedSrno[] = $serialnoExist;
                        }
                    }
                    $setFlag = 0;
                    if (empty($notfoundBranch) && empty($foundedSrno)) {
                        foreach ($file as $key_main => $all_roe) {
                            if ($key_main != 0) {
                                $allData = explode(",", $all_roe);
                                $stationData = [];
                                foreach ($allData as $key => $value) {
                                    $zip = '';
                                    if (empty($value)) {
                                        $flag_check = 1;
                                    } else {
                                        if (trim($bulkData[$key]) == 'Zip') {
                                            $stationData['Station']['branch_id'] = $branchList2[trim($value)];
                                        } elseif (trim($bulkData[$key]) == 'Station Code') {
                                            $stationData['Station']['station_code'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Name') {
                                            $stationData['Station']['name'] = $value;
                                        } elseif (trim($bulkData[$key]) == 'Station Sr no') {
                                            $stationData['Station']['serial_no'] = trim($value);
                                        }
                                    }
                                }
                                $stationData['Station']['status'] = 'Active';
                                $stationData['Station']['company_id'] = $lastId;
                                $stationData['Station']['created_by'] = $sessionData['id'];
                                $stationData['Station']['updated_by'] = $sessionData['id'];
                                $this->Station->create();
                                if ($this->Station->save($stationData)) {
                                    $bulk_stationIds[] = $this->Station->id;
                                } else {
                                    $setFlag = 1;
                                }
                            }
                        }
                        if (!empty($bulk_stationIds)) {
                            $this->Session->write('Report.bulk_stationIds',$bulk_stationIds);
                        }
                        if ($setFlag != 1) {
                            $responseArr = array(
                                'status' => 'succsess',
                                'message' => __('This Stations has been saved')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    } else {
                        $foundedBranch = '';
                        foreach ($notfoundBranch as $key => $value) {
                            if (!empty($value)) {
                                $foundedBranch .=  $value['branch_name'] . ",";
                            }
                        }
                        $foundedBranch = substr_replace($foundedBranch, '', -1);
                        $foundedserialNo = '';
                        foreach ($foundedSrno as $key => $value) {
                            if (!empty($value)) {
                                $foundedserialNo .=  $value['Station']['serial_no'] . ",";
                            }
                        }
                        $foundedserialNo = substr_replace($foundedserialNo, '', -1);
                        if (!empty($foundedBranch)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedBranch . ' Branches not found')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                        if (!empty($foundedserialNo)) {
                            $responseArr = array(
                                'status' => 'fail',
                                'message' => __('This ' . $foundedserialNo . ' Serial No already exist')
                            );
                            echo json_encode($responseArr);
                            exit;
                        }
                    }
                }
            }
        }
        // $responseArr = 'The station has been saved.';
        // return json_encode($responseArr);
    }
    function completed_bulk(Type $var = null)
    {
        if($this->Session->check('Report.bulk_usersIds')){
            $users_count = count($this->Session->read('Report.bulk_usersIds'));
        }
        if($this->Session->check('Report.bulk_regionIds')){
            $region_count = count($this->Session->read('Report.bulk_regionIds'));
        }
        if($this->Session->check('Report.bulk_branchesIds')){
            $branches_count = count($this->Session->read('Report.bulk_branchesIds'));
        }
        if($this->Session->check('Report.bulk_stationIds')){
            $station_count = count($this->Session->read('Report.bulk_stationIds'));
        }
        $this->set(compact('users_count', 'region_count', 'branches_count','station_count'));
    }

    public function transaction_detail($companyId = null,$regionId = null,$type = null,$period = null,$branchId = null,$stationId = null,$userName = null)
    {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.TransactionCondition_2');
        // }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
        }

        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        if (empty($this->request->data['Analytic']['company_id']) && $this->Session->check('Report.CompanyId')) {
            $this->request->data['Analytic']['company_id'] = $this->Session->read('Report.CompanyId');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }

        $all_condition = $this->Session->read('Report.TransactionCondition_2');
        if (($this->request->is('ajax')) or (isset($this->request->params['named']['sort'])) or (isset($this->request->params['named']['Paginate']))  or (isset($this->request->params['named']['page'])) or $f == 1) {

            if (isset($all_condition['FileProccessingDetail.company_id'])) {
                $companyId = $all_condition['FileProccessingDetail.company_id'];
            }
            if (isset($all_condition['FileProccessingDetail.regiones'])) {
                $this->request->data['Analytic']['regiones'] = $all_condition['FileProccessingDetail.regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($all_condition['FileProccessingDetail.regiones']);
            }
            if (isset($all_condition['FileProccessingDetail.branch_id']) && empty($this->request->data['Analytic']['branch_id'])) {
                $this->request->data['Analytic']['branch_id'] = $all_condition['FileProccessingDetail.branch_id'];
                $conditions['FileProccessingDetail.branch_id'] = $all_condition['FileProccessingDetail.branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($conditions['FileProccessingDetail.branch_id']);
            }
            if (isset($all_condition['FileProccessingDetail.station'])) {
                $this->request->data['Analytic']['station'] = $all_condition['FileProccessingDetail.station'];
                $conditions['FileProccessingDetail.station'] = $all_condition['FileProccessingDetail.station'];
                $conditions['BillsActivityReport.station'] = $all_condition['FileProccessingDetail.station'];
            }

            if (isset($all_condition['FileProccessingDetail.date'])) {
                $conditions_new['TransactionDetail.trans_datetime >= '] = $all_condition['FileProccessingDetail.date'] . ' 00:00:00';
                $conditions_new['TransactionDetail.trans_datetime <= '] = $all_condition['FileProccessingDetail.date'] . ' 23:59:59';
                $conditions_new['FileProccessingDetail.file_date = '] = $all_condition['FileProccessingDetail.date'];
                $this->request->data['Analytic']['date'] = $all_condition['FileProccessingDetail.date'];
            }
        }
        if (!isset($this->request->data['Analytic']['date']) or empty($this->request->data['Analytic']['date'])) {
            $this->request->data['Analytic']['date'] = date('Y-m-d', strtotime("-1 days"));
        } else {
            $conditions3 = array();
            $tdate = $this->request->data['Analytic']['date'];
            $tdate = str_replace('-', '/', $tdate);
            $tdate = date('Y-m-d', strtotime($tdate));
            $conditions3['TransactionDetail.trans_datetime >= '] = $tdate . ' 00:00:00';
            $conditions3['TransactionDetail.trans_datetime <= '] = $tdate . ' 23:59:59';
        }


        $sessData = getMySessionData();
        $companies = ClassRegistry::init('User')->getMyCompanyList($sessData['id'], $sessData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessData['id'], $sessData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessData['id']] = $sessData['first_name'];
        }
        if (isSuparAdmin() && empty($companyId)) {
            $companyId = array_keys($companies);
        }
        $this->set(compact('companies'));

        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (!isCompany()) {
            $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports($companyId);
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        // $conditions = $this->__getConditions('Transaction', $this->request->data['Filter'], 'TransactionDetail');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');

        $from = $conditions['from'];
        $xAxisDates1 = $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        if (!empty($conditions3)) {
            $conditions += $conditions3;
        }
        $this->loadModel('Region');
        $this->loadModel('Analytic');
        $this->loadModel('stations');
        $conditions2['company_id'] = $sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
        }
        $this->set(compact('branches'));
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        $this->set(compact('stations'));
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
        }

        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

            $conditions['FileProccessingDetail.branch_id'] = $branches;
        }
        if ($sessionData['role'] == 'Company' and $sessionData['user_type'] == 'Branch') {
            $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsName($sessionData['id']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsList($branchidListd);
            $this->set(compact('branches'));
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if ($type == 'hour') {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'TIME(TransactionDetail.trans_datetime) >= ' => $period.':00:00',
                'TIME(TransactionDetail.trans_datetime) <= ' => $period.':59:59',
                'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
                'regions.id' => $regionId
            ); 

            if (!empty($branchId)){
                $arrCondition += array(
                    'FileProccessingDetail.branch_id' => $branchId
                );
            }
            if (!empty($stationId)){
                $arrCondition += array(
                    'FileProccessingDetail.station' => $stationId
                );
            }
            if (!empty($userName)){
                $arrCondition += array(
                    'TransactionDetail.teller_name' => $userName
                );
                unset($arrCondition['FileProccessingDetail.station']);
            }
        }elseif ($type == 'monthly') {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'MONTHNAME(TransactionDetail.trans_datetime)' => $period,
                'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
                'regions.id' => $regionId
            );
            if (!empty($branchId)){
                $arrCondition += array(
                    'FileProccessingDetail.branch_id' => $branchId
                );
            }
            if (!empty($stationId)){
                $arrCondition += array(
                    'FileProccessingDetail.station' => $stationId
                );
            }
            if (!empty($userName)){
                $arrCondition += array(
                    'TransactionDetail.teller_name' => $userName
                );
            }
        }elseif ($type == 'yearly') {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'YEAR(TransactionDetail.trans_datetime)' => $period,
                'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
                'regions.id' => $regionId
            );
            if (!empty($branchId)){
                $arrCondition += array(
                    'FileProccessingDetail.branch_id' => $branchId
                );
            }
            if (!empty($stationId)){
                $arrCondition += array(
                    'FileProccessingDetail.station' => $stationId
                );
            }
            if (!empty($userName)){
                $arrCondition += array(
                    'TransactionDetail.teller_name' => $userName
                );
            }
        }elseif ($type == 'daily') {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'DAYNAME(TransactionDetail.trans_datetime)' => $period,
                'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
                'regions.id' => $regionId
            );
            if (!empty($branchId)){
                $arrCondition += array(
                    'FileProccessingDetail.branch_id' => $branchId
                );
            }
            if (!empty($stationId)){
                $arrCondition += array(
                    'FileProccessingDetail.station' => $stationId
                );
            }
            if (!empty($userName)){
                $arrCondition += array(
                    'TransactionDetail.teller_name' => $userName
                );
            }
        }elseif ($type == 'weekly') {

            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
                'regions.id' => $regionId
            );

            if ($period == 'Week-1') {
                    $arrCondition += array(
                        ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )>= 1'),
                        ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )<8'),
                );
            } elseif ($period == 'Week-2') {
                $arrCondition += array(
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )>= 8'),
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )<15'),
            );
            }elseif ($period == 'Week-3') {
                $arrCondition += array(
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )>= 15'),
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )<22'),
            );
            }elseif ($period == 'Week-4') {
                $arrCondition += array(
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )>= 22'),
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )<29'),
            );
            }elseif ($period == 'Week-5') {
                $arrCondition += array(
                    ('DATE_FORMAT(TransactionDetail.trans_datetime,"%d" )>= 29'),
            );
            }
            
            if (!empty($branchId)){
                $arrCondition += array(
                    'FileProccessingDetail.branch_id' => $branchId
                );
            }
            if (!empty($stationId)){
                $arrCondition += array(
                    'FileProccessingDetail.station' => $stationId
                );
            }
            if (!empty($userName)){
                $arrCondition += array(
                    'TransactionDetail.teller_name' => $userName
                );
                unset($arrCondition['FileProccessingDetail.station']);
            }
        }

        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('TransactionDetail');
        $transactionDetailArray = $this->Analytic->getTransactionDetails2($arrCondition);
        $this->Session->write('Report.TransactionCondition_2', $arrCondition);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $transactionsDetails = $this->paginate('TransactionDetail');
        $this->set(compact('transactionsDetails', 'temp_station', 'temp_companydata'));
    }

    public function readCSVdata(Type $var = null)
    {
        $this->loadModel('ErrorType');
        $file = file($this->request->form['formData']['tmp_name']);
        // echo '<pre><b>' . __FILE__ . ' (Line:'. __LINE__ .')</b><br>';
        // print_r($file);echo '<br>';exit;
        $bulkData = array();
        foreach ($file as $key_main => $row) {
            $data = explode(",", $row);
            if ($key_main != 0) {
                $error_categoty = '';
                foreach ($data as $key => $value) {
                   if ($bulkData[$key] == 'ERROR CATEGORY') {
                       $errorTypes['ErrorType']['error_code'] = trim($value);
                   }elseif (trim($bulkData[$key]) == 'ERROR') {
                    $error_categoty = trim($value);
                   }elseif (trim($bulkData[$key]) == 'POSITION') {
                    $errorTypes['ErrorType']['position'] = trim($value);
                   }elseif (trim($bulkData[$key]) == 'LEVEL') {
                    $errorTypes['ErrorType']['error_level'] = trim($value);
                   }elseif (trim($bulkData[$key]) == 'ERROR DESCRIPTION') {
                    $errorTypes['ErrorType']['error_text'] = trim($value);
                   }elseif (trim($bulkData[$key]) == 'ERROR CATEGORY DESCRIPTION') {
                    $errorTypes['ErrorType']['error_meaning'] = trim($value);
                   }elseif (trim($bulkData[$key]) == 'HOW_TO') {
                    $errorTypes['ErrorType']['how_to'] = trim($value);
                   }
                }
                $errorTypes['ErrorType']['error_code'] = $errorTypes['ErrorType']['error_code'].'('. $error_categoty.')';
                $this->ErrorType->create();
                $this->ErrorType->save($errorTypes);
            }else {
                $bulkData = explode(",", $row);
                // $checkFile = array("Branch Name", "Station Code", "Name", "Station Sr no", "City", "Zip");
                foreach ($bulkData as $key => $value) {
                    $value = trim($value);
                }
            }
        }
    }
    public function getTransactionTotal($conditions)
    {

         $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
            $this->loadModel('TransactionDetail');
            $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
            $transactionDetailArray2['chart']['chartDataSum']['group'] = 'FileProccessingDetail.file_date';
            $tranctionCount1 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartDataSum']);
            $total_Count = array();
            foreach ($tranctionCount1 as $key => $value) {
                $total_Count[] = $value['TransactionDetail']['transaction_count'];
            }
            $total_Count = array_sum($total_Count);
            return $total_Count;
    }
    public function allTickets($var = null)
    {

        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $startDate = !empty($this->request->data['Filter']['start_date']) ? $this->request->data['Filter']['start_date'] . ' 00:00:00' : '' ;
        $endDate = !empty($this->request->data['Filter']['end_date'])  ? $this->request->data['Filter']['end_date'] . ' 23:59:59' : '';
        $sessionData = getMySessionData();
        $this->User = ClassRegistry::init('User');
        $companyList = array();
        if (!isSuparAdmin()) {
            if (isSupportDealer()) {
                $companyList = $this->User->getMyCompanyList($sessionData['parent_id'], DEALER, 'id, id');
            } else {
                $companyList = $this->User->getMyCompanyList($sessionData['id'], $sessionData['role'], 'id, id');
            }
            $otherConditions = array(
                'User.user_type' => SUPPORT
            );
            if ($sessionData['user_type'] == ADMIN) {
                $otherConditions['User.parent_id'] = $sessionData['id'];
            }
            $responseArr['dealers'] = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], 'id, first_name', $otherConditions);
        } else {
            $responseArr['dealers'] = $this->User->getAllSupportDealers();
        }
        $contains = array(
            'Company' => array('id', 'first_name', 'last_name', 'email', 'name'),
            'Dealer' => array('id', 'first_name', 'last_name', 'email', 'name'),
            'Branch' => array('id', 'name'),
        );
        $conditions = array(
            'Ticket.company_id' => $companyList,
            'Ticket.status' => 'New',
            'NOT' => array('Ticket.status' => 'Deleted')
        );
        if (!empty($startDate)) {
            $conditions['ticket_date >= '] = $startDate;
        }
        if (!empty($endDate)) {
            $conditions['ticket_date <= '] = $endDate;
        }
        if (isSuparAdmin()) {
            unset($conditions['Ticket.company_id']);
        }
        $this->loadModel('Ticket');
        $limit = 25;
        if($var == "new"){
            $page = $this->pageForPagination('Ticket');
            $this->AutoPaginate->setPaginate(array(
                'order' => ' Ticket.id DESC',
                'conditions' => $conditions,
                'contain' => $contains,
                'limit' => $limit,
                'page' => $page
            ));
            $tickets = $this->paginate('Ticket');
        }elseif ($var == "open") {
            $conditions['Ticket.status'] = 'Open';
            $page = $this->pageForPagination('Ticket');
            $this->AutoPaginate->setPaginate(array(
                'order' => ' Ticket.id DESC',
                'conditions' => $conditions,
                'contain' => $contains,
                'limit' => $limit,
                'page' => $page
            ));
            $tickets = $this->paginate('Ticket');
        }elseif ($var == "closed") {
            $conditions['Ticket.status'] = 'Closed';
            $page = $this->pageForPagination('Ticket');
            $this->AutoPaginate->setPaginate(array(
                'order' => ' Ticket.id DESC',
                'conditions' => $conditions,
                'contain' => $contains,
                'limit' => $limit,
                'page' => $page
            ));
            $tickets = $this->paginate('Ticket');
        }
        $dealers = $responseArr['dealers'];
        $this->set(compact('tickets','dealers'));
    }
    public function all_users($all = null)
    {
        $sessionData = getMySessionData();
        $this->__checkPrevents('index');
        $resConditions = $this->__getUserConditions($this->request->params['named']);
        $parentId = $resConditions['parentId'];
        $parentDealer = $resConditions['parentDealer'];
        // $conditions = $resConditions['conditions'];
        $conditions = ['status != ' => 'deleted'];
        // echo '<pre><b></b><br>';
        // print_r($this->type);echo '<br>';exit;
        if ($all == "all") {
            $this->Session->write('UserAllSearchForm', '');
        }
        if (empty($this->request->data['User']) && $this->Session->read('UserAllSearchForm')) {
            $this->request->data['User'] = $this->Session->read('UserAllSearchForm');
        }
        if (!empty($this->request->data['User'])) {
            $this->request->data['User'] = array_filter($this->request->data['User']);
            $this->request->data['User'] = array_map('trim', $this->request->data['User']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['User']['name'])) {
                    $conditions['OR'] = array(
                        'User.first_name LIKE ' => '%' . $this->request->data['User']['name'] . '%',
                        'User.last_name LIKE ' => '%' . $this->request->data['User']['name'] . '%'
                    );
                }
                if (isset($this->request->data['User']['email'])) {
                    $conditions['User.email LIKE '] = '%' . $this->request->data['User']['email'] . '%';
                }
                if (isset($this->request->data['User']['user_type'])) {
                    $conditions['User.user_type'] = $this->request->data['User']['user_type'];
                }
                if (isset($this->request->data['User']['dealer_id'])) {
                    if (empty($parentId) && empty($all) && empty($this->request->params['named']) && empty($dealerId)) {
                        unset($this->request->data['User']['dealer_id']);
                    } else {
                        $conditions['User.dealer_id'] = $this->request->data['User']['dealer_id'];
                    }
                }
                if (isset($this->request->data['User']['status'])) {
                    $conditions['User.status'] = $this->request->data['User']['status'];
                }
            }
            $this->Session->write($this->type . 'Search', $this->request->data['User']);
        }

        $fields = array(
            'User.id, User.dealer_id, User.sub_dealer_count, User.sub_dealer_count, User.sub_company_count,' .
                'User.dealer_company_count, User.company_branch_count, User.station_count, User.first_name, User.last_name,' .
                'User.email, User.file_send_email, User.phone_no, User.photo, User.address, User.address2, User.country_id,' .
                'User.state_id, User.city_id, User.pincode, User.last_login_time, User.role, User.user_type, User.status,' .
                'User.is_display_billing, User.parent_id, User.subscription_id, User.created_by, User.updated_by, User.created, User.updated'
        );
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';
        $this->AutoPaginate->setPaginate(array(
            'order' => ' User.id DESC',
            'conditions' => $conditions,
            'contain' => false,
        ));
        $this->set('users', $this->paginate('User'));
        // $no_of_support_user = $this->paginate('User');
    }
    public function setHeaderFilter($all = null)
    {
        $data = $this->request->data['Analytic'];
        if(!empty($all)){
            $this->Session->delete('DashboardFilter');
            $this->Session->delete('setHeaderFilter');
        }
        $this->Session->write('setHeaderFilter', $data);
        return $this->redirect(Router::url( $this->referer(), true ));
    }
    
    public function check_file()
    {
        $this->loadModel('FileProccessingDetail');
        $date =  date('Y-m-d', strtotime('-1 days'));
        $sessionData = getMySessionData();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
        $from = $repConditions['from'];
        $xAxisDates1 = $xAxisDates = $repConditions['xAxisDates'];
        $tickInterval = $repConditions['tickInterval'];
        $startDate = $repConditions['start_date'];
        $endDate = $repConditions['end_date'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (!empty($all)) {
            $this->Session->delete('Report.TellerSetupFilter');
            $this->Session->delete('Report.DashboardCondition');
        }
        $startDate = $this->request->data['Filter']['start_date'];
        $endDate = $this->request->data['Filter']['end_date'];
		$isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions['FileProccessingDetail.file_date >= '] = $startDate;
        $conditions['FileProccessingDetail.file_date <= '] = $endDate;
        $conditions['FileProccessingDetail.company_id'] = $company_id;

        $chartDatas = $this->FileProccessingDetail->find('all', [ 'conditions' =>$conditions, 'order' => 'FileProccessingDetail.id DESC', 'contain' => array('TransactionDetail')]);
        $this->loadModel('TransactionDetail');
        if(!empty($chartDatas)){
            foreach ($chartDatas as $key => $chartData) {
                if(!empty($chartData['TransactionDetail'])){
                    foreach ($chartData['TransactionDetail'] as $key1 => $value) {
                        $total_amount[$key1] = $value['total_amount'];
                        $trans_type_id[$key1] = $value['trans_type_id'];
                        $inventory_snapshot_value[$key1]  = $value['inventory_snapshot_value'];
                        if($key1 > 0){
                            $prev_key = $key1 - 1;
                            $prev_inventory_snapshot_value = $inventory_snapshot_value[$prev_key];
                            $prev_total_amount = $total_amount[$prev_key];
                            $prev_trans_type_id = $trans_type_id[$prev_key];
                            $current_inventory_snapshot_value = $value['inventory_snapshot_value'];
                            if(in_array($prev_trans_type_id, [2,4,19])){
                                $prev_value = $prev_inventory_snapshot_value + $prev_total_amount;
                                if($current_inventory_snapshot_value == $prev_value){
                                    $this->TransactionDetail->id=$value['id'];                
                                    $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                    $this->TransactionDetail->save();
                                }else{
                                    $this->TransactionDetail->id=$value['id'];                
                                    $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                                    $this->TransactionDetail->save();
                                }
                            }else if(in_array($prev_trans_type_id, [1,11,5,13,14])){
                                $prev_value = $prev_inventory_snapshot_value - $prev_total_amount;
                                if($current_inventory_snapshot_value == $prev_value){
                                    $this->TransactionDetail->id=$value['id'];                
                                    $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                    $this->TransactionDetail->save();
                                }else{
                                    $this->TransactionDetail->id=$value['id'];                
                                    $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                                    $this->TransactionDetail->save();
                                }
                            }
                        }
                    }
                }
            }
            echo "Cron update succufully";exit;
        }
    }
    public function check_previous_file_data()
    {
        $sessionData = getMySessionData();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $repConditions = ClassRegistry::init('Analytic')->getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
        $from = $repConditions['from'];
        $xAxisDates1 = $xAxisDates = $repConditions['xAxisDates'];
        $tickInterval = $repConditions['tickInterval'];
        $startDate = $repConditions['start_date'];
        $endDate = $repConditions['end_date'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (!empty($all)) {
            $this->Session->delete('Report.TellerSetupFilter');
            $this->Session->delete('Report.DashboardCondition');
        }
        $startDate = $this->request->data['Filter']['start_date'];
        $endDate = $this->request->data['Filter']['end_date'];
		$isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $conditions['FileProccessingDetail.file_date >= '] = $startDate;
        $conditions['FileProccessingDetail.file_date <= '] = $endDate;
        $conditions['FileProccessingDetail.company_id'] = $company_id;
        $stationCondition['Station.company_id'] = $company_id;
        $this->loadModel('Station');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('TransactionDetail');
        $getAllStations = $this->Station->find('all', array('conditions' => $stationCondition, 'contain' =>false));
        foreach ($getAllStations as $key => $station) {
            $conditions['FileProccessingDetail.station'] = $station['Station']['id'];
            $conditions['FileProccessingDetail.branch_id'] = $station['Station']['branch_id'];
            $allFiles = $this->FileProccessingDetail->find('all', [ 'conditions' => $conditions,  'contain' => ['TransactionDetail']]);
            foreach ($allFiles as $key1 => $allFile) {
                if($key1 > 0){
                    $prev_key = $key1 - 1;
                    $last_transaction_key = count($allFiles[$prev_key]['TransactionDetail']) - 1;
                    $prev_inventory_snapshot_value = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['inventory_snapshot_value'];
                    $prev_total_amount = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['total_amount'];
                    $prev_trans_type_id = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['trans_type_id'];
                    $current_inventory_snapshot_value = $allFile['TransactionDetail'][0]['inventory_snapshot_value'];
                    if(in_array($prev_trans_type_id, [2,4,19])){
                        $prev_value = $prev_inventory_snapshot_value + $prev_total_amount;
                        if(round($current_inventory_snapshot_value) == intval($prev_value)){
                            $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];            
                            $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                            $this->TransactionDetail->save();
                        }else{
                            $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];            
                            $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                            $this->TransactionDetail->save();
                        }
                    }else if(in_array($prev_trans_type_id, [1,11,5,13,14, 20])){
                        $prev_value = $prev_inventory_snapshot_value - $prev_total_amount;
                        if(round($current_inventory_snapshot_value) == intval($prev_value)){
                            $this->TransactionDetail->id= $allFile['TransactionDetail'][0]['id'];           
                            $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                            $this->TransactionDetail->save();
                        }else{
                            $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];               
                            $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                            $this->TransactionDetail->save();
                        }
                    }
                }
            }
        }
        echo "Update scron succufully";exit;

    }

    public function get_read_file()
    {
        $condition['process'] = 0;
        $conditions['created_date >= '] = '2023-03-06 00:00:00';
        $conditions['created_date <= '] = '2023-03-06 23:59:59';
        $getFiles =  ClassRegistry::init("read_files")->find('all', ['conditions' => $conditions]);
        echo '<pre><b></b><br>';
        print_r($getFiles);echo '<br>';
        if(!empty($getFiles)){
            foreach ($getFiles as $key => $value) {
                echo '<pre><b></b><br>';
                print_r($value);echo '<br>';exit;
            }
        }
    }

    public function not_exits()
    {
        // $start_date =  date('Y-m-d', strtotime('-2 days'));
        // $end_date = date('Y-m-d', strtotime('-1 days'));
        $conditions['created_date >= '] = '2023-03-23 00:00:00';
        $conditions['created_date <= '] = '2023-03-23 23:59:59';
        $conditions['status'] = 'serial_not_exits';
        $conditions['notify_exist'] = 0;
        $file_name = [];
        $getFiles =  ClassRegistry::init("read_files")->find('all', ['conditions' => $conditions]);
        if(!empty($getFiles)){
            // foreach ($getFiles as $key => $value) {
            //     $data = array('read_files' => array('id' => $value['read_files']['id'], 'notify_exist' => 1));
            //     ClassRegistry::init("read_files")->save($data);exit;
            // }
            $this->SendEmail->sendSerialNoNotExist($getFiles);
            exit;
        }
    }
}
