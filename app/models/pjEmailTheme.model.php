<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjEmailThemeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'emails_themes';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'domain', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'type', 'type' => 'enum', 'default' => 'custom'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('name','subject','body');
	
	public static function factory($attr=array())
	{
		return new pjEmailThemeModel($attr);
	}
}
?>