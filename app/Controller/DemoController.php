<?php 
//require 'aws/aws-autoloader.php';
App::uses('AppController', 'Controller');
require ROOT . DS . 'vendor' . DS . 'autoload.php';

use Aws\S3\S3Client;
// use DateTime;
class AnalyticsController extends AppController
{
    function hour_by_transaction($all = '')
    {
        // if (!empty($all)) {
        $this->Session->delete('Report.CompanyId');
        $this->Session->delete('Report.Transaction');
        $this->Session->delete('Report.HourbyTransactionCondition');
        // }
        $f = 1;
        if (isset($this->request->data['Analytic'])) {
            $f = 0;
            unset($this->request->params['named']['page']);
        }

        $sessionData = getMySessionData();

        $filter_criteria = array();
        $isSuparCompany = isSuparCompany();
        $isCompanyAdmin = isCompanyAdmin();
        if(empty($isSuparCompany) && !empty($isCompanyAdmin)){
            $current_parent_id = ClassRegistry::init('User')->find('first', array('contain' => false, 'fields' => 'company_parent_id', 'conditions' => array('User.id' => $sessionData['id'])));
            $current_parent_id = $current_parent_id['User']['company_parent_id'];
        }
        $company_id = !empty($current_parent_id) ? $current_parent_id : $sessionData['id'];
        if (!empty($this->request->data['Analytic']['company_id'])) {
            $company_id = $this->request->data['Analytic']['company_id'];
        }
        $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name');
        if (isCompany()) {
            $companies = ClassRegistry::init('User')->getMyCompanyList($sessionData['id'], $sessionData['role'], 'User.id, User.first_name', true);
        }
        if (isCompany()) {
            $companies[$sessionData['id']] = $sessionData['first_name'];
        }
        if (isSuparAdmin() && empty($company_id)) {
            $company_id = array_keys($companies);
        }
        $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports();
        if (!isCompany()) {
            $branches = ClassRegistry::init('CompanyBranch')->getBrancheListForReports($companyId);
        }
        $conditions = $this->__getConditions('GlobalFilter', $this->request->data['Filter'], 'TransactionDetail');
        $from = $conditions['from'];
        $xAxisDates1 = $xAxisDates = $conditions['xAxisDates'];
        $tickInterval = $conditions['tickInterval'];
        $startDate = $conditions['start_date'];
        $endDate = $conditions['end_date'];
        $conditions = $conditions['conditions'];
        $conditions = array(
            'TransactionDetail.trans_datetime >= ' => $startDate . ' 00:00:00',
            'TransactionDetail.trans_datetime <= ' => $endDate . ' 23:59:59'
        );
        $getHeaderFilter = $this->Session->read('setHeaderFilter');
        if(!empty($getHeaderFilter)){
            $request_filter = !empty($this->request->data['Analytic']) ? $this->request->data['Analytic'] : [];
            $request_filter = array_merge($request_filter,$getHeaderFilter);
            $this->request->data['Analytic'] = $request_filter;
        }
        $this->loadModel('Region');
        $conditions2['company_id'] = $company_id;
        if (!isCompany() && isset($this->request->data['Analytic']['company_id'])) {
            $conditions2['company_id'] = $this->request->data['Analytic']['company_id'];
        }
        $regiones = $this->Region->getRegionList($conditions2);
        if (!empty($this->request->data['Analytic']['regiones'])) {
            $regionesCondition['Region.id'] = $this->request->data['Analytic']['regiones'];
            $regionsId = $this->request->data['Analytic']['regiones'];
            $region = ClassRegistry::init('Region')->getRegionList($regionesCondition);
            $filter_criteria['region'] = $region[key($region)];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['branch_id'])) {
            $branchCondition['CompanyBranch.id'] = $this->request->data['Analytic']['branch_id'];
            $branche_list = ClassRegistry::init('CompanyBranch')->getMyBranchExist($branchCondition);
            $filter_criteria['branch'] = $branche_list['CompanyBranch']['name'];
            $conditions['FileProccessingDetail.branch_id'] = $this->request->data['Analytic']['branch_id'];
            $stations = ClassRegistry::init('CompanyBranch')->getStationList($this->request->data['Analytic']['branch_id']);
            $this->set(compact('filter_criteria'));
        }
        if (isCompany()) {
            $conditions['FileProccessingDetail.company_id'] = $company_id;//getCompanyId();
        } else if (!empty($this->request->data['Analytic']['company_id'])) {
            $conditions['FileProccessingDetail.company_id'] = $this->request->data['Analytic']['company_id'];
        }
        if (!empty($this->request->data['Analytic']['station'])) {
            $stationConditions['Station.id'] = $this->request->data['Analytic']['station'];
            $stationsList = ClassRegistry::init('Station')->getStation($stationConditions);
            $filter_criteria['station'] = $stationsList['Station']['name'];
            $conditions['FileProccessingDetail.station'] = $this->request->data['Analytic']['station'];
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['daterange'])) {
            $date = (explode("-", $this->request->data['Analytic']['daterange']));
            $conditions['TransactionDetail.trans_datetime >= '] = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
            $conditions['TransactionDetail.trans_datetime <= '] = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            $startDate = date('Y-m-d', strtotime($date[0]));
            $endDate = date('Y-m-d', strtotime($date[1]));
            $filter_criteria['selected_dates'] = $startDate . " To " . $endDate;
            $this->set(compact('filter_criteria'));
        }
        if (!empty($this->request->data['Analytic']['regiones']) && empty($this->request->data['Analytic']['branch_id'])) {
            $branches = ClassRegistry::init('CompanyBranch')->getMyBranchLists2($this->request->data['Analytic']['regiones']);            $conditions_regiones = array(
                'CompanyBranch.regiones' => $this->request->data['Analytic']['regiones'],
                'CompanyBranch.branch_status' => 'active',
            );
            $branchLists = ClassRegistry::init('CompanyBranch')->find('list', array(
                'fields' => 'id, id',
                'contain' => false,
                'conditions' => $conditions_regiones
            ));
            $conditions['FileProccessingDetail.branch_id'] = $branchLists;
        }
        $transactionArray = $this->Analytic->getTransactionDetails($conditions, $regionsId);
        $this->loadModel('TransactionDetail');
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
        // echo '<pre><b></b><br>';
        // print_r($conditions);echo '<br>';exit;
        $transactionDetailArray = $this->Analytic->getTransactionDetails($conditions);
        $this->AutoPaginate->setPaginate($transactionDetailArray['paginate3']);
        $pieTitle = __('Inventory Management Transaction Type');
        $pieCatTitle = __('Transaction V/s Transaction Category');
        $pieClientTitle = __('Transactions by Branch');
        $pieName = __('Transactions');
        $transactions = $this->paginate('TransactionDetail');
        $transaction_chart = $this->TransactionDetail->find('all', $transactionDetailArray['paginate3']);
        $key_arr = array();
        $key_sort = array();
        foreach ($transaction_chart as $key => $value) {

            $key_arr[date("H", strtotime($value['TransactionDetail']['trans_datetime']))][$key] = $value['TransactionDetail']['total_transaction'];
            array_push($key_sort, date("H", strtotime($value['TransactionDetail']['trans_datetime'])));
        }
        $minValues = array();
        $maxValues = array();
        $original_Arr = array();
        $keyArr2 = array();
        $i = 1;
        $original_Arr[0][0] = "Hour";
        $original_Arr[0][1] = "Minimum";
        $original_Arr[0][2] = "Average";
        $original_Arr[0][3] = "Maximum";
        asort($key_sort);
        foreach ($key_sort as $key => $value) {
            $keyArr2[$value] = $key_arr[$value];
        }
        foreach ($keyArr2 as $key => $item) {
            $key_arr[$key] = array_values($item);
            $count_total = count($key_arr[$key]);
            $avrage_total = array_sum($key_arr[$key]) / $count_total;
            $date=date_create($key.":00:00");
            $time = date_format($date,"g A");
            $original_Arr[$i][0] = "$time ";
            $original_Arr[$i][1] = min($key_arr[$key]);
            $original_Arr[$i][2] = ceil($avrage_total);
            $original_Arr[$i][3] = max($key_arr[$key]);
            $i++;
        }
        // echo '<pre><b></b><br>';
        // print_r($original_Arr);echo '<br>';exit;
        $newBarchat = json_encode($original_Arr, JSON_NUMERIC_CHECK);
        if (!empty($this->request->data['Analytic']['date'])) {
            $aa = $this->request->data['Analytic']['date'];
            $aa = str_replace('-', '/', $aa);
            $aa = date('Y-m-d', strtotime($aa));
            $this->request->data['Analytic']['date'] = $aa;

            $conditions_new = $conditions;
            $conditions_new['TransactionDetail.trans_datetime >= '] = $this->request->data['Analytic']['date'] . ' 00:00:00';
            $conditions_new['TransactionDetail.trans_datetime <= '] = $this->request->data['Analytic']['date'] . ' 23:59:59';
            $conditions_new['FileProccessingDetail.file_date >= '] = $this->request->data['Analytic']['date'];
            $conditions_new['FileProccessingDetail.file_date <= '] = $this->request->data['Analytic']['date'];
            $hourly_data2 = array();
            $hourly_data3 = array();

            $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions_new);
            $hourly_data = $this->TransactionDetail->find('list', $transactionDetailArray2['paginate2']);

            foreach ($hourly_data as $key => $value) {
                $hourly_data2[$key][0] = date("H", strtotime($key));
                $hourly_data2[$key][1] = $value;
            }
            foreach ($hourly_data2 as $key => $value) {
                array_push($hourly_data3, $value);
            }
            if (empty($hourly_data3)) {
                array_push($hourly_data3, [0, 0]);
            }
            $hourly_report_data = json_encode($hourly_data3, JSON_NUMERIC_CHECK);

            if ($hourly_report_data == null) {
                $hourly_report_data = json_encode([[0, 0]], JSON_NUMERIC_CHECK);
            }
        } else {
            $hourly_report_data = json_encode(1, JSON_NUMERIC_CHECK);
        }
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionPieChart']);
        $transactionTypes = ClassRegistry::init('TransactionType')->find('list', array('fields' => 'id, text'));
        $transactionPie = $this->Analytic->getPieChartData($transactionTypes, $transactionPie);
        $transactionPie = json_encode($transactionPie);
        /**
         * transactions vs category
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        //        debug($conditions);

        $transactionCatPie = $this->TransactionDetail->find('list', $transactionDetailArray['chart']['transactionCategoryChart']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));
        $transactionCatPie = $this->Analytic->getPieChartData($transactionCategories, $transactionCatPie, true);
        $transactionCatPie = json_encode($transactionCatPie);
        /**
         * transactions vs branch
         */
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        $transactionClients = $branches;
        $transactionClientPie = $this->Analytic->getPieChartData($transactionClients, $transactionClientPie);
        $transactionClientPie = json_encode($transactionClientPie);
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $this->loadModel('stations');
        $stationData = $this->stations->find('all');
        $temp_station = array();
        foreach ($stationData as $value) {
            $temp_station[$value['stations']['id']] = $value['stations']['name'] . ' (' . $value['stations']['station_code'] . ')';
        }
        $chartData = $this->TransactionDetail->find('all', $transactionDetailArray['chart']['chartData']);
        $chartData2 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData2']);
        $transactionCategories = ClassRegistry::init('TransactionCategory')->find('list', array('fields' => 'id, text'));

        $temp = array();
        $temp2 = array();
        $tempCount = 0;
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['transaction_count']);
            $tempCount++;
        }
        $tempCount = 0;
        foreach ($chartData2 as $key => $value) {
            $date = date('Y-m-d', strtotime($value['TransactionDetail']['trans_datetime']));
            // ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            ($xAxisDates = date_range($date . ' 08:00:00', $date . ' 20:59:59', '+1 hour', 'Y-m-d H:i:s'));
            if (isset($temp2[$date])) {
            } else {
                $temp2[$date] = array((strtotime($value['TransactionDetail']['trans_datetime']) * 1000), $value['TransactionDetail']['trans_datetime'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            }
            if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[0]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[1])) {
                $temp2[$date][2]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[1]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[2])) {
                $temp2[$date][3]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[2]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[3])) {
                $temp2[$date][4]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[3]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[4])) {
                $temp2[$date][5]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[4]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[5])) {
                $temp2[$date][6]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[5]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[6])) {
                $temp2[$date][7]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[6]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[7])) {
                $temp2[$date][8]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[7]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[8])) {
                $temp2[$date][9]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[8]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[9])) {
                $temp2[$date][10]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[9]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[10])) {
                $temp2[$date][11]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[10]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[11])) {
                $temp2[$date][12]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[11]) && strtotime($value['TransactionDetail']['trans_datetime']) < strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            } else if (strtotime($value['TransactionDetail']['trans_datetime']) >= strtotime($xAxisDates[12])) {
                $temp2[$date][13]++;
            }
        }
        $sendArr = array();
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            if (array_key_exists($date, $temp)) {
                $sendArr[$key] = $temp[$date];
            } else {
                $date .= " 00:00:00";
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array(strtotime($date));
        endforeach;

        $temp = $sendArr;
        $temp = json_encode($temp, JSON_NUMERIC_CHECK);
        $xAxisDates = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();
        $companyDetail = ClassRegistry::init('Company')->find('first', array('fields' => 'id, first_name, last_name, email', 'contain' => false, 'conditions' => array('Company.id' => $company_id)));

        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'sum(if(TransactionDetail.trans_type_id=1 or TransactionDetail.trans_type_id=2 ,TransactionDetail.total_amount,0))';
        $this->TransactionDetail->virtualFields['total_amount_withdrawal'] = 'sum(if(TransactionDetail.trans_type_id=1,total_amount,0))';
        $this->TransactionDetail->virtualFields['total_cash_deposite'] = 'sum(if(TransactionDetail.trans_type_id=2,total_amount,0))';
        $chartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'trans_datetime', 'transaction_count', 'total_amount_withdrawal',
                'total_cash_deposite', 'FileProccessingDetail.file_date'
            ),
            'order' => 'FileProccessingDetail.file_date DESC',
            'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));
        $oldTemp = $temp;
        $temp = $temp1 = $temp2 = array();
        foreach ($chartData as $key => $value) {
            $date = date('Y-m-d', strtotime($value['FileProccessingDetail']['file_date']));
            if (isset($temp[$date])) {
                $value['TransactionDetail']['total_amount_withdrawal'] = $value['TransactionDetail']['total_amount_withdrawal'] + $temp[$date][1];
            }
            $temp[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_amount_withdrawal']);
            if (isset($temp1[$date])) {
                $value['TransactionDetail']['total_cash_deposite'] = $value['TransactionDetail']['total_cash_deposite'] + $temp1[$date][1];
            }
            $temp1[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), $value['TransactionDetail']['total_cash_deposite']);
            if (isset($temp2[$date])) {
                $value['TransactionDetail']['transaction_count'] = $value['TransactionDetail']['transaction_count'] + $temp2[$date][1];
            }
            $temp2[$date] = array((strtotime($value['FileProccessingDetail']['file_date']) * 1000), ($value['TransactionDetail']['transaction_count']));
        }
        $sendArr = $sendArr1 = $sendArr2 = array();
        foreach ($xAxisDates1 as $key => $date) :
            /**
             * For the No of error occurred over date period Line
             */
            if (isset($temp[$date])) {
                $sendArr[$key] = $temp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error acknowledged by dealers Line
             */
            if (isset($temp1[$date])) {
                $sendArr1[$key] = $temp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            /**
             * For the No of error resolved by dealer Line
             */
            if (isset($temp2[$date])) {
                $sendArr2[$key] = $temp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
        endforeach;
        $temp1 = array(
            array(
                'name' => 'Debit Transaction',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Credit Transaction',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );

        $temp1 = json_encode($temp1);

        $xAxisDates1 = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData1 = array();
        $temp = $oldTemp;

        $newchartData = $this->TransactionDetail->find('all', array(
            'conditions' => $conditions,
            'fields' => array(
                'denom_1',
                'denom_2',
                'denom_5',
                'denom_10',
                'denom_20',
                'denom_50',
                'denom_100',
                'trans_datetime'
            ),
            'order' => 'trans_datetime DESC',
            'group' => 'DATE_FORMAT(trans_datetime,"%Y-%m-%d %H")',
            'contain' => array(
                'FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                )
            )
        ));

        $newchartData = Hash::extract($newchartData, '{n}.TransactionDetail');
        sort($newchartData);
        $newTemp = $newTemp1 = $newTemp2 = $newTemp3 = $newTemp4 = $newTemp5 = $newTemp6 = array();
        foreach ($newchartData as $key => $value) {
            //                $date = date('Y-m-d', strtotime($value['trans_datetime']));
            $date = date('Y-m-d h:00 a', strtotime($value['trans_datetime']));
            if (isset($newTemp[$date])) {
                $value['denom_1'] = $value['denom_1'] + $newTemp[$date];
            }
            $newTemp[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_1']);
            if (isset($newTemp1[$date])) {
                $value['denom_2'] = $value['denom_2'] + $newTemp1[$date];
            }
            $newTemp1[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_2']);
            if (isset($newTemp2[$date])) {
                $value['denom_5'] = $value['denom_5'] + $newTemp2[$date];
            }
            $newTemp2[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_5']);
            if (isset($newTemp3[$date])) {
                $value['denom_10'] = $value['denom_10'] + $newTemp3[$date];
            }
            $newTemp3[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_10']);
            if (isset($newTemp4[$date])) {
                $value['denom_20'] = $value['denom_20'] + $newTemp4[$date];
            }
            $newTemp4[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_20']);
            if (isset($newTemp5[$date])) {
                $value['denom_50'] = $value['denom_50'] + $newTemp5[$date];
            }
            $newTemp5[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_50']);
            if (isset($newTemp6[$date])) {
                $value['denom_100'] = $value['denom_100'] + $newTemp6[$date];
            }
            $newTemp6[$date] = array((strtotime($value['trans_datetime']) * 1000), $value['denom_100']);
        }
        $sendArr = $sendArr1 = $sendArr2 = $sendArr3 = $sendArr4 = $sendArr5 = $sendArr6 = array();
        $previousDate = date('Y-m-d', strtotime($yesterdayDate));
        $xAxisDates = date_range($previousDate . ' 08:00:00', $previousDate . ' 20:59:59', '+1 hour', 'Y-m-d h:i a');
        foreach ($xAxisDates as $key => $date) :
            $data = 0;
            $bkpdate = $date;
            if (isset($newTemp[$date])) {
                $sendArr[$key] = $newTemp[$date];
            } else {
                $sendArr[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp1[$date])) {
                $sendArr1[$key] = $newTemp1[$date];
            } else {
                $sendArr1[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp2[$date])) {
                $sendArr2[$key] = $newTemp2[$date];
            } else {
                $sendArr2[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp3[$date])) {
                $sendArr3[$key] = $newTemp3[$date];
            } else {
                $sendArr3[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp4[$date])) {
                $sendArr4[$key] = $newTemp4[$date];
            } else {
                $sendArr4[$key] = array((strtotime($date) * 1000), 0);
            }

            $date = $bkpdate;
            if (isset($newTemp5[$date])) {
                $sendArr5[$key] = $newTemp5[$date];
            } else {
                $sendArr5[$key] = array((strtotime($date) * 1000), 0);
            }
            $date = $bkpdate;
            if (isset($newTemp6[$date])) {
                $sendArr6[$key] = $newTemp6[$date];
            } else {
                $sendArr6[$key] = array((strtotime($date) * 1000), 0);
            }
            $xAxisDates[$key] = array((strtotime($date) * 1000));
        endforeach;
        $lastTemp = array(
            array(
                'name' => 'Denom 1',
                'data' => json_encode($sendArr, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 2',
                'data' => json_encode($sendArr1, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 5',
                'data' => json_encode($sendArr2, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 10',
                'data' => json_encode($sendArr3, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 20',
                'data' => json_encode($sendArr4, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 50',
                'data' => json_encode($sendArr5, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            ),
            array(
                'name' => 'Denom 100',
                'data' => json_encode($sendArr6, JSON_NUMERIC_CHECK),
                'pointInterval' => $tickInterval
            )
        );
        $sentTemp = json_encode($lastTemp);
        $xAxisDatesTime = json_encode($xAxisDates, JSON_NUMERIC_CHECK);
        $yAxisData = array();

        $aa = $this->request->data['Analytic']['date'];
        $aa = str_replace('-', '/', $aa);
        $aa = date('m/d/Y', strtotime($aa));
        $this->request->data['Analytic']['date'] = $aa;

        if ($this->request->is('ajax')) {
            $this->layout = false;
            $transactionData = $this->render('/Elements/user/transaction_details')->body();
            $options = array(
                'id' => '#container',
                'name' => __('Transactions'),
                'title' => __('Transactions'),
                'xTitle' => __('Transaction Date'),
                'yTitle' => __('Transaction'),
            );
            echo json_encode(array(
                'options' => $options,
                'data' => $temp1,
                'xAxisDates' => $xAxisDates1,
                'htmlData' => $transactionData,
                'pieChartData' => $transactionPie,
                'transactionCatPie' => $transactionCatPie,
                'pieCatTitle' => $pieCatTitle,
                'transactionClientPie' => $transactionClientPie,
                'pieClientTitle' => $pieClientTitle,
                'pieTitle' => $pieTitle,
                'pieName' => $pieName,
                'tickInterval' => $tickInterval,
                'transactionDetails' => $temp1,
                'hourData' => $temp1
            ));
            exit;
        }

        $this->TransactionDetail = ClassRegistry::init('TransactionDetail');
        $this->TransactionDetail->virtualFields['transaction_count'] = 'count(TransactionDetail.id)';
        // $conditions = array(
        //     'FileProccessingDetail.file_date >= ' => $startDate . ' 00:00:00',
        //     'FileProccessingDetail.file_date <= ' => $endDate . ' 23:59:59',
        //     'FileProccessingDetail.company_id' => $sessData['id']
        // );
        $transactionDetailArray2 = $this->Analytic->getTransactionDetails($conditions);
        $this->loadModel('TransactionDetail');

        $transactionDetailArray2['chart']['chartData']['group'] = 'FileProccessingDetail.file_date';
        $chartData4 = $this->TransactionDetail->find('all', $transactionDetailArray2['chart']['chartData']);
        $i = 0;
        foreach ($chartData4 as $key => $data) {
            $data3[$i][0] = strtotime($data['FileProccessingDetail']['file_date']) * 1000;
            $data3[$i][1] = $data['TransactionDetail']['transaction_count'];
            $i++;
        }

        $newchartdata = json_encode($data3, JSON_NUMERIC_CHECK);
        $this->set(compact('sessionData', 'companies', 'branches', 'regiones', 'stations', 'newBarchat', 'hourly_report_data', 'transactionCatPie', 'transactionClientPie', 'pieClientTitle', 'pieCatTitle', 'pieTitle', 'pieName', 'tickInterval', 'transactions', 'temp_station', 'transactionCategories', 'transactionTypes', 'transactionPie', 'temp', 'xAxisDates', 'temp_hr', 'companyDetail', 'temp', 'temp1', 'xAxisDates1', 'sentTemp', 'xAxisDatesTime', 'newchartdata'));
    }

}