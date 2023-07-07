<?php
/**
 * Description of UsersController
 *
 * @author Securemetasys
 */
App::uses('UsersController', 'Controller');

class SubDealersController extends UsersController
{
    public $type = 'Dealer';
    public $usedController = 'sub_dealers';
    public $subType = 'sub_dealer';
    public $pageTitle = ' Sub Dealers ';
    public $viewPath = 'Users';
    public $uses = array('User');

}

?>
