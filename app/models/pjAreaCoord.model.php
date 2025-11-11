<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAreaCoordModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'areas_coords';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'area_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'icon', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'is_airport', 'type' => 'tinyint', 'default' => '0'),
		array('name' => 'data', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'tmp_hash', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'price_level', 'type' => 'tinyint', 'default' => '1'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'is_disabled', 'type' => 'tinyint', 'default' => '0'),
	);
	
	public $i18n = array('place_name');
	
	public static function factory($attr=array())
	{
		return new pjAreaCoordModel($attr);
	}
}
?>