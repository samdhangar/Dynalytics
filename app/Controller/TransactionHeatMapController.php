<?php
App::uses('AppController', 'Controller');

/** 
 * Transaction Controller
 *
 * @property Region $transaction_heat_maps
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class TransactionHeatMapController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Auth', 'Session');

    /**
     * beforefilter method
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
         $sessionData = getMySessionData();
        $conditions = array('TransactionHeatMap.company_id' => $sessionData['id']); 
          $conditions['TransactionHeatMap.Is_deleted'] = '0';
        if ($all == "all") {
            $this->Session->write('RegionSearch', '');
        }
$branches = ClassRegistry::init('CompanyBranch')->getAllBranch($sessionData['id']);
          $this->set(compact('branches')); 
        if (empty($this->request->data['TransactionHeatMap']) && $this->Session->read('RegionSearch')) {
            $this->request->data['TransactionHeatMap'] = $this->Session->read('RegionSearch');
        }
        if (!empty($this->request->data['TransactionHeatMap'])) {
            if (!empty($this->request->data)) {
                if (isset($this->request->data['TransactionHeatMap']['name']) && ($this->request->data['TransactionHeatMap']['name']!='')) {
                    $conditions['TransactionHeatMap.name LIKE '] = '%' . $this->request->data['TransactionHeatMap']['name'] . '%';
                }
                 if (isset($this->request->data['TransactionHeatMap']['branch_id']) && ($this->request->data['TransactionHeatMap']['branch_id']!='') ) {
                    $conditions['TransactionHeatMap.branch_id'] =$this->request->data['TransactionHeatMap']['branch_id'];
                   
                }
            } 
            $this->Session->write('RegionSearch', $this->request->data['TransactionHeatMap']);
        }
        $this->AutoPaginate->setPaginate(array(
             'contain' => array( 
                'CompanyBranches' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' TransactionHeatMap.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('TransactionHeatMap');
        $this->set('TransactionHeatMap', $this->paginate('TransactionHeatMap'));

    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {
        $id = decrypt($id);
        if (!$this->Country->exists($id)) {
            $this->Message->setWarning(__('Invalid country'), array('action' => 'index'));
        }
        $options = array('contain' => false, 'conditions' => array('Country.' . $this->Country->primaryKey => $id));
        $this->set('country', $this->Country->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
       // echo "<pre>";
 $sessionData = getMySessionData();
       $this->loadModel('CompanyBranch'); 
        $this->loadModel('DenominationHeatMap');
     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($sessionData['id']);
         $this->set(compact('branches'));
        $sessionData = getMySessionData();
        if ($this->request->is('post')) {
            $this->loadModel('TransactionHeatMap'); 
                $this->TransactionHeatMap->create();
 
 if (empty($this->request->data['TransactionHeatMap']['branch_id'])){
                    $barnch_id=0;
              }else{
                    $barnch_id=$this->request->data['TransactionHeatMap']['branch_id'];
              }
              if (empty($this->request->data['TransactionHeatMap']['machine_id'])){
                    $machine_id=0;
              }else{
                    $machine_id=$this->request->data['TransactionHeatMap']['machine_id'];
              }
              $options = array('contain' => false, 'conditions' => array('TransactionHeatMap.' . 'branch_id' => $barnch_id,'TransactionHeatMap.' . 'machine_id' => $machine_id));
            $count_total = $this->TransactionHeatMap->find('count', $options);
           if($count_total==0){
            
           /*      $this->loadModel('SideLog'); 
  
 $result_graph = $this->SideLog->query($sql_tableData);*/
            if (empty($this->request->data['TransactionHeatMap']['company_id'])) {
//                $this->request->data['Country']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['TransactionHeatMap']['company_id'] = $sessionData['id'];
               // $this->request->data['TrannsactionHeatMap']['status'] ='0';
            }
            if (empty($this->request->data['TransactionHeatMap']['machine_id'])) {
                 $this->request->data['TransactionHeatMap']['machine_id'] =0;
               
            }
            $this->request->data['TransactionHeatMap']['name']=$this->request->data['TransactionHeatMap']['name'];

            if ($this->TransactionHeatMap->save($this->request->data)) {
                $this->Message->setSuccess(__('The Transaction Heat Map has been saved.'));
                 return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Transaction Heat Map could not be saved. Please, try again.'));
            }
              }else{
             $machines = array();
              $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['TransactionHeatMap']['branch_id']);
              $this->set(compact('machines'));
        $this->Message->setWarning(__('Duplicate Entery.'));
    } 
      
        }
       
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $sessionData = getMySessionData();
        $id = decrypt($id);
 $sessionData = getMySessionData();
       $this->loadModel('CompanyBranch'); 
        $this->loadModel('DenominationHeatMap');
     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($sessionData['id']);
        /* $this->set(compact('branches'));*/
