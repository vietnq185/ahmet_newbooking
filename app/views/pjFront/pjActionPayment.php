<?php
$index = pjObject::escapeString($_GET['index']);
$front_messages = __('front_messages', true, false);
$months = __('months', true);
$days = __('days', true);
ksort($months);
ksort($days);
if ($tpl['status'] == 'OK') {
	if ($tpl['arr']['payment_method'] == 'saferpay' && empty($tpl['arr']['txn_id'])) {
		include_once 'elements/payment.php';
	} else {
		$t1 = strtotime($tpl['arr']['booking_date']);
		$t2 = time();
		$diff = $t1 - $t2;
		$hours = $diff / ( 60 * 60 );
		include_once 'elements/summary.php';
	}
} else {
	?>
	<div class="alert alert-danger"><?php __('front_error'); ?></div>
	<?php 
} ?>