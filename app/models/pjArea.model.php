<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAreaModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'areas';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order_index', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjAreaModel($attr);
	}
}
?>