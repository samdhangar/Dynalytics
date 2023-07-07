<?php
App::uses('AppModel', 'Model');

class TransactionCategory extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'transaction_category';
    

}
