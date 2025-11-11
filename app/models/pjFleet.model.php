<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'fleets';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'min_passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'return_discount_1', 'type' => 'decimal', 'default' => ':NULL'), // Monday
		array('name' => 'return_discount_2', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_3', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_4', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_5', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_6', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_7', 'type' => 'decimal', 'default' => ':NULL'), // Sunday
		
	    array('name' => 'return_discount_1_2', 'type' => 'decimal', 'default' => ':NULL'), // Monday
	    array('name' => 'return_discount_2_2', 'type' => 'decimal', 'default' => ':NULL'), // .
	    array('name' => 'return_discount_3_2', 'type' => 'decimal', 'default' => ':NULL'), // .
	    array('name' => 'return_discount_4_2', 'type' => 'decimal', 'default' => ':NULL'), // .
	    array('name' => 'return_discount_5_2', 'type' => 'decimal', 'default' => ':NULL'), // .
	    array('name' => 'return_discount_6_2', 'type' => 'decimal', 'default' => ':NULL'), // .
	    array('name' => 'return_discount_7_2', 'type' => 'decimal', 'default' => ':NULL'), // Sunday
	    
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'crossedout_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'crossedout_type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'start_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'thumb_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'order_index', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'station_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('fleet', 'model', 'description', 'badget');
	
	public static function factory($attr=array())
	{
		return new pjFleetModel($attr);
	}
}
?>