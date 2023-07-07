<?php
App::uses('AppModel', 'Model');

class TransactionType extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'transaction_type';
}
