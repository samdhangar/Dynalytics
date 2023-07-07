<?php
App::uses('AppController', 'Controller');

/**
 * Cities Controller
 *
 * @property City $City
 * @property PaginatorComponent $Paginator
 * @property AUthComponent $AUth
 * @property SessionComponent $Session
 */
class CitiesController extends AppController
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
        $conditions = array('NOT' => array('City.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('CitySearch', '');
        }

        if (empty($this->request->data['City']) && $this->Session->read('CitySearch')) {
            $this->request->data['City'] = $this->Session->read('CitySearch');
        }
        if (!empty($this->request->data['City'])) {
            $this->request->data['City'] = array_filter($this->request->data['City']);
            $this->request->data['City'] = array_map('trim', $this->request->data['City']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['City']['name'])) {
                    $conditions['City.name LIKE '] = '%' . $this->request->data['City']['name'] . '%';
                }
                if (isset($this->request->data['City']['country_id'])) {
                    $conditions['City.country_id'] = $this->request->data['City']['country_id'];
                }
                if (isset($this->request->data['City']['state_id'])) {
                    $conditions['City.state_id'] = $this->request->data['City']['state_id'];
                }
            }
            $this->Session->write('CitySearch', $this->request->data['City']);
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
                ),
                'State' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' City.id DESC',
            'conditions' => $conditions
        ));
        $countries = $this->City->Country->getCountryList();
        $cId = !empty($conditions['City.country_id']) ? $conditions['City.country_id'] : null;
        $states = $this->City->State->getStateList($cId);
        $this->set(compact('countries', 'states'));
        $this->loadModel('City');
        $this->set('cities', $this->paginate('City'));
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
        if (!$this->City->exists($id)) {
            $this->Message->setWarning(__('Invalid city'), array('action' => 'index'));
        }
        $conditions = array('NOT' => array('City.status' => 'deleted'), 'City.' . $this->City->primaryKey => $id);
        $options = array('contain' => false, 'conditions' => $conditions);
        $this->set('city', $this->City->find('first', $options));
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
            $this->City->create();
            if (empty($this->request->data['City']['user_id'])) {
//                $this->request->data['City']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['City']['user_id'] = $sessionData['id'];
            }
            if ($this->City->save($this->request->data)) {
                $this->Message->setSuccess(__('The city has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The city could not be saved. Please, try again.'));
            }
        }
        $countries = $this->City->Country->getCountryList();
        $states = (!empty($this->request->data['City']['country_id'])) ? $this->City->State->getStateList($this->request->data['City']['country_id']) : array();
        $this->set(compact('countries', 'states'));
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
        if (!$this->City->exists($id)) {
            $this->Message->setWarning(__('Invalid city'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['City']['updated_by'])) {
//                $this->request->data['City']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['City']['updated_by'] = $sessionData['id'];
            }
            if ($this->City->save($this->request->data)) {
                $this->Message->setSuccess(__('The city has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The city could not be updated. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('City.' . $this->City->primaryKey => $id));
            $this->request->data = $this->City->find('first', $options);
        }
        $countries = $this->City->Country->getCountryList();
        $states = (!empty($this->request->data['City']['country_id'])) ? $this->City->State->getStateList($this->request->data['City']['country_id']) : array();
        $this->set(compact('countries', 'states'));
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
            $this->City->updateAll(array('status' => "'deleted'"), array('City.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The city has been deleted.'), $this->referer());
        }
        
        $id = decrypt($id);
        $this->City->id = $id;
        if (!$this->City->exists($id)) {
            $this->Message->setWarning(__('Invalid city'), array('action' => 'index'));
        }
        if ($this->City->saveField('status', 'deleted')) {
            //delete whole hierarchy city,taluka,village
            $this->loadModel('Country');
            $this->Country->deleteAddressHierarchy($id, 'City');
            $this->Message->setSuccess(__('The city has been deleted.'));
        } else {
            $this->Message->setWarning(__('The city could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }
}
