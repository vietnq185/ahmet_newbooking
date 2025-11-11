<?php
$index = pjObject::escapeString($_GET['index']);
$STORE = @$tpl['store'];
$date = pjUtil::formatDate(@$STORE['search']['date'], $tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
include_once 'elements/services.php';
?>