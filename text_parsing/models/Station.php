<?php
Hook::sub("subscription", function($line) {

    if ($station = Station::isStation($line)) {
        Station::instance()->id = $station;
    }
});

class Station extends Model
{
    public static $instance = null;
    protected static $type = null;
    protected static $counter = 0;
    protected $fields = array("id",
        "company_id",
        "branch_id",
        "name",
        "first_file_process_id",
        "last_file_process_id",
        "first_file_date",
        "last_file_date",
        "file_processed_count",
        "status",
        "created_by",
        "updated_by",
        "created",
        "updated");
    public $id = null;
    public $company_id = 0;
    public $branch_id = 0;
    public $name = '';
    public $first_file_process_id = 0;
    public $last_file_process_id = 0;
    public $first_file_date = '0000-00-00';
    public $last_file_date = '0000-00-00';
    public $file_processed_count = 1;
    public $status = 'active';
    public $created_by = 0;
    public $updated_by = 0;
    public $created = '0000-00-00';
    public $updated = '0000-00-00';

    public function __construct()
    {
        $this->updated = $this->created = date("Y-m-d H:i:s");
    }

    public static function getTotalFiles($companyId = 0, $branchId = 0, $name = '')
    {
        $totalFile = 0;
        if (!empty($companyId) && !empty($branchId) && !empty($name)) {
            $totalFile = ProcessingFile::instance()->getNoOfFiles($companyId, $branchId, $name);
        }
        return $totalFile;
    }

    public static function isStation($str)
    {
        $matches = array();
        if (!preg_match_all("/Station #[ ]+([A-Za-z0-9_]+)/", $str, $matches))
            return false;

        return array_shift($matches[1]);
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::init();
        }

        return self::$instance;
    }

    public static function getFileStationId($companyId = false, $branchId = false, $station = false)
    {
        if (!empty($companyId) && !empty($branchId) && !empty($station)) {
            $stmt = Db::getInstance()->prepare("SELECT id,first_file_process_id,first_file_date,last_file_date FROM `stations` WHERE `company_id`=:company_id and `branch_id`=:branch_id and `name`=:station LIMIT 1");
            $stmt->bindParam(":company_id", $companyId, PDO::PARAM_INT);
            $stmt->bindParam(":branch_id", $branchId, PDO::PARAM_INT);
            $stmt->bindParam(":station", $station, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Station");
            $stmt->execute();
            $a = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($a['id'])) {
                return $a;
            }
        }
        return array('id' => 0);
    }

    public static function getStation($id)
    {
        $stmt = Db::getInstance()->prepare("SELECT * FROM `stations` WHERE `id`=:station_id LIMIT 1");
        $stmt->bindParam(":station_id", $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Station");
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function init($id = false)
    {
        if ($id)
            self::$instance = self::getStation($id);

        if (empty(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    public function save()
    {
        $returnStatus = 0;
        if (empty($this->id)) {
            $returnStatus = DB::insert("stations", $this->toArray(), $this->id);
            /*
             * Manage station count of the company
             */
            if (!empty($this->company_id)) {
                $query = "select count(`id`) as station_count from stations where company_id = " . $this->company_id;
                $stmt = Db::getInstance()->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result['station_count'])) {
                    $totalStation = $result['station_count'];
                    $query = "update users set `station_count` = $totalStation where id = " . $this->company_id;
                    $stmt = Db::getInstance()->prepare($query);
                    $stmt->execute();
                }
            }
        }
        $returnStatus = DB::update("stations", $this->toArray(), "id");
        return $returnStatus;
    }

    public function toArray()
    {
        $array = array();
        foreach ($this->fields AS $k) {
            $array[$k] = $this->$k;
        }

        return $array;
    }
}
