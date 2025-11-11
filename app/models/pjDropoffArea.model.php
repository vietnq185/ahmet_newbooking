<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDropoffAreaModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'dropoff_areas';
	
	protected $schema = array(
		array('name' => 'dropoff_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'area_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjDropoffAreaModel($attr);
	}
}
?>