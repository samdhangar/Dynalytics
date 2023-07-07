<?php
App::uses('AppController', 'Controller');

class TicketsController extends AppController
{
    public $components = array('Auth');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
    }

    function getDetail($id = null)
    {
        $id = decrypt($id);
        $this->layout = false;
        $ticket = $this->Ticket->find('first', array(
            'conditions' => array('Ticket.id' => $id),
            'contain' => array(
                'Company' => array('fields' => 'id, first_name, last_name, name'),
                'Dealer' => array('fields' => 'id, first_name, last_name, name'),
                'UpdatedBy' => array('fields' => 'id, first_name, last_name, name'),
                'TicketMessage' => array(
                    'Dealer' => array('order' => 'created ASC', 'fields' => 'id, first_name, last_name, name')
                ),
                'Branch' => array('fields' => 'id, name')
            )
        ));
        $this->set(compact('ticket'));
    }

    function assigns()
    {
        if ($this->request->is(array('post', 'put'))) {
            /**
             * convert encrypted id to decrypt formate
             */
            $assignIds = array();
            foreach ($this->request->data['delete']['id'] as $ids) {
                $ids = decrypt($ids);
                $assignIds[$ids] = $ids;
            }
            /**
             * assign ticket to support dealer
             */
            $sessData = getMySessionData();
            $updateData = array(
                'Ticket.dealer_id' => $this->request->data['User']['dealer_id'],
                'Ticket.status' => '"Open"',
                'Ticket.updated' => '"' . date('Y-m-d H:i:s') . '"',
                'Ticket.updated_by' => '"' . $sessData['id'] . '"',
            );
            $this->Ticket->updateAll($updateData, array('Ticket.id' => $assignIds));


            $this->Message->setSuccess(__('Ticket assign successfull.'), $this->referer());
            /*
             * Sent Mail to Dealer
             */
        }
        $this->Message->setWarning(__('Unable to assign support dealer'), $this->referer());
    }

    function notify($ticketId = null)
    {
        $ticketId = decrypt($ticketId);
        if ($this->Ticket->exists($ticketId)) {
            //send mail to support dealer
            $this->Ticket->id = $ticketId;
            $emailData = $this->Ticket->find('first', array(
                'conditions' => array('Ticket.id' => $ticketId),
                'contain' => array(
                    'Company' => array('fields' => 'id, first_name, last_name, name'),
                    'Dealer' => array('fields' => 'id, first_name, last_name, name, email, phone_no'),
                    'Branch' => array('fields' => 'id, name')
                )
            ));
            $emailData['Ticket']['text'] = '';
            if ($emailData['Ticket']['error_warning_status'] == 'error') {
                $emailData['Ticket']['text'] = $emailData['Ticket']['error'];
            } else {
                $emailData['Ticket']['text'] = $emailData['Ticket']['warning'];
            }
            if (!empty($emailData['Dealer']['email'])) {
                $this->SendEmail->sendTicketSupportNotifyEmail($emailData);
                $companyData = $emailData['Company'];
                $branchData = $emailData['Branch'];
                $ticketData = $emailData['Ticket'];
                $dealerData = $emailData['Dealer'];
                $branchAdmins = ClassRegistry::init('BranchAdmin')->getBranchAdminsEmailDetails($emailData['Ticket']['branch_id']);
                $mailData = array(
                    'Ticket' => $ticketData,
                    'Admins' => $branchAdmins,
                    'Branch' => $branchData,
                    'Company' => $companyData,
                    'Dealer' => $dealerData
                );
                $this->SendEmail->sendTicketOpenedEmail($mailData);
                $this->Message->setSuccess(__('Support Notify Successfull'), $this->referer());
            }
        }
        $this->Message->setWarning(__('Invalid Ticket'), $this->referer());
    }

    function status_change($ticketId = null, $status = 'Closed')
    {
        $sessionData = getMySessionData();
        $this->layout = false;
        $ticketId = decrypt($ticketId);
        if (!$this->Ticket->exists($ticketId)) {
            $this->Message->setWarning(__('Invalid Ticket'), $this->referer());
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['Ticket']['id'] = $ticketId;
//            $this->request->data['Ticket']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['Ticket']['updated_by'] = $sessionData['id'];
            $this->Ticket->id = $ticketId;
            $ackDate = $this->Ticket->field('acknowledge_date');
            if ($ackDate == '0000-00-00 00:00:00') {
                $this->Ticket->id = $ticketId;
                $ackDate = $this->Ticket->field('ticket_date');
            }
            $this->request->data['Ticket']['dealer_work_hour'] = getWorkedMinutes($ackDate);
            if ($this->Ticket->save($this->request->data['Ticket'])) {
                /*
                 * Sent Mail to Branch Admin/ Regional Admin
                 */
                $ticketData = $this->Ticket->find('first', array(
                    'contain' => array(
                        'Branch' => array(
                            'fields' => array('id', 'name')
                        ),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'last_name', 'email')
                        )
                    ),
                    'conditions' => array('Ticket.id' => $this->Ticket->id)
                ));
                $companyData = $ticketData['Company'];
                $branchData = $ticketData['Branch'];
                $ticketData = $ticketData['Ticket'];
                $branchAdmins = ClassRegistry::init('BranchAdmin')->getBranchAdminsEmailDetails($ticketData['branch_id']);
                $mailData = array(
                    'Ticket' => $ticketData,
                    'Admins' => $branchAdmins,
                    'Branch' => $branchData,
                    'Company' => $companyData
                );
                $this->SendEmail->sendTicketClosedEmail($mailData);
                $this->Message->setSuccess(__('Ticket has been closed'), $this->referer());
            }
        }
        $this->set(compact('ticketId', 'status'));
    }

    function add_ack($ticketId = null)
    {
        $sessionData = getMySessionData();
        $this->layout = false;
        $ticketId = decrypt($ticketId);
        if (!$this->Ticket->exists($ticketId)) {
            $this->Message->setWarning(__('Invalid Ticket'), $this->referer());
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['Ticket']['id'] = $ticketId;
//            $this->request->data['Ticket']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['Ticket']['updated_by'] = $sessionData['id'];
            if ($this->Ticket->save($this->request->data['Ticket'])) {

                $this->Message->setSuccess(__('Ticket Acknowledgement has been added'), $this->referer());
            }
        }
        $this->set(compact('ticketId', 'status'));
    }
}
