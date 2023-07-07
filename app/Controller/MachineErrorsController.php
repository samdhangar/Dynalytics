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

class MachineErrorsController extends AppController
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
        $this->loadModel('MachineType');
        $this->loadModel('ErrorType');
        $machineTypes = $this->MachineType->find('list', array(
            'conditions' => array(
                'MachineType.status' => 'active'
            )
        ));
        ClassRegistry::init('ErrorType')->virtualFields['error_level'] = 'CONCAT(UCASE(LEFT(error_level, 1)), SUBSTRING(error_level, 2))';
        $errorTypes = $this->ErrorType->find('list', array(
            'fields' => 'id, error_level'
        ));
        $this->set(compact('errorTypes', 'machineTypes'));

        $conditions = array('MachineError.status' => 'active');
        if ($all == "all") {
            $this->Session->write('MachineErrorSearch', '');
        }
        if (empty($this->request->data['MachineError']) && $this->Session->read('MachineErrorSearch')) {
            $this->request->data['MachineError'] = $this->Session->read('MachineErrorSearch');
        }
        if (!empty($this->request->data['MachineError'])) {
            $this->request->data['MachineError'] = array_filter($this->request->data['MachineError']);
            $this->request->data['MachineError'] = array_map('trim', $this->request->data['MachineError']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['MachineError']['machine_type_id'])) {
                    $conditions['MachineError.machine_type_id'] = $this->request->data['MachineError']['machine_type_id'];
                }
                if (isset($this->request->data['MachineError']['error_type_id'])) {
                    $conditions['MachineError.error_type_id'] = $this->request->data['MachineError']['error_type_id'];
                }
            }
            $this->Session->write('MachineErrorSearch', $this->request->data['MachineError']);
        }
        ClassRegistry::init('ErrorType')->virtualFields['error_level'] = 'CONCAT(UCASE(LEFT(error_level, 1)), SUBSTRING(error_level, 2))';
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'MachineType' => array("fields" => "id", "name"),
                'ErrorType' => array("fields" => "id", "error_level")
            ),
            'order' => ' MachineError.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('MachineError');
        $this->set('machineErrors', $this->paginate('MachineError'));
        /**
         * get the dealer machine error which has dealer marked as no
         * @param type $userId
         * @param type $status
         */
        $this->loadModel('DealerMachineError');
        $dealerErrors = $this->DealerMachineError->find('list', array(
            'fields' => 'machine_error_id, machine_error_id',
            'conditions' => array(
                'DealerMachineError.dealer_id' => getDealerId()
            )
        ));
        $this->set(compact('dealerErrors'));
        /**
         * Get data for the ticket config popup
         */
        $companies = ClassRegistry::init('User')->getSuparCompanyListFromDeal(getDealerId());
        $this->set(compact('companies'));
    }

    function dealer_errors($wantHtml = false)
    {
        $this->loadModel('DealerMachineError');
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
        if ($wantHtml) {
            $this->layout = false;
            $addData = $machineErrors;
            $addComboTitle = __('Select Machine Error');
            $addDataTitle = __('No any machine error');
            $this->set(compact('addData', 'addComboTitle', 'addDataTitle'));
            echo $this->render('/Elements/makeAddOptions')->body();
            exit;
        }
    }

    function change_status($machineId = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of machine error'));
        if ($this->MachineError->exists($machineId) && !empty($status)) {
            $saveData = array(
                'machine_error_id' => $machineId,
                'dealer_id' => getDealerId(),
                'is_generate_ticket' => 0,
            );
            $this->loadModel('DealerMachineError');
            $this->DealerMachineError->saveData($saveData, $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __('Machine error status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
}
