<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportController
 *
 * @author securemetasys002
 */
class ExportController extends AppController
{
    public $uses = array('Export', 'Analytic');

    function transaction_details($all = null)
    {
        if (!empty($all)) {
            $this->set(compact('all'));
        }
        ini_set('memory_limit', '-1');
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TransactionReportCondition');
          if(isset($conditions['FileProccessingDetail.regiones'])){
            unset($conditions['FileProccessingDetail.regiones']);
        } 
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);


        $this->loadModel('TransactionDetail');
        $transactions = $this->TransactionDetail->find('all', $transactionDetailArray['paginate2']);
        $companyDetail = array();
        $this->loadModel('stations');
        $this->loadModel('Region');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $regionesCondition['Region.company_id'] = $conditions['FileProccessingDetail.company_id'];
        $regiondata = $this->Region->find('list',array('conditions'=>$regionesCondition));
        if (empty($transactions)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('transactions', 'companyDetail','temp_station','temp_companydata','regiondata'));
    }

    function note_count()
    {
        $this->layout = null;
        $this->autoLayout = false;
        
        $this->loadModel('TransactionDetail');
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';
        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_deposit_denom1'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_1,0)) AS deposit_denom_1';
        $this->TransactionDetail->virtualFields['total_deposit_denom2'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_2,0)) AS deposit_denom_2';
        $this->TransactionDetail->virtualFields['total_deposit_denom5'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_5,0)) AS deposit_denom_5';
        $this->TransactionDetail->virtualFields['total_deposit_denom10'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_10,0)) AS deposit_denom_10';
        $this->TransactionDetail->virtualFields['total_deposit_denom20'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_20,0)) AS deposit_denom_20';
        $this->TransactionDetail->virtualFields['total_deposit_denom50'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_50,0)) AS deposit_denom_50';
        $this->TransactionDetail->virtualFields['total_deposit_denom100'] = 'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_100,0)) AS deposit_denom_100';
        $conditions = $this->Session->read('Report.TransactionReport_NoteCondition');
        $transactionDetailArray = $this->Analytic->getTransactionDetailsForNoteCount($conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $transactions = $this->paginate('TransactionDetail');
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('transactions','temp_station'));
    }
    function special_notes_reconciliation($all = null)
    {
        if (!empty($all)) {
            $this->set(compact('all'));
        }
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.Special_notes_reconciliation_condition');
        $SpecialNotes = $this->Analytic->getSpecialnotesreconciliation($conditions);
        $this->loadModel('Specialnotesreconciliation');
        $specialNotes = $this->Specialnotesreconciliation->find('all', $SpecialNotes['paginate']);
        // $this->AutoPaginate->setPaginate($SpecialNotes['paginate']);
        // $specialNotes = $this->paginate('Specialnotesreconciliation');
        if (empty($specialNotes)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->loadModel('Region');
        $regionesCondition['Region.company_id'] = $conditions['FileProccessingDetail.company_id'];
        $regiondata = $this->Region->find('list',array('conditions'=>$regionesCondition));
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('specialNotes','temp_station','regiondata'));
    }
    function activity_report()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.ActivityFilterReportCondition');
        $activityReportArr = $this->Analytic->getActivityReport($conditions);
        $this->loadModel('ActivityReport');

        $this->ActivityReport->virtualFields['bill_count'] = 'count(ActivityReport.id)';
        $activities = $this->ActivityReport->find('all', $activityReportArr['paginate']);

        if (empty($activities)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'activities'));
    }

    function automix()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.AutomixSettingReportCondition');
        $automixArr = $this->Analytic->getAutomix($conditions);

        $this->loadModel('AutomixSetting');
//        $this->AutomixSetting->virtualFields['bill_count'] = 'count(AutomixSetting.id)';
        $automixSettings = $this->AutomixSetting->find('all', $automixArr['paginate']);
        if (empty($automixSettings)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'automixSettings'));
    }

    function bill_activity($all = null)
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.BillsActivityReportCondition');
        if(isset($conditions['FileProccessingDetail.regiones'])){
            unset($conditions['FileProccessingDetail.regiones']);
        } 
         
        // $billActivityArr = $this->Analytic->getBillActivity($conditions);
        $this->loadModel('BillsActivityReport');
        $this->BillsActivityReport->virtualFields['bill_count'] = 'count(BillsActivityReport.id)';

$this->BillsActivityReport->virtualFields['denom_100'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1 
    THEN BillsActivityReport.denom_100 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom_50'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1 
    THEN BillsActivityReport.denom_50 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom_20'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1 
    THEN BillsActivityReport.denom_20 
    ELSE 0 
END)';$this->BillsActivityReport->virtualFields['denom_10'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1 
    THEN BillsActivityReport.denom_10 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom_5'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_5 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom_2'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_2 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom_1'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_1 
    ELSE 0 
