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

class ErrorTicketsController extends AppController
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
        $machineTypes = $this->MachineType->find('list', array(
            'conditions' => array(
                'MachineType.status' => 'active'
            )
        ));
        
        $this->loadModel('MachineError');
        $machineErrors = $this->MachineError->find('list',array(
            'fields' => 'MachineError.id, MachineError.error_message'
        ));
        $this->loadModel('Station');
        $stations = $this->Station->getDealerStationList(getDealerId());
        
        $this->set(compact('machineTypes','machineErrors','stations'));
        $conditions = array();
        if ($all == "all") {
            $this->Session->write('ErrorTicketSearch', '');
        }
        if (empty($this->request->data['ErrorTicket']) && $this->Session->read('ErrorTicketSearch')) {
            $this->request->data['ErrorTicket'] = $this->Session->read('ErrorTicketSearch');
        }
        if (!empty($this->request->data['ErrorTicket'])) {
            $this->request->data['ErrorTicket'] = array_filter($this->request->data['ErrorTicket']);
            $this->request->data['ErrorTicket'] = array_map('trim', $this->request->data['ErrorTicket']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['ErrorTicket']['machine_type_id'])) {
                    $conditions['ErrorTicket.machine_type_id'] = $this->request->data['ErrorTicket']['machine_type_id'];
                }
                if (isset($this->request->data['ErrorTicket']['machine_error_id'])) {
                    $conditions['ErrorTicket.machine_error_id'] = $this->request->data['ErrorTicket']['machine_error_id'];
                }
                if (isset($this->request->data['ErrorTicket']['company_id'])) {
                    $conditions['ErrorTicket.company_id'] = $this->request->data['ErrorTicket']['company_id'];
                }
                if (isset($this->request->data['ErrorTicket']['branch_id'])) {
                    $conditions['ErrorTicket.branch_id'] = $this->request->data['ErrorTicket']['branch_id'];
                }
                if (isset($this->request->data['ErrorTicket']['station'])) {
                    $conditions['ErrorTicket.station'] = $this->request->data['ErrorTicket']['station'];
                }
            }
            $this->Session->write('ErrorTicketSearch', $this->request->data['ErrorTicket']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'Dealer', 'Branch', 'Company', 'MachineError'
            ),
            'order' => ' ErrorTicket.id DESC',
            'conditions' => $conditions
        ));
        $this->set('errorTickets', $this->paginate('ErrorTicket'));
    }
    
}
