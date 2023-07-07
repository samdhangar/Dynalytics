<?php

class ErrorTicket extends Model
{
    public static $instance = null;
    protected $fields = array(
        "id",
        "company_id",
        "branch_id",
        "station",
        "ticket_config_id",
        "file_processing_detail_id",
        "error_detail_id",
        "ticket_date",
        "error_warning_status",
        "error",
        "created",
        "dealer_id",
        "machine_error_id"
    );
    public $id;
    public $company_id;
    public $branch_id;
    public $station;
    public $ticket_config_id;
    public $file_processing_detail_id;
    public $error_detail_id;
    public $ticket_date;
    public $error_warning_status;
    public $error;
    public $created;
    public $dealer_id;
    public $machine_error_id;

    public function __construct()
    {
        $this->company_id = ProcessingFile::instance()->company_id;
        $this->branch_id = ProcessingFile::instance()->branch_id;
        $this->station = ProcessingFile::instance()->station;
        $this->error_warning_status = "error";
        $this->ticket_date = date('Y-m-d H:i:s');
        $this->created = date('Y-m-d H:i:s');
    }

    public function toArray()
    {
        return parent::toArray();
    }

    public static function create($data = array())
    {
        $machineError = MachineError::$machine_errors;
        $dealerError = MachineError::$dealer_errors;
        $companyId = ProcessingFile::instance()->company_id;
        $dealerId = ProcessingFile::instance()->get_dealer_id($companyId);
        $dealerError = isset($dealerError[$dealerId]) ? $dealerError[$dealerId] : array();
        $machineError = array_diff_key($machineError, $dealerError);
        if(isset($data['message'])){
        if (self::instance()->isTicket($data['message'], $machineError)) {
            $ticketData = self::instance()->toArray();
            $ticketData['station'] = ProcessingFile::instance()->station;
            $branchId = $ticketData['branch_id'];
            $station = $ticketData['station'];
            $machineErrorId = array_search($data['message'], $machineError);
            $ticketData['machine_error_id'] = $machineErrorId;
            $query = "SELECT `id` FROM `ticket_configs` where branch_id = $branchId and dealer_id = $dealerId and "
                . "company_id = $companyId and station = $station and machine_error_id = $machineErrorId limit 1";
            $stmt = Db::getInstance()->prepare($query);
            $stmt->execute();
            $ticketConfigDetail = $stmt->fetch(PDO::FETCH_ASSOC);
            $ticketData['ticket_config_id'] = isset($ticketConfigDetail['id']) ? $ticketConfigDetail['id'] : 0;
            $ticketData['file_processing_detail_id'] = ProcessingFile::instance()->id;
            $ticketData['error'] = $data['message'];
            $ticketData['dealer_id'] = $dealerId;
            $file_processing_detail_id= $ticketData['file_processing_detail_id'];
            $error = $ticketData['error'];
            $query = "SELECT `id`, `error_detail_id` FROM `error_tickets` where branch_id = $branchId and  company_id = $companyId and station = $station and error = '$error' limit 1";
            $stmt = Db::getInstance()->prepare($query);
            $stmt->execute();
            $ticketDetail = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($ticketDetail['id'])) {
                $errorDetailId = ErrorDetail::save($data['message']);
            } else {
                $errorDetailId = $ticketDetail['error_detail_id'];
            }
            $ticketData['error_detail_id'] =$errorDetailId;
                if (!DB::insert('error_tickets', $ticketData, $id)) {
                     echo "Error: Error Ticket not Saved\n";
                } else {
                      if (TicketConfig::checkForGenerateTicket($ticketData)) {
                        Ticket::instance()->create($ticketData); 
                    }
                }
        }
        }else{
            $ticketData = self::instance()->toArray();
            $ticketData['station'] = ProcessingFile::instance()->station;
            $branchId = $ticketData['branch_id'];
            $station = $ticketData['station'];
            $machineErrorId = array_search($data['error_message'], $machineError);
            $ticketData['machine_error_id'] = $machineErrorId;    
            $ticketData['ticket_config_id'] =0;
            $ticketData['file_processing_detail_id'] = ProcessingFile::instance()->id;
            $ticketData['error'] = $data['error_message'];
            $ticketData['dealer_id'] = $dealerId;
            $file_processing_detail_id= $ticketData['file_processing_detail_id'];
            $error = $ticketData['error'];
            $error_details_id=$data['error_type_id'];
            $query = "SELECT `id`, `error_detail_id` FROM `error_tickets` where branch_id = $branchId and  company_id = $companyId and station = $station and error_detail_id = '$error_details_id' limit 1";
            $stmt = Db::getInstance()->prepare($query);
            $stmt->execute();
            $ticketDetail = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($ticketDetail['id'])) { 
                 $q= "SELECT `id`  FROM `error_detail` where  error_type_id = '$error_details_id' and  file_processing_detail_id = $file_processing_detail_id  limit 1";
            $s = Db::getInstance()->prepare($q);
            $s->execute();
            $ticketDetail = $s->fetch(PDO::FETCH_ASSOC); 
             $errorDetailId = $ticketDetail['id'];
            } else {
                $errorDetailId = $ticketDetail['error_detail_id'];
            }
            $ticketData['error_detail_id'] =$errorDetailId;
              
                if (!DB::insert('error_tickets', $ticketData, $id)) {
                     echo "Error: Error Ticket not Saved\n";
                } else { 
                        Ticket::instance()->create($ticketData); 
                }
        }
    }

    public static function countNoOfMachineError($ticketConfig = array())
    {
        if (!empty($ticketConfig)) {
            $ticketConfigId = $ticketConfig['id'];
            $todayDate = date('Y-m-d');
            $sqlQuery = "select count from `error_tickets` where `ticket_config_id`=$ticketConfigId and date(`ticket_date`)='$todayDate' group by `ticket_config_id`";
            $stmt = Db::getInstance()->prepare($sqlQuery);
            $stmt->execute();
            $totalErrors = $stmt->fetch(PDO::FETCH_ASSOC);
            if (($totalErrors % $ticketConfig['exceed_limit']) == 0) {
                return true;
            }
        }
        return false;
    }

    private function isTicket($message, $availableErrors = array())
    {
        if (in_array($message, $availableErrors)) {
            return true;
        }
        return false;
    }
}
