<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/Hook.php");
require_once(__DIR__ . "/models/DB.php");
require_once(__DIR__ . "/models/Model.php");
require_once(__DIR__ . "/models/Client.php");
require_once(__DIR__ . "/models/CompanyBranch.php");
require_once(__DIR__ . "/models/_ErrorLog.php");
   

class Worker
{
    protected $debug = false;
    protected $branch_info = 0;
    protected $path = "";
    public $machineError = array();

    public function __construct()
    {
     
//        $this->debug = true;
        $this->path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    public function run(CompanyBranch $dir = null)
    {
           $db = DB::getInstance();
         $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        //CHECK FOR COMPANY BRANCH AND IF FTP EXIST THEN PROCESS FILE
        $trans = $db->prepare("DELETE FROM activity_report; 
            DELETE FROM messages;
            DELETE FROM bills_activity_report;
            DELETE FROM history_report;
            DELETE FROM verification_required;
            DELETE FROM side_log;
            DELETE FROM coin_inventory;
        DELETE FROM net_cash_usage_activity_report;
        DELETE FROM side_activity_report;
        DELETE FROM teller_transaction_total_vault_buys;
        DELETE FROM teller_setup;

        DELETE FROM  teller_user_report;
            DELETE FROM vault_buys;
            DELETE FROM manager_user_report;
            DELETE FROM manager_setup;
        DELETE FROM networking_settings;
        DELETE FROM bills_count;
        DELETE FROM inventory_by_hours;
        DELETE FROM inventory;

        DELETE FROM  error_detail;
            DELETE FROM bills_history;
            DELETE FROM manager_log;
            DELETE FROM automix_setting;
        DELETE FROM teller_activity_report;
        
        DELETE FROM transaction_details;
        DELETE FROM file_processing_detail;");

        $trans->execute();
       

     /*   while ($row = $trans->fetch(PDO::FETCH_ASSOC)) {
                if(isset($row['file_processing_detail_id'])){
              

                }
                } */ 
    }
 
   
/**/
    

    
    
}

function runApp($dont_use_client_logic = false)
{
    if ($dont_use_client_logic) {
     
        $worker = new Worker();
        $worker->run();
    } else {
        $companyBranches = CompanyBranch::getAll();
        foreach (CompanyBranch::getAll() AS $value) {
            $worker = new Worker();
            //print_r($value);
            $worker->run($value);
        }
        if(empty($companyBranches)){
            echo "\n\n#########################################\n\n\n";
            echo "\tNo Any Active Branch Exists\n\n\n";
            echo "#########################################\n\n\n";
        }
    }
}


runApp($do_not_use_client_logic);
