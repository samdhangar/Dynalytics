<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('SiteconfigComponent', 'Controller/Component');
App::uses('SendEmailComponent', 'Controller/Component');

class SerialNoMissingShell extends AppShell{

    var $SendEmail;

    public function initialize(){
        $collection = new ComponentCollection();  
        $controller = new Controller();        
        $this->SendEmail = new SendEmailComponent($collection);
        $this->Siteconfig = new SiteconfigComponent($collection);
        
    }
    public function main()
    {
      
        $start_date =  date('Y-m-d');
        $end_date =  date('Y-m-d');
        
        $conditions['created_date >= '] = $start_date.' 00:00:00'; //'2023-04-21 00:00:00';
        $conditions['created_date <= '] = $end_date.' 23:59:59';//'2023-04-21 23:59:59';
        $conditions['status'] = 'serial_not_exits';
        $conditions['mail_notification'] = 0;
        $file_name = [];
        $getFiles =  ClassRegistry::init("read_files")->find('all', ['conditions' => $conditions]);
        if(!empty($getFiles)){
            $this->SendEmail->sendSerialNoNotExist($getFiles);
            foreach ($getFiles as $key => $value) {
                $data = array('read_files' => array('id' => $value['read_files']['id'], 'mail_notification' => 1));
                // $data = array('read_files' => array('id' => $value['read_files']['id'], 'process' => 2));
                ClassRegistry::init("read_files")->save($data);
            }
        }
        echo "Cron update succufully";exit;
    }
}
?>