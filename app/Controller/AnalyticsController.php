<?php
//require 'aws/aws-autoloader.php';
App::uses('AppController', 'Controller');
require ROOT . DS . 'vendor' . DS . 'autoload.php';

use Aws\S3\S3Client;
// use DateTime;
class AnalyticsController extends AppController
{
    public $components = array('Auth');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
        $controller_actionArr = array(
            'Bill Inventory Report' => 'analytics/bill_activity',
            'Log File Processing' => 'analytics/index',
            'Inventory Report' => 'analytics/inventory_management',
            'Bill Adjustment Report' => 'analytics/bill_adjustment',
            'Audit trail Report' => 'analytics/bill_count',
            'Teller Activity Report' => 'analytics/teller_activity',
            'Transaction Details' => 'analytics/transaction_details',
            'Transactions By Hours' => 'analytics/hour_by_transaction',
            'Active Teller Sign On' => 'analytics/side_log',
            'Error/Warning' => 'analytics/error_warning',
            'Denomination Heat Map' => 'analytics/heat_map',
            'Transaction Map' => 'analytics/transaction_map',
            'Note Count' => 'analytics/note_count',
            'Special notes reconciliation' => 'analytics/special_notes_reconciliation',
            'User Report' => 'analytics/userReport'
        );
        $currentAction = $this->request->params['action'];
        $currentController = $this->request->params['controller'];
        $baseUrl = Router::url('/', true);
        $fullUrl = $baseUrl . $currentController . '/' . $currentAction;
        $actionUrl = $currentController . '/' . $currentAction;

        if (in_Array($actionUrl, $controller_actionArr)) {
            $reportName = array_search($actionUrl, $controller_actionArr);
            $this->loadModel('RecentReports');
            $sessionData = getMySessionData();
            $counter_tag = 1;
            $recentReports['user_id'] = $sessionData['id'];
            $recentReports['controller_name'] = $currentController;
            $recentReports['action_name'] = $currentAction;
            $recentReports['report_name'] = $reportName;
            $recentReports['counter_tag'] = $counter_tag;
            $recentReports['full_url'] = $fullUrl;
            $date = new DateTime("now", new DateTimeZone('America/New_York'));
            $date = $date->format('Y-m-d H:i:s');
            if (!empty($recentReports)) {
                $recentConditions['RecentReports.action_name'] = $currentAction;
                $isExist = $this->RecentReports->find('first', array(
                    'conditions' => $recentConditions,
                    'contain' => false
                ));
                if (empty($isExist)) {
                    $this->RecentReports->save($recentReports);
                } else {
                    $this->RecentReports->id = $isExist['RecentReports']['id'];
                    $counters = $isExist['RecentReports']['counter_tag'] + 1;
                    $this->RecentReports->set(array('updated' => $date, 'counter_tag' => $counters));
                    $this->RecentReports->save();
                }
            } else {
                $this->Message->setWarning(__('Something went wrong'));
                $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
            }
        }

