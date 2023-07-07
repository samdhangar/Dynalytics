<?php

class ProcessingFile
{
    public static $instance = null;
    protected $fields = array("id",
        "filename",
        "company_id",
        "branch_id",
        "station",
        "file_date",
        "row_number",
        "processing_counter",
        "processing_starttime",
        "processing_endtime",
        "transaction_number",
        "adjustment_number",
        "activity_report_number",
        "total_amount_tendered",
        "total_deposit",
        "created_date",
        "created_by",
        "updated_date",
        "updated_by");
    public $id;
    public $filename;
    public $company_id;
    public $station;
    public $file_date;
    public $row_number = 0;
    public $processing_counter = 0;
    public $processing_starttime;
    public $processing_endtime;
    public $transaction_number = 0;
    public $adjustment_number = 0;
    public $activity_report_number = 0;
    public $total_amount_tendered = 0;
    public $total_deposit = 0;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;
    public $dealer_id;

    public function __construct()
    {
        $this->processing_starttime = date("Y-m-d H:i:s");
    }

    public static function getCountForFile($name)
    {
        $stmt = Db::getInstance()->prepare("SELECT row_number FROM `file_processing_detail` WHERE `filename`=:file limit 1");
        $stmt->bindParam(":file", $name, PDO::PARAM_STR);
        $stmt->execute();

        $a = $stmt->fetch(PDO::FETCH_ASSOC);

        return $a["row_number"];
    }

    public static function getNoOfFiles($companyId = 0, $branchId = 0, $stationName = '')
    {
        if (!empty($companyId) && !empty($branchId) && !empty($stationName)) {
            $stmt = Db::getInstance()->prepare("SELECT count(*) as total_files FROM `file_processing_detail` WHERE `company_id`=:company_id and `branch_id`=:branch_id and `station`=:station");
            $stmt->bindParam(":company_id", $companyId, PDO::PARAM_INT);
            $stmt->bindParam(":branch_id", $branchId, PDO::PARAM_INT);
            $stmt->bindParam(":station", $stationName, PDO::PARAM_STR);
            $stmt->execute();
            $a = $stmt->fetch(PDO::FETCH_ASSOC);
            return $a["total_files"];
        }
        return 0;
    }

    public static function getFile($name, $branchId, $companyId)
    {
        $stmt = Db::getInstance()->prepare("SELECT * FROM `file_processing_detail` WHERE `filename`=:file and `branch_id`=:branch_id and `company_id`=:company_id LIMIT 1");
        $stmt->bindParam(":file", $name, PDO::PARAM_STR);
        $stmt->bindParam(":branch_id", $branchId, PDO::PARAM_INT);
        $stmt->bindParam(":company_id", $companyId, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "ProcessingFile");
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::init();
        }

        return self::$instance;
    }

    public static function init($name = false, $branchId = null, $companyId = null)
    {
        if (!empty($name) && !empty($branchId) && !empty($companyId))
            self::$instance = self::getFile($name, $branchId, $companyId);

        if (empty(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    public function save()
    {
        $return = 0;
        if (empty($this->id)) {
            $this->created_date = date('Y-m-d H:i:s');
            $return = DB::insert("file_processing_detail", $this->toArray(), $this->id);
        } else {
            $return = DB::update("file_processing_detail", $this->toArray(), "id");
        }
        /**
         * Insert Station with first file date and last file date
         */
//        $this->dealer_id = Self::get_dealer_id($this->company_id);
        $stationId = Station::getFileStationId($this->company_id, $this->branch_id, $this->station);
        $staion = Station::init();
        $staion->file_processed_count = $this->getNoOfFiles($this->company_id, $this->branch_id, $this->station);
        if (!empty($stationId['id'])) {
            $staion->id = $stationId['id'];
            $staion->first_file_process_id = $stationId['first_file_process_id'];
			//UPDATE IF FIRST FILE IS LARGE THEN CURENT
			if($stationId['first_file_date'] > $this->file_date){
				$staion->first_file_date = $this->file_date;				
			}else{
				$staion->first_file_date = $stationId['first_file_date'];				
			}
        } else {
            $staion->id = 0;
            $staion->first_file_process_id = $this->id;
            $staion->first_file_date = $this->file_date;
        }

        $staion->company_id = $this->company_id;
        $staion->branch_id = $this->branch_id;
        $staion->name = $this->station;
        $staion->last_file_process_id = $this->id;
		//UPDATE ONLY IF NEW FILE DATE IS LARGE THAN OLD ONE
		if(!empty($stationId['id']) && $stationId['last_file_date'] < $this->file_date){
			$staion->last_file_date = $this->file_date;			
		}else{
			$staion->last_file_date = $stationId['last_file_date'];
		}
        $staion->save();
        return $return;
    }

    public static function get_dealer_id($companyId = null)
    {
        if (!empty($companyId)) {
            $stmt = Db::getInstance()->prepare("SELECT dealer_id FROM `users` where `users`.id =");
            $stmt = Db::getInstance()->prepare("SELECT dealer_id FROM `users` where `users`.id =:user_id LIMIT 1");
            $stmt->bindParam(":user_id", $companyId, PDO::PARAM_INT);
            $stmt->execute();
            $a = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return isset($a['dealer_id']) ? $a['dealer_id'] : 0;
    }

    public function inc_transaction_number()
    {
        $this->transaction_number++;
    }

    public function inc_adjustment_number()
    {
        $this->adjustment_number++;
    }

    public function inc_activity_report_number()
    {
        $this->activity_report_number++;
    }

    public function add_total_amount_tendered($val)
    {
        $this->total_amount_tendered += $val;
    }

    public function add_total_deposit($val)
    {
        $this->total_deposit += $val;
    }

    public function toArray()
    {
        $array = array();
        foreach ($this->fields AS $k) {
            $array[$k] = $this->$k;
        }

        return $array;
    }
}
