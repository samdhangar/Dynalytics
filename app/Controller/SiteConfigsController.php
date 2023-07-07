<?php
App::uses('AppController', 'Controller');

class SiteConfigsController extends AppController
{
    var $name = 'SiteConfigs';
    public $components = array('Auth');

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
    }

    public function index()
    {
        
        if (!empty($this->request->data) && ($this->request->is('put') || $this->request->is('post'))) {
            $this->SiteConfig->updateConfigs($this->request->data);
            Cache::delete('siteConfig');
            $this->Message->setSuccess('General setting has been updated successfully.');
        }
        $arrKeys = array(
            'Site.AdminEmail','Site.filterOption', 'Site.Name', 'Site.Url', 
            'Site.ContactEmail', 'Site.SupportEmail', 'Site.SupportPhone', 
            'Site.FromEmail', 'Site.FromName', 'Site.PublishEmail1', 'Site.PublishEmail2',
            'Site.Address1', 'Site.Address2', 'Site.State', 'Site.Country','Site.Theme',
            'Site.TextparsingUrl'
            );
        $arrConfigs = $this->SiteConfig->find('all', array('conditions' => array('key' => $arrKeys)));
        
        $this->set('arrConfigs', Hash::combine($arrConfigs, '{n}.SiteConfig.key', '{n}.SiteConfig'));
    }
}
