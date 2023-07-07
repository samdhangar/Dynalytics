<?php
App::uses('AppController', 'Controller');

class BillingController extends AppController
{
    public $components = array('Auth');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
    }

    function index()
    {
        $this->set('title',__('Billing'));
        $this->render('/Pages/unknown');
    }
}
