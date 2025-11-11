<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraLimitationModel extends pjAppModel
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
	var $table = 'extras_limitations';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'max_qty', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new self($attr);
	}

    public function saveLimitations($extra_arr, $fleet_id)
    {
        if(!is_array($extra_arr) || !$fleet_id)
        {
            return false;
        }

        if(!empty($extra_arr))
        {
            $this->reset()->where('fleet_id', $fleet_id)->whereNotIn('extra_id', array_keys($extra_arr))->eraseAll();
            foreach($extra_arr as $extra_id => $max_qty)
            {
                $inserted = $this
                    ->reset()
                    ->setAttributes(array(
                        'extra_id'  => $extra_id,
                        'fleet_id'  => $fleet_id,
                        'max_qty'   => $max_qty,
                    ))
                    ->insert()
                    ->getInsertId();
                if($inserted === false)
                {
                    // Record already exists. Update quantity.
                    $this
                        ->reset()
                        ->where('extra_id', $extra_id)
                        ->where('fleet_id', $fleet_id)
                        ->limit(1)
                        ->modifyAll(array('max_qty' => $max_qty));
                }
            }
        }
        else
        {
            $this->reset()->where('fleet_id', $fleet_id)->eraseAll();
        }

        return true;
    }
}
?>
