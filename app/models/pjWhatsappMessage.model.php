<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWhatsappMessageModel extends pjAppModel
{
/**
 * The name of table's primary key. If PK is over 2 or more columns set this to boolean null
 *
 * @var string
 * @access public
 */
	var $primaryKey = 'id';
/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'whatsapp_messages';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'external_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'available_for', 'type' => 'enum', 'default' => 'both'),   
	    array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'domain', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $validate = array(
	
	);
	
	public $i18n = array('subject','message');

	public static function factory($attr=array())
	{
		return new pjWhatsappMessageModel($attr);
	}
}
?>