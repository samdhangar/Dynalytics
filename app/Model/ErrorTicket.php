<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class ErrorTicket extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $useTable = 'error_tickets';
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
        'ErrorDetail' => array(
            'className' => 'ErrorDetail',
            'foreignKey' => 'error_detail_id'
        ),
        'FileProccessingDetail' => array(
            'className' => 'FileProccessingDetail',
            'foreignKey' => 'file_processing_detail_id',
        ),
        'MachineError' => array(
            'className' => 'MachineError',
            'foreignKey' => 'machine_error_id'
        ),
        'TicketConfig' => array(
            'className' => 'TicketConfig',
            'foreignKey' => 'ticket_config_id'
        )
    );

    function getCountErrorTickets($startDate = '', $endDate = '')
    {
        $responseArr = array(
            'dealers' => array(),
            'tickets' => array()
        );
        $sessionData = getMySessionData();
        $this->User = ClassRegistry::init('User');
        if (!isSuparAdmin()) {
            $responseArr['dealers'] = getDealerId();
        } else {
            $responseArr['dealers'] = $this->User->find('list', array(
                'contain' => false,
                'fields' => 'User.id, User.id',
                'conditions' => array(
                    'User.user_type' => SUPAR_ADM,
                    'User.role' => DEALER,
                    'User.status' => 'active'
                )
            ));
        }
        $totalErrorCount = 0;
        if (!empty($responseArr['dealers'])) {
            $totalErrorCount = $this->find('count', array(
                'contain' => false,
                'conditions' => array(
                    'ErrorTicket.dealer_id' => $responseArr['dealers']
                )
            ));
        }
        return $totalErrorCount;
    }
}
