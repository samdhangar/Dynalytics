<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('SiteconfigComponent', 'Controller/Component');
App::uses('SendEmailComponent', 'Controller/Component');

class MissingFileShell extends AppShell{

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
        $conditions['created_date >= '] = $start_date.' 00:00:00';
        $conditions['created_date <= '] = $end_date.'  23:59:59';
        $stationCondition['Station.company_id'] =127;
        $this->loadModel('Station');
        $getAllStations = $this->Station->find('all', array('fields' => 'name,id,serial_no','conditions' => $stationCondition, 'contain' =>false));
        $all_stations = [];
        $get_file_name = [];
        foreach ($getAllStations as $key => $value) {
            $all_stations[] = $value['Station']['serial_no'];
        }
        $getFiles =  ClassRegistry::init("read_files")->find('all', ['conditions' => $conditions]);
        if(!empty($getFiles)){
            foreach ($getFiles as $key => $value) {
                if(!empty($value['read_files']['name'])){
                    $check_file_name = explode("[",$value['read_files']['name']);
                    if(!empty($check_file_name[1])){
                        $check_file_name  = explode("]",$check_file_name[1]);
                    }
                    $get_file_name[] = !empty($check_file_name[0]) ? $check_file_name[0] : '';
                }
            }
        }
        $check_station_match = array_diff($all_stations , $get_file_name);
        $stationCondition['Station.company_id'] =127;
        $stationCondition['Station.serial_no IN'] =$check_station_match;
        $getAllStations = $this->Station->find('all', array('fields' => 'name,id,serial_no','conditions' => $stationCondition, 'contain' =>false));
        if(!empty($getAllStations)){
            $this->SendEmail->sendMissingFile($getAllStations);
        }
        echo "Cron update succufully";exit;
    }
}
?>