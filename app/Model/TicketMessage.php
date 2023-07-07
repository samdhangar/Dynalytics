<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class TicketMessage extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $useTable = 'ticket_messages';
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
        'Ticket' => array(
            'className' => 'Ticket',
            'foreignKey' => 'ticket_id'
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id'
        )
    );
}
