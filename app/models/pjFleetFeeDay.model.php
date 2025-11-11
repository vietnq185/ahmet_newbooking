<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetFeeDayModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'fleets_fees_days';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'day', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'start', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'end', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
	    return new pjFleetFeeDayModel($attr);
	}
}
?>