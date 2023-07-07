<?php
App::uses('AppController', 'Controller');
 
/**
 * States Controller
 *
 * @property State $State
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class StatesController extends AppController
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
        $conditions = array('NOT' => array('State.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('StateSearch', '');
        }

        if (empty($this->request->data['State']) && $this->Session->read('StateSearch')) {
            $this->request->data['State'] = $this->Session->read('StateSearch');
        }
        if (!empty($this->request->data['State'])) {
            $this->request->data['State'] = array_filter($this->request->data['State']);
            $this->request->data['State'] = array_map('trim', $this->request->data['State']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['State']['name'])) {
                    $conditions['State.name LIKE '] = '%' . $this->request->data['State']['name'] . '%';
                }
                if (isset($this->request->data['State']['country_id'])) {
                    $conditions['State.country_id'] = $this->request->data['State']['country_id'];
                }
            }
            $this->Session->write('StateSearch', $this->request->data['State']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'AddedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                ),
                'UpdatedBy' => array(
                    'fields' => 'id, first_name, last_name, phone_no, role, user_type, email, name'
                ),
                'Country' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' State.id DESC',
            'conditions' => $conditions
        ));
        $countries = $this->State->Country->getCountryList();
        $this->set(compact('countries'));
        $this->loadModel('State');
        $this->set('states', $this->paginate('State'));
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
        if (!$this->State->exists($id)) {
            $this->Message->setWarning(__('Invalid state'), array('action' => 'index'));
        }
        $options = array('contain' => false, 'conditions' => array('State.' . $this->State->primaryKey => $id));
        $this->set('state', $this->State->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        $sessionData = getMySessionData();
        if ($this->request->is('post')) {
            $this->State->create();
            if (empty($this->request->data['State']['user_id'])) {
//                $this->request->data['State']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['State']['user_id'] = $sessionData['id'];
            }
            if ($this->State->save($this->request->data)) {
                $this->Message->setSuccess(__('The state has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The state could not be saved. Please, try again.'));
            }
        }
        $countries = $this->State->Country->getCountryList();
        $this->set(compact('countries'));
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
        if (!$this->State->exists($id)) {
            $this->Message->setWarning(__('Invalid state'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['State']['updated_by'])) {
//                $this->request->data['State']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['State']['updated_by'] = $sessionData['id'];
            }
            if ($this->State->save($this->request->data)) {
                $this->Message->setSuccess(__('The state has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The state could not be updated. Please, try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('State.' . $this->State->primaryKey => $id));
            $this->request->data = $this->State->find('first', $options);
        }
        $countries = $this->State->Country->getCountryList();
        $this->set(compact('countries'));
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
        if(!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])){
            foreach ($this->request->data['delete']['id'] as $key => $del_id){
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->State->updateAll(array('status' => "'deleted'"), array('State.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The State has been deleted.'), $this->referer());
        }
        
        $id = decrypt($id);
        $this->State->id = $id;
        if (!$this->State->exists()) {
            $this->Message->setWarning(__('Invalid state'), array('action' => 'index'));
        }
        if ($this->State->saveField('status', 'deleted')) {
            //delete whole hierarchy city,taluka,village
            $this->loadModel('Country');
            $this->Country->deleteAddressHierarchy($id, 'State');
            $this->Message->setSuccess(__('The state has been deleted.'));
        } else {
            $this->Message->setWarning(__('The state could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }
}
