<?php
App::uses('AppController', 'Controller');

class MonitoringController extends AppController
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
        $this->set('title',__('Performance Management'));
        $this->render('/Pages/unknown');
    }
}
