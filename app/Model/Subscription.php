<?php
App::uses('AppModel', 'Model');

class Subscription extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    public $actsAs = array(
        'Containable'
    );
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter subscription name',
                'last' => true,
            ),
            'isUnique' => array(
                'rule' => array('isUnique', array('name', 'type'), false),
                'message' => 'Subscription Name already Exist'
            )
        )
    );

    function getSubscriptionList($type = '')
    {
        $conditions = array('Subscription.status' => 'active');
        if (!empty($type)) {
            $conditions['Subscription.type'] = $type;
        }
        return $this->find('list', array('contain' => false, 'fields' => 'id, name', 'conditions' => $conditions));
    }

    function getAllSubscriptions()
    {
        $conditions = array('Subscription.status' => 'active');
        $fields = array('id', 'name', 'setup_cost', 'charge', 'type');
        $subscriptionList = $this->find('all', array('fields' => $fields, 'conditions' => $conditions));
        if (!empty($subscriptionList)) {
            $subscriptionList = Hash::extract($subscriptionList, '{n}.Subscription');
            $subscriptionList = Hash::combine($subscriptionList, '{n}.id', '{n}');
        }
        return $subscriptionList;
    }

    function getSubscriptionDetail($subscriptionId = null)
    {
        $subscriptionDetail = array();
        if ($this->exists($subscriptionId)) {
            $subscriptionDetail = $this->find('first', array(
                'contain' => false,
                'conditions' => array(
                    'Subscription.id' => $subscriptionId
                )
            ));
            $subscriptionDetail = Hash::extract($subscriptionDetail , 'Subscription');
        }
        return $subscriptionDetail;
    }
}
