<?php
App::uses('AppModel', 'Model');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Analytic
 *
 * @author securemetasys002
 */
class Analytic extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
    public $useTable = false;

    function getPieChartData($types, $pie, $getNullCategory = false)
    {
        $tempArr = array();
        $transactionPieCount = 0;
        foreach ($pie as $value) {
            $transactionPieCount = $transactionPieCount + $value;
        }
        $nullCount = 0;
        $emptyCount = 0;
        foreach ($types as $typeId => $typeName) {
            if (isset($pie[$typeId])) {
                $percen = getPercentage($transactionPieCount, $pie[$typeId]);
                empty($percen) ? $emptyCount++ : '';
                $tempArr[] = array(
                    // 'name' => $typeName,
                    'name' => str_replace("'","",$typeName),
                    'y' => $percen,
                    'totalcount' => $pie[$typeId]
                );
            } else {
                $nullCount++;
            }
        }
        if (!empty($nullCount) && $getNullCategory && !empty($transactionPieCount)) {
            $percen = getPercentage($transactionPieCount, $nullCount);
            $tempArr[] = array(
                'name' => 'NULL',
                'y' => $percen,
                'totalcount' => $nullCount
            );
        }
        if ($emptyCount == count($types)) {
            $tempArr = array();
        }
        return $tempArr;
    }

    function getDateRanges($timeRange = null)
    {
        if (empty($timeRange)) {
            $timeRange = Configure::read('Site.filterOption');
        }
        // echo '<pre><b></b><br>';
        // print_r($timeRange);echo '<br>';exit;
        //        $timeRange = 'last_6months';
        $retArray = array(
            'xAxisDates' => array(),
            'tickInterval' => 1,
            'start_date' => date('Y-m-d', strtotime('-6 days')),
            'end_date' => date('Y-m-d'),
            'from' => $timeRange
        );
        if ($timeRange == 'last_7days') {
            $retArray['xAxisDates'] = date_range(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'), '+1 day');
        } elseif ($timeRange == 'last_15days') {
            $retArray['tickInterval'] = 2;
            $startDate = date('Y-m-d', strtotime('-14 days'));
            $endDate = date('Y-m-d');
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
        } elseif ($timeRange == 'last_months') {
            $retArray['tickInterval'] = 7;
            $startDate = date('Y-m-01', strtotime('-1 month'));
            $startDate = date('Y-m-d', strtotime('-1 month'));
            //            $endDate = date('Y-m-t', strtotime('-1 month'));//28-01-2016
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_3months') {
            $retArray['tickInterval'] = 14;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_6months') {
            $retArray['tickInterval'] = 30;
            $startDate = date('Y-m-01', strtotime('-6 month'));
            $startDate = date('Y-m-d', strtotime('-6 month'));
            $endDate = date('Y-m-t', strtotime('-1 month'));
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif($timeRange == 'last_12months'){
            $retArray['tickInterval'] = 52;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }elseif($timeRange == "last_18days"){
            $retArray['tickInterval'] = 5;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }else {
            $retArray['xAxisDates'] = [date('Y-m-d')];
        }
        return $retArray;
    }
    function getQuarter($date,$title)
    {
        $time=strtotime($date);
        $m = date("m",$time);
        $y=date("Y",$time);
        if($title == 'last_3months'){
            switch($m) {
                case $m >= 1 && $m <= 3:
                    $start = '01/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('03/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 4 && $m <= 6:
                    $start = '04/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('06/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 7 && $m <= 9:
                    $start = '07/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('09/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
                case $m >= 10 && $m <= 12:
                    $start = '10/01/'.$y;
                    $end = date('m/d/'.$y);//(new DateTime('12/1/'.$y))->modify('Last day of this month')->format('m/d/Y');
                    break;
            }
        }elseif ($title == 'last_12months') {
            $start = '01/01/'.$y;
            $end = date('m/d/'.$y);
        }elseif ($title == "last_months") {
            $start = date("m/d/".$y, strtotime("first day of previous month"));
            $end = date("m/d/".$y, strtotime("last day of previous month"));
        }elseif ($title == "last_18days") {
            $start = date('m/01/Y'); // hard-coded '01' for first day
            $end  = date("m/d/Y");
        }
        $start=date_create($start);
        $start = date_format($start,"Y-m-d");
        $end=date_create($end);
        $end = date_format($end,"Y-m-d");
        return array(
                'start' => $start,
                'end' => $end,
        );
       
    }
    function getTimeRanges($timeRange = null)
    {


        if (empty($timeRange)) {
            //            $timeRange = Configure::read('Site.filterOptionTime');
            $timeRange = '';
        }
        $retArray = array(
            'xAxisDates' => array(),
            'tickInterval' => 1,
            'start_date' => date('Y-m-d', strtotime('-6 days')),
            'end_date' => date('Y-m-d'),
            'from' => $timeRange
        );
        if ($timeRange == 'last_7days') {
            $retArray['xAxisDates'] = date_range(date('Y-m-d', strtotime('-6 days')), date('Y-m-d'), '+1 day');
        } elseif ($timeRange == 'last_15days') {
            $retArray['tickInterval'] = 2;
            $startDate = date('Y-m-d', strtotime('-14 days'));
            $endDate = date('Y-m-d');
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
        } elseif ($timeRange == 'last_months') {
            $retArray['tickInterval'] = 7;
            $startDate = date('Y-m-01', strtotime('-1 month'));
            $startDate = date('Y-m-d', strtotime('-1 month'));
            //            $endDate = date('Y-m-t', strtotime('-1 month'));//28-01-2016
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_3months') {
            $retArray['tickInterval'] = 14;
            $startDate = date('Y-m-01', strtotime('-3 month'));
            $startDate = date('Y-m-d', strtotime('-3 month'));
            $endDate = date('Y-m-t', strtotime('-1 month'));
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif ($timeRange == 'last_6months') {
            $retArray['tickInterval'] = 30;
            $startDate = date('Y-m-01', strtotime('-6 month'));
            $startDate = date('Y-m-d', strtotime('-6 month'));
            $endDate = date('Y-m-t', strtotime('-1 month'));
            $endDate = date('Y-m-d');
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        } elseif($timeRange == "last_18days"){
            $retArray['tickInterval'] = 5;
            $date = $this->getQuarter(date('Y-m-d'),$timeRange);
            $startDate = $date['start'];
            $endDate = $date['end'];
            $retArray['start_date'] = $startDate;
            $retArray['end_date'] = $endDate;
            $retArray['xAxisDates'] = date_range($startDate, $endDate, '+1 day');
        }else {
            $retArray['xAxisDates'] = [date('Y-m-d')];
        }
        return $retArray;
    }

    function getConditions($sessionName = null, $reqData = array(), $model = '')
    {
        $from = !empty($reqData['from']) ? $reqData['from'] : '';
        if (empty($reqData['from']) && CakeSession::check('Report.' . $sessionName)) {
            $from = CakeSession::read('Report.' . $sessionName . '.from');
        }
        $dateRange = $this->getDateRanges($from);
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];
        $retArr = array(
            'from' => $dateRange['from'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'xAxisDates' => $dateRange['xAxisDates'],
            'tickInterval' => $dateRange['tickInterval'],
            'conditions' => array(
                'created_date >= ' => $startDate,
                'created_date <= ' => $endDate
            )
        );
        if (!empty($sessionName)) {
            //            CakeSession::delete('Report.' . $sessionName);
            if (CakeSession::check('Report.' . $sessionName) && empty($reqData)) {
                $reqData = CakeSession::read('Report.' . $sessionName);
            }
            if (!empty($reqData)) {
                $retArr['from'] = !empty($reqData['from']) ? $reqData['from'] : $retArr['from'];
                $dateRange = $this->getDateRanges($retArr['from']);
                $startDate = $retArr['start_date'] = !empty($dateRange['start_date']) ? $dateRange['start_date'] : $retArr['start_date'];
                $endDate = $retArr['end_date'] = !empty($dateRange['end_date']) ? $dateRange['end_date'] : $retArr['end_date'];
                $retArr['xAxisDates'] = $dateRange['xAxisDates'];
                $retArr['tickInterval'] = $dateRange['tickInterval'];
                $retArr['conditions'] = array(
                    'created_date >= ' => $startDate,
                    'created_date <= ' => $endDate
                );
            }
        }
        if (!empty($model)) {
            $retArr['conditions'][$model . '.created_date >= '] = $retArr['conditions']['created_date >= '];
            $retArr['conditions'][$model . '.created_date <= '] = $retArr['conditions']['created_date <= '];
            unset($retArr['conditions']['created_date >= ']);
            unset($retArr['conditions']['created_date <= ']);
        }
        return $retArr;
    }

    function getSideActivity($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'ActivityReport')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getTransactionDetailsForNoteCount($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate2' => array(
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.total_transaction',

                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime ASC',
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`), DAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name')
                    )
                )
            ),
            'paginate3' => array(
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.total_transaction',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_1,0)) AS deposit_denom_1',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_2,0)) AS deposit_denom_2',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_5,0)) AS deposit_denom_5',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_10,0)) AS deposit_denom_10',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_20,0)) AS deposit_denom_20',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_50,0)) AS deposit_denom_50',
                    'SUM(if(TransactionDetail.trans_type_id=2,TransactionDetail.denom_100,0)) AS deposit_denom_100',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_1,0)) AS withdrawal_denom_1',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_2,0)) AS withdrawal_denom_2',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_5,0)) AS withdrawal_denom_5',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_10,0)) AS withdrawal_denom_10',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_20,0)) AS withdrawal_denom_20',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_50,0)) AS withdrawal_denom_50',
                    'SUM(if(TransactionDetail.trans_type_id= 1 or TransactionDetail.trans_type_id=11,TransactionDetail.denom_100,0)) AS withdrawal_denom_100',
                    'regions.name',

                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime DESC',
                'group' =>   'FileProccessingDetail.station',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones')
                    )
                )
            )
        );
        return $responseArr;
    }

    function getTransactionDetails($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $new_conditions = $conditions;
            $new_conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginateCount' => array(
                'fields' => array(
                   'SUM(TransactionDetail.total_amount) AS total_amount','TransactionDetail.*'
                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name','regiones'),
                    ),
                    'TransactionType' => array('id', 'text'),
                )
            ),
            'paginate2' => array(
                'fields' => array(
                   'TransactionDetail.*'
                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station', 'filename'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name','regiones'),
                    ),
                    'TransactionType' => array('id', 'text'),
                )
            ),
            'paginate3' => array(
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.total_transaction',
                    'SUM(TransactionDetail.denom_1) AS denom_1',
                    'SUM(TransactionDetail.denom_2) AS denom_2',
                    'SUM(TransactionDetail.denom_5) AS denom_5',
                    'SUM(TransactionDetail.denom_10) AS denom_10',
                    'SUM(TransactionDetail.denom_20) AS denom_20',
                    'SUM(TransactionDetail.denom_50) AS denom_50',
                    'SUM(TransactionDetail.denom_100) AS denom_100',
                    'regions.name',

                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime DESC',
                'group' =>   'FileProccessingDetail.station, HOUR(`TransactionDetail`.`trans_datetime`), DAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    )
                )
            ),
            'paginate4' => array(
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.total_transaction',
                    'SUM(TransactionDetail.denom_1) AS denom_1',
                    'SUM(TransactionDetail.denom_2) AS denom_2',
                    'SUM(TransactionDetail.denom_5) AS denom_5',
                    'SUM(TransactionDetail.denom_10) AS denom_10',
                    'SUM(TransactionDetail.denom_20) AS denom_20',
                    'SUM(TransactionDetail.denom_50) AS denom_50',
                    'SUM(TransactionDetail.denom_100) AS denom_100',
                    'regions.name',

                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime DESC',
                'group' =>   'FileProccessingDetail.station,DAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    )
                )
            ),
            'chart' => array(
                'transactionPieChart' => array(
                    'conditions' => $conditions,
                    'fields' => array('trans_type_id', 'transaction_count'),
                    'order' => 'trans_type_id ASC',
                    'group' => 'trans_type_id',
                    'contain' => array('FileProccessingDetail' => array('id', 'file_date'))
                ),
                'transactionCategoryChart' => array(
                    'conditions' => $conditions,
                    'fields' => array('transaction_category', 'transaction_count'),
                    'order' => 'transaction_category ASC',
                    'group' => 'transaction_category',
                    'contain' => array('FileProccessingDetail' => array('id', 'file_date'))
                ),
                'transactionClientPie' => array(
                    'conditions' => $conditions,
                    'fields' => array('FileProccessingDetail.branch_id', 'transaction_count'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail' => array('id', 'file_date', 'branch_id'))
                ),
                'chartData' => array(
                    'conditions' => $conditions,
                    'fields' => array('transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
                ),
                'chartDataSum' => array(
                    'conditions' => $conditions,
                    'fields' => array('transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
                ),
                'chartData2' => array(
                    'conditions' => $conditions,
                    'fields' => array(
                        'TransactionDetail.id',
                        'TransactionDetail.trans_datetime', 'FileProccessingDetail.file_date'
                    ),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'contain' => array('FileProccessingDetail')
                ),
                'deposite' => array(
                    // 'conditions' => $conditions,
                    // 'fields' => array('SUM(TransactionDetail.total_amount) AS total_amount',  'FileProccessingDetail.file_date', 'transaction_count'),
                    // 'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    // FileProccessingDetail.file_date',
                    // 'order' => 'FileProccessingDetail.file_date DESC',
                    // 'contain' => array('FileProccessingDetail')

                    'fields' => array(
                        'SUM(TransactionDetail.total_amount) AS total_amount', 'FileProccessingDetail.file_date',
                     ),
                     'joins' => array(
                         array(
                             'table' => 'company_branches',
                             'alias' => 'CompanyBranches',
                             'type' => 'LEFT ',
                             'conditions' => array(
                                 'CompanyBranches.id = FileProccessingDetail.branch_id'
                             )
                         ),
                         array(
                             'table' => 'regions',
                             'alias' => 'regions',
                             'type' => 'INNER',
                             'conditions' => array(
                                 'regions.id = CompanyBranches.regiones'
                             )
                         ),
                     ),
                     'conditions' => $conditions,
                     'order' => 'TransactionDetail.trans_datetime DESC',
                     'contain' => array(
                         'FileProccessingDetail' => array(
                             'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                             'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                             'Branch' => array('id', 'name','regiones'),
                         ),
                         'TransactionType' => array('id', 'text'),
                     )

                ),
                'DepositeChart' => array(
                    'conditions' => $conditions,
                    'fields' => array('transaction_count', 'FileProccessingDetail.file_date'),
                    'order' => 'FileProccessingDetail.file_date DESC',
                    'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,
                    FileProccessingDetail.file_date',
                    'contain' => array('FileProccessingDetail')
                ),
            )
        );
        return $responseArr;
    }
    function getTransactionDetails2($conditions = array())
    {
        $responseArr = array(
            'paginate2' => array(
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime ASC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                    'TransactionType' => array('id', 'text'),
                )
            ),
            'paginate3' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => 'TransactionDetail.trans_datetime ASC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    )
                )
            ),

        );
        return $responseArr;
    }
    function getTransactionallData($conditions = array())
    {
        $responseArr = array(
            'daily' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    // 'COUNT(TransactionDetail.trans_datetime) as count',
                    'COUNT(DAYOFWEEK(`TransactionDetail`.`trans_datetime`)) as day'
                ),
                'group' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`),CompanyBranches.regiones',
                // 'order' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'branchDaily' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.branch_id',
                'order' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'stationDaily' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'userDaily' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station,TransactionDetail.teller_name',
                'order' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'userdetails_daily' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'DAYOFWEEK(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'weekly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'DAY(`TransactionDetail`.`trans_datetime`)',
                'order' => 'DAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'branchWeekly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.branch_id',
                'order' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'stationWeekly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'userWeekly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station,TransactionDetail.teller_name',
                'order' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'userdetails_weekly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'WEEKDAY(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'monthly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'MONTH(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'TransactionDetail.trans_datetime DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'yearly' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'YEAR(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'TransactionDetail.trans_datetime DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'byHour' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`),CompanyBranches.regiones',
                'order' => 'HOUR(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'hourBranch' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.branch_id',
                'order' => 'HOUR(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'hourStation' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'HOUR(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'hourUser' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station,TransactionDetail.teller_name',
                'order' => 'HOUR(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
            'userdetails_hour' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id'
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'fields' => array(
                    'TransactionDetail.trans_datetime',
                    'TransactionDetail.teller_name',
                    'COUNT(TransactionDetail.trans_datetime) as count'
                ),
                'group' => 'HOUR(`TransactionDetail`.`trans_datetime`),FileProccessingDetail.station',
                'order' => 'HOUR(`TransactionDetail`.`trans_datetime`)',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'branch_id', 'company_id', 'file_date', 'station'),
                        'Company' => array('id', 'first_name', 'email', 'last_name', 'station_count'),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            ),
        );
        return $responseArr;
    }

    function getActivityReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'ActivityReport.trans_datetime DESC',
                'group' => 'ActivityReport.id',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'Manager' => array('id', 'name')
                )
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getAutomix($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                    ),
                    'Branch' => array('id', 'name'),
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array('id', 'file_date', 'company_id', 'branch_id'))
            )
        );
        return $responseArr;
    }

    function getBillActivity($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate_new' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'BillsActivityReport.file_processing_detail_id',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date', 'station'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('name', 'regiones'),
                    ),
                    'ActivityReport' => array(
                        'fields' => array('id')
                    ),
                    'ActivityReport' => array(
                        'fields' => array('id')
                    ),
                    'BillType',
                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                )
            ),
            'chart_new' => array(
                'conditions' => $conditions,
                'order' => 'BillsActivityReport.bill_type_id',
                'group' => 'BillsActivityReport.bill_type_id',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'ActivityReport' => array(
                        'fields' => array('id')
                    ),
                    'BillType'
                )
            )
        );

        return $responseArr;
    }
    function getInventory($conditions = array())
    {
        $responseArr = array(
            'paginate_new' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'Inventory.file_processing_detail_id',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date', 'station'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('name'),
                    ),
                    'ActivityReport' => array(
                        'fields' => array('id')
                    ),
                    'BillType'
                )
            ),
            'chart_new' => array(
                'conditions' => $conditions,
                // 'order' => 'BillsActivityReport.bill_type_id', 
                // 'group' => 'BillsActivityReport.bill_type_id',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'ActivityReport' => array(
                        'fields' => array('id')
                    ),
                    'BillType'
                )
            )
        );

        return $responseArr;
    }


    function getBillAdjustment($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'BillAdjustment.adjustment_value DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'station', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name', 'regiones'),
                ), 'Manager')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array('fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date')))
            )
        );
        return $responseArr;
    }

    function getBillCount($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'Manager')
            ),
            'chart' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array('fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')))
            )
        );
        return $responseArr;
    }
    function getHeatMap($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                // 'order' => 'FileProccessingDetail.file_date DESC', 
                'order' => 'BillCount.id DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'Manager')
            ),
            'chart' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                // 'order' => 'FileProccessingDetail.file_date DESC',
                'order' => 'BillCount.id DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array('fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')))
            )
        );
        return $responseArr;
    }
    function getBillHistory($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'BillHistory.trans_datetime DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'Manager' => array('id', 'name'), 'BillType')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'trans_datetime'),
                'order' => 'trans_datetime DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getCoinInventory($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getTellerTransaction($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'CurrentTellerTransactions.trans_datetime DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'CurrentTellerTransactions.trans_datetime DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getHistoryReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'Manager')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getManagerSetup($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'Manager')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getNetCashUsageActivity($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ), 'ActivityReport')
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getFileProcessing($conditions = array(), $fields = NULL, $isGetPaginateData = true)
    {
        $responseArr = array(
            'paginate' => array(

                'contain' => array(
                    'TransactionDetail' => array(
                        'fields' => array(
                            'id',
                            'no_of_deposit',
                            'no_of_withdrawal',
                            'total_cash_withdrawal',
                            'total_cash_requested',
                            'no_of_report',
                            'no_of_transaction',
                            'total_cash_deposit'
                        )

                    ),
                    'ErrorDetail' => array(
                        'fields' =>
                        'no_of_errors'
                    ),
                    'AutomixSetting' => array('no_of_automix'),
                    'BillsActivityReport' => array('no_of_billactivity'),
                    'BillAdjustment' => array('no_of_billadjustment'),
                    'BillCount' => array('no_of_billcount'),
                    'BillHistory' => array('no_of_billhistory'),
                    'CoinInventory' => array('no_of_coininventory'),
                    'CurrentTellerTransactions' => array('no_of_currTellerTrans'),
                    'HistoryReport' => array('no_of_historyReport'),
                    'ManagerSetup' => array('no_of_mgrSetup'),
                    'NetCashUsageActivityReport' => array('no_of_netCashUsage'),
                    'SideActivityReport' => array('no_of_sideActivity'),
                    'TellerActivityReport' => array('no_of_tellerActivity'),
                    'ValutBuy' => array('no_of_vaultBuy'),
                    'Branch' => array('id', 'name'),
                    'TellerSetup' => array('no_of_teller_setup')
                ),
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => array('station, branch_id', 'file_date')
            ),
            'chartData' => array(
                'conditions' => $conditions,
                'fields' => array('file_count', 'file_date'),
                'order' => 'file_date asc',
                'group' => 'branch_id, file_date',
                'contain' => false
            ),
            'filesPieData' => array(
                'fields' => isset($fields) ? $fields : '',
                'conditions' => $conditions,
                'contain' => false,
                'order' => 'FileProccessingDetail.file_date ASC',
                'group' => 'branch_id'
            )
        );
        if (empty($isGetPaginateData)) {
            unset($responseArr['paginate']);
        }
        if (!empty($isGetPaginateData) && ($isGetPaginateData != true)) {
            unset($responseArr['filesPieData']);
            unset($responseArr['chartData']);
        }
        return $responseArr;
    }

    function getTellerActivity($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }

        $responseArr = array(
            'paginate' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                //'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id',  // Commented on 101121 need to check again
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'station', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'ActivityReport'
                ),
                'order' => 'TellerActivityReport.id DESC',
            ),
            'chart' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date ASC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            ),
            'deposit' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                'fields' => array('sum', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date ASC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                    )

                )
            )
        );
        return $responseArr;
    }

    function getTellerSetup($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getValutBuy($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name')
                    ),
                    'Manager' => array('id', 'first_name')
                )
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getSideLog($conditions = array())
    {
        $joins = array(
            array(
                'table' => 'side_log',
                'alias' => 'side',
                'type' => '  ',
                'conditions' => array(
                    'side.file_processing_detail_id = FileProccessingDetail.id',
                )
            )
        );
        $joins2 = array(
            array(
                'table' => 'manager_log',
                'alias' => 'manager',
                'type' => ' ',
                'conditions' => array(
                    'manager.file_processing_detail_id = FileProccessingDetail.id',
                )
            )
        );

        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'station', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'paginate3' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'station', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'paginate2' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail2.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'station', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                )),
                'UNION' => 'UNION',


                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'station', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),


            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('SideLog.id', 'SideLog.teller_id', 'SideLog.logon_datetime', 'SideLog.logoff_datetime'),
                'order' => 'FileProccessingDetail.file_date DESC',

                'contain' => array(
                    'FileProccessingDetail' => array('fields' => array('station'))
                )
            )
        );
        return $responseArr;
    }

    function getSideLogNew($conditions = array(), $limit, $order)
    {
        /*$responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name'),
                            'Dealer' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name')
                    ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array(
                'FileProccessingDetail' => array('fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'))
                )
            )
        );
    return $responseArr;*/


        $joins = array(
            array(
                'table' => 'side_log',
                'alias' => 'side',
                'type' => '  ',
                'conditions' => array(
                    'side.file_processing_detail_id = FileProccessingDetail.id',
                )
            )
        );
        $joins2 = array(
            array(
                'table' => 'manager_log',
                'alias' => 'manager',
                'type' => ' ',
                'conditions' => array(
                    'manager.file_processing_detail_id = FileProccessingDetail.id',
                )
            )
        );

        $content = ClassRegistry::init('SideLog');
        $content2 = ClassRegistry::init('ManagerLog');

        $dbo = $this->getDataSource();
        $unionQuery = $dbo->buildStatement(
            array(
                'fields' => array('side.teller_id AS name , FileProccessingDetail.station AS station , side.logon_datetime AS logon_datetime , side.logoff_datetime AS logoff_datetime, side.side_type AS side_type ,  "Teller"'),
                'table' => $dbo->fullTableName('file_processing_detail'),
                'alias' => 'FileProccessingDetail',
                'limit' => null,
                'offset' => null,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => null,
                'group' => null
            ),
            $content
        );

        $unionQuery .= ' UNION ';
        $dbo = $this->getDataSource();
        $unionQuery .= $dbo->buildStatement(
            array(
                'fields' => array(' manager.manager_id  AS name ,   FileProccessingDetail.station  AS station , manager.logon_datetime  AS logon_datetime  ,  manager.logoff_datetime  AS logoff_datetime ,  "" AS side_type, "Manager" '),
                'table' => $dbo->fullTableName('file_processing_detail'),
                'alias' => 'FileProccessingDetail',
                'limit' => null,
                'offset' => null,
                'joins' => $joins2,
                'conditions' => $conditions,
                'order' => null,
                'group' => null
            ),
            $content2
        );
        // $unionQuery .=' ORDER BY side_type asc';
        if (isset($order)) {
            $unionQuery .= $order;
        }
        if (isset($limit)) {
            $unionQuery .= $limit;
        }

        $result = $this->query($unionQuery);
        debug($result);
        return $result;
    }




    function getInventoryManagement($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'Inventory.id',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array(
                    'total_denome_1', 'total_denome_2', 'total_denome_5',
                    'total_denome_10', 'total_denome_20', 'total_denome_50',
                    'total_denome_100', 'FileProccessingDetail.file_date'
                ),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                    )
                )
            )
        );
        return $responseArr;
    }

    function getTotalVaultBuy($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                    )
                )
            )
        );
        return $responseArr;
    }

    function getTransactionVaultBuy($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getTellerUser($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'TransactionVaultBuy.teller_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }

    function getTellerUserReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date', 'station'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date', 'station')
                    )
                )
            )
        );
        return $responseArr;
    }

    function getErrorWarning($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate' => array(
                'contain' => array(
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Dealer' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Branch' => array(
                        'Station',
                        'fields' => array('id', 'name')
                    ),
                    'FileProccessingDetail' => array(

                        'fields' => array('id', 'station')
                    )
                ),
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions
            ),
            'paginate2' => array(

                'joins' => array(
                    array(

                        'table' => 'error_types',
                        'alias' => 'ErrorTypes',
                        'type' => 'RIGHT',
                        'fields' => array('severity'),
                        'conditions' => array(
                            'ErrorTypes.id = ErrorDetail.error_type_id'
                        )
                    ),
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'contain' => array(
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Dealer' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Branch' => array(
                        'Station',
                        'fields' => array('id', 'name')
                    ),
                    'ErrorDetail' => array(
                        'fields' => array('id', 'error_type_id')
                    )
                ),
                'conditions' => $conditions,
                'order' => 'ErrorTypes.id ASC',
                'group' =>  'ErrorTypes.severity',
            ),
            'chart' => array(

                'joins' => array(
                    array(

                        'table' => 'error_types',
                        'alias' => 'ErrorTypes',
                        'type' => 'RIGHT',
                        'fields' => array('severity'),
                        'conditions' => array(
                            'ErrorTypes.id = ErrorDetail.error_type_id'
                        )
                    ),
                ),
                'contain' => array(
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Dealer' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'ErrorDetail' => array(
                        'fields' => array('id', 'error_type_id')
                    )
                ),
                'conditions' => $conditions,
                'order' => 'ErrorTypes.id ASC',
                'group' =>  'ErrorTypes.severity',
            )
        );
        return $responseArr;
    }
    function getErrorWarningDetails($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'contain' => array(
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Dealer' => array(
                        'fields' => array('id', 'first_name', 'last_name', 'name')
                    ),
                    'Branch' => array(
                        'Station',
                        'fields' => array('id', 'name')
                    )
                ),
                'conditions' => $conditions
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_type_id', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );

        return $responseArr;
    }
    function getErrorWarningReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'ActivityReport'
                )
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            ),
            'deposit' => array(
                'conditions' => $conditions,
                'fields' => array('sum', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail2' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }


    function getInventoryByTeller($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id')
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array(
                    'total_denom_1', 'total_denom_2', 'total_denom_5',
                    'total_denom_10', 'total_denom_20', 'total_denom_50',
                    'total_denom_100', 'FileProccessingDetail.file_date'
                ),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id,FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id'),
                ))
            )
        );
        return $responseArr;
    }

    function getDashboard($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'contain' => array(
                    'Company' => array('id', 'first_name', 'last_name'),
                    'Branch' => array('id', 'name')
                ),
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'station, branch_id, file_date'
            )
        );
        return $responseArr;
    }

    function getIssueReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'Ticket.ticket_date DESC',
                'group' => 'Ticket.id',
                'contain' => array(
                    'ErrorDetail' => array(
                        'FileProccessingDetail' => array(
                            'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station'),
                            'Company' => array(
                                'fields' => array('id', 'first_name', 'email', 'last_name'),
                                'Dealer' => array('id', 'first_name', 'email', 'last_name')
                            ),
                            'Branch' => array('id', 'name'),
                        )
                    ),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                    ),
                    'Dealer' => array('id', 'first_name', 'email', 'last_name'),
                    'Branch' => array('id', 'name')
                )
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('ticket_count', 'ticket_date'),
                'order' => 'ticket_date DESC', 'group' => 'DATE_FORMAT(ticket_date,"%Y-%m-%d")',
                'contain' => false
            )
        );
        return $responseArr;
    }

    function getClientIssue($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'Ticket.ticket_count DESC',
                'group' => 'Ticket.station,Ticket.id',
                'contain' => array(
                    'ErrorDetail' => array(
                        'FileProccessingDetail' => array(
                            'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station'),
                            'Company' => array(
                                'fields' => array('id', 'first_name', 'email', 'last_name'),
                                'Dealer' => array('id', 'first_name', 'email', 'last_name')
                            ),
                            'Branch' => array('id', 'name'),
                        )
                    ),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name'),
                    ),
                    'Dealer' => array('id', 'first_name', 'email', 'last_name'),
                    'Branch' => array('id', 'name')
                )
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('ticket_count', 'ticket_date'),
                'order' => 'ticket_date DESC', 'group' => 'company_id',
                'contain' => false
            )
        );
        return $responseArr;
    }

    function getindexFileProcessing($conditions = array(), $fields = NULL)
    {

        $responseArr = array(
            'paginate' => array(
                'contain' => array('Company', 'Branch'),
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
            ),
            'chartData' => array(
                'conditions' => $conditions,
                'fields' => array('file_count', 'file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'station, branch_id, file_date',
                'contain' => false
            ),
            'filesPieData' => array(
                'fields' => $fields,
                'conditions' => $conditions,
                'contain' => false,
                'order' => 'FileProccessingDetail.created_date ASC',
                'group' => 'FileProccessingDetail.company_id'
            )
        );
        return $responseArr;
    }

    function getErrorReport($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name'),
                            'Dealer' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                    'Ticket' => array(
                        'fields' => array('id', 'ticket_date', 'dealer_id', 'acknowledge_date', 'is_acknowledge', 'status', 'created', 'updated'),
                        'Dealer' => array('id', 'first_name', 'email', 'last_name')
                    )
                )
            )
        );
        return $responseArr;
    }

    function getUnidentifyMessage($conditions = array())
    {
        $responseArr = array(
            'paginate' => array(
                'conditions' => $conditions,
                'order' => 'Message.datetime DESC',
                'group' => 'Message.id',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'file_date', 'transaction_number'),
                    'Company' => array(
                        'fields' => array('id', 'first_name', 'email', 'last_name')
                    ),
                    'Branch' => array('id', 'name'),
                ))
            ),
            'chart' => array(
                'conditions' => $conditions,
                'fields' => array('error_count', 'FileProccessingDetail.file_date'),
                'order' => 'FileProccessingDetail.file_date DESC',
                'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id, FileProccessingDetail.file_date',
                'contain' => array('FileProccessingDetail' => array(
                    'fields' => array('id', 'filename', 'company_id', 'branch_id', 'station', 'file_date')
                ))
            )
        );
        return $responseArr;
    }
    function getUserReport($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                //'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id',  // Commented on 101121 need to check again
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'station', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name'),
                    ),
                ),
                'order' => 'UserReport.id DESC',
            )
        );
        return $responseArr;
    }
    function getSpecialnotesreconciliation($conditions = array(), $regionsId = null)
    {
        if (!empty($regionsId)) {
            $conditions['regions.id'] = $regionsId;
        }
        $responseArr = array(
            'paginate' => array(
                'joins' => array(
                    array(
                        'table' => 'company_branches',
                        'alias' => 'CompanyBranches',
                        'type' => 'LEFT ',
                        'conditions' => array(
                            'CompanyBranches.id = FileProccessingDetail.branch_id',
                        )
                    ),
                    array(
                        'table' => 'regions',
                        'alias' => 'regions',
                        'type' => 'INNER',
                        'conditions' => array(
                            'regions.id = CompanyBranches.regiones'
                        )
                    )
                ),
                'conditions' => $conditions,
                //'group' => 'FileProccessingDetail.station, FileProccessingDetail.branch_id',  // Commented on 101121 need to check again
                'contain' => array(
                    'FileProccessingDetail' => array(
                        'fields' => array('id', 'filename', 'station', 'company_id', 'branch_id', 'file_date'),
                        'Company' => array(
                            'fields' => array('id', 'first_name', 'email', 'last_name')
                        ),
                        'Branch' => array('id', 'name', 'regiones'),
                    ),
                )
            )
        );
        return $responseArr;
    }
}
