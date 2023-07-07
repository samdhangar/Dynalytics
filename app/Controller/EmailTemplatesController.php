<?php
App::uses('AppController', 'Controller');

class EmailTemplatesController extends AppController
{
    var $name = 'EmailTemplates';
    var $components = array('Auth');

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
        if (!isSuparAdmin()) {
            $this->Message->setWarning(__('Sorry you are not allowed to access'));
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }
    }

    function index()
    {
        $arrFields = array('EmailTemplate.id', 'EmailTemplate.subject', 'EmailTemplate.name', 'EmailTemplate.updated');
        $this->AutoPaginate->setPaginate(array('fields' => $arrFields));
        $this->set('EmailTemplates', $this->paginate('EmailTemplate'));
    }

    function view($id = null)
    {
        $id = decrypt($id);
        if (empty($id)) {
            $this->Message->setWarning(__('Invalid User'), array('action' => 'index'));
        }
        $this->set('isFromView', true);
        $view = new View($this, false);
        $view->viewPath = 'Layouts/Emails/html';
        $view->layout = false;
        $html = $view->render('default');
        $emailTemplate = $this->EmailTemplate->read(null, $id);
        $replacement = array(
            '{SITE_NAME}' => Configure::read('Site.Name'),
            '{SITE_URL}' => Configure::read('Site.Url'),
            '{SITE_SUPPORT_EMAIL}' => Configure::read('Site.SupportEmail'),
            '{SITE_SUPPORT_PHONE}' => Configure::read('Site.SupportPhone'),
            '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            '{ACTIVATION_LINK}' => Router::url(array('controller' => 'users', 'action' => 'activate'), true),
            '{SITE_LOGIN_URL}' => Router::url(array('controller' => 'users', 'action' => 'login'), true)
        );
        $emailTemplate['EmailTemplate']['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplate['EmailTemplate']['subject']);
        $body = str_replace('{BODY}', $emailTemplate['EmailTemplate']['body'], $html);
        $body = str_replace(array_keys($replacement), array_values($replacement), $body);
        $subject = str_replace(array_keys($replacement), array_values($replacement), $emailTemplate['EmailTemplate']['subject']);
        $this->set(compact('body', 'subject', 'emailTemplate'));
    }

    function add($id = null)
    {
        if (!empty($id)) {
            $id = decrypt($id);
        }
        $this->Message->setWarning('Invalid Template', $this->referer());
        $arrResponse = array("status" => "fail", "message" => "Please try again");
        if (!empty($this->request->data)) {
            $this->EmailTemplate->create();
            if ($this->EmailTemplate->saveAll($this->request->data)) {
                if (isset($this->request->params['pass'][0])) {
                    $arrResponse = array("status" => "success", "message" => __('%s Has been saved', 'Email Templates'), "drip_mail_id" => $this->request->params['pass'][0], 'email_template_id' => $this->EmailTemplate->id);
                } else {
                    $arrResponse = array("status" => "success", "message" => __('%s Has been saved', 'Email Templates'));
                    $arrResponse['email_template_id'] = $this->EmailTemplate->id;
                }
                $this->Message->setSuccess(Configure::read('NM.EmailTemplate.AddSuccess'), array('action' => 'index'));
            }
        }
        $this->render('admin_add');
    }

    function edit($id = null)
    {
        if (!empty($id)) {
            $id = decrypt($id);
        }
        if (!$id && empty($this->request->data)) {
            $this->Message->setWarning(Configure::read('NM.EmailTemplate.Invalid'), array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $this->request->data["EmailTemplate"]["id"] = $id;
            if ($this->EmailTemplate->saveAll($this->request->data)) {
                $this->Message->setSuccess(__('Email Templete Saved SuccessFully'), array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('Email Templete does not update'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->EmailTemplate->find('first', array('conditions' => array('EmailTemplate.id' => $id), 'recursive' => -1));
            if (empty($this->request->data)) {
                $this->Message->setWarning(Configure::read('NM.EmailTemplate.Invalid'), array('action' => 'index'));
            }
        }
        $this->set('edit', 1);
        $this->render('add');
    }

    function get_template()
    {
        $arrResponse = array('status' => 'fail', 'message' => 'Email Template not Found');
        $emailTemplete = $this->EmailTemplate->find('first', array('fields' => array('mail_subject', 'body'), 'conditions' => array('name' => $this->request->data['templete_type']), 'recursive' => -1));
        if (!empty($emailTemplete)) {
            $arrResponse = array('status' => 'success', 'message' => 'Email Template', 'subject' => $emailTemplete['EmailTemplate']['subject'], 'body' => $emailTemplete['EmailTemplate']['body']);
        }
        echo json_encode($arrResponse);
        exit;
    }
}

?>