        //        ClassRegistry::init('Invoice')->billCron();
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
        } elseif ($timeRange == 'last_10days') {
            $retArray['tickInterval'] = 2;
            $startDate = date('Y-m-d', strtotime('-10 days'));
            $endDate = date('Y-m-d', strtotime('-1 days'));
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
        } elseif ($timeRange == 'last_months') {
            $retArray['tickInterval'] = 4;
            $startDate = date('Y-m-d', strtotime('-1 month'));
            $endDate = date('Y-m-d');
            $startDate = date("Y-n-j", strtotime("first day of previous month"));
            $endDate =  date("Y-n-j", strtotime("last day of previous month"));
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
        } elseif($timeRange == 'last_12months'){
            $retArray['tickInterval'] = 52;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }elseif($timeRange == "last_18days"){
            $retArray['tickInterval'] = 5;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }elseif ($timeRange == "today") {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }
        else {
            $retArray['xAxisDates'] = [date('Y-m-d')];
        }
        return $retArray;
    }

    function __getConditions($sessionName = null, $reqData = array(), $model = '')
    {
        $from = !empty($reqData['from']) ? $reqData['from'] : '';
        if (empty($reqData['from']) && $this->Session->check('Report.' . $sessionName)) {
            $from = $this->Session->read('Report.' . $sessionName . '.from');
        }
        // echo '<pre><b></b><br>';
        // print_r($from);echo '<br>';exit;
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

    /**
     * admin side file processing view
     * @param type $all
     */
    function index($all = null)
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
        if (!empty($this->request->params['named']['generate'])) {
            $this->set('generate', 'true');
        }
        if (!empty($all)) {
            $this->Session->delete('Report.CompanyId');
            $this->Session->delete('Report.FileProcessing');
        }
        if (empty($this->request->data['Analytic']['company_id']) && $this->Session->check('Report.CompanyId')) {
            $this->request->data['Analytic']['company_id'] = $this->Session->read('Report.CompanyId');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Dashboard.Filter', $this->request->data['Filter']);
        }
   

        $compCond = array(
            'User.status' => 'active',
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM
        );
        if (!isSuparAdmin() && isAdmin()) {
            $compCond['User.created_by'] = $sessionData['id'];
        }
        if (isAdmin()) {
            $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $compCond));
            $this->set(compact('companies'));
        }
        $getCompanyId = getCompanyId();
        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'FileProccessingDetail');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate,
        );
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
        if (!empty($companyId)) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;
        }
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones', 'stations', 'branches'));
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        /**
         * add extra conditions except the common conditions
         */
        $indexArr = $this->Analytic->getindexFileProcessing($conditions);
        if (isCompany()) {
            // $conditions = array(
            //     'FileProccessingDetail.file_date >= ' => $startDate,
            //     'FileProccessingDetail.file_date <= ' => $endDate,
            //     'FileProccessingDetail.company_id' => getCompanyId(),
            // );
            $indexArr['paginate']['conditions'] = $conditions;
        }
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('stations');
        $joinTable['joins'] = array(
            array(
                'table' => 'stations',
                'type' => 'LEFT',
                'conditions' => array(
                    'FileProccessingDetail.station = stations.id'

                ),
            ),
        );
        // $this->AutoPaginate->setPaginate($indexArr['paginate']);
        // $processFiles = $this->FileProccessingDetail->find('all', $joinTable)->getSql();
        // $stattionData = $this->FileProccessingDetail->find('all', $joinTable);
        $stattionData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stattionData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->AutoPaginate->setPaginate($indexArr['paginate']);
        $processFiles = $this->paginate('FileProccessingDetail');
        $this->set(compact('processFiles', 'temp_station'));
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        if (isCompany()) {
            // $conditions = array(
            //     'FileProccessingDetail.file_date >= ' => $startDate,
            //     'FileProccessingDetail.file_date <= ' => $endDate,
            //     'FileProccessingDetail.company_id' => getCompanyId(),
            // );
            $indexArr['chartData']['conditions'] = $conditions;
        }
        $chartData = $this->FileProccessingDetail->find('all', $indexArr['chartData']);
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.FileProccessingDetail');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['file_date']));
            if (isset($temp[$date])) {
                $value['file_count'] = $value['file_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['file_date']) * 1000), $value['file_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        /**
         * pie chart
         */
        $pieTitle = __('File Processed');
        $pieName = __('Files');
        $fields = 'FileProccessingDetail.id, file_count, FileProccessingDetail.company_id,FileProccessingDetail.branch_id, FileProccessingDetail.created_date';
        $indexArr = $this->Analytic->getindexFileProcessing($conditions, $fields);
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        $filesPieData = $this->FileProccessingDetail->find('all', $indexArr['filesPieData']);
        $this->Session->write('Report.IndexFileProcessingReportCondition', $conditions);
        $totalFiles = 0;
        foreach ($filesPieData as $file) {
            $totalFiles = $totalFiles + $file['FileProccessingDetail']['file_count'];
        }
        $this->loadModel('CompanyBranch');
        $companybranch = $this->CompanyBranch->find('all');
        $temp_branches = array();
        foreach ($companybranch as $value) {
            $temp_branches[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $fileArr = array();
        foreach ($filesPieData as $file) {
            $companyName = isset($temp_branches[$file['FileProccessingDetail']['branch_id']]) ? $temp_branches[$file['FileProccessingDetail']['branch_id']] : '';
            $percen = getPercentage($totalFiles, $file['FileProccessingDetail']['file_count']);
            $fileArr[] = array(
                'name' => str_replace("'","",$companyName), 
                'y' => $percen
            );
        }

        $filesPieData = json_encode($fileArr);
        $this->set(compact('temp', 'xAxisDates', 'tickInterval', 'filesPieData', 'pieTitle', 'pieName'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.FileProcessing', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CompanyId', $this->request->data['Analytic']['company_id']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $this->set(compact('companyDetail'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $htmlDataTable = $this->render('/Elements/user/process_files')->body();
            echo json_encode(array('data' => $temp, 'pieChartData' => $filesPieData, 'pieTitle' => $pieTitle, 'pieName' => $pieName, 'xAxisDates' => $xAxisDates, 'tickInterval' => $tickInterval, 'htmlData' => $htmlDataTable));
            exit;
        }
    }
    /**
     * company side file processing view
     * @param type $all
     */
    function file_processing($all = null)
    {
        if (!empty($all)) {
            $this->Session->delete('Report.CompanyIdCompany');
            $this->Session->delete('Report.FileProcessingCompany');
        }
        if (empty($this->request->data['Analytic']['company_id']) && $this->Session->check('Report.CompanyIdCompany')) {
            $this->request->data['Analytic']['company_id'] = $this->Session->read('Report.CompanyIdCompany');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        if (isCompany()) {
            $companyId = getCompanyId();
        }
        if (isSuparAdmin()) {
            $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
            //            $this->request->data['Analytic']['company_id'] = $companyId;
            $this->set(compact('companies'));
        }
        if (empty($this->request->data['Filter']) && $this->Session->check('Report.FileProcessingCompany')) {
            $this->request->data['Filter'] = $this->Session->read('Report.FileProcessingCompany');
        }
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->__getConditions('FileProcessingCompany', $this->request->data['Filter'], 'FileProccessingDetail');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate,
        );
        if (!empty($companyId)) {
            $conditions['FileProccessingDetail.company_id'] = $companyId;
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        /**
         * add extra conditions except the common conditions
         */
        $fileProcessingArr = $this->Analytic->getFileProcessing($conditions);

        $this->loadModel('FileProccessingDetail');

        $this->FileProccessingDetail->virtualFields['fileProcessed'] = "SUM(IF(processing_endtime != '0000-00-00 00:00:00' and processing_endtime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done
        $this->FileProccessingDetail->virtualFields['no_of_file_received'] = "SUM(IF(processing_endtime = '0000-00-00 00:00:00' and processing_starttime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done 
        $this->loadModel('ErrorDetail');
        $this->ErrorDetail->virtualFields['no_of_errors'] = "count(ErrorDetail.id)";
        $this->loadModel('TransactionDetail');
        $this->TransactionDetail->virtualFields['no_of_deposit'] = "count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['no_of_withdrawal'] = "count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['total_cash_deposit'] = "sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.other_cash_deposited,0))";
        $this->TransactionDetail->virtualFields['total_cash_withdrawal'] = "sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['total_cash_requested'] = "sum(TransactionDetail.amount_requested)";
        $this->TransactionDetail->virtualFields['no_of_report'] = "count(TransactionDetail.history_report_id)";
        $this->TransactionDetail->virtualFields['no_of_transaction'] = "count(TransactionDetail.id)";

        ///GETTING COUNT START
        $this->FileProccessingDetail->virtualFields['no_of_deposit'] = 'select count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0)) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_withdrawal'] = 'select count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0)) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['total_cash_deposit'] = 'select sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.other_cash_deposited,0)) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['total_cash_withdrawal'] = 'select sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0)) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['total_cash_requested'] = 'select sum(TransactionDetail.amount_requested) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_report'] = 'select count(TransactionDetail.history_report_id) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_transaction'] = 'select count(TransactionDetail.id) from transaction_details as TransactionDetail where TransactionDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_errors'] = 'select count(*) from error_detail as ErrorDetail where ErrorDetail.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_netCashUsage'] = 'select count(*) from net_cash_usage_activity_report where net_cash_usage_activity_report.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_automix'] = 'select count(*) from automix_setting as AutomixSetting where AutomixSetting.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_billactivity'] = 'select count(*) from bills_activity_report as BillsActivityReport where BillsActivityReport.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_billadjustment'] = 'select count(*) from bill_adjustments as BillAdjustment where BillAdjustment.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_billcount'] = 'select count(*) from bills_count as BillCount where BillCount.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_billhistory'] = 'select count(*) from bills_history as BillHistory where BillHistory.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_coininventory'] = 'select count(*) from coin_inventory as CoinInventory where CoinInventory.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_currTellerTrans'] = 'select count(*) from current_teller_transactions as CurrentTellerTransactions where CurrentTellerTransactions.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_historyReport'] = 'select count(*) from history_report as HistoryReport where HistoryReport.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_mgrSetup'] = 'select count(*) from manager_setup as ManagerSetup where ManagerSetup.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_sideActivity'] = 'select count(*) from side_activity_report as SideActivityReport where SideActivityReport.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_tellerActivity'] = 'select count(*) from teller_activity_report as TellerActivityReport where TellerActivityReport.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_vaultBuy'] = 'select count(*) from vault_buys as ValutBuy where ValutBuy.file_processing_detail_id = FileProccessingDetail.id';
        $this->FileProccessingDetail->virtualFields['no_of_teller_setup'] = 'select count(*) from teller_setup as TellerSetup where TellerSetup.file_processing_detail_id = FileProccessingDetail.id';
        ///GETTING COUNT END
        ClassRegistry::init('AutomixSetting')->virtualFields['no_of_automix'] = "count(AutomixSetting.id)";
        ClassRegistry::init('BillsActivityReport')->virtualFields['no_of_billactivity'] = "count(BillsActivityReport.id)";
        ClassRegistry::init('BillAdjustment')->virtualFields['no_of_billadjustment'] = "count(BillAdjustment.id)";
        ClassRegistry::init('BillCount')->virtualFields['no_of_billcount'] = "count(BillCount.id)";
        ClassRegistry::init('BillHistory')->virtualFields['no_of_billhistory'] = "count(BillHistory.id)";
        ClassRegistry::init('CoinInventory')->virtualFields['no_of_coininventory'] = "count(CoinInventory.id)";
        ClassRegistry::init('CurrentTellerTransactions')->virtualFields['no_of_currTellerTrans'] = "count(CurrentTellerTransactions.id)";
        ClassRegistry::init('HistoryReport')->virtualFields['no_of_historyReport'] = "count(HistoryReport.id)";
        ClassRegistry::init('ManagerSetup')->virtualFields['no_of_mgrSetup'] = "count(ManagerSetup.id)";
        ClassRegistry::init('NetCashUsageActivityReport')->virtualFields['no_of_netCashUsage'] = "count(NetCashUsageActivityReport.id)";
        ClassRegistry::init('SideActivityReport')->virtualFields['no_of_sideActivity'] = "count(SideActivityReport.id)";
        ClassRegistry::init('TellerActivityReport')->virtualFields['no_of_tellerActivity'] = "count(TellerActivityReport.id)";
        ClassRegistry::init('ValutBuy')->virtualFields['no_of_vaultBuy'] = "count(ValutBuy.id)";
        ClassRegistry::init('TellerSetup')->virtualFields['no_of_teller_setup'] = "count(TellerSetup.id)";
        $this->AutoPaginate->setPaginate($fileProcessingArr['paginate']);
        $processFiles = $this->paginate('FileProccessingDetail');
        $this->Session->write('Report.FileProcessingReportCondition', $conditions);
        $this->set(compact('processFiles'));
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        $chartData = $this->FileProccessingDetail->find('all', $fileProcessingArr['chartData']);
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.FileProccessingDetail');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['file_date']));
            if (isset($temp[$date])) {
            }
            $temp[$date] = array((strtotime($value['file_date']) * 1000), $value['file_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        /**
         * pie chart
         */
        $pieTitle = __('File Processed');
        $pieName = __('Files');
        $fields = 'FileProccessingDetail.id, file_count, FileProccessingDetail.company_id, FileProccessingDetail.branch_id, FileProccessingDetail.file_date';
        $fileProcessingArr = $this->Analytic->getFileProcessing($conditions, $fields);
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        $filesPieData = $this->FileProccessingDetail->find('all', $fileProcessingArr['filesPieData']);
        $totalFiles = 0;
        foreach ($filesPieData as $file) {
            $totalFiles = $totalFiles + $file['FileProccessingDetail']['file_count'];
        }
        $fileArr = $branchesLists = array();
        if (isCompany()) {
            $branchesLists = ClassRegistry::init('CompanyBranch')->getMyBranchLists(getCompanyId());
        }
        foreach ($filesPieData as $file) {
            if (isCompany()) {
                $companyName = isset($branchesLists[$file['FileProccessingDetail']['branch_id']]) ? $branchesLists[$file['FileProccessingDetail']['branch_id']] : '';
            } else {
                $companyName = isset($companies[$file['FileProccessingDetail']['company_id']]) ? $companies[$file['FileProccessingDetail']['company_id']] : '';
            }
            $percen = getPercentage($totalFiles, $file['FileProccessingDetail']['file_count']);
            $fileArr[] = array(
                'name' => $companyName,
                'y' => $percen
            );
        }
        $filesPieData = json_encode($fileArr);
        $this->set(compact('temp', 'xAxisDates', 'tickInterval', 'filesPieData', 'pieTitle', 'pieName'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.FileProcessingCompany', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CompanyIdCompany', $this->request->data['Analytic']['company_id']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $this->set(compact('companyDetail'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $htmlDataTable = $this->render('/Elements/user/file_processing')->body();
            echo json_encode(array('data' => $temp, 'pieChartData' => $filesPieData, 'pieTitle' => $pieTitle, 'pieName' => $pieName, 'xAxisDates' => $xAxisDates, 'tickInterval' => $tickInterval, 'htmlData' => $htmlDataTable));
            exit;
        }
    }

    /**
     * Transaction details
     * @param type $all
     */
    function note_count($all = '')
    {

        if (!empty($all)) {
            $this->Session->delete('Report.CompanyId');
            $this->Session->delete('Report.Transaction');
            $this->Session->delete('Report.TransactionReport_NoteCondition');
        }

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

        if ($this->Session->read('Report.GlobalFilter') == null || $this->Session->read('Report.GlobalFilter') == '') {
            $this->request->data['from'] = 'last_7days';
            $this->request->data['start_date'] = date('Y-m-d', strtotime('-6 days'));
            $this->request->data['end_date'] = date('Y-m-d');
        }
        $from = !empty($this->request->data['from']) ? $this->request->data['from'] : 'last_7days';

        if (!empty($this->request->data['from'])) {
            $this->Session->write('Report.Transaction', $this->request->data);
        }

        $final_data = array();
        $trans_datetime = date('Y-m-d', strtotime('-6 days'));
        $today = date('Y-m-d', strtotime('0 days'));
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');

        // $transactionConditionData = $this->Session->read('Report.Transaction');
        $transactionConditionData = $this->Session->read('Report.GlobalFilter');
        $conditions = '';
        if (!empty($this->request->data['start_date']) && !empty($this->request->data['end_date'])) {
            $conditions = array(
                'TransactionDetail.trans_datetime >= ' => $this->request->data['start_date'] . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $this->request->data['end_date'] . ' 23:59:59'
            );
        } elseif ($transactionConditionData != '') {
            $from = !empty($transactionConditionData['from']) ? $transactionConditionData['from'] : 'last_7days';
            $conditions = array(
                'TransactionDetail.trans_datetime >= ' => $transactionConditionData['start_date'] . ' 00:00:00',
                'TransactionDetail.trans_datetime <= ' => $transactionConditionData['end_date'] . ' 23:59:59'
            );
        } else {
            if (empty($this->request->data)) {
                $conditions = array(
                    'TransactionDetail.trans_datetime >= ' => $trans_datetime . ' 00:00:00',
                    'TransactionDetail.trans_datetime <= ' => $today . ' 23:59:59'
                );
            }
        }

        $all_condition = $this->Session->read('Report.TransactionReport_NoteCondition');
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
                /*$conditions['BillsActivityReport.station'] = $all_condition['FileProccessingDetail.station'];*/
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
            /* $conditions_new['TransactionDetail.trans_datetime >= ']=date('Y-m-d 00:00:00', strtotime("-1 days"));
             $conditions_new['TransactionDetail.trans_datetime <= ']=date('Y-m-d 23:59:59', strtotime("-1 days"));
             $conditions_new['FileProccessingDetail.file_date = ']=date('Y-m-d', strtotime("-1 days"));*/
        }

        $sessData = getMySessionData();

        
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
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

        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (!isCompany()) {
            $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports($companyId);
        }

        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);


        if (isCompany()) {

            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $sessData = getMySessionData();
        if (!empty($sessData['BranchDetail']['id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $sessData['BranchDetail']['id'];
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if (!empty($sessData['assign_branches'])) {
            $assignedBranches = $sessData['assign_branches'];
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        $filter_criteria = array();
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            // if (!empty($branches)) {
            //     $conditions['FileProccessingDetail.branch_id'] = $branches;
            // }
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branches_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branches_list['CompanyBranch']['name'];
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($conditions['FileProccessingDetail.branch_id']);
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['TransactionDetail.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['TransactionDetail.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            $startDate = date('Y-m-d', strtotime($date[0]));
            $endDate = date('Y-m-d', strtotime($date[1]));
            $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

            // $conditions['FileProccessingDetail.branch_id'] = $branches;
        }
        /*if (!empty($this->request->data['Analytic']['regiones'])) {  
            $conditions['FileProccessingDetail.regiones'] = $this->request->data['Analytic']['regiones'];
        }*/
       
        $this->Session->write('Report.TransactionReport_NoteCondition', $conditions);
        $total_selected_denom = 0;
        $total_denom = 0;
        $datas = $this->TransactionDetail->find('all', array(
            'fields' => array(
                'SUM(TransactionDetail.denom_1) AS denom_1',
                'SUM(TransactionDetail.denom_2) AS denom_2',
                'SUM(TransactionDetail.denom_5) AS denom_5',
                'SUM(TransactionDetail.denom_10) AS denom_10',
                'SUM(TransactionDetail.denom_20) AS denom_20',
                'SUM(TransactionDetail.denom_50) AS denom_50',
                'SUM(TransactionDetail.denom_100) AS denom_100'
            )
        ));

        foreach ($datas as $key => $data) {
            foreach ($data as $key1 => $value) {
                $total_denom =  $total_denom + $value['denom_1'] + $value['denom_2'] + $value['denom_5'] + $value['denom_10'] + $value['denom_20'] + $value['denom_50'] + $value['denom_100'];
            }
        }
        $sevendays_transactio_details = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(TransactionDetail.denom_1) AS denom_1',
                'SUM(TransactionDetail.denom_2) AS denom_2',
                'SUM(TransactionDetail.denom_5) AS denom_5',
                'SUM(TransactionDetail.denom_10) AS denom_10',
                'SUM(TransactionDetail.denom_20) AS denom_20',
                'SUM(TransactionDetail.denom_50) AS denom_50',
                'SUM(TransactionDetail.denom_100) AS denom_100'
            )
        ));

        foreach ($sevendays_transactio_details as $key => $sevendays_transactio_detail) {
            foreach ($sevendays_transactio_detail as $key1 => $value) {
                $total_selected_denom =  $total_selected_denom + $value['denom_1'] + $value['denom_2'] + $value['denom_5'] + $value['denom_10'] + $value['denom_20'] + $value['denom_50'] + $value['denom_100'];
                if (empty($value['denom_1'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_1'];
                }
                if (empty($value['denom_2'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_2'];
                }
                if (empty($value['denom_5'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_5'];
                }
                if (empty($value['denom_10'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_10'];
                }
                if (empty($value['denom_20'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_20'];
                }
                if (empty($value['denom_50'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_50'];
                }
                if (empty($value['denom_100'])) {
                    $final_data[] = '0';
                } else {
                    $final_data[] = $value['denom_100'];
                }
            }
        }

        $this->loadModel('Analytic');
        $this->loadModel('TransactionDetail');
        $this->Session->write('Report.TransactionReport_NoteCondition', $conditions);

        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';
        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_deposit_denom1'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_1,0)) AS deposit_denom_1';
        $this->TransactionDetail->virtualFields['total_deposit_denom2'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_2,0)) AS deposit_denom_2';
        $this->TransactionDetail->virtualFields['total_deposit_denom5'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_5,0)) AS deposit_denom_5';
        $this->TransactionDetail->virtualFields['total_deposit_denom10'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_10,0)) AS deposit_denom_10';
        $this->TransactionDetail->virtualFields['total_deposit_denom20'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_20,0)) AS deposit_denom_20';
        $this->TransactionDetail->virtualFields['total_deposit_denom50'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_50,0)) AS deposit_denom_50';
        $this->TransactionDetail->virtualFields['total_deposit_denom100'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_100,0)) AS deposit_denom_100';
        $transactionDetailArray = $this->Analytic->getTransactionDetailsForNoteCount($conditions, $regionsId);
        // echo '<pre><b>' . __FILE__ . ' (Line:'. __LINE__ .')</b><br>';
        // print_r($transactionDetailArray);echo '<br>';exit;
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $transactions = $this->paginate('TransactionDetail');
        $this->set(compact('final_data', 'companies', 'regiones', 'branches', 'stations', 'companyDetail', 'transactions', 'total_selected_denom', 'total_denom', 'temp_station'));


        if ($this->request->is('ajax')) {
            $this->layout = false;

            $transactions = $this->render('/Elements/user/note_count_details')->body();

            echo json_encode(array('final_data' => $final_data, 'htmlData' => $transactions, 'total_selected_denom' => $total_selected_denom, 'total_denom' => $total_denom), JSON_NUMERIC_CHECK);
            exit;
        }
    }
    function transaction_details($all = '')
    {
        // if (!empty($all)) {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.TransactionCondition');
        // }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
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
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        if (!isset($this->request->data['Analytic']['date']) or empty($this->request->data['Analytic']['date'])) {
            $this->request->data['Analytic']['date'] = date('Y-m-d', strtotime("-1 days"));
        } else {
            $conditions3 = array();
            $tdate = $this->request->data['Analytic']['date'];
            $tdate = str_replace('-', '/', $tdate);
            $tdate = date('Y-m-d', strtotime($tdate));
        }

        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        if (isSuparAdmin() && empty($company_id)) {
            $company_id = array_keys($companies);
        }
        $this->loadModel('TransactionDetail');
        $this->loadModel('TransactionType');
        $transaction_type_ids  = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_id], 'fields' => 'DISTINCT trans_type_id '));
        $transaction_type_arr = array();
        foreach ($transaction_type_ids as $key => $value) {
            $transactionType_condition['TransactionType.id'] = $value['TransactionDetail']['trans_type_id'];
            $transaction_type = $this->TransactionType->find('first', array('fields' => 'id, text', 'conditions' => $transactionType_condition));
            $transaction_type_arr[$value['TransactionDetail']['trans_type_id']] = $transaction_type['TransactionType']['text'];
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
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
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        // echo '<pre><b></b><br>';
        // print_r($this->request->data['Analytic']);echo '<br>';exit;
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            if (!empty($branches)) {
            }
            $branches = ClassRegistry::init('Com/panyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
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
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['tellerName'])) {
            $conditions['TransactionDetail.teller_name'] = $this->request->data['Analytic']['tellerName'];
            $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['trasaction_type'])) {
            $conditions['TransactionDetail.trans_type_id'] = $this->request->data['Analytic']['trasaction_type'];
            $filter_criteria['trasaction_type'] = $this->request->data['Analytic']['trasaction_type'];
            $this->set(compact('filter_criteria'));
        }
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_id],'fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $transactionArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
        $this->AutoPaginate->setPaginate($transactionArray['paginate2']);
        $transactionsDetails = $this->paginate('TransactionDetail');
        $this->Session->write('Report.TransactionReportCondition', $conditions);
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';

        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)';
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $pieTitle = __('Transaction Percentage by Type of Transaction');
        $pieCatTitle = __('Transaction V/s Transaction Category');
        $pieClientTitle = __('Transaction Volume by Branch');
        $pieName = __('Transactions');
        if (!empty($this->request->data['Analytic']['date'])) {
            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions_new = $conditions;
            $conditions_new['TransactionDetail.trans_datetime >= '] = $this->request->data['Analytic']['date'] . ' 00:00:00';
            $conditions_new['TransactionDetail.trans_datetime <= '] = $this->request->data['Analytic']['date'] . ' 23:59:59';
            $conditions_new['FileProccessingDetail.file_date >= '] = $this->request->data['Analytic']['date'];
            $conditions_new['FileProccessingDetail.file_date <= '] = $this->request->data['Analytic']['date'];
            $hourly_data2 = array();
            $hourly_data3 = array();

            $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions_new);
            $hourly_data = $this->TransactionDetail->find('list', $transactionDetailArray2['paginate2']);

            foreach ($hourly_data as $key => $value) {
                $hourly_data2[$key][0] = date("H", strtotime($key));
                $hourly_data2[$key][1] = $value;
            }
            foreach ($hourly_data2 as $key => $value) {
                array_push($hourly_data3, $value);
            }
            if (empty($hourly_data3)) {
                array_push($hourly_data3, [0, 0]);
            }
            $hourly_report_data = json_encode($hourly_data3, JSON_NUMERIC_CHECK);

            if ($hourly_report_data == null) {
                $hourly_report_data = json_encode([[0, 0]], JSON_NUMERIC_CHECK);
            }
        } else {
            $hourly_report_data = json_encode(1, JSON_NUMERIC_CHECK);
        }

        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionPieChart']);
        $transactionTypes = ClassRegistry::init('TransactionType')->find('list', array('fields' => 'id, text'));
        $transactionPie = $this->Analytic->getPieChartData($transactionTypes, $transactionPie);
        $transactionPie = json_encode($transactionPie);
        /**
         * transactions vs category
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        //        debug($conditions);
        $transactionCatPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionCategoryChart']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $transactionCatPie = $this->Analytic->getPieChartData($transactionCategories, $transactionCatPie, true);
        $transactionCatPie = json_encode($transactionCatPie);
        /**
         * transactions vs branch
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        // $conditions['FileProccessingDetail.branch_id'] = array_keys($branches);
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);

        }

        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
        $transactionClientPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionClientPie']);
        if (empty($branches)) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($conditions['FileProccessingDetail.company_id']);
        }


        $transactionClients = $branches;
        $transactionClientPie = $this->Analytic->getPieChartData($transactionClients, $transactionClientPie);
        $transactionClientPie = json_encode($transactionClientPie);
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions2);
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['company']['id']] = $value['CompanyBranch']['name'];
        }
        $chartData = $this->TransactionDetail->find('all', $transactionDetailArray['chart']['chartData']);
        $chartData2 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData2']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));


        $temp = array();
        $temp2 = array();
        $tempCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['transaction_count']);
            $tempCount++;
        }
        $tempCount = 0;
        foreach ($chartData2 as $key => $value) {
            $date = date('Y-m-d', strtotime($value['TransactionDetail']['trans_datetime']));
            // ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            if (isset($temp2[$date])) {
            } else {
                $temp2[$date] = array((strtotime($value['TransactionDetail']['trans_datetime']) * 1000), $value['TransactionDetail']['trans_datetime'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            }
            if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[0]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[1])) {
                $temp2[$date][2]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[1]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[2])) {
                $temp2[$date][3]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[2]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[3])) {
                $temp2[$date][4]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[3]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[4])) {
                $temp2[$date][5]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[4]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[5])) {
                $temp2[$date][6]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[5]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[6])) {
                $temp2[$date][7]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[6]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[7])) {
                $temp2[$date][8]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[7]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[8])) {
                $temp2[$date][9]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[8]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[9])) {
                $temp2[$date][10]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[9]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[10])) {
                $temp2[$date][11]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[10]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[11])) {
                $temp2[$date][12]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[11]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            }
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;

        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $chartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'trans_datetime', 'transaction_count', 'total_amount_withdrawal',
                'total_cash_deposite', 'FileProccessingDetail.file_date'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['total_amount_withdrawal'] = $value['TransactionDetail']['total_amount_withdrawal'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_amount_withdrawal']);
            if (isset($temp1[$date])) {
                $value['TransactionDetail']['total_cash_deposite'] = $value['TransactionDetail']['total_cash_deposite'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_cash_deposite']);
            if (isset($temp2[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), ($value['TransactionDetail']['transaction_count']));
        }
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
            )
        );

        $temp1 = json_encode($temp1);

        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $temp = $oldTemp;

        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            //                $date = date('Y-m-d', strtotime($value['trans_datetime']));
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime($yesterdayDate));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;
        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/transaction_details')->body();
            $options = array(
                'id' => '#container',
                'name' => __('Transactions'),
                'title' => __('Transactions'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction'),
            );
            echo json_encode(array(
                'options' => $options,
                'data' => $temp1,
                'xAxisDates' => $xAxisDates1,
                'htmlData' => $transactionData,
                'pieChartData' => $transactionPie,
                'transactionCatPie' => $transactionCatPie,
                'pieCatTitle' => $pieCatTitle,
                'transactionClientPie' => $transactionClientPie,
                'pieClientTitle' => $pieClientTitle,
                'pieTitle' => $pieTitle,
                'pieName' => $pieName,
                'tickInterval' => $tickInterval,
                'transactionDetails' => $temp1,
                'hourData' => $temp1
            ));
            exit;
        }
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $i = 0;
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }

        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);

        $depositeCondition = $transactionDetailArray2['chart']['DepositeChart'];
        $depositeCondition['conditions']['TransactionDetail.trans_type_id'] = 2;
        $depositeCondition['group'] = 'FileProccessingDetail.file_date';
        $depositeCount = $this->TransactionDetail->find('all', $depositeCondition);
        $j = 0;
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
        foreach ($InventoryCount as $key => $data) {
            $Inventory_data[$h][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $Inventory_data[$h][1] = $data['TransactionDetail']['transaction_count'];
            $h++;
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.Transaction', $this->request->data['Filter']);
        }
        $Inventorychartdata = json_encode($Inventory_data, JSON_NUMERIC_CHECK);
        $this->set(compact('newchartdata', 'depositechartdata', 'withdrawschartdata', 'Inventorychartdata'));
        $this->set(compact('sessionData', 'companies', 'regiones', 'branches', 'stations', 'tellerNames_Arr', 'transaction_type_arr', 'transactionsDetails', 'hourly_report_data', 'temp_station', 'transactionCatPie', 'transactionClientPie', 'pieClientTitle', 'pieCatTitle', 'pieTitle', 'pieName', 'tickInterval', 'transactions', 'temp_companydata', 'temp_station', 'transactionCategories', 'transactionTypes', 'transactionPie', 'temp_hr', 'temp', 'xAxisDates', 'temp1', 'sentTemp', 'xAxisDatesTime'));

    }

    function hour_by_transaction($all = '')
    {
        // if (!empty($all)) {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.HourbyTransactionCondition');
        // }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
        }

        $sessionData = getMySessionData();

        $filter_criteria = array();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        if (isSuparAdmin() && empty($company_id)) {
            $company_id = array_keys($companies);
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (!isCompany()) {
            $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports($companyId);
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorDetail');
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
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $this->set(compact('filter_criteria'));
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;//getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['TransactionDetail.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['TransactionDetail.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            $startDate = date('Y-m-d', strtotime($date[0]));
            $endDate = date('Y-m-d', strtotime($date[1]));
            $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);            $conditions_regiones = array(
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
        if(!empty($this->request->data['Analytic']['regiones'])){
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
        }
        $transactionArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
        $this->loadModel('TransactionDetail');
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';

        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)';
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $pieTitle = __('Inventory Management Transaction Type');
        $pieCatTitle = __('Transaction V/s Transaction Category');
        $pieClientTitle = __('Transactions by Branch');
        $pieName = __('Transactions');
        $transactions = $this->paginate('TransactionDetail');
        $transaction_chart = $this->TransactionDetail->find('all', $transactionDetailArray['paginate3']);
        $key_arr = array();
        $key_sort = array();
        foreach ($transaction_chart as $key => $value) {

            $key_arr[date("H", strtotime($value['TransactionDetail']['trans_datetime']))][$key] = $value['TransactionDetail']['total_transaction'];
            array_push($key_sort, date("H", strtotime($value['TransactionDetail']['trans_datetime'])));
        }
        $minValues = array();
        $maxValues = array();
        $original_Arr = array();
        $keyArr2 = array();
        $i = 1;
        $original_Arr[0][0] = "Hour";
        $original_Arr[0][1] = "Minimum";
        $original_Arr[0][2] = "Average";
        $original_Arr[0][3] = "Maximum";
        asort($key_sort);
        foreach ($key_sort as $key => $value) {
            $keyArr2[$value] = $key_arr[$value];
        }
        foreach ($keyArr2 as $key => $item) {
            $key_arr[$key] = array_values($item);
            $count_total = count($key_arr[$key]);
            $avrage_total = array_sum($key_arr[$key]) / $count_total;
            $date=date_create($key.":00:00");
            $time = date_format($date,"g A");
            $original_Arr[$i][0] = "$time ";
            $original_Arr[$i][1] = min($key_arr[$key]);
            $original_Arr[$i][2] = ceil($avrage_total);
            $original_Arr[$i][3] = max($key_arr[$key]);
            $i++;
        }
        // echo '<pre><b></b><br>';
        // print_r($original_Arr);echo '<br>';exit;
        $newBarchat = json_encode($original_Arr, JSON_NUMERIC_CHECK);
        if (!empty($this->request->data['Analytic']['date'])) {
            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions_new = $conditions;
            $conditions_new['TransactionDetail.trans_datetime >= '] = $this->request->data['Analytic']['date'] . ' 00:00:00';
            $conditions_new['TransactionDetail.trans_datetime <= '] = $this->request->data['Analytic']['date'] . ' 23:59:59';
            $conditions_new['FileProccessingDetail.file_date >= '] = $this->request->data['Analytic']['date'];
            $conditions_new['FileProccessingDetail.file_date <= '] = $this->request->data['Analytic']['date'];
            $hourly_data2 = array();
            $hourly_data3 = array();

            $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions_new);
            $hourly_data = $this->TransactionDetail->find('list', $transactionDetailArray2['paginate2']);

            foreach ($hourly_data as $key => $value) {
                $hourly_data2[$key][0] = date("H", strtotime($key));
                $hourly_data2[$key][1] = $value;
            }
            foreach ($hourly_data2 as $key => $value) {
                array_push($hourly_data3, $value);
            }
            if (empty($hourly_data3)) {
                array_push($hourly_data3, [0, 0]);
            }
            $hourly_report_data = json_encode($hourly_data3, JSON_NUMERIC_CHECK);

            if ($hourly_report_data == null) {
                $hourly_report_data = json_encode([[0, 0]], JSON_NUMERIC_CHECK);
            }
        } else {
            $hourly_report_data = json_encode(1, JSON_NUMERIC_CHECK);
        }
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionPieChart']);
        $transactionTypes = ClassRegistry::init('TransactionType')->find('list', array('fields' => 'id, text'));
        $transactionPie = $this->Analytic->getPieChartData($transactionTypes, $transactionPie);
        $transactionPie = json_encode($transactionPie);
        /**
         * transactions vs category
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        //        debug($conditions);

        $transactionCatPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionCategoryChart']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $transactionCatPie = $this->Analytic->getPieChartData($transactionCategories, $transactionCatPie, true);
        $transactionCatPie = json_encode($transactionCatPie);
        /**
         * transactions vs branch
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionClients = $branches;
        $transactionClientPie = $this->Analytic->getPieChartData($transactionClients, $transactionClientPie);
        $transactionClientPie = json_encode($transactionClientPie);
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $chartData = $this->TransactionDetail->find('all', $transactionDetailArray['chart']['chartData']);
        $chartData2 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData2']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));

        $temp = array();
        $temp2 = array();
        $tempCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['transaction_count']);
            $tempCount++;
        }
        $tempCount = 0;
        foreach ($chartData2 as $key => $value) {
            $date = date('Y-m-d', strtotime($value['TransactionDetail']['trans_datetime']));
            // ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            if (isset($temp2[$date])) {
            } else {
                $temp2[$date] = array((strtotime($value['TransactionDetail']['trans_datetime']) * 1000), $value['TransactionDetail']['trans_datetime'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            }
            if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[0]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[1])) {
                $temp2[$date][2]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[1]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[2])) {
                $temp2[$date][3]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[2]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[3])) {
                $temp2[$date][4]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[3]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[4])) {
                $temp2[$date][5]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[4]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[5])) {
                $temp2[$date][6]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[5]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[6])) {
                $temp2[$date][7]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[6]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[7])) {
                $temp2[$date][8]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[7]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[8])) {
                $temp2[$date][9]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[8]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[9])) {
                $temp2[$date][10]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[9]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[10])) {
                $temp2[$date][11]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[10]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[11])) {
                $temp2[$date][12]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[11]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            }
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;

        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $company_id)));

        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $chartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'trans_datetime', 'transaction_count', 'total_amount_withdrawal',
                'total_cash_deposite', 'FileProccessingDetail.file_date'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['total_amount_withdrawal'] = $value['TransactionDetail']['total_amount_withdrawal'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_amount_withdrawal']);
            if (isset($temp1[$date])) {
                $value['TransactionDetail']['total_cash_deposite'] = $value['TransactionDetail']['total_cash_deposite'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_cash_deposite']);
            if (isset($temp2[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), ($value['TransactionDetail']['transaction_count']));
        }
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
            )
        );

        $temp1 = json_encode($temp1);

        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $temp = $oldTemp;

        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));

        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            //                $date = date('Y-m-d', strtotime($value['trans_datetime']));
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime($yesterdayDate));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;
        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();

        $aa = $this->request->data['Analytic']['date'];
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;

        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/transaction_details')->body();
            $options = array(
                'id' => '#container',
                'name' => __('Transactions'),
                'title' => __('Transactions'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction'),
            );
            echo json_encode(array(
                'options' => $options,
                'data' => $temp1,
                'xAxisDates' => $xAxisDates1,
                'htmlData' => $transactionData,
                'pieChartData' => $transactionPie,
                'transactionCatPie' => $transactionCatPie,
                'pieCatTitle' => $pieCatTitle,
                'transactionClientPie' => $transactionClientPie,
                'pieClientTitle' => $pieClientTitle,
                'pieTitle' => $pieTitle,
                'pieName' => $pieName,
                'tickInterval' => $tickInterval,
                'transactionDetails' => $temp1,
                'hourData' => $temp1
            ));
            exit;
        }

        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        // $conditions = array(
        //     'FileProccessingDetail.file_date >= ' => $startDate . ' 00:00:00',
        //     'FileProccessingDetail.file_date <= ' => $endDate . ' 23:59:59',
        //     'FileProccessingDetail.company_id' => $sessData['id']
        // );
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $this->loadModel('TransactionDetail');

        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $i = 0;
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }

        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);
        $this->set(compact('sessionData', 'companies', 'branches', 'regiones', 'stations', 'newBarchat', 'hourly_report_data', 'transactionCatPie', 'transactionClientPie', 'pieClientTitle', 'pieCatTitle', 'pieTitle', 'pieName', 'tickInterval', 'transactions', 'temp_station', 'transactionCategories', 'transactionTypes', 'transactionPie', 'temp', 'xAxisDates', 'temp_hr', 'companyDetail', 'temp', 'temp1', 'xAxisDates1', 'sentTemp', 'xAxisDatesTime', 'newchartdata'));
    }

    function transaction_details2($companyId, $station, $datetime, $hour, $conditions_new)
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
            /* $conditions_new['TransactionDetail.trans_datetime >= ']=date('Y-m-d 00:00:00', strtotime("-1 days"));
             $conditions_new['TransactionDetail.trans_datetime <= ']=date('Y-m-d 23:59:59', strtotime("-1 days"));
             $conditions_new['FileProccessingDetail.file_date = ']=date('Y-m-d', strtotime("-1 days"));*/
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
        // $transactionArray = $this->Analytic->getTransactionDetails2($conditions);
        $this->loadModel('TransactionDetail');
        // $this->Session->write('Report.TransactionReportCondition', $conditions);
        if ($conditions_new == 1) {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'FileProccessingDetail.station' => $station,
                'TransactionDetail.teller_name' => $datetime,
                'FileProccessingDetail.id' => $hour,
                'TransactionDetail.trans_type_id' => array(1, 2, 11)
            );
        } else {
            $arrCondition = array(
                'FileProccessingDetail.company_id' => $companyId,
                'FileProccessingDetail.station' => $station,
                'TransactionDetail.trans_datetime >=' => date("Y-m-d", $datetime) . " " . $hour . ":00:00",
                'TransactionDetail.trans_datetime <=' => date("Y-m-d", $datetime) . " " . $hour . ":59:59",
            );
        }
        if ($conditions_new == 6) {
            $arrCondition['TransactionDetail.trans_type_id'] = array(4, 5);
        }
        if ($conditions_new == 9) {
            if ($hour != 0) {
                $date = (explode("_", $hour));
                $arrCondition = array(
                    'FileProccessingDetail.company_id' => $companyId,
                    'FileProccessingDetail.branch_id' => $station,
                    'TransactionDetail.teller_name' => $datetime,
                    'TransactionDetail.trans_datetime >=' => date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00')),
                    'TransactionDetail.trans_datetime <=' => date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59')),
                    'TransactionDetail.trans_type_id' => array(1, 2, 11)
                );
            } else {
                $arrCondition = array(
                    'FileProccessingDetail.company_id' => $companyId,
                    'FileProccessingDetail.branch_id' => $station,
                    'TransactionDetail.teller_name' => $datetime,
                    'TransactionDetail.trans_type_id' => array(1, 2, 11)
                );
            }
        }
        if ($conditions_new == 5) {
            if ($hour != 0) {
                $date = (explode("_", $hour));
                $arrCondition = array(
                    'FileProccessingDetail.company_id' => $companyId,
                    'FileProccessingDetail.branch_id' => $station,
                    'TransactionDetail.teller_name' => $datetime,
                    'TransactionDetail.trans_datetimse >=' => date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00')),
                    'TransactionDetail.trans_datetime <=' => date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59')),
                    "NOT" => array('TransactionDetail.trans_type_id' => array(1, 2, 11))
                );
            } else {
                $arrCondition = array(
                    'FileProccessingDetail.company_id' => $companyId,
                    'FileProccessingDetail.branch_id' => $station,
                    'TransactionDetail.teller_name' => $datetime,
                    "NOT" => array('TransactionDetail.trans_type_id' => array(1, 2, 11))
                );
            }
        }
        $transactionDetailArray = $this->Analytic->getTransactionDetails2($arrCondition);
        $this->Session->write('Report.TransactionCondition_2', $arrCondition);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate2']);
        $transactionsDetails = $this->paginate('TransactionDetail');
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.Transaction', $this->request->data['Filter']);
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $this->set(compact('transactionsDetails', 'temp_station', 'temp_companydata'));
    }
    /**
     * admin side No. of error report
     * @param type $all
     */
    function errors($all = '')
    {

        if (!empty($all)) {
            $this->Session->delete('Report.CompanyId');
            $this->Session->delete('Report.Errors');
        }
        if (empty($this->request->data['Analytic']['company_id']) && $this->Session->check('Report.CompanyId')) {
            $this->request->data['Analytic']['company_id'] = $this->Session->read('Report.CompanyId');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }

        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * Get filter and set the conditions and the x-axis dates as well as the tickinterval
         */
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->__getConditions('Errors', $this->request->data['Filter'], 'ErrorDetail');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (!empty($companyId)) {
            $conditions['FileProccessingDetail.company_id'] = $companyId;
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $errorReportArr = $this->Analytic->getErrorReport($conditions);
        $this->loadModel('ErrorDetail');
        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $this->AutoPaginate->setPaginate($errorReportArr['paginate']);
        $this->Session->write('Report.ErrorReportCondition', $conditions);
        $pieTitle = __('Error V/s Error Type');
        $pieClientTitle = __('Errors V/s Clients');
        $pieErrorTitle = __('Error V/s Error Type');
        $pieName = __('Errors');
        $errors = $this->paginate('ErrorDetail');
        /**
         * error pie chart
         * errors vs types
         */
        $errorPie = $errorTypes = array();
        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $errorPie = $this->ErrorDetail->find('list', array(
            'conditions' => $conditions,
            'fields' => array('error_type_id', 'error_count'),
            'order' => 'error_type_id ASC',
            'group' => 'error_type_id',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $errorTypes = ClassRegistry::init('ErrorType')->find('list', array('fields' => 'id, error_text'));
        $errorPie = $this->Analytic->getPieChartData($errorTypes, $errorPie);
        $errorPie = json_encode($errorPie);
        /**
         * errors vs clients
         */
        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $errorClientPie = $this->ErrorDetail->find('list', array(
            'conditions' => $conditions,
            'fields' => array('FileProccessingDetail.company_id', 'error_count'),
            'group' => 'FileProccessingDetail.company_id',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $errorClients = $companies;
        $errorClientPie = $this->Analytic->getPieChartData($errorClients, $errorClientPie);
        $errorClientPie = json_encode($errorClientPie);
        /**
         * line chart
         */
        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $chartData = $this->ErrorDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array('error_count', 'FileProccessingDetail.file_date'),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $this->set(compact('errorClientPie', 'pieClientTitle', 'pieTitle', 'pieName', 'tickInterval', 'errors', 'errorTypes', 'errorPie'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['ErrorDetail']['error_count'] = $value['ErrorDetail']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['ErrorDetail']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.Errors', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CompanyId', $this->request->data['Analytic']['company_id']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $this->set(compact('companyDetail'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/error_details')->body();
            echo json_encode(array('data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'pieChartData' => $errorPie, 'errorClientPie' => $errorClientPie, 'pieClientTitle' => $pieClientTitle, 'pieTitle' => $pieTitle, 'pieName' => $pieName, 'tickInterval' => $tickInterval));
            exit;
        }
    }
    /**
     * Company side bill history report
     * @param type $all
     */
    function bill_history($all = '')
    {
        $filSesName = 'BillHistoryFilter';
        $repSesName = 'BillHistoryReport';
        $billTypes = ClassRegistry::init('BillType')->find('list', array('fields' => 'id, bill_type'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.' . $filSesName);
            $this->Session->delete('Report.' . $repSesName);
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.' . $filSesName)) {
            $this->request->data['Analytic'] = $this->Session->read('Report.' . $filSesName);
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->__getConditions($repSesName, $this->request->data['Filter'], 'BillHistory');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate,
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $billHistoryArr = $this->Analytic->getBillHistory($conditions);
        $this->loadModel('BillHistory');
        $this->AutoPaginate->setPaginate($billHistoryArr['paginate']);
        $bills = $this->paginate('BillHistory');
        /**
         * line chart
         */
        $this->BillHistory->virtualFields['error_count'] = 'count(BillHistory.id)';
        $this->BillHistory->virtualFields['trans_datetime'] = 'DATE_FORMAT(trans_datetime,"%Y-%m-%d")';
        $chartData = $this->BillHistory->find('all', $billHistoryArr['chart']);
        $this->Session->write('Report.BillHistoryReportCondition', $conditions);
        $this->set(compact('tickInterval', 'bills', 'billTypes'));
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.BillHistory');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['trans_datetime']));
            if (isset($temp[$date])) {
                $value['error_count'] = $value['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.' . $repSesName, $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.' . $filSesName, $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Bill History');
        $lineChartTitle = __('Bill History');
        $lineChartxAxisTitle = __('Bill History Date');
        $lineChartyAxisTitle = __('No. of bill history');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/bill_history')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }


    /**
     * Company side Bill count report
     * @param type $all
     */
    function bill_count($all = '')
    {
        $filSesName = 'BillCountFilter';
        $repSesName = 'BillCountReport';
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.' . $filSesName);
            $this->Session->delete('Report.' . $repSesName);
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));

        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'BillCount');

        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate =  $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_parent_id;// getCompanyId();
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
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_parent_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        $filter_criteria = array();
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
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
                $conditions['FileProccessingDetail.branch_id'] = $branchLists;
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }
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
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('BillCount');
        $billCountArr = $this->Analytic->getBillCount($conditions);
        $this->AutoPaginate->setPaginate($billCountArr['paginate']);
        $bills = $this->paginate('BillCount');

         /**
         * line chart
         */
        $this->BillCount->virtualFields['error_count'] = 'count(BillCount.id)';
        $chartData = $this->BillCount->find('all', $billCountArr['chart']);
        $this->Session->write('Report.BillCountReportCondition', $conditions);
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['BillCount']['error_count'] = $value['BillCount']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['BillCount']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();

        $lineChartName = __('Bill Count');
        $lineChartTitle = __('Bill Count');
        $lineChartxAxisTitle = __('Bill Count Date');
        $lineChartyAxisTitle = __('No. of bill counts');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/bill_count')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
        $this->set(compact('companies', 'branches', 'sessionData', 'regiones', 'stations', 'temp_station', 'tickInterval', 'bills', 'temp', 'xAxisDates'));

    }
    /*
     * Bill Adjustment report
     */

    function old_bill_adjustment($all = '')
    {

        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        if (!empty($all)) {
            $this->Session->delete('Report.BillAdjustmentFilter');
            $this->Session->delete('Report.BillAdjustmentReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.BillAdjustmentFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.BillAdjustmentFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }


        $all_condition = $this->Session->read('Report.BillAdjustmentFilter');
        if (($this->request->is('ajax')) or (isset($this->request->params['named']['sort'])) or (isset($this->request->params['named']['Paginate']))  or (isset($this->request->params['named']['page']))) {

            if (isset($all_condition['company_id'])) {
                $companyId = $all_condition['company_id'];
            }
            if (isset($all_condition['regiones'])) {
                $this->request->data['Analytic']['regiones'] = $all_condition['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($all_condition['regiones']);
            }
            if (isset($all_condition['branch_id']) && (!empty($all_condition['branch_id'])) && empty($this->request->data['Analytic']['branch_id'])) {
                $this->request->data['Analytic']['branch_id'] = $all_condition['branch_id'];
                $conditions['FileProccessingDetail.branch_id'] = $all_condition['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($conditions['branch_id']);
            }
            if (isset($all_condition['station'])) {
                $this->request->data['Analytic']['station'] = $all_condition['station'];
                $conditions['FileProccessingDetail.station'] = $all_condition['station'];
                $conditions['BillsActivityReport.station'] = $all_condition['station'];
            }
        }

        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->__getConditions('BillAdjustmentReport', $this->request->data['Filter'], 'BillAdjustment');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        $this->loadModel('Region');
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */


        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            $this->set(compact('companies'));
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

                $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
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
        }
        $billAdjustmentArr = $this->Analytic->getBillAdjustment($conditions);
        $this->loadModel('BillAdjustment');
        $this->AutoPaginate->setPaginate($billAdjustmentArr['paginate']);
        $bills = $this->paginate('BillAdjustment');

        /**
         * line chart
         */
        //print_r($this->AutoPaginate);
        // var_dump($bills);
        $this->BillAdjustment->virtualFields['error_count'] = 'count(BillAdjustment.id)';
        $chartData = $this->BillAdjustment->find('all', $billAdjustmentArr['chart']);
        $this->Session->write('Report.BillAdjustmentReportCondition', $conditions);
        $this->set(compact('tickInterval', 'bills'));
        $temp = array();
        // var_dump($chartData);
        //        $chartData = Hash::extract($chartData, '{n}.BillAdjustment');
        //        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['BillAdjustment']['error_count'] = $value['BillAdjustment']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['BillAdjustment']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.BillAdjustmentReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.BillAdjustmentFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Bill Adjustment');
        $lineChartTitle = __('Bill Adjustment');
        $lineChartxAxisTitle = __('Bill Adjustment Date');
        $lineChartyAxisTitle = __('No. of bill adjustments');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/bill_adjustment')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /*
     * new Bill Adjustment report
    */
    function bill_adjustment($all = '')
    {
        // if (!empty($all)) {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.BillAdjustmentReportCondition');
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

        // $all_condition = $this->Session->read('Report.BillAdjustmentReportCondition');
        // if (($this->request->is('ajax')) or (isset($this->request->params['named']['sort'])) or (isset($this->request->params['named']['Paginate']))  or (isset($this->request->params['named']['page'])) or $f == 1) {

        //     if (isset($all_condition['FileProccessingDetail.company_id'])) {
        //         $companyId = $all_condition['FileProccessingDetail.company_id'];
        //     }
        //     if (isset($all_condition['FileProccessingDetail.regiones'])) {
        //         $this->request->data['Analytic']['regiones'] = $all_condition['FileProccessingDetail.regiones'];
        //         $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($all_condition['FileProccessingDetail.regiones']);
        //     }
        //     if (isset($all_condition['FileProccessingDetail.branch_id']) && empty($this->request->data['Analytic']['branch_id'])) {
        //         $this->request->data['Analytic']['branch_id'] = $all_condition['FileProccessingDetail.branch_id'];
        //         $conditions['FileProccessingDetail.branch_id'] = $all_condition['FileProccessingDetail.branch_id'];
        //         $stations = ClassRegistry::init('CompanyBranch')->getStationList($conditions['FileProccessingDetail.branch_id']);
        //     }
        //     if (isset($all_condition['FileProccessingDetail.station'])) {
        //         $this->request->data['Analytic']['station'] = $all_condition['FileProccessingDetail.station'];
        //         $conditions['FileProccessingDetail.station'] = $all_condition['FileProccessingDetail.station'];
        //         $conditions['BillsActivityReport.station'] = $all_condition['FileProccessingDetail.station'];
        //     }

        //     if (isset($all_condition['FileProccessingDetail.date'])) {
        //         $conditions_new['TransactionDetail.trans_datetime >= '] = $all_condition['FileProccessingDetail.date'] . ' 00:00:00';
        //         $conditions_new['TransactionDetail.trans_datetime <= '] = $all_condition['FileProccessingDetail.date'] . ' 23:59:59';
        //         $conditions_new['FileProccessingDetail.file_date = '] = $all_condition['FileProccessingDetail.date'];
        //     }
        // }
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['TransactionDetail.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['TransactionDetail.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
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

        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        // $conditions = $this->__getConditions('Transaction', $this->request->data['Filter'], 'TransactionDetail');
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
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];

        $conditions2['company_id'] = $company_id;//$sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            if (!empty($branches)) {
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
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

            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
        
        }
        $this->set(compact('branches'));
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
            $this->set(compact('filter_criteria'));
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $this->set(compact('stations', 'branches'));
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;//getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $this->set(compact('filter_criteria'));
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
        }
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            $startDate = date('Y-m-d', strtotime($date[0]));
            $endDate = date('Y-m-d', strtotime($date[1]));
            $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
            $this->set(compact('filter_criteria'));
        }

        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

            // $conditions['FileProccessingDetail.branch_id'] = $branches;
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
        $conditions_new['TransactionDetail.trans_type_id'] = array(4, 5);
        $conditions['TransactionDetail.trans_type_id'] = array(4, 5);
        $transactionArray = $this->Analytic->getTransactionDetails($conditions);
        $this->loadModel('TransactionDetail');
        $this->Session->write('Report.BilladjustReportCondition', $conditions);
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';
        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)';
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
        $this->Session->write('Report.BillAdjustmentReportCondition', $conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $pieTitle = __('Inventory Management Transaction Type');
        $pieCatTitle = __('Transaction V/s Transaction Category');
        $pieClientTitle = __('Transactions by Branch');
        $pieName = __('Transactions');
        // $transactions = $this->paginate('TransactionDetail');
        $this->set('transactions', $this->paginate('TransactionDetail'));
        if (!empty($this->request->data['Analytic']['date'])) {

            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions_new = $conditions;
            $conditions_new['TransactionDetail.trans_datetime >= '] = $this->request->data['Analytic']['date'] . ' 00:00:00';
            $conditions_new['TransactionDetail.trans_datetime <= '] = $this->request->data['Analytic']['date'] . ' 23:59:59';
            $conditions_new['FileProccessingDetail.file_date >= '] = $this->request->data['Analytic']['date'];
            $conditions_new['FileProccessingDetail.file_date <= '] = $this->request->data['Analytic']['date'];
            $hourly_data2 = array();
            $hourly_data3 = array();

            $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions_new);
            $hourly_data = $this->TransactionDetail->find('list', $transactionDetailArray2['paginate2']);
            foreach ($hourly_data as $key => $value) {
                $hourly_data2[$key][0] = date("H", strtotime($key));
                $hourly_data2[$key][1] = $value;
            }
            foreach ($hourly_data2 as $key => $value) {
                array_push($hourly_data3, $value);
            }
            if (empty($hourly_data3)) {
                array_push($hourly_data3, [0, 0]);
            }
            $hourly_report_data = json_encode($hourly_data3, JSON_NUMERIC_CHECK);

            if ($hourly_report_data == null) {
                $hourly_report_data = json_encode([[0, 0]], JSON_NUMERIC_CHECK);
            }
        } else {
            $hourly_report_data = json_encode(1, JSON_NUMERIC_CHECK);
        }
        $this->set(compact('hourly_report_data'));
        /**
         * transaction pie chart
         * transactions vs types
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionPieChart']);
        $transactionTypes = ClassRegistry::init('TransactionType')->find('list', array('fields' => 'id, text'));
        $transactionPie = $this->Analytic->getPieChartData($transactionTypes, $transactionPie);
        $transactionPie = json_encode($transactionPie);
        /**
         * transactions vs category
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        //        debug($conditions);

        $transactionCatPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionCategoryChart']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $transactionCatPie = $this->Analytic->getPieChartData($transactionCategories, $transactionCatPie, true);
        $transactionCatPie = json_encode($transactionCatPie);
        /**
         * transactions vs branch
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        // $conditions['FileProccessingDetail.branch_id'] = array_keys($branches);
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);

            //  $conditions['FileProccessingDetail.branch_id'] = $branches;

        }

        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
        $transactionClientPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionClientPie']);
        if (empty($branches)) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($conditions['FileProccessingDetail.company_id']);
        }


        $transactionClients = $branches;
        $transactionClientPie = $this->Analytic->getPieChartData($transactionClients, $transactionClientPie);
        /* echo "<pre>";
         print_r($transactionClients);
            die();*/
        $transactionClientPie = json_encode($transactionClientPie);

        /**
         * line chart
         */
        $conditions2 = $conditions;
        unset($conditions2['FileProccessingDetail.file_date >= ']);
        unset($conditions2['FileProccessingDetail.file_date <= ']);
        $conditions2['FileProccessingDetail.file_date'] = date('Y-m-d', strtotime("-1 days"));
        // echo '<pre><b></b><br>';
        // print_r($conditions2);echo '<br>';exit;
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions2);
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $chartData = $this->TransactionDetail->find('all', $transactionDetailArray['chart']['chartData']);
        $chartData2 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData2']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $this->set(compact('transactionCatPie', 'transactionClientPie', 'pieClientTitle', 'pieCatTitle', 'pieTitle', 'pieName', 'tickInterval', 'transactions', 'temp_station', 'transactionCategories', 'transactionTypes', 'transactionPie'));
        $temp = array();
        $temp2 = array();
        $tempCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['transaction_count']);
            $tempCount++;
        }
        $tempCount = 0;
        // foreach ($chartData2 as $key => $value) {
        //     $date = date('Y-m-d', strtotime($value['TransactionDetail']['trans_datetime']));
        //     ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
        //     if (isset($temp2[$date])) {
        //     } else {
        //         $temp2[$date] = array((strtotime($value['TransactionDetail']['trans_datetime']) * 1000), $value['TransactionDetail']['trans_datetime'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        //     }
        //     if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[0]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[1])) {
        //         $temp2[$date][2]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[1]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[2])) {
        //         $temp2[$date][3]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[2]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[3])) {
        //         $temp2[$date][4]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[3]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[4])) {
        //         $temp2[$date][5]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[4]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[5])) {
        //         $temp2[$date][6]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[5]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[6])) {
        //         $temp2[$date][7]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[6]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[7])) {
        //         $temp2[$date][8]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[7]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[8])) {
        //         $temp2[$date][9]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[8]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[9])) {
        //         $temp2[$date][10]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[9]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[10])) {
        //         $temp2[$date][11]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[10]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[11])) {
        //         $temp2[$date][12]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[11]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[12])) {
        //         $temp2[$date][13]++;
        //     } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[12])) {
        //         $temp2[$date][13]++;
        //     }
        // }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $this->set(compact('temp_hr'));
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.Transaction', $this->request->data['Filter']);
        }
        if (isCompany()) {
            $company_id = getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CompanyId', $company_id);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $company_id)));
        }
        $this->set(compact('companyDetail'));
        /**
         * 2. Line chart
         * transaction_detail chart
         * credit transactions / 
         * debit transaction /
         * total sum
         */
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {

            $conditions['FileProccessingDetail.company_id'] = $company_id;// getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $this->Session->write('Report.TransactionCondition', $conditions);
        unset($conditions['FileProccessingDetail.date']);
        unset($conditions['FileProccessingDetail.regiones']);
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        $chartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'trans_datetime', 'transaction_count', 'total_amount_withdrawal',
                'total_cash_deposite', 'FileProccessingDetail.file_date'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['total_amount_withdrawal'] = $value['TransactionDetail']['total_amount_withdrawal'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_amount_withdrawal']);
            if (isset($temp1[$date])) {
                $value['TransactionDetail']['total_cash_deposite'] = $value['TransactionDetail']['total_cash_deposite'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_cash_deposite']);
            if (isset($temp2[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), ($value['TransactionDetail']['transaction_count']));
        }
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
            )
        );
        $temp1 = json_encode($temp1);
        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $temp = $oldTemp;
        $this->set(compact('temp', 'temp1', 'xAxisDates1'));
        /**
         * 3. Line chart
         * This chart show balance of each denom against time. 
         */
        //            ------------------------- start -------------------------
        $balanceConditions = array();
        $yesterdayDate = $this->request->data['Analytic']['date']; //date('Y-m-d', strtotime("-1 days"));
        $balanceConditions['date(trans_datetime)'] = $yesterdayDate;
        if (isset($conditions['FileProccessingDetail.company_id'])) {
            $balanceConditions['FileProccessingDetail.company_id'] = $conditions['FileProccessingDetail.company_id'];
        }
        if (isset($conditions['FileProccessingDetail.branch_id'])) {
            $balanceConditions['FileProccessingDetail.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }
        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $balanceConditions,
            'fields' => array(
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));

        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            //                $date = date('Y-m-d', strtotime($value['trans_datetime']));
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime($yesterdayDate));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;
        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('sentTemp', 'xAxisDatesTime'));

        //            -------------------------end------------------------- 
        $aa = $this->request->data['Analytic']['date'];
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;

        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/transaction_details')->body();
            $options = array(
                'id' => '#container',
                'name' => __('Transactions'),
                'title' => __('Transactions'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction'),
            );
            echo json_encode(array(
                'options' => $options,
                'data' => $temp1,
                'xAxisDates' => $xAxisDates1,
                'htmlData' => $transactionData,
                'pieChartData' => $transactionPie,
                'transactionCatPie' => $transactionCatPie,
                'pieCatTitle' => $pieCatTitle,
                'transactionClientPie' => $transactionClientPie,
                'pieClientTitle' => $pieClientTitle,
                'pieTitle' => $pieTitle,
                'pieName' => $pieName,
                'tickInterval' => $tickInterval,
                'transactionDetails' => $temp1,
                'hourData' => $temp1
            ));
            exit;
        }
    }
    function heat_map($all = '')
    {
        $filSesName = 'HeatMapFilter';
        $repSesName = 'HeatMapReport';
        $sessionData = getMySessionData();
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
        $this->set(compact('sessionData'));
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
        }
        if (isset($this->request['named']['sort'])) {
            $order_by = "ORDER by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
            // $order_by = "ORDER by id DESC";
        } else {
            // $order_by = '';
            $order_by = "ORDER by BillCount.id DESC";
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.' . $filSesName);
            $this->Session->delete('Report.' . $repSesName);
            $this->Session->delete('Report.HeatMapReportCondition');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.' . $filSesName)) {
            $this->request->data['Analytic'] = $this->Session->read('Report.' . $filSesName);
        } else {
            $analysis = 1;
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
            $_SESSION['company_id'] = $companyId;
        } else if (isset($_SESSION['company_id'])) {
            $this->request->data['Analytic']['company_id'] = $_SESSION['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $all_condition = [];//$this->Session->read('Report.HeatMapReportCondition');
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
        }


        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        // $conditions = $this->__getConditions($repSesName, $this->request->data['Filter'], 'HeatMap');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'HeatMap');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate =  $conditions['end_date'];
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            $this->set(compact('filter_criteria'));
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($conditions['start_date'])); //  
        $endDate = date('Y-m-d 23:59:59', strtotime($conditions['end_date']));
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        // $conditions = array(
        //     'FileProccessingDetail.file_date >= ' => $startDate,
        //     'FileProccessingDetail.file_date <= ' => $endDate
        // );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
            }
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_parent_id;
        }
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
        $this->loadModel('Region');
        $conditions2['company_id'] = $sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        $filter_criteria = array();
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
            if ($analysis == 1 && empty($this->request->data['Analytic']['regiones']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['regiones']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $_SESSION['regiones'] = $this->request->data['Analytic']['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    // $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            } 
            if ($analysis == 1 && empty($this->request->data['Analytic']['branch_id']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $_SESSION['branch_id'] = $this->request->data['Analytic']['branch_id'];
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
            }
            if ($analysis == 1 && empty($this->request->data['Analytic']['station']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['station']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $_SESSION['station'] = $this->request->data['Analytic']['station'];
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            } 
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
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
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('BillCount');
        $billCountArr = $this->Analytic->getHeatMap($conditions, $regionsId);
        $this->AutoPaginate->setPaginate($billCountArr['paginate']);
        $bills = $this->paginate('BillCount');
        $this->set(compact('bills'));

        /**
         * line chart
         */
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . " IN(" . implode(',', $value) . ")";
            } else {
                if (strpos($key, '<=') !== false or strpos($key, '>=') !== false) {
                    $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                } else {
                    $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                }
            }
        }
        $Paginate = 20;
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
            $_SESSION['Paginate'] = $this->request->params['named']['Paginate'];
        } else if (isset($_SESSION['Paginate'])) {
            $Paginate = $_SESSION['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        $sql3 = "SELECT   
		if(`BillCount`.`_1_actual_count`>DenominationHeatMap.1_upper ,'6', if(DenominationHeatMap.1_lower>`BillCount`.`_1_actual_count` ,'3', '2')) AS 1_colour , 
        if(`BillCount`.`_2_actual_count`>DenominationHeatMap.2_upper ,'6', if(DenominationHeatMap.2_lower>`BillCount`.`_2_actual_count` ,'3', '2')) AS 2_colour ,  
        if(`BillCount`.`_5_actual_count`>DenominationHeatMap.5_upper ,'6', if(DenominationHeatMap.5_lower>`BillCount`.`_5_actual_count` ,'3', '2')) AS 5_colour ,  
        if(`BillCount`.`_10_actual_count`>DenominationHeatMap.10_upper ,'6', if(DenominationHeatMap.10_lower>`BillCount`.`_10_actual_count` ,'3', '2')) AS 10_colour ,  
        if(`BillCount`.`_20_actual_count`>DenominationHeatMap.20_upper ,'6', if(DenominationHeatMap.20_lower>`BillCount`.`_20_actual_count` ,'3', '2')) AS 20_colour ,   
        if(`BillCount`.`_50_actual_count`>DenominationHeatMap.50_upper ,'6', if(DenominationHeatMap.50_lower>`BillCount`.`_50_actual_count` ,'3', '2')) AS 50_colour ,   
        if(`BillCount`.`_100_actual_count`>DenominationHeatMap.100_upper ,'6', if(DenominationHeatMap.100_lower>`BillCount`.`_100_actual_count` ,'3', '2')) AS 100_colour ,  
        TransactionHeatMaps.trans_lower , TransactionHeatMaps.trans_upper , CompanyBranches.name  ,  CompanyBranches.lat  ,  CompanyBranches.lon  , `FileProccessingDetail`.`branch_id` FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON ((`DenominationHeatMap`.`machine_id` = 0 OR `DenominationHeatMap`.`machine_id` = `FileProccessingDetail`.`station`)  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON (`TransactionHeatMaps`.`machine_id` = `FileProccessingDetail`.`station` AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`)";
        //TransactionHeatMaps.trans_lower , TransactionHeatMaps.trans_upper , CompanyBranches.name  ,  CompanyBranches.lat  ,  CompanyBranches.lon  , `FileProccessingDetail`.`branch_id` FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON (`DenominationHeatMap`.`machine_id` = `FileProccessingDetail`.`station`  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON (`TransactionHeatMaps`.`machine_id` = `FileProccessingDetail`.`station` AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`) ";
        $sql3 = $sql3 . " WHERE " . $conditions_new . " order by branch_id";
        $result_graph = $this->BillCount->query($sql3);
        $heatmap_data = array();
        $arr_tmp = array();
        foreach ($result_graph as $key => $value) {
            $priority = 0;
            if (isset($heatmap_data[$value['FileProccessingDetail']['branch_id']])) {
                if ($priority < $value[0]['1_colour']) {
                    $priority = $value[0]['1_colour'];
                }
                if ($priority < $value[0]['2_colour']) {
                    $priority = $value[0]['2_colour'];
                }
                if ($priority < $value[0]['5_colour']) {
                    $priority = $value[0]['5_colour'];
                }
                if ($priority < $value[0]['10_colour']) {
                    $priority = $value[0]['10_colour'];
                }
                if ($priority < $value[0]['20_colour']) {
                    $priority = $value[0]['20_colour'];
                }
                if ($priority < $value[0]['50_colour']) {
                    $priority = $value[0]['50_colour'];
                }
                if ($priority < $value[0]['100_colour']) {
                    $priority = $value[0]['100_colour'];
                }
                if ($heatmap_data[$value['FileProccessingDetail']['branch_id']][2] < $priority) {
                    $heatmap_data[$value['FileProccessingDetail']['branch_id']][2] = $priority;
                }
            } else {
                if ($priority < $value[0]['1_colour']) {
                    $priority = $value[0]['1_colour'];
                }
                if ($priority < $value[0]['2_colour']) {
                    $priority = $value[0]['2_colour'];
                }
                if ($priority < $value[0]['5_colour']) {
                    $priority = $value[0]['5_colour'];
                }
                if ($priority < $value[0]['10_colour']) {
                    $priority = $value[0]['10_colour'];
                }
                if ($priority < $value[0]['20_colour']) {
                    $priority = $value[0]['20_colour'];
                }
                if ($priority < $value[0]['50_colour']) {
                    $priority = $value[0]['50_colour'];
                }
                if ($priority < $value[0]['100_colour']) {
                    $priority = $value[0]['100_colour'];
                }
                $arr_tmp[0] = $value['CompanyBranches']['lat'];
                $arr_tmp[1] = $value['CompanyBranches']['lon'];
                $arr_tmp[2] = $priority;
                $heatmap_data[$value['FileProccessingDetail']['branch_id']] = $arr_tmp;
                unset($arr_tmp);
            }
        }
        $heatmap_data = json_encode($heatmap_data, JSON_NUMERIC_CHECK);
        $this->set(compact('heatmap_data'));
        /////////////////////////////////////end new code 
        $sql = "SELECT   if(DenominationHeatMap.1_lower>`BillCount`.`_1_actual_count` ,'#F5BC47', if(`BillCount`.`_1_actual_count`>DenominationHeatMap.1_upper ,'#E43F4A', '#BFBFBF')) AS 1_colour ,  `BillCount`.`_1_actual_count`,
        if(DenominationHeatMap.2_lower>`BillCount`.`_2_actual_count` ,'#F5BC47', if(`BillCount`.`_2_actual_count`>DenominationHeatMap.2_upper ,'#E43F4A', '#BFBFBF')) AS 2_colour ,  `BillCount`.`_2_actual_count`,
        if(DenominationHeatMap.5_lower>`BillCount`.`_5_actual_count` ,'#F5BC47', if(`BillCount`.`_5_actual_count`>DenominationHeatMap.5_upper ,'#E43F4A', '#BFBFBF')) AS 5_colour ,  `BillCount`.`_5_actual_count`,
        if(DenominationHeatMap.10_lower>`BillCount`.`_10_actual_count` ,'#F5BC47', if(`BillCount`.`_10_actual_count`>DenominationHeatMap.10_upper ,'#E43F4A', '#BFBFBF')) AS 10_colour ,  `BillCount`.`_10_actual_count`,
        if(DenominationHeatMap.20_lower>`BillCount`.`_20_actual_count` ,'#F5BC47', if(`BillCount`.`_20_actual_count`>DenominationHeatMap.20_upper ,'#E43F4A', '#BFBFBF')) AS 20_colour ,  `BillCount`.`_20_actual_count`,
        if(DenominationHeatMap.50_lower>`BillCount`.`_50_actual_count` ,'#F5BC47', if(`BillCount`.`_50_actual_count`>DenominationHeatMap.50_upper ,'#E43F4A', '#BFBFBF')) AS 50_colour ,  `BillCount`.`_50_actual_count`,
        if(DenominationHeatMap.100_lower>`BillCount`.`_100_actual_count` ,'#F5BC47', if(`BillCount`.`_100_actual_count`>DenominationHeatMap.100_upper ,'#E43F4A', '#BFBFBF')) AS 100_colour ,  `BillCount`.`_100_actual_count`,
        DenominationHeatMap.1_lower , DenominationHeatMap.2_lower ,DenominationHeatMap.5_lower ,DenominationHeatMap.10_lower , DenominationHeatMap.20_lower ,DenominationHeatMap.50_lower , DenominationHeatMap.100_lower ,DenominationHeatMap.1_upper,DenominationHeatMap.2_upper,DenominationHeatMap.5_upper,DenominationHeatMap.10_upper,DenominationHeatMap.20_upper,DenominationHeatMap.50_upper,DenominationHeatMap.100_upper, TransactionHeatMaps.trans_lower , TransactionHeatMaps.trans_upper , `FileProccessingDetail`.`station`  , CompanyBranches.name , Company.first_name , `BillCount`.`count_date`  ,  `FileProccessingDetail`.`company_id`, `FileProccessingDetail`.`branch_id`,  `FileProccessingDetail`.`file_date`, `Manager`.`id`, `Manager`.`name`, `Manager`.`created` FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON (`DenominationHeatMap`.`machine_id` =CASE WHEN DenominationHeatMap.machine_id = FileProccessingDetail.station THEN FileProccessingDetail.station ELSE     if((select count(id) as match_machine from transaction_heat_maps a where a.machine_id = FileProccessingDetail.station and a.branch_id =  FileProccessingDetail.branch_id)>0 ,'9999', '0')                                                        
  END  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON (`TransactionHeatMaps`.`machine_id` = 
CASE WHEN TransactionHeatMaps.machine_id = FileProccessingDetail.station THEN FileProccessingDetail.station ELSE     if((select count(id) as match_machine from transaction_heat_maps a where a.machine_id = FileProccessingDetail.station and a.branch_id =  FileProccessingDetail.branch_id)>0 ,'99999', '0')                                                        
  END AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`)";
        $sql2 = $sql . " WHERE " . $conditions_new . "  " . $order_by . " " . $limit;
        $result_graph = $this->BillCount->query($sql2);
        $this->BillCount->virtualFields['error_count'] = 'count(BillCount.id)';
        $chartData = $this->BillCount->find('all', $billCountArr['chart']);
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $conditions['FileProccessingDetail.regiones'] = $this->request->data['Analytic']['regiones'];
        }
        $this->Session->write('Report.HeatMapReportCondition', $conditions);
        unset($conditions['FileProccessingDetail.regiones']);
        $this->set(compact('tickInterval', 'result_graph', 'temp_station'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['BillCount']['error_count'] = $value['BillCount']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['BillCount']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.' . $repSesName, $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.HeatMapReportFilter', $this->request->data['Analytic']);
            if (!isCompany()) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $aa = (isset($this->request->data['Analytic']['date'])) ? $this->request->data['Analytic']['date'] : date('Y-m-d', strtotime(date("Y-m-d") . ' -1 day'));
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;
        $lineChartName = __('Bill Count');
        $lineChartTitle = __('Bill Count');
        $lineChartxAxisTitle = __('Bill Count Date');
        $lineChartyAxisTitle = __('No. of bill counts');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/heat_map')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }
    function transaction_map($all = '')
    {
        $filSesName = 'TransactionMapReport';
        $repSesName = 'TransactionMapReport';
        $sessionData = getMySessionData();
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];

        if (!empty($all)) {
            $this->Session->delete('Report.' . $filSesName);
            $this->Session->delete('Report.' . $repSesName);
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.' . $filSesName)) {
            $this->request->data['Analytic'] = $this->Session->read('Report.' . $filSesName);
            $analysis = 0;
        } else {
            $analysis = 1;
        }
        if (isset($this->request['named']['sort'])) {
            $order_by = "ORder by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'BillCount');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
        }
        $startDate = date('Y-m-d 00:00:00', strtotime($conditions['start_date'])); // $conditions['start_date'];
        $endDate = date('Y-m-d 23:59:59', strtotime($conditions['end_date']));
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            if (!empty($sessionData)) {
            }
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_parent_id;//getCompanyId();
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
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_parent_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }

        $filter_criteria = array();
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
            if ($analysis == 1 && empty($this->request->data['Analytic']['regiones']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['regiones']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $_SESSION['regiones'] = $this->request->data['Analytic']['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    // $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            } 
            if ($analysis == 1 && empty($this->request->data['Analytic']['branch_id']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $_SESSION['branch_id'] = $this->request->data['Analytic']['branch_id'];
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
            }
            if ($analysis == 1 && empty($this->request->data['Analytic']['station']) && (!isset($this->request->params['named']['Paginate'])) && (!isset($this->request->params['named']['page']))) {
                unset($_SESSION['station']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $_SESSION['station'] = $this->request->data['Analytic']['station'];
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            } 
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
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
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $billCountArr = $this->Analytic->getHeatMap($conditions, $regionsId);
        $this->loadModel('BillCount');
        $this->AutoPaginate->setPaginate($billCountArr['paginate']);
        $bills = $this->paginate('BillCount');
        /**
         * line chart
         */

        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . " IN(" . implode(',', $value) . ")";
            } else {
                if (strpos($key, '<=') !== false or strpos($key, '>=') !== false) {
                    $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                } else {
                    $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                }
            }
        }
        $Paginate = 20;
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
            $_SESSION['Paginate'] = $this->request->params['named']['Paginate'];
        } else if (isset($_SESSION['Paginate'])) {
            $Paginate = $_SESSION['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        //////////////////////////////////////////start new code 
        $sql3 = "SELECT `BillCount`.`_1_actual_count`,   DenominationHeatMap.1_upper,DenominationHeatMap.1_lower,
		if(`BillCount`.`_1_actual_count`>DenominationHeatMap.1_upper ,'6', if(DenominationHeatMap.1_lower>`BillCount`.`_1_actual_count` ,'3', '1')) AS 1_colour , 
        ((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100)) AS total_transaction ,
        if(TransactionHeatMaps.trans_lower>((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100)) ,'3', if(((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100))>TransactionHeatMaps.trans_upper ,'6', '2')) AS total_colour , 
        if(`BillCount`.`_2_actual_count`>DenominationHeatMap.2_upper ,'6', if(DenominationHeatMap.2_lower>`BillCount`.`_2_actual_count` ,'3', '1')) AS 2_colour ,  
        if(`BillCount`.`_5_actual_count`>DenominationHeatMap.5_upper ,'6', if(DenominationHeatMap.5_lower>`BillCount`.`_5_actual_count` ,'3', '1')) AS 5_colour ,  
        if(`BillCount`.`_10_actual_count`>DenominationHeatMap.10_upper ,'6', if(DenominationHeatMap.10_lower>`BillCount`.`_10_actual_count` ,'3', '1')) AS 10_colour ,  
        if(`BillCount`.`_20_actual_count`>DenominationHeatMap.20_upper ,'6', if(DenominationHeatMap.20_lower>`BillCount`.`_20_actual_count` ,'3', '1')) AS 20_colour ,   
        if(`BillCount`.`_50_actual_count`>DenominationHeatMap.50_upper ,'6', if(DenominationHeatMap.50_lower>`BillCount`.`_50_actual_count` ,'3', '1')) AS 50_colour ,   
        if(`BillCount`.`_100_actual_count`>DenominationHeatMap.100_upper ,'6', if(DenominationHeatMap.100_lower>`BillCount`.`_100_actual_count` ,'3', '1')) AS 100_colour ,  
        TransactionHeatMaps.trans_lower , TransactionHeatMaps.trans_upper ,  CompanyBranches.name  ,  CompanyBranches.lat  ,  CompanyBranches.lon  , `FileProccessingDetail`.`branch_id` FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON ((`DenominationHeatMap`.`machine_id` = 0 OR `DenominationHeatMap`.`machine_id` = `FileProccessingDetail`.`station`)  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON ((`TransactionHeatMaps`.`machine_id` =0 OR `TransactionHeatMaps`.`machine_id` = `FileProccessingDetail`.`station`) AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`) ";
        $sql3 = $sql3 . " WHERE " . $conditions_new . " order by branch_id";
        $result_graph = $this->BillCount->query($sql3);

        $heatmap_data = array();
        $arr_tmp = array();
        foreach ($result_graph as $key => $value) {
            $priority = 0;
            if (isset($heatmap_data[$value['FileProccessingDetail']['branch_id']])) {
                if ($priority < $value[0]['total_colour']) {
                    $priority = $value[0]['total_colour'];
                }
                if ($heatmap_data[$value['FileProccessingDetail']['branch_id']][2] < $priority) {
                    $heatmap_data[$value['FileProccessingDetail']['branch_id']][2] = $priority;
                }
            } else {
                if ($priority < $value[0]['total_colour']) {
                    $priority = $value[0]['total_colour'];
                }
                $arr_tmp[0] = $value['CompanyBranches']['lat'];
                $arr_tmp[1] = $value['CompanyBranches']['lon'];
                $arr_tmp[2] = $priority;
                $heatmap_data[$value['FileProccessingDetail']['branch_id']] = $arr_tmp;
                unset($arr_tmp);
            }
        }

        $heatmap_data = json_encode($heatmap_data, JSON_NUMERIC_CHECK);
        $this->set(compact('heatmap_data'));
        $sql = "SELECT  ((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100)) AS total_transaction ,
        if(TransactionHeatMaps.trans_lower>((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100)) ,'#F5BC47', if(((`BillCount`.`_1_actual_count`)+(`BillCount`.`_2_actual_count`*2)+(`BillCount`.`_5_actual_count`*5)+(`BillCount`.`_10_actual_count`*10)+(`BillCount`.`_20_actual_count`*20)+(`BillCount`.`_50_actual_count`*50)+(`BillCount`.`_100_actual_count`*100))>TransactionHeatMaps.trans_upper ,'#E43F4A', '#BFBFBF')) AS total_colour , 
        TransactionHeatMaps.trans_lower , TransactionHeatMaps.trans_upper , `FileProccessingDetail`.`station`  , CompanyBranches.name , Company.first_name ,
        `BillCount`.`count_date`  ,  `FileProccessingDetail`.`company_id`, `FileProccessingDetail`.`branch_id`,  `FileProccessingDetail`.`file_date`, `Manager`.`id`, `Manager`.`name`, `Manager`.`created` FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON (`DenominationHeatMap`.`machine_id` = `FileProccessingDetail`.`station`  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON ( `TransactionHeatMaps`.`machine_id` = 
CASE WHEN TransactionHeatMaps.machine_id = FileProccessingDetail.station THEN FileProccessingDetail.station ELSE     if((select count(id) as match_machine from transaction_heat_maps a where a.machine_id = FileProccessingDetail.station and a.branch_id =  FileProccessingDetail.branch_id)>0 ,'99999', '0')                                                        
  END

     AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`)";
        $sql2 = $sql . " WHERE " . $conditions_new . " " . $order_by . " " . $limit;
        $result_graph = $this->BillCount->query($sql2);
        $sql_count = "SELECT  count(BillCount.id) FROM `bills_count` AS `BillCount` LEFT JOIN `file_processing_detail` AS `FileProccessingDetail` ON (`BillCount`.`file_processing_detail_id` = `FileProccessingDetail`.`id`) LEFT JOIN `manager_info` AS `Manager` ON (`BillCount`.`manager_id` = `Manager`.`id`)  LEFT JOIN `company_branches` AS `CompanyBranches` ON (`CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`)  LEFT JOIN `users` AS `Company` ON (`Company`.`id` = `FileProccessingDetail`.`company_id`) LEFT JOIN `denomination_heat_maps` AS `DenominationHeatMap` ON (`DenominationHeatMap`.`machine_id` = `FileProccessingDetail`.`station`  AND `DenominationHeatMap`.`branch_id` = `FileProccessingDetail`.`branch_id`) LEFT JOIN `transaction_heat_maps` AS `TransactionHeatMaps` ON (`TransactionHeatMaps`.`machine_id` = `FileProccessingDetail`.`station` AND `TransactionHeatMaps`.`branch_id` = `FileProccessingDetail`.`branch_id`)";
        $sql_count = $sql_count . " WHERE " . $conditions_new;
        $result_count = $this->BillCount->query($sql_count);
        $total_data_count = $result_count[0][0]['count(BillCount.id)'];
        $total_page = ceil($total_data_count / $Paginate);
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
        $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $total_data_count, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
        $this->BillCount->virtualFields['error_count'] = 'count(BillCount.id)';
        $chartData = $this->BillCount->find('all', $billCountArr['chart']);
        $this->set(compact('tickInterval', 'bills', 'result_graph', 'temp_station'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['BillCount']['error_count'] = $value['BillCount']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['BillCount']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.' . $repSesName, $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TransactionMapReportFilter', $this->request->data['Analytic']);
            if (!isCompany()) {
            }
        }
        $aa = (isset($this->request->data['Analytic']['date'])) ? $this->request->data['Analytic']['date'] : date('Y-m-d', strtotime(date("Y-m-d") . ' -1 day'));
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;
        $lineChartName = __('Bill Count');
        $lineChartTitle = __('Bill Count');
        $lineChartxAxisTitle = __('Bill Count Date');
        $lineChartyAxisTitle = __('No. of bill counts');
        $this->set(compact('branches', 'companyDetail', 'stations', 'top_left', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/heat_map')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
        $this->set(compact('sessionData', 'companies', 'regiones', 'branches'));
    }
    /*
     * Bill Adjustment report
     */

    /**
     * Bill Activity report
     * @param type $all
     */
    function bill_activity($all = '')
    {

        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
        }
        $this->Session->delete('Report.BillsActivityReportCondition');
        $billTypes = ClassRegistry::init('BillType')->find('list', array('fields' => 'id, bill_type'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();

        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.BillReportFilter');
            $this->Session->delete('Report.BillsActivityReport');
            $this->Session->delete('Report.BillsActivityReportCondition');
        }
        $this->Session->delete('Report.BillReportFilter');
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.BillReportFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.BillReportFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }

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
        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        $conditions2['company_id'] = $company_id;//$sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
        }
        if ($sessionData['role'] == 'Company' and $sessionData['user_type'] == 'Branch') {
            $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsName($sessionData['id']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsList($branchidListd);
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        // Set Global Filter
        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'BillsActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (!empty($activityReportId)) {
            $conditions['BillsActivityReport.activity_report_id'] = $activityReportId;
        }
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
            }
            $this->request->data['Analytic']['company_id'] = $company_id;//getCompanyId();
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if ($this->Session->check('Auth.User.assign_branches')) {
            $assignedBranches = $this->Session->read('Auth.User.assign_branches');
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
      
        $all_condition = $this->Session->read('Report.BillsActivityReportCondition');
        // echo '<pre><b></b><br>';
        // print_r($all_condition);echo '<br>';exit;
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
        }


        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        $filter_criteria = array();
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                if (!isCompany()) {
                    $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
                }
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $regionsId = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                // $conditions['FileProccessingDetail']['Branch.regiones'] = $this->request->data['Analytic']['regiones'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branches_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branches_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
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
                // $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $conditions['BillsActivityReport.station'] = $this->request->data['Analytic']['station'];
            }
            if (!empty($this->request->data['Analytic']['bill_type_id'])) {
                $conditions['BillsActivityReport.bill_type_id'] = $this->request->data['Analytic']['bill_type_id'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            // $this->Session->write('Report.GlobalFilter',$this->request->data['Filter']);
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (empty($conditions['FileProccessingDetail.branch_id'])) {
            // $conditions['FileProccessingDetail.branch_id']='';
        }

        $conditions['BillsActivityReport.bill_type_id'] = array(0 => 1, 1 => 7, 2 => 3);
        // echo '<pre><b>' . __FILE__ . ' (Line:'. __LINE__ .')</b><br>';
        // print_r($conditions);echo '<br>';exit;
        $billActivityArr = $this->Analytic->getBillActivity($conditions);
        // echo '<pre><b></b><br>';
        // print_r($billActivityArr);echo '<br>';exit;
        $this->loadModel('BillsActivityReport');
        $this->loadModel('stations');


        $this->BillsActivityReport->virtualFields['denom_100'] =
            'IF(SUM(BillsActivityReport.denom_100)>0,
        SUM(BillsActivityReport.denom_100), 0)';
        $this->BillsActivityReport->virtualFields['denom_50'] =
            'IF(SUM(BillsActivityReport.denom_50)>0,
        SUM(BillsActivityReport.denom_50), 0)';
        $this->BillsActivityReport->virtualFields['denom_20'] =
            'IF(SUM(BillsActivityReport.denom_20)>0,
        SUM(BillsActivityReport.denom_20), 0)';
        $this->BillsActivityReport->virtualFields['denom_10'] =
            'IF(SUM(BillsActivityReport.denom_10)>0,
        SUM(BillsActivityReport.denom_10), 0)';
        $this->BillsActivityReport->virtualFields['denom_5'] =
            'IF(SUM(BillsActivityReport.denom_5)>0,
        SUM(BillsActivityReport.denom_5), 0)';
        $this->BillsActivityReport->virtualFields['denom_2'] =
            'IF(SUM(BillsActivityReport.denom_2)>0,
        SUM(BillsActivityReport.denom_2), 0)';
        $this->BillsActivityReport->virtualFields['denom_1'] =
            'IF(SUM(BillsActivityReport.denom_1)>0,
        SUM(BillsActivityReport.denom_1), 0)';
        $chartData =
            $this->BillsActivityReport->find(
                'all',
                $billActivityArr['chart_new']
            );
        $opration_cassette_id = '';
        $reject_id = '';
        $dispance_bill_id = '';


        if (isset($chartData[0])) {
            if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 1) {
                $dispance_bill_id = 0;
            } else if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 7) {
                $opration_cassette_id = 0;
            } else if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 3) {
                $reject_id = 0;
            }
        }
        if (isset($chartData[1])) {
            if ($chartData[1]['BillsActivityReport']['bill_type_id'] == 7) {
                $opration_cassette_id = 1;
            } else if ($chartData[1]['BillsActivityReport']['bill_type_id'] == 3) {
                $reject_id = 1;
            }
        }
        if (isset($chartData[2])) {
            if ($chartData[2]['BillsActivityReport']['bill_type_id'] == 3) {
                $reject_id = 2;
            } else if ($chartData[2]['BillsActivityReport']['bill_type_id'] == 7) {
                $opration_cassette_id = 2;
            }
        }

        $dispance_bill = array(
            'dispance_bill_$100' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_100'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_100'] : 0,
            'dispance_bill_$50' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_50'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_50'] : 0, 'dispance_bill_$20'
            => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_20'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_20'] : 0,
            'dispance_bill_$10' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_10'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_10'] : 0, 'dispance_bill_$5'
            => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_5'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_5'] : 0,
            'dispance_bill_$2' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_2'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_2'] : 0,
            'dispance_bill_$1' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_1'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_1'] : 0
        );

        $opration_cassette = array(
            'op_cassette_$100' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_100'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_100'] : 0,
            'op_cassette_$50' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_50'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_50'] : 0, 'op_cassette_$20'
            => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_20'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_20'] : 0,
            'op_cassette_$10' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_10'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_10'] : 0, 'op_cassette_$5'
            => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_5'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_5'] : 0,
            'op_cassette_$2' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_2'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_2'] : 0,
            'op_cassette_$1' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_1'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_1'] : 0
        );


        $reject = array(
            'Reject_$100' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_100'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_100'] : 0,
            'Reject_$50' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_50'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_50'] : 0, 'Reject_$20'
            => (isset($chartData[$reject_id]['BillsActivityReport']['denom_20'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_20'] : 0,
            'Reject_$10' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_10'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_10'] : 0, 'Reject_$5'
            => (isset($chartData[$reject_id]['BillsActivityReport']['denom_5'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_5'] : 0,
            'Reject_$2' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_2'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_2'] : 0,
            'Reject_$1' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_1'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_1'] : 0
        );
        foreach ($dispance_bill as $key => $value) {
            if ($dispance_bill[$key] == '') {
                $dispance_bill[$key] = 0;
            }
        }
        foreach ($opration_cassette as $key => $value) {
            if ($opration_cassette[$key] == '') {
                $opration_cassette[$key] = 0;
            }
        }
        foreach ($reject as $key => $value) {
            if ($reject[$key] == '') {
                $reject[$key] = 0;
            }
        }
        $total_inventory = array(
            'Total_inventory_$100'
            => ($dispance_bill['dispance_bill_$100'] + $opration_cassette['op_cassette_$100'] + $reject['Reject_$100']), 'Total_inventory_$50'
            => ($dispance_bill['dispance_bill_$50'] + $opration_cassette['op_cassette_$50'] + $reject['Reject_$50']), 'Total_inventory_$20'
            => ($dispance_bill['dispance_bill_$20'] + $opration_cassette['op_cassette_$20'] + $reject['Reject_$20']), 'Total_inventory_$10'
            => ($dispance_bill['dispance_bill_$10'] + $opration_cassette['op_cassette_$10'] + $reject['Reject_$10']), 'Total_inventory_$5'
            => ($dispance_bill['dispance_bill_$5'] + $opration_cassette['op_cassette_$5'] + $reject['Reject_$5']), 'Total_inventory_$2'
            => ($dispance_bill['dispance_bill_$2'] + $opration_cassette['op_cassette_$2'] + $reject['Reject_$2']), 'Total_inventory_$1'
            => ($dispance_bill['dispance_bill_$1'] + $opration_cassette['op_cassette_$1'] + $reject['Reject_$1'])
        );
        $graph_data_all = array();
        $graph_data_all['Dispense_Bill'] =
            $dispance_bill;
        $graph_data_all['Operation_Cassette']
            = $opration_cassette;
        $graph_data_all['Reject'] = $reject;
        $graph_data_all['Total_Inventory'] = $total_inventory;
        $graph_data_all
            = json_encode($graph_data_all, JSON_NUMERIC_CHECK);

        /* SUM(CASE 
                    WHEN BillsActivityReport.bill_type_id = '1' 
                    THEN BillsActivityReport.denom_100 
                    ELSE 0 
                END) AS denom1_100,*/

        $this->BillsActivityReport->virtualFields['denom_100'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1 
            THEN BillsActivityReport.denom_100 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_50'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1 
            THEN BillsActivityReport.denom_50 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_20'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1 
            THEN BillsActivityReport.denom_20 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_10'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1 
            THEN BillsActivityReport.denom_10 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_5'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_5 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_2'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_2 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom_1'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_1 
            ELSE 0 
        END)';



        $this->BillsActivityReport->virtualFields['denom2_100'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_100 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_50'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_50 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_20'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_20 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_10'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_10 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_5'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_5 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_2'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_2 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_1'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_1 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom2_100'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 7
            THEN BillsActivityReport.denom_100 
            ELSE 0 
        END)';

        $this->BillsActivityReport->virtualFields['denom3_100'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3 
            THEN BillsActivityReport.denom_100 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_50'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3
            THEN BillsActivityReport.denom_50 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_20'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3
            THEN BillsActivityReport.denom_20 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_10'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3 
            THEN BillsActivityReport.denom_10 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_5'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3
            THEN BillsActivityReport.denom_5
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_2'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3
            THEN BillsActivityReport.denom_2
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom3_1'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 3
            THEN BillsActivityReport.denom_1 
            ELSE 0 
        END)';

        $this->BillsActivityReport->virtualFields['denom1_100'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_100 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_50'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_50 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_20'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_20 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_10'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1 
            THEN BillsActivityReport.denom_10 
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_5'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_5
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_2'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_2
            ELSE 0 
        END)';
        $this->BillsActivityReport->virtualFields['denom1_1'] = 'SUM(CASE 
            WHEN BillsActivityReport.bill_type_id = 1
            THEN BillsActivityReport.denom_1 
            ELSE 0 
        END)';

        $billActivityArr_new = $this->Analytic->getBillActivity($conditions, $regionsId);
        $stattionData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stattionData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $chartData = $this->BillsActivityReport->find('all', $billActivityArr_new['paginate_new']);
        $this->AutoPaginate->setPaginate($billActivityArr_new['paginate_new']);
        $this->paginate['order'] = ['BillsActivityReport.denom1_100' => 'DESC'];
        $bills = $this->paginate('BillsActivityReport');

        /**
         * line chart
         */
        $this->BillsActivityReport->virtualFields['error_count'] = 'count(BillsActivityReport.id)';
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $conditions['FileProccessingDetail.regiones'] = $this->request->data['Analytic']['regiones'];
        }
        $this->Session->write('Report.BillsActivityReportCondition', $conditions);
        unset($conditions['FileProccessingDetail.regiones']);
        $this->set(compact('tickInterval', 'bills', 'temp_station', 'branches'));
        $temp = '1';
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode('1', JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates', 'graph_data_all'));

        if (!empty($this->request->data['Filter'])) {
            // $this->Session->write('Report.BillsActivityReport', $this->request->data['Filter']);
            // $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
        }
        $companyDetail = array();
        $lineChartName = __('Bill Activity');
        $lineChartTitle = __('Bill Activities');
        $lineChartxAxisTitle = __('Bill Activity Date');
        $lineChartyAxisTitle = __('No. of bill activity');
        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/bill_activities')->body();

            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $graph_data_all, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }





    function inventory_activity($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
        }
        // $billTypes = ClassRegistry::init('BillType')->find('list', array('fields' => 'id, bill_type'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();

        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.BillReportFilter');
            $this->Session->delete('Report.BillsActivityReport');
            $this->Session->delete('Report.BillsActivityReportCondition');
        }

        // if (empty($this->request->data['Analytic']) && $this->Session->check('Report.BillReportFilter')) {
        //     $this->request->data['Analytic'] = $this->Session->read('Report.BillReportFilter');
        // }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }




        $sessData = getMySessionData();
        $this->loadModel('Region');
        $sessionData = getMySessionData();
        $this->set(compact('sessionData'));
        $conditions2['company_id'] = $sessData['id'];
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
        }
        if ($sessionData['role'] == 'Company' and $sessionData['user_type'] == 'Branch') {
            $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsName($sessionData['id']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsList($branchidListd);
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        // if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
        //     $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        // }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('Inventory', $this->request->data['Filter'], 'Inventory');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        // if (!empty($activityReportId)) {
        //     $conditions['BillsActivityReport.activity_report_id'] = $activityReportId;
        // }
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
            }
            $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if ($this->Session->check('Auth.User.assign_branches')) {
            $assignedBranches = $this->Session->read('Auth.User.assign_branches');
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        $all_condition = $this->Session->read('Report.BillsActivityReportCondition');
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
                $conditions['Inventory.station'] = $all_condition['FileProccessingDetail.station'];
            }
        }


        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];

                if (!isCompany()) {
                    $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
                }
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

                $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }

            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $conditions['Inventory.station'] = $this->request->data['Analytic']['station'];
            }
            // if (!empty($this->request->data['Analytic']['bill_type_id'])) {
            //     $conditions['BillsActivityReport.bill_type_id'] = $this->request->data['Analytic']['bill_type_id'];
            // }

        }

        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        if (empty($conditions['FileProccessingDetail.branch_id'])) {
            // $conditions['FileProccessingDetail.branch_id']='';
        }

        // $conditions['BillsActivityReport.bill_type_id'] = array(0 => 1, 1 => 7, 2 => 3);;
        $billActivityArr = $this->Analytic->getInventory($conditions);
        // $this->loadModel('BillsActivityReport');
        $this->loadModel('Inventory');
        // echo "<pre>";
        // print_r($this->Inventory);exit;

        // $this->Inventory->virtualFields['denom_100'] =
        //     'IF(SUM(Inventory.denom_100)>0,
        // SUM(Inventory.denom_100), 0)';
        // $this->Inventory->virtualFields['denom_50'] =
        //     'IF(SUM(Inventory.denom_50)>0,
        // SUM(Inventory.denom_50), 0)';
        // $this->Inventory->virtualFields['denom_20'] =
        //     'IF(SUM(Inventory.denom_20)>0,
        // SUM(Inventory.denom_20), 0)';
        // $this->Inventory->virtualFields['denom_10'] =
        //     'IF(SUM(Inventory.denom_10)>0,
        // SUM(Inventory.denom_10), 0)';
        // $this->Inventory->virtualFields['denom_5'] =
        //     'IF(SUM(Inventory.denom_5)>0,
        // SUM(Inventory.denom_5), 0)';
        // $this->Inventory->virtualFields['denom_2'] =
        //     'IF(SUM(Inventory.denom_2)>0,
        // SUM(Inventory.denom_2), 0)';
        // $this->Inventory->virtualFields['denom_1'] =
        //     'IF(SUM(Inventory.denom_1)>0,
        // SUM(Inventory.denom_1), 0)';

        $chartData =
            $this->Inventory->find(
                'all',
                $billActivityArr['chart_new']
            );
        //     exit;
        // $opration_cassette_id = '';
        // $reject_id = '';
        // $dispance_bill_id = '';



        //         if (isset($chartData[0])) {
        //             if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 1) {
        //                 $dispance_bill_id = 0;
        //             } else if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 7) {
        //                 $opration_cassette_id = 0;
        //             } else if ($chartData[0]['BillsActivityReport']['bill_type_id'] == 3) {
        //                 $reject_id = 0;
        //             }
        //         }
        //         if (isset($chartData[1])) {
        //             if ($chartData[1]['BillsActivityReport']['bill_type_id'] == 7) {
        //                 $opration_cassette_id = 1;
        //             } else if ($chartData[1]['BillsActivityReport']['bill_type_id'] == 3) {
        //                 $reject_id = 1;
        //             }
        //         }
        //         if (isset($chartData[2])) {
        //             if ($chartData[2]['BillsActivityReport']['bill_type_id'] == 3) {
        //                 $reject_id = 2;
        //             }
        //         }

        //         $dispance_bill = array(
        //             'dispance_bill_$100' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_100'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_100'] : 0,
        //             'dispance_bill_$50' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_50'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_50'] : 0, 'dispance_bill_$20'
        //             => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_20'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_20'] : 0,
        //             'dispance_bill_$10' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_10'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_10'] : 0, 'dispance_bill_$5'
        //             => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_5'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_5'] : 0,
        //             'dispance_bill_$2' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_2'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_2'] : 0,
        //             'dispance_bill_$1' => (isset($chartData[$dispance_bill_id]['BillsActivityReport']['denom_1'])) ? $chartData[$dispance_bill_id]['BillsActivityReport']['denom_1'] : 0
        //         );

        //         $opration_cassette = array(
        //             'op_cassette_$100' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_100'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_100'] : 0,
        //             'op_cassette_$50' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_50'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_50'] : 0, 'op_cassette_$20'
        //             => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_20'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_20'] : 0,
        //             'op_cassette_$10' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_10'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_10'] : 0, 'op_cassette_$5'
        //             => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_5'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_5'] : 0,
        //             'op_cassette_$2' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_2'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_2'] : 0,
        //             'op_cassette_$1' => (isset($chartData[$opration_cassette_id]['BillsActivityReport']['denom_1'])) ? $chartData[$opration_cassette_id]['BillsActivityReport']['denom_1'] : 0
        //         );


        //         $reject = array(
        //             'Reject_$100' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_100'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_100'] : 0,
        //             'Reject_$50' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_50'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_50'] : 0, 'Reject_$20'
        //             => (isset($chartData[$reject_id]['BillsActivityReport']['denom_20'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_20'] : 0,
        //             'Reject_$10' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_10'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_10'] : 0, 'Reject_$5'
        //             => (isset($chartData[$reject_id]['BillsActivityReport']['denom_5'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_5'] : 0,
        //             'Reject_$2' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_2'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_2'] : 0,
        //             'Reject_$1' => (isset($chartData[$reject_id]['BillsActivityReport']['denom_1'])) ? $chartData[$reject_id]['BillsActivityReport']['denom_1'] : 0
        //         );
        //         foreach ($dispance_bill as $key => $value) {
        //             if ($dispance_bill[$key] == '') {
        //                 $dispance_bill[$key] = 0;
        //             }
        //         }
        //         foreach ($opration_cassette as $key => $value) {
        //             if ($opration_cassette[$key] == '') {
        //                 $opration_cassette[$key] = 0;
        //             }
        //         }
        //         foreach ($reject as $key => $value) {
        //             if ($reject[$key] == '') {
        //                 $reject[$key] = 0;
        //             }
        //         }
        //         $total_inventory = array(
        //             'Total_inventory_$100'
        //             => ($dispance_bill['dispance_bill_$100'] + $opration_cassette['op_cassette_$100'] + $reject['Reject_$100']), 'Total_inventory_$50'
        //             => ($dispance_bill['dispance_bill_$50'] + $opration_cassette['op_cassette_$50'] + $reject['Reject_$50']), 'Total_inventory_$20'
        //             => ($dispance_bill['dispance_bill_$20'] + $opration_cassette['op_cassette_$20'] + $reject['Reject_$20']), 'Total_inventory_$10'
        //             => ($dispance_bill['dispance_bill_$10'] + $opration_cassette['op_cassette_$10'] + $reject['Reject_$10']), 'Total_inventory_$5'
        //             => ($dispance_bill['dispance_bill_$5'] + $opration_cassette['op_cassette_$5'] + $reject['Reject_$5']), 'Total_inventory_$2'
        //             => ($dispance_bill['dispance_bill_$2'] + $opration_cassette['op_cassette_$2'] + $reject['Reject_$2']), 'Total_inventory_$1'
        //             => ($dispance_bill['dispance_bill_$1'] + $opration_cassette['op_cassette_$1'] + $reject['Reject_$1'])
        //         );
        //         $graph_data_all = array();
        //         $graph_data_all['Dispense_Bill'] =
        //             $dispance_bill;
        //         $graph_data_all['Operation_Cassette']
        //             = $opration_cassette;
        //         $graph_data_all['Reject'] = $reject;
        //         $graph_data_all['Total_Inventory'] = $total_inventory;
        //         $graph_data_all
        //             = json_encode($graph_data_all, JSON_NUMERIC_CHECK);

        //                     /* SUM(CASE 
        //                     WHEN BillsActivityReport.bill_type_id = '1' 
        //                     THEN BillsActivityReport.denom_100 
        //                     ELSE 0 
        //                 END) AS denom1_100,*/

        //         $this->Inventory->virtualFields['denom_100'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_100 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_50'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_50 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_20'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_20 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_10'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_10 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_5'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_5 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_2'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_2 
        //     ELSE 0 
        // END)';
        //         $this->Inventory->virtualFields['denom_1'] = 'SUM(CASE 

        //     THEN BillsActivityReport.denom_1 
        //     ELSE 0 
        // END)';

        // Configure::write('debug', 2);
        $billActivityArr_new = $this->Analytic->getInventory($conditions);
        $chartData = $this->Inventory->find('all', $billActivityArr_new['paginate_new']);
        $this->AutoPaginate->setPaginate($billActivityArr_new['paginate_new']);
        $this->paginate['order'] = ['BillsActivityReport.denom1_100' => 'DESC'];
        $bills = $this->paginate('Inventory');
        // echo "<pre>";
        // print_r($bills);exit;


        //         /**
        //          * line chart
        //          */
        $this->Inventory->virtualFields['error_count'] = 'count(Inventory.id)';
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $conditions['FileProccessingDetail.regiones'] = $this->request->data['Analytic']['regiones'];
        }
        //         $this->Session->write('Report.BillsActivityReportCondition', $conditions);
        unset($conditions['FileProccessingDetail.regiones']);
        $this->set(compact('tickInterval', 'bills'));
        $temp = '1';
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode('1', JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates', 'graph_data_all'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.Inventory', $this->request->data['Filter']);
        }
        $companyDetail = array();
        $lineChartName = __('Bill Activity');
        $lineChartTitle = __('Bill Activities');
        $lineChartxAxisTitle = __('Bill Activity Date');
        $lineChartyAxisTitle = __('No. of bill activity');
        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/bill_inventory')->body();

            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }


    /**
     * Side Activity Report
     * @param type $all
     */
    function side_activity($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.SideActivityFilter');
            $this->Session->delete('Report.SideActivityReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.SideActivityFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.SideActivityFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('SideActivityReport', $this->request->data['Filter'], 'SideActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (!empty($activityReportId)) {
            $conditions['SideActivityReport.activity_report_id'] = $activityReportId;
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $this->loadModel('SideActivityReport');
        $activityArr = $this->Analytic->getSideActivity($conditions);
        $this->AutoPaginate->setPaginate($activityArr['paginate']);
        $activity = $this->paginate('SideActivityReport');
        /**
         * line chart
         */
        $this->SideActivityReport->virtualFields['error_count'] = 'count(SideActivityReport.id)';
        $chartData = $this->SideActivityReport->find('all', $activityArr['chart']);
        $this->Session->write('Report.SideActivityReportCondition', $conditions);
        $this->set(compact('tickInterval', 'activity'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['SideActivityReport']['error_count'] = $value['SideActivityReport']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['SideActivityReport']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.SideActivityReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.SideActivityFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Side Activity');
        $lineChartTitle = __('Side Activities');
        $lineChartxAxisTitle = __('Side Activity Date');
        $lineChartyAxisTitle = __('No. of Side activity');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/side_activities')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /**
     * Activity report
     * @param type $all
     */
    function activity_report($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.ActivityFilter');
            $this->Session->delete('Report.ActivityReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.ActivityFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.ActivityFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('ActivityReport', $this->request->data['Filter'], 'ActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if ($this->Session->check('Auth.User.assign_branches')) {
            $assignedBranches = $this->Session->read('Auth.User.assign_branches');
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
            $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                if (!isCompany()) {
                    $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
                }
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $conditions['ActivityReport.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $this->loadModel('ActivityReport');
        $this->ActivityReport->virtualFields['bill_count'] = 'count(ActivityReport.id)';
        $activityReportArr = $this->Analytic->getActivityReport($conditions);
        $this->AutoPaginate->setPaginate($activityReportArr['paginate']);
        $activity = $this->paginate('ActivityReport');
        /**
         * line chart
         */
        $this->ActivityReport->virtualFields['error_count'] = 'count(ActivityReport.id)';
        $chartData = $this->ActivityReport->find('all', $activityReportArr['chart']);
        $this->Session->write('Report.ActivityFilterReportCondition', $conditions);
        $this->set(compact('tickInterval', 'activity'));
        $temp = array();
        $totalCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
            }
            $totalCount = $totalCount + $value['ActivityReport']['error_count'];
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['ActivityReport']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.ActivityReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.ActivityFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Activity Report');
        $lineChartTitle = __('Activity Report');
        $lineChartxAxisTitle = __('Activity Date');
        $lineChartyAxisTitle = __('No. of Activity');
        $this->set(compact('totalCount', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/reports/activity_report')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /**
     * Teller activity report
     * @param type $all
     */
    function teller_activity($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $sessionData = getMySessionData();

        // Parent company id get.
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerActivityFilter');
            $this->Session->delete('Report.TellerActivityReport');
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $conditions2['company_id'] = $company_parent_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TellerActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        $filter_criteria = array();
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();

            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
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
                $conditions['FileProccessingDetail.branch_id'] = $branchLists;
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }

            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    // $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }

            if (!empty($this->request->data['Analytic']['tellerName'])) {
                $conditions['TellerActivityReport.teller_name LIKE'] = $this->request->data['Analytic']['tellerName'] . '%';
                $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
            }
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
        }
        if (!empty($activityReportId)) {
            $conditions['TellerActivityReport.activity_report_id'] = $activityReportId;
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
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('TransactionDetail');

        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_parent_id], 'fields' => 'DISTINCT teller_name', 'order' => ['teller_name' => 'ASC']));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->Session->write('Report.TellerActivityCondition', $conditions);

        $tellerActivityArr = $this->Analytic->getTellerActivity($conditions, $regionsId);
        $this->loadModel('TellerActivityReport');
        $this->AutoPaginate->setPaginate($tellerActivityArr['paginate']);
        $activity = $this->paginate('TellerActivityReport');
        $i = 1;
        $chartData_Arr = array();
        $chartData_Arr[0][0] = "Teller Name";
        $chartData_Arr[0][1] = "Withdrawal Total";
        $chartData_Arr[0][2] = "Deposite Total";
        $chartData2 = $this->TellerActivityReport->find('all', array(  'fields' => array(
            'SUM(TellerActivityReport.Withdrawal_total) AS Withdrawal_total', 'SUM(TellerActivityReport.deposit_total) deposit_total', 'TellerActivityReport.teller_name'
         ),'conditions' => $conditions, 'group' => array('TellerActivityReport.teller_name')));

        if (!empty($chartData2)) {
            foreach ($chartData2 as $key => $value) {
                $chartData_Arr[$i][0] = !empty($value['TellerActivityReport']['teller_name']) ? $value['TellerActivityReport']['teller_name'] : 'noname';
                $chartData_Arr[$i][1] = $value[0]['deposit_total'];
                $chartData_Arr[$i][2] = $value[0]['Withdrawal_total'];
                $i++;
            }
            $chartData_Arr = json_encode($chartData_Arr, JSON_NUMERIC_CHECK);
            $this->set(compact('chartData_Arr'));
        } else {
            $chartData_Arr = json_encode("No data found");
            $this->set(compact('chartData_Arr'));
        }

        $j = 1;
        $chartData_Arr2 = array();
        $chartData_Arr2[0][0] = "Teller Name";
        $chartData_Arr2[0][1] = "Withdrawal Count";
        $chartData_Arr2[0][2] = "Deposite Count";
        // $chartData_count = $this->TellerActivityReport->find('all', array('conditions' => $conditions));

        $chartData_count = $this->TellerActivityReport->find('all', array(  'fields' => array(
            'SUM(TellerActivityReport.number_of_deposits) AS number_of_deposits', 'SUM(TellerActivityReport.number_of_withdrawals) number_of_withdrawals', 'TellerActivityReport.teller_name'
         ),'conditions' => $conditions, 'group' => array('TellerActivityReport.teller_name')));
        if (!empty($chartData_count)) {
            foreach ($chartData_count as $key => $value) {
                $teller_name = !empty($value['TellerActivityReport']['teller_name']) ? $value['TellerActivityReport']['teller_name'] : 'noname';
                $chartData_Arr2[$j][0] = $teller_name;
                $chartData_Arr2[$j][1] = $value[0]['number_of_deposits'];
                $chartData_Arr2[$j][2] = $value[0]['number_of_withdrawals'];
                $j++;
            }
            // echo '<pre><b></b><br>';
            // print_r($chartData_Arr2);echo '<br>';exit;
            $chartData_Arr2 = json_encode($chartData_Arr2, JSON_NUMERIC_CHECK);
            $this->set(compact('chartData_Arr2'));
        } else {
            $chartData_Arr2[1][0] = "No Teller";
            $chartData_Arr2[1][1] = 0;
            $chartData_Arr2[1][2] = 0;    
            $chartData_Arr2 = json_encode($chartData_Arr2, JSON_NUMERIC_CHECK);
            $this->set(compact('chartData_Arr2'));
        }
        $this->TellerActivityReport->virtualFields['error_count'] = 'count(TellerActivityReport.id)';
        $chartData = $this->TellerActivityReport->find('all', $tellerActivityArr['chart']);
        $this->Session->write('Report.TellerActivityReportCondition', $conditions);
        $this->TellerActivityReport->virtualFields['teller_id'] = 'TellerActivityReport.teller_id';
        $this->TellerActivityReport->virtualFields['sum'] = 'SUM(TellerActivityReport.number_of_deposits)';
        $number_of_deposits = $this->TellerActivityReport->find('all', $tellerActivityArr['deposit']);
        $this->TellerActivityReport->virtualFields['sum'] = 'SUM(TellerActivityReport.number_of_withdrawals)';
        $number_of_withdrawals = $this->TellerActivityReport->find('all', $tellerActivityArr['deposit']);
        $this->set(compact('tickInterval', 'activity', 'temp_station', 'tellerNames_Arr'));
        $temp = array();
        $temp_deposits = array();
        $temp_withdrawals = array();
        $temp_data = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TellerActivityReport']['error_count'] = $value['TellerActivityReport']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TellerActivityReport']['error_count']);
        }
        foreach ($number_of_deposits as $key => $value) {
            $date = $value['TellerActivityReport']['teller_id'];
            //if (!empty($value['TellerActivityReport']['teller_id'])) {
            if (isset($temp_deposits[$date])) {
                $value['TellerActivityReport']['sum'] = $value['TellerActivityReport']['sum'] + $temp_deposits[$date][1];
            }
            $temp_deposits[$date] = array($value['TellerActivityReport']['teller_id'], $value['TellerActivityReport']['sum']);
            //}
        }
        foreach ($number_of_withdrawals as $key => $value) {
            $date = $value['TellerActivityReport']['teller_id'];
            //if (!empty($value['TellerActivityReport']['teller_id'])) {
            if (isset($temp_withdrawals[$date])) {
                $value['TellerActivityReport']['sum'] = $value['TellerActivityReport']['sum'] + $temp_withdrawals[$date][1];
            }
            $temp_withdrawals[$date] = array($value['TellerActivityReport']['teller_id'], $value['TellerActivityReport']['sum']);
            //}
        }
        foreach ($temp_withdrawals as $key => $value) {
            array_push($temp_withdrawals[$key], $temp_deposits[$key][1]);
            array_push($temp_data, $temp_withdrawals[$key]);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
        endforeach;


        $temp = $sendArr;
        $temp = json_encode($temp_data, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerActivityReport', $this->request->data['Filter']);
        }
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $conditions['FileProccessingDetail.regiones'] = $this->request->data['Analytic']['regiones'];
        }
        $this->Session->write('Report.TellerActivityCondition', $conditions);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TellerActivityFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $aa = $this->request->data['Analytic']['date'];
        // $aa = str_replace('-', '/', $aa); 
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;
        $lineChartName = __('Teller Activity');
        $lineChartTitle = __('Teller Activities');
        $lineChartxAxisTitle = __('Teller Activity Date');
        $lineChartyAxisTitle = __('No. of Teller activity');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $activity);
            $transactionData = $this->render('/Elements/reports/teller_activity')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
        $this->set(compact('branches', 'stations', 'sessionData', 'companies', 'regiones', 'temp_station', 'activity', 'filter_criteria'));
    }
    /**
     * Teller Setup report
     * @param type $all
     */
    function teller_setup($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerSetupFilter');
            $this->Session->delete('Report.TellerSetupReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TellerSetupFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TellerSetupFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TellerSetupReport', $this->request->data['Filter'], 'TellerSetup');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'TellerSetup.datetime >=' => $startDate,
            'TellerSetup.datetime <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
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
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $tellerSetupArr = $this->Analytic->getTellerSetup($conditions);
        $this->loadModel('TellerSetup');
        $this->TellerSetup->virtualFields['bill_count'] = 'count(TellerSetup.id)';
        $this->AutoPaginate->setPaginate($tellerSetupArr['paginate']);
        $TellerSetups = $this->paginate('TellerSetup');
        /**
         * line chart
         */
        $this->TellerSetup->virtualFields['error_count'] = 'count(TellerSetup.id)';
        $this->TellerSetup->virtualFields['datetime'] = 'DATE_FORMAT(datetime,"%Y-%m-%d")';
        $chartData = $this->TellerSetup->find('all', $tellerSetupArr['chart']);
        $this->Session->write('Report.TellerSetupReportCondition', $conditions);
        $this->set(compact('tickInterval', 'TellerSetups'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TellerSetup']['error_count'] = $value['TellerSetup']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TellerSetup']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerSetupReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TellerSetupFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Teller Setup');
        $lineChartTitle = __('Teller Setup');
        $lineChartxAxisTitle = __('Teller Setup Date');
        $lineChartyAxisTitle = __('No. of Teller Setup');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $TellerSetups);
            $transactionData = $this->render('/Elements/reports/teller_setup')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /**
     * Side Log Report
     * @param type $all
     */
    function side_log($all = '')
    {
        date_default_timezone_set("America/New_York");
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
        }
        $sessionData = getMySessionData();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];

        $this->set(compact('sessionData'));
        if (!empty($all)) {
            $this->Session->delete('Report.SideLogFilter');
            $this->Session->delete('Report.SideLogReport');
            $this->Session->delete('Report.SideLogReportCondition');
        }
        $date = date("d-m-Y");
        $this->set(compact('date'));
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.SideLogFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.SideLogFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */

        if (isset($this->request['named']['sort'])) {
            $order_by = "ORder by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('SideLogReport', $this->request->data['Filter'], 'SideLog');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        if (!empty($this->request->data['Analytic']['date'])) {

            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions['start_date'] = $this->request->data['Analytic']['date'];
            $conditions['end_date'] = $this->request->data['Analytic']['date'];
        } else {
            $conditions['start_date'] = date('Y-m-d', strtotime(date("Y-m-d") . ' -1 day')); //date("Y-m-d"); 
            $conditions['end_date'] = date('Y-m-d', strtotime(date("Y-m-d") . ' -1 day')); //date("Y-m-d"); 
            $this->request->data['Analytic']['date'] = date('Y-m-d', strtotime(date("Y-m-d") . ' -1 day'));
        }
        $startDate = date('Y-m-d', strtotime($conditions['start_date'] . ' -1 day')); // $conditions['start_date'];
        $endDate = date('Y-m-d', strtotime($conditions['end_date'] . ' +1 day'));
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }

        $conditions = array(
            'FileProccessingDetail.file_date >' => $startDate,
            'FileProccessingDetail.file_date <' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = $company_parent_id;//getCompanyId();
        }
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

        $this->loadModel('Region');
        $conditions2['company_id'] = $company_parent_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

                $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if ($sessionData['role'] == 'Company' and $sessionData['user_type'] == 'Branch') {
            $branchidListd = ClassRegistry::init('BranchAdmin')->getAssignedAdminsName($sessionData['id']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsList($branchidListd);
            $this->set(compact('branches'));
        }
        if (empty($this->request->data['Filter'])) {
            /*  $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;*/
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
                $conditions_new = $conditions_new . " " . $key . " IN(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
            }
        }

        $page_info = $this->Session->read('Report.SideLogReportCondition');
        /* echo "<pre>";
        print_r($this->Session->read('Report.SideLogReportCondition'));
        print_r($page_info);
        die();*/
        if ((isset($this->request->params['named']['sort'])) or (isset($this->request->params['named']['Paginate']))  or (isset($this->request->params['named']['page'])) or $f == 1) {
            $Paginate = (isset($page_info['FileProccessingDetail.Paginate'])) ? $page_info['FileProccessingDetail.Paginate'] : 20;
            $page = (isset($page_info['FileProccessingDetail.page'])) ? $page_info['FileProccessingDetail.page'] : 1;
        } else {
            $Paginate = 20;
            $page = 1;
        }

        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        $sql_tableData = "SELECT side.id, side.file_processing_detail_id, side.teller_id, side.side_type,  'T'
        FROM side_log AS side
        JOIN file_processing_detail AS 
        FileProccessingDetail ON ( side.file_processing_detail_id = FileProccessingDetail.id ) 
        WHERE " . $conditions_new . "
        UNION 
        SELECT manager.id, manager.file_processing_detail_id, manager.manager_id,  '',  'M'
        FROM manager_log AS manager
        JOIN file_processing_detail AS 
        FileProccessingDetail ON ( manager.file_processing_detail_id = FileProccessingDetail.id ) 
        WHERE " . $conditions_new . " " . $limit . " ";
        $sql_tableTotal_record = "SELECT    count(side.id)
        FROM side_log AS side
        JOIN file_processing_detail AS 
        FileProccessingDetail ON ( side.file_processing_detail_id = FileProccessingDetail.id ) 
        WHERE " . $conditions_new . "
        UNION 
        SELECT count(manager.id)
        FROM manager_log AS manager
        JOIN file_processing_detail AS 
        FileProccessingDetail ON ( manager.file_processing_detail_id = FileProccessingDetail.id ) 
        WHERE " . $conditions_new . " ";
        $this->loadModel('SideLog');
        $result_graph = $this->SideLog->query($sql_tableData);
        $result_graph_count = $this->SideLog->query($sql_tableTotal_record);
        $total_data_count = $result_graph_count[0][0]['count(side.id)'] + $result_graph_count[1][0]['count(side.id)'];
        $sideLogArr = $this->Analytic->getSideLog($conditions);
        $this->loadModel('SideLog');
        $this->AutoPaginate->setPaginate($sideLogArr['paginate2']);
        $sideLogArr_new = $this->Analytic->getSideLogNew($conditions, $limit, $order_by);
        $SideLogs = $this->paginate('SideLog');
        $SideLogs = $result_graph;
        /**
         * line chart
         */
        /* echo "<pre>";
        print_r($sideLogArr_new);
        die();*/
        $sideLogArr_new2 = $this->Analytic->getSideLogNew($conditions, '', '');
        $total_data_count = sizeof($sideLogArr_new2);
        $total_page = ceil($total_data_count / $Paginate);
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
        $top_left = array('start' => $limit_start, 'end' => $Paginate, 'total' => $total_data_count, 'page' => $page, 'page_start' => $page_start, 'page_end' => $page_end);
        $this->SideLog->virtualFields['error_count'] = 'count(SideLog.id)';
        $chartData = $this->SideLog->find('all', $sideLogArr['chart']);
        $conditions['FileProccessingDetail.page'] = $page;
        $conditions['FileProccessingDetail.Paginate'] = $Paginate;
        $this->Session->write('Report.SideLogReportCondition', $conditions);
        unset($conditions['FileProccessingDetail.page']);
        unset($conditions['FileProccessingDetail.Paginate']);

        $this->set(compact('tickInterval', 'SideLogs', 'top_left', 'sideLogArr_new'));
        $tempCount = 0;
        $graph_data = array();
        $graph_data_temp = array();
        foreach ($chartData as $key => $value) {
            $name = $value['FileProccessingDetail']['station'];
            $graph_data_temp[$key][0] = $value['FileProccessingDetail']['station'];
            $graph_data_temp[$key][1] = $value['SideLog']['teller_id'];
            $graph_data_temp[$key][2] =  (strtotime($value['SideLog']['logon_datetime']));
            /* if((strtotime($value['SideLog']['logon_datetime'])) <= (strtotime($value['SideLog']['logoff_datetime']))){
                $graph_data_temp[$key][3]=(strtotime($value['SideLog']['logon_datetime']))+100000;
            }else{
            $graph_data_temp[$key][3]=  (strtotime($value['SideLog']['logon_datetime']))+100000; 
            }*/
            $graph_data_temp[$key][3] =  (strtotime($value['SideLog']['logoff_datetime']));
        }
        foreach ($graph_data_temp as $key => $value) {
            array_push($graph_data, $value);
        }
        $temp1 = array(
            array(
                'data' => json_encode($graph_data, JSON_NUMERIC_CHECK)
            )
        );
        /*  echo "<pre>";
                print_r($graph_data);
                die(); */
        $temp = json_encode($temp1, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.SideLogReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.SideLogFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $aa = $this->request->data['Analytic']['date'];
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;
        $lineChartName = __('Side Log');
        $lineChartTitle = __('Side Log');
        $lineChartxAxisTitle = __('Side Log Date');
        $lineChartyAxisTitle = __('No. of Side log');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $SideLogs);
            $transactionData = $this->render('/Elements/reports/side_log')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }
    function total_vault_buy($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TotalVaultBuyFilter');
            $this->Session->delete('Report.TotalVaultBuyReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TotalVaultBuyFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TotalVaultBuyFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TotalVaultBuyReport', $this->request->data['Filter'], 'TotalVaultBuy');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $totalVaultBuyArr = $this->Analytic->getTotalVaultBuy($conditions);
        $this->loadModel('TotalVaultBuy');
        //        $this->TotalVaultBuy->virtualFields['bill_count'] = 'count(TotalVaultBuy.id)';
        $this->AutoPaginate->setPaginate($totalVaultBuyArr['paginate']);
        $TotalVaultBuys = $this->paginate('TotalVaultBuy');
        /**
         * line chart
         */
        $this->TotalVaultBuy->virtualFields['error_count'] = 'count(TotalVaultBuy.id)';
        $chartData = $this->TotalVaultBuy->find('all', $totalVaultBuyArr['chart']);
        $this->Session->write('Report.TotalVaultBuyReportCondition', $conditions);
        $this->set(compact('tickInterval', 'TotalVaultBuys'));
        $temp = array();

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TotalVaultBuy']['error_count'] = $value['TotalVaultBuy']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TotalVaultBuy']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TotalVaultBuyReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TotalVaultBuyFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Total Vault Buys');
        $lineChartTitle = __('Total Vault Buys');
        $lineChartxAxisTitle = __('Total Vault Buys Date');
        $lineChartyAxisTitle = __('No. of Vault Buys');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $TotalVaultBuys);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/total_vault_buy')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function transaction_vault_buys($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TransactionVaultBuyFilter');
            $this->Session->delete('Report.TransactionVaultBuyReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TransactionVaultBuyFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TransactionVaultBuyFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TransactionVaultBuyReport', $this->request->data['Filter'], 'TransactionVaultBuy');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $transactionVaultBuyArr = $this->Analytic->getTransactionVaultBuy($conditions);
        $this->loadModel('TransactionVaultBuy');
        //        $this->TransactionVaultBuy->virtualFields['bill_count'] = 'count(TransactionVaultBuy.id)';
        $this->AutoPaginate->setPaginate($transactionVaultBuyArr['paginate']);
        $TransactionVaultBuys = $this->paginate('TransactionVaultBuy');
        /**
         * line chart
         */
        $this->TransactionVaultBuy->virtualFields['error_count'] = 'count(TransactionVaultBuy.id)';
        $chartData = $this->TransactionVaultBuy->find('all', $transactionVaultBuyArr['chart']);
        $this->Session->write('Report.TransactionVaultBuyReportCondition', $conditions);
        $this->set(compact('tickInterval', 'TransactionVaultBuys'));
        $temp = array();

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionVaultBuy']['error_count'] = $value['TransactionVaultBuy']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionVaultBuy']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TransactionVaultBuyReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TransactionVaultBuyFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Teller Transaction Vault Buys');
        $lineChartTitle = __('Teller Transaction Vault Buys');
        $lineChartxAxisTitle = __('Teller Transaction Vault Buys Date');
        $lineChartyAxisTitle = __('No. of Teller Transaction Vault Buys');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $TransactionVaultBuys);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/transaction_vault_buys')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function teller_user($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerUserFilter');
            $this->Session->delete('Report.TellerUserReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TellerUserFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TellerUserFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TellerUserReport', $this->request->data['Filter'], 'TransactionVaultBuy');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $tellerUserArr = $this->Analytic->getTellerUser($conditions);
        $this->loadModel('TransactionVaultBuy');
        //        $this->TransactionVaultBuy->virtualFields['bill_count'] = 'count(TransactionVaultBuy.id)';
        $this->AutoPaginate->setPaginate($tellerUserArr['paginate']);
        $TransactionVaultBuys = $this->paginate('TransactionVaultBuy');
        /**
         * line chart
         */
        $this->TransactionVaultBuy->virtualFields['error_count'] = 'count(TransactionVaultBuy.id)';
        $chartData = $this->TransactionVaultBuy->find('all', $tellerUserArr['chart']);
        $this->Session->write('Report.TellerUserCondition', $conditions);
        $this->set(compact('tickInterval', 'TransactionVaultBuys'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionVaultBuy']['error_count'] = $value['TransactionVaultBuy']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionVaultBuy']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerUserReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TellerUserFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Teller User');
        $lineChartTitle = __('Teller User');
        $lineChartxAxisTitle = __('Teller User Date');
        $lineChartyAxisTitle = __('No. of Teller User');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $TransactionVaultBuys);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/teller_user')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function teller_user_report($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerUserReportFilter');
            $this->Session->delete('Report.TellerUserReportReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TellerUserReportFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TellerUserReportFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TellerUserReportReport', $this->request->data['Filter'], 'TellerUserReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if ($this->Session->check('Auth.User.assign_branches')) {
            $assignedBranches = $this->Session->read('Auth.User.assign_branches');
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $tellerUserReportArr = $this->Analytic->getTellerUserReport($conditions);

        $this->loadModel('TellerUserReport');
        //        $this->TellerUserReport->virtualFields['bill_count'] = 'count(TellerUserReport.id)';
        $this->AutoPaginate->setPaginate($tellerUserReportArr['paginate']);
        $TellerUserReports = $this->paginate('TellerUserReport');
        /**
         * line chart
         */
        $this->TellerUserReport->virtualFields['error_count'] = 'count(TellerUserReport.id)';
        $chartData = $this->TellerUserReport->find('all', $tellerUserReportArr['chart']);
        $this->Session->write('Report.TellerUserReportCondition', $conditions);
        $this->set(compact('tickInterval', 'TellerUserReports'));
        $temp = array();

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TellerUserReport']['error_count'] = $value['TellerUserReport']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TellerUserReport']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date . " 00:00:00") * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerUserReportReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TellerUserReportFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Teller User Report');
        $lineChartTitle = __('Teller User Report');
        $lineChartxAxisTitle = __('Teller User Date');
        $lineChartyAxisTitle = __('No. of Teller User');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('TellerUserReports', $TellerUserReports);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/teller_user_report')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function valut_buy($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.ValutBuyFilter');
            $this->Session->delete('Report.ValutBuyReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.ValutBuyFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.ValutBuyFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('ValutBuyReport', $this->request->data['Filter'], 'ValutBuy');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $valutBuyArr = $this->Analytic->getValutBuy($conditions);
        $this->loadModel('ValutBuy');
        //        $this->ValutBuy->virtualFields['bill_count'] = 'count(ValutBuy.id)';
        $this->AutoPaginate->setPaginate($valutBuyArr['paginate']);
        $valutBuys = $this->paginate('ValutBuy');
        /**
         * line chart
         */
        $this->ValutBuy->virtualFields['error_count'] = 'count(ValutBuy.id)';
        $chartData = $this->ValutBuy->find('all', $valutBuyArr['chart']);
        $this->Session->write('Report.ValutBuyReportCondition', $conditions);
        $this->set(compact('tickInterval', 'valutBuys'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.ValutBuy');
        //
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['ValutBuy']['error_count'] = $value['ValutBuy']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['ValutBuy']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.ValutBuyReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.ValutBuyFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Vault Buy');
        $lineChartTitle = __('Vault Buy');
        $lineChartxAxisTitle = __('Vault Buy Date');
        $lineChartyAxisTitle = __('No. of Vault Buy');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $valutBuys);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/valut_buy')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /**
     * Display Net Cash usage Activity Report
     * @param type $all
     */
    function net_cash_usage($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.NetCashUsageFilter');
            $this->Session->delete('Report.NetCashUsageReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.NetCashUsageFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.NetCashUsageFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('NetCashUsageReport', $this->request->data['Filter'], 'NetCashUsageActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (!empty($activityReportId)) {
            $conditions['NetCashUsageActivityReport.activity_report_id'] = $activityReportId;
        }
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (isCompany()) {
            $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $netCashUsageArr = $this->Analytic->getNetCashUsageActivity($conditions);
        $this->loadModel('NetCashUsageActivityReport');
        //        $this->NetCashUsageActivityReport->virtualFields['bill_count'] = 'count(NetCashUsageActivityReport.id)';
        $this->AutoPaginate->setPaginate($netCashUsageArr['paginate']);
        $NetCashes = $this->paginate('NetCashUsageActivityReport');
        /**
         * line chart
         */
        $this->NetCashUsageActivityReport->virtualFields['error_count'] = 'count(NetCashUsageActivityReport.id)';

        $chartData = $this->NetCashUsageActivityReport->find('all', $netCashUsageArr['chart']);
        $this->Session->write('Report.NetCashUsageReportCondition', $conditions);
        $this->set(compact('tickInterval', 'NetCashes'));
        $temp = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['NetCashUsageActivityReport']['error_count'] = $value['NetCashUsageActivityReport']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['NetCashUsageActivityReport']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date . " 00:00:00") * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date . " 00:00:00") * 1000);
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.NetCashUsageReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.NetCashUsageFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Net Cash Usage');
        $lineChartTitle = __('Net Cash usage Activity Report');
        $lineChartxAxisTitle = __('Net Cash usage Date');
        $lineChartyAxisTitle = __('No. of Net Cash usage');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            //Used side activity element as same field are required 
            $transactionData = $this->render('/Elements/reports/net_cash_usage_report')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function history($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.HistoryFilter');
            $this->Session->delete('Report.HistoryReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.HistoryFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.HistoryFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('HistoryReport', $this->request->data['Filter'], 'HistoryReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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

        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $historyReportArr = $this->Analytic->getHistoryReport($conditions);
        $this->loadModel('HistoryReport');
        $this->HistoryReport->virtualFields['bill_count'] = 'count(HistoryReport.id)';
        $this->AutoPaginate->setPaginate($historyReportArr['paginate']);
        $HistoryReport = $this->paginate('HistoryReport');

        /**
         * line chart
         */
        $this->HistoryReport->virtualFields['error_count'] = 'count(HistoryReport.id)';
        $chartData = $this->HistoryReport->find('all', $historyReportArr['chart']);
        $this->Session->write('Report.HistoryReportCondition', $conditions);
        $this->set(compact('tickInterval', 'HistoryReport'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.HistoryReport');
        //
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['HistoryReport']['error_count'] = $value['HistoryReport']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['HistoryReport']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.HistoryReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.HistoryFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('History');
        $lineChartTitle = __('History Report');
        $lineChartxAxisTitle = __('History Date');
        $lineChartyAxisTitle = __('No. of History');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('HistoryReport', $HistoryReport);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/history')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function teller_transaction($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.TellerTransactionFilter');
            $this->Session->delete('Report.TellerTransactionReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.TellerTransactionFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.TellerTransactionFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('TellerTransactionReport', $this->request->data['Filter'], 'CurrentTellerTransactions');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $tellerTransactionArr = $this->Analytic->getTellerTransaction($conditions);
        $this->loadModel('CurrentTellerTransactions');
        //        $this->CurrentTellerTransactions->virtualFields['bill_count'] = 'count(CurrentTellerTransactions.id)';
        $this->AutoPaginate->setPaginate($tellerTransactionArr['paginate']);
        $TellerTransactions = $this->paginate('CurrentTellerTransactions');


        /**
         * line chart
         */
        $this->CurrentTellerTransactions->virtualFields['error_count'] = 'count(CurrentTellerTransactions.id)';
        $chartData = $this->CurrentTellerTransactions->find('all', $tellerTransactionArr['chart']);
        $this->Session->write('Report.TellerTransactionReportCondition', $conditions);
        $this->set(compact('tickInterval', 'TellerTransactions'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.CurrentTellerTransactions');
        //
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['CurrentTellerTransactions']['error_count'] = $value['CurrentTellerTransactions']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['CurrentTellerTransactions']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerTransactionReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.TellerTransactionFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Current Teller');
        $lineChartTitle = __('Current Teller Transaction Report');
        $lineChartxAxisTitle = __('Transaction Date');
        $lineChartyAxisTitle = __('No. of Transaction');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('TellerTransactions', $TellerTransactions);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/teller_transaction')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function coin_inventory($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.CoinInventoryFilter');
            $this->Session->delete('Report.CoinInventoryReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.CoinInventoryFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.CoinInventoryFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('CoinInventoryReport', $this->request->data['Filter'], 'CoinInventory');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $coinInventoryArr = $this->Analytic->getCoinInventory($conditions);

        $this->loadModel('CoinInventory');
        //        $this->CoinInventory->virtualFields['bill_count'] = 'count(CoinInventory.id)';
        $this->AutoPaginate->setPaginate($coinInventoryArr['paginate']);
        $CoinInventorys = $this->paginate('CoinInventory');

        /**
         * line chart
         */
        $this->CoinInventory->virtualFields['error_count'] = 'count(CoinInventory.id)';
        $chartData = $this->CoinInventory->find('all', $coinInventoryArr['chart']);
        $this->Session->write('Report.CoinInventoryReportCondition', $conditions);
        $this->set(compact('tickInterval', 'CoinInventorys'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.CoinInventory');
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['CoinInventory']['error_count'] = $value['CoinInventory']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['CoinInventory']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.CoinInventoryReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CoinInventoryFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Coin Inventory');
        $lineChartTitle = __('Coin Inventory Report');
        $lineChartxAxisTitle = __('Coin Inventory Date');
        $lineChartyAxisTitle = __('No. of Coin Inventory');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('CoinInventorys', $CoinInventorys);
            //Used side activity element as same field are required 
            $transactionData = $this->render('/Elements/reports/coin_inventory')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    /**
     * 2. Automix report for company admin
     * @param type $all
     */
    function automix($all = '')
    {
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.AutomixSettingFilter');
            $this->Session->delete('Report.AutomixSettingReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.AutomixSettingFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.AutomixSettingFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($companyId);
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('AutomixSettingReport', $this->request->data['Filter'], 'AutomixSetting');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['FileProccessingDetail.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if ($this->Session->check('Auth.User.assign_branches')) {
            $assignedBranches = $this->Session->read('Auth.User.assign_branches');
            if (!empty($assignedBranches)) {
                $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $this->loadModel('AutomixSetting');
        //        $this->AutomixSetting->virtualFields['bill_count'] = 'count(AutomixSetting.id)';
        $automixArr = $this->Analytic->getAutomix($conditions);
        $this->AutoPaginate->setPaginate($automixArr['paginate']);
        $AutomixSettings = $this->paginate('AutomixSetting');
        /**
         * line chart
         */
        $this->AutomixSetting->virtualFields['error_count'] = 'count(AutomixSetting.id)';
        $chartData = $this->AutomixSetting->find('all', $automixArr['chart']);
        $this->Session->write('Report.AutomixSettingReportCondition', $conditions);
        $this->set(compact('tickInterval', 'AutomixSettings'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.AutomixSetting');
        //        sort($chartData);
        $tempCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                //                $temp[$date] = $value['AutomixSetting']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['AutomixSetting']['error_count']);
            $tempCount = $tempCount + $value['AutomixSetting']['error_count'];
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.AutomixSettingReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.AutomixSettingFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Automix Settings');
        $lineChartTitle = __('Automix Setting');
        $lineChartxAxisTitle = __('Automix Setting Date');
        $lineChartyAxisTitle = __('No. of Automix Settings');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $AutomixSettings);
            //Used side activity element as same field are required 
            $transactionData = $this->render('/Elements/reports/automix')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function manager_setup($all = '')
    {
        $sessData = getMySessionData();
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.ManagerSetupFilter');
            $this->Session->delete('Report.ManagerSetupReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.ManagerSetupFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.ManagerSetupFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        if (!isSuparAdmin() && isset($sessData['assign_companies'])) {
            $companies = $sessData['assign_companies'];
        }
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('ManagerSetupReport', $this->request->data['Filter'], 'ManagerSetup');

        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
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
        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $managerSetupArr = $this->Analytic->getManagerSetup($conditions);
        $this->loadModel('ManagerSetup');
        //        $this->ManagerSetup->virtualFields['bill_count'] = 'count(ManagerSetup.id)';
        $this->AutoPaginate->setPaginate($managerSetupArr['paginate']);
        $ManagerSetups = $this->paginate('ManagerSetup');


        /**
         * line chart
         */
        $this->ManagerSetup->virtualFields['error_count'] = 'count(ManagerSetup.id)';
        $chartData = $this->ManagerSetup->find('all', $managerSetupArr['chart']);
        $this->Session->write('Report.ManagerSetupReportCondition', $conditions);
        $this->set(compact('tickInterval', 'ManagerSetups'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.ManagerSetup');
        //
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['ManagerSetup']['error_count'] = $value['ManagerSetup']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['ManagerSetup']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.ManagerSetupReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            //            $this->Session->write('Report.ManagerSetupFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Manager Setup');
        $lineChartTitle = __('Manager Setup');
        $lineChartxAxisTitle = __('Manager Setup Date');
        $lineChartyAxisTitle = __('No. of Manager Setup');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $ManagerSetups);
            //Used side activity element as same field are required 
            $transactionData = $this->render('/Elements/reports/manager_setup')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function issue_report($all = '')
    {
        $sessData = getMySessionData();
        $this->set('title', __('Issue Report'));
        $dealers = ClassRegistry::init('User')->getMySupportPerson();
        $this->set(compact('dealers'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.IssueReportFilter');
            $this->Session->delete('Report.IssueReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.IssueReportFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.IssueReportFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companyCond = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
        );
        $companies = ClassRegistry::init('User')->find('list', array(
            'contain' => false,
            'fields' => 'id, first_name',
            'conditions' => $companyCond
        ));
        if (!isSuparAdmin() && isset($sessData['assign_companies'])) {
            $companies = $sessData['assign_companies'];
            $this->request->data['Analytic']['company_id'] = array_keys($companies);
        }
        $this->set(compact('companies'));
        /**
         * get data form table issue_report
         */
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->Analytic->getConditions('IssueReport', $this->request->data['Filter'], 'Ticket');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'ticket_date >= ' => $startDate . ' 00:00:00',
            'ticket_date <= ' => $endDate . ' 23:59:59'
        );
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
        /**
         * add condition for display its data
         */
        /**
         * Line Chart
         * 1. No. of Issue occured
         * 2. Resolved Per Day
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['Ticket.company_id'] = $this->request->data['Analytic']['company_id'];
            }
            if (!empty($this->request->data['Analytic']['dealer_id'])) {
                $conditions['Ticket.dealer_id'] = $this->request->data['Analytic']['dealer_id'];
            }
            if (!empty($this->request->data['Analytic']['error_warning_status'])) {
                $conditions['Ticket.error_warning_status'] = $this->request->data['Analytic']['error_warning_status'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $issueReportArr = $this->Analytic->getIssueReport($conditions);
        $this->loadModel('Ticket');
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $this->AutoPaginate->setPaginate($issueReportArr['paginate']);
        $this->Session->write('Report.IssueReportCondition', $conditions);
        $tickets = $this->paginate('Ticket');
        /**
         * line chart
         */
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $this->Ticket->virtualFields['ticket_date'] = 'DATE_FORMAT(ticket_date,"%Y-%m-%d")';
        $chartData = $this->Ticket->find('all', $issueReportArr['chart']);
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.Ticket');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['ticket_date']));
            $temp[$date] = array((strtotime($value['ticket_date']) * 1000), $value['ticket_count']);
        }
        $resolvedConditions = array(
            'Ticket.status' => 'Closed'
        );
        $resolvedConditions = array_merge($conditions, $resolvedConditions);
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $resolvedChartData = $this->Ticket->find('all', array('conditions' => $resolvedConditions, 'fields' => array('ticket_count', 'ticket_date'), 'order' => 'ticket_date DESC', 'group' => 'DATE_FORMAT(ticket_date,"%Y-%m-%d")', 'contain' => false));
        $temp1 = array();
        $resolvedChartData = Hash::extract($resolvedChartData, '{n}.Ticket');
        sort($resolvedChartData);
        foreach ($resolvedChartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['ticket_date']));
            $temp1[$date] = array((strtotime($value['ticket_date']) * 1000), $value['ticket_count']);
        }
        $this->set(compact('tickInterval', 'tickets'));

        $sendArr = $sendArr1 = array();
        foreach ($xAxisDates as $key => $date) :
            $xAxisDates[$key] = (strtotime($date) * 1000);
            $bkpDate = $date;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            if (array_key_exists($bkpDate, $temp1)) {
                $sendArr1[$key] = $temp1[$bkpDate];
            } else {
                $bkpDate .= " 00:00:00";
                $sendArr1[$key] = array((strtotime($bkpDate) * 1000), 0);
            }
        endforeach;
        $temp = array(
            array(
                'name' => 'No. of issue occurred over date period',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'No. of issue Resolved by support persons',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp = json_encode($temp);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.IssueReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.IssueReportFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $lineChartName = __('Issue Report');
        $lineChartTitle = __('Issue Report');
        $lineChartxAxisTitle = __('Issue Report Date');
        $lineChartyAxisTitle = __('No. of Issues');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/issue_report')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }
    function inventory_management($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $sessionData = getMySessionData();
        $displayflag = false;
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.inventoryFilter');
            $this->Session->delete('Report.inventoryReport');
        }

        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'Inventory');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate .' 23:59:59',
        );
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if (isCompany()) {
            $this->request->data['Analytic']['company_id'] = $company_id;
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (!empty($this->request->data['Analytic'])) {
            if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
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

            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['Inventory.station'] = $this->request->data['Analytic']['station'];
            }
            if(!empty($this->request->data['Analytic']['regiones'])){
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
            }
        }
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones','branches','stations'));
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (!empty($activityReportId)) {
            $conditions['Inventory.activity_report_id'] = $activityReportId;
            $displayflag = true;
        }
        if (!$this->Session->check('Report.inventoryReport')) {
            $this->Session->write('Report.inventoryReport', $this->request->data['Filter']);
        }
        if (isSuparDealer()) {
            $dealerList = ClassRegistry::init('User')->getMyDealerList($sessionData['id'], $sessionData['role'], 'User.id, User.id');
            $dealerList = array_merge(array($sessionData['id'] => $sessionData['id']), $dealerList);
        }
        $this->loadModel('Inventory');
        //        $this->Inventory->virtualFields['bill_count'] = 'count(Inventory.id)';
        $this->Inventory->virtualFields['total_denom_1'] = 'sum(denom_1)';
        $this->Inventory->virtualFields['total_denom_2'] = 'sum(denom_2)';
        $this->Inventory->virtualFields['total_denom_5'] = 'sum(denom_5)';
        $this->Inventory->virtualFields['total_denom_10'] = 'sum(denom_10)';
        $this->Inventory->virtualFields['total_denom_20'] = 'sum(denom_20)';
        $this->Inventory->virtualFields['total_denom_50'] = 'sum(denom_50)';
        $this->Inventory->virtualFields['total_denom_100'] = 'sum(denom_100)';

        $Inventorys = array();
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('Analytic');
        $inventoryManagementArr = $this->Analytic->getInventoryManagement($conditions);
        $this->AutoPaginate->setPaginate($inventoryManagementArr['paginate']);
        $Inventorys = $this->paginate('Inventory');
        // echo '<pre><b></b><br>';
        // print_r($Inventorys);echo '<br>';exit;
        $this->Inventory->virtualFields['error_count'] = 'count(Inventory.id)';
        $this->Inventory->virtualFields['total_denome_1'] = 'sum(denom_1)';
        $this->Inventory->virtualFields['total_denome_2'] = 'sum(denom_2)';
        $this->Inventory->virtualFields['total_denome_5'] = 'sum(denom_5)';
        $this->Inventory->virtualFields['total_denome_10'] = 'sum(denom_10)';
        $this->Inventory->virtualFields['total_denome_20'] = 'sum(denom_20)';
        $this->Inventory->virtualFields['total_denome_50'] = 'sum(denom_50)';
        $this->Inventory->virtualFields['total_denome_100'] = 'sum(denom_100)';
        $this->Inventory->virtualFields['created_date'] = 'DATE_FORMAT(Inventory.created_date,"%Y-%m-%d")';
        $this->set(compact('tickInterval', 'Inventorys', 'temp_station'));
        $chartData = $this->Inventory->find('all', $inventoryManagementArr['chart']);
        $temp = $temp1 = $temp2 = $temp3 = $temp4 = $temp5 = $temp6 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['Inventory']['total_denome_1'] = $value['Inventory']['total_denome_1'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_1']);
            if (isset($temp1[$date])) {
                $value['Inventory']['total_denome_2'] = $value['Inventory']['total_denome_2'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_2']);
            if (isset($temp2[$date])) {
                $value['Inventory']['total_denome_5'] = $value['Inventory']['total_denome_5'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_5']);
            if (isset($temp3[$date])) {
                $value['Inventory']['total_denome_10'] = $value['Inventory']['total_denome_10'] + $temp3[$date][1];
            }
            $temp3[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_10']);
            if (isset($temp4[$date])) {
                $value['Inventory']['total_denome_20'] = $value['Inventory']['total_denome_20'] + $temp4[$date][1];
            }
            $temp4[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_20']);
            if (isset($temp5[$date])) {
                $value['Inventory']['total_denome_50'] = $value['Inventory']['total_denome_50'] + $temp5[$date][1];
            }
            $temp5[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_50']);
            if (isset($temp6[$date])) {
                $value['Inventory']['total_denome_100'] = $value['Inventory']['total_denome_100'] + $temp6[$date][1];
            }
            $temp6[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_100']);
        }


        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();

        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp1)) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp2)) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp3)) {
                $sendArr3[$key] = $temp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp4)) {
                $sendArr4[$key] = $temp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp5)) {
                $sendArr5[$key] = $temp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (array_key_exists($date, $temp6)) {
                $sendArr6[$key] = $temp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }

            $xAxisDates[$key] = array(strtotime($date));
        endforeach;


        $temp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );

        $temp = json_encode($temp);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.inventoryReport', $this->request->data['Filter']);
        }

        $lineChartName = __('Inventory');
        $lineChartTitle = __('Inventory');
        $lineChartxAxisTitle = __('Inventory Date');
        $lineChartyAxisTitle = __('No. of Inventory');
        //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        $displayflag = true;
        $this->set(compact('flag', 'branches', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $Inventorys);
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/inventory_management')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
        $companyCond = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
        );
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
        if (isDealer() && isset($sessionData['assign_companies'])) {
            $companies = $sessionData['assign_companies'];
            $companyCond['User.id'] = array_keys($companies);
            $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
            //            $this->request->data['Analytic']['company_id'] = array_keys($sessionData['assign_companies']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.inventoryFilter', $this->request->data['Analytic']);
            if (!isDealer()) {
                $companyDetail = ClassRegistry::init('User')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('User.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $this->set(compact('companyDetail', 'displayflag', 'companies', 'branches', 'stations'));
        //store id in session ussed for filtering
        // if (isset($this->request->data['Analytic']['company_id'])) {
        //     $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        //     $this->Session->write('FileProccessingDetail.company_id', $this->request->data['Analytic']['company_id']);
        // } else {
        //     $conditions['FileProccessingDetail.company_id'] = $this->Session->read('FileProccessingDetail.company_id');
        // }
        // if (isset($this->request->data['Analytic']['branch_id'])) {
        //     $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
        // }

        // if (!empty($this->request->data['Analytic']['station_id'])) {
        //     $conditions['Inventory.station'] = $this->request->data['Analytic']['station_id'];
        // }
        // echo "HERE";exit;
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
    }
    function inventory_management1($all = '')
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $sessionData = getMySessionData();
        $displayflag = false;
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.inventoryFilter');
            $this->Session->delete('Report.inventoryReport');
        }

        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'Inventory');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >= ' => $startDate,
            'FileProccessingDetail.file_date <= ' => $endDate,
        );
        $sessData = getMySessionData();

        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
        $conditions2['company_id'] = $company_id;
        $this->loadModel('Region');
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->set(compact('regiones'));
        if (isCompany() && empty($this->request->data['Analytic']['company_id'])) {
            $this->request->data['Analytic']['company_id'] = $company_id;
        }

        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        if (!empty($activityReportId)) {
            $conditions['Inventory.activity_report_id'] = $activityReportId;
            $displayflag = true;
        }
        if (!$this->Session->check('Report.inventoryReport')) {
            $this->Session->write('Report.inventoryReport', $this->request->data['Filter']);
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
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
        }
        $this->set(compact('branches',  'stations'));
        if (isset($this->request->data['Analytic']['company_id']) || ($all == 'display') || (isset($activityReportId) && !empty($activityReportId))) {

            if (empty($this->request->data['Analytic']) && $this->Session->check('Report.inventoryFilter')) {
                $this->request->data['Analytic'] = $this->Session->read('Report.inventoryFilter');
            }
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyId = $this->request->data['Analytic']['company_id'];
            }
            if (isCompany()) {
                $this->request->data['Analytic']['company_id'] = getCompanyId();
            }
            if (isSuparDealer()) {
                $dealerList = ClassRegistry::init('User')->getMyDealerList($sessionData['id'], $sessionData['role'], 'User.id, User.id');
                $dealerList = array_merge(array($sessionData['id'] => $sessionData['id']), $dealerList);
            }

            /**
             * Line Chart
             * No. of inventory vs Dates from inventory table
             */
            $this->loadModel('Inventory');
            //        $this->Inventory->virtualFields['bill_count'] = 'count(Inventory.id)';
            $this->Inventory->virtualFields['total_denom_1'] = 'sum(denom_1)';
            $this->Inventory->virtualFields['total_denom_2'] = 'sum(denom_2)';
            $this->Inventory->virtualFields['total_denom_5'] = 'sum(denom_5)';
            $this->Inventory->virtualFields['total_denom_10'] = 'sum(denom_10)';
            $this->Inventory->virtualFields['total_denom_20'] = 'sum(denom_20)';
            $this->Inventory->virtualFields['total_denom_50'] = 'sum(denom_50)';
            $this->Inventory->virtualFields['total_denom_100'] = 'sum(denom_100)';

            $Inventorys = array();
            //store id in session ussed for filtering
            if (isset($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $this->Session->write('FileProccessingDetail.company_id', $this->request->data['Analytic']['company_id']);
            } else {
                $conditions['FileProccessingDetail.company_id'] = $this->Session->read('FileProccessingDetail.company_id');
            }
            if (isset($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            }

            if (!empty($this->request->data['Analytic']['station_id'])) {
                $conditions['Inventory.station'] = $this->request->data['Analytic']['station_id'];
            }

            $this->loadModel('stations');
            $stationData = $this->stations->find('all');
            $temp_station = array();
            foreach ($stationData as $value) {
                $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
            }
            // echo '<pre><b></b><br>';
            // print_r($conditions);echo '<br>';exit;
            $inventoryManagementArr = $this->Analytic->getInventoryManagement($conditions);
            $this->AutoPaginate->setPaginate($inventoryManagementArr['paginate']);
            $Inventorys = $this->paginate('Inventory');

            /**
             * line chart
             */
            $this->Inventory->virtualFields['error_count'] = 'count(Inventory.id)';
            $this->Inventory->virtualFields['total_denome_1'] = 'sum(denom_1)';
            $this->Inventory->virtualFields['total_denome_2'] = 'sum(denom_2)';
            $this->Inventory->virtualFields['total_denome_5'] = 'sum(denom_5)';
            $this->Inventory->virtualFields['total_denome_10'] = 'sum(denom_10)';
            $this->Inventory->virtualFields['total_denome_20'] = 'sum(denom_20)';
            $this->Inventory->virtualFields['total_denome_50'] = 'sum(denom_50)';
            $this->Inventory->virtualFields['total_denome_100'] = 'sum(denom_100)';
            $this->Inventory->virtualFields['created_date'] = 'DATE_FORMAT(Inventory.created_date,"%Y-%m-%d")';
            $this->set(compact('tickInterval', 'Inventorys', 'temp_station'));

            $chartData = $this->Inventory->find('all', $inventoryManagementArr['chart']);
            // echo '<pre><b></b><br>';
            // print_r($chartData);echo '<br>';exit;
            $this->Session->write('Report.inventoryManagementReportReportCondition', $conditions);
            $temp = $temp1 = $temp2 = $temp3 = $temp4 = $temp5 = $temp6 = array();
            foreach ($chartData as $key => $value) {
                $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
                if (isset($temp[$date])) {
                    $value['Inventory']['total_denome_1'] = $value['Inventory']['total_denome_1'] + $temp[$date][1];
                }
                $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_1']);
                if (isset($temp1[$date])) {
                    $value['Inventory']['total_denome_2'] = $value['Inventory']['total_denome_2'] + $temp1[$date][1];
                }
                $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_2']);
                if (isset($temp2[$date])) {
                    $value['Inventory']['total_denome_5'] = $value['Inventory']['total_denome_5'] + $temp2[$date][1];
                }
                $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_5']);
                if (isset($temp3[$date])) {
                    $value['Inventory']['total_denome_10'] = $value['Inventory']['total_denome_10'] + $temp3[$date][1];
                }
                $temp3[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_10']);
                if (isset($temp4[$date])) {
                    $value['Inventory']['total_denome_20'] = $value['Inventory']['total_denome_20'] + $temp4[$date][1];
                }
                $temp4[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_20']);
                if (isset($temp5[$date])) {
                    $value['Inventory']['total_denome_50'] = $value['Inventory']['total_denome_50'] + $temp5[$date][1];
                }
                $temp5[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_50']);
                if (isset($temp6[$date])) {
                    $value['Inventory']['total_denome_100'] = $value['Inventory']['total_denome_100'] + $temp6[$date][1];
                }
                $temp6[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Inventory']['total_denome_100']);
            }


            $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();

            foreach ($xAxisDates as $key => $date) :
                $data = 0;
                $bkpdate = $date;
                if (array_key_exists($date, $temp)) {
                    $sendArr[$key] = $temp[$date];
                } else {
                    $sendArr[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp1)) {
                    $sendArr1[$key] = $temp1[$date];
                } else {
                    $sendArr1[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp2)) {
                    $sendArr2[$key] = $temp2[$date];
                } else {
                    $sendArr2[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp3)) {
                    $sendArr3[$key] = $temp3[$date];
                } else {
                    $sendArr3[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp4)) {
                    $sendArr4[$key] = $temp4[$date];
                } else {
                    $sendArr4[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp5)) {
                    $sendArr5[$key] = $temp5[$date];
                } else {
                    $sendArr5[$key] = array((strtotime($date) * 1000), 0);
                }

                $date = $bkpdate;
                if (array_key_exists($date, $temp6)) {
                    $sendArr6[$key] = $temp6[$date];
                } else {
                    $sendArr6[$key] = array((strtotime($date) * 1000), 0);
                }

                $xAxisDates[$key] = array(strtotime($date));
            endforeach;


            $temp = array(
                array(
                    'name' => 'Denom 1',
                    'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 2',
                    'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 5',
                    'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 10',
                    'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 20',
                    'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 50',
                    'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denom 100',
                    'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                )
            );

            $temp = json_encode($temp);

            $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
            $yAxisData = array();
            $this->set(compact('temp', 'xAxisDates'));
            if (!empty($this->request->data['Filter'])) {
                $this->Session->write('Report.inventoryReport', $this->request->data['Filter']);
            }

            $lineChartName = __('Inventory');
            $lineChartTitle = __('Inventory');
            $lineChartxAxisTitle = __('Inventory Date');
            $lineChartyAxisTitle = __('No. of Inventory');
            //        $this->set(compact('billTypes', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
            $displayflag = true;
            $this->set(compact('flag', 'branches', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
            if ($this->request->is('ajax')) {
                $this->layout = false;
                $this->set('activity', $Inventorys);
                //Used side activity element as same field are required 

                $transactionData = $this->render('/Elements/reports/inventory_management')->body();
                $options = array(
                    'name' => $lineChartName,
                    'title' => $lineChartTitle,
                    'xTitle' => $lineChartxAxisTitle,
                    'yTitle' => $lineChartyAxisTitle
                );
                echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
                exit;
            }
        }
        $companyCond = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
        );
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
        if (isDealer() && isset($sessionData['assign_companies'])) {
            $companies = $sessionData['assign_companies'];
            $companyCond['User.id'] = array_keys($companies);
            $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
            //            $this->request->data['Analytic']['company_id'] = array_keys($sessionData['assign_companies']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.inventoryFilter', $this->request->data['Analytic']);
            if (!isDealer()) {
                $companyDetail = ClassRegistry::init('User')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('User.id' => $this->Session->read('FileProccessingDetail.company_id'))));
            }
        }
        // $branches = array();
        // if (isset($this->request->data['Analytic']['company_id']) && !is_array($this->request->data['Analytic']['company_id'])) {

        //     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($this->request->data['Analytic']['company_id']);
        // }
        $stations = array();
        if (isset($this->request->data['Analytic']['station_id']) && !is_array($this->request->data['Analytic']['company_id'])) {
            $branchId = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($branchId);
            //$stations = $this->CompanyBranch->getStationList($branchId);
        }
        $this->set(compact('companyDetail', 'displayflag', 'companies', 'branches', 'stations'));
    }

    function inventory_by_teller($all = '')
    {

        $sessionData = getMySessionData();
        $displayflag = false;
        $this->loadModel('TellerSetup');
        if (!empty($this->request->data)) {
            //Set below session for export csv
            $this->Session->write('Report.inventoryByTellerId', $this->request->data['TellerSearch']['Teller']);
            $this->Session->write('Report.inventoryByTellerDate', $this->request->data['TellerSearch']['Date']);
            $tellerSearchId = isset($this->request->data['TellerSearch']['Teller']) ? $this->request->data['TellerSearch']['Teller'] : 0;
            $tellerDate = '0000-00-00';
            if (isset($this->request->data['TellerSearch']['Date'])) {
                $tellerDate = date('Y-m-d', strtotime($this->request->data['TellerSearch']['Date']));
            }
            $tellerConditions = array(
                'TellerSetup.teller_id' => $tellerSearchId,
                'date(TellerSetup.datetime)' => $tellerDate
            );
            if (isCompany()) {
                $tellerConditions['FileProccessingDetail.company_id'] = getCompanyId();
            }
            $fileProcessingID = $this->TellerSetup->find('list', array(
                'contain' => array('FileProccessingDetail' => array('id', 'file_date', 'company_id', 'branch_id')),
                'conditions' => $tellerConditions,
                'fields' => 'file_processing_detail_id, datetime'
            ));
            $stations = array();
            if (!empty($all)) {
                $this->Session->delete('Report.inventoryByTellerFilter');
                $this->Session->delete('Report.inventoryByTellerReport');
            }
            if (empty($this->request->data['Analytic']) && $this->Session->check('Report.inventoryByTellerFilter')) {
                $this->request->data['Analytic'] = $this->Session->read('Report.inventoryByTellerFilter');
            }
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyId = $this->request->data['Analytic']['company_id'];
            }

            $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
            $conditions = $this->__getConditions('inventoryByTellerReport', $this->request->data['Filter'], 'Inventory');

            $from = $conditions['from'];
            $xAxisDates = $conditions['xAxisDates'];
            $tickInterval = $conditions['tickInterval'];
            $startDate = $conditions['start_date'];
            $endDate = $conditions['end_date'];
            $conditions = $conditions['conditions'];
            if (isCompany()) {
                //            $company_condtions['User.dealer_id'] = $this->Session->read('Auth.User.dealer_id');
                $company_condtions['User.dealer_id'] = $sessionData['dealer_id'];
            }
            if (isDealer()) {
                if (isSuparDealer()) {
                    //                $dealerList = ClassRegistry::init('User')->getMyDealerList($this->Session->read('Auth.User.id'),$this->Session->read('Auth.User.role'),'User.id, User.id');
                    //                $dealerList = array_merge(array($this->Session->read('Auth.User.id')=>$this->Session->read('Auth.User.id')),$dealerList);
                    $dealerList = ClassRegistry::init('User')->getMyDealerList($sessionData['id'], $sessionData['role'], 'User.id, User.id');
                    $dealerList = array_merge(array($sessionData['id'] => $sessionData['id']), $dealerList);
                    $company_condtions['User.dealer_id'] = $dealerList;
                }
                if (isAdminDealer()) {
                    //                $company_condtions['User.dealer_id'] = $this->Session->read('Auth.User.id');
                    $company_condtions['User.dealer_id'] = $sessionData['id'];
                }
            }
            $companyList = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, id', 'conditions' => $company_condtions));
            $fileProcessingIds = ClassRegistry::init('FileProccessingDetail')->find('list', array('contain' => false, 'fields' => 'id, id', 'conditions' => array('company_id' => $companyList)));

            /**
             * Line Chart
             * No. of automix vs Dates from automix_network table
             */
            //      
            if (empty($this->request->data['Filter'])) {
                $this->request->data['Filter']['from'] = $from;
                $this->request->data['Filter']['start_date'] = $startDate;
                $this->request->data['Filter']['end_date'] = $endDate;
            }

            $this->loadModel('Inventory');
            $conditions = array();
            $conditions['Inventory.file_processing_detail_id'] = array_keys($fileProcessingID);
            $inventoryByTellerArr = $this->Analytic->getInventoryByTeller($conditions);
            $this->AutoPaginate->setPaginate($inventoryByTellerArr['paginate']);
            $Inventorys = $this->paginate('Inventory');
            /**
             * line chart
             */
            $this->Inventory->virtualFields['error_count'] = 'count(Inventory.id)';
            $this->Inventory->virtualFields['total_denom_1'] = 'sum(denom_1)';
            $this->Inventory->virtualFields['total_denom_2'] = 'sum(denom_2)';
            $this->Inventory->virtualFields['total_denom_5'] = 'sum(denom_5)';
            $this->Inventory->virtualFields['total_denom_10'] = 'sum(denom_10)';
            $this->Inventory->virtualFields['total_denom_20'] = 'sum(denom_20)';
            $this->Inventory->virtualFields['total_denom_50'] = 'sum(denom_50)';
            $this->Inventory->virtualFields['total_denom_100'] = 'sum(denom_100)';
            $this->set(compact('tickInterval', 'Inventorys'));
            $chartData = $this->Inventory->find('all', $inventoryByTellerArr['chart']);
            $this->Session->write('Report.InventoryByTellerReportCondition', $conditions);
            //            $chartData = Hash::extract($chartData, '{n}.Inventory');
            //            sort($chartData);
            $temp = $temp1 = $temp2 = $temp3 = $temp4 = $temp5 = $temp6 = array();
            foreach ($chartData as $key => $value) {
                if (isset($fileProcessingID[$value['FileProccessingDetail']['id']])) {

                    if (!empty($fileProcessingID)) {
                        $selecDate = date('Y-m-d h:00 A', strtotime($fileProcessingID[$value['FileProccessingDetail']['id']]));
                    } else {
                        $selecDate = date('Y-m-d h:00 A');
                        if (!empty($this->request->data['TellerSearch']['Date'])) {
                            $selecDate = date('Y-m-d h:00 A', strtotime($this->request->data['TellerSearch']['Date']));
                        }
                    }
                    $date = date('Y-m-d h:00 A', strtotime($selecDate));
                    if (isset($temp[$date])) {
                        $value['Inventory']['total_denom_1'] = $value['Inventory']['total_denom_1'] + $temp[$date][1];
                    }
                    $temp[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_1']);

                    if (isset($temp1[$date])) {
                        $value['Inventory']['total_denom_2'] = $value['Inventory']['total_denom_2'] + $temp1[$date][1];
                    }
                    $temp1[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_2']);
                    if (isset($temp2[$date])) {
                        $value['Inventory']['total_denom_5'] = $value['Inventory']['total_denom_5'] + $temp2[$date][1];
                    }
                    $temp2[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_5']);
                    if (isset($temp3[$date])) {
                        $value['Inventory']['total_denom_10'] = $value['Inventory']['total_denom_10'] + $temp3[$date][1];
                    }
                    $temp3[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_10']);
                    if (isset($temp4[$date])) {
                        $value['Inventory']['total_denom_20'] = $value['Inventory']['total_denom_20'] + $temp4[$date][1];
                    }
                    $temp4[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_20']);
                    if (isset($temp5[$date])) {
                        $value['Inventory']['total_denom_50'] = $value['Inventory']['total_denom_50'] + $temp5[$date][1];
                    }
                    $temp5[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_50']);
                    if (isset($temp6[$date])) {
                        $value['Inventory']['total_denom_100'] = $value['Inventory']['total_denom_100'] + $temp6[$date][1];
                    }
                    $temp6[$date] = array((strtotime($selecDate) * 1000), $value['Inventory']['total_denom_100']);
                }
            }
            $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
            //            $selectedDate = date('Y-m-d', strtotime($fileProcessingID[$value['FileProccessingDetail']['id']]));
            $selectedDate = date('Y-m-d');
            if (!empty($this->request->data['TellerSearch']['Date'])) {
                $selectedDate = date('Y-m-d', strtotime($this->request->data['TellerSearch']['Date']));
            }
            $xAxisDates = date_range($selectedDate . ' 06:00:00', $selectedDate . ' 18:59:59', '+1 hour', 'Y-m-d h:i A');
            foreach ($xAxisDates as $key => $date) :
                $data = 0;
                $bkpdate = $date;

                if (isset($temp[$date])) {
                    $sendArr[$key] = $temp[$date];
                } else {
                    $sendArr[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp1)) {
                    $sendArr1[$key] = $temp1[$date];
                } else {
                    $sendArr1[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp2)) {
                    $sendArr2[$key] = $temp2[$date];
                } else {
                    $sendArr2[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp3)) {
                    $sendArr3[$key] = $temp3[$date];
                } else {
                    $sendArr3[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp4)) {
                    $sendArr4[$key] = $temp4[$date];
                } else {
                    $sendArr4[$key] = array((strtotime($date) * 1000), 0);
                }
                $date = $bkpdate;
                if (array_key_exists($date, $temp5)) {
                    $sendArr5[$key] = $temp5[$date];
                } else {
                    $sendArr5[$key] = array((strtotime($date) * 1000), 0);
                }

                $date = $bkpdate;
                if (array_key_exists($date, $temp6)) {
                    $sendArr6[$key] = $temp6[$date];
                } else {
                    $sendArr6[$key] = array((strtotime($date) * 1000), 0);
                }

                $xAxisDates[$key] = array(strtotime($date));
            endforeach;

            $temp = array(
                array(
                    'name' => 'Denome 1',
                    'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 2',
                    'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 5',
                    'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 10',
                    'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 20',
                    'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 50',
                    'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                ),
                array(
                    'name' => 'Denome 100',
                    'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                    'pointInterval' => $tickInterval
                )
            );
            $temp = json_encode($temp);

            $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
            $yAxisData = array();
            $this->set(compact('temp', 'xAxisDates'));
            if (!empty($this->request->data['Filter'])) {
                $this->Session->write('Report.inventoryByTellerReport', $this->request->data['Filter']);
            }
            $companyDetail = array();
            if (!empty($this->request->data['Analytic'])) {
                $this->Session->write('Report.inventoryByTellerFilter', $this->request->data['Analytic']);
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
            $lineChartName = __('Inventory');
            $lineChartTitle = __('Inventory');
            $lineChartxAxisTitle = __('Inventory Date');
            $lineChartyAxisTitle = __('No. of Inventory');
            $displayflag = true;
            $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
            if ($this->request->is('ajax')) {
                $this->layout = false;
                $this->set('activity', $Inventorys);
                //Used side activity element as same field are required
                $transactionData = $this->render('/Elements/reports/inventory_management')->body();
                $options = array(
                    'name' => $lineChartName,
                    'title' => $lineChartTitle,
                    'xTitle' => $lineChartxAxisTitle,
                    'yTitle' => $lineChartyAxisTitle
                );
                echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
                exit;
            }
        }
        $tellerConditions = array('NOT' => array('TellerSetup.teller_id' => 'NULL'));
        if (!empty($this->request->data)) {
            //            $tellerConditions['FileProccessingDetail.file_date >= '] = $startDate;
            //            $tellerConditions['FileProccessingDetail.file_date <= '] = $endDate;
        }
        if (isCompany()) {
            $tellerConditions['FileProccessingDetail.company_id'] = getCompanyId();
        }
        $tellerId = $this->TellerSetup->find('list', array(
            'contain' => array('FileProccessingDetail'),
            'fields' => 'teller_id, teller_id',
            'conditions' => $tellerConditions
        ));
        $this->set(compact('displayflag', 'tellerId'));
    }

    function user_performance($all = '')
    {
        $this->set('title', __('Users Performance'));
        $dealers = ClassRegistry::init('User')->getMySupportPerson();
        $this->set(compact('dealers'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.UserPerformanceReportFilter');
            $this->Session->delete('Report.UserPerformanceReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.UserPerformanceReportFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.UserPerformanceReportFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companyCond = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
        );
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
        $this->set(compact('companies'));
        /**
         * get data form table issue_report
         */
        $this->loadModel('Ticket');
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->Analytic->getConditions('UserPerformanceReport', $this->request->data['Filter'], 'Ticket');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'updated >= ' => $startDate . ' 00:00:00',
            'updated <= ' => $endDate . ' 23:59:59'
        );
        /**
         * add condition for display its data
         */
        /**
         * Bar Chart
         *
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['dealer_id'])) {
                $conditions['Ticket.dealer_id'] = $this->request->data['Analytic']['dealer_id'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        /**
         * bar chart
         */
        /**
         * get support persons
         */
        $dealerList = $this->Ticket->find('list', array(
            'conditions' => array(
                'Ticket.status' => 'Closed'
            ),
            'fields' => 'dealer_id, dealer_id'
        ));
        $dealerList = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id,first_name', 'conditions' => array('User.id' => $dealerList)));
        $this->set('dealers1', $dealerList);
        $conditions = array('Ticket.status' => 'Closed');
        //        $conditions['Ticket.status'] = 'Closed';
        //        $this->Ticket->virtualFields['dealer_work_hour'] = '.dealer_work_hour * 60';
        $this->Ticket->virtualFields['max_hour'] = 'max(Ticket.dealer_work_hour)';
        $this->Ticket->virtualFields['min_hour'] = 'min(Ticket.dealer_work_hour)';
        $this->Ticket->virtualFields['avg_hour'] = 'avg(Ticket.dealer_work_hour)';
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $fields = array(
            'ticket_count',
            'dealer_id',
            'max_hour',
            'min_hour',
            'avg_hour',
        );
        $ticketsData = $this->Ticket->find('all', array(
            'group' => 'dealer_id',
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => 'ticket_date DESC',
            'contain' => false
        ));
        $minArr = $maxArr = $avgArr = array();
        $tempMinArr = $tempMaxArr = $tempAvgArr = array();
        foreach ($ticketsData as $ticket) {
            $tempMinArr[$ticket['Ticket']['dealer_id']] = getWorkedHours($ticket['Ticket']['min_hour']);
            $tempMaxArr[$ticket['Ticket']['dealer_id']] = getWorkedHours($ticket['Ticket']['max_hour']);
            $tempAvgArr[$ticket['Ticket']['dealer_id']] = getWorkedHours($ticket['Ticket']['avg_hour']);
        }
        foreach ($dealerList as $dealId => $dealName) {
            $minArr[] = isset($tempMinArr[$dealId]) ? $tempMinArr[$dealId] : 0;
            $maxArr[] = isset($tempMaxArr[$dealId]) ? $tempMaxArr[$dealId] : 0;
            $avgArr[] = isset($tempAvgArr[$dealId]) ? $tempAvgArr[$dealId] : 0;
        }

        $this->set(compact('tickInterval', 'tickets'));
        $xAxisDates = array_values($dealerList);
        $temp = array(
            array(
                'name' => 'Min',
                'data' => json_encode($minArr, JSON_NUMERIC_CHECK)
            ),
            array(
                'name' => 'Avg',
                'data' => json_encode($avgArr, JSON_NUMERIC_CHECK)
            ),
            array(
                'name' => 'Max',
                'data' => json_encode($maxArr, JSON_NUMERIC_CHECK)
            )
        );
        $temp = json_encode($temp);
        $xAxisDates = json_encode($xAxisDates);
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.UserPerformanceReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.UserPerformanceReportFilter', $this->request->data['Analytic']);
        }
        $lineChartName = __('Support Person Performance Report');
        $lineChartTitle = __('Support Person Performance Report');
        $lineChartxAxisTitle = __('Support Person');
        $lineChartyAxisTitle = __('No. of hours');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function client_issue($all = '')
    {
        $sessData = getMySessionData();
        $this->set('title', __('Client issue Report'));
        $dealers = ClassRegistry::init('User')->getMySupportPerson();
        $this->set(compact('dealers'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.ClientIssueReportFilter');
            $this->Session->delete('Report.ClientIssueReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.ClientIssueReportFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.ClientIssueReportFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companyCond = array(
            'User.role' => COMPANY,
            'User.user_type' => SUPAR_ADM,
        );
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => $companyCond));
        //        debug($this->request->data['Analytic']['company_id']);exit;
        $selectedCompanies = '';
        if (!empty($this->request->data['Analytic']['company_id']) && !is_array($this->request->data['Analytic']['company_id'])) {
            $selectedCompanies = $this->request->data['Analytic']['company_id'];
        }

        if (isDealer() && isset($sessData['assign_companies'])) {
            $companies = $sessData['assign_companies'];
            $this->request->data['Analytic']['company_id'] = array_keys($sessData['assign_companies']);
        }

        $this->set(compact('companies', 'selectedCompanies'));
        /**
         * get data form table issue_report
         */
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $conditions = $this->Analytic->getConditions('ClientIssueReport', $this->request->data['Filter'], 'Ticket');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'ticket_date >= ' => $startDate . ' 00:00:00',
            'ticket_date <= ' => $endDate . ' 23:59:59'
        );
        /**
         * add condition for display its data
         */
        /**
         * Line Chart
         * 1. No. of Issue occured
         * 2. Resolved Per Day
         */

        if(($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Super') || ($sessData['role'] == 'Dealer') && ($sessData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessData['id']);
            $conditions['Ticket.company_id'] = $company_list;
        }elseif (($sessData['role'] == 'Admin') && ($sessData['user_type'] == 'Super')) {
            unset($conditions['company_id']);
        }
        // if (!empty($this->request->data['Analytic'])) {
        //     $filePCond = $filePIds = array();
        //     if (!empty($this->request->data['Analytic']['company_id'])) {
        //         $conditions['Ticket.company_id'] = $this->request->data['Analytic']['company_id'];
        //     }
        //     if (!empty($this->request->data['Analytic']['dealer_id'])) {
        //         $conditions['Ticket.dealer_id'] = $this->request->data['Analytic']['dealer_id'];
        //     }
        // }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $clientIssueArr = $this->Analytic->getClientIssue($conditions);
        
        $this->loadModel('Ticket');
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $this->AutoPaginate->setPaginate($clientIssueArr['paginate']);
        $tickets = $this->paginate('Ticket');
        /**
         * line chart
         */
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $this->Ticket->virtualFields['ticket_date'] = 'DATE_FORMAT(ticket_date,"%Y-%m-%d")';
        $chartData = $this->Ticket->find('all', $clientIssueArr['chart']);
      
        $this->Session->write('Report.ClientIssueReportCondition', $conditions);
        //        $chartData = $this->Ticket->find('all', array('conditions' => $conditions, 'fields' => array('ticket_count', 'ticket_date'), 'order' => 'ticket_date DESC', 'group' => 'DATE_FORMAT(ticket_date,"%Y-%m-%d"), company_id', 'contain' => false));
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.Ticket');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['ticket_date']));
            $temp[$date] = array((strtotime($value['ticket_date']) * 1000), $value['ticket_count']);
        }
        $resolvedConditions = array(
            'Ticket.status' => 'Closed'
        );
        $resolvedConditions = array_merge($conditions, $resolvedConditions);
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $resolvedChartData = $this->Ticket->find('all', array('conditions' => $resolvedConditions, 'fields' => array('ticket_count', 'ticket_date'), 'order' => 'ticket_date DESC', 'group' => 'DATE_FORMAT(ticket_date,"%Y-%m-%d")', 'contain' => false));
        $temp1 = array();
        $resolvedChartData = Hash::extract($resolvedChartData, '{n}.Ticket');
        sort($resolvedChartData);
        foreach ($resolvedChartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['ticket_date']));
            $temp1[$date] = array((strtotime($value['ticket_date']) * 1000), $value['ticket_count']);
        }
        $this->set(compact('tickInterval', 'tickets'));

        $sendArr = $sendArr1 = array();
        foreach ($xAxisDates as $key => $date) :
            $xAxisDates[$key] = (strtotime($date) * 1000);
            $bkpDate = $date;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            if (array_key_exists($bkpDate, $temp1)) {
                $sendArr1[$key] = $temp1[$bkpDate];
            } else {
                $bkpDate .= " 00:00:00";
                $sendArr1[$key] = array((strtotime($bkpDate) * 1000), 0);
            }
        endforeach;
        $temp = array(
            array(
                'name' => 'No. of issue occurred over date period',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp = json_encode($temp);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.ClientIssueReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.ClientIssueReportFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Client Issue Report');
        $lineChartTitle = __('Client Issue Report');
        $lineChartxAxisTitle = __('Client Issue Report Date');
        $lineChartyAxisTitle = __('No. of Issue Report By Client');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            //Used side activity element as same field are required 

            $transactionData = $this->render('/Elements/reports/client_issue')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function availability_report()
    {
        $this->set('title', __('Availability Report'));
        $this->render('/Pages/unknown');
    }

    function denom_usage()
    {
        $this->set('title', __('Denom Usage'));
        $this->render('/Pages/unknown');
    }

    function inventory()
    {
        $this->set('title', __('Inventory'));
        $this->render('/Pages/unknown');
    }

    function transactions()
    {
        /**
         * No. Of Transactions
         */
        if ($this->Session->check('Report.NoOfTransaction') && empty($this->request->data)) {
            $this->request->data = $this->Session->read('Report.NoOfTransaction');
        }
        if (empty($this->request->data)) {
            $this->request->data['from'] = 'last_7days';
            $this->request->data['start_date'] = date('Y-m-d', strtotime('-6 days'));
            $this->request->data['end_date'] = date('Y-m-d');
        }
        $from = !empty($this->request->data['from']) ? $this->request->data['from'] : 'last_7days';
        $xAxisDates = array();
        if ($from == 'today') {
            $xAxisDates = [date('Y-m-d')];
        } elseif ($from == 'last_7days') {
            $xAxisDates = date_range(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'), '+1 day');
        } elseif ($from == 'last_15days') {
            $xAxisDates = date_range(date('Y-m-d', strtotime('-14 days')), date('Y-m-d'), '+1 day');
        } elseif ($from == 'last_months') {
            $xAxisDates = date_range(date('Y-m-d', strtotime('-29 days')), date('Y-m-d'), '+1 day');
        } elseif ($from == 'last_3months') {
            $xAxisDates = date_range(date('Y-m-d', strtotime('-3 month')), date('Y-m-d'), '+1 day');
        } else {
            $xAxisDates = date_range($this->request->data['start_date'], $this->request->data['end_date'], '+1 day');
        }
        $startDate = !empty($this->request->data['start_date']) ? $this->request->data['start_date'] : date('Y-m-d', strtotime('-3 month'));
        $endDate = !empty($this->request->data['end_date']) ? $this->request->data['end_date'] : date('Y-m-d');
        $conditions = array();
        if (!empty($startDate) && !empty($endDate)) {
            $conditions = array('created_date >= ' => $startDate, 'created_date <= ' => $endDate);
        }
        $this->loadModel('TransactionDetail');
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'conditions' => $conditions,
            'order' => 'created_date DESC',
            'contain' => false
        ));
        $transactions = $this->paginate('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(id)';
        $transactionPie = $this->TransactionDetail->find('list', array('conditions' => $conditions, 'fields' => array('status', 'transaction_count'), 'order' => 'status ASC', 'group' => 'status', 'contain' => false));
        $transactionTypes = array('C' => __('Complete'), 'I' => __('Incomplete'));
        $tempArr = array();
        $transactionPieCount = 0;
        foreach ($transactionPie as $value) {
            $transactionPieCount = $transactionPieCount + $value;
        }
        foreach ($transactionTypes as $typeId => $typeName) {
            if (isset($transactionPie[$typeId])) {
                $tempArr[] = array(
                    'name' => $typeName,
                    'y' => getPercentage($transactionPieCount, $transactionPie[$typeId])
                );
            } else {
                $tempArr[] = array(
                    'name' => $typeName,
                    'y' => 0
                );
            }
        }
        $transactionPie = $tempArr;
        $chartData = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => array('transaction_count', 'created_date'), 'order' => 'created_date DESC', 'group' => 'DATE_FORMAT(created_date,"%Y-%m-%d")', 'contain' => false));
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $transactionPie = json_encode($transactionPie);
        $this->set(compact('transactions', 'transactionCategories', 'transactionTypes', 'transactionPie'));

        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.TransactionDetail');
        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['created_date']));
            $temp[$date] = array($date, $value['transaction_count']);
            if (empty($startDate) && $value['created_date'] != '0000-00-00 00:00:00') {
                $startDate = date('Y-m-d', strtotime($value['created_date']));
            }
            $endDate = date('Y-m-d', strtotime($value['created_date']));
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = 0;
            }
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates', 'yAxisData'));
        if (!empty($this->request->data)) {
            $this->Session->write('Report.NoOfTransaction', $this->request->data);
        }
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/transaction_details')->body();
            echo json_encode(array('status' => 'success', 'data' => $temp, 'xAxisDates' => $xAxisDates, 'yAxisData' => $yAxisData, 'transactionData' => $transactionData, 'transactionPie' => $transactionPie));
            exit;
        }
        $this->set('title', __('No. of Transactions'));
    }

    function getTransactionDetail($id = null)
    {
        $id = decrypt($id);
        $this->layout = false;
        $transaction = ClassRegistry::init('TransactionDetail')->find('first', array('contain' => false, 'conditions' => array('TransactionDetail.id' => $id)));
        $this->set(compact('transaction'));
    }

    function getErrorDetail($id = null, $type = 'company')
    {
        $id = decrypt($id);
        $conditions = $errors = $companyDetail = array();
        $this->layout = false;
        if ($type == 'company') {
            $this->loadModel('ErrorDetail');
            $this->ErrorDetail->id = $id;
            $createDate = $this->ErrorDetail->field('entry_timestamp');
            $errors = ClassRegistry::init('ErrorDetail')->find('all', array(
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id'),
                        'Company' => array('Dealer'),
                        'Branch' => array('id', 'name'),
                    )
                ),
                'conditions' => array(
                    'DATE_FORMAT(ErrorDetail.entry_timestamp,"%Y-%m-%d")' => date('Y-m-d', strtotime($createDate))
                )
            ));
        } else {
            $errors = ClassRegistry::init('ErrorDetail')->find('all', array(
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id'),
                        'Company' => array('Dealer'),
                        'Branch' => array('id', 'name'),
                    )
                ),
                'conditions' => array(
                    'ErrorDetail.id' => $id
                )
            ));
        }
        $this->set(compact('errors', 'companyDetail'));
    }

    function getActivityDetail($billActId = null)
    {
        $billActId = decrypt($billActId);
        $bill = array();
        $this->layout = false;
        $bill = ClassRegistry::init('BillsActivityReport')->find('first', array(
            'contain' => array('FileProccessingDetail' => array(
                'fields' => array('id', 'filename', 'company_id', 'branch_id'),
                'Company' => array(
                    'fields' => array('id', 'first_name', 'email', 'last_name'),
                    'Dealer' => array('id', 'first_name', 'email', 'last_name')
                ),
                'Branch' => array('id', 'name'),
            ), 'ActivityReport', 'BillType'),
            'conditions' => array('BillsActivityReport.id' => $billActId)
        ));
        $this->set(compact('bill'));
    }

    function download_file($fileName = '')
    {
        if (empty($fileName)) {
            $this->Message->setWarning(__('Invalid file'), array('controller' => 'analytics', 'action' => 'file_processing'));
        }
        $filePath = getFileProcessPath($fileName, true);
        $this->layout = false;
        if (!is_dir($filePath) && is_file($filePath) && file_exists($filePath)) {
            $this->response->file($filePath, array('download' => true, 'name' => $fileName . '.pdf'));
            return $this->response;
            exit;
        }
        return $this->redirect(array('controller' => 'analytics', 'action' => 'file_processing'));
    }

    function file_processing_pdf()
    {
        $sessionData = getMySessionData();
        $conditions = $this->Session->read('Report.FileProcessingReportCondition');

        $this->loadModel('FileProccessingDetail');
        $this->layout = null;
        $this->autoRender = false;
        /**
         * save chart image url
         */
        if ($this->request->is('ajax')) {
            $arrResponse = array('status' => 'success', 'filename' => '');
            $lineChartUrl = $this->request->data['lineChartUrl'];
            $pieChartUrl = $this->request->data['pieChartUrl'];
            $displayFlag = false;
            $companyDetail = array();
            $fileProcessingArr = $this->Analytic->getFileProcessing($conditions, null, 'paginate');
            $this->FileProccessingDetail->virtualFields['fileProcessed'] = "SUM(IF(processing_endtime != '0000-00-00 00:00:00' and processing_endtime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done
            $this->FileProccessingDetail->virtualFields['no_of_file_received'] = "SUM(IF(processing_endtime = '0000-00-00 00:00:00' and processing_starttime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done
            $this->loadModel('ErrorDetail');
            $this->ErrorDetail->virtualFields['no_of_errors'] = "count(ErrorDetail.id)";
            $this->loadModel('TransactionDetail');
            $this->TransactionDetail->virtualFields['no_of_deposit'] = "count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))";
            $this->TransactionDetail->virtualFields['no_of_withdrawal'] = "count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
            $this->TransactionDetail->virtualFields['total_cash_deposit'] = "sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.other_cash_deposited,0))";
            $this->TransactionDetail->virtualFields['total_cash_withdrawal'] = "sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
            $this->TransactionDetail->virtualFields['total_cash_requested'] = "sum(TransactionDetail.amount_requested)";
            $this->TransactionDetail->virtualFields['no_of_report'] = "count(TransactionDetail.history_report_id)";
            $this->TransactionDetail->virtualFields['no_of_transaction'] = "count(TransactionDetail.id)";
            ClassRegistry::init('AutomixSetting')->virtualFields['no_of_automix'] = "count(AutomixSetting.id)";
            ClassRegistry::init('BillsActivityReport')->virtualFields['no_of_billactivity'] = "count(BillsActivityReport.id)";
            ClassRegistry::init('BillAdjustment')->virtualFields['no_of_billadjustment'] = "count(BillAdjustment.id)";
            ClassRegistry::init('BillCount')->virtualFields['no_of_billcount'] = "count(BillCount.id)";
            ClassRegistry::init('BillHistory')->virtualFields['no_of_billhistory'] = "count(BillHistory.id)";
            ClassRegistry::init('CoinInventory')->virtualFields['no_of_coininventory'] = "count(CoinInventory.id)";
            ClassRegistry::init('CurrentTellerTransactions')->virtualFields['no_of_currTellerTrans'] = "count(CurrentTellerTransactions.id)";
            ClassRegistry::init('HistoryReport')->virtualFields['no_of_historyReport'] = "count(HistoryReport.id)";
            ClassRegistry::init('ManagerSetup')->virtualFields['no_of_mgrSetup'] = "count(ManagerSetup.id)";
            ClassRegistry::init('NetCashUsageActivityReport')->virtualFields['no_of_netCashUsage'] = "count(NetCashUsageActivityReport.id)";
            ClassRegistry::init('SideActivityReport')->virtualFields['no_of_sideActivity'] = "count(SideActivityReport.id)";
            ClassRegistry::init('TellerActivityReport')->virtualFields['no_of_tellerActivity'] = "count(TellerActivityReport.id)";
            ClassRegistry::init('ValutBuy')->virtualFields['no_of_vaultBuy'] = "count(ValutBuy.id)";
            ClassRegistry::init('TellerSetup')->virtualFields['no_of_teller_setup'] = "count(TellerSetup.id)";
            $processFiles = $this->FileProccessingDetail->find('all', $fileProcessingArr['paginate']);
            $this->set(compact('displayFlag', 'companyDetail', 'processFiles'));
            /**
             * gerenerate url
             */
            App::import('Vendor', 'tcpdf/tcpdf');

            $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetHeaderData('', '', 'File Processing View', 'Company Name :' . $sessionData['first_name']); //ToDo: Change Header Here
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 8);
            $htmlDataTable = $this->render('/Analytics/file_processing_pdf')->body();
            if (!checkImageAvailable($lineChartUrl) || !checkImageAvailable($pieChartUrl)) {
                $arrResponse = array('status' => 'fail');
                echo json_encode($arrResponse);
                exit;
            }
            $file = $lineChartUrl;
            $x = 10;
            $y = 20;
            $w = 180;
            $h = 100;
            $type = 'PNG';
            $link = '';
            $align = 'M';
            $resize = true;
            $dpi = 300;
            $palign = 'C';
            $ismask = false;
            $imgmask = false;
            $border = 1;
            $fitbox = false;
            $hidden = false;
            $fitonpage = true;
            $alt = true;
            $pdf->Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage, $alt);
            $file = $pieChartUrl;
            $x = 10;
            $y = 125;
            $h = 120;
            $pdf->Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border, $fitbox, $hidden, $fitonpage, $alt);
            if (empty($processFiles)) {
                $pdf->SetXY(120, 90);

                $pdf->Write(0, 'No data available for selected period');
            }

            $pdf->AddPage();
            $pdf->SetXY(10, 0);
            $pdf->writeHTML($htmlDataTable, true, false, true, false, '');

            $this->response->header(array('Content-type' => 'application/pdf'));
            $fileBaseName = 'file_' . date('Y_m_d_H_i_s');
            $arrResponse['filename'] = $fileBaseName;
            $fileName = getFileProcessPath($fileBaseName, true);
            $pdf->Output($fileName, 'F');
            echo json_encode($arrResponse);
            exit;
        }

        $filter = $this->Session->read('Report.FileProcessingCompany');
        $response = $this->Analytic->getDateRanges($filter['from']);
        $tickInterval = $response['tickInterval'];
        $xAxisDates = $response['xAxisDates'];

        $fileProcessingArr = $this->Analytic->getFileProcessing($conditions, null, false);
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        $chartData = $this->FileProccessingDetail->find('all', $fileProcessingArr['chartData']);
        $temp = array();
        $chartData = Hash::extract($chartData, '{n}.FileProccessingDetail');
        sort($chartData);
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['file_date']));
            if (isset($temp[$date])) {
                $value['file_count'] = $value['file_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['file_date']) * 1000), $value['file_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        /**
         * pie chart
         */
        $pieTitle = __('File Processed');
        $pieName = __('Files');
        $fields = 'FileProccessingDetail.id, file_count, FileProccessingDetail.company_id, FileProccessingDetail.branch_id, FileProccessingDetail.file_date';
        $this->FileProccessingDetail->virtualFields['file_count'] = 'count(FileProccessingDetail.id)';
        $fileProcessingArr = $this->Analytic->getFileProcessing($conditions, $fields, false);
        $filesPieData = $this->FileProccessingDetail->find('all', $fileProcessingArr['filesPieData']);
        $checkPieData = $filesPieData;
        $totalFiles = 0;
        foreach ($filesPieData as $file) {
            $totalFiles = $totalFiles + $file['FileProccessingDetail']['file_count'];
        }
        $fileArr = $branchesLists = array();

        if (isCompany()) {
            $branchesLists = ClassRegistry::init('CompanyBranch')->getMyBranchLists(getCompanyId());
        }
        foreach ($filesPieData as $file) {
            if (isCompany()) {
                $companyName = isset($branchesLists[$file['FileProccessingDetail']['branch_id']]) ? $branchesLists[$file['FileProccessingDetail']['branch_id']] : '';
            } else {
                $companyName = isset($companies[$file['FileProccessingDetail']['company_id']]) ? $companies[$file['FileProccessingDetail']['company_id']] : '';
            }
            $percen = getPercentage($totalFiles, $file['FileProccessingDetail']['file_count']);
            $fileArr[] = array(
                'name' => $companyName,
                'y' => $percen
            );
        }
        $filesPieData = json_encode($fileArr);
        $companyDetail = array();
        if (!empty($this->request->params['named']['genPdf'])) {
            $this->set('displayFlag', false);
        } else {
        }
        $displayFlag = true;
        $this->set(compact('displayFlag', 'companyDetail', 'temp', 'xAxisDates', 'tickInterval', 'filesPieData', 'pieTitle', 'pieName'));
        $this->render('/Analytics/file_processing_pdf');
    }

    function error_warning($all = '')
    {
        $sessionData = getMySessionData();
        $this->set('title', __('Errors/Warnings'));
        $this->loadModel('Ticket');
        $this->loadModel('User');
        $companyList = $this->User->getMyCompanyList($sessionData['id'], $sessionData['user_type'], 'id,id', true);
        $loginCompany[$sessionData['id']] = $sessionData['id'];
        $companyListId = array_merge($loginCompany, $companyList);
        
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];

        $conditions['FileProcessingDetail.company_id'] = $company_id;
        if (!empty($all)) {
            $this->Session->delete('Report.ErrorWarningFilter');
            $this->Session->delete('Report.ErrorWarningCondition');
            $this->Session->delete('Report.ErrorWarningReport');
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'ErrorWarningReport');

        $this->Session->write('Report.ErrorWarningReport', $conditions);
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'ErrorDetail.entry_timestamp >' => $startDate,
            'ErrorDetail.entry_timestamp <' => $endDate,
            'FileProccessingDetail.company_id' => $company_id
        );
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }

        if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $filter_criteria = array();
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();

            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
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
                $conditions['FileProccessingDetail.branch_id'] = $branchLists;
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }

            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    // $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }

            if (!empty($this->request->data['Analytic']['tellerName'])) {
                $conditions['TellerActivityReport.teller_name LIKE'] = $this->request->data['Analytic']['tellerName'] . '%';
                $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
            }
            if (!empty($this->request->data['Analytic']['error_description'])) {
                $conditions['ErrorDetail.error_message'] = $this->request->data['Analytic']['error_description'];
            }
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
        }
        if (empty($this->request->data['Ticket']) && $this->Session->read($this->type . 'Search')) {
            $this->request->data['Ticket'] = $this->Session->read($this->type . 'Search');
        }
        $this->Session->write('Report.ErrorWarningCondition', $conditions);
        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if(($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Super') || ($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessionData['id']);
            $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM, 'User.id IN' =>$company_list )));
            // $conditions2['FileProccessingDetail.company_id'] = $company_list;
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
         if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (!empty($this->request->data['Ticket'])) {
            if (!empty($this->request->data['Ticket']['client_name'])) {
                $conditions['Company.first_name LIKE '] = '%' . $this->request->data['Ticket']['client_name'] . '%';
            }
            if (!empty($this->request->data['Ticket']['branch_name'])) {
                $conditions['Branch.name LIKE '] = '%' . $this->request->data['Ticket']['branch_name'] . '%';
            }
            if (!empty($this->request->data['Ticket']['ticket_status'])) {
                $conditions['Ticket.status'] = $this->request->data['Ticket']['ticket_status'];
            }
            $this->Session->write($this->type . 'Search', $this->request->data['Ticket']);
        }
        if(($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Super') || ($sessionData['role'] == 'Dealer') && ($sessionData['user_type'] == 'Support')){
            $this->loadModel('DealerCompany');
            $company_list = $this->DealerCompany->getDealerClientsId($sessionData['id']);
            $conditions['FileProccessingDetail.company_id'] = $company_list;
        }elseif (($sessionData['role'] == 'Admin') && ($sessionData['user_type'] == 'Super')) {
            unset($conditions['FileProccessingDetail.company_id']);
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('list');

        $temp_companydata = array();
        foreach ($companydata as $key =>  $value) {
            $temp_companydata[$key] = $value;
        }
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $errorWarningArr = $this->Analytic->getErrorWarning($conditions);
        $this->loadModel('ErrorDetail');
        $this->loadModel('CompanyBranches');
        $error_messages  = $this->ErrorDetail->find('all', array('fields' => 'DISTINCT error_message ', 'contain' => false));
        foreach ($error_messages as $key => $value) {
            $error_messages_arr[$value['ErrorDetail']['error_message']] = $value['ErrorDetail']['error_message'];
        }
        $result_table =  $this->AutoPaginate->setPaginate(array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'company_branches',
                    'alias' => 'CompanyBranches',
                    'type' => 'LEFT ',
                    'conditions' => array(
                        'CompanyBranches.id = FileProccessingDetail.branch_id',
                    )
                ),
                array(
                    'table' => 'regions',
                    'alias' => 'regions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'regions.id = CompanyBranches.regiones'
                    )
                ),
            ),
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'branch_id', 'company_id', 'file_date', 'station'),
                    'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                    'CompanyBranches' => array('id', 'name', 'regiones')
                ),
                'ErrorType' => array('id', 'error_code', 'error_level', 'severity', 'transaction_type')
            )
        ));
        $result_table = $this->paginate('ErrorDetail');
        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $errorChartData = $this->ErrorDetail->find('all', array(
            'conditions' => $conditions, 'fields' => array('error_count', 'ErrorDetail.entry_timestamp'),
            'order' => 'ErrorDetail.entry_timestamp DESC',
            'group' => 'date(ErrorDetail.entry_timestamp)',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'branch_id', 'company_id', 'file_date', 'station'),
                    'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                    'CompanyBranches' => array('id', 'name', 'regiones')
                ),
                'ErrorType' => array('id', 'error_code', 'error_level', 'severity', 'transaction_type')
            )
        ));
        $i = 0;
        foreach ($errorChartData as $key => $data) {
            $date_format = date('Y-m-d',strtotime($data['ErrorDetail']['entry_timestamp']));
            $errorChartData_arr[$i][0] = strtotime($date_format) * 1000;
            $errorChartData_arr[$i][1] = $data['ErrorDetail']['error_count'];
            $i++;
        }
        $errorChartData_arr = json_encode($errorChartData_arr, JSON_NUMERIC_CHECK);
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
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $this->set(compact('branches','stations','companies','regiones', 'result_table', 'temp_station', 'temp_companydata', 'error_messages_arr','errorChartData_arr','xAxisDates','tickInterval'));
    }

    function database_growth()
    {
        if (!empty($all)) {
            $this->Session->delete('Report.databaseGrowthFilter');
            $this->Session->delete('Report.databaseGrowthReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.databaseGrowthFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.databaseGrowthFilter');
        }
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('databaseGrowthReport', $this->request->data['Filter'], 'DatabaseGrowth');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'DatabaseGrowth.check_date >= ' => $startDate,
            'DatabaseGrowth.check_date <= ' => $endDate
        );
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }


        $this->loadModel('DatabaseGrowth');
        $this->AutoPaginate->setPaginate(array(
            'conditions' => $conditions,
            'order' => 'DatabaseGrowth.check_date DESC',
        ));
        $DatabaseGrowths = $this->paginate('DatabaseGrowth');
        $this->Session->write('Report.DatabaseGrowthReportCondition', $conditions);

        /**
         * line chart
         */
        //old code
        $responseData = $this->DatabaseGrowth->getLineChartData($xAxisDates, $tickInterval, $conditions);
        $temp = json_encode($responseData['temp'], JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($responseData['xAxisDates'], JSON_NUMERIC_CHECK);
        $yAxisData = array();
        //end old code

        // new code

        /*$tempXaxis = $xAxisDates;
        $this->DatabaseGrowth->virtualFields['size'] = 'sum(DatabaseGrowth.size)';
        $responseData = $this->DatabaseGrowth->find('all',
            array('conditions'=>$conditions,
                'fields'=>array('id','check_date','size'),
                'group' =>'check_date'
                ));
        //new code

        $chartData = Hash::extract($responseData, '{n}.DatabaseGrowth');
        sort($chartData);
        //debug($responseData);exit;

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['check_date']));
            if(isset($temp[$date])){
                $value['size'] = $value['size'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['check_date']) * 1000), $value['size']);
        }
        $sendArr = array();
        foreach ($tempXaxis as $key => $date):
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000 ), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;

        $sendTempArr = array();
        foreach($tempArr as $tableName => $chartdata){
            $sendTempArr[] = array(
                'name' => $tableName,
                'data' => $chartdata,
                'pointInterval' => $tickInterval
            );
        }
        
        $tempArr = json_encode($sendArr, JSON_NUMERIC_CHECK);
       
        $temp = json_encode($responseData);

        $xAxisDates = json_encode($responseData['xAxisDates'], JSON_NUMERIC_CHECK);
        $yAxisData = array();*/

        //end new code

        /**
         * Pie chart
         */
        $pieTitle = __('Database Growth');
        $pieName = __('Size');
        $this->DatabaseGrowth->virtualFields['total_size'] = 'sum(DatabaseGrowth.size)';
        $dbGrowthPieData = $this->DatabaseGrowth->find('all', array('conditions' => $conditions, 'contain' => false, 'order' => 'DatabaseGrowth.check_date ASC', 'group' => 'DatabaseGrowth.table_name'));
        $totalSize = 0;
        foreach ($dbGrowthPieData as $dbGrowth) {
            $totalSize = $totalSize + $dbGrowth['DatabaseGrowth']['total_size'];
        }

        $fileArr = array();
        foreach ($dbGrowthPieData as $dbGrowth) {
            $companyName = isset($dbGrowth['DatabaseGrowth']['table_name']) ? $dbGrowth['DatabaseGrowth']['table_name'] : '';
            $percen = getPercentage($totalSize, $dbGrowth['DatabaseGrowth']['total_size']);
            $fileArr[] = array(
                'name' => $companyName,
                'y' => $percen
            );
        }

        $dbGrowthPieData = json_encode($fileArr);
        $this->set(compact('temp', 'xAxisDates', 'DatabaseGrowths', 'tickInterval', 'dbGrowthPieData', 'pieTitle', 'pieName'));

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.databaseGrowthReport', $this->request->data['Filter']);
        }

        $lineChartName = __('Database Growth');
        $lineChartTitle = __('Database Growth');
        $lineChartxAxisTitle = __('Growth Date');
        $lineChartyAxisTitle = __('Growth Size');
        $this->set(compact('lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('dbGrowth', $DatabaseGrowths);

            $transactionData = $this->render('/Elements/reports/database_growth')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function unidentify_messages($all = '')
    {

        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.UnidentifyMessagesFilter');
            $this->Session->delete('Report.UnidentifyMessagesReport');
        }
        if (empty($this->request->data['Analytic']) && $this->Session->check('Report.UnidentifyMessagesFilter')) {
            $this->request->data['Analytic'] = $this->Session->read('Report.UnidentifyMessagesFilter');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        /**
         * find users of the logged in user
         */
        /**
         * get data form table bill_activity_report
         */
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('UnidentifyMessagesReport', $this->request->data['Filter'], 'Message');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
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

        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                //get file process ids of this company id
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }

        $unidentifyMessageArr = $this->Analytic->getUnidentifyMessage($conditions);
        $this->loadModel('Message');
        $this->Message->virtualFields['message_count'] = 'count(Message.id)';
        $this->AutoPaginate->setPaginate($unidentifyMessageArr['paginate']);
        $unidentifyMessages = $this->paginate('Message');
        /**
         * line chart
         */
        $this->Message->virtualFields['error_count'] = 'count(Message.id)';
        $chartData = $this->Message->find('all', $unidentifyMessageArr['chart']);
        $this->Session->write('Report.UnidentifyMessageReportCondition', $conditions);
        $this->set(compact('tickInterval', 'unidentifyMessages'));
        $temp = array();
        //        $chartData = Hash::extract($chartData, '{n}.Message');
        //
        //        sort($chartData);

        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['Message']['error_count'] = $value['Message']['error_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['Message']['error_count']);
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);

        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.UnidentifyMessagesReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.UnidentifyMessagesFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        $lineChartName = __('Unidentify Message');
        $lineChartTitle = __('Unidentify Message');
        $lineChartxAxisTitle = __('Unidentify Message Date');
        $lineChartyAxisTitle = __('No. of Messages');
        $this->set(compact('branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $this->set('activity', $unidentifyMessages);
            /**
             * set the table data
             */
            $transactionData = $this->render('/Elements/reports/unidentify_message')->body();
            $options = array(
                'name' => $lineChartName,
                'title' => $lineChartTitle,
                'xTitle' => $lineChartxAxisTitle,
                'yTitle' => $lineChartyAxisTitle
            );
            echo json_encode(array('options' => $options, 'data' => $temp, 'xAxisDates' => $xAxisDates, 'htmlData' => $transactionData, 'tickInterval' => $tickInterval));
            exit;
        }
    }

    function error_reporting($all = '')
    {
        $this->loadModel('MachineType');
        $this->loadModel('ErrorType');
        $this->loadModel('MachineError');
        $machineName = $this->MachineType->find('list');
        $machineErrorType = $this->ErrorType->find('list', array('fields' => 'id,error_level'));
        $machineErrorType = array_map('ucfirst', $machineErrorType);
        $conditions = array();
        if ($all == "all") {
            $this->Session->write($this->type . 'Search', '');
        }
        if (empty($this->request->data['MachineError']) && $this->Session->read($this->type . 'Search')) {
            $this->request->data['MachineError'] = $this->Session->read($this->type . 'Search');
        }
        if (!empty($this->request->data['MachineError'])) {
            $this->request->data['MachineError'] = array_filter($this->request->data['MachineError']);
            $this->request->data['MachineError'] = array_map('trim', $this->request->data['MachineError']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['MachineError']['machine_type'])) {
                    $conditions['MachineError.machine_type_id'] = $this->request->data['MachineError']['machine_type'];
                }
                if (isset($this->request->data['MachineError']['error_type'])) {
                    $conditions['MachineError.error_type_id'] = $this->request->data['MachineError']['error_type'];
                }
            }
            $this->Session->write($this->type . 'Search', $this->request->data['MachineError']);
        }
        $this->AutoPaginate->setPaginate(array(
            'conditions' => $conditions
        ));
        $machineErrorDetails = $this->paginate('MachineError');
        $this->set(compact('machineName', 'machineErrorType', 'machineErrorDetails'));
    }
    function activity_report_view($id = '')
    {
        $id = decrypt($id);
        $this->loadModel('ActivityReport');
        $activityReport = $this->ActivityReport->find('first', array(
            'fields' => array('station', 'id'),
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('file_date'),
                    'Branch' => array('id', 'name')
                )
            ),
            'conditions' => array(
                'ActivityReport.id' => $id
            )
        ));
        $this->set(compact('activityReport'));
    }
    function export_csv()
    {
        $this->layout = null;
        $this->autoLayout = false;
    }
    //demon graph move from dashboard
    function denom_report()
    {
        $repConditions = ClassRegistry::init('Analytic')->getConditions('DashboardErrors', isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array(), 'ErrorDetail');
        $from = $repConditions['from'];
        $xAxisDates1 = $xAxisDates = $repConditions['xAxisDates'];
        $tickInterval = $repConditions['tickInterval'];
        $startDate = $repConditions['start_date'];
        $endDate = $repConditions['end_date'];
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
        }
        $startDate = $this->request->data['Filter']['start_date'] . ' 00:00:00';
        $endDate = $this->request->data['Filter']['end_date'] . ' 23:59:59';
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        //            ------------------------- start -------------------------
        $balanceConditions = array();
        $yesterdayDate = date('Y-m-d', strtotime("-1 days"));
        $balanceConditions['date(trans_datetime)'] = $yesterdayDate;
        if (isset($conditions['FileProccessingDetail.company_id'])) {
            $balanceConditions['FileProccessingDetail.company_id'] = $conditions['FileProccessingDetail.company_id'];
        }
        if (isset($conditions['FileProccessingDetail.branch_id'])) {
            $balanceConditions['FileProccessingDetail.branch_id'] = $conditions['FileProccessingDetail.branch_id'];
        }
        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $balanceConditions,
            'fields' => array(
                'id',
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime(' -1 day'));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;

        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);

        $yAxisData = array();
        $this->set(compact('sentTemp', 'xAxisDatesTime'));

        //            -------------------------end------------------------- 

    }

    // inventory by hours report
    function inventory_by_hours()
    {
        $stations = array();
        /**
         * Company List
         */
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $this->set(compact('companies'));
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (isCompany()) {
            $data = getMySessionData();
            if (!empty($data)) {
                $stations = ClassRegistry::init('Station')->getStationList($data['id']);
            }
        }
        $this->set(compact('branches', 'companyDetail', 'stations'));
        $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        /**
         * line chart
         */
        $previousDate = date('Y-m-d', strtotime(' -1 day'));
        $conditions = array();
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['InventoryByHour.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        $conditions = array('InventoryByHour.trans_date' => $previousDate);
        if (isCompany()) {
            $conditions['InventoryByHour.company_id'] = getCompanyId();
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['InventoryByHour.company_id'] = $this->request->data['Analytic']['company_id'];
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['InventoryByHour.branch_id'] = $this->request->data['Analytic']['branch_id'];
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['InventoryByHour.station'] = $this->request->data['Analytic']['station'];
            }
        }
        $denom_1 = $this->getSingleDenomCount('denom_1', $conditions);
        $denom_2 = $this->getSingleDenomCount('denom_2', $conditions);
        $denom_5 = $this->getSingleDenomCount('denom_5', $conditions);
        $denom_10 = $this->getSingleDenomCount('denom_10', $conditions);
        $denom_20 = $this->getSingleDenomCount('denom_20', $conditions);
        $denom_50 = $this->getSingleDenomCount('denom_50', $conditions);
        $denom_100 = $this->getSingleDenomCount('denom_100', $conditions);
        $temp = $temp1 = $temp2 = $temp3 = $temp4 = $temp5 = $temp6 = array();
        foreach ($denom_1 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_1'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_2 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_2'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp1[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_5 as $key => $value) {
            $date = date('Y-m-d hebrev(hebrew_text):i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_5'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp2[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_10 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_10'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp3[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_20 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_20'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp4[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_50 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_50'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp5[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_100 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_100'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp6[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime(' -1 day'));
        $xAxisDates = date_range($previousDate . '08:00:00', $previousDate . '18:00:00', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp1)) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp2)) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp3)) {
                $sendArr3[$key] = $temp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp4)) {
                $sendArr4[$key] = $temp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp5)) {
                $sendArr5[$key] = $temp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp6)) {
                $sendArr6[$key] = $temp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $tickInterval = 30;
        $temp = array(
            array(
                'name' => 'Denome 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp = json_encode($temp);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $this->set(compact('temp', 'xAxisDates', 'tickInterval'));
        // for 1 week
        $conditions1 = array();
        $weekday = date('Y-m-d', strtotime(' -6 day'));
        $time = array('08:00:00', '18:00:00');
        $conditions1 = array(
            'InventoryByHour.trans_date <=' => $previousDate,
            'InventoryByHour.trans_date >=' => $weekday,
            'InventoryByHour.end_hours' => $time
        );
        if (isCompany()) {
            $conditions1['InventoryByHour.company_id'] = getCompanyId();
        }
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions1['InventoryByHour.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions1['InventoryByHour.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions1['InventoryByHour.station'] = $this->request->data['Analytic']['station'];
            }
        }
        $denom_1 = $this->getWeekDenomCount('denom_1', $conditions1);
        $denom_2 = $this->getWeekDenomCount('denom_2', $conditions1);
        $denom_5 = $this->getWeekDenomCount('denom_5', $conditions1);
        $denom_10 = $this->getWeekDenomCount('denom_10', $conditions1);
        $denom_20 = $this->getWeekDenomCount('denom_20', $conditions1);
        $denom_50 = $this->getWeekDenomCount('denom_50', $conditions1);
        $denom_100 = $this->getWeekDenomCount('denom_100', $conditions1);
        $temp = $temp1 = $temp2 = $temp3 = $temp4 = $temp5 = $temp6 = array();
        foreach ($denom_1 as $key => $value) {
            $date = date('Y-m-d H:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_1'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_2 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_2'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp1[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_5 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_5'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp2[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_10 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_10'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp3[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_20 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_20'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp4[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_50 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_50'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp5[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        foreach ($denom_100 as $key => $value) {
            $date = date('Y-m-d h:i a', strtotime($value[0]['trans_date']));
            if (isset($temp[$date])) {
                $value[0]['denom_100'] = $value['0']['doman_count'] + $temp[$date][1];
            }
            $temp6[$date] = array((strtotime($value[0]['trans_date']) * 1000), $value[0]['doman_count']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $xAxisDates = array();
        $date1 = date("Y-m-d") . ' 08:00:00';
        $date2 = date("Y-m-d") . ' 18:00:00';
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-6 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-6 day', strtotime($date2)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-5 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-5 day', strtotime($date2)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-4 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-4 day', strtotime($date2)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-3 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-3 day', strtotime($date2)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-2 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-2 day', strtotime($date2)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-1 day', strtotime($date1)));
        $xAxisDates[] = date('Y-m-d h:i a', strtotime('-1 day', strtotime($date2)));
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp1)) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp2)) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp3)) {
                $sendArr3[$key] = $temp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp4)) {
                $sendArr4[$key] = $temp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp5)) {
                $sendArr5[$key] = $temp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (array_key_exists($date, $temp6)) {
                $sendArr6[$key] = $temp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;
        $tickInterval = 14;
        $temp = array(
            array(
                'name' => 'Denome 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denome 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $temp1 = json_encode($temp);
        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $this->set(compact('temp1', 'xAxisDates1', 'tickInterval'));
    }
    function getSingleDenomCount($denom, $conditions)
    {
        $this->loadModel('InventoryByHour');
        $total = $this->InventoryByHour->find(
            'all',
            array(
                'fields' => array(
                    'SUM(InventoryByHour.' . $denom . ') AS doman_count',
                    'CONCAT_WS(" ", InventoryByHour.trans_date,InventoryByHour.end_hours) as trans_date'
                ),
                'conditions' => $conditions,
                'group' => 'InventoryByHour.end_hours',
                'order' => 'InventoryByHour.end_hours'

            )
        );

        return $total;
    }
    function getWeekDenomCount($denom, $conditions)
    {
        $this->loadModel('InventoryByHour');

        $total = $this->InventoryByHour->find(
            'all',
            array(
                'fields' => array(
                    'SUM(InventoryByHour.' . $denom . ') AS doman_count',
                    'CONCAT_WS(" ", InventoryByHour.trans_date,InventoryByHour.end_hours) as trans_date'
                ),
                'conditions' => $conditions,
                'group' => 'InventoryByHour.trans_date,InventoryByHour.end_hours',
                'order' => 'InventoryByHour.trans_date,InventoryByHour.end_hours'
            )
        );
        return $total;
    }

    /**
     * 
     * Download File Processing From S3
     */
    function downloadProcessfile($filename)
    {
        //if ($this->request->is('ajax')){
        $filename = base64_decode($filename);
        //$filename = $this->request->data['filename'];

        $this->layout = false;

        $client = S3Client::factory(
            array(
                'version' => 'latest',
                'region' => AWS_S3_REGION,
                'bucket' => S3_BUCKET,
                'credentials' => array(
                    'key'       => awsAccessKey,
                    'secret'    => awsSecretKey,
                ),
            )
        );

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => S3_BUCKET,
            'Key' => 'warehouse/' . $filename,
        ]);
        $request = $client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();

        $fileData =  file_get_contents($presignedUrl);
        
        $fileData = str_replace(
            array("SimplyBank", "420 Third Avenue", "Dayton TN 37321", "Dayton Main"),
            array("MyBank", "123 First st", "San Francisco", "CA -12345"),
            $fileData
        );

        header('Content-Type: application/txt');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $filename . "\"");
        echo $fileData;
        //readfile($presignedUrl);
        exit;
        //echo json_encode(array('download_link'=> $presignedUrl));
        //exit;            
        //}

    }
    function show_error($filename = null, $lineNo = null)
    {
        if (!empty($filename)) {
            $filename = base64_decode($filename);
        }
        if (!empty($lineNo)) {
            $lineNo = $lineNo;
        }
        $client = S3Client::factory(
            array(
                'version' => 'latest',
                'region' => AWS_S3_REGION,
                'bucket' => S3_BUCKET,
                'credentials' => array(
                    'key'       => awsAccessKey,
                    'secret'    => awsSecretKey,
                ),
            )
        );

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => S3_BUCKET,
            'Key' => 'warehouse/' . $filename,
        ]);
        $request = $client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        $fileData =  file_get_contents($presignedUrl);
        $fileData = str_replace(
            array("SimplyBank", "420 Third Avenue", "Dayton TN 37321", "Dayton Main"),
            array("MyBank", "123 First st", "San Francisco", "CA -12345"),
            $fileData
        );
        // echo '<pre><b></b><br>';
        // print_r($fileData);echo '<br>';exit;
        $this->set(compact('fileData', 'lineNo'));
        // header('Content-Type: application/txt');
        // header("Content-Transfer-Encoding: Binary");
        // header("Content-disposition: attachment; filename=\"" . $filename . "\"");

    }

    function setDataFilter()
    {
        $data = $this->request->data['Filter'];
        $title = $data['from'];
        if($title == 'last_3months'){
            $date = $this->getQuarter($data['end_date'],$title);
            $data['start_date'] = $date['start'];
            $data['end_date'] = $date['end'];
        }elseif ($title == 'last_12months') {
            $date = $this->getQuarter($data['end_date'],$title);
            $data['start_date'] = $date['start'];
            $data['end_date'] = $date['end'];
        }elseif($title == "last_months"){
            $date = $this->getQuarter($data['end_date'],$title);
            $data['start_date'] = $date['start'];
            $data['end_date'] = $date['end'];
        }elseif($title == "last_18days"){
            $date = $this->getQuarter($data['end_date'],$title);
            $data['start_date'] = $date['start'];
            $data['end_date'] = $date['end'];
        }
        $this->Session->write('Report.GlobalFilter', $data);
        exit;
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
    function userReport(Type $var = null)
    {
        if (!empty($all) && $all != 'all') {
            $activityReportId = decrypt($all);
            $this->set(compact('activityReportId'));
        }
        $sessionData = getMySessionData();
        $stations = array();
        if (!empty($all)) {
            $this->Session->delete('Report.UserReport');
            $this->Session->delete('Report.TellerActivityReport');
        }
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $companyId = $this->request->data['Analytic']['company_id'];
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));

        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        

        /**
         */
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);

        /**
         * get data form table bill_activity_report
         */
        if (!empty($activityReportId) && empty($this->request->data['Filter'])) {
            $this->request->data['Filter'] = $this->Session->read('Report.ActivityReport');
        }
        // $this->request->data['Filter'] = isset($this->request->data['Filter']) ? $this->request->data['Filter'] : array();
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'UserReport');
        // $conditions = $this->__getConditions('TellerActivityReport', $this->request->data['Filter'], 'TellerActivityReport');

        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        if (isCompany()) {
            if (!empty($sessionData)) {
                $stations = ClassRegistry::init('Station')->getStationList($sessionData['id']);
            }
        }
        $conditions = array(
            'FileProccessingDetail.file_date >=' => $startDate,
            'FileProccessingDetail.file_date <=' => $endDate
        );
        $this->set(compact('companies', 'sessionData', 'regiones'));
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'] = getCompanyId();
        }
        $sessData = getMySessionData();
        if (!empty($sessData['BranchDetail']['id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $sessData['BranchDetail']['id'];
        }
        if (!empty($sessData['assign_branches'])) {
            $assignedBranches = $sessData['assign_branches'];
            if (!empty($assignedBranches)) {
                // $conditions['FileProccessingDetail.branch_id'] = array_keys($assignedBranches);
            }
        }


        /**
         * Line Chart
         * No. of automix vs Dates from automix_network table
         */
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        $filter_criteria = array();
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
                // $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $regionsId = $this->request->data['Analytic']['regiones'];
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                    // $conditions['FileProccessingDetail.branch_id'] = $branches;
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);

                // $conditions['FileProccessingDetail.branch_id'] = $branches;
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['tellerName'])) {
                $conditions['UserReport.user_name LIKE'] = $this->request->data['Analytic']['tellerName'] . '%';
                $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
            }
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
        }
        if (empty($this->request->data['Filter'])) {
            $this->request->data['Filter']['from'] = $from;
            $this->request->data['Filter']['start_date'] = $startDate;
            $this->request->data['Filter']['end_date'] = $endDate;
            if (!isset($_SESSION['Report.GlobalFilter'])) {
                $this->Session->write('Report.GlobalFilter', $this->request->data['Filter']);
            }
        }
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }

        $this->loadModel('UserReport');
        $tellerNames_Arr = array();
        $tellerNames = $this->UserReport->find('all', array('fields' => 'DISTINCT user_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['UserReport']['user_name']] = $value['UserReport']['user_name'];
        }
        $conditions_data = $this->Analytic->getUserReport($conditions, $regionsId);
        $this->AutoPaginate->setPaginate($conditions_data['paginate']);
        $activity = $this->paginate('UserReport');
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('branches', 'stations', 'temp_station', 'activity', 'tellerNames_Arr'));
    }
    function special_notes_reconciliation()
    {
        // if (!empty($all)) {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.Special_notes_reconciliation_condition');
        
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
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
        if (isCompany()) {
            $this->request->data['Analytic']['company_id'] = $company_id;
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'Specialnotesreconciliation');
        $from = $conditions['from'];
        $xAxisDates1 = $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'Specialnotesreconciliation.trans_datetime >= ' => $startDate . ' 00:00:00',
            'Specialnotesreconciliation.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        // echo '<pre><b></b><br>';
        // print_r($getHeaderFilter);echo '<br>';exit;
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }

        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        if (isSuparAdmin() && empty($company_id)) {
            $companyId = array_keys($companies);
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (!isCompany()) {
            $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports($company_id);
        }
        if (!empty($this->request->data['Analytic'])) {
            if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $this->set(compact('filter_criteria'));

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

            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }
            if(!empty($this->request->data['Analytic']['regiones'])){
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (empty($branches)) {
                    $branches = '';
                }
            }
            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['Specialnotesreconciliation.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['Specialnotesreconciliation.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
        }
        $SpecialNotes = $this->Analytic->getSpecialnotesreconciliation($conditions, $regionsId);
        $this->loadModel('Specialnotesreconciliation');
        $this->AutoPaginate->setPaginate($SpecialNotes['paginate']);
        $specialNotes = $this->paginate('Specialnotesreconciliation');
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('sessionData', 'regiones', 'companies', 'branches', 'temp_station', 'stations', 'specialNotes'));
    }
    function displayhelpPage()
    {
        if ($this->request->is('ajax')) {
            $responseArr = [];
            $this->layout = false;
            if (!empty($this->request->data)) {
                $reportName = $this->request->data['formData'];
                $this->loadModel('Help');
                $helpPage = $this->Help->find('first', array('conditions' => array('Help.report_name' => $reportName)));
                $this->set(compact('helpPage'));
            } else {
                echo "Invalid help page";
                exit;
            }
        }
    }

    public function getRecent_report()
    {
        $this->loadModel('RecentReports');
        $recentMenu = $this->RecentReports->find('all', array(
            'limit' => 5,
            'order' => 'RecentReports.counter_tag DESC',
            'recursive' => 1,
        ));
        if ($this->request->is('requested')) {
            return $recentMenu;
        }
        $this->set('recentMenu', $recentMenu);
    }
    
    public function user_activity($all = '')
    {
          $sessionData = getMySessionData();
          ini_set('memory_limit', '-1');
        // Parent company id get.
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
        }
        $company_parent_id = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
        $stations = array();
        if ($all == 'all') {
            $this->Session->delete('Report.UserActivityFilter');
            $this->Session->delete('Report.UserActivityReport');
            $this->Session->delete('setUserActivityFilter');
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));
        $conditions2['company_id'] = $company_parent_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        // if(!empty($this->request->data['Analytic']['company_id'])){
        //     $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        // }else{
        //     $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $company_parent_id)));
        // }
        $regiones = $this->Region->getRegionList($conditions2);
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TellerActivityReport');
        $from = $conditions['from'];
        $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'FileProccessingDetail.company_id' => $company_parent_id
        );
       
        $data = $this->request->data['Analytic'];
        if(!empty($data)){
            $this->Session->write('setUserActivityFilter', $data);
        }
        $getHeaderFilter = !empty($this->Session->read('setHeaderFilter')) ? $this->Session->read('setHeaderFilter') : [];
        $getUserActivityFilter = $this->Session->read('setUserActivityFilter');
        $getHeaderFilter = array_merge($getHeaderFilter, $getUserActivityFilter);
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        $filter_criteria = array();
        if (!empty($this->request->data['Analytic'])) {
            $filePCond = $filePIds = array();

            if (!empty($this->request->data['Analytic']['company_id'])) {
                $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['company_id']);
            }
            if (!empty($this->request->data['Analytic']['regiones'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
                if (!empty($branches)) {
                }
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
                $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
                $regionsId = $this->request->data['Analytic']['regiones'];
                $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
                $filter_criteria['region'] = $region[key($region)];
                $this->set(compact('filter_criteria'));
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
                $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
            }
            if (!empty($this->request->data['Analytic']['branch_id'])) {
                $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
                $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
                $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
                $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
                $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['station'])) {
                $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
                $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
                $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
                $filter_criteria['station'] = $stationsList['Station']['name'];
                $this->set(compact('filter_criteria'));
            }
            if (!empty($this->request->data['Analytic']['tellerName'])) {
                $conditions['TransactionDetail.teller_name'] = trim($this->request->data['Analytic']['tellerName']);
                $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
                $this->set(compact('filter_criteria'));
            }

            if (!empty($this->request->data['Analytic']['daterange'])) {
                $date = (explode("-", $this->request->data['Analytic']['daterange']));
                $conditions['FileProccessingDetail.file_date >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $conditions['FileProccessingDetail.file_date <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
                $startDate = date('Y-m-d', strtotime($date[0]));
                $endDate = date('Y-m-d', strtotime($date[1]));
                $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
                $this->set(compact('filter_criteria'));
            }
            if(!empty($this->request->data['Analytic']['date'])){
                $conditions['TransactionDetail.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($this->request->data['Analytic']['date'] . ' 00:00:00'));
                $conditions['TransactionDetail.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($this->request->data['Analytic']['date'] . ' 23:59:59'));
                $filter_criteria['date'] = date('Y-m-d', strtotime($this->request->data['Analytic']['date']));
                $this->set(compact('filter_criteria'));
            }
            
        }
        if (!empty($activityReportId)) {
            $conditions['TellerActivityReport.activity_report_id'] = $activityReportId;
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
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('TransactionDetail');
        $tellerNames_Arr = array();
        // $teller_name_condition = $conditions;
        // $data = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id' => $company_parent_id, 'TransactionDetail.id <=' => 1901015], 'order' => ['TransactionDetail.id' => 'DESC'], 'limit' => 300));
        // foreach ($data as $key => $value) {
        //     echo '<pre><b></b><br>';
        //     print_r($value);echo '<br>';
        //     $this->TransactionDetail->id = $value['TransactionDetail']['id'];
        //     $this->TransactionDetail->set(array('teller_name' => trim($value['TransactionDetail']['teller_name'])));
        //     $this->TransactionDetail->save();
        // }exit;
        //  unset($teller_name_condition['TransactionDetail.trans_datetime >= ']);
        //  unset($teller_name_condition['TransactionDetail.trans_datetime <= ']);
        //  unset($teller_name_condition['TransactionDetail.match_with_prev']);
        //  unset($teller_name_condition['TransactionDetail.teller_name']);
          $tellerNames = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id' => $company_parent_id],'fields' => 'DISTINCT teller_name ', 'order' => ['teller_name' => 'ASC']));
          // $tellerNames = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_id],'fields' => 'DISTINCT teller_name '));
          foreach ($tellerNames as $key => $value) {
              $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
          }
        $transaction_count_data['total_withdrawal'] = 0;
        $transaction_count_data['total_amount_withdrawal'] = 0;
        $transaction_count_data['total_deposit'] = 0;
        $transaction_count_data['total_amount_deposit'] = 0;
        $transaction_count_data['net_cash_usage'] = 0;
        if(!empty($this->request->data['Analytic']['date']) && !empty($this->request->data['Analytic']['tellerName'])){
            $conditions['TransactionDetail.trans_type_id IN'] = [2,4,19,1,11,5,13,14,20];
          
            $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
            $this->AutoPaginate->setPaginate($transactionDetailArray['paginate2']);
            $transactions = $this->paginate('TransactionDetail');
            $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
            $withdrawsCondition = $transactionDetailArray['chart']['deposite'];
            $withdrawsCondition['conditions']['TransactionDetail.trans_type_id IN'] = [1,11,5,13,14,20];
            $withdrawsCount = $this->TransactionDetail->find('all', $withdrawsCondition);
            $total_amount_withdrawal = !empty($withdrawsCount[0]) ? $withdrawsCount[0][0]['total_amount'] : 0;
            $total_withdrawal = !empty($withdrawsCount[0]) ? $withdrawsCount[0]['TransactionDetail']['transaction_count'] : 0;
            $transaction_count_data['total_withdrawal'] = $total_withdrawal;
            $transaction_count_data['total_amount_withdrawal'] = $total_amount_withdrawal;

            $depositeCondition = $transactionDetailArray['chart']['deposite'];
            $depositeCondition['conditions']['TransactionDetail.trans_type_id IN'] = [2,4,19];
            $depositeCount = $this->TransactionDetail->find('all', $depositeCondition);

            $total_amount_deposit = !empty($depositeCount[0]) ? $depositeCount[0][0]['total_amount'] : 0;
            $total_deposit = !empty($depositeCount[0]) ? $depositeCount[0]['TransactionDetail']['transaction_count'] : 0;
            $transaction_count_data['total_deposit'] = $total_deposit;
            $transaction_count_data['total_amount_deposit'] = $total_amount_deposit;
            $transaction_count_data['net_cash_usage'] = $total_amount_deposit - $total_amount_withdrawal;
        }else{
            $transactions = [];
        }
        $this->Session->write('Report.UserActivityCondition', $conditions);
        $this->set(compact('branches', 'stations', 'sessionData', 'companies', 'regiones', 'temp_station', 'activity', 'filter_criteria', 'tellerNames_Arr', 'transactions', 'sessionData', 'companyDetail', 'transaction_count_data'));
    }

    public function out_of_balance($all = '')
    { 
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.OutOfBalance');
        $this->Session->delete('Report.TransactionReportCondition');
        // }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
        }
        $sessionData = getMySessionData();
        if ($all == "all") {
            $this->Session->delete('setOutOfBalanaceFilter');
        }
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        if (!isset($this->request->data['Analytic']['date']) or empty($this->request->data['Analytic']['date'])) {
            $this->request->data['Analytic']['date'] = date('Y-m-d', strtotime("-1 days"));
        } else {
            $conditions3 = array();
            $tdate = $this->request->data['Analytic']['date'];
            $tdate = str_replace('-', '/', $tdate);
            $tdate = date('Y-m-d', strtotime($tdate));
        }

        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        if (isSuparAdmin() && empty($company_id)) {
            $company_id = array_keys($companies);
        }
        $this->loadModel('TransactionDetail');
        $this->loadModel('TransactionType');
        $transaction_type_ids  = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_id], 'fields' => 'DISTINCT trans_type_id '));
        $transaction_type_arr = array();
        foreach ($transaction_type_ids as $key => $value) {
            $transactionType_condition['TransactionType.id'] = $value['TransactionDetail']['trans_type_id'];
            $transaction_type = $this->TransactionType->find('first', array('fields' => 'id, text', 'conditions' => $transactionType_condition));
            $transaction_type_arr[$value['TransactionDetail']['trans_type_id']] = $transaction_type['TransactionType']['text'];
        }
        $this->request->data['Filter'] = ($this->Session->check('Report.GlobalFilter')) ? $this->Session->read('Report.GlobalFilter') : array();
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $from = $conditions['from'];
        $xAxisDates1 = $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59',
            'TransactionDetail.match_with_prev' => 'No'
        );
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['from'])) {
            $this->Session->write('Report.OutOfBalance', $this->request->data);
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (isset($sessionData['user_type']) && $sessionData['user_type'] == 'Region') {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($sessionData['user_type']);
            if (!empty($branches)) {
                // $conditions['FileProccessingDetail.branch_id'] = $branches;
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($sessionData['user_type']);
        }
        // $data = $this->request->data['Analytic'];
        // if(!empty($data)){
        //     $this->Session->write('setOutOfBalanaceFilter', $data);
        // }
        // $getHeaderFilter = $this->Session->read('setHeaderFilter');
        // $getOutOfBalanceFilter = $this->Session->read('setOutOfBalanaceFilter');
        // $getHeaderFilter = array_merge($getHeaderFilter, $getOutOfBalanceFilter);

        $data = $this->request->data['Analytic'];
      
        if(!empty($data)){
            if(!empty($data['tellerName'])){
                $this->Session->write('setOutOfBalanaceFilter', $data);
            }
        }
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        $getUserActivityFilter = $this->Session->read('setOutOfBalanaceFilter');
        $getHeaderFilter = array_merge($getHeaderFilter, $getUserActivityFilter);

        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        // echo '<pre><b></b><br>';
        // print_r($this->request->data['Analytic']);echo '<br>';exit;
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);
            if (!empty($branches)) {
            }
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
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
            $conditions['FileProccessingDetail.branch_id IN'] = $branchLists;
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['tellerName'])) {
            $conditions['TransactionDetail.teller_name'] = $this->request->data['Analytic']['tellerName'];
            $filter_criteria['user'] = $this->request->data['Analytic']['tellerName'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['trasaction_type'])) {
            $conditions['TransactionDetail.trans_type_id'] = $this->request->data['Analytic']['trasaction_type'];
            $filter_criteria['trasaction_type'] = $this->request->data['Analytic']['trasaction_type'];
            $this->set(compact('filter_criteria'));
        }
      
        $tellerNames_Arr = array();
       $teller_name_condition = $conditions;
     
       unset($teller_name_condition['TransactionDetail.trans_datetime >= ']);
       unset($teller_name_condition['TransactionDetail.trans_datetime <= ']);
       unset($teller_name_condition['TransactionDetail.match_with_prev']);
       unset($teller_name_condition['TransactionDetail.teller_name']);
        $tellerNames = $this->TransactionDetail->find('all', array('conditions' => $teller_name_condition,'fields' => 'DISTINCT teller_name ', 'order' => ['teller_name' => 'ASC']));
        // $tellerNames = $this->TransactionDetail->find('all', array('conditions' => ['FileProccessingDetail.company_id =' => $company_id],'fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $transactionArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
        $this->AutoPaginate->setPaginate($transactionArray['paginate2']);
        $transactionsDetails = $this->paginate('TransactionDetail');
        $this->Session->write('Report.TransactionReportCondition', $conditions);
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';

        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)';
     
        if (!empty($this->request->data['Analytic']['date'])) {
            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions_new = $conditions;
            $conditions_new['TransactionDetail.trans_datetime >= '] = $this->request->data['Analytic']['date'] . ' 00:00:00';
            $conditions_new['TransactionDetail.trans_datetime <= '] = $this->request->data['Analytic']['date'] . ' 23:59:59';
            $conditions_new['FileProccessingDetail.file_date >= '] = $this->request->data['Analytic']['date'];
            $conditions_new['FileProccessingDetail.file_date <= '] = $this->request->data['Analytic']['date'];
            $hourly_data2 = array();
            $hourly_data3 = array();

            $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions_new);
            $hourly_data = $this->TransactionDetail->find('list', $transactionDetailArray2['paginate2']);

            foreach ($hourly_data as $key => $value) {
                $hourly_data2[$key][0] = date("H", strtotime($key));
                $hourly_data2[$key][1] = $value;
            }
            foreach ($hourly_data2 as $key => $value) {
                array_push($hourly_data3, $value);
            }
            if (empty($hourly_data3)) {
                array_push($hourly_data3, [0, 0]);
            }
            $hourly_report_data = json_encode($hourly_data3, JSON_NUMERIC_CHECK);

            if ($hourly_report_data == null) {
                $hourly_report_data = json_encode([[0, 0]], JSON_NUMERIC_CHECK);
            }
        } else {
            $hourly_report_data = json_encode(1, JSON_NUMERIC_CHECK);
        }

        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        
        /**
         * transactions vs category
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        //        debug($conditions);
    
        /**
         * transactions vs branch
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        // $conditions['FileProccessingDetail.branch_id'] = array_keys($branches);
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($this->request->data['Analytic']['regiones']);

        }
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['company']['id']] = $value['CompanyBranch']['name'];
        }
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));


        $temp = array();
        $temp2 = array();
        $tempCount = 0;
       
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;

        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $chartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'trans_datetime', 'transaction_count', 'total_amount_withdrawal',
                'total_cash_deposite', 'FileProccessingDetail.file_date'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['total_amount_withdrawal'] = $value['TransactionDetail']['total_amount_withdrawal'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_amount_withdrawal']);
            if (isset($temp1[$date])) {
                $value['TransactionDetail']['total_cash_deposite'] = $value['TransactionDetail']['total_cash_deposite'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_cash_deposite']);
            if (isset($temp2[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), ($value['TransactionDetail']['transaction_count']));
        }
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
            )
        );

        $temp1 = json_encode($temp1);

        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $temp = $oldTemp;

        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            //                $date = date('Y-m-d', strtotime($value['trans_datetime']));
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime($yesterdayDate));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;
        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/out_of_balance')->body();
            $options = array(
                'id' => '#container',
                'name' => __('Transactions'),
                'title' => __('Transactions'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction'),
            );
            echo json_encode(array(
                'options' => $options,
                'data' => $temp1,
                'xAxisDates' => $xAxisDates1,
                'htmlData' => $transactionData,
                'tickInterval' => $tickInterval,
                'transactionDetails' => $temp1,
                'hourData' => $temp1
            ));
            exit;
        }
        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $i = 0;
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.OutOfBalance', $this->request->data['Filter']);
        }
        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);
        $this->set(compact('newchartdata', 'depositechartdata', 'withdrawschartdata', 'Inventorychartdata'));
        $this->set(compact('sessionData', 'companies', 'regiones', 'branches', 'stations', 'tellerNames_Arr', 'transaction_type_arr', 'transactionsDetails', 'hourly_report_data', 'temp_station', 'transactionCatPie', 'transactionClientPie', 'pieClientTitle', 'pieCatTitle', 'pieTitle', 'pieName', 'tickInterval', 'transactions', 'temp_companydata', 'temp_station', 'transactionCategories', 'transactionTypes', 'transactionPie', 'temp_hr', 'temp', 'xAxisDates', 'temp1', 'sentTemp', 'xAxisDatesTime'));
    }
    function show_transaction($filename = null, $lineNo = null)
    {
        if (!empty($filename)) {
            $filename = base64_decode($filename);
        }
        $this->loadModel('FileProccessingDetail');
        $file_data = $this->FileProccessingDetail->find('first', array(
            'conditions' => ['filename' => $filename],
            'contain' => false
        ));
        
        $this->loadModel('TransactionDetail');
        $transaction_data = $this->TransactionDetail->find('first', array(
            'conditions' => ['file_processing_detail_id' => $file_data['FileProccessingDetail']['id'], 'trans_line_no' => $lineNo],
            'contain' => false
        ));
        $file_id = $file_data['FileProccessingDetail']['id'];
        $transaction_no = $transaction_data['TransactionDetail']['id'];
        $cuurent_transaction_no = !empty($transaction_data['TransactionDetail']['trans_number']) ? $transaction_data['TransactionDetail']['trans_number'] : 0;
        $previous_transaction_no = 1;
        if($cuurent_transaction_no > 1){
            $previous_transaction_no = $cuurent_transaction_no - 1;
        }
        $previous_transaction_data = $this->TransactionDetail->find('first', array(
            'conditions' => ['file_processing_detail_id' => $file_data['FileProccessingDetail']['id'], 'trans_number' => $previous_transaction_no],
            'contain' => false
        ));
        $transaction_line_no = !empty($previous_transaction_data['TransactionDetail']['trans_line_no']) ? $previous_transaction_data['TransactionDetail']['trans_line_no'] : $lineNo;
        if (!empty($transaction_line_no)) {
            $lineNo = $transaction_line_no;
        }
        $client = S3Client::factory(
            array(
                'version' => 'latest',
                'region' => AWS_S3_REGION,
                'bucket' => S3_BUCKET,
                'credentials' => array(
                    'key'       => awsAccessKey,
                    'secret'    => awsSecretKey,
                ),
            )
        );

        $cmd = $client->getCommand('GetObject', [
            'Bucket' => S3_BUCKET,
            'Key' => 'warehouse/' . $filename,
        ]);
        $request = $client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        $fileData =  file_get_contents($presignedUrl);
        $fileData = str_replace(
            array("SimplyBank", "420 Third Avenue", "Dayton TN 37321", "Dayton Main"),
            array("MyBank", "123 First st", "San Francisco", "CA -12345"),
            $fileData
        );
        // echo '<pre><b></b><br>';
        // print_r($fileData);echo '<br>';exit;
        $this->set(compact('fileData', 'lineNo', 'file_id', 'transaction_no'));
        // header('Content-Type: application/txt');
        // header("Content-Transfer-Encoding: Binary");
        // header("Content-disposition: attachment; filename=\"" . $filename . "\"");

    }
    function previous_transaction($filename = null, $transaction_no = null)
    {
        $this->loadModel('FileProccessingDetail');
        $file_data = $this->FileProccessingDetail->find('first', array(
            'conditions' => ['id' => $filename],
            'contain' => false
        ));
        ini_set('memory_limit', '-1');
        $this->loadModel('TransactionDetail');
        $transaction_data = $this->TransactionDetail->find('all', array(
            'conditions' => ['file_processing_detail_id' => $filename, 'id <= ' => $transaction_no],
            'order' => 'TransactionDetail.id DESC',
            'contain' =>  false,
            'limit' => 10,
        ));
        $transaction_data_1 = [];
        if(count($transaction_data) < 10){
            $limit = 10 - count($transaction_data);
            $conditions = array(
                'FileProccessingDetail.id <' => $file_data['FileProccessingDetail']['id'],
                'FileProccessingDetail.branch_id' => $file_data['FileProccessingDetail']['branch_id'],
                'FileProccessingDetail.station' => $file_data['FileProccessingDetail']['station']
            );
           $file_data_1 =  $this->FileProccessingDetail->find('first', array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.id DESC',
                'contain' => false
            ));
            $transaction_data_1 = $this->TransactionDetail->find('all', array(
                'conditions' => ['file_processing_detail_id' => $file_data_1['FileProccessingDetail']['id']],
                'order' => 'TransactionDetail.id DESC',
                'contain' =>  false,
                'limit' => $limit,
            ));
        }
        $transaction_data_1 = array_reverse($transaction_data_1,true);
        $transactionsDetails = array_merge($transaction_data,$transaction_data_1);
        $companyDetail = array();
        $this->set(compact('transactionsDetails', 'companyDetail'));
        // $all_transaction = array_merge()
    }
}