/* $id = decrypt($id);
        $namedParam = getNamedParameter($this->request->params['named']);
        $namedParamArr = getNamedParameter($this->request->params['named'], true);
        */
        if (!$this->TransactionHeatMap->exists($id)) {
            $this->Message->setWarning(__('Invalid Region'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {

             if (empty($this->request->data['TransactionHeatMap']['branch_id'])){
                    $barnch_id=0;
              }else{
                    $barnch_id=$this->request->data['TransactionHeatMap']['branch_id'];
              }
              if (empty($this->request->data['TransactionHeatMap']['machine_id'])){
                    $machine_id=0;
              }else{
                    $machine_id=$this->request->data['TransactionHeatMap']['machine_id'];
              }

                $options_count = array('contain' => false, 'conditions' => array('TransactionHeatMap.' . 'branch_id' => $barnch_id,'TransactionHeatMap.' . 'machine_id' => $machine_id ,  "NOT" => array( "TransactionHeatMap.id" =>$id)));
            $count_total = $this->TransactionHeatMap->find('count', $options_count);

            
if($count_total==0){

            if (empty($this->request->data['Country']['updated_by'])) {
//                $this->request->data['Country']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['TransactionHeatMap']['updated_by'] = $sessionData['id'];
            }
             $this->request->data['TransactionHeatMap']['name']=$this->request->data['TransactionHeatMap']['name'];
             $this->request->data['TransactionHeatMap']['company_id'] = $sessionData['id'];
                $this->request->data['TransactionHeatMap']['status'] ='Active';
            if ($this->TransactionHeatMap->save($this->request->data)) {
                $this->Message->setSuccess(__('The Transaction Heat Map has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Transaction Heat Map could not be updated. Please try again.'));
            }
             }else{
             $machines = array();
              $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['TransactionHeatMap']['branch_id']);
              $this->set(compact('machines'));
        $this->Message->setWarning(__('Duplicate Entery.'));
    } 
        } else {
            $options = array('contain' => false, 'conditions' => array('TransactionHeatMap.' . $this->TransactionHeatMap->primaryKey => $id));
            $this->request->data = $this->TransactionHeatMap->find('first', $options);
             $this->request->data['TransactionHeatMap']['region']=$this->request->data['TransactionHeatMap']['name'];
             $machines = array();
              $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['TransactionHeatMap']['branch_id']);
        }
 //die();
        if($this->request->data['TransactionHeatMap']['Is_default']!=1){
     $this->set(compact('branches'));
     $this->set(compact('machines'));
}
        $this->set('edit', 1);
        $this->render('add');
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
       // echo "<pre>";
       
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
          
         
        if ($this->request->is(array('post', 'put'))) {
            $this->TransactionHeatMap->updateAll(array('Is_deleted' => "'1'"), array('TransactionHeatMap.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Transaction Heat Map has been deleted.'), $this->referer());
        }
   
        
        $id = decrypt($id);
        $this->TransactionHeatMap->id = $id;
        if (!$this->TransactionHeatMap->exists()) {
            $this->Message->setWarning(__('Invalid Transaction Heat Map'), array('action' => 'index'));
        }
          
        
        //$this->request->onlyAllow('post', 'delete');
        if ($this->TransactionHeatMap->saveField('Is_deleted', '1')) {
            //delete whole hierarchy state,city,taluka,village
          //  $this->Region->deleteAddressHierarchy($id, 'Region');
            
            $this->Message->setSuccess(__('The Transaction Heat Map has been deleted.'));
            
        } else {
            $this->Message->setWarning(__('The Transaction Heat Map could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('action' => 'index'));
    }
    /*
     * get states from country
     */
  
}
