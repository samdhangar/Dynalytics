<?php
namespace Setup;

$_i = NetworkCtrl::getInstance();

\Hook::sub("subscription", array($_i, "index"));

class NetworkCtrl
{
	protected static $instance = null;
	
	protected $line = "";
	protected $emptyLine = 0;
	protected $settings = null;
	protected $setup = null;
	protected $start = false;

	public static function getInstance()
	{
		if(empty(self::$instance))
			self::$instance = new self();

		return self::$instance;
	}

	public function __construct()
	{
	}

	public function index($line)
	{
		$this->line = $line;

		if($this->isStartLine())
		{
			return $this->settings = new NetworkSettings();
		}

		if(!$this->settings)
			return;

		if(!strlen(trim($this->line)))
		{
			if($this->emptyLine > 1)
				return $this->complete();
			
			return $this->emptyLine++;
		}

		$this->emptyLine = 0;

		$matches = array();
		if(preg_match_all("/([A-Za-z]+( [A-Za-z\(\)\.]+)*):?[ ]{2,}([A-Za-z0-9\:\/\.]+( AM| PM)?)/", $this->line, $matches))
		{
			$key = array_shift($matches[1]);
			$val = array_shift($matches[3]);

			if($key == "Date")
			{
				$this->settings->date = format_date($val);
			}
			if($key == "Time")
			{
				$this->settings->time = format_time($val);
			}
			if(strpos($key, "Mode") !== false)
			{
				$this->settings->mode = $val;
			}
			if($key == "Network")
			{
				$this->settings->network = $val;
			}
			if($key == "DHCP")
			{
				$this->settings->dhcp = $val;
			}
			if($key == "DHCP Fallback")
			{
				$this->settings->dhcp_fallback = $val;
			}
			if($key == "DHCP Timeout (Secs.)")
			{
				$this->settings->dhcp_timeout = $val;
			}
			if($key == "IP Address")
			{
				$this->settings->ip_address = $val;
			}
			if($key == "NetMask")
			{
				$this->settings->netmask = $val;
			}
			if($key == "GateWay")
			{
				$this->settings->gateway = $val;
			}
			if($key == "MAC Address")
			{
				$this->settings->mac_address = $val;

				$this->complete();
			}

		}
	}

	protected function complete()
	{
		$this->settings->completed();
		$this->settings = null;
	}

	protected function isStartLine()
	{
		$matches = array();
		return preg_match_all("/^[ ]+(Network Settings)[ ]+/", $this->line, $matches);
	}
	protected function isEndLine()
	{
		$matches = array();
		return preg_match_all("//", $this->line, $matches);
	}
}

class NetworkSettings extends \Model
{
	public static $instance = null;
	protected $fields = array("id",
								"file_processing_detail_id",
								"datetime",
								"mode",
								"network",
								"dhcp",
								"dhcp_fallback",
								"dhcp_timeout",
								"ip_address",
								"netmask",
								"gateway",
								"mac_address",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by");

	public $id;
	public $file_processing_detail_id;
	public $datetime;
	public $mode;
	public $network;
	public $dhcp;
	public $dhcp_fallback;
	public $dhcp_timeout;
	public $ip_address;
	public $netmask;
	public $gateway;
	public $mac_address;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public $date = "1999-01-01";
	public $time = "00:00:00";

	public function __construct()
	{
		$this->file_processing_detail_id = \ProcessingFile::instance()->id;
		$this->created_date = date("Y-m-d H:i:s");
		$this->created_by = "sys";
	}

	public function completed()
	{
		$this->datetime = implode(" ", array($this->date, $this->time));

		// Save
		\DB::insert("networking_settings", $this->toArray(), $this->id);
	}
}