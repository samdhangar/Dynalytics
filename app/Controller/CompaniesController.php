<?php
/**
 * Description of UsersController
 *
 * @author Securemetasys
 */
App::uses('UsersController', 'Controller');

class CompaniesController extends UsersController
{
    public $type = 'Company';
    public $usedController = 'companies';
    public $pageTitle = 'Financial Institution ';
    public $pageSubTitle = 'Financial Institution';
    public $viewPath = "Users";
    public $uses = array('User');

    function beforeFilter()
    {
        $formParamsArr = getNamedParameter($this->request->params['named'], true);
        if (!empty($formParamsArr) && $formParamsArr['type'] == DEALER) {
            $this->pageSubTitle = ' Users ';
        }
        if (isCompany()) {
            $this->pageTitle = ' Users ';
        }
        parent::beforeFilter();
    }

    public function add($parentId = 0)
    {
        $sessionData = getMySessionData();
        $parentId = decrypt($parentId);
        $branches = array();
        if (isSuparCompany() || isCompanyAdmin()) {
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            }
            $suparid = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($suparid);
        }
        if ((isSuparAdmin() || isAdminAdmin()) && !empty($parentId)) {
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            }
            $suparid = !empty($current_parent_id) ? $current_parent_id['User']['company_parent_id'] : $sessionData['id'];
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($suparid);
             
        }
        $assignedAdmins = $branchAdmins = array();
        if (isSuparCompany() || isSuparAdmin() || isAdminAdmin()) {
           $assignedAdmins = ClassRegistry::init('BranchAdmin')->getAssignedAdmins();
        }
        $isDisplayFields = false;
        if (empty($parentId)) {
            $isDisplayFields = true;
        }
        $this->set(compact('branches', 'assignedAdmins', 'branchAdmins', 'isDisplayFields'));
        $parentId = encrypt($parentId);
  
 
        parent::add($parentId);

    }

    public function edit($id = null)
    {
        $sessionData = getMySessionData();
        $id = decrypt($id);
        $branches = array();
        if (isCompany()) {
//            $suparid = (isSuparCompany() ? $this->Session->read('Auth.User.id') : $this->Session->read('Auth.User.parent_id'));
            $suparid = (isSuparCompany() ? $sessionData['id'] : $sessionData['parent_id']);
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($suparid);
        }
        if (isSuparAdmin() || isAdminAdmin()) {
            $namedParamArr = getNamedParameter($this->request->params['named'], true);
            if (!empty($namedParamArr['value'])) {
                $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists($namedParamArr['value']);
            }
        }
        $assignedAdmins = $branchAdmins = array();
        if (isSuparCompany() || isSuparAdmin() || isAdminAdmin()) {
            $branchAdmins = ClassRegistry::init('BranchAdmin')->getAssignedAdmins($id);
            $assignedAdmins = ClassRegistry::init('BranchAdmin')->getAssignedAdmins();
            if (count($branchAdmins) > 1 && $this->type == COMPANY) {
                $this->set('userTypes', $this->User->getTypes($this->type, REGION));
            }
        }
        $isDisplayFields = false;
        if (!empty($id)) {
            $userDetailChk = $this->User->find('first', array('fields' => 'id, first_name, user_type, role', 'conditions' => array('User.id' => $id)));
        }
        if ($userDetailChk['User']['user_type'] == SUPAR_ADM) {
            $isDisplayFields = true;
        }
        $this->set(compact('branches', 'assignedAdmins', 'branchAdmins', 'isDisplayFields'));
        
        $id = encrypt($id);
        parent::edit($id);
        if(isset($this->request->data['User']['user_type']) && (count($branchAdmins) > 1) && $this->request->data['User']['user_type'] == REGIONAL){
            $this->set('userTypes', $this->User->getTypes($this->type, REGION));
        }
        if(isset($this->request->data['User']['user_type']) && (count($branchAdmins) ==  1) && $this->request->data['User']['user_type'] == REGIONAL){
            $this->set('userTypes', $this->User->getTypes($this->type));
        }
    }
}

?>
