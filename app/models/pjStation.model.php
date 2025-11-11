<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'stations';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'start_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'lat', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'free_starting_fee_in_km', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'max_base_station_distance', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'min_travel_distance', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjStationModel($attr);
	}
}
?>