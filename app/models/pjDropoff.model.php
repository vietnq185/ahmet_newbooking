<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDropoffModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'dropoff';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'distance', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'duration', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_airport', 'type' => 'tinyint', 'default' => '0'),
		array('name' => 'icon', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'order_index', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'region', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'price_level', 'type' => 'tinyint', 'default' => '1'),
	    array('name' => 'base_station_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('location', 'address');
	
	public static function factory($attr=array())
	{
		return new pjDropoffModel($attr);
	}
}
?>