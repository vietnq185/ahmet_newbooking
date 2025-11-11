<?php 
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
?>
<div id="pjSbReturnCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
<div class="pjSbReturnInfo pjSbBox">
	<div class="text-end text-danger"><a href="javascript:void(0);" class="trSetTransferTypeButton pjSbRemoveReturnTransfer text-danger" data-is_return="0"><?php __('front_remove_return_transfer');?>&nbsp;&nbsp;<i class="fa-solid fa-circle-xmark"></i></a></div>
	<h3><?php __('front_booking_details_return'); ?></h3>
	<div><?php __('front_your_transfer_from');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['dropoff_location_name']);?></span></div><br/>
	<?php if(!$STORE['search']['is_airport'] && $STORE['search']['dropoff_is_airport'] == 0) { ?>
		<div class="row">
			<div class="col-md-5 col-sm-6 col-12">
				<div class="form-group">
					<label class="control-label"><?php __('front_pickup_date'); ?></label>
					<div class="input-group pjSbBookingDetailReturnDate">
						<span class="input-group-addon">
							<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
						</span>				
						<input type="text" placeholder="<?php __('front_pickup_date', false, true); ?>" id="trReturnDate_<?php echo $index?>" name="return_date" readonly value="<?php echo isset($FORM['return_date']) ? htmlspecialchars($FORM['return_date']) : null; ?>" class="form-control hasDatepicker required" data-min="<?php echo htmlspecialchars($STORE['search']['date']) ?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					</div>
					<small><?php __('front_verify_return_transfer_date'); ?></small>
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
						<input type="text" name="passengers_return" id="passengers_return" readonly="readonly" value="<?php echo isset($FORM['passengers_return']) ? (int)$FORM['passengers_return'] : (!isset($FORM['passengers_return']) && isset($FORM_DEPARTURE['passengers']) ? (int)$FORM_DEPARTURE['passengers'] : 1);?>" class="form-control text-center required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
		<div class="row">
			<div class="col-md-4 col-sm-12 col-12">
				<div class="form-group">
					<label class="control-label"><?php __('front_pickup_time'); ?></label>
					<div class="input-group pjSbBookingDetailsReturnPickupTime" data-label_done="<?php __('front_label_done'); ?>">
						<span class="input-group-addon">
							<span class="fa-solid fa-clock" aria-hidden="true"></span>
						</span>				
						<input type="text" placeholder="<?php __('front_pickup_time', false, true); ?>" name="return_pickup_time" id="return_pickup_time" readonly value="<?php echo isset($FORM['return_pickup_time']) ? pjSanitize::html($FORM['return_pickup_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
						<input type="text" placeholder="<?php __('front_pickup_address', false, true); ?>" name="return_c_address" id="return_c_address" value="<?php echo isset($FORM['return_c_address']) ? pjSanitize::clean($FORM['return_c_address']) : '';?>" class="form-control required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					</div>
					<small><?php __('front_pickup_address_desc'); ?></small>
				</div><!-- /.form-group -->
			</div>
		</div>
		<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['pickup_location_name']);?></span></div><br/>
		<div class="alert alert-warning d-flex align-items-center">
			<i class="fa-solid fa-circle-info"></i>
			<span class="alert-desc">
			<?php 
			if (isset($STORE['search']['is_airport']) && $STORE['search']['is_airport'] == 1) {
				echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['dropoff_location_name'], $tpl['cart']['pickup_location_name'], round($STORE['search']['return_duration']/60));
			} else {
				echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['dropoff_location_name'], $tpl['cart']['pickup_location_name'], round($STORE['search']['return_duration']/60));
			}
			?>
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
						<input type="text" placeholder="<?php __('front_dropoff_address', false, true); ?>" name="return_c_destination_address" id="return_c_destination_address" value="<?php echo isset($FORM['return_c_destination_address']) ? stripslashes($FORM['return_c_destination_address']) : '';?>" class="form-control required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					</div>
					<small><?php __('front_dropoff_address_desc'); ?></small>
				</div><!-- /.form-group -->
			</div>
		</div>
		<div class="row">
			<div class="col-md-5 col-sm-6 col-12">
				<div class="form-group">
					<label class="control-label"><?php __('front_notes'); ?></label>
					<textarea name="return_c_notes" id="return_c_notes" class="form-control" rows="2"><?php echo isset($FORM['return_c_notes']) ? pjSanitize::clean($FORM['return_c_notes']) : null;?></textarea>
					<small><?php __('front_optional'); ?></small>
				</div><!-- /.form-group -->
			</div>
		</div>
	<?php } else { ?>
		<?php if ($STORE['search']['is_airport']) { ?>
			<div class="row">
				<div class="col-md-5 col-sm-6 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_pickup_date'); ?></label>
						<div class="input-group pjSbBookingDetailReturnDate">
							<span class="input-group-addon">
								<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_pickup_date', false, true); ?>" id="trReturnDate_<?php echo $index?>" name="return_date" readonly value="<?php echo isset($FORM['return_date']) ? htmlspecialchars($FORM['return_date']) : null; ?>" class="form-control hasDatepicker required" data-min="<?php echo htmlspecialchars($STORE['search']['date']) ?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_verify_return_transfer_date'); ?></small>
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
							<input type="text" name="passengers_return" id="passengers_return" readonly="readonly" value="<?php echo isset($FORM['passengers_return']) ? (int)$FORM['passengers_return'] : (!isset($FORM['passengers_return']) && isset($FORM_DEPARTURE['passengers']) ? (int)$FORM_DEPARTURE['passengers'] : 1);?>" class="form-control text-center required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
			<div class="alert alert-warning d-flex align-items-center">
				<i class="fa-solid fa-circle-info"></i>
				<span class="alert-desc">
				<?php 
				if (isset($STORE['search']['is_airport']) && $STORE['search']['is_airport'] == 1) {
					echo sprintf(__('front_recommended_airport_pickup_time_info', true), $tpl['cart']['dropoff_location_name'], $tpl['cart']['pickup_location_name'], round($STORE['search']['return_duration']/60));
				} else {
					echo sprintf(__('front_recommended_pickup_time_info', true), $tpl['cart']['dropoff_location_name'], $tpl['cart']['pickup_location_name'], round($STORE['search']['return_duration']/60));
				}
				?>
				</span>   		
			</div>
			<div class="row">
				<div class="col-md-4 col-sm-12 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_pickup_time'); ?></label>
						<div class="input-group pjSbBookingDetailsReturnPickupTime" data-label_done="<?php __('front_label_done'); ?>">
							<span class="input-group-addon">
								<span class="fa-solid fa-clock" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_pickup_time', false, true); ?>" name="return_pickup_time" id="return_pickup_time" readonly value="<?php echo isset($FORM['return_pickup_time']) ? pjSanitize::html($FORM['return_pickup_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_select_pickup_time'); ?></small>
					</div><!-- /.form-group -->
				</div>
				<div class="col-md-5 col-sm-6 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_address'); ?></label>
						<div class="input-group">
							<span class="input-group-addon">
								<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_pickup_address', false, true); ?>" name="return_c_address" id="return_c_address" value="<?php echo isset($FORM['return_c_address']) ? pjSanitize::html($FORM['return_c_address']) : null; ?>" class="form-control required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_pickup_address_desc'); ?></small>
					</div><!-- /.form-group -->
				</div>
			</div>
			<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['pickup_location_name']);?></span></div><br/>
			<div class="row">
				<div class="col-md-4 col-sm-12 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_flight_departure_time'); ?></label>
						<div class="input-group pjSbBookingDetailsReturnFlightDepartureTime" data-label_done="<?php __('front_label_done'); ?>">
							<span class="input-group-addon">
								<span class="fa-solid fa-clock" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_flight_departure_time', false, true); ?>" name="return_c_departure_flight_time" id="return_c_departure_flight_time" readonly value="<?php echo isset($FORM['return_c_departure_flight_time']) ? pjSanitize::html($FORM['return_c_departure_flight_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_select_flight_departure_time'); ?></small>
					</div><!-- /.form-group -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-5 col-sm-6 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_notes'); ?></label>
						<textarea name="return_c_notes" id="return_c_notes" class="form-control" rows="2"><?php echo isset($FORM['return_c_notes']) ? pjSanitize::clean($FORM['return_c_notes']) : null;?></textarea>
						<small><?php __('front_optional'); ?></small>
					</div><!-- /.form-group -->
				</div>
			</div>
		<?php } else { ?>
			<div class="row">
				<div class="col-md-5 col-sm-6 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_pickup_date'); ?></label>
						<div class="input-group pjSbBookingDetailReturnDate">
							<span class="input-group-addon">
								<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_pickup_date', false, true); ?>" id="trReturnDate_<?php echo $index?>" name="return_date" readonly value="<?php echo isset($FORM['return_date']) ? htmlspecialchars($FORM['return_date']) : null; ?>" class="form-control hasDatepicker required" data-min="<?php echo htmlspecialchars($STORE['search']['date']) ?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_verify_return_transfer_date'); ?></small>
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
							<input type="text" name="passengers_return" id="passengers_return" readonly="readonly" value="<?php echo isset($FORM['passengers_return']) ? (int)$FORM['passengers_return'] : (!isset($FORM['passengers_return']) && isset($FORM_DEPARTURE['passengers']) ? (int)$FORM_DEPARTURE['passengers'] : 1);?>" class="form-control text-center required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
			<?php if ($STORE['search']['dropoff_is_airport'] == 1) { ?>
				<div class="alert alert-success d-flex align-items-center">
					<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><?php __('front_stress_free');?></span>   		
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-4 col-sm-12 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_flight_time'); ?></label>
						<div class="input-group pjSbBookingDetailsReturnTime" data-label_done="<?php __('front_label_done'); ?>">
							<span class="input-group-addon">
								<span class="fa-solid fa-clock" aria-hidden="true"></span>
							</span>				
							<input type="text" placeholder="<?php __('front_arrival_time', false, true); ?>" name="return_time" id="return_time" readonly value="<?php echo isset($FORM['return_time']) ? pjSanitize::html($FORM['return_time']) : null; ?>" class="form-control hasTimepick required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
							<input type="text" placeholder="OS2055" name="return_c_flight_number" id="return_c_flight_number" value="<?php echo isset($FORM['return_c_flight_number']) ? pjSanitize::html($FORM['return_c_flight_number']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
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
							<input type="text" placeholder="Austrian" name="return_c_airline_company" id="return_c_airline_company" value="<?php echo isset($FORM['return_c_airline_company']) ? pjSanitize::html($FORM['return_c_airline_company']) : null; ?>" class="form-control" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						</div>
						<small><?php __('front_optional'); ?></small>
					</div><!-- /.form-group -->
				</div>
			</div>
			<div><?php __('front_going_to');?>: <span class="fw-bold"><?php echo pjSanitize::clean($tpl['cart']['pickup_location_name']);?></span></div><br/>
			<div class="row">
				<div class="col-md-5 col-sm-6 col-12">
					<div class="form-group">
						<label class="control-label"><?php __('front_notes'); ?></label>
						<textarea name="return_c_notes" id="return_c_notes" class="form-control" rows="2"><?php echo isset($FORM['return_c_notes']) ? pjSanitize::clean($FORM['return_c_notes']) : null;?></textarea>
						<small><?php __('front_optional'); ?></small>
					</div><!-- /.form-group -->
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	
	<div class="modal fade" id="pjSbBookingDetailsReturnDateModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_return_transfer_date_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <div id="trBookingDetailsReturnDatePick_<?php echo $index?>"></div>
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsReturnPickupTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_pickup_time_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsReturnPickupTimePick_<?php echo $index?>" id="trBookingDetailsReturnPickupTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsReturnFlightDepartureTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_flight_departure_time_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsReturnFlightDepartureTimePick_<?php echo $index?>" id="trBookingDetailsReturnFlightDepartureTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade pjSbBookingDetailsTimeModal" id="pjSbBookingDetailsReturnTimeModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_time_of_arrival_title');?></div>
        			<div>
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <input type="text" class="trBookingDetailsInputTimePick" name="trBookingDetailsReturnTimePick_<?php echo $index?>" id="trBookingDetailsReturnTimePick_<?php echo $index?>" autofocus data-label_done="<?php __('front_label_done'); ?>" />
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
</div>