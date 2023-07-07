<?php

class FileInventoryUpdateShell extends AppShell{


    public function main()
    {
        $start_date =  date('Y-m-d', strtotime('-15 days'));
        $end_date =  date('Y-m-d');

        $conditions['FileProccessingDetail.file_date >= '] = $start_date;
        $conditions['FileProccessingDetail.file_date <= '] = $end_date;
        $conditions['FileProccessingDetail.company_id'] =127;
        $stationCondition['Station.company_id'] = 127;

        $this->loadModel('Station');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('TransactionDetail');
        $getAllStations = $this->Station->find('all', array('conditions' => $stationCondition, 'contain' =>false));
        if(!empty($getAllStations)){
            foreach ($getAllStations as $key => $station) {
                $conditions['FileProccessingDetail.station'] = $station['Station']['id'];
                $conditions['FileProccessingDetail.branch_id'] = $station['Station']['branch_id'];
                $allFiles = $this->FileProccessingDetail->find('all', [ 'conditions' => $conditions,  'contain' => ['TransactionDetail']]);
                if(!empty($allFiles)){
                    foreach ($allFiles as $key1 => $allFile) {
                        if($key1 > 0){
                            $prev_key = $key1 - 1;
                            if(count($allFiles[$prev_key]['TransactionDetail']) == 0){
                                $prev_key = $prev_key - 1;
                            }
                            $last_transaction_key = count($allFiles[$prev_key]['TransactionDetail']) - 1;
                            $prev_inventory_snapshot_value = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['inventory_snapshot_value'];
                            $prev_total_amount = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['total_amount'];
                            $prev_trans_type_id = $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['trans_type_id'];
                            $current_inventory_snapshot_value = !empty($allFile['TransactionDetail']) ? $allFile['TransactionDetail'][0]['inventory_snapshot_value'] : '';
                            if(in_array($prev_trans_type_id, [2,4])){
                                $prev_total_amount = !empty($allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['machine_total']) ? $allFiles[$prev_key]['TransactionDetail'][$last_transaction_key]['machine_total'] : $prev_total_amount;
                            }
                            if(in_array($prev_trans_type_id, [17, 19])){
                                if(!empty($allFile['TransactionDetail'])){
                                    $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];            
                                    $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                    $this->TransactionDetail->save();
                                }
                            }else{
                                if(in_array($prev_trans_type_id, [2,4])){
                                    $prev_value = $prev_inventory_snapshot_value + $prev_total_amount;
                                    if(round($current_inventory_snapshot_value) == intval($prev_value)){
                                        if(!empty($allFile['TransactionDetail'])){
                                            $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];            
                                            $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                            $this->TransactionDetail->save();
                                        }
                                    }else{
                                        if(!empty($allFile['TransactionDetail'])){
                                            $this->TransactionDetail->id=$allFile['TransactionDetail'][0]['id'];           
                                            $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                                            $this->TransactionDetail->save();
                                        }
                                    }
                                }else if(in_array($prev_trans_type_id, [1,11,5,13,14,20])){
                                    $prev_value = $prev_inventory_snapshot_value - $prev_total_amount;
                                    if(round($current_inventory_snapshot_value) == intval($prev_value)){
                                        if(!empty($allFile['TransactionDetail'])){
                                            $this->TransactionDetail->id= $allFile['TransactionDetail'][0]['id'];           
                                            $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                            $this->TransactionDetail->save();
                                        }
                                    }else{
                                        if(!empty($allFile['TransactionDetail'])){
                                            $this->TransactionDetail->id = $allFile['TransactionDetail'][0]['id'];     
                                            $this->TransactionDetail->set(array('match_with_prev'=>'No'));     
                                            $this->TransactionDetail->save();
                                        }
                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
            echo "Update scron succufully";exit;
        }        
    }
}
?>