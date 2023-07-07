<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DealerMachineError
 *
 * @author securemetasys002
 */
App::uses('AppModel', 'Model');

class DealerMachineError extends AppModel
{
    public $actsAs = array(
        'Containable'
    );

    function saveData($saveData = array(), $status = '')
    {
        if (!empty($saveData) && !empty($status)) {
            $count = array();
            if ($status == 'no') {
                $this->save($saveData);
            }else{
                $count = $this->find('first', array(
                    'conditions' => array(
                        'DealerMachineError.machine_error_id' => $saveData['machine_error_id'],
                        'DealerMachineError.dealer_id' => $saveData['dealer_id'],
                    )
                ));
                if(!empty($count)){
                    $this->delete($count['DealerMachineError']['id']);
                }
            }
            return true;
        }
        return false;
    }
}
