<div style="margin-bottom: 14px; margin-top: 10px; font-weight: bold; font-size: 16px;">
<?php
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
?>
</div>
<?php include PJ_VIEWS_PATH . 'pjAdminBookings/elements/single.php'; ?>