END)';



 $this->BillsActivityReport->virtualFields['denom2_100'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_100 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_50'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_50 
    ELSE 0 
END)';
$this->BillsActivityReport->virtualFields['denom2_20'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_20 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_10'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_10 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_5'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_5 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_2'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_2 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_1'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_1 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom2_100'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 7
    THEN BillsActivityReport.denom_100 
    ELSE 0 
END)';
        
        $this->BillsActivityReport->virtualFields['denom3_100'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3 
    THEN BillsActivityReport.denom_100 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_50'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3
    THEN BillsActivityReport.denom_50 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_20'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3
    THEN BillsActivityReport.denom_20 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_10'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3 
    THEN BillsActivityReport.denom_10 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_5'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3
    THEN BillsActivityReport.denom_5
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_2'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3
    THEN BillsActivityReport.denom_2
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom3_1'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 3
    THEN BillsActivityReport.denom_1 
    ELSE 0 
END)';

 $this->BillsActivityReport->virtualFields['denom1_100'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_100 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_50'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_50 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_20'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_20 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_10'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1 
    THEN BillsActivityReport.denom_10 
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_5'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_5
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_2'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_2
    ELSE 0 
END)';
 $this->BillsActivityReport->virtualFields['denom1_1'] = 'SUM(CASE 
    WHEN BillsActivityReport.bill_type_id = 1
    THEN BillsActivityReport.denom_1 
    ELSE 0 
END)';
        $billActivityArr = $this->Analytic->getBillActivity($conditions);
        $bills = $this->BillsActivityReport->find('all', $billActivityArr['paginate_new']);
        $exportCondition = $all;
        $companyDetail = array();
        if (empty($bills)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->loadModel('stations');
        $stattionData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stattionData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('bills', 'billTypes', 'branches', 'companyDetail','temp_station','exportCondition'));
    }

    function bill_adjustment($all = '')
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.BilladjustReportCondition');
        $billAdjustmentArr = $this->Analytic->getTransactionDetails($conditions);

        $this->loadModel('TransactionDetail');
//        $this->BillAdjustment->virtualFields['bill_count'] = 'count(BillAdjustment.id)';
        $this->Session->write('Report.TransactionReportCondition', $conditions);
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';
        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)';
        $bills = $this->TransactionDetail->find('all', $billAdjustmentArr['paginate3']);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.BillAdjustmentFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($bills)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->loadModel('Stations');
        $stationData = $this->Stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['Stations']['id']] = $value['Stations']['name'] . ' (' . $value['Stations']['station_code'] . ')';
        }
        if (empty($bills)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'bills','temp_station'));
    }

    function transaction_details2()
    {
        $this->layout = null;
        $this->autoLayout = false;   
        $conditions = $this->Session->read('Report.TransactionCondition_2');
        $this->loadModel('TransactionDetail');
        $transactionDetailArray = $this->Analytic->getTransactionDetails2($conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate2']);
        $transactionsDetails = $this->paginate('TransactionDetail');
        $this->loadModel('stations');
        $this->loadModel('Region');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $regionesCondition['Region.company_id'] = $conditions['FileProccessingDetail.company_id'];
        $regiondata = $this->Region->find('list',array('conditions'=>$regionesCondition));
        if (empty($transactionsDetails)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('transactionsDetails', 'temp_station', 'temp_companydata','regiondata'));
    }
    function bill_count($all = '')
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.BillCountReportCondition');
        $billCountArr = $this->Analytic->getBillCount($conditions);
        $this->loadModel('BillCount');
//        $this->BillCount->virtualFields['bill_count'] = 'count(BillCount.id)';
        $bills = $this->BillCount->find('all', $billCountArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.' . $repSesName, $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.' . $filSesName, $this->request->data['Analytic']);
            if (!isCompany()) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($bills)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'bills'));
    }

    function bill_history()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.BillHistoryReportCondition');
        $billHistoryArr = $this->Analytic->getBillHistory($conditions);


        $this->loadModel('BillHistory');
//        $this->BillHistory->virtualFields['bill_count'] = 'count(BillHistory.id)';
        $bills = $this->BillHistory->find('all', $billHistoryArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.' . $repSesName, $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.' . $filSesName, $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($bills)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('bills', 'branches', 'companyDetail'));
    }

    function coin_inventory()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.CoinInventoryReportCondition');
        $coinInventoryArr = $this->Analytic->getCoinInventory($conditions);
        $this->loadModel('CoinInventory');
//        $this->CoinInventory->virtualFields['bill_count'] = 'count(CoinInventory.id)';
        $coinInventorys = $this->CoinInventory->find('all', $coinInventoryArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.CoinInventoryReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CoinInventoryFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($coinInventorys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('coinInventorys', 'branches', 'companyDetail', 'stations', 'lineChartName', 'lineChartTitle', 'lineChartxAxisTitle', 'lineChartyAxisTitle'));
    }

    function teller_transaction()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TellerTransactionReportCondition');
        $tellerTransactionArr = $this->Analytic->getTellerTransaction($conditions);

        $this->loadModel('CurrentTellerTransactions');
//        $this->CurrentTellerTransactions->virtualFields['bill_count'] = 'count(CurrentTellerTransactions.id)';
        $tellerTransactions = $this->CurrentTellerTransactions->find('all', $tellerTransactionArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.TellerTransactionReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.TellerTransactionFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($tellerTransactions)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'tellerTransactions'));
    }

    function history()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.HistoryReportCondition');
        $historyReportArr = $this->Analytic->getHistoryReport($conditions);

        $this->loadModel('HistoryReport');
        $this->HistoryReport->virtualFields['bill_count'] = 'count(HistoryReport.id)';
        $historyReport = $this->HistoryReport->find('all', $historyReportArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.HistoryReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.HistoryFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($historyReport)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('historyReport', 'branches', 'companyDetail'));
    }

    function manager_setup()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.ManagerSetupReportCondition');
        $managerSetupArr = $this->Analytic->getManagerSetup($conditions);

        $this->loadModel('ManagerSetup');
