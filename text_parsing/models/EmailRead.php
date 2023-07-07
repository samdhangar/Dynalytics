<?php  
class EmailRead extends Model
{ 
    public function __construct()
    { 
    }
     public static function getLastTime()
    {
         $query = "SELECT last_date FROM `email_read` LIMIT 1";
         $stmt = Db::getInstance()->prepare($query);
         $stmt->execute();
         $result = $stmt->fetch(PDO::FETCH_ASSOC);
         return date("d F Y",strtotime($result['last_date']));
    }
     public static function updateLastTime()
    {
         $query = "UPDATE `email_read` set last_date='".date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'))."'";
         $stmt = Db::getInstance()->prepare($query);
         $stmt->execute();
    } 
}
