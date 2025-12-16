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
	
	pjUtil::printNotice(__('infoAddBookingTitle', true, false), __('infoAddBookingDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateBooking" enctype="multipart/form-data">
		<input type="hidden" name="booking_create" value="1" />
		
		<input type="hidden" name="pickup_id" id="pickup_id" value="">
		<input type="hidden" name="pickup_address" id="pickup_address" value="">
		<input type="hidden" name="pickup_lat" id="pickup_lat" value="">
		<input type="hidden" name="pickup_lng" id="pickup_lng" value="">
		<input type="hidden" name="pickup_type" id="pickup_type" value="">		
		<input type="hidden" name="custom_pickup_id" id="custom_pickup_id" value="">
		
		<input type="hidden" name="dropoff_address" id="dropoff_address" value="">
		<input type="hidden" name="dropoff_lat" id="dropoff_lat" value="">
		<input type="hidden" name="dropoff_lng" id="dropoff_lng" value="">			
		<input type="hidden" name="dropoff_type" id="dropoff_type" value="">
		
		<input type="hidden" name="pickup_is_airport" id="pickup_is_airport" value="">
		<input type="hidden" name="dropoff_is_airport" id="dropoff_is_airport" value="">		
			
		<input type="hidden" name="custom_dropoff_id" id="custom_dropoff_id" value="">
		<input type="hidden" name="custom_dropoff_place_id" id="custom_dropoff_place_id" value="">		
			
		<input type="hidden" name="duration" id="duration" value="">
		<input type="hidden" name="distance" id="distance" value="">
		
		<input type="hidden" name="station_id" id="distance" value="">
		<input type="hidden" name="station_fee" id="station_fee" value="">
		<input type="hidden" name="price_by_distance" id="price_by_distance" value="F">
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails');?></a></li>
			</ul>

			<div id="tabs-1" class="bs-loader-outer">
				<div class="bs-loader"></div>
				<div class="float_right w500">
					<p style="display: none;">
						<label class="title"><?php __('lblDuration'); ?>:</label>
						<label id="tr_duration" class="content"></label>
					</p>
					<p style="display: none;">
						<label class="title"><?php __('lblDistance'); ?>:</label>
						<label id="tr_distance" class="content"></label>
					</p>
                    <p style="display: none;">
                        <label class="title"><?php __('lblDriver'); ?>:</label>
                        <select name="driver_id" id="driver_id" class="pj-form-field w300">
                            <option value="">-- <?php __('lblChoose'); ?>--</option>
                            <?php foreach($tpl['driver_arr'] as $k => $v): ?>
                                <option value="<?php echo $v['id'];?>"><?= "{$v['fname']} {$v['lname']} ({$v['email']})";?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
				</div>
				<p>
					<label class="title"><?php __('lblDateTime'); ?>:</label>
					<span class="block overflow">
						<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" name="booking_date" id="booking_date" class="pj-form-field pointer w120 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
                <p>
                    <label class="title"><?php __('lblAcceptsSharedTrip'); ?>:</label>
						<span class="inline-block">
							<span class="block t6 b5">
								<input type="checkbox" id="accept_shared_trip" name="accept_shared_trip" />
							</span>
						</span>
                </p>
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
				<p>
					<label class="title"><?php __('lblPickupLocation'); ?>:</label>
					<span class="inline-block">
						<select name="location_id" id="location_id" class="pj-form-field w500 required">
							<option value="server~::~0">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['pickup_arr'] as $k => $v)
							{
								?><option value="server~::~<?php echo $v['id'];?>" data-icon="<?php echo $v['icon']; ?>" data-is-airport="<?php echo (int) $v['is_airport']; ?>"><?php echo $v['pickup_location'];?></option><?php
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
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblFleet'); ?>:</label>
					<span class="inline-block pjVehiclesContainer">
						<select name="fleet_id" id="fleet_id" class="pj-form-field w500 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['fleet_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>"><?php echo pjSanitize::html($v['fleet'] .' / '.$v['station_name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPassengers'); ?>:</label>
					<span class="inline-block">
						<input type="text" id="passengers" name="passengers" class="pj-form-field field-int w80 required pj-positive-number" data-value="0"/>
						<span id="tr_max_passengers"></span>
					</span>
				</p>
				
				<p class="trReturnDetails" style="display: none;">
					<label class="title"><?php __('lblPassengersReturn'); ?>:</label>
					<span class="inline-block">
						<input type="text" id="passengers_return" name="passengers_return" class="pj-form-field field-int w80 required pj-positive-number" data-value="0"/>
						<span id="tr_max_passengers_return"></span>
					</span>
				</p>
				
                <p>
                    <label class="title"><?php __('front_discount_code'); ?></label>
					<span class="inline-block">
						<input type="text" name="voucher_code" id="voucher_code" class="pj-form-field w400"/>
					</span>
                </p>
                
                <div class="float_left w350">
                	<p>
						<label class="title"><?php __('lblPriceExtra'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="total_extra_price" name="total_extra_price" class="pj-form-field number w108" />
							<input type="hidden" id="extra_price_first_transfe" name="extra_price_first_transfe" class="pj-form-field number w108" />
							<input type="hidden" id="extra_price_return_transfe" name="extra_price_return_transfe" class="pj-form-field number w108" />
						</span>
					</p>
                	<p>
						<label class="title"><?php __('lblSubTotal'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="sub_total" name="sub_total" class="pj-form-field number w108"/>
						</span>
					</p>
	                <p<?= $tpl['option_arr']['o_tax_payment'] > 0? null: ' style="display: none;"' ?>>
						<label class="title"><?php __('lblTax'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="tax" name="tax" class="pj-form-field number w108" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
						</span>
					</p>
	                <p>
	                    <label class="title"><?php __('voucher_discount'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="discount" name="discount" class="pj-form-field number w108"/>
						</span>
	                </p>
	                <p>
						<label class="title"><?php __('lblCreditCardFee'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="credit_card_fee" name="credit_card_fee" class="pj-form-field number w108" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblTotal'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="total" name="total" class="pj-form-field number w108"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblDeposit'); ?>:</label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
						</span>
					</p>
                </div>
                <div class="float_left w350">
                	<div class="pjPriceOneway">
                		<p>
							<label class="title"><?php __('lblPriceTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price" name="price" class="pj-form-field number w108" />
							</span>
						</p>
                	</div>
                	<div class="pjPriceRoundtrip" style="display: none;">
                		<p>
							<label class="title"><?php __('lblPriceFirstTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price_first_transfer" name="price_first_transfer" class="pj-form-field number w108" />
							</span>
						</p>
						<p>
							<label class="title"><?php __('lblPriceReturnTransfer'); ?>:</label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" id="price_return_transfer" name="price_return_transfer" class="pj-form-field number w108" />
							</span>
						</p>
                	</div>
                </div>
                <br class="clear_both"/>
                
				<p>
					<label class="title"><?php __('lblPaymentMethod');?>:</label>
					<span class="inline-block">
						<select name="payment_method" id="payment_method" class="pj-form-field w400 required">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach (__('payment_methods', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
                <p class="boxCC" style="display: none;">
                    <label class="title"><?php __('lblCCOwner'); ?></label>
					<span class="inline-block">
						<input type="text" name="cc_owner" id="cc_owner" class="pj-form-field w400"/>
					</span>
                </p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCNum'); ?>:</label>
					<span class="inline-block">
						<input type="text" name="cc_num" id="cc_num" class="pj-form-field w400" />
					</span>
				</p>
				<p class="boxCC" style="display: none;">
					<label class="title"><?php __('lblCCExp'); ?>:</label>
					<span class="inline-block">
						<select name="cc_exp_month" class="pj-form-field">
							<option value="">---</option>
							<?php
							$month_arr = __('months', true, false);
							ksort($month_arr);
							foreach ($month_arr as $key => $val)
							{
								?><option value="<?php echo $key;?>"><?php echo $val;?></option><?php
							}
							?>
						</select>
						<select name="cc_exp_year" class="pj-form-field">
							<option value="">---</option>
							<?php
							$y = (int) date('Y');
							for ($i = $y; $i <= $y + 10; $i++)
							{
								?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p class="boxCC" style="display: none">
					<label class="title"><?php __('lblCCCode'); ?>:</label>
					<span class="inline-block">
						<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" />
					</span>
				</p>
				
				<div class="pj-status-container">
    				<div class="p pj-status-color">
    					<label class="title"><?php __('lblStatus'); ?>:</label>
    					<span class="inline-block">
    						<select name="status" id="status" data-placeholder="-- <?php __('lblChoose'); ?>--" class="pj-form-field w400 required chosen-select status-select">
    							<option value="">-- <?php __('lblChoose'); ?>--</option>
    							<?php
    							foreach (__('booking_statuses', true, false) as $k => $v)
    							{
    							    if ($k == 'passed_on') {
    							        continue;
    							    }
    								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
    							}
    							?>
    						</select>
    					</span>
    				</div>
				
    				<div class="p pjStatusReturnTrip trReturnDetails pj-status-return-color" style="display: none;">
    					<label class="title"><?php __('lblStatusReturnTrip'); ?></label>
    					<span class="inline-block">
    						<select name="status_return_trip" id="status_return_trip" data-placeholder="-- <?php __('lblChoose'); ?>--" class="pj-form-field w400 required chosen-select status-return-select">
    							<option value="">-- <?php __('lblChoose'); ?>--</option>
    							<?php
    							foreach (__('booking_statuses', true, false) as $k => $v)
    							{
    							    if ($k == 'passed_on') {
    							        continue;
    							    }
    								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
    							}
    							?>
    						</select>
    					</span>
    				</div>	
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
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
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
                            <textarea name="notes_for_support" id="notes_for_support" class="pj-form-field w300 h300"></textarea>
                        </span>
                    </div>
                </div>
                <br class="clear_both" />
                
                <div class="pjSbReturnExtras" style="display: none;">
                	<div class="p">&nbsp;</div>
	                <?php foreach($tpl['extra_arr'] as $index => $extra): ?>
	                    <div class="p">
	                        <label class="title"><?= $index == 0? __('menuReturnExtras', true): '&nbsp;'; ?></label>
	                        <span class="inline-block">
	                            <span class="w150" style="display: inline-block;"><?= $extra['name'] ?></span>
	                            <select name="extras_return[<?= $extra['id'] ?>]" id="extra_return_<?= $extra['id'] ?>" class="pj-form-field w50 pjExtraSelector">
	                                <?php for($i = 0; $i <= $tpl['option_arr']['o_extras_max_qty']; $i++): ?>
	                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
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
                                ?><option value="<?php echo $v; ?>"><?php echo $name_titles[$v]; ?></option><?php
                            }
                            ?>
                        </select>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingFname'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_fname" id="c_fname" class="pj-form-field w400 required"/>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingLname'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_lname" id="c_lname" class="pj-form-field w400 required"/>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingEmail'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_email" id="c_email" class="pj-form-field w400 required"/>
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
                                ?><option value="<?php echo $v['id']; ?>" data-code="<?= $v['code'] ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
                            }
                            ?>
                        </select>
                    </span>
                </div>

                <div class="p">
                    <label class="title"><?php __('lblBookingPhone'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_dialing_code" id="c_dialing_code" class="pj-form-field w40 required"/>
                        <input type="text" name="c_phone" id="c_phone" class="pj-form-field w200 required"/>
                    </span>
                </div>

				<div class="p">
					<label for="customized_name_plate" class="title"><?php __('lblBookingCustomizedNamePlate'); ?></label>
					<span class="inline-block">
						<input type="text" name="customized_name_plate" id="customized_name_plate" class="pj-form-field w400">
					</span>
				</div>
				
				<p>
					<label class="title"><?php __('lblUploadNameSign'); ?></label>
					<span class="inline_block">
						<input type="file" name="name_sign" id="name_sign" class="pj-form-field w400"/>
					</span>
				</p>
				
                <div class="p">&nbsp;</div>
                <div class="p">
                    <label class="title"><?php __('front_transfer_details'); ?></label>
                </div>

				<div id="departure_info_is_airport_2" style="display: none">
                    <div class="p">
                        <label class="title"><?php __('lblPickupAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="cl_address" id="cl_address" class="pj-form-field w400" />
						</span>
                    </div>
					<div class="p">
	                    <label class="title"><?php __('lblBookingPickupGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    	<input type="text" name="pickup_google_map_link" id="pickup_google_map_link" class="pj-form-field w400" />
	                    </span>
	                </div>
                    <div class="p">
                        <label class="title"><?php __('lblDropAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="cl_destination_address" id="cl_destination_address" class="pj-form-field w400" />
						</span>
                    </div>
                    <div class="p">
	                    <label class="title"><?php __('lblBookingDropoffGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    	<input type="text" name="dropoff_google_map_link" id="dropoff_google_map_link" class="pj-form-field w400" />
	                    </span>
	                </div>
                </div>
                
                <div id="departure_info_is_airport_1" style="display: none;">
                    <div class="p">
                        <label class="title"><?php __('lblArrivalFlightNumber'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_flight_number" id="c_flight_number" class="pj-form-field w400"/>
						</span>
                    </div>

                    <div class="p">
                        <label class="title"><?php __('lblBookingAirlineCompany'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_airline_company" id="c_airline_company" class="pj-form-field w400"/>
						</span>
                    </div>

                    <div class="p">
                        <label class="title"><?php __('lblBookingDestAddress'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_destination_address" id="c_destination_address" class="pj-form-field w400"/>
						</span>
                    </div>
                </div>

                <div id="departure_info_is_airport_0" style="display: none;">
                    <div class="p">
                        <label class="title"><?php __('front_address'); ?>:</label>
						<span class="inline-block">
							<input type="text" name="c_address" id="c_address" class="pj-form-field w400"/>
						</span>
                    </div>

                    <div class="p">
                        <label class="title"><?php __('lblFlightDepartureTime'); ?>:</label>
                        <span class="inline-block">
                            <select name="c_departure_flight_time_h" id="c_departure_flight_time_h" class="pj-form-field w60 required">
                                <option value="">Hh</option>
                                <?php for($i = 0; $i <= 23; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="c_departure_flight_time_m" id="c_departure_flight_time_m" class="pj-form-field w60 required">
                                <option value="">Mm</option>
                                <?php for($i = 0; $i <= 55; $i += 5): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </div>
                </div>

                <div class="p pjHotelName" style="display: none;">
                    <label class="title"><?php __('front_hotel'); ?>:</label>
                    <span class="inline-block">
                        <input type="text" name="c_hotel" id="c_hotel" class="pj-form-field w400"/>
                    </span>
                </div>
				<div class="p pjHotelName" style="display: none;">
                    <label class="title"><?php __('lblBookingGoogleMapsLink'); ?>:</label>
                    <span class="inline-block">
                    	<input type="text" name="google_map_link" id="google_map_link" class="pj-form-field w400" />
                    </span>
                </div>
                <div class="p">
                    <label class="title"><?php __('lblBookingNotes'); ?></label>
                    <span class="inline-block">
                        <textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120"></textarea>
                    </span>
                </div>

				<div class="p">
                    <label class="title"><?php __('lblInternalNotes'); ?></label>
                    <span class="inline-block">
                        <textarea name="internal_notes" id="internal_notes" class="pj-form-field w500 h120"></textarea>
                    </span>
                </div>
                
                <div class="trReturnDetails" style="display: none;">
                    <div class="p">&nbsp;</div>
                    <div class="p">
                        <label class="title"><?php __('front_return_transfer_details'); ?></label>
                    </div>

					<div id="return_info_is_airport_2" style="display: none">
                        <div class="p">
                            <label class="title"><?php __('lblPickupAddress'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_cl_address" id="return_cl_address" class="pj-form-field w400" />
                            </span>
                        </div>
						<div class="p">
		                    <label class="title"><?php __('lblBookingPickupGoogleMapsLink'); ?>:</label>
		                    <span class="inline-block">
		                    	<input type="text" name="return_pickup_google_map_link" id="return_pickup_google_map_link" class="pj-form-field w400" />
		                    </span>
		                </div>
                        <div class="p">
                            <label class="title"><?php __('lblDropAddress'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_cl_destination_address" id="return_cl_destination_address" class="pj-form-field w400" />
                            </span>
                        </div>
                        <div class="p">
		                    <label class="title"><?php __('lblBookingDropoffGoogleMapsLink'); ?>:</label>
		                    <span class="inline-block">
		                    	<input type="text" name="return_dropoff_google_map_link" id="return_dropoff_google_map_link" class="pj-form-field w400" />
		                    </span>
		                </div>
                    </div>
                    
                    <div id="return_info_is_airport_1" style="display: none;">
                        <div class="p">
                            <label class="title"><?php __('front_address'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_address" id="return_c_address" class="pj-form-field w400"/>
                            </span>
                        </div>

                        <div class="p">
                            <label class="title"><?php __('lblFlightDepartureTime'); ?>:</label>
                            <span class="inline-block">
                                <select name="return_c_departure_flight_time_h" id="return_c_departure_flight_time_h" class="pj-form-field w60 required">
                                    <option value="">Hh</option>
                                    <?php for($i = 0; $i <= 23; $i++): ?>
                                        <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="return_c_departure_flight_time_m" id="return_c_departure_flight_time_m" class="pj-form-field w60 required">
                                    <option value="">Mm</option>
                                    <?php for($i = 0; $i <= 55; $i += 5): ?>
                                        <option value="<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </span>
                        </div>
                    </div>

                    <div id="return_info_is_airport_0" style="display: none;">
                        <div class="p">
                            <label class="title"><?php __('lblArrivalFlightNumber'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_flight_number" id="return_c_flight_number" class="pj-form-field w400"/>
                            </span>
                        </div>

                        <div class="p">
                            <label class="title"><?php __('lblBookingAirlineCompany'); ?>:</label>
                            <span class="inline-block">
                                <input type="text" name="return_c_airline_company" id="return_c_airline_company" class="pj-form-field w400"/>
                            </span>
                        </div>
                    </div>
                    <div class="p pjHotelName" style="display: none;">
	                    <label class="title"><?php __('lblBookingGoogleMapsLink'); ?>:</label>
	                    <span class="inline-block">
	                    <input type="text" name="return_google_map_link" id="return_google_map_link" class="pj-form-field w400" />
	                    </span>
	                </div>
                    <div class="p">
                        <label class="title"><?php __('lblBookingNotes'); ?></label>
                        <span class="inline-block">
                            <textarea name="return_c_notes" id="return_c_notes" class="pj-form-field w500 h120"></textarea>
                        </span>
                    </div>
                    <div class="p">
	                    <label class="title"><?php __('lblInternalNotes'); ?></label>
	                    <span class="inline-block">
	                        <textarea name="return_internal_notes" id="return_internal_notes" class="pj-form-field w500 h120"></textarea>
	                    </span>
	                </div>
                </div>

				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				</p>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.maximum = '<?php echo __('lblMaximum', true, false)?>';
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	myLabel.max_number = "<?php __('lblMaxNumber'); ?>";
	myLabel.loader = '<img src="<?php echo PJ_IMG_PATH;?>backend/pj-preloader.gif" />';
	</script>
	<?php
}
?>