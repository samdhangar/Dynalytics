<?php

class Ticket extends Model
{
    public static $instance = null;
    protected $fields = array(
        "id",
        "company_id",
        "branch_id",
        "error_detail_id",
        "ticket_date",
        "error_warning_status",
        "error",
        "dealer_id",
        "created_by",
        "created"
    );
    public $id;
    public $company_id;
    public $branch_id;
    public $dealer_id;
    public $error_detail_id;
    public $ticket_date;
    public $error_warning_status;
    public $error;
    public $created_by;
    public $created;

    public function __construct()
    {
        $this->company_id = ProcessingFile::instance()->company_id;
        $this->branch_id = ProcessingFile::instance()->branch_id;
        $this->created_by = 1;
        $this->error_warning_status = "error";
        $this->ticket_date = date('Y-m-d H:i:s');
        $this->created = date('Y-m-d H:i:s');
    }

    public function toArray()
    {
        return parent::toArray();
    }

    public static function create($inputData = array())
    {
        
        $ticketData = self::instance()->toArray();
        /*if(!empty($inputData) && !empty($inputData['station'])
            && !empty($inputData['file_processing_detail_id'])
            && !empty($inputData['error'])
            && !empty($inputData['dealer_id'])
            && !empty($inputData['error_detail_id'])
            ){*/
                if(!empty($inputData)){
            $ticketData['station'] = $inputData['station'];
            $ticketData['file_processing_detail_id'] = $inputData['file_processing_detail_id'];
            $ticketData['error'] = $inputData['error'];
            /* $ticketData['created_by'] = 2;*/
            $ticketData['dealer_id'] = $dealerId = $inputData['dealer_id'];
            $ticketData['error_detail_id'] = $inputData['error_detail_id'];
            Echo " Under ticket if condition";
            $companyId = ProcessingFile::instance()->company_id;
            $branchId = $ticketData['branch_id'];
            $station = $ticketData['station'];
            $error = $ticketData['error'];

           
//            $query = "SELECT `id`, `error_detail_id` FROM `tickets` where branch_id = $branchId and "
//                . "company_id = $companyId and station = $station and error = '$error' limit 1";
//            $stmt = Db::getInstance()->prepare($query);
//            $stmt->execute();
//            $ticketDetail = $stmt->fetch(PDO::FETCH_ASSOC);
//            if (!empty($ticketDetail['id'])) {
//                $ticketData['id'] = $ticketDetail['id'];
//                DB::update("tickets", $ticketData, "id");
//            } else {
                if (!DB::insert('tickets', $ticketData, $id)) {
                    print_r($ticketData);
                    echo "Error: Ticket not Saved\n";
                } else {
                    /**
                     * sent mail to dealer and company
                     */
                    echo "Ticket Data";
                     print_r($ticketData);
                    $inputData = self::instance()->get_mail_data($companyId, $dealerId, $ticketData);
                    SendEmail::send_ticket_mail($inputData, 'dealer');
                    SendEmail::send_ticket_mail($inputData);
                     echo "Error: Ticket  Saved\n";
                }
//            }
        }
        
        /*$machineError = MachineError::$machine_errors;
        $dealerError = MachineError::$dealer_errors;
        $companyId = ProcessingFile::instance()->company_id;
        $dealerId = ProcessingFile::instance()->get_dealer_id($companyId);
        $dealerError = isset($dealerError[$dealerId]) ? $dealerError[$dealerId] : array();
        $machineError = array_diff_key($machineError, $dealerError);
        if (self::instance()->isTicket($data['message'], $machineError)) {
            $ticketData = self::instance()->toArray();
            $ticketData['station'] = ProcessingFile::instance()->station;
            $ticketData['file_processing_detail_id'] = ProcessingFile::instance()->id;
            $ticketData['error'] = $data['message'];
            $ticketData['dealer_id'] = $dealerId;
            $branchId = $ticketData['branch_id'];
            $station = $ticketData['station'];
            $error = $ticketData['error'];
            $query = "SELECT `id`, `error_detail_id` FROM `tickets` where branch_id = $branchId and "
                . "company_id = $companyId and station = $station and error = '$error' limit 1";
            $stmt = Db::getInstance()->prepare($query);
            $stmt->execute();
            $ticketDetail = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($ticketDetail['id'])) {
                $errorDetailId = ErrorDetail::save($data['message']);
            }else{
                $errorDetailId = $ticketDetail['error_detail_id'];
            }
            $ticketData['error_detail_id'] = $errorDetailId;
            if (!empty($ticketDetail['id'])) {
                $ticketData['id'] = $ticketDetail['id'];
                DB::update("tickets", $ticketData, "id");
            } else {
                if (!DB::insert('tickets', $ticketData, $id)) {
                    print_r($ticketData);
                    echo "Error: Ticket not Saved\n";
                } else {
                    /**
                     * sent mail to dealer and company
                     */
                    /*$inputData = self::instance()->get_mail_data($companyId, $dealerId, $ticketData);
                    SendEmail::send_ticket_mail($inputData, 'dealer');
                    SendEmail::send_ticket_mail($inputData);
                }
            }
        }*/
    }

    private function isTicket($message, $availableErrors = array())
    {

//        $availableErrors = array(
//            'Coin Link Error',
//            'TCR Link Error',
//            'RBG-100 Link Error',
//            'Cass. 4 Fatal Error',
//            'MTYPE114 Link Error',
//        );
        if (in_array($message, $availableErrors)) {
            return true;
        }
        return false;
    }

    public function get_mail_data($companyId = null, $dealerId = null, $ticketData = array())
    {
        $stmt = Db::getInstance()->prepare("SELECT `id`,`first_name`, `last_name`, `email`, `phone_no`  FROM `users` where id =:user_id");
        $stmt->bindParam(":user_id", $companyId, PDO::PARAM_STR);
        $stmt->execute();
        $companyDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = Db::getInstance()->prepare("SELECT `id`,`first_name`, `last_name`, `email`, `phone_no`  FROM `users` where id =:user_id");
        $stmt->bindParam(":user_id", $dealerId, PDO::PARAM_STR);
        $stmt->execute();
        $dealerDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        $branchId = ProcessingFile::instance()->branch_id;
        $stmt = Db::getInstance()->prepare("SELECT `id`,`name`  FROM `company_branches` where id =:branch_id limit 1");
        $stmt->bindParam(":branch_id", $branchId, PDO::PARAM_INT);
        $stmt->execute();
        $branchDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        $branchDetail['station'] = ProcessingFile::instance()->station;
        $inputData = array(
            'Dealer' => $dealerDetail,
            'Company' => $companyDetail,
            'Branch' => $branchDetail,
            'Ticket' => $ticketData
        );
        return $inputData;
    }
}
