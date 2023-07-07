<?php
App::uses('Controller', 'Controller');

class AppController extends Controller
{
    public $components = array('Auth','Acl','RequestHandler', 'Cookie', 'SendEmail', 'Common', 'Session', 'Paginator', 'Siteconfig', 'Message', 'AutoPaginate' => array('options' => array(10, 20, 50, 100, 250), 'defaultLimit' => 20));
    public $helpers = array('Html' => array('className' => 'Custom'), 'Form' => array('className' => 'CustomForm'),'Csv');

    function beforeFilter()
    {
        parent::beforeFilter();
       
        $this->Auth->allow('*');
        $sessionData = getMySessionData();
        $change_password = $this->request->params['action'];
        $controller = $this->request->params['controller'];
        $user = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'is_setup', 'conditions' => array('User.id' => $sessionData['id'])));
        if($change_password != 'change_password' && $change_password != "getRecent_report"  && $change_password != 'logout'){
            if(!empty($user) && ($user['User']['is_setup'] == 0) ){
                $redirect = array('controller' => 'users', 'action' => 'change_password');
                $this->redirect($redirect);
            }
        }
       
        $this->__setAllObj();
    }

    function __setAllObj()
    {
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        $regiones = $this->Region->getRegionList($conditions2);
        $regiones_data = ClassRegistry::init('Region')->find('list', array(
            'fields' => 'id, id',
            'contain' => false,
            'conditions' => $conditions2
        ));

        $conditions_regiones = array(
            'CompanyBranch.regiones' => $regiones_data,
            'CompanyBranch.branch_status' => 'active',
        );
        $branches = ClassRegistry::init('CompanyBranch')->find('list', array(
            'fields' => 'id, name',
            'contain' => false,
            'conditions' => $conditions_regiones
        ));
        
        $this->set(compact('regiones','branches'));
        if (isSuparCompany() || isCompanyAdmin()) {
            $companyId = 0;
            if (isSuparCompany()) {
                $companyId = $sessionData['id'];
            } elseif (isCompanyAdmin() || isCompanyCompanyBranchAdmin() || isCompanyRegionalAdmin()) {
                $companyId = $sessionData['parent_id'];
            }
            $myBranchLists = ClassRegistry::init('CompanyBranch')->getMyBranchLists($companyId);
            $myBranchLists['all'] = __('All Branch');
            $this->set('myBranchLists', $myBranchLists);
        }
    }

    function _checkLogin()
    {
        

        $this->_checkUserSession();
    }

    function _checkUserSession()
    {
        $this->Auth->authenticate = array(
            'Form' => array(
                'fields' => array('username' => 'email')
        ));
        $this->Auth->loginAction = array(
            'controller' => 'users',
            'action' => 'login'
        );
            $this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'dashboard');
        // }
        $this->Auth->logoutRedirect = '/';
        $this->Auth->authError = __('Please login to view that page.');
    }

    function __isSuparAdmin()
    {
        $sessionData = getMySessionData();
//        return ($this->Session->read('Auth.User.role') == 'Admin' && $this->Session->read('Auth.User.user_type') == SUPAR_ADM);
        return ($sessionData['role'] == 'Admin' && $sessionData['user_type'] == SUPAR_ADM);
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
        if ($timeRange == 'last_7days') {
            $retArray['xAxisDates'] = date_range(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'), '+1 day');
        } elseif ($timeRange == 'last_15days') {
            $retArray['tickInterval'] = 2;
            $startDate = date('Y-m-d', strtotime('-14 days'));
            $endDate = date('Y-m-d');
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
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
        } elseif ($timeRange == 'last_6months') {
            $retArray['tickInterval'] = 26;
            $startDate = date('Y-m-01', strtotime('-6 month'));
            $startDate = date('Y-m-d', strtotime('-6 month'));
            $endDate = date('Y-m-t', strtotime('-1 month'));
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }  elseif($timeRange == 'last_12months'){
            $retArray['tickInterval'] = 52;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }else {
            $retArray['xAxisDates'] = [date('Y-m-d')];
        }
        return $retArray;
    }

    function __getConditions($sessionName = 'BillsActivityReport', $reqData = array(), $model = '')
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

    function isAuthorized($user) {
        // return false;
        return $this->Auth->loggedIn();
    }
}
