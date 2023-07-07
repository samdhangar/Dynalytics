<?php
error_reporting(E_ALL);

$do_not_use_client_logic = false;

require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/Hook.php");
require_once(__DIR__ . "/models/DB.php");
require_once(__DIR__ . "/models/Model.php");
require_once(__DIR__ . "/models/CompanyBranch.php");

class RenewFiles
{
    protected $debug = false;
    protected $branch_info = 0;
    protected $path = "";
   
    public function __construct()
    {
     
        $this->debug = true;
        $this->path = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    public function run(CompanyBranch $dir = null)
    {
        $this->dir = is_null($dir->ftp_path) ? "storage" : $dir->ftp_path;
        $this->dir = str_replace('//', DIRECTORY_SEPARATOR, $this->path . $this->dir);
        
        //CHECK FOR COMPANY BRANCH AND IF FTP EXIST THEN PROCESS FILE
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
        register_shutdown_function(array($this, 'fatalErrorShutdownHandler'));
        //header("Content-Type: text/plain");
    }

    protected function checkQueue()
    {
     
        $this->queueDir = "{$this->dir}/queue";
        $this->warehouseDir = "{$this->dir}/warehouse";

        if (!is_dir($this->queueDir)) {
            mkdir($this->queueDir, 755);
        }
        if (!is_dir($this->warehouseDir)) {
            mkdir($this->warehouseDir, 755);
        }

        $this->files = glob("{$this->warehouseDir}/*.txt");

        if (empty($this->files))
            return false;

        $count = count($this->files);

        $this->out("Found $count files in queue!", 1);
        
        return true;
    }

    protected function processQueue()
    {
        while (count($this->files)) {
            
            $this->processFile(array_shift($this->files));
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
		$date =  substr($this->file,-12,8);

		if(DateTime::createFromFormat("mdY", $date)){
			//Assign New Path
			$newFileDate =  DateTime::createFromFormat("mdY", $date)->format("01d2020");
			$newPath = str_replace("{$this->warehouseDir}/", "{$this->queueDir}/",$this->file);
			$newPath = str_replace("{$date}.", "{$newFileDate}.",$newPath);
			
			$olddate =  DateTime::createFromFormat("mdY", $date)->format("n/d/y");			
			$newdate =  DateTime::createFromFormat("mdY", $date)->format("01/d/20");	
			
			file_put_contents($this->file,str_replace($olddate,$newdate,file_get_contents($this->file)));
			
			
			// is file ready
			if (!is_file_ready($this->file)){
				return;
			}
			// Processing file move to processing folder
			if (!copy($this->file, $newPath)){
				$this->error("Unable to move file {$this->file}");
			}
		}else{
			return;
		}
        $this->file = null;
    }
}

function runApp($dont_use_client_logic = false)
{
    if ($dont_use_client_logic) {     
        $RenewFiles = new RenewFiles();
        $RenewFiles->run();
    } else {
        $companyBranches = CompanyBranch::getAll();
        foreach (CompanyBranch::getAll() AS $value) {
            $RenewFiles = new RenewFiles();
            $RenewFiles->run($value);
        }
        if(empty($companyBranches)){
            echo "\n\n#########################################\n\n\n";
            echo "\tNo Any Active Branch Exists\n\n\n";
            echo "#########################################\n\n\n";
        }
    }
}
runApp($do_not_use_client_logic);