<?php

Hook::sub("subscription", function($line){

	if($bt = BillActivityReport::isBegin($line))
	{
		BillActivityReport::instance()->bill_type_id = BillType::getId($bt);
		BillActivityReport::instance()->_counter = 0;
		return BillActivityReport::instance()->bill_type_fulltext = $bt;
	}

	if(!BillActivityReport::has())
		return;

	if(BillActivityReport::isEnd($line))
	{
		BillActivityReport::instance()->completed();
		return BillActivityReport::destroy();
	}

	if(BillActivityReport::instance()->messageOpen)
	{
		return BillActivityReport::instance()->message .= $line;
	}
	$matches = array();
	if(preg_match_all("/([152]0{0,2})[ ]+([0-9]+)[ ]+[0-9\.]+/", strtolower($line), $matches))
	{
		BillActivityReport::instance()->_counter = 0;
		$key = array_shift($matches[1]);
		$value = array_shift($matches[2]);
		if(in_array($key, array("100", "50", "20", "10", "5", "2", "1")))
		{
			$k = "denom_$key";
			BillActivityReport::instance()->$k = intVal($value);
			BillActivityReport::countTable($key , $value);
		}

		if($key == "coin")
			BillActivityReport::instance()->coin = $value;
	}
	else if(preg_match_all("/(total( [A-Za-z]+)*)[ ]+([0-9]*.[0-9]{2})/", strtolower($line), $matches))
	{
		BillActivityReport::instance()->_counter = 0;
		$key = array_shift($matches[1]);
		$value = array_shift($matches[3]);
		BillActivityReport::instance()->total = $value;
	}
	else if(preg_match_all("/(cass. [0-9](u|l)?)(.*? )([0-9]+)[ ]+([0-9]*.[0-9]{2})/", strtolower($line), $matches))
	{
		BillActivityReport::instance()->_counter = 0;
		$key = array_shift($matches[1]);
		$num = array_shift($matches[4]);
		$val = array_shift($matches[5]);

		$k = str_replace(". ", "_", $key);

		BillActivityReport::instance()->$k = intVal($num);

		$k .= "_total";
		BillActivityReport::instance()->{$k} = floatVal($val);
	}
	else if(preg_match_all("/ *\*\*\* (.*?) \*\*\* */", $line, $matches))
	{
		BillActivityReport::instance()->message = array_shift($matches[1])."\n";
		BillActivityReport::instance()->messageOpen = true;
		return;
	}
	else
	{
		BillActivityReport::instance()->_counter++;
		if(BillActivityReport::instance()->_counter > 1)
		{
			BillActivityReport::instance()->completed();
			return BillActivityReport::destroy();
		}
	}
});

class BillActivityReport extends Model
{
	public static $instance = null;
	protected $fields = array("id",
							"file_processing_detail_id",
							"station",
							"activity_report_id",
							"bill_type_id",
							"denom_100",
							"denom_50",
							"denom_20",
							"denom_10",
							"denom_5",
							"denom_2",
							"denom_1",
							"coin",
							"total",
							"cass_1",
							"cass_2",
							"cass_3",
							"cass_4u",
							"cass_4l",
							"cass_5",
							"cass_1_total",
							"cass_2_total",
							"cass_3_total",
							"cass_4u_total",
							"cass_4l_total",
							"cass_5_total",
							"message",
							"created_date",
							"created_by",
							"updated_date",
							"updated_by");


	public $id;
	public $file_processing_detail_id;
	public $station;
	public $activity_report_id;
	public $bill_type_id;
	public $denom_100;
	public $denom_50;
	public $denom_20;
	public $denom_10;
	public $denom_5;
	public $denom_2;
	public $denom_1;
	public $coin;
	public $total;
	public $cass_1;
	public $cass_2;
	public $cass_3;
	public $cass_4u;
	public $cass_4l;
	public $cass_5;
	public $cass_1_total;
	public $cass_2_total;
	public $cass_3_total;
	public $cass_4u_total;
	public $cass_4l_total;
	public $cass_5_total;
	public $message;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $messageOpen = false;

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->station = Station::has() ? Station::instance()->id : null;
		$this->activity_report_id = ActivityReport::has() ? ActivityReport::instance()->id : null;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		DB::insert("bills_activity_report", $this->toArray(), $this->id);
	}
	public static function countTable($denom , $count)
	{
		$insertData['fiel_id']= ProcessingFile::instance()->id;
		$insertData['branch_id']= ProcessingFile::instance()->branch_id;
		$insertData['date']= ProcessingFile::instance()->file_date;
		$insertData['station']= Station::instance()->id;
		$insertData['denom']=$denom;
		$insertData['count']=$count;
		
		DB::insert('total_bill_activity', $insertData, $insertData['id']);
		print_r($insertData);

	}
	public static function isBegin($subject)
	{
		$matches = array();
		if(!preg_match_all("/(( )+((Collection Box Bills)|(Exposed Bills)|(Excess Bills)|(Dispensable Bills)|(Overflow Cassette Bills)|(Reject Cassette Bills)|(Op Cassette Bills))( )+\n)/", $subject, $matches))
		{
			return;
		}

		return array_shift($matches[3]);
	}

	public static function isEnd($subject)
	{
		$matches = array();
		return preg_match_all("/(=)+/", $subject, $matches);
	}
}