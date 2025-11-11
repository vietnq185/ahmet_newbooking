<?php
$client_name = __('personal_titles_ARRAY_' . $pickup_arr['c_title'], true, false) . ' ' .  $pickup_arr['c_fname'] . ' ' . $pickup_arr['c_lname'];
?>
<div style="overflow: hidden; margin-bottom: 16px;">
	<div style="width: 48%;margin-right: 4%;float:left;">
		<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;margin-bottom: 40px;">
			<thead>
				<tr>
					<th colspan="2"><?php __('front_step_passenger_details');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$statuses = __('booking_statuses', true, false);
				$payment_methods = __('payment_methods', true, false);
                ?>
                <tr class="bold">
                    <td style="width:40%;"><?php __('front_name_surname', false, false);?></td>
                    <td style="width:60%;"><?= $client_name ?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_mobile_number', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_dialing_code'] . $pickup_arr['c_phone']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_email', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_email']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_country', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_country']);?></td>
                </tr>
			</tbody>
		</table>
	</div>
	<div style="width: 48%;float:right;">
		<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 40px;">
			<thead>
				<tr>
					<th colspan="2"><?php __('lblTransfer');?>: <?php echo count($item) == 2 ? __('lblRoundTrip') : __('lblSingle');?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblVehicle', false, false);?></td>
					<td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['vehicle']);?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblPassengers', false, false);?></td>
					<td style="width:60%;"><?php echo $pickup_arr['passengers'];?></td>
				</tr>
                <?php if (!empty($return_arr)): ?>
                    <tr>
                        <td style="width:40%;"><?php __('lblPassengersReturn', false, false);?></td>
                        <td style="width:60%;"><?php echo $return_arr['passengers'];?></td>
                    </tr>
                <?php endif; ?>

                <?php if(isset($pickup_arr['extra_arr']) && !empty($pickup_arr['extra_arr'])): ?>
                    <?php foreach($pickup_arr['extra_arr'] as $index => $extra): ?>
                        <tr>
                            <td style="width:40%;"><?= $index == 0? __('front_extras', true): '&nbsp;'; ?></td>
                            <td style="width:60%;">
                                <?= $extra['quantity'] . ' x ' . $extra['name'] ?>
                                <?php if(!empty($extra['info'])): ?>
                                    <i>(<?= $extra['info'] ?>)</i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if($pickup_arr['discount'] > 0): ?>
                    <tr>
                        <td style="width:40%;"><?php __('front_discount', false, false);?></td>
                        <td style="width:60%;"><?php echo pjUtil::formatCurrencySign($pickup_arr['discount'], $tpl['option_arr']['o_currency']); ?> (<?= $pickup_arr['voucher_code'] ?>)</td>
                    </tr>
                <?php endif; ?>
				<tr>
					<td style="width:40%;"><?php __('lblPayment', false, false);?></td>
					<td style="width:60%;"><?php echo pjUtil::formatCurrencySign((in_array($pickup_arr['payment_method'], array('bank', 'creditcard'))) ? 0 : $pickup_arr['total'], $tpl['option_arr']['o_currency']);?> / <?php echo $payment_methods[$pickup_arr['payment_method']];?></td>
				</tr>
                <tr>
                    <td style="width:40%;"><?php __('lblDeposit', false, false);?></td>
                    <td style="width:60%;"><?php echo pjUtil::formatCurrencySign($pickup_arr['deposit'], $tpl['option_arr']['o_currency']);?></td>
                </tr>
				<tr>
					<td style="width:40%;"><?php __('lblStatus', false, false);?></td>
					<td style="width:60%;"><?php echo $statuses[$pickup_arr['status']];?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div style="overflow: hidden; margin-bottom: 16px;">
	<div style="width: 48%;margin-right: 4%;float:left;">
		<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
			<thead>
				<tr>
					<th colspan="2"><?php __('lblPickup');?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="bold">
					<td style="width:40%;"><?php __('lblDateAndTime', false, false);?></td>
					<td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($pickup_arr['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($pickup_arr['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblFrom', false, false);?></td>
					<td style="width:60%;"><?php echo $pickup_arr['location'];?></td>
				</tr>
				<tr>
					<td style="width:40%;"><?php __('lblTo', false, false);?></td>
					<td style="width:60%;"><?php echo $pickup_arr['dropoff_location'];?></td>
				</tr>
                <?php if($pickup_arr['is_airport']): ?>
                    <?php if(!empty($pickup_arr['c_flight_number'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_flight_number', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_flight_number']);?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(!empty($pickup_arr['c_airline_company'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_airline_company', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_airline_company']);?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(!empty($pickup_arr['c_destination_address'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_destination_address', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_destination_address']);?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(!empty($pickup_arr['c_hotel'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_hotel', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_hotel']);?></td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(!empty($pickup_arr['c_address'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_pickup_address', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_address']);?></td>
                        </tr>
                    <?php endif; ?>
                     <?php if(!empty($pickup_arr['c_destination_address'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_dropoff_address', false, false);?></td>
                            <td style="width:60%;"><?php echo stripslashes($pickup_arr['c_destination_address']);?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(!empty($pickup_arr['c_hotel'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_hotel', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_hotel']);?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(!empty($pickup_arr['c_departure_flight_time'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_flight_departure_time', false, false);?></td>
                            <td style="width:60%;"><?php echo pjSanitize::clean($pickup_arr['c_departure_flight_time']);?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(!empty($pickup_arr['c_notes'])): ?>
                    <tr>
                        <td style="width:40%;"><?php __('front_notes', false, false);?></td>
                        <td style="width:60%;"><?php echo nl2br(pjSanitize::clean($pickup_arr['c_notes']));?></td>
                    </tr>
                <?php endif; ?>
			</tbody>
		</table>
	</div>
	<div style="width: 48%;float:right;">
		<?php
		if(count($item) == 2)
		{
			?>
			<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
				<thead>
					<tr>
						<th colspan="2"><?php __('lblReturn');?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="bold">
						<td style="width:40%;"><?php __('lblDateAndTime', false, false);?></td>
						<td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($return_arr['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($return_arr['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
					</tr>
					<tr>
						<td style="width:40%;"><?php __('lblFrom', false, false);?></td>
						<td style="width:60%;"><?php echo $return_arr['location2'];?></td>
					</tr>
					<tr>
						<td style="width:40%;"><?php __('lblTo', false, false);?></td>
						<td style="width:60%;"><?php echo $return_arr['dropoff_location2'];?></td>
					</tr>

                    <?php if($pickup_arr['is_airport']): ?>
                        <?php if(!empty($return_arr['c_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_address', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($return_arr['c_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($return_arr['c_departure_flight_time'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_departure_time', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($return_arr['c_departure_flight_time']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                    	<?php if(!empty($return_arr['c_address'])): ?>
	                        <tr>
	                            <td style="width:40%;"><?php __('front_pickup_address', false, false);?></td>
	                            <td style="width:60%;"><?php echo pjSanitize::clean($return_arr['c_address']);?></td>
	                        </tr>
	                    <?php endif; ?>
	                     <?php if(!empty($return_arr['c_destination_address'])): ?>
	                        <tr>
	                            <td style="width:40%;"><?php __('front_dropoff_address', false, false);?></td>
	                            <td style="width:60%;"><?php echo stripslashes($return_arr['c_destination_address']);?></td>
	                        </tr>
	                    <?php endif; ?>
                        <?php if(!empty($return_arr['c_flight_number'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_number', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($return_arr['c_flight_number']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($return_arr['c_airline_company'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_airline_company', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($return_arr['c_airline_company']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if(!empty($return_arr['c_notes'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_notes', false, false);?></td>
                            <td style="width:60%;"><?php echo nl2br(pjSanitize::clean($return_arr['c_notes']));?></td>
                        </tr>
                    <?php endif; ?>
				</tbody>
			</table>
			<?php
		} 
		?>
	</div>
</div>

<div class="page-break">
    <div class="client-name-plate">
        <table class="table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th><?php echo (!empty($pickup_arr['customized_name_plate'])) ? $pickup_arr['customized_name_plate'] : $client_name; ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>