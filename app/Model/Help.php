<?php
App::uses('AppModel', 'Model');

class Help extends AppModel
{
    var $name = 'Help';
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a title of help page.'
            )
        ),
        'description' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter a description of help page.'
            ),
        ),
        'report_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a report name.'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Report name already Exist.'
            )
        ),
    );

}
