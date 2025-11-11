<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjAppModel
{
	protected $table = 'prices';
	
	protected $schema = array(
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'dropoff_id', 'type' => 'int', 'default' => ':NULL'),		
		array('name' => 'price_1', 'type' => 'decimal', 'default' => ':NULL'), // Monday
		array('name' => 'price_2', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'price_3', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'price_4', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'price_5', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'price_6', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'price_7', 'type' => 'decimal', 'default' => ':NULL'), // Sunday
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
}
?>