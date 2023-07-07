<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel');
if (!class_exists('PHPExcel')) {
    throw new CakeException('Vendor class PHPExcel not found!');
}
/**
 * Stations Controller
 *
 * @property Station $Station
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
require ROOT . DS . 'vendor' . DS . 'autoload.php';
use Aws\S3\S3Client;

class StationsController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $companyId = 0;
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
        $sessData = getMySessionData();
        $this->companyId = 0;
        if (isSuparCompany()) {
            $this->companyId = $sessData['id'];
        } elseif (isCompanyAdmin() || isCompanyBranchAdmin() || isCompanyRegionalAdmin()) {
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
            $this->companyId = $current_parent_id;
        }
        $this->set('companyId', $this->companyId);
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
        $sesData = getMySessionData();
        $conditions = $company_ids = array();
        if (isSuparDealer()) {
            $company_ids = array_keys($sesData['assign_companies']);
            $conditions = array(
                'NOT' => array('Station.status' => 'deleted'),
                'Station.company_id IN' => $company_ids
            );
        } else if (isCompanyAdmin() || isSuparCompany()) {
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            $current_parent_id = '';
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sesData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
            }
            $company_id = !empty($current_parent_id) ? $current_parent_id : $sesData['id'];
            $conditions = array(
                'NOT' => array('Station.status' => 'deleted'),
                'Station.company_id' => $company_id
            );
        }
        if ($this->Session->check('Auth.User.BranchDetail.id')) {
            $conditions['Station.branch_id'] = $this->Session->read('Auth.User.BranchDetail.id');
        }
        if (!isCompanyAdmin() && !isSuparCompany() && $this->Session->check('Auth.User.assign_branches')) {
            $assignBranches = $this->Session->read('Auth.User.assign_branches');
            $conditions['Station.branch_id'] = array_keys($assignBranches);
        }
        if ($all == "all") {
            $this->Session->write('StationSearch', '');
        }
        //        $sesData = getMySessionData();
        //        if (isCompanyAdmin()) {
        //            $branchList = ClassRegistry::init('CompanyBranch')->getBranchList($sesData['id']);
        //            $branchList = array_keys($branchList);
        //            if (!empty($branchList)) {
        //                $conditions['branch_id'] = $branchList;
        //            }
        //        }



        if (isSuparDealer()) {
            $companies = $sesData['assign_companies'];
            $this->set(compact('companies'));
            /* $branches = ClassRegistry::init('CompanyBranch')->getBranchList($company_ids);
   
			$this->set(compact('branches'));  */

            /* $stations = ClassRegistry::init('CompanyBranch')->getStationListAll($company_ids);
			$this->set(compact('stations')); */
        } else {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($sesData['id']);

            $this->set(compact('branches'));

            /* $stations = ClassRegistry::init('CompanyBranch')->getStationListAll($sesData['id']);
			$this->set(compact('stations')); */
        }

        /* if(empty($branches)){ 
             $branches = ClassRegistry::init('CompanyBranch')->getMyBranchListsAdmin($conditions['FileProccessingDetail.company_id']);
        }*/


        if (empty($this->request->data['Station']) && $this->Session->read('StationSearch')) {
            $this->request->data['Station'] = $this->Session->read('StationSearch');
        }
        /* print_r($this->request->data);
        die();*/
        if (!empty($this->request->data['Station'])) {
            $this->request->data['Station'] = array_filter($this->request->data['Station']);
            $this->request->data['Station'] = array_map('trim', $this->request->data['Station']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Station']['station'])) {
                    $conditions['Station.name LIKE '] = '%' . $this->request->data['Station']['station'] . '%';
                }
                if (isset($this->request->data['Station']['branch_id'])) {
                    $conditions['Station.branch_id'] = $this->request->data['Station']['branch_id'];
                    $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Station']['branch_id']);
                    $this->set(compact('stations'));
                }
                if (isset($this->request->data['Station']['branch_status'])) {
                    $conditions['Station.status'] = $this->request->data['Station']['branch_status'];
                }
            }

            $this->Session->write('StationSearch', $this->request->data['Station']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => array(
                'CompanyBranch' => array(
                    'fields' => 'id, name'
                ),
                'Location' => array(
                    'fields' => 'id, name'
                )
            ),
            'order' => ' Station.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Station');
        $this->set('Stations', $this->paginate('Station'));
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
        if (!$this->Station->exists($id)) {
            $this->Message->setWarning(__('Invalid Station'), array('action' => 'index'));
        }
        $options = array('conditions' => array('Station.' . $this->Station->primaryKey => $id));
        $this->set('station', $this->Station->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {

        $sessData = getMySessionData();
         $this->loadModel('Location');
         $location_list = $this->Location->getlocationList();
         $this->set(compact('location_list'));
        if ($this->request->is('post')) {
            $this->Station->create();
            $this->request->data['Station']['status'] = 'Active';
            $this->request->data['Station']['created_by'] = $this->request->data['Station']['updated_by'] = $sessData['id'];
            if ($this->Station->save($this->request->data)) {
                $this->Message->setSuccess(__('The DynaCore Station has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The DynaCore Station could not be added. Please, check the input errors and try again.'));
            }
        }


        if (isCompany() && !isSuparDealer()) {
            $companies = $this->Station->Company->find('list');
            $this->set(compact('companies'));
            $isSuparCompany = isSuparCompany();
            $isCompanyAdmin = isCompanyAdmin();
            $current_parent_id = '';
            if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
                $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessData['id'])));
                $current_parent_id = $current_parent_id['User']['company_parent_id'];
            }
            $company_id = !empty($current_parent_id) ? $current_parent_id : $sessData['id'];
            $branches = $this->Station->CompanyBranch->getMyBranchListsAdmin($company_id);
            $this->set(compact('branches'));
        } else if (isSuparDealer()) {
            $companies = $sessData['assign_companies'];
            $this->set(compact('companies'));

            $branches = $this->Station->CompanyBranch->getMyBranchListsAdmin(array_keys($companies));
            $this->set(compact('branches'));
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
        $this->loadModel('Location');
         $location_list = $this->Location->getlocationList();
         $this->set(compact('location_list'));
        $id = decrypt($id);
        $sessData = getMySessionData();
        if (!$this->Station->exists($id)) {
            $this->Message->setWarning(__('Invalid DynaCore Station'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {

            $this->request->data['Station']['updated_by'] = $sessData['id'];
            if ($this->Station->save($this->request->data)) {
                $this->Message->setSuccess(__('The DynaCore Station has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The DynaCore Station could not be updated. Please check the input error and try again.'));
            }
        } else {
            $options = array('conditions' => array('Station.' . $this->Station->primaryKey => $id));
            $this->request->data = $this->Station->find('first', $options);
        }

        if (!isCompany() && !isSuparDealer()) {
            $companies = $this->Station->Company->find('list');
            $this->set(compact('companies'));
        } else if (isSuparDealer()) {
            $companies = $sessData['assign_companies'];
            $this->set(compact('companies'));

            $branches = $this->Station->CompanyBranch->getMyBranchListsAdmin(array_keys($companies));
            $this->set(compact('branches'));
        } else {
            $branches = $this->Station->CompanyBranch->getMyBranchListsAdmin($this->companyId);
            $this->set(compact('branches'));
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
            $this->Station->updateAll(array('status' => "'deleted'"), array('Station.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The DynaCore Station has been deleted.'), $this->referer());
        }

        $id = decrypt($id);
        $this->Station->id = $id;
        if (!$this->Station->exists()) {
            $this->Message->setWarning(__('Invalid DynaCore Station'), array('action' => 'index'));
        }
        if ($this->Station->saveField('status', 'deleted')) {
            $this->Message->setSuccess(__('The DynaCore Station has been deleted.'));
        } else {
            $this->Message->setWarning(__('The DynaCore Station could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    function change_status($stId = null, $status = null)
    {
        $stId = decrypt($stId);
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of the DynaCore Station'));
        if ($this->Station->exists($stId) && !empty($status)) {
            $this->Station->id = $stId;

            $this->Station->saveField('status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __('DynaCore Station status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
    function export()
    {
        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('station');
        if (isCompany() || isCompanyAdmin() || isSuparCompany()) {
            $conditions = array(
                'NOT' => array('Station.status' => 'deleted'),
                'Station.company_id' => $this->companyId
            );
        }
        $stationArrs = $this->Station->find("all", array('conditions' => $conditions, 'order' => array('Station.name' => 'ASC')));
        $this->loadModel('FileProccessingDetail');
        $this->set(compact('stationArrs'));
    }
    function upload_station()
    {
        if ($this->request->is('post')) {
            
            $this->loadModel('DynacoreStationFile');
            $this->DynacoreStationFile->create();
            // $this->request->data['DynacoreStationFile']['name'] = $this->request->data['uploadStation']['name'];
            $fileName = time().'_'.rand(10,100).'.zip';
            try {
                $client = S3Client::factory(
                    array(
                        'version' => 'latest',
                        'region' => AWS_S3_REGION,
                        'bucket' => S3_BUCKET,
                        'credentials' => array(
                            'key'       => awsAccessKey,
                            'secret'    => awsSecretKey,
                        ),
                    )
                );
                $client->putObject([
                    'Bucket'       => S3_BUCKET,
                    'Key'          => 'dynacore_station_files/' . $fileName,
                    'SourceFile'   => $this->request->data['DynacoreStationFile']['name']['tmp_name'],
                    'ContentType'  => 'application/zip',
                    'ACL'          => 'public-read',
                    'StorageClass' => 'STANDARD'
                ]);
            } catch (S3Exception $e) {
                // Catch an S3 specific exception.
                echo $e->getMessage();
            }
            $this->request->data['DynacoreStationFile']['name'] = $fileName;
            if ($this->DynacoreStationFile->save($this->request->data)) {
                $this->Message->setSuccess(__('The DynaCore Station file has been upload.'));
                return $this->redirect(array('action' => 'upload_station'));
            } else {
                $error = $this->DynacoreStationFile->validationErrors;
                $error = !empty($error['name'][0]) ? $error['name'][0] : '';
                if(!empty($error)){
                    $this->Message->setWarning(__($error));
                }else{
                    $this->Message->setWarning(__('The DynaCore Station could not be uploaad. Please, check the input errors and try again.'));
                }
            }
        }
    }
}
