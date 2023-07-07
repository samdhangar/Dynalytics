<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class DatabaseGrowth extends AppModel
{

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'db_growth';

    function getDatabaseTablesList()
    {
//        $tableLists = array('bill_adjustments', 'email_templates', 'file_processing_detail');
        $tableLists = $this->find('list',array(
            'fields' => 'id,table_name'
        ));
        $tableLists = array_unique($tableLists);
        return $tableLists;
    }

    function getLineChartData($xAxisDates = array(),$tickInterval = 1,$conditions = array())
    {
        $tableList = $this->getDatabaseTablesList();
        $tempArr = array();
        $tempXaxis = $xAxisDates;
        $chartData = $this->find('all', array(
                'fields' => array('UNIX_TIMESTAMP(check_date)*1000 as check_date','sum(size) as size'),
                'conditions' => $conditions,
                'group' => array('check_date')
            ));
        $xAxisData = $yAxisData = array(); 
        foreach($chartData as $k=>$data){
            $xAxisData[] = $data[0]['check_date'];
            $yAxisData[] = array($data[0]['check_date'],$data[0]['size']);
        }

        return array('temp' => $yAxisData,'xAxisDates' => $yAxisData);

        //FOLLOWING CODE IS USE FOR TABLE BASE LINE CHART, IF YOU ENABLE THIS THEN YOU HAVE TO REMOVE ABOVE CODE AND CHANG FUNCATION NAME FROM LINE CHART TO MULTILINE CHART ON database_growth.ctp FILE
        $tableList = $this->getDatabaseTablesList();
        $tempArr = array();
        $tempXaxis = $xAxisDates;
        foreach ($tableList as $tableName) {
            $conditions['DatabaseGrowth.table_name'] = $tableName;
            $chartData = $this->find('all', array(
                'conditions' => $conditions
            ));
            $chartData = Hash::extract($chartData, '{n}.DatabaseGrowth');
            sort($chartData);
            $tempArr[$tableName] = $temp = array();
            foreach ($chartData as $key => $value) {
                $date = date('Y-m-d', strtotime($value['check_date']));
                if(isset($temp[$date])){
                    $value['size'] = $value['size'] + $temp[$date][1];
                }
                $temp[$date] = array((strtotime($value['check_date']) * 1000), $value['size']);
            }
            $sendArr = array();
            foreach ($tempXaxis as $key => $date):
                if (array_key_exists($date, $temp)) {
                    $sendArr[$key] = $temp[$date];
                } else {
                    $sendArr[$key] = array((strtotime($date) * 1000 ), 0);
                }
                $xAxisDates[$key] = array(strtotime($date));
            endforeach;
            $tempArr[$tableName] = json_encode($sendArr, JSON_NUMERIC_CHECK);
        }
        $sendTempArr = array();
        foreach($tempArr as $tableName => $chartdata){
            $sendTempArr[] = array(
                'name' => $tableName,
                'data' => $chartdata,
                'pointInterval' => $tickInterval
            );
        }
        return array('temp' => $sendTempArr,'xAxisDates' => $xAxisDates);
    }

    function getPieChartData($conditions = array())
    {
        
    }
    
}
