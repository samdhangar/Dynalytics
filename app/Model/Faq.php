<?php
App::uses('AppModel', 'Model');

class Faq extends AppModel
{
    var $name = 'Faq';
    public $validate = array(
        'question' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a title of faq.'
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('question', 'user_role'), false),
                'message' => 'Email Address already Exist.'
            )
        ),
        'answer' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter a answer of faq.'
            ),
        ),
        'order_no' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter a display order of faq.'
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('order_no', 'user_role'), false),
                'message' => 'This display order already exists.'
            )
        )
    );

}
