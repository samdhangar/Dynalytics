<?php

class ErrorDetail extends Model
{
    public static $instance = null;
    protected $fields = array(
        "id",
        "file_processing_detail_id",
        "error_type_id",
        "start_date",
        "end_date",
        "error_message",
        "entry_timestamp",
    );
    public $id;
    public $file_processing_detail_id = 0;
    public $error_type_id = 0;
    public $start_date;
    public $end_date;
    public $error_message;
    public $entry_timestamp;

    public function __construct()
    {
        
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self(); 
        }

        return self::$instance;
    }

    public function toArray()
    {
        return parent::toArray();
    }

    public static function init()
    {
        
    }

    public static function save($errorMessage = '')
    {
         $error_type_id=$GLOBALS['machine_id'];
         $error_code=102;
 $db = DB::getInstance();
         $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
          $stmt_error = Db::getInstance()->prepare("SELECT `id` FROM `error_types` where error_code='102' AND machine_type='$error_type_id'");
        $stmt_error->execute();
        $error_Type = $stmt_error->fetch(PDO::FETCH_ASSOC);
       
        if(empty($error_Type['id'])){
           $sql = $db->prepare("INSERT INTO  error_types (error_code , error_level , error_text , error_meaning , machine_type , severity , transaction_type , error_type) VALUES('$error_code','error','$errorMessage','','$error_type_id','Med','Online Deposit','1')");
    $sql->execute(); 
     $stmt_error = Db::getInstance()->prepare("SELECT `id` FROM `error_types` where error_code='102' AND machine_type='$error_type_id'");
        $stmt_error->execute();
        $error_Type = $stmt_error->fetch(PDO::FETCH_ASSOC);
        }

        $instanseDetail = self::instance();
        $instanseDetail->file_processing_detail_id = ProcessingFile::instance()->id;
        $instanseDetail->error_type_id = $error_Type['id'];
        $instanseDetail->start_date = date('Y-m-d H:i:s');
        $instanseDetail->error_message = $errorMessage;
        $instanseDetail->entry_timestamp = date('Y-m-d H:i:s');
        $condition = " file_processing_detail_id= " . $instanseDetail->file_processing_detail_id .
            " and error_type_id=".$error_Type['id']." and error_message='$errorMessage'";
        $stmt = Db::getInstance()->prepare("SELECT `id` FROM `error_unknown` where $condition");
        $stmt->execute();
        $errorDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        $insertData = $instanseDetail->toArray();
        echo "error Details ".$errorMessage."end";
        if (!empty($errorDetail['id'])) {
            /*
             * Update record 
             */
            return $errorDetail['id'];
        } else {
            $stmt_msg = Db::getInstance()->prepare("SELECT `id` FROM `available_messages` where `text` LIKE  '%$errorMessage%'");
        $stmt_msg->execute();
        $availableMessages = $stmt_msg->fetch(PDO::FETCH_ASSOC);
         if (empty($availableMessages['id'])) {
            if (!DB::insert('error_unknown', $insertData, $id)) {
                print_r($insertData);
                echo "Error: Error Unknown not Saved\n";
            }
            return DB::getInstance()->lastInsertId();
         }
            
        }
        echo "Error: Error Unknown not Saved\n";
        return 0;
    }
}
