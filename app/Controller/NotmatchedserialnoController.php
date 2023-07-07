<?php
App::uses('AppController', 'Controller');

class NotmatchedserialnoController extends AppController
{

    public $components = array('Paginator', 'Auth', 'Session');
   public function index()
   {
    $this->loadModel('Notmatchedserialno');
    $get_serialData=$this->Notmatchedserialno->find('all');

    $this->AutoPaginate->setPaginate(array(
        'contain' => array(
            'AddedBy' => array(
                'fields' => 'id, serial_no, date, created_date'
            ),
            'UpdatedBy' => array(
                'fields' => 'id, serial_no, date, created_date'
            )
        ),
        'order' => ' Notmatchedserialno.id DESC'
    ));
    $this->set(compact('get_serialData'));
    $this->set('get_serialData', $this->paginate('Notmatchedserialno'));
   }
}

?>