<?php
App::uses('AppController', 'Controller');

class HelpsController extends AppController
{
    public $components = array('Auth');
    public $helpers = array('Ck');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
    }

    function add()
    {
        $sessionData = getMySessionData();
        if ($this->request->is('post') || $this->request->is('put')) {
//            $this->request->data['Faq']['created_by'] = $this->request->data['Faq']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['Help']['created_by'] = $this->request->data['Help']['updated_by'] = $sessionData['id'];
            if ($this->Help->save($this->request->data)) {
                $this->Message->setSuccess(__('Help page added successfully.'), array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to add Help page.'));
        }
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('title', 'Add New Help page');
    }

    function edit($id = null)
    {
        $sessionData = getMySessionData();
        if (!empty($id)) {
            $id = decrypt($id);
        }
        if ($this->request->is('post') || $this->request->is('put')) {
//            $this->request->data['Faq']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['Help']['updated_by'] = $sessionData['id'];
            if ($this->Help->save($this->request->data)) {
                $this->Message->setSuccess(__('Help page has been updated successfully.'), array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to update faq.'));
            if (empty($this->request->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        if (empty($this->request->data) && !empty($id)) {
            $this->request->data = $this->Help->read(null, $id);
        }
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('title', 'Edit helppage');
        $this->render('add');
    }

    public function view($help_pageId = null)
    {
            if (!empty($help_pageId)) {
                $help_pageId = decrypt($help_pageId);
                if ($this->Help->exists($help_pageId)) {
                    $help = $this->Help->find('first', array('contain' => false, 'conditions' => array('Help.id' => $help_pageId)));
                    $this->set(compact('help'));
                }else{
                    $this->Message->setWarning(__('Invalid Request'),array('controller'=>'helps','action'=>'index'));
                }
            }else{
                $this->Message->setWarning(__('Invalid Request'),array('controller'=>'helps','action'=>'index'));
            }
    }

    public function index($all = null)
    {
        $conditions = array('NOT' => array('Help.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('Help_pageSearch', '');
        }
        if (empty($this->request->data['Help']) && $this->Session->read('Help_pageSearch')) {
            $this->request->data['Help'] = $this->Session->read('Help_pageSearch');
        }
        if (!empty($this->request->data['Help'])) {
            $this->request->data['Help'] = array_filter($this->request->data['Help']);
            $this->request->data['Help'] = array_map('trim', $this->request->data['Help']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Help']['title'])) {
                    $conditions['title.title LIKE '] = '%' . $this->request->data['Help']['title'] . '%';
                }
                if (isset($this->request->data['Help']['status'])) {
                    $conditions['Help.status'] = $this->request->data['Help']['status'];
                }
            }
            $this->Session->write('Help_pageSearch', $this->request->data['Help']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'conditions' => $conditions,
            'order' => ' Help.id DESC'
        ));
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('helps', $this->paginate('Help'));
    }

    public function delete($faqId = null)
    {
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->Help->updateAll(array('status' => "'deleted'"), array('Faq.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The faq has been deleted.'), $this->referer());
        }

        if (!empty($faqId)) {
            $faqId = decrypt($faqId);
        }
        if (empty($faqId) || !$this->Help->exists($faqId)) {
            $this->Message->setWarning(__('Invalid Faq'), array('action' => 'index'));
        }
        $this->Help->id = $faqId;
        if ($this->Help->saveField('status', 'Deleted')) {
            $this->Message->setSuccess(__('Help page has been deleted.'), Router::url(array('action' => 'index'), true));
        } else {
            $this->Message->setWarning(__('Unable to delete Help page'), Router::url(array('action' => 'index'), true));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function lists()
    {
        $sessionData = getMySessionData();
        $this->layout = 'default';
        $conditions = array('Help.status' => 'active');
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'conditions' => $conditions,
        ));
        $this->set('helps', $this->paginate('Help'));
    }

    function change_status($id = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of ' . $this->type));
        if ($this->Help->exists($id) && !empty($status)) {
            $this->Help->id = $id;
            $this->Help->saveField('status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __($this->type . ' status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
}
