<?php
App::uses('AppModel', 'Model');

class Invoice extends AppModel
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

    function billCron()
    {
        $this->Subscription = ClassRegistry::init('Subscription');
        $this->User = ClassRegistry::init('User');
        $this->DealerCompany = ClassRegistry::init('DealerCompany');
        $this->Station = ClassRegistry::init('Station');
        $this->FileProccessingDetail = ClassRegistry::init('FileProccessingDetail');
        $subscriptions = $this->Subscription->getAllSubscriptions();
        $previousMonthYear = date('Y-m', strtotime('-1 month'));
        /**
         * Generate Company Billed
         */
        $companyDetails = $this->User->find('all', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count'
            ),
            'contain' => false,
            'conditions' => array(
                'NOT' => array('subscription_id' => 0),
                'role' => COMPANY,
                'user_type' => SUPAR_ADM,
                'status' => 'active'
            )
        ));
        $companyDetails = Hash::extract($companyDetails, '{n}.User');
        $companyDetails = Hash::combine($companyDetails, '{n}.id', '{n}');
        $lastInsertedId = 0;
        foreach ($companyDetails as $companyDetail) {
            $invoiceId = $this->generateInvoiceId(COMPANY, $companyDetail['id']);
            $countInvoice = $this->find('count', array('conditions' => array('user_id' => $companyDetail['id'])));
            $subscriptionDetail = $this->Subscription->getSubscriptionDetail($companyDetail['subscription_id']);
            $grossAmount = 0.00;
            $branchAmount = $subscriptionDetail['branch_cost'] * $companyDetail['company_branch_count'];
            $totalStationFile = $this->FileProccessingDetail->getStationFileInLastMonth(array('FileProccessingDetail.company_id' => $companyDetail['id']));
            $stationAmount = $subscriptionDetail['setup_cost'] * $companyDetail['station_count'] * $totalStationFile;
            $discount = getSubcriptionDiscount($companyDetail['station_count']);
            $grossAmount = ($branchAmount) + ($stationAmount);
            if ($countInvoice < 2) {
                $grossAmount = $subscriptionDetail['charge'] + ($branchAmount) + ($stationAmount);
            }
            $billedAmount = empty($discount) ? $grossAmount : ($grossAmount - ($grossAmount * $discount));
            $saveData = array(
                'invoice_id' => $invoiceId,
                'customer_type' => COMPANY,
                'name' => $companyDetail['first_name'],
                'invoice_date' => date('Y-m-d'),
                'gross_amount' => $grossAmount,
                'discount' => $discount,
                'billed_amount' => $billedAmount,
                'billed_date' => date('Y-m-d', strtotime('-1 month')),
                'status' => 'unpaid',
                'user_id' => $companyDetail['id']
            );
            $invoiceDetail = $this->find('first', array('conditions' => array('invoice_id' => $invoiceId)));
            if (!empty($invoiceDetail)) {
                $saveData['id'] = $invoiceDetail['Invoice']['id'];
            } else {
                $this->create();
            }
            $this->save($saveData);
            $this->requestAction(array('controller' => 'subscriptions', 'action' => 'generate_pdf', $this->id, $companyDetail['id']));
        }
        /**
         * Generate Dealer Billed
         */
        $dealerDetails = $this->User->find('all', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count'
            ),
            'contain' => false,
            'conditions' => array(
                'NOT' => array('subscription_id' => 0),
                'role' => DEALER,
                'user_type' => SUPAR_ADM,
                'status' => 'active'
            )
        ));
        $dealerDetails = Hash::extract($dealerDetails, '{n}.User');
        $dealerDetails = Hash::combine($dealerDetails, '{n}.id', '{n}');
        $lastInsertedId = 0;
        foreach ($dealerDetails as $dealerDetail) {
            $invoiceId = $this->generateInvoiceId(DEALER, $dealerDetail['id']);

            $invoiceData = $this->getDealerInvoiceData($invoiceId, $dealerDetail['id']);
            $saveData = array(
                'invoice_id' => $invoiceId,
                'customer_type' => DEALER,
                'name' => $dealerDetail['first_name'],
                'invoice_date' => date('Y-m-d'),
                'gross_amount' => $invoiceData['grossAmount'],
                'discount' => $invoiceData['discount'],
                'billed_amount' => $invoiceData['billedAmount'],
                'billed_date' => date('Y-m-d', strtotime('-1 month')),
                'status' => 'unpaid',
                'user_id' => $dealerDetail['id']
            );
            $invoiceDetail = $this->find('first', array('conditions' => array('invoice_id' => $invoiceId)));
            if (!empty($invoiceDetail)) {
                $saveData['id'] = $invoiceDetail['Invoice']['id'];
            } else {
                $this->create();
            }
            $this->save($saveData);
            $this->requestAction(array('controller' => 'subscriptions', 'action' => 'dealer_generate_pdf', $this->id, $dealerDetail['id']));
        }
        echo "Cron Executed Successfully.<br>Thank You.";
        exit;
    }

    function getDealerInvoiceData($invoiceId = null, $userId = 0)
    {
        $this->Subscription = ClassRegistry::init('Subscription');
        $this->User = ClassRegistry::init('User');
        $this->DealerCompany = ClassRegistry::init('DealerCompany');
        $this->Station = ClassRegistry::init('Station');
        $this->FileProccessingDetail = ClassRegistry::init('FileProccessingDetail');
        $dealerDetail = $this->User->find('first', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count'
            ),
            'contain' => false,
            'conditions' => array(
                'NOT' => array('subscription_id' => 0),
                'role' => DEALER,
                'user_type' => SUPAR_ADM,
                'status' => 'active',
                'id' => $userId
            )
        ));
        $dealerDetail = $dealerDetail['User'];

        $companyList = $this->DealerCompany->find('list', array(
            'fields' => 'company_id, company_id',
            'conditions' => array('DealerCompany.dealer_id' => $userId)
        ));

        $silverCompany = $this->getInvoiceCompanyDetail($companyList, SILVER_ID);

        $silverCompanyCount = $silverCompany['silverCompanyCount'];
        $silverCompanyList = $silverCompany['silverCompanyList'];
        $silverCompanyStationCount = $silverCompany['silverCompanyStationCount'];
        $silverCompanyBranchCount = $silverCompany['silverCompanyBranchCount'];
        $silverCompanyStationFile = $silverCompany['silverCompanyStationFile'];

        $goldCompany = $this->getInvoiceCompanyDetail($companyList, GOLD_ID);

        $goldCompanyCount = $goldCompany['silverCompanyCount'];
        $goldCompanyList = $goldCompany['silverCompanyList'];
        $goldCompanyStationCount = $goldCompany['silverCompanyStationCount'];
        $goldCompanyBranchCount = $goldCompany['silverCompanyBranchCount'];
        $goldCompanyStationFile = $goldCompany['silverCompanyStationFile'];


        $platCompany = $this->getInvoiceCompanyDetail($companyList, GOLD_ID);

        $platCompanyCount = $platCompany['silverCompanyCount'];
        $platCompanyList = $platCompany['silverCompanyList'];
        $platCompanyStationCount = $platCompany['silverCompanyStationCount'];
        $platCompanyBranchCount = $platCompany['silverCompanyBranchCount'];
        $platCompanyStationFile = $platCompany['silverCompanyStationFile'];


        $goldSubscruptionDetail = $this->Subscription->getSubscriptionDetail(GOLD_ID);
        $silverSubscruptionDetail = $this->Subscription->getSubscriptionDetail(SILVER_ID);
        $platSubscruptionDetail = $this->Subscription->getSubscriptionDetail(PLATINUM_ID);

        $subscriptionDetail = $this->Subscription->getSubscriptionDetail($dealerDetail['subscription_id']);

        $grossAmount = 0.00;
        $countInvoice = $this->find('count', array('conditions' => array('user_id' => $userId)));
        if ($countInvoice < 2) {
            $grossAmount = $subscriptionDetail['charge'];
        }
        $silverCompanyAmount = ($silverSubscruptionDetail['charge'] * $silverCompanyCount) +
            ($silverSubscruptionDetail['branch_cost'] * $silverCompanyBranchCount) +
            ($silverSubscruptionDetail['setup_cost'] * $silverCompanyStationCount * $silverCompanyStationFile);

        $goldCompanyAmount = ($goldSubscruptionDetail['charge'] * $goldCompanyCount) +
            ($goldSubscruptionDetail['branch_cost'] * $goldCompanyBranchCount) +
            ($goldSubscruptionDetail['setup_cost'] * $goldCompanyStationCount * $goldCompanyStationFile);

        $platCompanyAmount = ($platSubscruptionDetail['charge'] * $platCompanyCount) +
            ($platSubscruptionDetail['branch_cost'] * $platCompanyBranchCount) +
            ($platSubscruptionDetail['setup_cost'] * $platCompanyStationCount * $platCompanyStationFile);

        $grossAmount = $grossAmount + $silverCompanyAmount + $goldCompanyAmount + $platCompanyAmount;


        $totalStationCount = $silverCompanyStationCount + $goldCompanyStationCount + $platCompanyStationCount;
        $discount = getSubcriptionDiscount($totalStationCount);

        $billedAmount = empty($discount) ? $grossAmount : ($grossAmount - ($grossAmount * $discount));
        $responseArr = array(
            'companyList' => $companyList,
            'platCompany' => array(
                'count' => $platCompanyCount,
                'branch_count' => $platCompanyBranchCount,
                'station_count' => $platCompanyStationCount,
                'file_count' => $platCompanyStationFile
            ),
            'goldCompany' => array(
                'count' => $goldCompanyCount,
                'branch_count' => $goldCompanyBranchCount,
                'station_count' => $goldCompanyStationCount,
                'file_count' => $goldCompanyStationFile
            ),
            'silverCompany' => array(
                'count' => $silverCompanyCount,
                'branch_count' => $silverCompanyBranchCount,
                'station_count' => $silverCompanyStationCount,
                'file_count' => $silverCompanyStationFile
            ),
            'grossAmount' => $grossAmount,
            'billedAmount' => $billedAmount,
            'discount' => $discount,
            'totalStationCount' => $totalStationCount
        );
        return $responseArr;
    }

    function getInvoiceCompanyDetail($companyList = 0, $subId = null)
    {
        $this->Subscription = ClassRegistry::init('Subscription');
        $this->User = ClassRegistry::init('User');
        $this->DealerCompany = ClassRegistry::init('DealerCompany');
        $this->Station = ClassRegistry::init('Station');
        $this->FileProccessingDetail = ClassRegistry::init('FileProccessingDetail');

        $silverCompanyCount = $this->User->find('count', array(
            'conditions' => array(
                'User.id' => $companyList,
                'User.subscription_id' => $subId
            )
        ));
        $silverCompanyList = $this->User->find('list', array(
            'contain' => false,
            'fields' => 'id, id',
            'conditions' => array(
                'User.id' => $companyList,
                'User.subscription_id' => $subId
            )
        ));
        $this->User->virtualFields['total_branch_count'] = 'sum(User.company_branch_count)';
        $this->User->virtualFields['total_station_count'] = 'sum(User.station_count)';
        $silverCompanyListDetail = $this->User->find('all', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count',
                'total_branch_count', 'total_station_count'
            ),
            'contain' => false,
            'conditions' => array(
                'User.id' => $silverCompanyList,
                'User.subscription_id' => $subId
            )
        ));
        $silverCompanyStationCount = isset($silverCompanyListDetail['User']['total_station_count']) ? $silverCompanyListDetail['User']['total_station_count'] : 0;
        $silverCompanyBranchCount = isset($silverCompanyListDetail['User']['total_branch_count']) ? $silverCompanyListDetail['User']['total_station_count'] : 0;

        $silverCompanyStationFile = $this->FileProccessingDetail->getStationFileInLastMonth(
            array('FileProccessingDetail.company_id' => $silverCompanyList));
        $retArr = array(
            'silverCompanyStationFile' => $silverCompanyStationFile,
            'silverCompanyList' => $silverCompanyList,
            'silverCompanyStationCount' => $silverCompanyStationCount,
            'silverCompanyBranchCount' => $silverCompanyBranchCount,
            'silverCompanyCount' => $silverCompanyCount
        );
        return $retArr;
    }

    function getStationSetupCost($subscriptionId = null, $fileDate = null)
    {
        $retArr = array();
        $this->Subscription = ClassRegistry::init('Subscription');
        $subscriptionDetail = $this->Subscription->find('first', array(
            'contain' => false,
            'conditions' => array(
                'Subscription.id' => $subscriptionId
            )
        ));
        $retArr['setup_cost'] = $subscriptionDetail['Subscription']['setup_cost'];
        $retArr['charge'] = getSubscriptionCharge($subscriptionDetail['Subscription']['charge'], $fileDate);
        $retArr['total_amount'] = $retArr['setup_cost'] + $retArr['charge'];
        return $retArr;
    }

    function generateInvoiceId($type = DEALER, $userId = 0, $branchId = 0)
    {

        $inv = $this->find('first', array(
            'fields' => 'invoice_id, invoice_date',
            'conditions' => array(
                'DATE_FORMAT(billed_date,"%Y-%m")' => date("Y-m", strtotime('-1 month')),
                'user_id' => $userId,
                'customer_type' => $type
            ),
            'recursive' => -1,
            'order' => 'invoice_id DESC'
        ));
        $previousDate = date("Ym", strtotime('-1 month'));
        if (!empty($inv)) {
            return $inv['Invoice']["invoice_id"];
        } else {
            $lastInvoiceId = $this->find('first', array('fields' => 'id', 'order' => 'Invoice.id DESC'));
            $userId = isset($lastInvoiceId['Invoice']['id']) ? $lastInvoiceId['Invoice']['id'] + 1 : 1;
            $userId = sprintf('%04u', $userId);

//            if (!empty($branchId)) {
//                $userId = $userId . '_' . $branchId;
//            }
            $invoiceId = $type . $previousDate . '#' . $userId;
            return $invoiceId;
        }
    }
}
