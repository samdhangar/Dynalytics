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
class LocationController extends AppController
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
          $conditions['Location.status'] = 'Active';
        if ($all == "all") {
            $this->Session->delete('LocationSearch', '');
        }

        if (empty($this->request->data['Location']) && $this->Session->read('LocationSearch')) {
            $this->request->data['Location'] = $this->Session->read('LocationSearch');
        }
        if (!empty($this->request->data['Location'])) {
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Location']['name'])) {
                    $conditions['Location.name LIKE '] = '%' . $this->request->data['Location']['name'] . '%';
                }
            } 
            $this->Session->write('LocationSearch', $this->request->data['Location']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'AddedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                ),
                'UpdatedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                )
            ),
            'order' => ' Location.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Location');
        $this->set('location', $this->paginate('Location'));

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
        if ($this->request->is('post')) {
              $this->Location->create();
            if (!empty($this->request->data['Location']['location'])) {
//                $this->request->data['Country']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['Location']['status'] ='Active';
                $this->request->data['Location']['name']=$this->request->data['Location']['location'];
            }
            $this->request->data['Location']['name']=$this->request->data['Location']['location'];
             
            if ($this->Location->save($this->request->data)) {
                $this->Message->setSuccess(__('The Location has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Location could not be saved. Please try again.'));
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
        if (!$this->Location->exists($id)) {
            $this->Message->setWarning(__('Invalid Location'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
             $this->request->data['Location']['name']=$this->request->data['Location']['location'];
             $this->request->data['Location']['company_id'] = $sessionData['id'];
                $this->request->data['Location']['status'] ='Active';
            if ($this->Location->save($this->request->data)) {
                $this->Message->setSuccess(__('The Location has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Location could not be updated. Please try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('Location.' . $this->Location->primaryKey => $id));
            $this->request->data = $this->Location->find('first', $options);
             $this->request->data['Location']['location']=$this->request->data['Location']['name'];
            
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
       
       
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
         
         
        if ($this->request->is(array('post', 'put'))) {
            $this->Location->updateAll(array('status' => "'deleted'"), array('Location.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Location has been deleted.'), $this->referer());
        }
 
        
        $id = decrypt($id);
        $this->Location->id = $id;
        if (!$this->Location->exists()) {
            $this->Message->setWarning(__('Invalid Location'), array('action' => 'index'));
        }
         
        
        //$this->request->onlyAllow('post', 'delete');
        if ($this->Location->saveField('status', 'deleted')) {
            //delete whole hierarchy state,city,taluka,village
          //  $this->Region->deleteAddressHierarchy($id, 'Region');
            
            $this->Message->setSuccess(__('The Location has been deleted.'));
            
        } else {
            $this->Message->setWarning(__('The Location could not be deleted. Please try again.'));
        }

        return $this->redirect(array('action' => 'index'));
    }
    /*
     * get states from country
     */
  
} 