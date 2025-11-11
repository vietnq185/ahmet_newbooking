<?php 
$t1 = strtotime($tpl['arr']['booking_date']);
$t2 = time();
$diff = $t1 - $t2;
$hours = $diff / ( 60 * 60 );
$front_messages = __('front_messages', true, false);
include_once 'elements/summary.php';
?>