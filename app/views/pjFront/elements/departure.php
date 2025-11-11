<div class="pjSbBoxWrap">
	<div class="pjSbProgress progress">
		<div class="progress-bar" style="width:33%"></div>
	</div> 
	<?php if($tpl['status'] == 'OK') { 
		$months = __('months', true);
		$short_days = __('short_days', true);
		ksort($months);
		ksort($short_days);
		$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
		?>
		<div id="pjSbCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
		<div class="row">
			<div class="col-md-8 col-12">
				<form id="trDepartureForm_<?php echo $index;?>" action="" method="post" class="pjSbForm">
	                <input type="hidden" name="index" value="<?= $index ?>">
	                <input type="hidden" name="original_date" id="trDateOriginal_<?php echo $index?>" value="<?php echo isset($STORE['search']) && isset($STORE['search']['date']) ? htmlspecialchars($STORE['search']['date']) : null; ?>">
	                <input type="hidden" name="has_return" id="trIsReturn_<?php echo $index?>" value="<?php echo $STORE['is_return'];?>" />
	                <?php if ($STORE['search']['is_airport']) { ?>
						<div class="alert alert-success d-flex align-items-center">
							<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><?php __('front_stress_free');?></span>   		
						</div>
					<?php } ?>
					<div class="pjSbDepartureInfo pjSbBox">
						<h3><?php __('front_booking_details_departure'); ?></h3>
						<div><?php __('front_your_transfer_from');?>: <span class="fw-bold"><?php echo pjSanitize::clean(@$tpl['cart']['pickup_location_name']);?></span></div><br/>
						<div class="row">
							<div class="col-md-5 col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_transfer_date'); ?></label>
									<div class="input-group pjSbBookingDetailsTransferDate">
										<span class="input-group-addon">
											<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
										</span>				
										<input type="text" placeholder="<?php __('front_select_transfer_date', false, true); ?>" id="trDateConfirm_<?php echo $index?>" name="date_confirm" readonly value="<?php echo isset($STORE['search']) && isset($STORE['search']['date']) ? htmlspecialchars($STORE['search']['date']) : null; ?>" class="form-control hasDatepicker required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
									</div>
									<small><?php __('front_verify_departure_date'); ?></small>
								</div><!-- /.form-group -->
							</div>
							<div class="col-md-4 col-sm-6 col-12">
								<div class="form-group pjSbSpinWrap">
									<label class="control-label"><?php __('front_how_many_persons'); ?></label>
									<div class="input-group pjSbSpins">
										<div class="pjSbSpinLeft">
											<a href="javascript:void(0);" class="pjSbSpin" data-type="minus" data-min="<?php echo (int)$tpl['fleet']['min_passengers'];?>">
												<span class="input-group-addon text-blue">
													<span class="fa-solid fa-circle-minus" aria-hidden="true"></span>
												</span>		
											</a>
										</div>			
										<input type="text" name="passengers" id="passengers" readonly="readonly" value="<?php echo isset($FORM['passengers']) ? (int)$FORM['passengers'] : (isset($STORE['search']['passengers_from_to']) ? (int)$STORE['search']['passengers_from_to'] : 1);?>" class="form-control text-center required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										<div class="pjSbSpinRight">
											<a href="javascript:void(0);" class="pjSbSpin" data-type="plus" data-max="<?php echo (int)$tpl['fleet']['passengers'];?>">
												<span class="input-group-addon text-blue">
													<span class="fa-solid fa-circle-plus" aria-hidden="true"></span>
												</span>		
											</a>
										</div>
									</div>
									<small><?php __('front_number_of_passengers'); ?></small>
								</div><!-- /.form-group -->
							</div>
						</div>
						
						<div id="trDateConfirmMsg_<?php echo $index?>" style="display: none;">
							<div class="alert alert-warning d-flex align-items-center">
								<i class="fa-solid fa-circle-info"></i><span class="alert-desc"><?php __('front_date_change_message');?></span>
							</div>
						</div>
						
						<?php if(!$STORE['search']['is_airport'] && $STORE['search']['dropoff_is_airport'] == 0) { ?>
							<div class="row">
								<div class="col-md-4 col-sm-12 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_pickup_time'); ?></label>
										<div class="input-group pjSbBookingDetailsPickupTime" data-label_done="<?php __('front_label_done'); ?>">
											<span class="input-group-addon">
												<span class="fa-solid fa-clock" aria-hidden="true"></span>
											</span>				
											<input type="text" placeholder="<?php __('front_pickup_time', false, true); ?>" name="pickup_time" id="pickup_time" readonly value="<?php echo isset($FORM['pickup_time']) ? pjSanitize::html($FORM['pickup_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small><?php __('front_select_pickup_time'); ?></small>
									</div><!-- /.form-group -->
								</div>
								<div class="col-md-5 col-sm-6 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_pickup_address'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
											</span>				
											<input type="text" placeholder="<?php __('front_pickup_address', false, true); ?>" name="c_address" id="c_address" value="<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : ($STORE['search']['pickup_type'] == 'google' ? stripslashes($tpl['cart']['pickup_location_name']) : $tpl['cart']['pickup_address']);?>" class="form-control required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small><?php __('front_pickup_address_desc'); ?></small>
									</div><!-- /.form-group -->
								</div>
							</div>
							<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div><br/>
							<div class="alert alert-warning d-flex align-items-center">
								<i class="fa-solid fa-circle-info"></i>
								<span class="alert-desc">
								<?php
								if ($STORE['search']['dropoff_is_airport'] == 0) { 
									echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
								} else {
									echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
								} ?>
								</span>   		
							</div>
							<div class="row">
								<div class="col-md-5 col-sm-6 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_dropoff_address'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
											</span>				
											<input type="text" placeholder="<?php __('front_dropoff_address', false, true); ?>" name="c_destination_address" id="c_destination_address" value="<?php echo isset($FORM['c_destination_address']) ? stripslashes($FORM['c_destination_address']) : ($STORE['search']['dropoff_type'] == 'google' ? stripslashes($tpl['cart']['dropoff_location_name']) : '');?>" class="form-control required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small><?php __('front_dropoff_address_desc'); ?></small>
									</div><!-- /.form-group -->
								</div>
							</div>
							<div class="row">
								<div class="col-md-5 col-sm-6 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_notes'); ?></label>
										<textarea name="c_notes" id="c_notes" class="form-control" rows="2"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
										<small><?php __('front_optional'); ?></small>
									</div><!-- /.form-group -->
								</div>
							</div>
						<?php } else { ?>
							<?php if($STORE['search']['is_airport']) { ?>
								<div class="row">
									<div class="col-md-4 col-sm-12 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_flight_time'); ?></label>
											<div class="input-group pjSbBookingDetailsArrivalTime" data-label_done="<?php __('front_label_done'); ?>">
												<span class="input-group-addon">
													<span class="fa-solid fa-clock" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_arrival_time', false, true); ?>" name="arrival_time" id="arrival_time" readonly value="<?php echo isset($FORM['arrival_time']) ? pjSanitize::html($FORM['arrival_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_select_flight_landing_time'); ?></small>
										</div><!-- /.form-group -->
									</div>
									<div class="col-md-4 col-sm-6 col-6">
										<div class="form-group">
											<label class="control-label"><?php __('front_flight_number'); ?></label>
											<div class="input-group pjSbArrivalTime">
												<span class="input-group-addon">
													<span class="fa-solid fa-plane-arrival" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="OS2055" name="c_flight_number" id="c_flight_number" value="<?php echo isset($FORM['c_flight_number']) ? pjSanitize::html($FORM['c_flight_number']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_optional'); ?></small>
										</div><!-- /.form-group -->
									</div>
									<div class="col-md-4 col-sm-6 col-6">
										<div class="form-group">
											<label class="control-label"><?php __('front_airline_company'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">
													<span class="fa-solid fa-plane-circle-check" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="Austrian" name="c_airline_company" id="c_airline_company" value="<?php echo isset($FORM['c_airline_company']) ? pjSanitize::html($FORM['c_airline_company']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_optional'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
								<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean(@$tpl['cart']['dropoff_location_name']);?></span></div><br/>
								<div class="alert alert-warning d-flex align-items-center">
									<i class="fa-solid fa-circle-info"></i>
									<span class="alert-desc">
									<?php
									if ($STORE['search']['dropoff_is_airport'] == 0) { 
										echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
									} else {
										echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
									} ?>
									</span>   		
								</div>
								<div class="row">
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_destination_address'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">
													<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_dropoff_address', false, true); ?>" name="c_destination_address" id="c_destination_address" value="<?php echo isset($FORM['c_destination_address']) ? pjSanitize::html($FORM['c_destination_address']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_dropoff_address_desc'); ?></small>
										</div><!-- /.form-group -->
									</div>
									
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_hotel_pension'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">
													<span class="fa-solid fa-hotel" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_accommodation_name', false, true); ?>" name="c_hotel" id="c_hotel" value="<?php echo isset($FORM['c_hotel']) ? pjSanitize::html($FORM['c_hotel']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_accommodation_name_desc'); ?></small>
										</div><!-- /.form-group -->
									</div>
									
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_notes'); ?></label>
											<textarea name="c_notes" id="c_notes" class="form-control" rows="2"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
											<small><?php __('front_optional'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
							<?php } else { ?>
								<div class="alert alert-warning d-flex align-items-center">
									<i class="fa-solid fa-circle-info"></i>
									<span class="alert-desc">
									<?php
									if ($STORE['search']['dropoff_is_airport'] == 0) { 
										echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
									} else {
										echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['pickup_location_name'], $tpl['cart']['dropoff_location_name'], round($STORE['search']['duration']/60));
									} ?>
									</span>   		
								</div>
								<div class="row">
									<div class="col-md-4 col-sm-12 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_pickup_time'); ?></label>
											<div class="input-group pjSbBookingDetailsPickupTime" data-label_done="<?php __('front_label_done'); ?>">
												<span class="input-group-addon">
													<span class="fa-solid fa-clock" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_pickup_time', false, true); ?>" name="pickup_time" id="pickup_time" readonly value="<?php echo isset($FORM['pickup_time']) ? pjSanitize::html($FORM['pickup_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_select_pickup_time'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
								<div class="row">
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_address'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">
													<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_pickup_address', false, true); ?>" name="c_address" id="c_address" value="<?php echo isset($FORM['c_address']) ? pjSanitize::html($FORM['c_address']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_pickup_address_desc'); ?></small>
										</div><!-- /.form-group -->
									</div>
									
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_hotel_pension'); ?></label>
											<div class="input-group">
												<span class="input-group-addon">
													<span class="fa-solid fa-hotel" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_accommodation_name', false, true); ?>" name="c_hotel" id="c_hotel" value="<?php echo isset($FORM['c_hotel']) ? pjSanitize::html($FORM['c_hotel']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_accommodation_name_desc'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
								<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div><br/>
								<div class="row">
									<div class="col-md-4 col-sm-12 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_flight_departure_time'); ?></label>
											<div class="input-group pjSbBookingDetailsFlightDepartureTime" data-label_done="<?php __('front_label_done'); ?>">
												<span class="input-group-addon">
													<span class="fa-solid fa-clock" aria-hidden="true"></span>
												</span>				
												<input type="text" placeholder="<?php __('front_flight_departure_time', false, true); ?>" name="c_departure_flight_time" id="c_departure_flight_time" readonly value="<?php echo isset($FORM['c_departure_flight_time']) ? pjSanitize::html($FORM['c_departure_flight_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
											</div>
											<small><?php __('front_select_flight_departure_time'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
								<div class="row">
									<div class="col-md-5 col-sm-6 col-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_notes'); ?></label>
											<textarea name="c_notes" id="c_notes" class="form-control" rows="2"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
											<small><?php __('front_optional'); ?></small>
										</div><!-- /.form-group -->
									</div>
								</div>
							<?php } ?>
						<?php } ?>						
					</div>
					
					<div class="pjSbAlertAddReturnTransfer" style="display: <?php echo $STORE['is_return'] == 0 ? '' : 'none';?>">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr class="row">
								<td class="col-lg-1 col-md-2 col-sm-2 col-2 align-middle"><i class="fa-solid fa-arrow-right-arrow-left"></i></td>
								<td class="col-lg-7 col-md-6 col-sm-6 col-10 align-middle">
									<?php 
									if ($tpl['fleet']["return_discount_{$dayIndex}"] > 0) {
										echo sprintf(__('front_add_return_transfer_note_with_discount', true), pjSanitize::clean($tpl['cart']['dropoff_location_name']), pjSanitize::clean($tpl['cart']['pickup_location_name']), $tpl['fleet']["return_discount_{$dayIndex}"].'%');
									} else {
										echo sprintf(__('front_add_return_transfer_note', true), pjSanitize::clean($tpl['cart']['dropoff_location_name']), pjSanitize::clean($tpl['cart']['pickup_location_name']));
									}
									?>
								</td>
								<td class="col-lg-4 col-md-4 col-sm-4 col-12 text-end align-middle"><button type="button" class="btn btn-secondary trSetTransferTypeButton" data-is-return="1"><?php __('front_button_add_return_transfer');?></button></td>
							</tr>
						</table>
					</div>
					
					<div id="trBookingStep_Return_<?php echo $index;?>">
						
					</div>
					
					<div class="pjSbExtrasInfo pjSbBox" id="pjSbExtras_<?php echo $index;?>">
						<?php include_once PJ_VIEWS_PATH . 'pjFront/pjActionGetExtras.php';?>
					</div>
					
					<div class="row d-flex align-items-center">
						<div class="col-sm-6 col-12"><a href="javascript:void(0);" class="pjSbBtnGoBack pjSbLoadFleets"><i class="fa-solid fa-circle-arrow-left"></i> <?php __('front_button_go_back');?></a></div>
						<div class="col-sm-6 col-12 text-end"><button type="submit" class="btn btn-primary"><?php __('front_btn_continue'); ?></button></div>
					</div>
				</form>
			</div>
			<div class="col-md-4 col-12">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
			</div>
		</div>
	<?php } else { ?>
		<div class="alert alert-warning d-flex align-items-center">
			<i class="fa-solid fa-circle-info"></i><span class="alert-desc"><span class="alert-title"><?php __('front_error')?></span><?php __('front_search_fleets_error_desc');?></span>   		
		</div>
	<?php } ?>
	
	<div class="modal fade" id="pjSbBookingDetailsTransferDateModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_transfer_date_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <div id="trBookingDetailsTransferDatePick_<?php echo $index?>"></div>
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsArrivalTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_time_of_arrival_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsArrivalTimePick_<?php echo $index?>" id="trBookingDetailsArrivalTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsPickupTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_pickup_time_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsPickupTimePick_<?php echo $index?>" id="trBookingDetailsPickupTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsFlightDepartureTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_flight_departure_time_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsFlightDepartureTimePick_<?php echo $index?>" id="trBookingDetailsFlightDepartureTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
</div>
