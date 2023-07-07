<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class AdminUser extends AppModel
{

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'admin_users';
    public $belongsTo = array(
        'Admin' => array(
            'className' => 'User',
            'foreignKey' => 'admin_id'
        ),
        'Dealer' => array(
            'className' => 'User',
            'foreignKey' => 'dealer_id'
        ),
        'Company' => array(
            'className' => 'User',
            'foreignKey' => 'company_id'
        )
    );

    function getAssignedDealerList()
    {
        $sesData = getMySessionData();
        return $this->find('list', array(
                'fields' => 'dealer_id,dealer_id',
                'conditions' => array(
                    'AdminUser.admin_id' => $sesData['id']
                )
        ));
    }

    function getAssignedCompanyList()
    {
        $sesData = getMySessionData();
        return $this->find('list', array(
                'fields' => 'company_id,company_id',
                'conditions' => array(
                    'AdminUser.admin_id' => $sesData['id']
                )
        ));
    }

    function saveSupportAdminUsers($inData = array())
    {
        if (!empty($inData) && !empty($inData['admin_id']) && !empty($inData['multicompany_id'])) {
            $adminId = $inData['admin_id'];
            $multiDealerIds = $inData['multidealer_id'];
            $multiCompanyIds = $inData['multicompany_id'];
            $saveData = array();
            /**
             * save dealer with company
             */
            debug($multiDealerIds);
            exit;
            foreach ($multiDealerIds as $dealerId) {
                $companyList = ClassRegistry::init('User')->getSuparCompanyListFromDeal($dealerId, 'User.id, User.id');
                $count = 0;
                foreach ($multiCompanyIds as $companyId) {
                    if (isset($companyList[$companyId])) {

                        $count++;
                        $saveData[] = array(
                            'admin_id' => $adminId,
                            'dealer_id' => $dealerId,
                            'company_id' => $companyId
                        );
                    }
                }
                if (empty($count)) {
                    $saveData[] = array(
                        'admin_id' => $adminId,
                        'dealer_id' => $dealerId,
                        'company_id' => 0
                    );
                }
            }
            /*
             * Remove Old Data
             */
            $this->deleteAll(array('AdminUser.admin_id' => $adminId));
            if (!empty($saveData)) {
                $this->saveMany($saveData);
            }
        }
    }
}
                