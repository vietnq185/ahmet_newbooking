<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form']['departure'];
$index = pjObject::escapeString($_GET['index']);
$dayIndex = date('N', strtotime(pjUtil::formatDate(@$STORE['search']['date'], $tpl['option_arr']['o_date_format'])));
include_once 'elements/departure.php';
?>
