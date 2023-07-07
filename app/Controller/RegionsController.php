<?php 
App::uses('AppController', 'Controller');

/** 
 * Regiones Controller
 *
 * @property Region $Region
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class RegionsController extends AppController
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
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        $company_id = $sessionData['id'];
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
            $company_id = $current_parent_id;
        }elseif (!empty($isSuparCompany) && empty($isCompanyAdmin)) {
            $company_id = $sessionData['id'];
        }

        $conditions = array('Regions.company_id' => $company_id); 
        $conditions['Regions.status'] = 'Active';
        if ($all == "all") {
            $this->Session->delete('RegionSearch', '');
        }

        if (empty($this->request->data['Region']) && $this->Session->read('RegionSearch')) {
            $this->request->data['Region'] = $this->Session->read('RegionSearch');
        }
        if (!empty($this->request->data['Region'])) {
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Region']['name'])) {
                    $conditions['Regions.name LIKE '] = '%' . $this->request->data['Region']['name'] . '%';
                }
            } 
            $this->Session->write('RegionSearch', $this->request->data['Region']);
        }
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'AddedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                ),
                'UpdatedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                )
            ),
            'order' => ' Regions.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Regions');
        $this->set('regions', $this->paginate('Regions'));

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
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        $current_parent_id = '';
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if ($this->request->is('post')) {
              $this->Region->create();
            if (empty($this->request->data['Regions']['company_id'])) {
//                $this->request->data['Country']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['Region']['company_id'] = $company_id;
                $this->request->data['Region']['status'] ='Active';
            }
            $this->request->data['Region']['name']=$this->request->data['Region']['region'];
             
            if ($this->Region->save($this->request->data)) {
                $this->Message->setSuccess(__('The Region has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Region could not be saved. Please, try again.'));
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

/* $id = decrypt($id);
        $namedParam = getNamedParameter($this->request->params['named']);
        $namedParamArr = getNamedParameter($this->request->params['named'], true);
        */
        if (!$this->Region->exists($id)) {
            $this->Message->setWarning(__('Invalid Region'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['Country']['updated_by'])) {
//                $this->request->data['Country']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['Region']['updated_by'] = $sessionData['id'];
            }
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            $current_parent_id = '';
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
            }
            $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];

             $this->request->data['Region']['name']=$this->request->data['Region']['region'];
             $this->request->data['Region']['company_id'] = $company_id;
                $this->request->data['Region']['status'] ='Active';
            if ($this->Region->save($this->request->data)) {
                $this->Message->setSuccess(__('The Region has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The country could not be updated. Please, try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('Region.' . $this->Region->primaryKey => $id));
            $this->request->data = $this->Region->find('first', $options);
             $this->request->data['Region']['region']=$this->request->data['Region']['name'];
            
        }
 //die();
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
            $this->Region->updateAll(array('status' => "'deleted'"), array('Region.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Region has been deleted.'), $this->referer());
        }
        
        $id = decrypt($id);
        $this->Region->id = $id;
        if (!$this->Region->exists()) {
            $this->Message->setWarning(__('Invalid Region'), array('action' => 'index'));
        }
        
        //$this->request->onlyAllow('post', 'delete');
        if ($this->Region->saveField('status', 'deleted')) {
            //delete whole hierarchy state,city,taluka,village
          //  $this->Region->deleteAddressHierarchy($id, 'Region');
            
            $this->Message->setSuccess(__('The Region has been deleted.'));
            
        } else {
            $this->Message->setWarning(__('The Region could not be deleted. Please, try again.'));
        }

        return $this->redirect(array('action' => 'index'));
    }
    /*
     * get states from country
     */
  
} 