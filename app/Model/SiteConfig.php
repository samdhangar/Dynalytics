<?php

App::uses('AppModel', 'Model');

class SiteConfig extends AppModel {

    var $name = 'SiteConfig';
    var $primaryKey = 'key';
    var $displayField = 'value';
    var $validate = array(
        'key' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter Key',
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'This Key is already exist',
            ),
        )
    );

    function updateConfigs($arrConfigs) {
        foreach ($arrConfigs as $key => $val) {
            $this->updateAll(array('value' => "'" . $val . "'"), array('key' => $key));
        }
    }


    function updateSyncDate() {
        $date = date("Y-m-d");;
        $this->updateAll(array('value' => "'" . $date . "'"), array('key' => 'Site.last_sync'));
        return true;
    }

}

?>
