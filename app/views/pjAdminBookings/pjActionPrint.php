<div style="margin-bottom: 14px; margin-top: 10px; font-weight: bold; font-size: 16px;">
<?php
if(!isset($_GET['id']) && !isset($_GET['details']))
{ 
	if (isset($_GET['record']) && $_GET['record'] != '')
	{ 
		__('lblReservationPrintList');
	}else{
		__('lblTodayTransfers');
	}
}elseif (isset($_GET['id']) && !isset($_GET['details'])){
    $pickup_arr = $tpl['transfer_arr'][0];
    if(count($tpl['transfer_arr']) == 2)
    {
        $return_arr = $tpl['transfer_arr'][1];
    }

	__('lblReservationPrint', false, false);
	if(!empty($pickup_arr))
	{
		echo '<br/>' . __('lblID', true, false) . ': ' . $pickup_arr['uuid'];
	}
} 
?>
</div>
<?php
if(!isset($_GET['id']))
{ 
	if (isset($_GET['details'])) {
		$transfer_arr = array();
		foreach ($tpl['transfer_arr'] as $transfer) {
			if ($transfer['return_id'] > 0) {
				$transfer_arr[$transfer['return_id']][] = $transfer;
			} else {
				$transfer_arr[$transfer['id']][] = $transfer;
			}
		}
		__('lblReservationPrint', false, false);
		$i = 0;
		foreach ($transfer_arr as $item) {
			$pickup_arr = $item[0];
		    if(count($item) == 2)
		    {
		        $return_arr = $item[1];
		    }		
			if(!empty($pickup_arr))
			{
				echo '<br/>' . __('lblID', true, false) . ': ' . $pickup_arr['uuid'];
			}
			if ($i > 0) { ?>
				<div class="page-break"></div>
			<?php } 
			include PJ_VIEWS_PATH . 'pjAdminBookings/elements/details.php'; 
			$i++;
		}
	} else {
		include PJ_VIEWS_PATH . 'pjAdminBookings/elements/list.php';
	} 
}else{
	include PJ_VIEWS_PATH . 'pjAdminBookings/elements/single.php'; 
} 
?>