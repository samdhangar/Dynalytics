<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{

    public function getGender()
    {
        return array(
            'Male' => __('Male'),
            'Female' => __('Female'),
        );
    }

    public function getTypes($userType = null,$extra = null)
    {
        $types = array(
            'Admin' => __('Admin'),
            'Support' => __('Support'),
        );
        if($userType == COMPANY){
            $types = array(
                'Admin' => __('Company Admin'),
                'Branch' => __('Branch User'),
                'Region' => __('Region User'),
//                'Branch' => __('Branch Manager'),
//                'Regional' => __('Regional Admin'),
            );
            if($extra){
                unset($types['Branch']);
                $types[REGIONAL] = REGIONAL. ' ' . ADMIN;
            }
        }
        return $types;
    }

    public function getStatuses()
    {
        return array(
            'Active' => __('Active'),
            'Inactive' => __('Inactive')
        );
    }

    public function getUserRole()
    {
        return array('Admin' => 'Admin', 'Company' => 'Company', 'Delear' => 'Dealer');
    }
    
}
