<?php

class TicketConfig
{
    public static $instance = null;
    protected $fields = array("id",
        "company_id",
        "branch_id",
        "station",
        "machine_error_id",
        "dealer_id",
        "exceed_limit"
    );
    public $id;
    public $company_id;
    public $branch_id;
    public $station;
    public $machine_error_id;
    public $dealer_id;
    public $exceed_limit;
    public static $dealer_ticket_configs;

    public function __construct()
    {
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
    
    public static function checkForGenerateTicket($inputData = array())
    {
        $return = false;
        if (!empty($inputData['machine_error_id']) && !empty($inputData['station']) && !empty($inputData['company_id']) && !empty($inputData['branch_id'])) {
            $ticketConfig = TicketConfig::getTicketConfig($inputData);
            if(!empty($ticketConfig)){
                $return = false;
                if(ErrorTicket::countNoOfMachineError($ticketConfig)){
                    $return = true;
                }
            }else{
                $return = true;
            }
        }
        return $return;
    }

    public static function getTicketConfig($inputData = array())
    {
        $ticketConfigs = array();
        if (!empty($inputData['machine_error_id']) && !empty($inputData['station']) && !empty($inputData['company_id']) && !empty($inputData['branch_id'])) {
            $mcErrId = $inputData['machine_error_id'];
            $station = $inputData['station'];
            $compId = $inputData['company_id'];
            $branchId = $inputData['branch_id'];
            $dbQuery = "select * from `ticket_configs` where machine_error_id=$mcErrId and company_id=$compId" .
                " and branch_id=$branchId and station=$station limit 1 ";
            $stmt = Db::getInstance()->prepare($dbQuery);
            $stmt->execute();
            $ticketConfigs = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ticketConfigs;
        }
        return $ticketConfigs;
    }

    public static function init()
    {
        $stmt = Db::getInstance()->prepare("SELECT * FROM `ticket_configs`");
        $stmt->execute();
        $machineErrors = $machineNameErrors = $dealerError = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $machineErrors[$row['m_id']] = $machineNameErrors[$row['name']][$row['m_id']] = $row['error_message'];
        }

        $stmt = Db::getInstance()->prepare("SELECT * FROM `dealer_machine_errors`");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dealerError[$row['dealer_id']][$row['machine_error_id']] = $row['machine_error_id'];
        }

        self::$machine_errors = $machineErrors;
        self::$machine_names = $machineNameErrors;
        self::$dealer_errors = $dealerError;
    }
}
