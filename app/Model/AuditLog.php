<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @property AuditLog $AuditLog
 */
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class AuditLog extends AppModel
{

    public $useTable = 'audit_logs';

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }

    public $actsAs = array('Containable');

    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function insertLog($data = array())
    {
        if (!empty($data)) {
            $this->save($data);
        }
    }

    public function addUserLog($status = 'login')
    {
        $sessionData = getMySessionData('User');
        $details = array('status' => $status, 'username' => $sessionData['email']);
        $auditData = array(
            'user_id' => $sessionData['id'],
            'detail' => json_encode($details),
            'type' => $status,
        );
        $this->insertLog($auditData);
    }

    public function addSecurityAssesmentLog($status = 'Security Assessment Edited', $data = null)
    {
        $sessionData = getMySessionData('User');
        $auditData = array(
            'user_id' => $sessionData['id'],
            'detail' => $data,
            'type' => $status,
        );
        $this->insertLog($auditData);
    }

    public function addTargetThreshold($status = 'Target Thresholds Edited', $data = null)
    {
        $sessionData = getMySessionData('User');
        $auditData = array(
            'user_id' => $sessionData['id'],
            'detail' => $data,
            'type' => $status,
        );
        $this->insertLog($auditData);
    }



    public function getAuditLogStatusList()
    {
        return $this->find('list', array(
            'contain' => false,
            'fields' => 'type, type'
        ));
    }


}