//        $this->ManagerSetup->virtualFields['bill_count'] = 'count(ManagerSetup.id)';
        $managerSetups = $this->ManagerSetup->find('all', $managerSetupArr['paginate']);

        $this->set(compact('temp', 'xAxisDates'));
        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.ManagerSetupReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.ManagerSetupFilter', $this->request->data['Analytic']);
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($managerSetups)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('managerSetups', 'branches', 'companyDetail'));
    }

    function net_cash_usage()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.NetCashUsageReportCondition');
        $netCashUsageArr = $this->Analytic->getNetCashUsageActivity($conditions);

        $this->loadModel('NetCashUsageActivityReport');
//        $this->NetCashUsageActivityReport->virtualFields['bill_count'] = 'count(NetCashUsageActivityReport.id)';
        $netCashes = $this->NetCashUsageActivityReport->find('all', $netCashUsageArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.NetCashUsageReport', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.NetCashUsageFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($netCashes)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('netCashes', 'branches', 'companyDetail'));
    }

    function file_processing()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.FileProcessingReportCondition');
        $fileProcessingArr = $this->Analytic->getFileProcessing($conditions);

        $this->loadModel('FileProccessingDetail');
        $this->FileProccessingDetail->virtualFields['fileProcessed'] = "SUM(IF(processing_endtime != '0000-00-00 00:00:00' and processing_endtime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done
        $this->FileProccessingDetail->virtualFields['no_of_file_received'] = "SUM(IF(processing_endtime = '0000-00-00 00:00:00' and processing_starttime = '0000-00-00 00:00:00',1,0))"; //ToDo: cant done
        $this->loadModel('ErrorDetail');
        $this->ErrorDetail->virtualFields['no_of_errors'] = "count(ErrorDetail.id)";
        $this->loadModel('TransactionDetail');
        $this->TransactionDetail->virtualFields['no_of_deposit'] = "count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['no_of_withdrawal'] = "count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['total_cash_deposit'] = "sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.other_cash_deposited,0))";
        $this->TransactionDetail->virtualFields['total_cash_withdrawal'] = "sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))";
        $this->TransactionDetail->virtualFields['total_cash_requested'] = "sum(TransactionDetail.amount_requested)";
        $this->TransactionDetail->virtualFields['no_of_report'] = "count(TransactionDetail.history_report_id)";
        $this->TransactionDetail->virtualFields['no_of_transaction'] = "count(TransactionDetail.id)";
        ClassRegistry::init('AutomixSetting')->virtualFields['no_of_automix'] = "count(AutomixSetting.id)";
        ClassRegistry::init('BillsActivityReport')->virtualFields['no_of_billactivity'] = "count(BillsActivityReport.id)";
        ClassRegistry::init('BillAdjustment')->virtualFields['no_of_billadjustment'] = "count(BillAdjustment.id)";
        ClassRegistry::init('BillCount')->virtualFields['no_of_billcount'] = "count(BillCount.id)";
        ClassRegistry::init('BillHistory')->virtualFields['no_of_billhistory'] = "count(BillHistory.id)";
        ClassRegistry::init('CoinInventory')->virtualFields['no_of_coininventory'] = "count(CoinInventory.id)";
        ClassRegistry::init('CurrentTellerTransactions')->virtualFields['no_of_currTellerTrans'] = "count(CurrentTellerTransactions.id)";
        ClassRegistry::init('HistoryReport')->virtualFields['no_of_historyReport'] = "count(HistoryReport.id)";
        ClassRegistry::init('ManagerSetup')->virtualFields['no_of_mgrSetup'] = "count(ManagerSetup.id)";
        ClassRegistry::init('NetCashUsageActivityReport')->virtualFields['no_of_netCashUsage'] = "count(NetCashUsageActivityReport.id)";
        ClassRegistry::init('SideActivityReport')->virtualFields['no_of_sideActivity'] = "count(SideActivityReport.id)";
        ClassRegistry::init('TellerActivityReport')->virtualFields['no_of_tellerActivity'] = "count(TellerActivityReport.id)";
        ClassRegistry::init('ValutBuy')->virtualFields['no_of_vaultBuy'] = "count(ValutBuy.id)";
        ClassRegistry::init('TellerSetup')->virtualFields['no_of_teller_setup'] = "count(TellerSetup.id)";

        $processFiles = $this->FileProccessingDetail->find('all', $fileProcessingArr['paginate']);

        if (!empty($this->request->data['Filter'])) {
            $this->Session->write('Report.FileProcessingCompany', $this->request->data['Filter']);
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $this->Session->write('Report.CompanyIdCompany', $this->request->data['Analytic']['company_id']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($processFiles)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('companyDetail', 'processFiles'));
    }

    function side_activity()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.SideActivityReportCondition');
        $activityArr = $this->Analytic->getSideActivity($conditions);
        $this->loadModel('SideActivityReport');
//        $this->SideActivityReport->virtualFields['bill_count'] = 'count(SideActivityReport.id)';
        $activity = $this->SideActivityReport->find('all', $activityArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($activity)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('activity', 'branches', 'companyDetail'));
    }

    function teller_activity()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TellerActivityReportCondition');
        $tellerActivityArr = $this->Analytic->getTellerActivity($conditions);
        $this->loadModel('TellerActivityReport');
