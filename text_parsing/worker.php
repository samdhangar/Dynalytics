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

/* Other files */
require_once(__DIR__ . "/models/Counter.php");
require_once(__DIR__ . "/models/EntryCounter.php");
require_once(__DIR__ . "/models/Container.php");
require_once(__DIR__ . "/models/TransactionDetails/DepositCanceled.php");
require_once(__DIR__ . "/models/TransactionDetails/InventoryLoadCancelled.php");
require_once(__DIR__ . "/models/TransactionCategory.php");
require_once(__DIR__ . "/models/ProcessingFile.php");  // OK
require_once(__DIR__ . "/models/Station.php");  // OK
require_once(__DIR__ . "/models/AvailableMessages.php");
// require_once(__DIR__."/models/ManagerSetup.php");
// require_once(__DIR__."/models/TellerSetup.php");
require_once(__DIR__ . "/models/Msg.php");

require_once(__DIR__ . "/models/TransactionType.php");  // OK
require_once(__DIR__ . "/models/TransactionDetails.php");
require_once(__DIR__ . "/models/BillType.php");  // OK
require_once(__DIR__ . "/models/CurrentTellerTransaction.php");  // OK
require_once(__DIR__ . "/models/Manager.php");  // OK
require_once(__DIR__ . "/models/Side.php"); // OK

require_once(__DIR__ . "/models/VaultBuy.php");
require_once(__DIR__ . "/models/VBActivityReport.php");
require_once(__DIR__ . "/models/HistoryReport.php");
require_once(__DIR__ . "/models/SideActivityReport.php");
require_once(__DIR__ . "/models/Message.php");
require_once(__DIR__ . "/models/UserReport.php");
require_once(__DIR__ . "/models/TellerActivity.php");
require_once(__DIR__ . "/models/NetCasheUsage.php");
require_once(__DIR__ . "/models/Inventory.php");
require_once(__DIR__ . "/models/CoinInventory.php");
require_once(__DIR__ . "/models/AutomixSettings.php");
require_once(__DIR__ . "/models/VerificationRequired.php");

require_once(__DIR__ . "/models/ActivityReport.php");  // OK
require_once(__DIR__ . "/models/ConfigModeAccess.php");  // OK
require_once(__DIR__ . "/models/BillActivityReport.php");  // OK
require_once(__DIR__ . "/models/BillHistory.php");  // OK

require_once(__DIR__ . "/models/TransactionDetails/InventoryRemoval.php");
require_once(__DIR__ . "/models/TransactionDetails/OnlineDeposit.php");
require_once(__DIR__ . "/models/TransactionDetails/AutoAuditBillCount.php");
require_once(__DIR__ . "/models/TransactionDetails/ReplenishedCassettes.php");
require_once(__DIR__ . "/models/TransactionDetails/ReconciledRejects.php");
require_once(__DIR__ . "/models/TransactionDetails/CassetteAdjustment.php");

require_once(__DIR__ . "/models/Setup/Teller.php");
require_once(__DIR__ . "/models/Setup/Manager.php");
require_once(__DIR__ . "/models/Setup/NetworkCtrl.php");

require_once(__DIR__ . "/models/Ticket.php");
require_once(__DIR__ . "/models/MachineError.php");
require_once(__DIR__ . "/models/SendEmail.php");
require_once(__DIR__ . "/PHPMailer/PHPMailerAutoload.php");
require_once(__DIR__ . "/models/ErrorDetail.php");
require_once(__DIR__ . "/models/ErrorTicket.php");
require_once(__DIR__ . "/models/TicketConfig.php");

require_once(__DIR__ . "/models/DynAssign_Session_Status.php");
require_once(__DIR__ . "/models/SecuritySetting.php");
require_once(__DIR__ . "/models/InventrySetting.php");

