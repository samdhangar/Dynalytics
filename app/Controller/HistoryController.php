<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel');
if (!class_exists('PHPExcel')) {
    throw new CakeException('Vendor class PHPExcel not found!');
}

class HistoryController extends AppController
{
    public $components = array('Auth');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
    }

    function index($all = '')
    {
        $sessionData = getMySessionData();
        $this->set('title', __('Historical Errors/Warnings'));
        $this->Ticket = ClassRegistry::init('Ticket');
        $this->User = ClassRegistry::init('User');
//        $companyList = $this->User->getMyCompanyList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), 'id, first_name');
        $companyList = $this->User->getMyCompanyList($sessionData['id'] , $sessionData['role'] , 'id, first_name');
        $companyListIds = array_keys($companyList);
        $otherConditions = array(
            'User.user_type' => SUPPORT,
//            'User.parent_id' => $this->Session->read('Auth.User.id')
            'User.parent_id' => $sessionData['id']
        );
//        $dealers = $this->User->getMyDealerList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), 'id, first_name', $otherConditions);
        $dealers = $this->User->getMyDealerList($sessionData['id'], $sessionData['role'], 'id, first_name', $otherConditions);
        //search panel code
        $conditions = array('Ticket.company_id' => $companyListIds);
        if ($all == "all") {
            $this->Session->write('HistorySearch', '');
        }
        if (empty($this->request->data['Ticket']) && $this->Session->read('HistorySearch')) {
            $this->request->data['Ticket'] = $this->Session->read('HistorySearch');
        }
        if (!empty($this->request->data['Ticket'])) {
            $conditions = $this->__getHistoryConditions($this->request->data);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'file_date')
                ),
                'Company' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                ),
                'Dealer' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                ),
                'Branch' => array(
                    'Station',
                    'fields' => array('id', 'name')
                ),
                'UpdatedBy' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                )
            ),
            'conditions' => $conditions
        ));
        $branches = $stations = array();
        if (!empty($this->request->data['Ticket']['client_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getBranchList($this->request->data['Ticket']['client_id']);
        }
        if (!empty($this->request->data['Ticket']['branch_id'])) {
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Ticket']['branch_id']);
        }
        $tickets = $this->paginate('Ticket');
        $this->set(compact('tickets', 'companyList', 'dealers', 'branches', 'stations'));
    }

    function __getHistoryConditions($reqData = array())
    {
        $sessionData = getMySessionData();
        $this->User = ClassRegistry::init('User');
//        $companyList = $this->User->getMyCompanyList($this->Session->read('Auth.User.id'), $this->Session->read('Auth.User.role'), 'id, first_name');
        $companyList = $this->User->getMyCompanyList($sessionData['id'] , $sessionData['role'] , 'id, first_name');
        $companyListIds = array_keys($companyList);
        $retConditions = array('Ticket.company_id' => $companyListIds);
        if (!empty($reqData['Ticket'])) {
            $reqData['Ticket'] = array_filter($reqData['Ticket']);
            $reqData['Ticket'] = array_map('trim', $reqData['Ticket']);
            if (!empty($reqData)) {
                if (isset($reqData['Ticket']['client_id'])) {
                    $retConditions['Ticket.company_id'] = $reqData['Ticket']['client_id'];
                }
                if (isset($reqData['Ticket']['branch_id'])) {
                    $retConditions['Ticket.branch_id'] = $reqData['Ticket']['branch_id'];
                }
                if (isset($reqData['Ticket']['station_id'])) {
                    $branchConditions = array(
                        'Station.name' => $reqData['Ticket']['station_id']
                    );
                    $branchList = ClassRegistry::init('Station')->find('list', array('fields' => 'branch_id, branch_id', 'conditions' => $branchConditions));
                    $retConditions['Branch.id'] = $branchList;
                }
                if (isset($reqData['Ticket']['ticket_date'])) {
                    $ticketDate = showdate($reqData['Ticket']['ticket_date'], 'N/A', '', 'Y-m-d');
                    $retConditions['Ticket.ticket_date LIKE '] = '%' . $ticketDate . '%';
                }
                if (isset($reqData['Ticket']['error_warning_status'])) {
                    $retConditions['Ticket.error_warning_status'] = $reqData['Ticket']['error_warning_status'];
                }
                if (isset($reqData['Ticket']['updated_by'])) {
                    $retConditions['Ticket.updated_by'] = $reqData['Ticket']['updated_by'];
                }
                if (isset($reqData['Ticket']['updated'])) {
                    $updatedDate = showdate($reqData['Ticket']['updated'], 'N/A', '', 'Y-m-d');
                    $retConditions['Ticket.updated LIKE '] = '%' . $updatedDate . '%';
                }
            }
            $this->Session->write('HistorySearch', $reqData['Ticket']);
        }
        return $retConditions;
    }

    function download_history()
    {
        $sessionData = getMySessionData();
        $message = __('No Data available.');
        $this->layout = false;
        if (empty($this->request->data['Ticket']) && $this->Session->read('HistorySearch')) {
            $this->request->data['Ticket'] = $this->Session->read('HistorySearch');
        }
        $conditions = $this->__getHistoryConditions($this->request->data);
        $tickets = ClassRegistry::init('Ticket')->find('all', array(
            'contain' => array(
                'Company' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                ),
                'Dealer' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                ),
                'Branch' => array(
                    'Station',
                    'fields' => array('id', 'name')
                ),
                'UpdatedBy' => array(
                    'fields' => array('id', 'first_name', 'last_name', 'name')
                )
            ),
            'conditions' => $conditions
        ));
        if (!empty($tickets)) {
            $objPHPExcel = new PHPExcel();
            $sheetName = date('Y_m_d_H_i_s') . '_history';
//            $objPHPExcel->getProperties()->setCreator($this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name'));
            $objPHPExcel->getProperties()->setCreator($sessionData['first_name'] . ' ' . $sessionData['last_name']);
            $objPHPExcel->getProperties()->setTitle($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setSubject($sheetName . " Spreadsheet");
            $objPHPExcel->getProperties()->setDescription($sheetName . " Spreadsheet");
            //set header
            $objPHPExcel->setActiveSheetIndex(0);
            $excelHeader = array(
                'company_id' => __('Client Name'),
                'branch_id' => __('Branch Name'),
                'ticket_date' => __('Occurrence Date'),
                'station' => __('Station(s)'),
                'error_warning' => __('Error/Warning'),
                'acknowledge_date' => __('Ack Date'),
                'updated_by' => __('Resolved By'),
                'note' => __('Note'),
                'updated' => __('Resolved Date'),
            );
            $excelHeaderWidth = array(
                'company_id' => '15',
                'branch_id' => '15',
                'ticket_date' => '15',
                'station' => '12',
                'error_warning' => '20',
                'acknowledge_date' => '12',
                'updated_by' => '15',
                'note' => '20',
                'updated' => '12',
            );
            $col = 'A';
            foreach ($excelHeader as $key => $header) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($excelHeaderWidth[$key]);
                $objPHPExcel->getActiveSheet()->setCellValue($col . '1', $header);
                $col++;
            }
            $appCount = 2;
            foreach ($tickets as $ticket) {
                $col = 'A';
                foreach ($excelHeader as $key => $value) {
                    if ($key == 'company_id') {
                        $ticket['Ticket'][$key] = isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : '';
                    }
                    if ($key == 'branch_id') {
                        $ticket['Ticket'][$key] = isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : '';
                    }
                    if ($key == 'updated_by') {
                        $ticket['Ticket'][$key] = isset($ticket['UpdatedBy']['first_name']) ? $ticket['UpdatedBy']['first_name'] : '';
                    }
                    if ($key == 'acknowledge_date') {
                        $ticket['Ticket'][$key] = showdatetime($ticket['Ticket'][$key]);
                    }
                    if ($key == 'updated') {
                        $ticket['Ticket'][$key] = showdatetime($ticket['Ticket'][$key]);
                    }
                    if ($key == 'error_warning') {
                        if ($ticket['Ticket']['error_warning_status'] == 'error') {
                            $ticket['Ticket'][$key] = $ticket['Ticket']['error'];
                        } else {
                            $ticket['Ticket'][$key] = $ticket['Ticket']['warning'];
                        }
                    }
                    if ($key == 'station') {
                        $ticket['Ticket'][$key] = '';
                        foreach ($ticket['Branch']['Station'] as $station):
                            $ticket['Ticket'][$key] .= $station['name'] . ((count($ticket['Branch']['Station']) > 1) ? ',' : '');
                        endforeach;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($col . $appCount, $ticket['Ticket'][$key]);
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
            $message = __('Unable to export history.Please try again');
        }
        $this->Message->setWarning($message,$this->referer());
    }

    
}
