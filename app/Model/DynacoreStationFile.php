<?php
App::uses('AppModel', 'Model');

class DynacoreStationFile extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    public $useTable = 'dynacore_station_files';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $validate = array(
        'name' => array(
            'rule' => array(
                  'extension',
                   array('zip')
              ),
              'message' => 'Please upload a valid file.'
          )
    );
}
