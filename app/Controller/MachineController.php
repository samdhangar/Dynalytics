<?php
App::uses('AppController', 'Controller');

/** 
 * Regiones Controller
 *
 * @property Region $Machine
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
  * @property City $City
 */
class MachineController extends AppController
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
         $conditions = array('Machine.company_id' => $sessionData['id']); 
           $conditions['Machine.status'] = 'Active';
        if ($all == "all") {
            $this->Session->write('RegionSearch', '');
        }

        if (empty($this->request->data['Machine']) && $this->Session->read('RegionSearch')) {
            $this->request->data['Machine'] = $this->Session->read('RegionSearch');
        }
        if (!empty($this->request->data['Machine'])) {
            $this->request->data['Machine'] = array_filter($this->request->data['Country']);
            $this->request->data['Machine'] = array_map('trim', $this->request->data['Country']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Machine']['name'])) {
                    $conditions['Machine.name LIKE '] = '%' . $this->request->data['Machine']['name'] . '%';
                }
            } 
            $this->Session->write('RegionSearch', $this->request->data['Machine']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array( 
                'CompanyBranches' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' Machine.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Machine');
        $this->set('machine', $this->paginate('Machine'));

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
         $sessionData = getMySessionData();
       $this->loadModel('CompanyBranch'); 
     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($sessionData['id']);
         $this->set(compact('branches'));
          if ($this->request->is('post')) {
              $this->Machine->create();
            if (empty($this->request->data['Machine']['company_id'])) {
//                $this->request->data['Country']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['Machine']['company_id'] = $sessionData['id'];
                $this->request->data['Machine']['status'] ='Active';
            }
            $this->request->data['Machine']['name']=$this->request->data['Machine']['machine'];
            
             $this->request->data['Machine']['branch_id']=$this->request->data['Machine']['branches'];
             
           unset($this->request->data['Machine']['branches']); // remove item at index 0
            $foo2 = array_values($this->request->data); 
           
            if ($this->Machine->save($this->request->data)) {
                $this->Message->setSuccess(__('The Machine has been saved.'));
               // return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Machine could not be saved. Please, try again.'));
            }
            
        }
       
    }

 
function get_machine($branchId = null)
    {
 $this->loadModel('Machine'); 
    $this->layout = false;
        $machineList = $this->Machine->getMachineList($this->request->data);
        $addData = $machineList;
        $addComboTitle = __('Select All');
        $addDataTitle = __('No any Station');
        
        $this->set(compact('addDataTitle', 'addComboTitle', 'addData'));
        $this->render('/Elements/makeAddOptions');
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

/* $id = decrypt($id);
        $namedParam = getNamedParameter($this->request->params['named']);
        $namedParamArr = getNamedParameter($this->request->params['named'], true);
        */
         $this->loadModel('CompanyBranch'); 
     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($sessionData['id']);
         $this->set(compact('branches'));
        if (!$this->Machine->exists($id)) {
            $this->Message->setWarning(__('Invalid Machine'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['Machine']['updated_by'])) {
//                $this->request->data['Country']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['Machine']['updated_by'] = $sessionData['id'];
            }
             $this->request->data['Machine']['name']=$this->request->data['Machine']['machine'];
             $this->request->data['Machine']['company_id'] = $sessionData['id'];
                $this->request->data['Machine']['status'] ='Active';
                 $this->request->data['Machine']['branch_id']=$this->request->data['Machine']['branches'];
             
           unset($this->request->data['Machine']['branches']); // remove item at index 0
            $foo2 = array_values($this->request->data); 
            if ($this->Machine->save($this->request->data)) {
                $this->Message->setSuccess(__('The Machine has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Machine could not be updated. Please, try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('Machine.' . $this->Machine->primaryKey => $id));
            $this->request->data = $this->Machine->find('first', $options);
             $this->request->data['Machine']['machine']=$this->request->data['Machine']['name'];
            
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
        print_r($this->request->data);
       
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
          print_r($this->request->data);
         
        if ($this->request->is(array('post', 'put'))) {
            $this->Machine->updateAll(array('status' => "'deleted'"), array('Machine.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Machine has been deleted.'), $this->referer());
        }
  print_r($this->request->data);
        
        $id = decrypt($id);
        $this->Machine->id = $id;
        if (!$this->Machine->exists()) {
            $this->Message->setWarning(__('Invalid Machine'), array('action' => 'index'));
        }
          print_r($this->request->data);
        
        //$this->request->onlyAllow('post', 'delete');
        if ($this->Machine->saveField('status', 'deleted')) {
            //delete whole hierarchy state,city,taluka,village
          //  $this->Region->deleteAddressHierarchy($id, 'Region');
            
            $this->Message->setSuccess(__('The Machine has been deleted.'));
            
        } else {
            $this->Message->setWarning(__('The Machine could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('action' => 'index'));
    }
    /*
     * get states from country
     */
  
}
