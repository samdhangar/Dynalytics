<?php
App::uses('AppController', 'Controller');

/**
 * Regiones Controller
 *
 * @property Region $denomination_heat_map
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
  * @property City $City
 */
class DenominationHeatMapController extends AppController
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

public function index($all='')
{
       $sessionData = getMySessionData();
          $conditions = array('DenominationHeatMap.company_id' => $sessionData['id']);
            $conditions['DenominationHeatMap.Is_deleted'] = '0';
        if ($all == "all") {
            $this->Session->write('RegionSearch', '');
        }
            $branches = ClassRegistry::init('CompanyBranch')->getAllBranch($sessionData['id']);
          $this->set(compact('branches'));  
        if (empty($this->request->data['DenominationHeatMap']) && $this->Session->read('RegionSearch')) {
            $this->request->data['DenominationHeatMap'] = $this->Session->read('RegionSearch');
        }
        if (!empty($this->request->data['DenominationHeatMap'])) {
            if (!empty($this->request->data)) {
                if (isset($this->request->data['DenominationHeatMap']['name']) && ($this->request->data['DenominationHeatMap']['name']!='')) {
                    $conditions['DenominationHeatMap.name LIKE '] = '%' . $this->request->data['DenominationHeatMap']['name'] . '%';
                }
                if (isset($this->request->data['DenominationHeatMap']['branch_id']) && ($this->request->data['DenominationHeatMap']['branch_id']!='') ) {
                    $conditions['DenominationHeatMap.branch_id'] =$this->request->data['DenominationHeatMap']['branch_id'];
                }
            } 
            $this->Session->write('ConfigurationSearch', $this->request->data['DenominationHeatMap']);
        } 
        $this->AutoPaginate->setPaginate(array(
            'contain' => array( 
                'CompanyBranches' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' DenominationHeatMap.id DESC',
            'conditions' => $conditions
        ));
     //   $this->loadModel('Configuration');
         $this->loadModel('DenominationHeatMap');
        $this->set('DenominationHeatMap', $this->paginate('DenominationHeatMap'));
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
        $stations = array();
        $this->set(compact('branches'));

        if ($this->request->is('post')) {
            $this->DenominationHeatMap->create();
            if (empty($this->request->data['DenominationHeatMap']['branch_id'])){
                $barnch_id=0;
            }else{
                $barnch_id=$this->request->data['DenominationHeatMap']['branch_id'];
            }
            if (empty($this->request->data['DenominationHeatMap']['machine_id'])){
                $machine_id=0;
            }else{
                $machine_id=$this->request->data['DenominationHeatMap']['machine_id'];
            }


            $options = array('contain' => false, 'conditions' => array('DenominationHeatMap.' . 'branch_id' => $barnch_id,'DenominationHeatMap.' . 'machine_id' => $machine_id, 'DenominationHeatMap.' . 'Is_deleted' => 0));
            $count_total = $this->DenominationHeatMap->find('count', $options);
            if($count_total==0){

                if (empty($this->request->data['DenominationHeatMap']['company_id'])) {
                    $this->request->data['DenominationHeatMap']['company_id'] = $sessionData['id'];
                }
                $this->request->data['DenominationHeatMap']['updated_by'] =$this->request->data['DenominationHeatMap']['company_id'];
                $this->request->data['DenominationHeatMap']['Is_default'] ='0';
                $this->request->data['DenominationHeatMap']['Is_deleted'] ='0';
                if ($this->DenominationHeatMap->save($this->request->data)) {
                    $this->Message->setSuccess(__('The Configuration has been saved.'));
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Message->setWarning(__('The Machine could not be saved. Please, try again.'));
                }
            }else{
                    $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['DenominationHeatMap']['branch_id']);
                    $this->set(compact('machines'));
                    $this->Message->setWarning(__('Duplicate Entry.'));
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
         $this->loadModel('CompanyBranch');
     $branches = ClassRegistry::init('CompanyBranch')->getBranchList($sessionData['id']);


        if (!$this->DenominationHeatMap->exists($id)) {
            $this->Message->setWarning(__('Invalid Configuration'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
 if (empty($this->request->data['DenominationHeatMap']['branch_id'])){
                    $barnch_id=0;
              }else{
                    $barnch_id=$this->request->data['DenominationHeatMap']['branch_id'];
              }
              if (empty($this->request->data['DenominationHeatMap']['machine_id'])){
                    $machine_id=0;
              }else{
                    $machine_id=$this->request->data['DenominationHeatMap']['machine_id'];
              }

              $options_count = array('contain' => false, 'conditions' => array('DenominationHeatMap.' . 'branch_id' => $barnch_id,'DenominationHeatMap.' . 'machine_id' => $machine_id ,  "NOT" => array( "DenominationHeatMap.id" =>$id)));
               $count_total = $this->DenominationHeatMap->find('count', $options_count);

                if($count_total==0){


            if (empty($this->request->data['DenominationHeatMap']['updated_by'])) {
//                $this->request->data['Country']['updated_by'] = $this->Session->read('Auth.User.id');
                $this->request->data['DenominationHeatMap']['updated_by'] = $sessionData['id'];
            }
             $this->request->data['DenominationHeatMap']['machine_id']=$this->request->data['DenominationHeatMap']['machine_id'];
             $this->request->data['DenominationHeatMap']['company_id'] = $sessionData['id'];
                $this->request->data['DenominationHeatMap']['status'] ='Active';
                 $this->request->data['DenominationHeatMap']['branch_id']=$this->request->data['DenominationHeatMap']['branch_id'];

           unset($this->request->data['DenominationHeatMap']['branches']); // remove item at index 0
            $foo2 = array_values($this->request->data);
            if ($this->DenominationHeatMap->save($this->request->data)) {
                $this->Message->setSuccess(__('The Denomination Heat Map has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The Denominati on HeatMap could not be updated. Please, try again.'));
            }
             }else{
             $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['DenominationHeatMap']['branch_id']);
              $this->set(compact('machines'));
        $this->Message->setWarning(__('Duplicate Entry.'));
    }
        } else {
            $options = array('contain' => false, 'conditions' => array('DenominationHeatMap.' . $this->DenominationHeatMap->primaryKey => $id));
            $this->request->data = $this->DenominationHeatMap->find('first', $options);
             $this->request->data['DenominationHeatMap']['machine_id']=$this->request->data['DenominationHeatMap']['machine_id'];

              $machines = array();
              $machines = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['DenominationHeatMap']['branch_id']);


        }

if($this->request->data['DenominationHeatMap']['Is_default']!=1){
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
            $this->Machine->updateAll(array('Is_deleted' => "'1'"), array('Machine.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Machine has been deleted.'), $this->referer());
        }

        $id = decrypt($id);
        $this->DenominationHeatMap->id = $id;
        if (!$this->DenominationHeatMap->exists()) {
            $this->Message->setWarning(__('Invalid Machine'), array('action' => 'index'));
        }

        //$this->request->onlyAllow('post', 'delete');
        if ($this->DenominationHeatMap->saveField('Is_deleted', '1')) {
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
   function validate($value='')
    {
        # code...
    }
}
