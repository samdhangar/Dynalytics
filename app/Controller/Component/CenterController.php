<?php
App::uses('AppController', 'Controller');

/**
 * Countries Controller
 *
 * @property Country $Country
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class CenterController extends AppController
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
        $conditions = array('NOT' => array('Center.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('CenterSearch', '');
        }

        if (empty($this->request->data['Country']) && $this->Session->read('CountrySearch')) {
            $this->request->data['Country'] = $this->Session->read('CountrySearch');
        }
        if (!empty($this->request->data['Country'])) {
            $this->request->data['Country'] = array_filter($this->request->data['Country']);
            $this->request->data['Country'] = array_map('trim', $this->request->data['Country']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Country']['name'])) {
                    $conditions['Country.name LIKE '] = '%' . $this->request->data['Country']['name'] . '%';
                }
            } 
            $this->Session->write('CountrySearch', $this->request->data['Country']);
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
            'order' => ' Country.id DESC',
            'conditions' => $conditions
        ));
        $this->set('countries', $this->paginate('Country'));
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
        if (!$this->Center->exists($id)) {
            $this->Message->setWarning(__('Invalid country'), array('action' => 'index'));
        }
        $options = array('contain' => false, 'conditions' => array('Center.' . $this->Center->primaryKey => $id));
        $this->set('center', $this->Center->find('first', $options));
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
            $this->Center->create();
            if (empty($this->request->data['Center']['user_id'])) {
//                $this->request->data['Country']['user_id'] = $this->Session->read('Auth.User.id');
                $this->request->data['Center']['user_id'] = $sessionData['id'];
            }
            if ($this->Center->save($this->request->data)) {
                $this->Message->setSuccess(__('The country has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The country could not be saved. Please, try again.'));
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
        if (!$this->Country->exists($id)) {
            $this->Message->setWarning(__('Invalid country'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['Country']['updated_by'])) {
//                $this->request->data['Country']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['Country']['updated_by'] = $sessionData['id'];
            }
            if ($this->Country->save($this->request->data)) {
                $this->Message->setSuccess(__('The country has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The country could not be updated. Please, try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('Country.' . $this->Country->primaryKey => $id));
            $this->request->data = $this->Country->find('first', $options);
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
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->Country->updateAll(array('status' => "'deleted'"), array('Country.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The country has been deleted.'), $this->referer());
        }

        $id = decrypt($id);
        $this->Country->id = $id;
        if (!$this->Country->exists()) {
            $this->Message->setWarning(__('Invalid country'), array('action' => 'index'));
        }
        //$this->request->onlyAllow('post', 'delete');
        if ($this->Country->saveField('status', 'deleted')) {
            //delete whole hierarchy state,city,taluka,village
            $this->Country->deleteAddressHierarchy($id, 'Country');
            $this->Message->setSuccess(__('The country has been deleted.'));
        } else {
            $this->Message->setWarning(__('The country could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }
    /*
     * get states from country
     */

    function getStates($countryId = null)
    {
        $this->layout = false;
        $this->loadModel('State');
        $addData = $this->State->find('list', array(
            'contain' => false,
            'order' => 'name ASC',
            'fields' => 'id,name',
            'conditions' => array('State.country_id' => $countryId, 'State.status' => 'active')
            )
        );
        $this->set(compact('addData'));
        $this->set('addDataTitle', __('No Record'));
        $this->set('addComboTitle', __('Select State'));
        $this->render('/Elements/makeAddOptions');
    }
    /*
     * get cities from state
     */

    function getCities($stateId = null)
    {
        $this->layout = false;
        $this->loadModel('City');
        $addData = $this->City->find('list', array(
            'contain' => false,
            'order' => 'name ASC',
            'fields' => 'id,name',
            'conditions' => array('City.state_id' => $stateId, 'City.status' => 'active')
            )
        );
        $this->set(compact('addData'));
        $this->set('addDataTitle', __('No Record'));
        $this->set('addComboTitle', __('Select City'));
        $this->render('/Elements/makeAddOptions');
    }
    /*
     * get taluka from city
     */

    function getTalukas($cityId = null)
    {
        $this->layout = false;
        $this->loadModel('Taluka');
        $addData = $this->Taluka->find('list', array('order' => 'name ASC', 'fields' => 'id,name', 'conditions' => array('Taluka.city_id' => $cityId, 'Taluka.status' => 'active')));
        $this->set(compact('addData'));
        $this->set('addDataTitle', __('No Record'));
        $this->set('addComboTitle', __('Select Taluka'));
        $this->render('/Elements/makeAddOptions');
    }
    /*
     * get villages from taluka
     */

    function getVillages($talukaId = null)
    {
        $this->layout = false;
        $this->loadModel('Village');
        $addData = $this->Village->find('list', array('order' => 'name ASC', 'fields' => 'id,name', 'conditions' => array('Village.taluka_id' => $talukaId, 'Village.status' => 'active')));
        $this->set(compact('addData'));
        $this->set('addDataTitle', __('No Record'));
        $this->set('addComboTitle', __('Select Village'));
        $this->render('/Elements/makeAddOptions');
    }
}
