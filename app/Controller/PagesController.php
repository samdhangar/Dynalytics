<?php
App::uses('AppController', 'Controller');

class PagesController extends AppController {
    public $components = array('Auth');
    public $helpers = array('Ck');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->_checkLogin();
        $this->Auth->allow ('home','view','maintenance','contact_us');
    }
    
    function maintenance() {
        $this->layout = 'ajax';
    }
    
    public function add(){
        if($this->request->is('post') || $this->request->is('put')){
            //unset the page id if there is already setted.
            if(isset($this->request->data['Page']['id'])){
                unset($this->request->data['Page']['id']);
            }
            if($this->Page->save($this->request->data)){
                $this->Message->setSuccess(__('Page added successfully'),array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to add page'));
            if(empty($this->request->data)){
                $this->redirect(array('action'=>'index'));
            }
        }
        $this->set('title',__('Add'));
        $this->render('form');
    }
    
    public function edit($pageId=null){
        if($this->request->is('post') || $this->request->is('put')){
            if(!isset($this->request->data['Page']['id'])){
                $this->request->data['Page']['id']=$pageId;
            }
            if($this->Page->save($this->request->data)){
                $this->Message->setSuccess(__("Page updated successfully."),array('action' => 'index'));
            }
            $this->Message->setWarning(__("Unable to update page."),array('action' => 'index'));
        }
        if(!empty($pageId)){
            $this->request->data=$this->Page->findById($pageId);
        }
        
        $this->set('title',__('Edit'));
        $this->render('form');
    }
    
    public function delete($pageId=null){
        if(empty($pageId)){
            $this->Message->setWarning(__('Invalid page'),array('action' => 'index'));
        }
        if($this->Page->delete($pageId)){
            $this->Message->setSuccess(__('Page deleted successfully'),Router::url(array('action'=>'index'),true));
        }
        $this->Message->setWarning(__('Unable to delete page'),Router::url(array('action'=>'index'),true));
    }
    
    public function index($all =null){
        $conditions = array();
        $conditions = array('NOT'=>array('Page.status'=>'deleted'));
        if($all == "all"){
            $this->Session->write('PageSearch','');
        }
        if (empty($this->request->data['Page']) && $this->Session->read('PageSearch')) {
            $this->request->data['Page'] = $this->Session->read('PageSearch');
        }
        if(!empty($this->request->data['Page'])){            
            $this->request->data['Page']=array_filter($this->request->data['Page']);
            $this->request->data['Page']=array_map('trim', $this->request->data['Page']);
            if(!empty($this->request->data)){
                if(isset($this->request->data['Page']['name'])){                    
                    $conditions['Page.name LIKE '] = '%'. $this->request->data['Page']['name'] .'%';
                }
                if(isset($this->request->data['Page']['url'])){                    
                    $conditions['Page.url LIKE '] = '%'. $this->request->data['Page']['url'] .'%';
                }
                if(isset($this->request->data['Page']['title'])){
                    $conditions['Page.title LIKE '] = '%'.$this->request->data['Page']['title'].'%';
                }
                if(isset($this->request->data['Page']['status'])){
                    $conditions['Page.status'] = $this->request->data['Page']['status'];
                }
            }
            $this->Session->write('PageSearch', $this->request->data['Page']);
        }
        $this->AutoPaginate->setPaginate(array(
            'conditions'=>$conditions,
            'order'=>' Page.id DESC'
        ));
        $this->set('pages',$this->paginate('Page'));
    }
    
    public function home() {
        $this->set('metadata',array('keywords'=>  Configure::read('Site.Name'),'description'=>Configure::read('Site.Name')));
        $this->set('title_for_layout',Configure::read('Site.Name'));
		$this->set('homepage',1);
		//Get Latest Job
		$latestJob = ClassRegistry::init("Job")->find('all',array(
															'fields'=>array('id','title','post_time'),
															'order'=>'post_time DESC',
															'limit'=>10,
															'recursive'=>-1
														));
		$this->set('latestJob',$latestJob);
    }
    public function contact_us() {
        if(!empty($this->request->data)){
            $this->SendEmail->sendContactUsEmail($this->request->data['ContactUs']);
            $this->Message->setSuccess(__('Thank You for contact us. We will give quick response to you.'),'/');
        }
	}
    public function view($pageName = null) {
        if(!empty($pageName)){
            $pageDetail = $this->Page->findByUrl($pageName);
            if(empty($pageDetail)){
                $pageDetail['Page'] = array(
                    'title' => __('Error 404'),
                    'body' => '<h2>' . __('Page Not Found') . '</h2>'
                );
            }
            $this->set(compact('pageDetail'));
            $this->set('title_for_layout',$pageDetail['Page']['title']);
            $this->set('metadata',array('keywords'=>$pageDetail['Page']['meta_keyword'],'description'=>$pageDetail['Page']['meta_description']));
        }
    }
    
    public function unknown()
    {
        
    }
}