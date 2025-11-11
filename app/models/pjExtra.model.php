<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraModel extends pjAppModel
{
	var $primaryKey = 'id';

	var $table = 'extras';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'image_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	protected $validate = array(
	
	);
	
	public $i18n = array('name', 'info');

	public static function factory($attr=array())
	{
		return new pjExtraModel($attr);
	}
}
?>