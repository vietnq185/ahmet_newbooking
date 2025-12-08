<?php 
$personal_titles = __('personal_titles', true);
$payment_methods = __('payment_methods', true);
?>
<div class="pjSbSummaryWrap">
	<div class="pjSbBoxWrap">
		<div class="pjSbProgress progress">
			<div class="progress-bar" style="width:100%"></div>
		</div> 
		<div class="alert alert-success d-flex align-items-center">
			<?php if ((int)$tpl['arr']['paid_via_payment_link'] == 1) { ?>
    			<i class="fad fa-check-circle"></i><span class="alert-desc"><span class="alert-title"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_2', true));?></span><br/><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_2_desc', true)); ?></span>
        	<?php } elseif ($hours < 24 || $tpl['arrivalNotice'] > 0 || $tpl['arr']['price_by_distance'] == 'T') { ?>
				<i class="fad fa-check-circle"></i><span class="alert-desc"><span class="alert-title"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary_1', true));?></span><br/><?php __('front_step_booking_summary_1_desc');?></span>
			<?php } else { ?>
				<i class="fad fa-check-circle"></i><span class="alert-desc"><span class="alert-title"><?php echo str_replace('{ReferenceNumber}', $tpl['arr']['uuid'], __('front_step_booking_summary', true));?></span><br/><?php __('front_step_booking_summary_desc');?></span>
			<?php } ?>   		
		</div>
		<h5><?php __('front_step_passenger_details'); ?></h5>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_name_surname'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo @$personal_titles[$tpl['arr']['c_title']].' '.pjSanitize::html($tpl['arr']['c_fname'] . ' ' . $tpl['arr']['c_lname']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_mobile_number'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_dialing_code'] . $tpl['arr']['c_phone']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_email'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_email']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_country'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['country_arr']['country_title']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_date'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['booking_date']));?></div>
		</div>
		
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_time'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_cart_from'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo $tpl['arr']['pickup_type'] == 'server' ? pjSanitize::html($tpl['pickup_arr']['pickup_location']) : pjSanitize::html($tpl['arr']['pickup_address']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_cart_to'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo $tpl['arr']['dropoff_type'] == 'server' ? pjSanitize::html($tpl['dropoff_arr']['place_name']) : pjSanitize::html($tpl['arr']['dropoff_address']);?></div>
		</div>
		<?php if($tpl['arr']['pickup_is_airport'] == 0 && $tpl['arr']['dropoff_is_airport'] == 0) { ?>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_pickup_address'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_address']);?></div>
			</div>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_dropoff_address'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_destination_address']);?></div>
			</div>
		<?php } else { ?>
			<?php if($tpl['arr']['pickup_is_airport'] == 1) { ?>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_flight_number'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_flight_number']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_airline_company'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_airline_company']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_destination_address'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_destination_address']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_hotel'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_hotel']);?></div>
				</div>
			<?php }  else { ?>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_address'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_address']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_hotel'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['c_hotel']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_flight_departure_time'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo !empty($tpl['arr']['c_departure_flight_time']) ? date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['c_departure_flight_time'])) : '';?></div>
				</div>
			<?php } ?>
		<?php } ?>		
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_passengers'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['arr']['passengers']);?></div>
		</div>
		<div class="row form-group d-flex align-items-center">
			<div class="col-md-3 col-sm-4 col-12"><?php __('front_vehicle'); ?></div>
			<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['fleet']['fleet']);?></div>
		</div>
		<?php if(!empty($tpl['arr']['c_notes'])) { ?>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_notes'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo nl2br($tpl['arr']['c_notes']);?></div>
			</div>
		<?php } ?>
		<?php if(!empty($tpl['extra_arr'])) { ?>
			<h5><?php __('front_extras'); ?></h5>
			 <?php foreach($tpl['extra_arr'] as $extra) { ?>
			 	<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12">
						<?php echo pjSanitize::html($extra['name']); ?>
						<?php if(!empty($extra['info'])) { ?>
							<div class="small"><i>(<?= $extra['info'] ?>)</i></div>
						<?php } ?>
					</div>
					<div class="col-md-9 col-sm-8 col-12">
						<?php if ((float)$extra['price'] > 0) { ?>
							<?php echo $extra['quantity'];?> x <?php echo number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>
						<?php } else { ?>
							<?php echo $extra['quantity'];?> (<?php __('front_label_free');?>)
						<?php } ?>
					</div>
				</div>
			 <?php } ?>
		<?php } ?>
		
		<?php if(!empty($tpl['return_arr'])) { ?>
			<h5><?php __('front_return_transfer_details'); ?></h5>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_date'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['return_date']));?></div>
			</div>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_time'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['return_date']));?></div>
			</div>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_cart_from'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo $tpl['arr']['dropoff_type'] == 'server' ? pjSanitize::html($tpl['dropoff_arr']['place_name']) : pjSanitize::html($tpl['arr']['dropoff_address']);?></div>
			</div>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_cart_to'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo $tpl['arr']['pickup_type'] == 'server' ? pjSanitize::html($tpl['pickup_arr']['pickup_location']) : pjSanitize::html($tpl['arr']['pickup_address']);?></div>
			</div>
			<?php if($tpl['arr']['pickup_is_airport'] == 0 && $tpl['arr']['dropoff_is_airport'] == 0) { ?>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_pickup_address'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['c_address']);?></div>
				</div>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_dropoff_address'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['c_destination_address']);?></div>
				</div>
			<?php } else { ?>
				<?php if($tpl['arr']['pickup_is_airport'] == 1) { ?>
					<div class="row form-group d-flex align-items-center">
						<div class="col-md-3 col-sm-4 col-12"><?php __('front_address'); ?></div>
						<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['c_address']);?></div>
					</div>
					<div class="row form-group d-flex align-items-center">
						<div class="col-md-3 col-sm-4 col-12"><?php __('front_flight_departure_time'); ?></div>
						<div class="col-md-9 col-sm-8 col-12"><?php echo !empty($tpl['return_arr']['c_departure_flight_time']) ? date($tpl['option_arr']['o_time_format'], strtotime($tpl['return_arr']['c_departure_flight_time'])) : '';?></div>
					</div>
				<?php } else { ?>
					<div class="row form-group d-flex align-items-center">
						<div class="col-md-3 col-sm-4 col-12"><?php __('front_flight_number'); ?></div>
						<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['c_flight_number']);?></div>
					</div>
					<div class="row form-group d-flex align-items-center">
						<div class="col-md-3 col-sm-4 col-12"><?php __('front_airline_company'); ?></div>
						<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['c_airline_company']);?></div>
					</div>
				<?php } ?>
			<?php } ?>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_passengers'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['return_arr']['passengers']);?></div>
			</div>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('front_vehicle'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo pjSanitize::html($tpl['fleet']['fleet']);?></div>
			</div>
			<?php if(!empty($tpl['arr']['c_notes'])) { ?>
				<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12"><?php __('front_notes'); ?></div>
					<div class="col-md-9 col-sm-8 col-12"><?php echo nl2br($tpl['return_arr']['c_notes']);?></div>
				</div>
			<?php } ?>
			<?php if(!empty($tpl['extra_return_arr'])) { ?>
			<h5><?php __('front_extras'); ?></h5>
			 <?php foreach($tpl['extra_return_arr'] as $extra) { ?>
			 	<div class="row form-group d-flex align-items-center">
					<div class="col-md-3 col-sm-4 col-12">
						<?php echo pjSanitize::html($extra['name']); ?>
						<?php if(!empty($extra['info'])) { ?>
							<div class="small"><i>(<?= $extra['info'] ?>)</i></div>
						<?php } ?>
					</div>
					<div class="col-md-9 col-sm-8 col-12">
						<?php if ((float)$extra['price'] > 0) { ?>
							<?php echo $extra['quantity'];?> x <?php echo number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>
						<?php } else { ?>
							<?php echo $extra['quantity'];?> (<?php __('front_label_free');?>)
						<?php } ?>
					</div>
				</div>
			 <?php } ?>
		<?php } ?>
		<?php } ?>		
		
		<?php if($tpl['arr']['discount'] > 0) { ?>
			<h5><?php __('front_discount_code'); ?></h5>
			<div class="row form-group d-flex align-items-center">
				<div class="col-md-3 col-sm-4 col-12"><?php __('voucher_code'); ?></div>
				<div class="col-md-9 col-sm-8 col-12"><?php echo number_format($tpl['arr']['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></div>
			</div>
		<?php } ?>
		<h5><?php __('front_payment_medthod'); ?></h5>
		<div class="form-group"><?php echo @$payment_methods[$tpl['arr']['payment_method']];?></div>
		<h3><?php __('front_total'); ?>: <?= number_format($tpl['arr']['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'] ?></h3>
		<div id="trBookingMsg_<?php echo $_GET['index'];?>" class="form-group">
			<?php 
			switch ($tpl['arr']['payment_method'])
			{
				case 'paypal':
					?>
					<div class="alert alert-success d-flex align-items-center">
						<i class="fad fa-check-circle"></i><span class="alert-desc"><?php echo $front_messages[1]; ?></span>		
					</div>
					<?php
					if (pjObject::getPlugin('pjPaypal') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
					break;
				case 'authorize':
					?>
					<div class="alert alert-success d-flex align-items-center">
						<i class="fad fa-check-circle"></i><span class="alert-desc"><?php echo $front_messages[2]; ?></span>		
					</div>
					<?php
					if (pjObject::getPlugin('pjAuthorize') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
					break;
				case 'saferpay':
					if ($controller->defaultPaySafePaymentMethod == 'direct') {
						if(!empty($tpl['arr']['txn_id']))
						{
							?>
							<div class="alert alert-success d-flex align-items-center">
								<i class="fad fa-check-circle"></i><span class="alert-desc"><?php __('front_messages_ARRAY_10');?></span>		
							</div>
							<?php
						}else{
							$paysafe = $tpl['paysafe_data'];
							if(isset($paysafe['body']['RedirectUrl']))
							{
								$url = $paysafe['body']['RedirectUrl'];
								?>
								<div class="alert alert-success d-flex align-items-center">
									<i class="fad fa-check-circle"></i><span class="alert-desc"><span class="alert-title"><?php echo $front_messages[11];?></span><?php echo $front_messages[12]; ?></span>		
								</div>
								<div id="trSaferpayForm_<?php echo $_GET['index'];?>">
									<iframe name="trSaferpay" id="trSaferpay_<?php echo $_GET['index'];?>" scrolling="no" src="<?php echo $url;?>" height="100%" width="100%" style="min-height: 760px;"></iframe>
								</div>
								<?php
							} else { ?>
								<div class="alert alert-warning d-flex align-items-center">
									<i class="fad fa-exclamation-triangle"></i>
									<span class="alert-desc">
										<?php echo $front_messages[7]; ?>
										<?php 
										if (isset($paysafe['body']['ErrorDetail'])) {
											foreach ($paysafe['body']['ErrorDetail'] as $paysafe_err) {
												?>
												<br/><?php echo $paysafe_err;?>
												<?php 	
											}
										} elseif (isset($paysafe['body']['ErrorMessage'])) {
											?>
											<br/><?php echo $paysafe['body']['ErrorMessage'];?>
											<?php 
										}
										?>
									</span>		
								</div>
							<?php }
						}
					}
				break;
			}
			?>
		</div>
	</div>
</div>
