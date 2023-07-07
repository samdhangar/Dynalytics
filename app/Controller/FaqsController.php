<?php
App::uses('AppController', 'Controller');

class FaqsController extends AppController
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
            $this->request->data['Faq']['created_by'] = $this->request->data['Faq']['updated_by'] = $sessionData['id'];
            if ($this->Faq->save($this->request->data)) {
                $this->Message->setSuccess(__('Faq added successfully.'), array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to add faq.'));
        }
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('title', 'Add New Faq');
    }

    function edit($id = null)
    {
        $sessionData = getMySessionData();
        if (!empty($id)) {
            $id = decrypt($id);
        }
        if ($this->request->is('post') || $this->request->is('put')) {
//            $this->request->data['Faq']['updated_by'] = $this->Session->read('Auth.User.id');
            $this->request->data['Faq']['updated_by'] = $sessionData['id'];
            if ($this->Faq->save($this->request->data)) {
                $this->Message->setSuccess(__('Faq has been updated successfully.'), array('action' => 'index'));
            }
            $this->Message->setWarning(__('Unable to update faq.'));
            if (empty($this->request->data)) {
                $this->redirect(array('action' => 'index'));
            }
        }
        if (empty($this->request->data) && !empty($id)) {
            $this->request->data = $this->Faq->read(null, $id);
        }
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('title', 'Edit Faq');
        $this->render('add');
    }

    public function view($faqId = null)
    {
        if($this->request->is('ajax')){
            if (!empty($faqId)) {
                $faqId = decrypt($faqId);
            }
            $this->layout = false;
            if ($this->Faq->exists($faqId)) {
                $faq = $this->Faq->find('first', array('contain' => false, 'conditions' => array('Faq.id' => $faqId)));
                $this->set(compact('faq'));
            } else {
                echo "Invalid faq";
                exit;
            }
        }
        else{
            $this->Message->setWarning(__('Invalid Request'),array('controller'=>'faqs','action'=>'index'));
        }
    }

    public function index($all = null)
    {
        $conditions = array('NOT' => array('Faq.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('FaqSearch', '');
        }
        if (empty($this->request->data['Faq']) && $this->Session->read('FaqSearch')) {
            $this->request->data['Faq'] = $this->Session->read('FaqSearch');
        }
        if (!empty($this->request->data['Faq'])) {
            $this->request->data['Faq'] = array_filter($this->request->data['Faq']);
            $this->request->data['Faq'] = array_map('trim', $this->request->data['Faq']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Faq']['user_role'])) {
                    $conditions['Faq.user_role'] = $this->request->data['Faq']['user_role'];
                }
                if (isset($this->request->data['Faq']['question'])) {
                    $conditions['Faq.question LIKE '] = '%' . $this->request->data['Faq']['question'] . '%';
                }
                if (isset($this->request->data['Faq']['question'])) {
                    $conditions['Faq.question LIKE '] = '%' . $this->request->data['Faq']['question'] . '%';
                }
                if (isset($this->request->data['Faq']['answer'])) {
                    $conditions['Faq.answer LIKE '] = '%' . $this->request->data['Faq']['answer'] . '%';
                }
                if (isset($this->request->data['Faq']['status'])) {
                    $conditions['Faq.status'] = $this->request->data['Faq']['status'];
                }
            }
            $this->Session->write('FaqSearch', $this->request->data['Faq']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'conditions' => $conditions,
            'order' => ' Faq.id DESC'
        ));
        $userRoles = getAllUserRoleTypes();
        $this->set(compact('userRoles'));
        $this->set('faqs', $this->paginate('Faq'));
    }

    public function delete($faqId = null)
    {
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->Faq->updateAll(array('status' => "'deleted'"), array('Faq.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The faq has been deleted.'), $this->referer());
        }

        if (!empty($faqId)) {
            $faqId = decrypt($faqId);
        }
        if (empty($faqId) || !$this->Faq->exists($faqId)) {
            $this->Message->setWarning(__('Invalid Faq'), array('action' => 'index'));
        }
        $this->Faq->id = $faqId;
        if ($this->Faq->saveField('status', 'Deleted')) {
            $this->Message->setSuccess(__('Faq has been deleted.'), Router::url(array('action' => 'index'), true));
        } else {
            $this->Message->setWarning(__('Unable to delete faq'), Router::url(array('action' => 'index'), true));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function lists()
    {
        $sessionData = getMySessionData();
        $this->layout = 'default';
        $conditions = array('Faq.status' => 'active');
        if (!isSuparAdmin()) {
//            $conditions['Faq.user_role'] = $this->Session->read('Auth.User.role') . '_' . $this->Session->read('Auth.User.user_type');
            $conditions['Faq.user_role'] = $sessionData['role'] . '_' . $sessionData['user_type'];
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'conditions' => $conditions,
//            'order' => ' Faq.id DESC ' . ',Faq.order_no ASC'
            'order' => ' Faq.order_no ASC ' . ',Faq.id DESC'
        ));
        $this->set('faqs', $this->paginate('Faq'));
    }

    function change_status($id = null, $status = null)
    {
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of ' . $this->type));
        if ($this->Faq->exists($id) && !empty($status)) {
            $this->Faq->id = $id;
            $this->Faq->saveField('status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __($this->type . ' status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
}
