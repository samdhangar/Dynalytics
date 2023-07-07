<?php

class CompanyBranch extends Model
{
	public static $instance = null;

	protected $fields = array(	"id",
								"company_id",
								"ftpuser",
								"ftp_pass",
								"ftp_path"
        );

	public static function getAll()
	{
		$sql = "SELECT id,company_id,ftpuser,ftp_pass,ftp_path FROM `company_branches` where `branch_status` = 'active' ORDER BY id ASC";
		$stmt = DB::getInstance()->prepare($sql);
		$stmt->setFetchMode(PDO::FETCH_CLASS, "CompanyBranch");
		$stmt->execute();

		$r = array();
		while($a = $stmt->fetch())
		{
			$r[] = $a;
		}

		return $r;
	}
}