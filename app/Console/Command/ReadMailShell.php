<?php
App::uses('ComponentCollection', 'Controller');
App::uses('SiteconfigComponent', 'Controller/Component');
App::uses('ReadEmailComponent', 'Controller/Component');

class ReadMailShell extends AppShell{
    var $ReadEmail;

    public function initialize(){
        $collection = new ComponentCollection();
        $this->ReadEmail = new ReadEmailComponent($collection);
        $this->SiteConfig = new SiteconfigComponent($collection);
    }
    public function main()
    {
    	$this->loadModel('Notification');
        $last_sync =  !empty(Configure::read('Site.last_sync')) ? date("d-M-Y",strtotime(Configure::read('Site.last_sync'))) : '';
        $emails = $this->ReadEmail->fetch_by_last_sync($last_sync);
        if(!empty($emails['emails'])){
            foreach ($emails['emails'] as $key => $value) {
                $this->Notification->create();
                $data['Notification'] = $value;
                if($this->Notification->save($data)){
                    echo "success<br>";
                }else{
                    echo "Not success<br>";
                }
            }
        }else{
            echo "EMAIL NOT FOUND";
        }
        $this->loadModel('SiteConfig');
        $this->SiteConfig->updateSyncDate();
        Cache::delete('siteConfig');
        echo $emails['totalEmails']. " messages found\n";
        exit;
    }
}
?>