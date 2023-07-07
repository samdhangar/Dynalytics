<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class Ticket extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $useTable = 'tickets';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id'
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id'
        ),
        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id'
        ),
        'UpdatedBy' => array(
            'className' => 'User',
            'foreignKey' => 'updated_by'
        ),
        'ErrorDetail' => array(
            'className' => 'ErrorDetail',
            'foreignKey' => 'error_detail_id'
        ),
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id'
        ) 
    );
    public $hasMany = array(
        'TicketMessage' => array(
            'className' => 'TicketMessage',
            'foreignKey' => 'ticket_id'
        )
    );

    function getTickets($startDate = '', $endDate = '')
    {
        $responseArr = array(
            'dealers' => array(),
            'tickets' => array()
        );
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
        $responseArr['tickets']['New'] = $this->find('all', array(
            'contain' => $contains,
            'conditions' => $conditions,
            'limit' => 100
        ));
        $conditions['Ticket.status'] = 'Open';
        $responseArr['tickets']['Open'] = $this->find('all', array(
            'contain' => $contains,
            'conditions' => $conditions,
            'limit' => 100
        ));
        $conditions['Ticket.status'] = 'Closed';
        $responseArr['tickets']['Closed'] = $this->find('all', array(
            'contain' => $contains,
            'conditions' => $conditions,
            'limit' => 100
        ));
        return $responseArr;
    }

    function getTicketCount($startDate = '', $endDate = '')
    {
        $responseArr = array(
            'dealers' => array(),
            'tickets' => array()
        );
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
        $responseArr['tickets']['New'] = $this->find('count', array(
            'contain' => $contains,
            'conditions' => $conditions,
        ));
        $conditions['Ticket.status'] = 'Open';
        $responseArr['tickets']['Open'] = $this->find('count', array(
            'contain' => $contains,
            'conditions' => $conditions,
        ));
        $conditions['Ticket.status'] = 'Closed';
        $responseArr['tickets']['Closed'] = $this->find('count', array(
            'contain' => $contains,
            'conditions' => $conditions,
        ));
        return $responseArr;
    }
}
