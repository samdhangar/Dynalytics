<?php

class InventoryUpdateShell extends AppShell{


    public function main()
    {
        $this->loadModel('FileProccessingDetail');
        $start_date =  date('Y-m-d', strtotime('-5 days'));
        $end_date =  date('Y-m-d');
        $conditions['FileProccessingDetail.file_date >= '] = $start_date;
        $conditions['FileProccessingDetail.file_date <= '] = $end_date;
        $chartDatas = $this->FileProccessingDetail->find('all', [ 'conditions' =>$conditions, 'order' => 'FileProccessingDetail.id DESC', 'contain' => array('TransactionDetail')]);
        $this->loadModel('TransactionDetail');
        if(!empty($chartDatas)){
            foreach ($chartDatas as $key => $chartData) {
                if(!empty($chartData['TransactionDetail'])){
                    foreach ($chartData['TransactionDetail'] as $key1 => $value) {
                        $total_amount[$key1] = $value['total_amount'];
                        $machine_total_amount[$key1] = !empty($value['machine_total']) ? $value['machine_total'] : '';
                        $trans_type_id[$key1] = $value['trans_type_id'];
                        $inventory_snapshot_value[$key1]  = $value['inventory_snapshot_value'];
                        if($key1 > 0){
                            $prev_key = $key1 - 1;
                            $prev_inventory_snapshot_value = $inventory_snapshot_value[$prev_key];
                            $prev_total_amount = $total_amount[$prev_key];
                            $prev_trans_type_id = $trans_type_id[$prev_key];
                            if(in_array($prev_trans_type_id, [2,4])){
                                $prev_total_amount = !empty($machine_total_amount[$prev_key]) ? $machine_total_amount[$prev_key] : $total_amount[$prev_key];
                            }
                            $current_inventory_snapshot_value = $value['inventory_snapshot_value'];
                            if(in_array($prev_trans_type_id, [17, 19])){
                                $this->TransactionDetail->id=$value['id'];                
                                $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                $this->TransactionDetail->save();
                            }else{
                                if(in_array($prev_trans_type_id, [2,4])){
                                    $prev_value = $prev_inventory_snapshot_value + $prev_total_amount;
                                    if(round($current_inventory_snapshot_value) == intval($prev_value)){
                                        $this->TransactionDetail->id=$value['id'];                
                                        $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                        $this->TransactionDetail->save();
                                    }else{
                                        $this->TransactionDetail->id=$value['id'];                
                                        $this->TransactionDetail->set(array('match_with_prev'=>'No'));                
                                        $this->TransactionDetail->save();
                                    }
                                }else if(in_array($prev_trans_type_id, [1,11,5,13,14,20])){
                                    $prev_value = $prev_inventory_snapshot_value - $prev_total_amount;
                                    if(round($current_inventory_snapshot_value) == intval($prev_value)){
                                        $this->TransactionDetail->id=$value['id'];                
                                        $this->TransactionDetail->set(array('match_with_prev'=>'Yes'));                
                                        $this->TransactionDetail->save();
                                    }else{
                                        $this->TransactionDetail->id=$value['id'];                
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
        echo "Cron update succufully";exit;
    }
}
?>