//        $this->TellerActivityReport->virtualFields['bill_count'] = 'count(TellerActivityReport.id)';
        $tellerActivity = $this->TellerActivityReport->find('all', $tellerActivityArr['paginate']);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        $this->set(compact('tellerActivity', 'branches', 'companyDetail'));
        if (empty($tellerActivity)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
    }

    function teller_setup()
    {
        $this->layout = null;
        $this->autoLayout = false;

        $conditions = $this->Session->read('Report.TellerSetupReportCondition');
        $tellerSetupArr = $this->Analytic->getTellerSetup($conditions);

        $this->loadModel('TellerSetup');
        $this->TellerSetup->virtualFields['bill_count'] = 'count(TellerSetup.id)';

        $tellerSetups = $this->TellerSetup->find('all', $tellerSetupArr['paginate']);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($tellerSetups)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'tellerSetups'));
    }

    function valut_buy()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.ValutBuyReportCondition');
        $valutBuyArr = $this->Analytic->getValutBuy($conditions);

        $this->loadModel('ValutBuy');
//        $this->ValutBuy->virtualFields['bill_count'] = 'count(ValutBuy.id)';
        $valutBuys = $this->ValutBuy->find('all', $valutBuyArr['paginate']);


        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($valutBuys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'valutBuys'));
    }

    function side_log()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.SideLogReportCondition');
        unset($conditions['FileProccessingDetail.page']);
        unset($conditions['FileProccessingDetail.Paginate']);
         if(isset($conditions['FileProccessingDetail.regiones'])){
            unset($conditions['FileProccessingDetail.regiones']);
        } 

        $sideLogArr = $this->Analytic->getSideLog($conditions);
       // echo "<pre>";
//print_r($sideLogArr);

        $this->loadModel('SideLog');
//        $this->SideLog->virtualFields['bill_count'] = 'count(SideLog.id)';
        /*$sideLogs = $this->SideLog->find('all', $sideLogArr['paginate2']);*/
        $sideLogs = $this->Analytic->getSideLogNew($conditions , ''); 
       /* print_r($sideLogs);
         die();*/
        if (empty($sideLogs)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('sideLogs', 'branches', 'companyDetail'));
         
         

    }
     


    function inventory_management($all = null)
    {
        $this->layout = null;
        $this->autoLayout = false;
        $sessionData = getMySessionData();
        $stations = array();
        $conditions = $this->Session->read('Report.inventoryManagementReportReportCondition');


        $this->loadModel('Inventory');
//        $this->Inventory->virtualFields['bill_count'] = 'count(Inventory.id)';
        $this->Inventory->virtualFields['total_denom_1'] = 'sum(denom_1)';
        $this->Inventory->virtualFields['total_denom_2'] = 'sum(denom_2)';
        $this->Inventory->virtualFields['total_denom_5'] = 'sum(denom_5)';
        $this->Inventory->virtualFields['total_denom_10'] = 'sum(denom_10)';
        $this->Inventory->virtualFields['total_denom_20'] = 'sum(denom_20)';
        $this->Inventory->virtualFields['total_denom_50'] = 'sum(denom_50)';
        $this->Inventory->virtualFields['total_denom_100'] = 'sum(denom_100)';
        $InventoryCondition = array(
            'conditions' => $conditions,
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'Inventory.id',
            'contain' => array('FileProccessingDetail' => array(
                'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                'Branch' => array('id', 'name'),
                // 'TransactionDetail' => array('id','teller_name'),
            ))
            );
        $inventorys = $this->Inventory->find('all', $InventoryCondition);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('User')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('User.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($inventorys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->set(compact('inventorys', 'branches', 'companyDetail','temp_station','all'));
    }

    function total_vault_buy()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TotalVaultBuyReportCondition');
        $totalVaultBuyArr = $this->Analytic->getTotalVaultBuy($conditions);

        $this->loadModel('TotalVaultBuy');
//        $this->TotalVaultBuy->virtualFields['bill_count'] = 'count(TotalVaultBuy.id)';
        $totalValutBuys = $this->TotalVaultBuy->find('all', $totalVaultBuyArr['paginate']);
        if (empty($totalValutBuys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('totalValutBuys', 'branches', 'companyDetail'));
    }

    function transaction_vault_buys()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TransactionVaultBuyReportCondition');
        $transactionVaultBuyArr = $this->Analytic->getTransactionVaultBuy($conditions);

        $this->loadModel('TransactionVaultBuy');
//        $this->TransactionVaultBuy->virtualFields['bill_count'] = 'count(TransactionVaultBuy.id)';
        $transactionVaultBuys = $this->TransactionVaultBuy->find('all', $transactionVaultBuyArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($transactionVaultBuys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('transactionVaultBuys', 'branches', 'companyDetail'));
    }

    function teller_user()
    {
        $this->layout = null;
        $this->autoLayout = false;

        $conditions = $this->Session->read('Report.TellerUserCondition');
        $tellerUserArr = $this->Analytic->getTellerUser($conditions);

        $this->loadModel('TransactionVaultBuy');
//        $this->TransactionVaultBuy->virtualFields['bill_count'] = 'count(TransactionVaultBuy.id)';
        $transactionVaultBuys = $this->TransactionVaultBuy->find('all', $tellerUserArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($transactionVaultBuys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('transactionVaultBuys', 'branches', 'companyDetail'));
    }

    function error_warning()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.ErrorWarningCondition');
        $errorWarningArr = $this->Analytic->getErrorWarning($conditions);
        $this->loadModel('Ticket');
        $conditions_count=1;
$conditions_new='';
foreach ($conditions as $key => $value) {
     if($conditions_count==1){
    $conditions_count++;
   }else{
    $conditions_new=$conditions_new." AND ";
   }
   if(is_array($value)){
   $conditions_new=$conditions_new." ".$key." IN(".implode(',',$value).")";

   }else{
    $conditions_new=$conditions_new." ".$key."='".$value."'";

   }
   
} 
        //$tickets = $this->Ticket->find('all', $errorWarningArr['paginate']);
          $sql2="SELECT  ErrorTypes.severity ,   ErrorTypes.transaction_type , ErrorTypes.error_code , Ticket.id, Ticket.company_id, Ticket.branch_id, Ticket.station, Ticket.file_processing_detail_id, Ticket.error_detail_id, Ticket.ticket_date, Ticket.error_warning_status, Ticket.error, Ticket.warning, Ticket.acknowledge_date, Ticket.dealer_id, Ticket.note, Ticket.status, Ticket.dealer_work_hour, Ticket.is_acknowledge, Ticket.created_by, Ticket.updated_by, Ticket.created, Ticket.updated, Company.id,  (CONCAT(Company.first_name, ' ', Company.last_name)) AS Company__name, Dealer.id,  Branch.id, Branch.name, ErrorDetail.id, ErrorDetail.error_type_id FROM tickets AS Ticket RIGHT JOIN file_processing_detail AS FileProccessingDetail ON (Ticket.file_processing_detail_id = FileProccessingDetail.id)  LEFT JOIN users AS Company ON (Ticket.company_id = Company.id) LEFT JOIN users AS Dealer ON (Ticket.dealer_id = Dealer.id) LEFT JOIN company_branches AS Branch ON (Ticket.branch_id = Branch.id) LEFT JOIN error_detail AS ErrorDetail ON (Ticket.error_detail_id = ErrorDetail.id) RIGHT JOIN error_types AS ErrorTypes ON (ErrorTypes.id = ErrorDetail.error_type_id) ";
 $sql=$sql2." WHERE ".$conditions_new."limit 10" ;
 $tickets = $this->Ticket->query($sql);
/* echo "<pre>";
print_r($tickets);  
die();*/
        if (empty($tickets)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('tickets'));
    }

    function index()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.IndexFileProcessingReportCondition');
        $indexArr = $this->Analytic->getindexFileProcessing($conditions);
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('stations');
        $processFiles = $this->FileProccessingDetail->find('all', $indexArr['paginate']);
        $stattionData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stattionData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.CompanyId', $this->request->data['Analytic']['company_id']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($processFiles)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('companyDetail', 'processFiles','temp_station'));
    }

    function database_growth()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.DatabaseGrowthReportCondition');
        $this->loadModel('DatabaseGrowth');
        $databaseGrowths = $this->DatabaseGrowth->find('all', array(
            'conditions' => $conditions,
            'order' => 'DatabaseGrowth.check_date DESC',
        ));

        if (empty($databaseGrowths)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('databaseGrowths'));
    }

    function errors()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $sessData = getMySessionData();
        
        $conditions_date = $this->Session->read('Report.Transaction');
     
        $startDate=$conditions_date['start_date'];
        $endDate=$conditions_date['end_date'];
 
 $conditions = array( 
            'FileProccessingDetail.file_date >= ' => $startDate . ' 00:00:00',
            'FileProccessingDetail.file_date <= ' => $endDate . ' 23:59:59'
        );
