<?php
/**
 * Description of UsersController
 *
 * @author Securemetasys
 */
App::uses('UsersController', 'Controller');

class DealersController extends UsersController
{
    public $type = 'Dealer';
    public $usedController = 'dealers';
    public $pageTitle = ' Dealers ';
    public $pageSubTitle = ' Dealers ';
    public $viewPath = 'Users';
    public $uses = array('User');

    function beforeFilter()
    {
        $formParamsArr = getNamedParameter($this->request->params['named'], true);
        if (!empty($formParamsArr) && $formParamsArr['type'] == DEALER) {
            $this->pageSubTitle = ' Users ';
        }
        if (isDealer()) {
            $this->pageTitle = ' Users ';
        }
        parent::beforeFilter();
    }

    public function add($parentId = 0)
    {
        $sessionData = getMySessionData();
        $parentId = decrypt($parentId);
        $clients = $branches = array();
        if (isAdminDealer() || isSuparDealer()) {
//            $clients = $this->User->getCompanyList($this->Session->read('Auth.User.id'));
            $clients = $this->User->getCompanyList($sessionData['id']);
        }
        if ((isSuparAdmin() || isAdminAdmin()) && !empty($parentId)) {
            $clients = $this->User->getCompanyList();
            if (!empty($parentId)) {
                $clients = $this->User->getCompanyList($parentId);
            }
        }
        if (!empty($clients)) {
            $branches = ClassRegistry::init('CompanyBranch')->find('list', array(
                'conditions' => array(
                    'company_id' => array_keys($clients),
                    'branch_status' => 'Active'
                )
            ));
        }
        $delerClients = array();
        $assignedClients = ClassRegistry::init('DealerCompany')->getAssignedClients();
        $isDisplayFields = false;
        if (empty($parentId)) {
            $isDisplayFields = true;
        }
        $this->set(compact('clients', 'delerClients', 'assignedClients', 'isDisplayFields', 'branches'));
        $parentId = encrypt($parentId);
        parent::add($parentId);
    }

    public function edit($id = null)
    {
        $sessionData = getMySessionData();
        $id = decrypt($id);
        $clients = $branches = array();
        if (isAdminDealer() || isSuparDealer()) {
//            $clients = $this->User->getCompanyList($this->Session->read('Auth.User.id'));
            $clients = $this->User->getCompanyList($sessionData['id']);
        }
        $userDetail = $this->User->find('first', array('fields' => 'id, parent_id, first_name, user_type, role', 'conditions' => array('User.id' => $id)));
        if ((isSuparAdmin() || isAdminAdmin()) && !empty($this->params['named']['Dealer'])) {
            $clients = $this->User->getCompanyList();
            $parentId = $userDetail['User']['parent_id'];
            if (!empty($parentId)) {
                $clients = $this->User->getCompanyList($parentId);
            }
        }
        if (isSupportAdmin()) {
            $parentId = $userDetail['User']['parent_id'];
            if (!empty($parentId)) {
                $clients = $this->User->getCompanyList($parentId);
            }
        }
        $delerClients = $dealerBranches = array();
        //01-02-2016 admin dealer also assign client to dealer
//        if(isSuparDealer()){
        if (isSuparDealer() || isAdminDealer() || isSupportAdmin()) {
            $delerClients = ClassRegistry::init('DealerCompany')->getDealerClients($id);
            $dealerBranches = ClassRegistry::init('BranchDealer')->getDealerBranch($id);
        }
        if (isSuparAdmin() || isAdminAdmin()) {
            $delerClients = ClassRegistry::init('DealerCompany')->getAssignedClients($id);
            $dealerBranches = ClassRegistry::init('BranchDealer')->getAssignedBranch($id);
        }
        $isDisplayFields = false;
        if (!empty($id)) {
            $userDetailChk = $this->User->find('first', array('fields' => 'id, first_name, user_type, role', 'conditions' => array('User.id' => $id)));
        }
        if ($userDetailChk['User']['user_type'] == SUPAR_ADM) {
            $isDisplayFields = true;
        }
        if (!empty($clients)) {
            $branches = ClassRegistry::init('CompanyBranch')->find('list', array(
                'conditions' => array(
                    'company_id' => array_keys($clients),
                    'branch_status' => 'Active'
                )
            ));
        }
        $this->set(compact('clients', 'delerClients', 'isDisplayFields', 'dealerBranches', 'branches'));
        $id = encrypt($id);
        parent::edit($id);
    }

    function support_users($companyId = null, $all = null)
    {
        $companyId = decrypt($companyId);
        $this->set(compact('companyId'));
        $dealConditions = array(
            'company_id' => $companyId
        );
        $dealerList = ClassRegistry::init('DealerCompany')->find('list', array('contain' => false, 'fields' => 'dealer_id, dealer_id', 'conditions' => $dealConditions));
        $sessionData = getMySessionData();

        $conditions = array(
            'User.id' => $dealerList
        );
        if ($all == "all") {
            $this->Session->write('SupportSearch', '');
        }
        if (empty($this->request->data['User']) && $this->Session->read('SupportSearch')) {
            $this->request->data['User'] = $this->Session->read('SupportSearch');
        }
        if (!empty($this->request->data['User'])) {
            $this->request->data['User'] = array_filter($this->request->data['User']);
            $this->request->data['User'] = array_map('trim', $this->request->data['User']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['User']['name'])) {
                    $conditions['OR'] = array(
                        'User.first_name LIKE ' => '%' . $this->request->data['User']['name'] . '%',
                        'User.last_name LIKE ' => '%' . $this->request->data['User']['name'] . '%'
                    );
                }
                if (isset($this->request->data['User']['email'])) {
                    $conditions['User.email LIKE '] = '%' . $this->request->data['User']['email'] . '%';
                }
                if (isset($this->request->data['User']['user_type'])) {
                    $conditions['User.user_type'] = $this->request->data['User']['user_type'];
                }
                if (isset($this->request->data['User']['dealer_id'])) {
                    $conditions['User.dealer_id'] = $this->request->data['User']['dealer_id'];
                }
                if (isset($this->request->data['User']['status'])) {
                    $conditions['User.status'] = $this->request->data['User']['status'];
                }
            }
            $this->Session->write($this->type . 'Search', $this->request->data['User']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'order' => ' User.id DESC',
            'fields' => array('User.*'),
            'conditions' => $conditions
        ));
        $this->set('users', $this->paginate('User'));
    }
}

?>
