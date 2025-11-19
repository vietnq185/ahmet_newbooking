<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjInvoiceTaxModel extends pjInvoiceAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'plugin_invoice_taxes';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),	    array('name' => 'is_default', 'type' => 'tinyint', 'default' => '0')
	);
	protected $i18n = array('name');
	public static function factory($attr=array())
	{
		return new pjInvoiceTaxModel($attr);
	}
}
?>