<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/Hook.php");
require_once(__DIR__ . "/models/DB.php");
require_once(__DIR__ . "/models/Model.php");
require_once(__DIR__ . "/models/EmailRead.php");  
require_once(__DIR__ . "/models/SendEmail.php");
require_once(__DIR__ . "/PHPMailer/PHPMailerAutoload.php");  
class Worker
{ 
    public function __construct()
    { 
        $this->path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    } 
    public function run(CompanyBranch $dir = null)
    { 
        SendEmail::init();
        SendEmail::red_new(); 
    } 
} 
function runApp($dont_use_client_logic = false)
{
     $worker = new Worker();
    $worker->run(); 
} 
runApp($do_not_use_client_logic);
 
