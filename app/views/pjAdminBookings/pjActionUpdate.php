<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	
	$date_time = pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);

    $is_airport = $tpl['arr']['pickup_is_airport'];
    $statuses = __('plugin_invoice_statuses', true);
	pjUtil::printNotice(__('infoUpdateBookingTitle', true, false), __('infoUpdateBookingDesc', true, false)); 
	?>

	

		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails');?></a></li>
				<?php if (empty($_GET['copy'])) { ?>
					<li><a href="#tabs-2"><?php __('tabInvoices');?></a></li>
					<li><a href="#tabs-3"><?php __('tabLog');?></a></li>
				<?php } ?>
			</ul>
		
			<div id="tabs-1" class="bs-loader-outer">
				<?php if (empty($_GET['copy'])): ?>
            		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmUpdateBooking" enctype="multipart/form-data">
            			<input type="hidden" name="booking_update" value="1" />
            			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />			
            			
            			<input type="hidden" name="pickup_id" id="pickup_id" value="<?php echo $tpl['arr']['pickup_id'];?>">
            			<input type="hidden" name="pickup_address" id="pickup_address" value="<?php echo stripslashes($tpl['arr']['pickup_address']);?>">
            			<input type="hidden" name="pickup_lat" id="pickup_lat" value="<?php echo $tpl['arr']['pickup_lat'];?>">
            			<input type="hidden" name="pickup_lng" id="pickup_lng" value="<?php echo $tpl['arr']['pickup_lng'];?>">
            			<input type="hidden" name="pickup_type" id="pickup_type" value="<?php echo $tpl['arr']['pickup_type'];?>">		
            			<input type="hidden" name="custom_pickup_id" id="custom_pickup_id" value="<?php echo $tpl['arr']['location_id'];?>">
            			
            			<input type="hidden" name="dropoff_address" id="dropoff_address" value="<?php echo stripslashes($tpl['arr']['dropoff_address']);?>">
            			<input type="hidden" name="dropoff_lat" id="dropoff_lat" value="<?php echo $tpl['arr']['dropoff_lat'];?>">
            			<input type="hidden" name="dropoff_lng" id="dropoff_lng" value="<?php echo $tpl['arr']['dropoff_lng'];?>">			
            			<input type="hidden" name="dropoff_type" id="dropoff_type" value="<?php echo $tpl['arr']['dropoff_type'];?>">
            			
            			<input type="hidden" name="pickup_is_airport" id="pickup_is_airport" value="<?php echo $tpl['arr']['pickup_is_airport'];?>">
            			<input type="hidden" name="dropoff_is_airport" id="dropoff_is_airport" value="<?php echo $tpl['arr']['dropoff_is_airport'];?>">		
            				
            			<input type="hidden" name="custom_dropoff_id" id="custom_dropoff_id" value="<?php echo $tpl['arr']['dropoff_id'];?>">
            			<input type="hidden" name="custom_dropoff_place_id" id="custom_dropoff_place_id" value="<?php echo $tpl['arr']['dropoff_place_id'];?>">		
            				
            			<input type="hidden" name="duration" id="duration" value="<?php echo $tpl['arr']['duration'];?>">
            			<input type="hidden" name="distance" id="distance" value="<?php echo $tpl['arr']['distance'];?>">
            			
            			<input type="hidden" name="station_id" id="distance" value="<?php echo $tpl['arr']['station_id'];?>">
            			<input type="hidden" name="station_fee" id="station_fee" value="<?php echo $tpl['arr']['station_fee'];?>">
            			<input type="hidden" name="price_by_distance" id="price_by_distance" value="<?php echo $tpl['arr']['price_by_distance'];?>">
            	<?php else: ?>
            		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateBooking" enctype="multipart/form-data">
            		<input type="hidden" name="booking_create" value="1" />
            		<input type="hidden" name="price_by_distance" id="price_by_distance" value="<?php echo isset($tpl['arr']['price_by_distance']) && !empty($tpl['arr']['price_by_distance']) ? $tpl['arr']['price_by_distance'] : 'F';?>">
            	<?php endif; ?>
				<div class="bs-loader"></div>
				<div class="float_right w500">
					<p>
						<label class="title"><?php __('lblIpAddress'); ?>:</label>
						<label class="content"><?php echo $tpl['arr']['ip']; ?></label>
					</p>
					<p>
						<label class="title"><?php __('lblCreatedOn'); ?>:</label>
						<label class="content">
							<?php
								if (empty($_GET['copy'])) {
									echo pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['created'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['created'])), 'H:i:s', $tpl['option_arr']['o_time_format']);
								}
								else {
									$dt = new DateTime();
									echo $dt->format(sprintf('%s %s', $tpl['option_arr']['o_date_format'], $tpl['option_arr']['o_time_format']));
								}
								?>
						</label>
					</p>
					<p>
						<label class="title"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionResend&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblResendConfirmation'); ?></a></label>
						<?php
						if(!empty($tpl['arr']['c_phone']))
						{ 
							?><label class="content"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSendSms&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblSendSMSNotification'); ?></a></label><?php
						} 
						?>
					</p>
					<p>
						<label class="title"><?php __('lblDuration'); ?>:</label>
						<label id="tr_duration" class="content"><?php echo !empty($tpl['arr']['duration']) ? $tpl['arr']['duration'] . ' ' . strtolower(__('lblMinutes', true, false)) : null; ?></label>
					</p>
					<p>
						<label class="title"><?php __('lblDistance'); ?>:</label>
						<label id="tr_distance" class="content"><?php echo !empty($tpl['arr']['distance']) ? $tpl['arr']['distance'] . ' ' . strtolower(__('lblKm', true, false)) : null; ?></label>
					</p>
					<p>
						<label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPrint&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblPrintReservation'); ?></a></label>
					</p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailPaymentConfirmation&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblSendPaymentConfirmation'); ?></a></label>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailPaymentLink&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblSendPaymentLink'); ?></a></label>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailCancellation&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblSendCancellationEmail'); ?></a></label>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailReminder&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblRemindClientViaEmail'); ?></a></label>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSmsReminder&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblRemindClientViaSMS'); ?></a></label>
                    </p>
                    <?php if(!empty($tpl['arr']['return_date'])): ?>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailReturnReminder&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblRemindClientForReturnViaEmail'); ?></a></label>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionSmsReturnReminder&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblRemindClientForReturnViaSMS'); ?></a></label>
                    </p> 
                    <?php endif; ?>
                    <p style="display: none;">
                        <label class="title"><?php __('lblDriver'); ?>:</label>
                        <select name="driver_id" id="driver_id" class="pj-form-field w300">
                            <option value="">-- <?php __('lblChoose'); ?>--</option>
                            <?php foreach($tpl['driver_arr'] as $k => $v): ?>
                                <option value="<?php echo $v['id'];?>" <?php echo $tpl['arr']['driver_id'] == $v['id'] ? ' selected="selected"' : null;?>><?= "{$v['fname']} {$v['lname']} ({$v['email']})";?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailRating&amp;id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblSendRatingEmail'); ?></a></label>
                    </p>
					<?php if (empty($_GET['copy'])): ?>
						<p>
							<label class="title">
								<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $tpl['arr']['id']; ?>&copy=1" target="_blank"><?php echo __('lblBookingDuplicate'); ?></a>
							</label>
						</p>
					<?php endif; ?>

					<?php if (!empty($tpl['return_arr'])): ?>
						<p>
							<label class="title" style="width: 100%;"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionPrintSingle&details&record=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php __('lblPrintReservationDetailsSingle2'); ?></a></label>
						</p>
						<p>
							<label class="title" style="width: 100%;"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionPrintSingle&details&record=<?php echo $tpl['return_arr']['id']; ?>" target="_blank"><?php __('lblPrintReservationDetailsSingle3'); ?></a></label>
						</p>
					<?php endif; ?>
					
					<?php foreach ($tpl['email_theme_arr'] as $et) { ?>
                    	<p>
                            <label class="title" style="width: 100%;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCustomEmail&amp;id=<?php echo $et['id'];?>&amp;booking_id=<?php echo $tpl['arr']['id']; ?>" target="_blank"><?php echo pjSanitize::html($et['name']); ?></a></label>
                        </p>
                    <?php } ?>

				</div>
				<p>
					<label class="title"><?php __('lblUniqueID');?>:</label>
					<span class="inline-block">
						<?php if (empty($_GET['copy'])): ?>
							<input type="text" id="uuid" name="uuid" value="<?php echo pjSanitize::clean($tpl['arr']['uuid']); ?>" class="pj-form-field w400"/>
						<?php else: ?>
							<input type="text" id="uuid" name="uuid" value="<?php echo time(); ?>" class="pj-form-field w400"/>
						<?php endif; ?>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDateTime'); ?>:</label>
					<span class="block overflow">
						<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" name="booking_date" class="pj-form-field pointer w120 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo $date_time; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
                <p>
                    <label class="title"><?php __('lblAcceptsSharedTrip'); ?>:</label>
						<span class="inline-block">
							<span class="block t6 b5">
								<input type="checkbox" id="accept_shared_trip" name="accept_shared_trip"<?= $tpl['arr']['accept_shared_trip']? ' checked="checked"': ''; ?> />
							</span>
						</span>
                </p>
				<?php
				if(!empty($tpl['arr']['return_date']))
				{ 
					$return_date_time = pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['return_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['return_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);
					?>
					<p>
						<label class="title"><?php __('lblReturnTrip'); ?>:</label>
						<span class="inline-block">
							<span class="block t6 b5">
								<input type="checkbox" id="has_return" name="has_return" checked="checked" />
							</span>
						</span>
					</p>
					<p id="return_date_outer">
						<label class="title"><?php __('lblReturnDateTime'); ?>:</label>
						<span class="inline-block">
							<span class="pj-form-field-custom pj-form-field-custom-after r5">
								<input type="text" name="return_date" class="pj-form-field pointer w120 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo $return_date_time; ?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</span>
					</p>
					<?php
				}else{
					?>
					<p>
						<label class="title"><?php __('lblReturnTrip'); ?>:</label>
						<span class="inline-block">
							<span class="block t6 b5">
								<input type="checkbox" id="has_return" name="has_return"/>
							</span>
						</span>
					</p>
					<p id="return_date_outer" style="display:none;">
						<label class="title"><?php __('lblReturnDateTime'); ?>:</label>
						<span class="inline-block">
							<span class="pj-form-field-custom pj-form-field-custom-after r5">
								<input type="text" name="return_date" id="return_date" class="pj-form-field pointer w120 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</span>
					</p>
					<?php
				} 
				?>
				<p>
					<label class="title"><?php __('lblPickupLocation'); ?>:</label>
					<span class="inline-block">
						<select name="location_id" id="location_id" class="pj-form-field w500 required">
							<option value="server~::~0">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['pickup_arr'] as $k => $v)
							{
								?><option value="server~::~<?php echo $v['id'];?>" data-icon="<?php echo $v['icon']; ?>" data-is-airport="<?php echo (int) $v['is_airport']; ?>"<?php echo $tpl['arr']['location_id'] == $v['id'] ? ' selected="selected"' : null;?>><?php echo $v['pickup_location'];?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblDropoffLocation'); ?>:</label>
					<span id="trDropoffContainer" class="inline-block">
						<select name="dropoff_id" id="dropoff_id" class="pj-form-field w500 required">
							<option value="server~::~0">-- <?php __('lblChoose'); ?>--</option>							
							<?php
							foreach($tpl['dropoff_place_arr'] as $k => $v)
							{
								?><option value="server~::~<?php echo $v['id'];?>~::~<?php echo $v['dropoff_id'];?>" <?php echo $tpl['arr']['dropoff_place_id'] == $v['id'] ? 'selected="selected"' : null;?> data-icon="<?php echo $v['icon']; ?>"><?php echo pjSanitize::html($v['text']);?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblFleet'); ?>:</label>
					<span class="inline-block">
						<select name="fleet_id" id="fleet_id" class="pj-form-field w500 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							$max_passengers = '';
							foreach($tpl['fleet_arr'] as $k => $v)
							{
								if($tpl['arr']['fleet_id'] == $v['id'])
								{
									$max_passengers = !empty($v['passengers']) ? $v['passengers'] : '';

									?><option value="<?php echo $v['id'];?>" selected="selected" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>"><?php echo pjSanitize::html($v['fleet'] .' / '.$v['station_name']);?></option><?php
								}else{
									?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>"><?php echo pjSanitize::html($v['fleet'] .' / '.$v['station_name']);?></option><?php
								}
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPassengers'); ?>:</label>
					<span class="inline-block">
						<input type="text" id="passengers" name="passengers" class="pj-form-field field-int w80 required pj-positive-number" value="<?php echo $tpl['arr']['passengers'];?>" data-value="<?php echo $max_passengers;?>"/>
						<span id="tr_max_passengers"><?php echo $max_passengers != '' ? '('.__('lblMaximum', true, false).' '.$max_passengers.')' : null;?></span>
					</span>
				</p>
				
				<p class="trReturnDetails" style="display: <?php echo !empty($tpl['arr']['return_date']) ? null : 'none';?>">
					<label class="title"><?php __('lblPassengersReturn'); ?>:</label>
					<span class="inline-block">
						<input type="text" id="passengers_return" name="passengers_return" class="pj-form-field field-int w80 required pj-positive-number" value="<?php echo @$tpl['return_arr']['passengers'];?>" data-value="<?php echo $max_passengers;?>"/>
						<span id="tr_max_passengers_return"><?php echo $max_passengers != '' ? '('.__('lblMaximum', true, false).' '.$max_passengers.')' : null;?></span>
					</span>
				</p>
				
                <p>
                    <label class="title"><?php __('front_discount_code'); ?></label>
					<span class="inline-block">
						<input type="text" name="voucher_code" id="voucher_code" value="<?php echo pjSanitize::clean($tpl['arr']['voucher_code']); ?>" class="pj-form-field w400"/>
					</span>
                </p>
                <div class="float_left w350">
                	<?php 
                	$total_extra_price = (float)@$tpl['arr']['extra_price'] + (float)@$tpl['return_arr']['extra_price'];
                	?>
                	<p>
						<label class="title"><?php __('lblPriceExtra'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="total_extra_price" name="total_extra_price" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($total_extra_price); ?>"/>
							<input type="hidden" id="extra_price_first_transfe" name="extra_price_first_transfe" class="pj-form-field number w108" value="<?php echo pjSanitize::clean((float)@$tpl['arr']['extra_price']); ?>"/>
							<input type="hidden" id="extra_price_return_transfe" name="extra_price_return_transfe" class="pj-form-field number w108" value="<?php echo pjSanitize::clean((float)@$tpl['return_arr']['extra_price']); ?>"/>
						</span>
					</p>
                	<p>
						<label class="title"><?php __('lblSubTotal'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="sub_total" name="sub_total" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['sub_total']); ?>"/>
						</span>
					</p>
					<p<?= $tpl['option_arr']['o_tax_payment'] > 0 || $tpl['arr']['tax'] > 0? null: ' style="display: none;"' ?>>
						<label class="title"><?php __('lblTax'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="tax" name="tax" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['tax']); ?>" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
						</span>
					</p>
	                <p>
	                    <label class="title"><?php __('voucher_discount'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="discount" name="discount" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['discount']); ?>"/>
						</span>
	                </p>
	                <p>
						<label class="title"><?php __('lblCreditCardFee'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="credit_card_fee" name="credit_card_fee" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['credit_card_fee']); ?>"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblTotal'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="total" name="total" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['total']); ?>"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblDeposit'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['deposit']); ?>" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
						</span>
					</p>
                </div>
                <div class="float_left w350">
                	<div class="pjPriceOneway" style="display: <?php echo empty($tpl['arr']['return_date']) ? '' : 'none';?>">
                		<p>
							<label class="title"><?php __('lblPriceTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price" name="price" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['price']); ?>"/>
							</span>
						</p>
                	</div>
                	<div class="pjPriceRoundtrip" style="display: <?php echo empty($tpl['arr']['return_date']) ? 'none' : '';?>">
                		<p>
							<label class="title"><?php __('lblPriceFirstTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price_first_transfer" name="price_first_transfer" class="pj-form-field number w108" value="<?php echo pjSanitize::clean($tpl['arr']['price']); ?>"/>
							</span>
						</p>
						<p>
							<label class="title"><?php __('lblPriceReturnTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price_return_transfer" name="price_return_transfer" class="pj-form-field number w108" value="<?php echo pjSanitize::clean(@$tpl['return_arr']['price']); ?>"/>
							</span>
						</p>
                	</div>
                </div>
                <br class="clear_both"/>
				
				<p>
					<label class="title"><?php __('lblPaymentMethod');?></label>
					<span class="inline-block">
						<select name="payment_method" id="payment_method" class="pj-form-field w400 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('payment_methods', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['payment_method'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
                <p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
                    <label class="title"><?php __('lblCCOwner'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_owner" id="cc_owner" value="<?php echo pjSanitize::clean($tpl['arr']['cc_owner']); ?>" class="pj-form-field w400"/>
					</span>
                </p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCNum'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_num" id="cc_num" value="<?php echo pjSanitize::clean($tpl['arr']['cc_num']); ?>" class="pj-form-field w400"/>
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCExp'); ?></label>
					<span class="inline-block">
						<select name="cc_exp_month" class="pj-form-field">
							<option value="">---</option>
							<?php
							list($year, $month) = explode("-", $tpl['arr']['cc_exp']);
							$month_arr = __('months', true, false);
							ksort($month_arr);
							foreach ($month_arr as $key => $val)
							{
								?><option value="<?php echo $key;?>"<?php echo (int) $month == $key ? ' selected="selected"' : NULL; ?>><?php echo $val;?></option><?php
							}
							?>
						</select>
						<select name="cc_exp_year" class="pj-form-field">
							<option value="">---</option>
							<?php
							$y = (int) date('Y');
							for ($i = $y; $i <= $y + 10; $i++)
							{
								?><option value="<?php echo $i; ?>"<?php echo $year == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : 'block'; ?>">
					<label class="title"><?php __('lblCCCode'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_code" id="cc_code" value="<?php echo pjSanitize::clean($tpl['arr']['cc_code']); ?>" class="pj-form-field w100" />
					</span>
				</p>
				<div class="p">
					<label class="title"><?php __('lblStatus'); ?></label>
					<span class="inline-block">
						<select name="status" id="status" class="pj-form-field w400 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('booking_statuses', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</div>
				
				<div class="p pjStatusReturnTrip trReturnDetails" style="display: <?php echo !empty($tpl['arr']['return_date']) ? null : 'none';?>">
					<label class="title"><?php __('lblStatusReturnTrip'); ?></label>
					<span class="inline-block">
						<select name="status_return_trip" id="status_return_trip" class="pj-form-field w400 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('booking_statuses', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo $k == @$tpl['return_arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</div>			

                <div class="float_left" style="width: 50%;">
                    <?php foreach($tpl['extra_arr'] as $index => $extra): ?>
                        <div class="p orderExtras">
                            <label class="title"><?= $index == 0? __('menuExtras', true): '&nbsp;'; ?></label>
                            <span class="inline-block">
                                <?php if (!empty($extra['image_path'])) { ?>
                            		<span class="w150" style="display: inline-block;"><?= $extra['name'] ?> <img alt="" src="<?php echo $extra['image_path'];?>"></span>
                            	<?php } else { ?>
                                	<span class="w150" style="display: inline-block;"><?= $extra['name'] ?></span>
                                <?php } ?>
                                <select name="extras[<?= $extra['id'] ?>]" id="extra_<?= $extra['id'] ?>" class="pj-form-field w50 pjExtraSelector">
                                    <?php for($i = 0; $i <= $tpl['option_arr']['o_extras_max_qty']; $i++): ?>
                                        <option value="<?php echo $i; ?>"<?php echo isset($tpl['arr']['extra_arr'][$extra['id']]) && $i == $tpl['arr']['extra_arr'][$extra['id']] ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <?php 
                                if ((float)$extra['price'] > 0) {
                                	echo ' x '.number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];
                                } else {
                                	echo __('front_label_free', true);
                                } 
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="float_left">
                	<div class="p">
                        <label class="title" style="float: none; padding-bottom: 5px;"><?php __('lblNoteForSupportTeam'); ?></label>
                        <span class="inline-block">
                            <textarea name="notes_for_support" id="notes_for_support" class="pj-form-field w300 h300"><?php echo stripslashes($tpl['arr']['notes_for_support']); ?></textarea>
                        </span>
                    </div>
                </div>
                <br class="clear_both" /> 
                   
                
                <div class="pjSbReturnExtras" style="display: <?php echo $tpl['return_arr'] ? '' : 'none';?>">
                	<div class="p">&nbsp;</div>
	                <?php foreach($tpl['extra_arr'] as $index => $extra): ?>
	                    <div class="p">
	                        <label class="title"><?= $index == 0? __('menuReturnExtras', true): '&nbsp;'; ?></label>
	                        <span class="inline-block">
	                            <span class="w150" style="display: inline-block;"><?= $extra['name'] ?></span>
	                            <select name="extras_return[<?= $extra['id'] ?>]" id="extra_return_<?= $extra['id'] ?>" class="pj-form-field w50 pjExtraSelector">
	                                <?php for($i = 0; $i <= $tpl['option_arr']['o_extras_max_qty']; $i++): ?>
	                                    <option value="<?php echo $i; ?>"<?php echo isset($tpl['return_arr']['extra_arr'][$extra['id']]) && $i == $tpl['return_arr']['extra_arr'][$extra['id']] ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option>
	                                <?php endfor; ?>
	                            </select>
	                            <?php 
	                            if ((float)$extra['price'] > 0) {
	                            	echo ' x '.number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];
	                            } else {
	                            	echo __('front_label_free', true);
	                            } 
	                            ?>
	                        </span>
	                    </div>
	                <?php endforeach; ?>
	           	</div>

                <div class="p">&nbsp;</div>
                <div class="p">
                    <label class="title"><?php __('front_step_passenger_details'); ?></label>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingTitle'); ?>:</label>
                    <span class="inline-block">
                        <select name="c_title" id="c_title" class="pj-form-field w400 required">
                            <option value="">-- <?php __('lblChoose'); ?>--</option>
                            <?php
                            $title_arr = pjUtil::getTitles();
                            $name_titles = __('personal_titles', true, false);
                            foreach ($title_arr as $v)
                            {
                                ?><option value="<?php echo $v; ?>"<?php echo $tpl['arr']['c_title'] == $v ? ' selected="selected"' : NULL; ?>><?php echo $name_titles[$v]; ?></option><?php
                            }
                            ?>
                        </select>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingFname'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_fname" id="c_fname" class="pj-form-field w400 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_fname'])); ?>" />
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingLname'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_lname" id="c_lname" class="pj-form-field w400 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_lname'])); ?>" />
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingEmail'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_email" id="c_email" class="pj-form-field w400 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_email'])); ?>" />
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingCountry'); ?>:</label>
                    <span class="inline-block">
                        <select name="c_country" id="c_country" class="pj-form-field w400 required">
                            <option value="" data-code="">-- <?php __('lblChoose'); ?>--</option>
                            <?php
                            foreach ($tpl['country_arr'] as $v)
                            {
                                ?><option value="<?php echo $v['id']; ?>" data-code="<?= $v['code'] ?>"<?php echo $tpl['arr']['c_country'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['country_title']); ?></option><?php
                            }
                            ?>
                        </select>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingPhone'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_dialing_code" id="c_dialing_code" class="pj-form-field w40 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_dialing_code'])); ?>" />
                        <input type="text" name="c_phone" id="c_phone" class="pj-form-field w130 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_phone'])); ?>" />
                    </span>
                </div>

				<div class="p">
					<label for="customized_name_plate" class="title"><?php __('lblBookingCustomizedNamePlate'); ?></label>
					<span class="inline-block">
						<input type="text" name="customized_name_plate" id="customized_name_plate" class="pj-form-field w400" value="<?php echo $tpl['arr']['customized_name_plate']; ?>">
					</span>
				</div>
				
				<p>
					<label class="title"><?php __('lblUploadNameSign'); ?></label>
					<span class="inline_block">
						<input type="file" name="name_sign" id="name_sign" class="pj-form-field w400"/>
					</span>
				</p>
				<?php
				if(!empty($tpl['arr']['name_sign']) && is_file(PJ_INSTALL_PATH . $tpl['arr']['name_sign']))
				{
					?>
					<p class="deleteNameSignWrap">
						<label class="title">&nbsp;</label>
						<span class="inline_block">
							<span class="block b10"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionDownloadFile&amp;id=<?php echo $tpl['arr']['id'];?>"><?php echo pjSanitize::html(basename($tpl['arr']['name_sign']));?></a></span>
							<span class="block"><input type="button" value="<?php __('btnDelete', false, true); ?>" class="pj-button pjMLDeleteNameSign" data-id="<?php echo $tpl['arr']['id'];?>" /></span>
						</span>
					</p>
					<?php
				}
				?>

                <div class="p">&nbsp;</div>
                <div class="p">
                    <label class="title"><?php __('front_transfer_details'); ?></label>
                </div>
                
                 <div id="departure_info_is_airport_2" style="display: <?= !$is_airport && $tpl['arr']['dropoff_is_airport'] == 0 ? 'block': 'none'; ?>">
                    <div class="p">
                        <label class="title"><?php __('lblPickupAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="cl_address" id="cl_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_address'])); ?>"/>
						</span>
                    </div>
					<div class="p">
	                    <label class="title"><?php __('lblBookingPickupGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    	<input type="text" name="pickup_google_map_link" id="pickup_google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['pickup_google_map_link'])); ?>"/>
	                    </span>
	                </div>
                    <div class="p">
                        <label class="title"><?php __('lblDropAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="cl_destination_address" id="cl_destination_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_destination_address'])); ?>"/>
						</span>
                    </div>
                    <div class="p">
	                    <label class="title"><?php __('lblBookingDropoffGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    	<input type="text" name="dropoff_google_map_link" id="dropoff_google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['dropoff_google_map_link'])); ?>"/>
	                    </span>
	                </div>
                </div>
                

                <div id="departure_info_is_airport_1" style="display: <?= $is_airport && $tpl['arr']['dropoff_is_airport'] == 0? 'block': 'none'; ?>">
                    <div class="p">
                        <label class="title"><?php __('lblArrivalFlightNumber'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_flight_number" id="c_flight_number" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_flight_number'])); ?>"/>
						</span>
                    </div>

                    <div class="p">
                        <label class="title"><?php __('lblBookingAirlineCompany'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_airline_company" id="c_airline_company" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_airline_company'])); ?>"/>
						</span>
                    </div>

                    <div class="p">
                        <label class="title"><?php __('lblBookingDestAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_destination_address" id="c_destination_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_destination_address'])); ?>"/>
						</span>
                    </div>
                </div>

                <div id="departure_info_is_airport_0" style="display: <?= !$is_airport && $tpl['arr']['dropoff_is_airport'] == 1? 'block': 'none'; ?>">
                    <div class="p">
                        <label class="title"><?php __('front_address'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_address" id="c_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_address'])); ?>"/>
						</span>
                    </div>

                    <div class="p">
                        <?php
                        if(!$is_airport && !empty($tpl['arr']['c_departure_flight_time']))
                        {
                            list($c_departure_flight_time_h, $c_departure_flight_time_m) = explode(':', $tpl['arr']['c_departure_flight_time']);
                        }
                        ?>
                        <label class="title"><?php __('lblFlightDepartureTime'); ?>:</label>
                        <span class="inline-block">
                            <select name="c_departure_flight_time_h" id="c_departure_flight_time_h" class="pj-form-field w60 required">
                                <option value="">Hh</option>
                                <?php for($i = 0; $i <= 23; $i++): ?>
                                    <option value="<?= $i ?>"<?= isset($c_departure_flight_time_h) && $c_departure_flight_time_h == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="c_departure_flight_time_m" id="c_departure_flight_time_m" class="pj-form-field w60 required">
                                <option value="">Mm</option>
                                <?php for($i = 0; $i <= 55; $i += 5): ?>
                                    <option value="<?= $i ?>"<?= isset($c_departure_flight_time_m) && $c_departure_flight_time_m == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </div>
                </div>

                <div class="p pjHotelName" style="display: <?= $is_airport || $tpl['arr']['dropoff_is_airport'] == 1? 'block': 'none'; ?>">
                    <label class="title"><?php __('front_hotel'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_hotel" id="c_hotel" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_hotel'])); ?>"/>
                    </span>
                </div>
				
				<div class="p pjHotelName" style="display: <?= $is_airport || $tpl['arr']['dropoff_is_airport'] == 1? 'block': 'none'; ?>">
                    <label class="title"><?php __('lblBookingGoogleMapsLink'); ?>:</label>
                    <span class="inline-block">
                    	<input type="text" name="google_map_link" id="google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['google_map_link'])); ?>"/>
                    </span>
                </div>
                
                <div class="p">
                    <label class="title"><?php __('lblBookingNotes'); ?></label>
                    <span class="inline-block">
                        <textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120"><?php echo stripslashes($tpl['arr']['c_notes']); ?></textarea>
                    </span>
                </div>
                <div class="p">
                    <label class="title"><?php __('lblInternalNotes'); ?></label>
                    <span class="inline-block">
                        <textarea name="internal_notes" id="internal_notes" class="pj-form-field w500 h120"><?php echo stripslashes($tpl['arr']['internal_notes']); ?></textarea>
                    </span>
                </div>

                <div class="trReturnDetails" style="display: <?= !empty($tpl['return_arr'])? 'block': 'none'; ?>">
                    <div class="p">&nbsp;</div>
                    <div class="p">
                        <label class="title"><?php __('front_return_transfer_details'); ?></label>
                    </div>

					<div id="return_info_is_airport_2" style="display: <?= !$is_airport && $tpl['arr']['dropoff_is_airport'] == 0 ? 'block': 'none'; ?>">
                        <div class="p">
                            <label class="title"><?php __('lblPickupAddress'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_cl_address" id="return_cl_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['c_address'])); ?>"/>
                            </span>
                        </div>
						<div class="p">
		                    <label class="title"><?php __('lblBookingPickupGoogleMapsLink'); ?>:</label>
		                    <span class="inline-block">
		                    	<input type="text" name="return_pickup_google_map_link" id="return_pickup_google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['pickup_google_map_link'])); ?>"/>
		                    </span>
		                </div>
                        <div class="p">
                            <label class="title"><?php __('lblDropAddress'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_cl_destination_address" id="return_cl_destination_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['c_destination_address'])); ?>"/>
                            </span>
                        </div>
                        <div class="p">
		                    <label class="title"><?php __('lblBookingDropoffGoogleMapsLink'); ?>:</label>
		                    <span class="inline-block">
		                    	<input type="text" name="return_dropoff_google_map_link" id="return_dropoff_google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['dropoff_google_map_link'])); ?>"/>
		                    </span>
		                </div>
                    </div>
                    
                    <div id="return_info_is_airport_1" style="display: <?= $is_airport && $tpl['arr']['dropoff_is_airport'] == 0 ? 'block': 'none'; ?>">
                        <div class="p">
                            <label class="title"><?php __('front_address'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_address" id="return_c_address" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['c_address'])); ?>"/>
                            </span>
                        </div>

                        <div class="p">
                            <?php
                            if($is_airport && !empty($tpl['return_arr']['c_departure_flight_time']))
                            {
                                list($return_c_departure_flight_time_h, $return_c_departure_flight_time_m) = explode(':', $tpl['return_arr']['c_departure_flight_time']);
                            }
                            ?>
                            <label class="title"><?php __('lblFlightDepartureTime'); ?>:</label>
                            <span class="inline-block">
                                <select name="return_c_departure_flight_time_h" id="return_c_departure_flight_time_h" class="pj-form-field w60 required">
                                    <option value="">Hh</option>
                                    <?php for($i = 0; $i <= 23; $i++): ?>
                                        <option value="<?= $i ?>"<?= isset($return_c_departure_flight_time_h) && $return_c_departure_flight_time_h == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="return_c_departure_flight_time_m" id="return_c_departure_flight_time_m" class="pj-form-field w60 required">
                                    <option value="">Mm</option>
                                    <?php for($i = 0; $i <= 55; $i += 5): ?>
                                        <option value="<?= $i ?>"<?= isset($return_c_departure_flight_time_m) && $return_c_departure_flight_time_m == $i? ' selected="selected"': null; ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </span>
                        </div>
                    </div>

                    <div id="return_info_is_airport_0" style="display: <?= !$is_airport && $tpl['arr']['dropoff_is_airport'] == 1? 'block': 'none'; ?>">
                        <div class="p">
                            <label class="title"><?php __('lblArrivalFlightNumber'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_flight_number" id="return_c_flight_number" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['c_flight_number'])); ?>"/>
                            </span>
                        </div>

                        <div class="p">
                            <label class="title"><?php __('lblBookingAirlineCompany'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_airline_company" id="return_c_airline_company" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['c_airline_company'])); ?>"/>
                            </span>
                        </div>
                    </div>
                    <div class="p pjHotelName" style="display: <?= $is_airport || $tpl['arr']['dropoff_is_airport'] == 1? 'block': 'none'; ?>">
	                    <label class="title"><?php __('lblBookingGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    <input type="text" name="return_google_map_link" id="return_google_map_link" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes(@$tpl['return_arr']['google_map_link'])); ?>"/>
	                    </span>
	                </div>
                    <div class="p">
                        <label class="title"><?php __('lblBookingNotes'); ?></label>
                        <span class="inline-block">
                            <textarea name="return_c_notes" id="return_c_notes" class="pj-form-field w500 h120"><?php echo stripslashes(@$tpl['return_arr']['c_notes']); ?></textarea>
                        </span>
                    </div>
                    <div class="p">
	                    <label class="title"><?php __('lblInternalNotes'); ?></label>
	                    <span class="inline-block">
	                        <textarea name="return_internal_notes" id="return_internal_notes" class="pj-form-field w500 h120"><?php echo stripslashes(@$tpl['return_arr']['internal_notes']); ?></textarea>
	                    </span>
	                </div>
                </div>
                
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</form>
		</div>
		<?php if (empty($_GET['copy'])) { ?>
			<div id="tabs-2">
    			<?php
				if (pjObject::getPlugin('pjInvoice') !== NULL)
				{
					?>
					
					<input type="button" class="pj-button btnCreateInvoice" value="<?php __('booking_create_invoice'); ?>" />
					
					<div id="grid_invoices" class="t10 b10"></div>
				
					<?php
				}
				?>
    		</div>
    		<div id="tabs-3">
    			<div id="grid_history" class="pj-grid-bookings"></div>
    		</div>
    	<?php } ?>
	</div>
	
	<div id="dialogDeleteNameSign" title="<?php __('lblDeleteNameSign'); ?>" style="display: none">
		<?php __('lblDeleteNameSignDesc'); ?>
	</div>
	
	<?php
	if (pjObject::getPlugin('pjInvoice') !== NULL)
	{
	    $map = array(
	        'confirmed' => 'paid',
	        'cancelled' => 'cancelled',
	        'in_progress' => 'not_paid',
	        'passed_on' => 'not_paid',
	        'pending' => 'not_paid'
	    );
	    $arr = $tpl['booking_arr'];
	    $return_arr = $tpl['return_arr'];
	    if ($arr['status'] == 'confirmed' && !in_array($arr['payment_method'], array('creditcard_later', 'cash'))) {
	        $paid_deposit = (float)$arr['deposit'];
	        $amount_due = (float)$arr['total'] - $paid_deposit;
	    } else {
	        $paid_deposit = 0;
	        $amount_due = (float)$arr['total'];
	    }
	    
	    $total_extra_price = $arr['extra_price'];
	    if ($return_arr) {
	        $total_extra_price += $return_arr['extra_price'];
	    }
	    
	    $sub_total_before_tax = pjAppController::getPriceBeforeTax($arr['sub_total'], $tpl['tax_percentage']);
	    $tax = round((float)$arr['sub_total'] - (float)$sub_total_before_tax, 2, PHP_ROUND_HALF_UP);
	    $idx = 0;
		?>
		<form action="<?php echo PJ_INSTALL_URL; ?>index.php" method="get" target="_blank" style="display: inline" id="frmCreateInvoice">
			<input type="hidden" name="controller" value="pjInvoice" />
			<input type="hidden" name="action" value="pjActionCreateInvoice" />
			<input type="hidden" name="tmp" value="<?php echo md5(uniqid(rand(), true)); ?>" />
			<input type="hidden" name="uuid" value="<?php echo pjUtil::uuid(); ?>" />
			<input type="hidden" name="order_id" value="<?php echo pjSanitize::html($arr['uuid']); ?>" />
			<input type="hidden" name="issue_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="due_date" value="<?php echo date('Y-m-d'); ?>" />
			<input type="hidden" name="status" value="<?php echo @$map[$arr['status']]; ?>" />
			<input type="hidden" name="subtotal" value="<?php echo $sub_total_before_tax; ?>" />
			<input type="hidden" name="discount" value="<?php echo $arr['discount']; ?>" />
			<input type="hidden" name="voucher_code" value="<?php echo $arr['voucher_code']; ?>" />
			<input type="hidden" name="tax" value="<?php echo $tax; ?>" />
			<input type="hidden" name="shipping" value="0.00" />
			<input type="hidden" name="total" value="<?php echo $arr['total']; ?>" />
			<input type="hidden" name="paid_deposit" value="<?php echo $paid_deposit;?>" />
			<input type="hidden" name="amount_due" value="<?php echo $amount_due;?>" />
			<input type="hidden" name="currency" value="<?php echo pjSanitize::html($tpl['option_arr']['o_currency']); ?>" />
			<input type="hidden" name="notes" value="<?php echo pjSanitize::html($arr['c_notes']); ?>" />
			<input type="hidden" name="b_billing_address" value="<?php echo pjSanitize::html($arr['c_address']); ?>" />
			<input type="hidden" name="b_name" value="<?php echo pjSanitize::html($arr['c_fname'].' '.$arr['c_lname']); ?>" />
			<input type="hidden" name="b_address" value="<?php echo pjSanitize::html($arr['c_address']); ?>" />
			<input type="hidden" name="b_street_address" value="" />
			<input type="hidden" name="b_city" value="<?php echo pjSanitize::html($arr['c_city']); ?>" />
			<input type="hidden" name="b_state" value="<?php echo pjSanitize::html($arr['c_state']); ?>" />
			<input type="hidden" name="b_zip" value="<?php echo pjSanitize::html($arr['c_zip']); ?>" />
			<input type="hidden" name="b_country" value="<?php echo pjSanitize::html($arr['c_country']); ?>" />
			<input type="hidden" name="b_phone" value="<?php echo pjSanitize::html($arr['c_dialing_code'].$arr['c_phone']); ?>" />
			<input type="hidden" name="b_email" value="<?php echo pjSanitize::html($arr['c_email']); ?>" />
			<input type="hidden" name="b_url" value="" />
			<input type="hidden" name="s_shipping_address" value="<?php echo $arr['c_destination_address']; ?>" />
			<input type="hidden" name="s_name" value="<?php echo pjSanitize::html($arr['c_fname'].' '.$arr['c_lname']); ?>" />
			<input type="hidden" name="s_address" value="<?php echo pjSanitize::html($arr['c_destination_address']); ?>" />
			<input type="hidden" name="s_street_address" value="" />
			<input type="hidden" name="s_city" value="<?php echo pjSanitize::html($arr['c_city']); ?>" />
			<input type="hidden" name="s_state" value="<?php echo pjSanitize::html($arr['c_state']); ?>" />
			<input type="hidden" name="s_zip" value="<?php echo pjSanitize::html($arr['c_zip']); ?>" />
			<input type="hidden" name="s_phone" value="<?php echo pjSanitize::html($arr['c_dialing_code'].$arr['c_phone']); ?>" />
			<input type="hidden" name="s_email" value="<?php echo pjSanitize::html($arr['c_email']); ?>" />
			<input type="hidden" name="s_url" value="" />
			
			<?php
			$items = array();
			$car_info_arr = array();
			$car_info_arr[] = __('front_vehicle', true).': '.pjSanitize::html($arr['fleet']);
			$car_info_arr[] = __('front_date', true).': '.date($tpl['option_arr']['o_date_format'].', '.$tpl['option_arr']['o_time_format'], strtotime($arr['booking_date']));
			if (!empty($arr['return_date'])) {
			    $car_info_arr[] = __('booking_return_on', true).': '.date($tpl['option_arr']['o_date_format'].', '.$tpl['option_arr']['o_time_format'], strtotime($arr['return_date']));
			}
			$car_info_arr[] = __('front_cart_from', true).': '.pjSanitize::html($arr['location']);
			$car_info_arr[] = __('front_cart_to', true).': '.pjSanitize::html($arr['dropoff']);
			?>
			<input type="hidden" name="items[<?php echo $idx; ?>][name]" value="<?php __('front_invoice_booking_details', true); ?>" />
			<input type="hidden" name="items[<?php echo $idx; ?>][description]" value="<?php echo implode("\r\n", $car_info_arr); ?>" />
			<input type="hidden" name="items[<?php echo $idx; ?>][qty]" value="1" />
			<input type="hidden" name="items[<?php echo $idx; ?>][unit_price]" value="<?php echo $arr['sub_total'] - (float)$total_extra_price; ?>" />
			<input type="hidden" name="items[<?php echo $idx; ?>][amount]" value="<?php echo $arr['sub_total'] - (float)$total_extra_price; ?>" />
			<input type="hidden" name="items[<?php echo $idx; ?>][tax_id]" value="<?php echo $tpl['tax_id']; ?>" />
			<?php 
			if ($tpl['booking_extra_arr']) {
			    foreach($tpl['booking_extra_arr'] as $extra)
			    {
			        $idx++;
			        ?>
			        <input type="hidden" name="items[<?php echo $idx; ?>][name]" value="<?php echo $extra['quantity'].' x '.pjSanitize::html(strip_tags($extra['name'])); ?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][description]" value="<?php echo pjSanitize::html($extra['info']); ?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][qty]" value="<?php echo $extra['quantity'];?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][unit_price]" value="<?php echo $extra['price'];?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][amount]" value="<?php echo $extra['quantity'] * $extra['price'];?>" />
			        <?php
			    }
			}
			if ($tpl['return_booking_extra_arr']) {
			    foreach($tpl['return_booking_extra_arr'] as $extra)
			    {
			        $idx++;
			        ?>
			        <input type="hidden" name="items[<?php echo $idx; ?>][name]" value="<?php echo $extra['quantity'].' x '.pjSanitize::html(strip_tags($extra['name'])).' ('.__('front_invoice_return', true).')'; ?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][description]" value="<?php echo pjSanitize::html($extra['info']); ?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][qty]" value="<?php echo $extra['quantity'];?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][unit_price]" value="<?php echo $extra['price'];?>" />
        			<input type="hidden" name="items[<?php echo $idx; ?>][amount]" value="<?php echo $extra['quantity'] * $extra['price'];?>" />
			        <?php
			    }
			}
			if ((float)$arr['credit_card_fee'] > 0) {
			    $idx++;
			    ?>
			    <input type="hidden" name="items[<?php echo $idx; ?>][name]" value="<?php __('front_invoice_credit_card_fee', true); ?>" />
    			<input type="hidden" name="items[<?php echo $idx; ?>][description]" value="" />
    			<input type="hidden" name="items[<?php echo $idx; ?>][qty]" value="1" />
    			<input type="hidden" name="items[<?php echo $idx; ?>][unit_price]" value="<?php echo (float)$extra['credit_card_fee'];?>" />
    			<input type="hidden" name="items[<?php echo $idx; ?>][amount]" value="<?php echo (float)$extra['credit_card_fee'];?>" />
			    <?php 
			}
			?>
		</form>
		<?php
	}
	?>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.maximum = '<?php echo __('lblMaximum', true, false)?>';
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	myLabel.max_number = "<?php __('lblMaxNumber'); ?>";
	myLabel.duplicated_id = "<?php __('lblDuplicatedUniqueID');?>",
	myLabel.loader = '<img src="<?php echo PJ_IMG_PATH;?>backend/pj-preloader.gif" />';
	myLabel.locations_empty = "<?php __('front_search_locations_empty', false, true); ?>";
	myLabel.searching = "<?php __('front_search_locations_searching', false, true); ?>";
	myLabel.btnDelete = "<?php __('btnDelete');?>";
	myLabel.btnCancel = "<?php __('btnCancel');?>";

	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "&booking_id=<?php echo $tpl['arr']['id'];?>";
	myLabel.h_content = "<?php __('lblBokingHistoryContent', false, true); ?>";
	myLabel.h_by = "<?php __('lblBokingHistoryBy', false, true); ?>";
	myLabel.h_created = "<?php __('lblBokingHistoryCreated', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";

	myLabel.num = "<?php __('plugin_invoice_i_num'); ?>";
	myLabel.order_id = "<?php __('plugin_invoice_i_order_id'); ?>";
	myLabel.issue_date = "<?php __('plugin_invoice_i_issue_date'); ?>";
	myLabel.due_date = "<?php __('plugin_invoice_i_due_date'); ?>";
	myLabel.created = "<?php __('plugin_invoice_i_created'); ?>";
	myLabel.status = "<?php __('plugin_invoice_i_status'); ?>";
	myLabel.total = "<?php __('plugin_invoice_i_total'); ?>";
	myLabel.delete_title = "<?php __('plugin_invoice_i_delete_title'); ?>";
	myLabel.delete_body = "<?php __('plugin_invoice_i_delete_body'); ?>";
	myLabel.paid = "<?php echo $statuses['paid']; ?>";
	myLabel.not_paid = "<?php echo $statuses['not_paid']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	myLabel.empty_date = "<?php __('gridEmptyDate'); ?>";
	myLabel.invalid_date = "<?php __('gridInvalidDate'); ?>";
	myLabel.empty_datetime = "<?php __('gridEmptyDatetime'); ?>";
	myLabel.invalid_datetime = "<?php __('gridInvalidDatetime'); ?>";
	myLabel.currency = "<?php echo $tpl['option_arr']['o_currency']; ?>";
	myLabel.currencysign = "<?php echo pjUtil::getCurrencySign($tpl['option_arr']['o_currency'], false); ?>";
	</script>
	<?php
}
?>