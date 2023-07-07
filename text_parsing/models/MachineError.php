<?php

class MachineError
{
    public static $instance = null;
    protected $fields = array("id",
        "file_processing_detail_id",
        "company_id",
        "branch_id",
    );
    public $id;
    public $file_processing_detail_id;
    public $company_id;
    public $branch_id;
    public static $machine_errors;
    public static $machine_names;
    public static $dealer_errors;

    public function __construct()
    {
        $this->file_processing_detail_id = ProcessingFile::instance()->id;
        $this->company_id = ProcessingFile::instance()->company_id;
        $this->branch_id = ProcessingFile::instance()->branch_id;
    }
    
    public static function instance()
    {
        if (empty(self::$instance)) {
            self::init();
        }

        return self::$instance;
    }

    public static function init()
    {
        $stmt = Db::getInstance()->prepare("SELECT MT.id,MT.name,ME.id as m_id,ME.error_message FROM `machine_types` as MT join machine_errors as ME on (MT.id = ME.machine_type_id) where MT.status = 'active'");
        $stmt->execute();
        $machineErrors = $machineNameErrors = $dealerError = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $machineErrors[$row['m_id']] = $machineNameErrors[$row['name']][$row['m_id']] = $row['error_message'];
        }
        
        $stmt = Db::getInstance()->prepare("SELECT * FROM `dealer_machine_errors`");
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dealerError[$row['dealer_id']][$row['machine_error_id']] = $row['machine_error_id'];
        }
        
        self::$machine_errors = $machineErrors;
        self::$machine_names = $machineNameErrors;
        self::$dealer_errors = $dealerError;
    }
}
