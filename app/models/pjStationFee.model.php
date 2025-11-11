<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStationFeeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'stations_fees';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'station_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'end', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjStationFeeModel($attr);
	}
}
?>