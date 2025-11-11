<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLocationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'locations';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_airport', 'type' => 'bool', 'default' => '0'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'icon', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'order_index', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'lat', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'area_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'color', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('pickup_location');
	
	public static function factory($attr=array())
	{
		return new pjLocationModel($attr);
	}
}
?>