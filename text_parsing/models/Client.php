<?php

class Client extends Model
{
	public static $instance = null;

	protected $fields = array(	"id",
								"name",
								"address",
								"city",
								"state",
								"phone",
								"country",
								"fax",
								"email",
								"storage_location",
								"created_date",
								"created_by",
								"updated_date",
								"updated_by"
							);

	public $id;
	public $name;
	public $address;
	public $city;
	public $state;
	public $phone;
	public $country;
	public $fax;
	public $email;
	public $storage_location;
	public $created_date;
	public $created_by;
	public $updated_date;
	public $updated_by;

	public static function getAll()
	{
		$sql = "SELECT * FROM `clients` WHERE 1";

		$stmt = DB::getInstance()->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
		$stmt->execute();

		$r = array();
		while($a = $stmt->fetch())
		{
			$r[] = $a;
		}

		return $r;
	}
}