$conditions['FileProccessingDetail.company_id']=$sessData['id'];

        $errorReportArr = $this->Analytic->getErrorReport($conditions);

        $this->loadModel('TransactionDetail');
 $this->Session->write('Report.TransactionReportCondition', $conditions);
        $this->TransactionDetail->virtualFields['total_transaction'] = 'count(TransactionDetail.id)';
        $this->TransactionDetail->virtualFields['no_of_withdrawals'] = 'count(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['no_of_diposite'] = 'count(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount'] = 'sum(TransactionDetail.total_amount)';
        $this->TransactionDetail->virtualFields['total_amount_requested'] = 'sum(TransactionDetail.amount_requested)';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_denom1'] = 'sum(denom_1)';
        $this->TransactionDetail->virtualFields['total_denom2'] = 'sum(denom_2)';
        $this->TransactionDetail->virtualFields['total_denom5'] = 'sum(denom_5)';
        $this->TransactionDetail->virtualFields['total_denom10'] = 'sum(denom_10)';
        $this->TransactionDetail->virtualFields['total_denom20'] = 'sum(denom_20)';
        $this->TransactionDetail->virtualFields['total_denom50'] = 'sum(denom_50)';
        $this->TransactionDetail->virtualFields['total_denom100'] = 'sum(denom_100)'; 
         //$this->loadModel('TransactionDetail');
         $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
//        $this->ErrorDetail->virtualFields['error_count'] = 'count(ErrorDetail.id)';
        $errors = $this->TransactionDetail->find('all', $transactionDetailArray['paginate3']);
        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($errors)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('companyDetail', 'errors'));
      /*  echo "<pre>";
        print_r($errors);
        die();*/
    }

    function unidentify_messages()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.UnidentifyMessageReportCondition');
        $unidentifyMessageArr = $this->Analytic->getUnidentifyMessage($conditions);
        $this->loadModel('Message');
        $this->Message->virtualFields['message_count'] = 'count(Message.id)';
        $unidentifyMessages = $this->Message->find('all', $unidentifyMessageArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
//            $this->Session->write('Report.UnidentifyMessagesFilter', $this->request->data['Analytic']);
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($unidentifyMessages)) {
            $this->Session->setFlash(__('No data available for selected period.', true), 'warning');
            $this->redirect($this->referer(), null, true);
        }
        $this->set(compact('branches', 'companyDetail', 'unidentifyMessages'));
    }

    function issue_report()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.IssueReportCondition');
        $issueReportArr = $this->Analytic->getIssueReport($conditions);

        $this->loadModel('Ticket');
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $tickets = $this->Ticket->find('all', $issueReportArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            if (!empty($this->request->data['Analytic']['company_id'])) {
                $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
            }
        }
        if (empty($tickets)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('tickets', 'branches', 'companyDetail'));
    }

    function client_issue()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.ClientIssueReportCondition');
        $clientIssueArr = $this->Analytic->getClientIssue($conditions);

        $this->loadModel('Ticket');
        $this->Ticket->virtualFields['ticket_count'] = 'count(Ticket.id)';
        $tickets = $this->Ticket->find('all', $clientIssueArr['paginate']);
        $this->request->data['Analytic'] = $this->Session->read('Report.ClientIssueReportFilter');
        $companyDetail = array();
        if ($this->Session->check('Report.ClientIssueReportFilter')) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($tickets)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('tickets', 'branches', 'companyDetail'));
    }

    function inventory_by_teller()
    {
        $this->layout = null;
        $this->autoLayout = false;
        $this->loadModel('TellerSetup');
        $this->loadModel('Inventory');
        $conditions = $this->Session->read('Report.InventoryByTellerReportCondition');
        $inventoryByTellerArr = $this->Analytic->getInventoryByTeller($conditions);

        $inventorys = $this->Inventory->find('all', $inventoryByTellerArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($inventorys)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'inventorys'));
    }

    function dashboard()
    {
        $this->layout = false;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.DashboardReportCondition');
        $dashboardArr = $this->Analytic->getDashboard($conditions);
        $this->loadModel('FileProccessingDetail');
        $processFiles = $this->FileProccessingDetail->find('all', $dashboardArr['paginate']);
        if (empty($processFiles)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('processFiles'));
    }

    function teller_user_report()
    {
        $this->layout = false;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.TellerUserReportCondition');
        $tellerUserReportArr = $this->Analytic->getTellerUserReport($conditions);


        $this->loadModel('TellerUserReport');
//        $this->TellerUserReport->virtualFields['bill_count'] = 'count(TellerUserReport.id)';
        $TellerUserReports = $this->TellerUserReport->find('all', $tellerUserReportArr['paginate']);

        $companyDetail = array();
        if (!empty($this->request->data['Analytic'])) {
            $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $this->request->data['Analytic']['company_id'])));
        }
        if (empty($TellerUserReports)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('branches', 'companyDetail', 'stations', 'TellerUserReports'));
    }
    function allTransactionData($var = null)
    {
        if (empty($var)) {
            $var = 'byHour';
        }
        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $this->set(compact('regiones', 'temp_station'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }
        if ($var == 'daily') {
            // Daily Data

            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.DAY,m.regiones order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($var == 'weekly') {
            // Weekly Data

            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`CompanyBranches`.`regiones`) as m  GROUP BY m.WEEK,m.regiones order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($var == 'monthly') {
            // Monthly Data

            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.MONTHNAME,m.regiones order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($var == 'yearly') {
            // Yearly Data
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.YEAR,m.regiones order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        }elseif ($var == 'byHour') {


            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.HOUR,m.regiones order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        }
    }
    function dashboard_daily($var = null, $action = null, $userName = null)
    {
        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }

        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'branchDaily') {
            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.DAY order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'stationDaily') {
            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.DAY,m.station order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'userDaily') {

            $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`

                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.DAY, m.teller_name order by m.DAY_NUMBER";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.DAY,m.DAY_NUMBER,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAYNAME(`TransactionDetail`.`trans_datetime` ) as DAY, DAYOFWEEK( `TransactionDetail`.`trans_datetime`) as DAY_NUMBER, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY DAY(`TransactionDetail`.`trans_datetime`),DAYOFWEEK(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.DAY,m.station order by m.DAY_NUMBER";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
                $this->set(compact('allData'));
            }
        }
    }

    function dashboard_hour($var = null, $action = null, $userName = null)
    {

        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'hourbranch') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.HOUR, m.branch_id order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);

            $this->set(compact('allData'));
        } elseif ($action == 'stationhour') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.HOUR, m.station order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $this->set(compact('allData'));
        } elseif ($action == 'userhour') {

            $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.HOUR, m.teller_name order by m.HOUR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }

                $query_string = "SELECT m.HOUR, m.DAY, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT DAY(`TransactionDetail`.`trans_datetime`) AS DAY, HOUR(`TransactionDetail`.`trans_datetime`) as HOUR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY HOUR(`TransactionDetail`.`trans_datetime`),DAY(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.HOUR, m.teller_name order by m.HOUR";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $this->set(compact('allData'));
            }
        }
    }

    function dashboard_weekly($var = null, $action = null, $userName = null)
    {
        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'branchWeekly') {
            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.WEEK,m.branch_id order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'stationWeekly') {

            $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`station`) as m  GROUP BY m.WEEK,m.station order by m.WEEK";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'userWeekly') {
                $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                ) AS COUNT,
                (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                `FileProccessingDetail`.`id`,
                `FileProccessingDetail`.`branch_id`,
                `FileProccessingDetail`.`company_id`,
                `FileProccessingDetail`.`file_date`,
                `FileProccessingDetail`.`station`,
                `TransactionDetail`.`teller_name`,
                `CompanyBranches`.`regiones`
            FROM
                `dynalytics`.`transaction_details` AS `TransactionDetail`
            LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
            ON
                (
                    `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                )
            LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
            ON
                (
                    `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                )
            INNER JOIN `dynalytics`.`regions` AS `regions`
            ON
                (
                    `regions`.`id` = `CompanyBranches`.`regiones`
                )";
                $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`TransactionDetail`.`teller_name`) as m  GROUP BY m.WEEK,m.teller_name order by m.WEEK";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
                $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
                if (!empty($userName)) {
                    $conditions += array(
                        'TransactionDetail.teller_name' => $userName
                    );
                    $conditions_count = 1;
                    $conditions_new = '';
                    foreach ($conditions as $key => $value) {
                        if ($conditions_count == 1) {
                            $conditions_count++;
                        } else {
                            $conditions_new = $conditions_new . " AND ";
                        }
                        if (is_array($value)) {
                            $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                        } else {
                            if ($key == 'TransactionDetail.teller_name') {
                                $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                            } else {
                                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                            }
                        }
                    }
                    $query_string = "SELECT m.WEEK,min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTH(`TransactionDetail`.`trans_datetime`) AS MONTH,COUNT( `TransactionDetail`.`trans_datetime`
                ) AS COUNT,
                (CASE WHEN DATE_FORMAT(`trans_datetime`, '%d') < 8 THEN 'Week-1' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 7 && DATE_FORMAT(`trans_datetime`, '%d') < 15 THEN 'Week-2' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 14 && DATE_FORMAT(`trans_datetime`, '%d') < 22 THEN 'Week-3' WHEN DATE_FORMAT(`trans_datetime`, '%d') > 21 && DATE_FORMAT(`trans_datetime`, '%d') < 29 THEN 'Week-4' ELSE 'Week-5' END ) AS WEEK,
                `FileProccessingDetail`.`id`,
                `FileProccessingDetail`.`branch_id`,
                `FileProccessingDetail`.`company_id`,
                `FileProccessingDetail`.`file_date`,
                `FileProccessingDetail`.`station`,
                `TransactionDetail`.`teller_name`,
                `CompanyBranches`.`regiones`
            FROM
                `dynalytics`.`transaction_details` AS `TransactionDetail`
            LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
            ON
                (
                    `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                )
            LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
            ON
                (
                    `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                )
            INNER JOIN `dynalytics`.`regions` AS `regions`
            ON
                (
                    `regions`.`id` = `CompanyBranches`.`regiones`
                )";
                    $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),WEEK,`FileProccessingDetail`.`station`) as m  GROUP BY m.WEEK,m.teller_name order by m.WEEK";
                    $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
                    $allData = $this->TransactionDetail->query($sql);
                    $countData = count($allData);
                    $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
                    $this->set(compact('allData'));
            }
        }
    }
    function dashboard_monthly($var = null, $action = null, $userName = null)
    {

        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'hourmonthly') {
            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`branch_id`) as m  GROUP BY m.MONTHNAME order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'stationmonth') {
            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.MONTHNAME,m.station order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'usermonth') {

            $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`

                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`) as m  GROUP BY m.MONTHNAME, m.teller_name order by m.MONTHNAME";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.MONTHNAME,m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT MONTHNAME(`TransactionDetail`.`trans_datetime` ) as MONTHNAME, YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY MONTH(`TransactionDetail`.`trans_datetime`),YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.MONTHNAME,m.station order by m.MONTHNAME";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
                $this->set(compact('allData'));
            }
            
        }
    }
    function dashboard_yearly($var = null, $action = null, $userName = null)
    {
        $this->layout = false;
        $this->autoLayout = false;
        $this->loadModel('TransactionDetail');
        $this->loadModel('FileProccessingDetail');
        $this->loadModel('Analytic');
        $this->loadModel('Region');
        $this->loadModel('stations');
        $this->loadModel('CompanyBranch');
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $sessionData = getMySessionData();
        $regionconditions['company_id'] = $sessionData['id'];
        $regiones = $this->Region->getRegionList($regionconditions);
        $branches = $this->CompanyBranch->getBranchList();
        $tellerNames_Arr = array();
        $tellerNames = $this->TransactionDetail->find('all', array('fields' => 'DISTINCT teller_name '));
        foreach ($tellerNames as $key => $value) {
            $tellerNames_Arr[$value['TransactionDetail']['teller_name']] = $value['TransactionDetail']['teller_name'];
        }
        $this->set(compact('regiones', 'temp_station', 'branches','tellerNames_Arr'));
        $allFilesidsArr = [];
        $allFilesID = $this->TransactionDetail->find('all', array('conditions' => $conditions, 'fields' => 'DISTINCT file_processing_detail_id '));
        foreach ($allFilesID as $key => $value) {
            array_push($allFilesidsArr, $value['TransactionDetail']['file_processing_detail_id']);
        }
        if (!empty($allFilesID)) {
            $conditions += array(
                "TransactionDetail.file_processing_detail_id IN" =>  $allFilesidsArr
            );
        }
        $conditions_count = 1;
        $conditions_new = '';
        foreach ($conditions as $key => $value) {
            if ($conditions_count == 1) {
                $conditions_count++;
            } else {
                $conditions_new = $conditions_new . " AND ";
            }
            if (is_array($value)) {
                $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
            } else {
                $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
            }
        }
        if ($this->Session->check('Pagination.limit')) {
            $Paginate = $this->Session->read('Pagination.limit');
        } else {
            $Paginate = 20;
        }
        $page = 1;
        if (isset($this->request->params['named']['Paginate'])) {
            $Paginate = $this->request->params['named']['Paginate'];
        }
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
        }
        $limit_start = ($Paginate * ($page - 1));
        $limit = "  LIMIT $limit_start , $Paginate";
        if (isset($this->request['named']['sort'])) {
            $order_by = "order by " . $this->request['named']['sort'] . " " . $this->request['named']['direction'];
        } else {
            $order_by = '';
        }

        if ($action == 'branchYearly') {
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`CompanyBranches`.`regiones`) as m  GROUP BY m.YEAR,m.regiones order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            
            $this->set(compact('allData'));
        } elseif ($action == 'stationyearly') {
            $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.station order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'useryearly') {

                    $query_string = "SELECT m.YEAR, min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `TransactionDetail`.`teller_name`,
                    `CompanyBranches`.`regiones`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
            $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`TransactionDetail`.`teller_name`,`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.teller_name,m.station order by m.YEAR";
            $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by;
            $allData = $this->TransactionDetail->query($sql);
            $countData = count($allData);
            $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);
            $this->set(compact('allData'));
        } elseif ($action == 'userdetails') {
            if (!empty($userName)) {
                $conditions += array(
                    'TransactionDetail.teller_name' => $userName
                );
                $conditions_count = 1;
                $conditions_new = '';
                foreach ($conditions as $key => $value) {
                    if ($conditions_count == 1) {
                        $conditions_count++;
                    } else {
                        $conditions_new = $conditions_new . " AND ";
                    }
                    if (is_array($value)) {
                        $conditions_new = $conditions_new . " " . $key . "(" . implode(',', $value) . ")";
                    } else {
                        if ($key == 'TransactionDetail.teller_name') {
                            $conditions_new = $conditions_new . " " . $key . "='" . $value . "'";
                        } else {
                            $conditions_new = $conditions_new . " " . $key . "'" . $value . "'";
                        }
                    }
                }
                $query_string = "SELECT m.YEAR,  min(m.COUNT),max(m.COUNT),sum(m.COUNT), m.id,m.branch_id,m.station,m.file_date,m.company_id,m.regiones,m.teller_name from (SELECT YEAR( `TransactionDetail`.`trans_datetime`) as YEAR, COUNT( `TransactionDetail`.`trans_datetime`
                    ) AS COUNT,
                    `FileProccessingDetail`.`id`,
                    `FileProccessingDetail`.`branch_id`,
                    `FileProccessingDetail`.`company_id`,
                    `FileProccessingDetail`.`file_date`,
                    `FileProccessingDetail`.`station`,
                    `CompanyBranches`.`regiones`,
                    `TransactionDetail`.`teller_name`
                FROM
                    `dynalytics`.`transaction_details` AS `TransactionDetail`
                LEFT JOIN `dynalytics`.`file_processing_detail` AS `FileProccessingDetail`
                ON
                    (
                        `TransactionDetail`.`file_processing_detail_id` = `FileProccessingDetail`.`id`
                    )
                LEFT JOIN `dynalytics`.`company_branches` AS `CompanyBranches`
                ON
                    (
                        `CompanyBranches`.`id` = `FileProccessingDetail`.`branch_id`
                    )
                INNER JOIN `dynalytics`.`regions` AS `regions`
                ON
                    (
                        `regions`.`id` = `CompanyBranches`.`regiones`
                    )";
                $groupBy = "GROUP BY YEAR(`TransactionDetail`.`trans_datetime`),`FileProccessingDetail`.`station`) as m  GROUP BY m.YEAR,m.station order by m.YEAR";
                $sql = $query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by . " " . $limit;
                $allData = $this->TransactionDetail->query($sql);
                $countData = count($allData);
                $allData = $this->TransactionDetail->query($query_string . " WHERE " . $conditions_new . " " . $groupBy . " " . $order_by);   
                $this->set(compact('allData'));
            }
        }
    }
    public function user_activity($all = null){
        if (!empty($all)) {
            $this->set(compact('all'));
        }
        ini_set('memory_limit', '-1');
        $this->layout = null;
        $this->autoLayout = false;
        $conditions = $this->Session->read('Report.UserActivityCondition');
          if(isset($conditions['FileProccessingDetail.regiones'])){
            unset($conditions['FileProccessingDetail.regiones']);
        } 
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);

        $this->loadModel('TransactionDetail');
        $transactions = $this->TransactionDetail->find('all', $transactionDetailArray['paginate2']);
        $companyDetail = array();
        $this->loadModel('stations');
        $this->loadModel('Region');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $this->loadModel('CompanyBranch');
        $companydata = $this->CompanyBranch->find('all');
        $temp_companydata = array();
        foreach ($companydata as $value) {
            $temp_companydata[$value['CompanyBranch']['id']] = $value['CompanyBranch']['name'];
        }
        $regionesCondition['Region.company_id'] = $conditions['FileProccessingDetail.company_id'];
        $regiondata = $this->Region->find('list',array('conditions'=>$regionesCondition));
        if (empty($transactions)) {
            $this->Message->setWarning('No data available for selected period.', $this->referer());
        }
        $this->set(compact('transactions', 'companyDetail','temp_station','temp_companydata','regiondata'));
    }
}
