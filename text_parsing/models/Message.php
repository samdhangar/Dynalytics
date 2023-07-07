<?php

Hook::sub("subscription", function($line){
	if(Message::has()
		AND (preg_match_all("/^(Date)[ ]+([0-9]+(\/[0-9]+){2})/", $line, $matches)
		OR preg_match_all("/^(Time)[ ]+([0-9]+(:[0-9]+){2} (PM|AM))/", $line, $matches)))
	{
		$key = array_shift($matches[1]);
		$val = array_shift($matches[2]);
		if($key == "Date")
			Message::instance()->date = format_date($val);
		if($key == "Time")
			Message::instance()->time = format_time($val);
	}
	else
	{
		if(Message::has())
		{
			Message::instance()->manager_id = Manager::has() ? Manager::instance()->manager_id : null;
			Message::instance()->teller_id = Side::has() ? Side::instance()->teller_id : null;
			Message::instance()->side_id = Side::has() ? Side::instance()->side_type : "";
			$data = Message::instance()->toArray();
			$data["created_date"] = date("Y-m-d H:i:s");
			$id = null;
			if(!DB::insert("messages", $data, $id))
			{
				 throw new Exception("Message not Saved\n", 1);
            }else{
                //Check For Ticket and If It's Error then create a Ticket
                ErrorTicket::instance()->create($data);
            }
			Message::destroy();
		}
		else if(strpos($line, '#') !== false){
			$pos=strpos($line, '#');
			if(preg_match('/^[a-zA-Z0-9#]+$/', $line{($pos+1)})){
				$machine_type=$GLOBALS['machine_id']; 
	        	$error_code=substr($line, ($pos+1), strpos(substr($line,($pos+1)), " "));
	        	$db = DB::getInstance();
	       	    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
	         	$stmt_error = Db::getInstance()->prepare("SELECT `id` , error_text FROM `error_types` where error_code='$error_code' AND machine_type='$machine_type'");
	        	$stmt_error->execute();
	        	$error_Type = $stmt_error->fetch(PDO::FETCH_ASSOC);
	        	if(empty($error_Type['id'])){
	           		$sql = $db->prepare("INSERT INTO  error_types (error_code , error_level , error_text , error_meaning , machine_type , severity , transaction_type , error_type) VALUES('$error_code','error','Unknown Error','','$machine_type','Med','Online Deposit','1')");
	    			$sql->execute(); 
	     			$stmt_error = Db::getInstance()->prepare("SELECT `id` , error_text FROM `error_types` where error_code='$error_code' AND machine_type='$machine_type'");
	        		$stmt_error->execute();
	       			 $error_Type = $stmt_error->fetch(PDO::FETCH_ASSOC);
       			 }
       		    $instanseDetail = ErrorDetail::instance();
		        $instanseDetail->file_processing_detail_id = ProcessingFile::instance()->id;
		        $instanseDetail->error_type_id = $error_Type['id'];
		        $instanseDetail->start_date = date('Y-m-d H:i:s');
		        $instanseDetail->error_message = $error_Type['error_text'];
		        $instanseDetail->entry_timestamp = date('Y-m-d H:i:s');
		        $insertData = $instanseDetail->toArray();
		        DB::insert('test_error_detail', $insertData, $id);
		        if (!DB::insert('error_detail', $insertData, $id)) {
		                echo "Error: Error Detail not Saved\n";
		        }else{
		        	ErrorTicket::instance()->create($insertData);
		        } 
			} 
		} 
	}
	if($r = AvailableMessages::instance()->find($line))
	{
        
		Message::instance()->message = trim($line);
	} 

});

class Message extends Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"manager_id",
								"teller_id",
								"side_id",
								"message",
								"datetime",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $manager_id;
	public $teller_id;
	public $side_id;
	public $message;
	public $datetime;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = ProcessingFile::instance()->id;
		$this->created_by = "sys";
	}

	public function toArray()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));

		return parent::toArray();
	}
}