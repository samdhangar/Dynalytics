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
App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class FileProccessingDetail extends AppModel
{
    public $displayField = 'name';
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'file_processing_detail';
    public $belongsTo = array(
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id'
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'company_id'
        ),
        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id'
        ),
        'Station' => array(
            'className' => 'Station',
            'foreignKey' => 'station',
            // 'counterCache' => array(
            //     'file_processed_count'
            // )
        )
    );
  

   /*
   

        'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => false,
            'conditions' => array(
                'Branch.id' => 'FileProccessingDetail.branch_id'
            )
        ),


    'Branch' => array(
            'className' => 'CompanyBranch',
            'foreignKey' => 'branch_id'
        ),*/
    public $hasMany = array(
        'ErrorDetail' => array(
            'className' => 'ErrorDetail',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'TransactionDetail' => array(
            'className' => 'TransactionDetail',
            'foreignKey' => 'file_processing_detail_id'
		),
        'AutomixSetting' => array(
            'className' => 'AutomixSetting',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'BillsActivityReport' => array(
            'className' => 'BillsActivityReport',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'BillAdjustment' => array(
            'className' => 'BillAdjustment',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'BillCount' => array(
            'className' => 'BillCount',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'BillHistory' => array(
            'className' => 'BillHistory',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'CoinInventory' => array(
            'className' => 'CoinInventory',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'CurrentTellerTransactions' => array(
            'className' => 'CurrentTellerTransactions',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'HistoryReport' => array(
            'className' => 'HistoryReport',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'ManagerSetup' => array(
            'className' => 'ManagerSetup',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'NetCashUsageActivityReport' => array(
            'className' => 'NetCashUsageActivityReport',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'SideActivityReport' => array(
            'className' => 'SideActivityReport',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'TellerActivityReport' => array(
            'className' => 'TellerActivityReport',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'ValutBuy' => array(
            'className' => 'ValutBuy',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'TellerSetup' => array(
            'className' => 'TellerSetup',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'Inventory' => array(
            'className' => 'Inventory',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'InventoryByHour' => array(
            'className' => 'InventoryByHour',
            'foreignKey' => 'file_processing_detail_id'
        ),
        'UserReport' => array(
            'className' => 'UserReport',
            'foreignKey' => 'file_processing_detail_id'
        )
    );

    function getCountProcessedFiles($conditions = array())
    {
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = getCompanyId();
        }
        if (isDealer()) {
            $sessData = getMySessionData();
            $conditions['FileProccessingDetail.company_id'] = ClassRegistry::init('User')->getMyCompanyList($sessData['id'], $sessData['role'],'User.id, User.id');
        }
		return $this->find('count', array('contain' => false, 'conditions' => $conditions));
    }

    function getFileDateList($stationList = array())
    {
        return $this->find('list', array('fields' => 'id, file_date', 'contain' => false, 'conditions' => array('FileProccessingDetail.station' => $stationList)));
    }
    
    function getStationFileInLastMonth($conditions = array())
    {
        $conditions['DATE_FORMAT(file_date,"%Y-%m")'] = date('Y-m',  strtotime('-1 month'));
        $totalFiles = $this->find('count',array(
            'conditions' => $conditions
        ));
        return $totalFiles;
    }
}
