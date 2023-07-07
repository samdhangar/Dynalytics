<?php
App::uses('AppModel', 'Model');

class Notmatchedserialno extends AppModel
{
     /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'serial_no';
 
    public $useTable = 'not_matched_serial_no';
   
}

?>