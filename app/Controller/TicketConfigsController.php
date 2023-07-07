<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DealerMachineErrors
 *
 * @author securemetasys002
 */
App::uses('AppController', 'Controller');

class TicketConfigsController extends AppController
{
    public $components = array('Auth');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
    }

    function old_index($all = '')
    {
        $this->loadModel('DealerMachineError');
        $this->loadModel('MachineError');
        $this->loadModel('MachineType');
        $this->loadModel('ErrorType');
        $this->loadModel('Station');
        $stations = $this->Station->getDealerStations();
        $machineTypes = $this->MachineType->find('list', array(
            'conditions' => array(
                'MachineType.status' => 'active'
            )
        ));
        $this->ErrorType->virtualFields['error_level'] = 'CONCAT(UCASE(LEFT(error_level, 1)), SUBSTRING(error_level, 2))';
        $errorTypes = $this->ErrorType->find('list', array(
            'fields' => 'id, error_level'
        ));
        $this->set(compact('errorTypes', 'machineTypes', 'stations'));
        $conditions = array('MachineError.status' => 'active');
        $dealerErrorLists = $this->DealerMachineError->find('list', array(
            'fields' => 'machine_error_id, machine_error_id',
            'conditions' => array(
                'DealerMachineError.dealer_id' => getDealerId()
            )
        ));
        if (!empty($dealerErrorLists)) {
            $conditions['MachineError.id <> '] = $dealerErrorLists;
        }
        if ($all == "all") {
            $this->Session->write('TicketConfigSearch', '');
        }
        if (empty($this->request->data['TicketConfig']) && $this->Session->read('TicketConfigSearch')) {
            $this->request->data['TicketConfig'] = $this->Session->read('TicketConfigSearch');
        }
        if (!empty($this->request->data['TicketConfig'])) {
            $this->request->data['TicketConfig'] = array_filter($this->request->data['TicketConfig']);
            $this->request->data['TicketConfig'] = array_map('trim', $this->request->data['TicketConfig']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['TicketConfig']['machine_type_id'])) {
                    $conditions['MachineError.machine_type_id'] = $this->request->data['TicketConfig']['machine_type_id'];
                }
                if (isset($this->request->data['TicketConfig']['error_type_id'])) {
                    $conditions['MachineError.error_type_id'] = $this->request->data['TicketConfig']['error_type_id'];
                }
            }
            $this->Session->write('TicketConfigSearch', $this->request->data['TicketConfig']);
        }
        $this->ErrorType->virtualFields['error_level'] = 'CONCAT(UCASE(LEFT(error_level, 1)), SUBSTRING(error_level, 2))';
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'MachineType' => array("fields" => "id", "name"),
                'ErrorType' => array("fields" => "id", "error_level")
            ),
            'order' => ' MachineError.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('MachineError');
        $this->set('ticketConfigs', $this->paginate('MachineError'));
        /**
         * get the dealer machine error which has dealer marked as no
         * @param type $userId
         * @param type $status
         */
        $dealerErrors = $this->TicketConfig->find('list', array(
            'fields' => 'machine_error_id, id',
            'conditions' => array(
                'TicketConfig.dealer_id' => getDealerId()
            )
        ));
        $this->set(compact('dealerErrors'));
    }

    function index($all = '')
    {
        $this->loadModel('MachineType');
        $this->loadModel('ErrorType');
        $machineTypes = $this->MachineType->find('list', array(
            'conditions' => array(
                'MachineType.status' => 'active'
            )
        ));
        $this->ErrorType->virtualFields['error_level'] = 'CONCAT(UCASE(LEFT(error_level, 1)), SUBSTRING(error_level, 2))';
        $errorTypes = $this->ErrorType->find('list', array(
            'fields' => 'id, error_level'
        ));
        $this->set(compact('errorTypes', 'machineTypes'));
        $conditions = array();
        if ($all == "all") {
            $this->Session->write('TicketConfigSearch', '');
        }
        if (empty($this->request->data['TicketConfig']) && $this->Session->read('TicketConfigSearch')) {
            $this->request->data['TicketConfig'] = $this->Session->read('TicketConfigSearch');
        }
        if (!empty($this->request->data['TicketConfig'])) {
            $this->request->data['TicketConfig'] = array_filter($this->request->data['TicketConfig']);
            $this->request->data['TicketConfig'] = array_map('trim', $this->request->data['TicketConfig']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['TicketConfig']['machine_type_id'])) {
                    $conditions['TicketConfig.machine_type_id'] = $this->request->data['TicketConfig']['machine_type_id'];
                }
                if (isset($this->request->data['TicketConfig']['company_id'])) {
                    $conditions['TicketConfig.company_id'] = $this->request->data['TicketConfig']['company_id'];
                }
                if (isset($this->request->data['TicketConfig']['branch_id'])) {
                    $conditions['TicketConfig.branch_id'] = $this->request->data['TicketConfig']['branch_id'];
                }
                if (isset($this->request->data['TicketConfig']['station'])) {
                    $conditions['TicketConfig.station'] = $this->request->data['TicketConfig']['station'];
                }
                if (isset($this->request->data['TicketConfig']['error_type_id'])) {
                    $conditions['TicketConfig.error_type_id'] = $this->request->data['TicketConfig']['error_type_id'];
                }
            }
            $this->Session->write('TicketConfigSearch', $this->request->data['TicketConfig']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'MachineError', 'Branch', 'Company'
            ),
            'order' => ' TicketConfig.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('TicketConfig');
        $this->set('ticketConfigs', $this->paginate('TicketConfig'));
    }

    function add()
    {
        $this->layout = false;
        $responseArr = array(
            'status' => false,
            'message' => __('Unable to add ticket config')
        );
        $this->request->data['TicketConfig']['dealer_id'] = getDealerId();
        if ($this->TicketConfig->save($this->request->data)) {
            $responseArr = array(
                'status' => true,
                'message' => __('Ticket config has been added sucessfully.')
            );
        }
        if ($this->TicketConfig->validationErrors) {
            $responseArr['error'] = $this->TicketConfig->validationErrors;
        }
        echo json_encode($responseArr);
        exit;
    }

    function edit($id = null)
    {
        $id = decrypt($id);
        if (!$this->TicketConfig->exists($id)) {
            $this->Message->setWarning(__('Invalid Ticket config'), $this->referer());
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->TicketConfig->save($this->request->data)) {
                $this->Message->setSuccess(__('Ticket config has been updated sucessfully.'), array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to update ticket config.'));
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->TicketConfig->find('first', array(
                'contain' => false,
                'conditions' => array(
                    'TicketConfig.id' => $id
                )
            ));
        }
        /**
         * Get data for the ticket config popup
         */
        $companies = ClassRegistry::init('User')->getSuparCompanyListFromDeal(getDealerId());
        $this->set(compact('companies'));
        if (!empty($this->request->data['TicketConfig']['company_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getBranchList($this->request->data['TicketConfig']['company_id']);
            $this->set(compact('branches'));
        }
        if (!empty($this->request->data['TicketConfig']['branch_id'])) {
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['TicketConfig']['branch_id']);
            $this->set(compact('stations'));
        }
        $edit = 'Edit';
        $this->set(compact('edit'));
        $this->loadModel('DealerMachineError');
        $this->loadModel('MachineError');
        $removeMachineErrorList = $this->DealerMachineError->find('list', array(
            'fields' => 'machine_error_id, machine_error_id',
            'conditions' => array(
                'DealerMachineError.dealer_id' => getDealerId()
            )
        ));
        $machineErrors = $this->MachineError->find('list', array(
            'conditions' => array(
                'MachineError.id <> ' => $removeMachineErrorList
            ),
            'contain' => false,
            'fields' => 'id, error_message'
        ));
        $this->set(compact('machineErrors'));
        $this->render('form');
    }

    function change_status($machineId = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of machine error'));
        if ($this->TicketConfig->exists($machineId) && !empty($status)) {
            $saveData = array(
                'machine_error_id' => $machineId,
                'dealer_id' => getDealerId(),
                'is_generate_ticket' => 0,
            );
            $this->loadModel('ErrorTicket');
            $this->ErrorTicket->saveData($saveData, $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __('Machine error status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
}
