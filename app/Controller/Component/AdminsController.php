<?php
/**
 * Description of UsersController
 *
 * @author Securemetasys
 */
App::uses('UsersController', 'Controller');

class AdminsController extends UsersController
{
    public $type = 'Admin';
    public $usedController = 'admins';
    public $pageTitle = ' Admins ';
    public $viewPath = 'Users';
    public $uses = array('User');

    function beforeFilter()
    {
        $isDisplayFields = true;
        $this->set(compact('isDisplayFields'));
        parent::beforeFilter();
    }
}

?>
