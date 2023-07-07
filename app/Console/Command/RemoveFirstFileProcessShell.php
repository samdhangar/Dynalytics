<?php

class RemoveFirstFileProcessShell extends AppShell{


    public function main()
    {
        $start_date =  date('Y-m-d', strtotime('-1 days'));
        $end_date = date('Y-m-d', strtotime('-1 days'));
        $conditions['status'] = 'yes';
        $conditions['process'] = 0;
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Notification');
        $getFiles =  ClassRegistry::init("read_files")->find('all', ['conditions' => $conditions]);
        if(!empty($getFiles)){
            foreach ($getFiles as $key => $value) {
                $file_conditions['FileProccessingDetail.filename'] = $value['read_files']['name'];
                $get_data = $this->FileProccessingDetail->find('first', [ 'conditions' => $file_conditions,  'contain' => false]);
                $this->FileProccessingDetail->id = $get_data['FileProccessingDetail']['id'];
                $this->FileProccessingDetail->delete();
                $data = array('read_files' => array('id' => $value['read_files']['id'], 'process' => 1));
                ClassRegistry::init("read_files")->save($data);
            }
            $getNotification = $this->Notification->find('all', ['order' => 'Notification.id DESC', 'limit' => 5]);
            foreach ($getNotification as $key => $value) {
                $this->Notification->id = $value['Notification']['id'];
                $this->Notification->delete();
            }
        }
        echo "Cron update succufully";exit;

    }
}
?>