<?php

App::uses('AppController', 'Controller');

/**
 * ClientReports Controller
 *
 * @property ClientReport $ClientReport
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class ClientReportsController extends AppController
{
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
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
        $limit = 20;
        $paginateModels = array(
            'ErrorDetail',
            'ManagerSetup',
            'ManagerLog',
            'SideLog',
            'TransactionDetail',
            'ActivityReport'
        );
        if (!empty($this->request->params['named']['Paginate']) && !empty($this->request->params['named']['Model']) && (in_array($this->request->params['named']['Model'], $paginateModels))) {
            $limit = $this->request->params['named']['Paginate'];
        }
        //TODO:optimized this method with pagination and fields HIGH PRIORITY in full project
        $conditions = array();
        if ($all == "all") {
            $this->Session->write('ClientReportSearch', '');
        }

        if (empty($this->request->data['ClientReport']) && $this->Session->read('ClientReportSearch')) {
            $this->request->data['ClientReport'] = $this->Session->read('ClientReportSearch');
        }
        $branches = $fileIds = $stations = $files = array();
        /**
         * set previos searchable data
         */
        $this->CompanyBranch = ClassRegistry::init('CompanyBranch');
        if (!empty($this->request->data['ClientReport'])) {
            $this->request->data['ClientReport'] = array_filter($this->request->data['ClientReport']);
            $this->request->data['ClientReport'] = array_map('trim', $this->request->data['ClientReport']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['ClientReport']['company_id'])) {
                    $conditions['company_id'] = $this->request->data['ClientReport']['company_id'];
                    $branches = $this->CompanyBranch->getBranchList($conditions['company_id']);
                }
                if (isset($this->request->data['ClientReport']['branch_id'])) {
                    $conditions['branch_id'] = $this->request->data['ClientReport']['branch_id'];
                    $stations = $this->CompanyBranch->getStationList($conditions['branch_id']);
                }
                if (isset($this->request->data['ClientReport']['station_id'])) {
                    $conditions['station_id'] = $this->request->data['ClientReport']['station_id'];
                    $files = ClassRegistry::init('FileProccessingDetail')->getFileDateList($conditions['station_id']);
                }
                if (isset($this->request->data['ClientReport']['file_id'])) {
                    $conditions['file_id'] = $this->request->data['ClientReport']['file_id'];
                }
            }
            $this->Session->write('ClientReportSearch', $this->request->data['ClientReport']);
        }
        $companies = ClassRegistry::init('User')->find('list', array('contain' => false, 'fields' => 'id, first_name', 'conditions' => array('User.role' => COMPANY, 'User.user_type' => SUPAR_ADM)));

        $this->set(compact('companies', 'branches', 'stations', 'files'));
        /**
         * get file processing id
         */
        if (!empty($files)) {
            $fileIds = array_keys($files);
        }
        $errors = $warnings = $mgrSetups = $mgrLogs = $sideLogs = $transactions = $activityReports = array();
        /**
         * get error data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'ErrorDetail.file_processing_detail_id' => $fileIds,
                'ErrorDetail.error_type_id' => 2
            );
        }

        $this->loadModel('ErrorDetail');
        $page = $this->pageForPagination('ErrorDetail');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $errors = $this->paginate('ErrorDetail');
        /**
         * get warning data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'ErrorDetail.file_processing_detail_id' => $fileIds,
                'ErrorDetail.error_type_id' => 1
            );
        }

        $page = $this->pageForPagination('ErrorDetail');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $warnings = $this->paginate('ErrorDetail');
        /**
         * get manager setup data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'ManagerSetup.file_processing_detail_id' => $fileIds
            );
        }
        $this->loadModel('ManagerSetup');
        $page = $this->pageForPagination('ManagerSetup');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $mgrSetups = $this->paginate('ManagerSetup');
        /**
         * get manager log data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'ManagerLog.file_processing_detail_id' => $fileIds
            );
        }

        $this->loadModel('ManagerLog');
        $page = $this->pageForPagination('ManagerLog');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $mgrLogs = $this->paginate('ManagerLog');
        /**
         * get side log data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'SideLog.file_processing_detail_id' => $fileIds
            );
        }

        $this->loadModel('SideLog');
        $page = $this->pageForPagination('SideLog');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $sideLogs = $this->paginate('SideLog');
        /**
         * get transaction data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'TransactionDetail.file_processing_detail_id' => $fileIds
            );
        }

        $this->loadModel('TransactionDetail');
        $page = $this->pageForPagination('TransactionDetail');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number'),
                'TransactionCategory',
                'TransactionType'
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $transactions = $this->paginate('TransactionDetail');
        /**
         * get activity reports data from conditions
         */
        $tableConditions = array();
        if (!empty($fileIds)) {
            $tableConditions = array(
                'ActivityReport.file_processing_detail_id' => $fileIds
            );
        }

        $this->loadModel('ActivityReport');
        $page = $this->pageForPagination('ActivityReport');
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array('filename', 'station', 'processing_counter', 'transaction_number')
            ),
            'conditions' => $tableConditions,
            'limit' => $limit,
            'page' => $page
        ));

        $activityReports = $this->paginate('ActivityReport');
        $this->set(compact('errors', 'warnings', 'mgrSetups', 'mgrLogs', 'sideLogs', 'transactions', 'activityReports'));
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
    public function audit_log($all = null)
    {
        $this->loadModel('AuditLog');
        $conditions = array();

        if ($all == "all") {
            $this->Session->write('AuditLog.Search', '');
        }
        if (empty($this->request->data['AuditLog']) && $this->Session->read('AuditLog.Search')) {
            $this->request->data['AuditLog'] = $this->Session->read('AuditLog.Search');
        }

        if (!empty($this->request->data['AuditLog'])) {
            $this->request->data['AuditLog'] = array_filter($this->request->data['AuditLog']);
            $this->request->data['AuditLog'] = array_map('trim', $this->request->data['AuditLog']);
            if (!empty($this->request->data)) {
//                debug($this->request->data);exit;
                if (isset($this->request->data['AuditLog']['name'])) {
                    $conditions['User.first_name LIKE '] = '%' . $this->request->data['AuditLog']['name'] . '%';
                }
                if (isset($this->request->data['AuditLog']['type'])) {
                    $conditions['AuditLog.type'] = $this->request->data['AuditLog']['type'];
                }
                if (isset($this->request->data['AuditLog']['created'])) {
                    $conditions['date(AuditLog.created)'] = $this->request->data['AuditLog']['created'];
                }
            }
            $this->Session->write('AuditLog.Search', $this->request->data['AuditLog']);
        }
        $this->AutoPaginate->setPaginate(array(
            'order' => 'AuditLog.id DESC',
            'conditions' => $conditions,
        ));

        $this->Session->write('Export.auditLogConditions', $conditions);

        $this->loadModel('AuditLog');
        $this->loadModel('User');

        $names = $this->User->find('list', array(
            'conditions' => array(
                'role !=' => 'admin',
                'status' => 'active'
            ),
            'fields' => 'first_name, name'
        ));
        $this->set(compact('names'));
        $this->set('types', $this->AuditLog->getAuditLogStatusList());
        $this->set('auditlogs', $this->paginate('AuditLog'));
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view1($id = null)
    {
        $this->loadModel('AuditLog');
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->autoRender = false;

            if (!$this->AuditLog->exists($id)) {
                throw new NotFoundException(__('Invalid audit log'));
            }


            $conditions = array(
                'AuditLog.id' => $id
            );
            $auditLogs = $this->AuditLog->find('first', array(
                    'conditions' => $conditions,
                    'contain' => array('User')
                )
            );
            $this->set(compact('auditLogs'));
			
            $renderData = $this->render()->body();
            echo $renderData;
            exit;
        }
    }
}