class Worker
{
    protected $debug = false;
    protected $branch_info = 0;
    protected $path = "";
    public $machineError = array();
    public $machine_id;
    public function __construct()
    {
     
//        $this->debug = true;
        $this->path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    public function run(CompanyBranch $dir = null)
    {
        $this->dir = is_null($dir->ftp_path) ? "storage" : $dir->ftp_path;
        $this->dir = str_replace('//', DIRECTORY_SEPARATOR, $this->path . $this->dir);
        $this->dir = str_replace('\\', '/', $this->dir);
        
        //CHECK FOR COMPANY BRANCH AND IF FTP EXIST THEN PROCESS FILE

        $date= date("Y-m-d H:i:s");
        $filecontent = file_get_contents("record.php");
        $myfile = fopen("record.php", "w") or die("Unable to open file!");
        $txt = $filecontent."\n".$date;
        fwrite($myfile, $txt);
        fclose($myfile);

        if (!is_dir($this->dir)) {
            return false;
        }
        
        //SET COMPANY ID
        $this->branch_info = $dir;
        $this->loadLibrary();

        $this->init();
        $this->out("\nTry do something ...", 1);
        if ($this->checkQueue()) {
            $this->processQueue();
        }
    }

    protected function loadLibrary()
    {
        
    }

    protected function init()
    {
        MachineError::init();
        SendEmail::init();
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
        //header("Content-Type: text/plain");
    }

    protected function checkQueue()
    {
        $this->queueDir = "".$this->dir."/queue";
        $this->queueDir = str_replace('//', DIRECTORY_SEPARATOR, $this->queueDir);
        $this->errorDir = "".$this->dir."/error_occured";
        $this->errorDir = str_replace('//', DIRECTORY_SEPARATOR, $this->errorDir);
        $this->warehouseDir = "".$this->dir."/warehouse";
        $this->warehouseDir = str_replace('//', DIRECTORY_SEPARATOR, $this->warehouseDir);
        $this->processingDir = "".$this->dir."/processing";
        $this->processingDir = str_replace('//', DIRECTORY_SEPARATOR, $this->processingDir);

        if (!is_dir($this->queueDir)) {
            mkdir($this->queueDir, 755);
        }
        if (!is_dir($this->errorDir)) {
            mkdir($this->errorDir, 755);
        }
        if (!is_dir($this->warehouseDir)) {
            mkdir($this->warehouseDir, 755);
        }
        if (!is_dir($this->processingDir)) {
            mkdir($this->processingDir, 755);
        }

        $this->files=glob($this->queueDir."/*.txt*");

        //$this->files = glob("{".$this->queueDir."}/*.txt");

        if (empty($this->files))
            return false;

        $count = count($this->files);

        $this->out("Found $count files in queue!", 1);
        
        return true;
    }

    protected function processQueue()
    {
        $data=[];
        while (count($this->files)) {
             
            $data[]=$this->processFile(array_shift($this->files));
        } 
        $db = DB::getInstance();      
        if (!empty($data)) {            
           foreach ($data as $key => $value) {
            
                $file_processing_detail_id = $value->id;
                $filename = $value->filename;
                $company_id = $value->company_id;
                $station = $value->station;
                $file_date = $value->file_date;
                $row_number = $value->row_number;
                $processing_counter = $value->processing_counter;
                $processing_starttime = $value->processing_starttime;
                $processing_endtime = $value->processing_endtime;
                $transaction_number = $value->transaction_number;
                $adjustment_number = $value->adjustment_number;
                $activity_report_number = $value->activity_report_number;
                $total_amount_tendered = $value->total_amount_tendered;
                $total_deposit = $value->total_deposit;
                $created_date = $value->created_date;
                $created_by = $value->created_by;
                $updated_date = $value->updated_date;
                $updated_by = $value->updated_by;
                $dealer_id = $value->dealer_id;
                $branch_id = $value->branch_id;

                $insertQuery = $db->prepare("INSERT INTO daily_file_processing_detail(file_processing_detail_id, filename, company_id, station, file_date, row_number, processing_counter, processing_starttime, processing_endtime, transaction_number, adjustment_number, activity_report_number, total_amount_tendered, total_deposit, created_date, created_by, branch_id) values($file_processing_detail_id, '$filename', $company_id, '$station', '$file_date', $row_number, $processing_counter, '$processing_starttime', '$processing_endtime', $transaction_number, $adjustment_number, $activity_report_number, $total_amount_tendered, $total_deposit, '$created_date', '$created_by', $branch_id)");  
                try {
                    $insertQuery->execute();
                }
                catch(Exception $e) {
                  echo ' Exception Message: ' .$e->getMessage();
                }
           }
        }
    }

    protected function idle()
    {
     
        die("\n\nDone!\n");
        // sleep(3);
    }

    public function fatalErrorShutdownHandler()
    {
        $last_error = error_get_last();

        if (!empty($this->fileObject))
            $this->fileObject = null;

        if (empty($this->file))
            return;

        if ($this->debug)
            rename($this->file, str_replace("{$this->processingDir}/", "{$this->queueDir}/", $this->file));
        else
            rename($this->file, str_replace("{$this->processingDir}/", "{$this->errorDir}/", $this->file));

        DB::rollBackTransaction();

        $err = new _ErrorLog();
        $err->error_message = implode(" ", array($last_error['message'], "in file",
            $last_error['file'], "at line",
            $last_error['line']));
        $err->file = $this->file;
        $err->created_date = date("Y-m-d H:i:s");
        $err->save();

        if ($last_error['type'] === E_ERROR)
            $this->error(implode("\n", array("Error Message: " . $last_error['message'], "File: " . $last_error['file'], "Line: " . $last_error['line'])));
    }

    protected function out($msg, $lvl = 4)
    {
        echo "$msg\n";
    }

    protected function error($msg, $lvl = 4)
    {
        echo "Error: $msg\n";
    }

    protected function processFile($file)
    {
        if (!file_exists($file))
            $this->error("File $file doesn't exist!");

        $this->file = $file;

        $this->out("####################");
        $this->out("# Processing file {$this->file}");
 
        $matches = array();
	   /*$regex = "/([A-Za-z0-9]+)[\(\_\[](([0-9]+)([\-\_][A-Za-z0-9-||A-Za-z0-9 ]+){1,2})[\)\_\]]([0-9]+).txt$/";*/

        $regex = "/([A-Za-z0-9]+)[\(\_\[](([A-Za-z0-9_]+)([\-\_][A-Za-z0-9-||A-Za-z0-9_ ]+){1,2})[\)\_\]]([0-9]+).txt$/";

	    if (!preg_match_all($regex, $this->file, $matches)) {
            $this->error("Wrong file name {$this->file}");
            return;
        //            throw new Exception("{$this->file}", 1);
        }
       // echo "<pre>";
       // print_r($matches);
        $db = DB::getInstance();
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $filename = array_shift($matches[0]);
        $company = array_shift($matches[1]);
        $station = array_shift($matches[3]);
      
        $machine = array_shift($matches[4]);
        $date = array_shift($matches[5]);
        $machine_name=str_replace($station."-","",array_shift($matches[2]));
        // is file ready
        //echo $machine_name;
        $sql_machine = $db->prepare("SELECT id FROM machine_types where name='$machine_name'");
        $sql_machine->execute();
        $machine_details = $sql_machine->fetch();
        if(empty($machine_details)){
            $sql = $db->prepare("INSERT INTO  machine_types (name , status , created) VALUES('$machine_name','active',now())");
            $sql->execute(); 
            $sql_machine = $db->prepare("SELECT id FROM machine_types where name='$machine_name'");
            $sql_machine->execute();
            $machine_details = $sql_machine->fetch();
        }
        $GLOBALS['machine_id']=$machine_details['id'];
        if (!is_file_ready($this->file))
            return;

        if (!DB::beginTransaction())
            return $this->error("PDO error! can not begin transaction!");

        // Try find this file in db by name
        // $currentLine = ProcessingFile::getCountForFile($filename);
        // Processing file move to processing folder
        if (!rename($this->file, str_replace("{$this->queueDir}/", "{$this->processingDir}/", $this->file)))
            $this->error("Unable to move file {$this->file}");

        $this->file = str_replace("{$this->queueDir}/", "{$this->processingDir}/", $this->file);
 
        // Open file
        $this->fileObject = new SplFileObject($this->file);
        

        $processing_file = ProcessingFile::init($filename,$this->branch_info->id,$this->branch_info->company_id);
        //echo "<pre>";
 
         print_r($processing_file);
          
        // print_r($processing_file->file_date);

        if (empty($processing_file->row_number)) {
            $processing_file->filename = $filename;
            $processing_file->company_id = $this->branch_info->company_id;
            $processing_file->branch_id = $this->branch_info->id;
            $processing_file->station = $station;
            $processing_file->file_date = DateTime::createFromFormat("mdY", $date) ? DateTime::createFromFormat("mdY", $date)->format("Y-m-d") : "1999-01-01";
            $processing_file->save();
        } else {
             $this->fileObject->seek($processing_file->row_number);
        }
         

        $inventorybyhoursdate = DateTime::createFromFormat("mdY", $date) ? DateTime::createFromFormat("mdY", $date)->format("Y-m-d") : "1999-01-01";
        $processing_file->processing_counter++;
        /*echo "<pre>";
         print_r($inventorybyhoursdate);
         echo "<pre>";
         print_r($processing_file);*/
          
        // Reset counter

        Counter::init();
        Counter::set($processing_file->row_number);

        EntryCounter::init($processing_file->id);
        $f=0;

        while (!$this->fileObject->eof()) {

            $s = preg_replace("/[^A-Za-z0-9\.\,\(\)\-\n\=\*\#\/ \:]*/", '', $this->fileObject->current());
             
            if($f==1){
                $date=str_replace("Date","",$s);
                $date=str_replace(" ","",$date);
                $transaction_date= date("Y-m-d",strtotime($date));
            if($processing_file->file_date!=$transaction_date)
            {    
                $sql_new = $db->prepare("SELECT * FROM company_branches where id=$processing_file->branch_id");
                $sql_new->execute();
                $barnch_details = $sql_new->fetch();
                $email=$barnch_details['email'];
                $file_path=BASE_URL."".$barnch_details['ftp_path']."processing";
                $subject="Unable to parse file -".$processing_file->filename;
                $body="Unable to parse file -".$processing_file->filename."  file path =". $file_path;
                SendEmail::init();
                SendEmail::send_new($email, $subject, $body , $file_path , $processing_file->filename);
                $sql = $db->prepare("DELETE FROM file_processing_detail where id=$processing_file->id");
                $sql->execute();
                break;
            }

            $f=0;
            }
             if (strpos($s, 'Transaction #') !== false) {
                  $f=1;
                }

           
            //echo  $s;
            Hook::trigger("subscription", $s);
            Counter::inc();
            $this->fileObject->next();

        }
 
        //fclose($file_handler);
        $this->fileObject = null;

         $difference = Counter::get() - $processing_file->row_number;
 

        $this->out("#");
        $this->out("# Status: " . (( $difference > 0) ? $difference . " lines processed!" : "Not modified!"));
        $this->out("#");
        $this->out("####################\n");

        if (Counter::get()) {
            $processing_file->row_number = Counter::get();
            $processing_file->processing_endtime = date("Y-m-d H:i:s");
            $processing_file->save();
        }

        $this->out("=============================");

        print(" ====================== Processed Files =====================");
        print_r($processing_file);

      /*  $this->check_logout_time($this->branch_info->company_id,$this->branch_info->id,$station,$inventorybyhoursdate);  */
        EntryCounter::reset();

        DB::commitTransaction();
        //functin to add inventory by hour
        if($difference > 0)
        {
            $this->inventorybyhours($this->branch_info->company_id,$this->branch_info->id,$station,$inventorybyhoursdate);
        }
               
        if ($this->debug) {
            // Processed file move to warehouse folder
            if (!rename($this->file, str_replace("{$this->processingDir}/", "{$this->queueDir}/", $this->file)))
                $this->error("Unable to move file {$this->file}");
        }
        else {
            // Processed file move to warehouse folder
            if (!rename($this->file, str_replace("{$this->processingDir}/", "{$this->warehouseDir}/", $this->file)))
                $this->error("Unable to move file {$this->file}");
        }

        $this->file = null;
        return $processing_file;
    }

  public function check_logout_time($company,$branch,$station,$invent_trans_date)
    {
        $db = DB::getInstance();
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $s = $db->prepare(" select id , station , file_date  from file_processing_detail where id = '".$company."'   order by id desc limit 1 ");
        $s->execute();
        $arr = $s->fetch();
       //echo " select id , station , file_date  from file_processing_detail where company_id = '".$company."' and branch_id = '".$branch."' and station = '".$station."' order by id desc limit 1 <br>";
        $file_procesing_id=$arr['id']; 
        $login_time_new=date("Y-m-d 08:00:00",strtotime($arr['file_date']));
        //echo "New time =".$login_time_new;
        $data=trim("select id , station , file_date  from file_processing_detail where company_id = '".$company."' and branch_id = '".$branch."' and station = '".$station."' order by id desc limit 1 ");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         $q1->execute();
        //  code for side log 
       
        $update_logon_side=$db->prepare("update side_log set logon_datetime='".$login_time_new."' where file_processing_detail_id = '".$file_procesing_id."' AND logon_datetime < '".$login_time_new."' ");
        $update_logon_side->execute();
        $data=trim("update side_log set logon_datetime='".$login_time_new."' where file_processing_detail_id = '".$file_procesing_id."' AND logon_datetime < '".$login_time_new."' ");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         
         $q1->execute();
        $update_logon_manager=$db->prepare("update manager_log set logon_datetime='".$login_time_new."' where file_processing_detail_id = '".$file_procesing_id."' AND logon_datetime < '".$login_time_new."' ");
        $update_logon_manager->execute(); 
$data=trim("update manager_log set logon_datetime='".$login_time_new."' where file_processing_detail_id = '".$file_procesing_id."' AND logon_datetime < '".$login_time_new."' ");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         $q1->execute();
        $query_logout=$db->prepare("select * from side_log   WHERE (
logoff_datetime < logon_datetime
OR logoff_datetime IS NULL
)  and file_processing_detail_id = '".$file_procesing_id."' ");
       // echo "select * from side_log   WHERE logoff_datetime < logon_datetime  and file_processing_detail_id = '".$file_procesing_id."' ";
        $query_logout->execute(); 
        $data=trim("select * from side_log   WHERE logoff_datetime < logon_datetime  and file_processing_detail_id = '".$file_procesing_id."' ");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         $q1->execute();
        while($row = $query_logout->fetch(PDO::FETCH_ASSOC)){  
            $next_login_time=$db->prepare("select logon_datetime from side_log   WHERE file_processing_detail_id =  '".$row['file_processing_detail_id']."'  and teller_id = ".$row['teller_id']."  and id > ".$row['id']." limit 1");
           // echo "select logon_datetime from side_log   WHERE file_processing_detail_id =  ".$row['file_processing_detail_id']."  and teller_id = ".$row['teller_id']."  and id > ".$row['id']." limit 1";
            $data=trim("select logon_datetime from side_log   WHERE file_processing_detail_id =  '".$row['file_processing_detail_id']."'  and teller_id = ".$row['teller_id']."  and id > ".$row['id']." limit 1");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         $q1->execute();
            $next_login_time->execute();
            $next_login_time = $next_login_time->fetch();
            if(empty($next_login_time)){
                $logoff_time=date("Y-m-d 20:03:01",strtotime($arr['file_date']));
            }else{
                $logoff_time=$next_login_time['logon_datetime'];
            }
             $update_logoff=$db->prepare("update side_log set logoff_datetime='".$logoff_time."' where id=".$row['id']." ");
             //echo "update side_log set logoff_datetime='".$logoff_time."' where id=".$row['id']." ";
              if($update_logoff->execute()){
                echo "Query Execute";
              }else{
                echo "query Not Executed";
              }
              $data=trim("update side_log set logoff_datetime='".$logoff_time."' where id=".$row['id']." ");
        $q1=$db->prepare('INSERT INTO query (query) VALUES ("'.$data.'")');
         $q1->execute();
        } 


         //  code for manager log  
        $query_logout=$db->prepare("select * from manager_log   WHERE (
logoff_datetime < logon_datetime
OR logoff_datetime IS NULL
)  and file_processing_detail_id = '".$file_procesing_id."' ");
        $query_logout->execute(); 
        while($row = $query_logout->fetch(PDO::FETCH_ASSOC)){  
            $next_login_time=$db->prepare("select logon_datetime from manager_log   WHERE file_processing_detail_id =  '".$row['file_processing_detail_id']."'  and id > '".$row['id']."' limit 1");
            $next_login_time->execute();
            $next_login_time = $next_login_time->fetch();
            if(empty($next_login_time)){
                $logoff_time=date("Y-m-d 20:03:01",strtotime($login_time_new));
            }else{
                $logoff_time=$next_login_time['logon_datetime'];
            }
             $update_logoff=$db->prepare("update manager_log set logoff_datetime='".$logoff_time."' where id='".$row['id']."' ");
             $update_logoff->execute();  
             //echo "<br>";
        } 
        
       
    }




    //FUNCTION TO ADD DATA IN INVENTORY BY HOURS22/02/17
    function inventorybyhours($company,$branch,$station,$invent_trans_date)
    {
       
        $db = DB::getInstance();
         $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        //1. get data
        $s = $db->prepare("select * from inventory where file_processing_detail_id =  (select id from file_processing_detail where company_id = '".$company."' and branch_id = '".$branch."' and station = '".$station."' order by id desc limit 1,1) and station = '".$station."' and activity_report_id is not null order by id desc limit 1");
        
        $s->execute();
        $arr = $s->fetch();
        
        $company_id = $company;
        $branch_id = $branch;
        $station = $station;
        $trans_date = date('Y-m-d',strtotime($invent_trans_date));
        $trans_hr = '';
        if(isset($arr['file_processing_detail_id'])){
            $file_processing_detail_id = $arr['file_processing_detail_id'];
            $company_id = $company;
            $branch_id = $branch;
            $station = $station;
            $trans_date = date('Y-m-d',strtotime($invent_trans_date));
            $trans_hr = '';
            $start_hours = '07:00:00';
            $end_hours = '08:00:00';
            $denom_100 = $arr['denom_100'];
            $denom_50 = $arr['denom_50'];
            $denom_20 = $arr['denom_20'];
            $denom_10 = $arr['denom_10'];
            $denom_5 = $arr['denom_5'];
            $denom_2 = $arr['denom_2'];
            $denom_1 = $arr['denom_1'];
            $coin = $arr['coin'];
            $no_depsites = '';
            $no_depsites = '';
            $no_withdrawl = '';
            $total_amount_calculated = $arr['total'];
            $total_pieces_calculated = '';
            $created_date = date('Y-m-d H:m:s');
            $created_by = 'sys';
            $updated_by = 'sys';
            //echo $file_processing_detail_id;
            //2.insert data for 8.am
          
            $i = $db->prepare("INSERT INTO `inventory_by_hours`(file_processing_detail_id, company_id, branch_id, station, trans_date, trans_hr, start_hours, end_hours, denom_100, denom_50, denom_20, denom_10, denom_5, denom_2, denom_1, coin, no_depsites, no_withdrawl, total_amount_calculated, total_pieces_calculated, created_date, created_by,  updated_by)
                VALUES(:file_processing_detail_id,:company_id,:branch_id,:station,:trans_date,:trans_hr,:start_hours,:end_hours,:denom_100,:denom_50,:denom_20,:denom_10,:denom_5,:denom_2,:denom_1,:coin,:no_depsites,:no_withdrawl,:total_amount_calculated,:total_pieces_calculated,:created_date,:created_by,:updated_by)");
                    
            $i->bindParam(':file_processing_detail_id', $file_processing_detail_id);
            $i->bindParam(':company_id', $company_id);
            $i->bindParam(':branch_id', $branch_id);
            $i->bindParam(':station', $station);
            $i->bindParam(':trans_date', $trans_date);
            $i->bindParam(':trans_hr', $trans_hr);
            $i->bindParam(':start_hours', $start_hours);
            $i->bindParam(':end_hours', $end_hours);
            $i->bindParam(':denom_100', $denom_100);
            $i->bindParam(':denom_50', $denom_50);
            $i->bindParam(':denom_20', $denom_20);
            $i->bindParam(':denom_10', $denom_10);
            $i->bindParam(':denom_5', $denom_5);
            $i->bindParam(':denom_2', $denom_2);
            $i->bindParam(':denom_1', $denom_1);
            $i->bindParam(':coin', $coin);
            $i->bindParam(':no_depsites', $no_depsites);
            $i->bindParam(':no_withdrawl', $no_withdrawl);
            $i->bindParam(':total_amount_calculated', $total_amount_calculated);
            $i->bindParam(':total_pieces_calculated', $total_pieces_calculated);
            $i->bindParam(':created_date', $created_date);
            $i->bindParam(':created_by', $created_by);
            $i->bindParam(':updated_by', $updated_by);

            $i->execute();
       }
        //3.get latest file for company, branch and station from transaction details

        $trans = $db->prepare("select start, end, sum(NoOfTrans) as NoOfTrans, sum(denom1) as denom1, sum(denom2) as denom2, sum(denom5) as denom5, sum(denom10) as denom10, sum(denom20) as denom20, sum(denom50) as denom50, sum(Denom100) as denom100, sum(Coin) as coin, sum(TotalAmount) as totalAmount, sum(TotalPieces) as totalPieces,trans_datetime,file_processing_detail_id
            from (
            select a.Start, a.end, NoOfTrans , a.trans_datetime,a.file_processing_detail_id,
            if (type = 'Withdrawl', Denom1 * -1, Denom1 ) Denom1,
            if (type = 'Withdrawl', Denom2 * -1, Denom2 ) Denom2, 
            if (type = 'Withdrawl', Denom5 * -1, Denom5 ) Denom5,
            if (type = 'Withdrawl', Denom10 * -1, Denom10) Denom10,
            if (type = 'Withdrawl', Denom20* -1, Denom20) Denom20,
            if (type = 'Withdrawl', Denom50 * -1, Denom50) Denom50,
            if (type = 'Withdrawl', Denom100 * -1, Denom100) Denom100,
            if (type = 'Withdrawl', Coin * -1, Coin ) Coin,
            if (type = 'Withdrawl', TotalAmount * -1, TotalAmount) TotalAmount,
            if (type = 'Withdrawl', TotalPieces * -1, TotalPieces) TotalPieces

            from 
            (select HOUR(trans_datetime) as start, HOUR(trans_datetime)+1 as end, trans_datetime,

            case trans_type_id when 1 then 'Withdrawl' when 11 then 'Withdrawl' when 2 then 'Deposite' else 'Unknown' END as type, 
            count(1) as 'NoOfTrans', 
            ifnull(sum(denom_1), 0) as 'Denom1', ifnull(sum(denom_2), 0) as 'Denom2', ifnull(sum(denom_5), 0)  as 'Denom5', ifnull(sum(denom_10), 0)  as 'Denom10', 
            ifnull(sum(denom_20), 0) as 'Denom20', ifnull(sum(denom_50), 0) as 'Denom50', ifnull(sum(denom_100), 0) as 'Denom100', ifnull(sum(coin), 0) as 'Coin', ifnull(sum(total_amount), 0)  as 'TotalAmount', ifnull(sum(total_pieces_calculated), 0) as 'TotalPieces', file_processing_detail_id
            from transaction_details
            where file_processing_detail_id =  (select id from file_processing_detail where company_id = '".$company_id."' and branch_id = '".$branch_id."' and station = '".$station."' order by id desc limit 1)
            group by 1,2,3) a 

            ) b
            group by 1, 2");
        $trans->execute();
         
            while ($row = $trans->fetch(PDO::FETCH_ASSOC)) {
                if(isset($row['file_processing_detail_id'])){    

                $ins = $db->prepare("INSERT INTO `inventory_by_hours`(file_processing_detail_id, company_id, branch_id, station, trans_date, trans_hr,start_hours, end_hours, denom_100, denom_50, denom_20, denom_10, denom_5, denom_2, denom_1, coin, no_depsites, no_withdrawl, total_amount_calculated, total_pieces_calculated, created_date, created_by,  updated_by)
                VALUES(:file_processing_detail_id,:company_id,:branch_id,:station,:trans_date,:trans_hr,:start_hours, :end_hours,:denom_100,:denom_50,:denom_20,:denom_10,:denom_5,:denom_2,:denom_1,:coin,:no_depsites,:no_withdrawl,:total_amount_calculated,:total_pieces_calculated,:created_date,:created_by,:updated_by)");
                $ins_created_date = date('Y-m-d H:i:s');
                $ins_start_hours = $row['start'].':00:00';
                $ins_end_hours = $row['end'].':00:00';
                $ins_no_depsites = $ins_no_withdrawl =0;
                $ins_trans_hr ='';
                $ins_trans_date = date('Y-m-d',strtotime($row['trans_datetime']));
                
                $ins->bindParam(':file_processing_detail_id', $row['file_processing_detail_id']);
                $ins->bindParam(':company_id', $company_id);
                $ins->bindParam(':branch_id', $branch_id);
                $ins->bindParam(':station', $station);
                $ins->bindParam(':trans_date',$ins_trans_date);
                $ins->bindParam(':trans_hr',$ins_trans_hr);
                $ins->bindParam(':start_hours',$ins_start_hours);
                $ins->bindParam(':end_hours',$ins_end_hours);
                $ins->bindParam(':denom_100', $row['denom100']);
                $ins->bindParam(':denom_50', $row['denom50']);
                $ins->bindParam(':denom_20', $row['denom20']);
                $ins->bindParam(':denom_10', $row['denom10']);
                $ins->bindParam(':denom_5', $row['denom5']);
                $ins->bindParam(':denom_2', $row['denom2']);
                $ins->bindParam(':denom_1', $row['denom1']);
                $ins->bindParam(':coin', $row['coin']);
                $ins->bindParam(':no_depsites', $ins_no_depsites);
                $ins->bindParam(':no_withdrawl', $ins_no_withdrawl);
                $ins->bindParam(':total_amount_calculated', $row['totalAmount']);
                $ins->bindParam(':total_pieces_calculated', $row['totalPieces']);
                $ins->bindParam(':created_date', $ins_created_date);
                $ins->bindParam(':created_by', $created_by);
                $ins->bindParam(':updated_by', $updated_by);

                $ins->execute(); 
                //$ins->debugDumpParams();
               }
            }
            
        //4. insert last record

        $inv = $db->prepare("select * from inventory where file_processing_detail_id =  (select id from file_processing_detail where company_id = '".$company_id."' and branch_id = '".$branch_id."' and station = '".$station."' order by id desc limit 0,1) and station = '".$station."' and activity_report_id is not null order by id desc limit 1");
       
        $inv->execute();
        $inv_arr = $inv->fetch();
        
       if(isset($inv_arr['file_processing_detail_id'])){
            $inv_ins = $db->prepare("INSERT INTO `inventory_by_hours`(file_processing_detail_id, company_id, branch_id, station, trans_date, trans_hr, start_hours, end_hours, denom_100, denom_50, denom_20, denom_10, denom_5, denom_2, denom_1, coin, no_depsites, no_withdrawl, total_amount_calculated, total_pieces_calculated, created_date, created_by,  updated_by)
                VALUES(:file_processing_detail_id,:company_id,:branch_id,:station,:trans_date,:trans_hr,:start_hours, :end_hours,:denom_100,:denom_50,:denom_20,:denom_10,:denom_5,:denom_2,:denom_1,:coin,:no_depsites,:no_withdrawl,:total_amount_calculated,:total_pieces_calculated,:created_date,:created_by,:updated_by)");

            $inv_trans_date = '';
            $inv_trans_hr = '';
            $inv_no_depsites = $inv_no_withdrawl='';
            $inv_total_pieces_calculated ='';
            $inv_created_date = date('Y-m-d H:i:s');
            $inv_created_by = $inv_updated_by = 'sys';
            $inv_start_hours = '17:00:00';
            $inv_end_hours = '18:00:00';
            $inv_trans_date = date('Y-m-d',strtotime($invent_trans_date));
            $inv_ins->bindParam(':file_processing_detail_id', $inv_arr['file_processing_detail_id']);
            $inv_ins->bindParam(':company_id', $company_id);
            $inv_ins->bindParam(':branch_id', $branch_id);
            $inv_ins->bindParam(':station', $station);
            $inv_ins->bindParam(':trans_date', $inv_trans_date);
            $inv_ins->bindParam(':trans_hr', $inv_trans_hr);
            $inv_ins->bindParam(':start_hours', $inv_start_hours);
            $inv_ins->bindParam(':end_hours', $inv_end_hours);
            $inv_ins->bindParam(':denom_100', $inv_arr['denom_100']);
            $inv_ins->bindParam(':denom_50', $inv_arr['denom_50']);
            $inv_ins->bindParam(':denom_20', $inv_arr['denom_20']);
            $inv_ins->bindParam(':denom_10',$inv_arr['denom_10']);
            $inv_ins->bindParam(':denom_5', $inv_arr['denom_5']);
            $inv_ins->bindParam(':denom_2', $inv_arr['denom_2']);
            $inv_ins->bindParam(':denom_1', $inv_arr['denom_1']);
            $inv_ins->bindParam(':coin', $inv_arr['coin']);
            $inv_ins->bindParam(':no_depsites', $inv_no_depsites);
            $inv_ins->bindParam(':no_withdrawl', $inv_no_withdrawl);
            $inv_ins->bindParam(':total_amount_calculated', $inv_arr['total']);
            $inv_ins->bindParam(':total_pieces_calculated', $inv_total_pieces_calculated);
            $inv_ins->bindParam(':created_date', $inv_created_date);
            $inv_ins->bindParam(':created_by', $inv_created_by);
            $inv_ins->bindParam(':updated_by', $inv_updated_by);
            $inv_ins->execute();
        }
       
    }
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
            /*print_r($value);*/
            $worker->run($value);
        }
        if(empty($companyBranches)){
            echo "\n\n#########################################\n\n\n";
            echo "\tNo Any Active Branch Exists\n\n\n";
            echo "#########################################\n\n\n";
        }
    }

      $db = DB::getInstance();
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
          $sql_machine = $db->prepare("SELECT * FROM file_processing_detail where created_date>='2019-04-28'");
    $sql_machine->execute();
    //echo "SELECT * FROM file_processing_detail where id>='19504'";
      while($row = $sql_machine->fetch(PDO::FETCH_ASSOC)){  
        $worker->check_logout_time($row['id'],$row['branch_id'],$row['station'],$row['file_date']); 
      }
}


runApp($do_not_use_client_logic);
 
