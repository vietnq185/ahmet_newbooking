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
    pjUtil::printNotice(__('infoInquiryGenaratorTitle', true, false), __('infoInquiryGenaratorBody', true, false), false);
    
    $week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
    $jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
    $jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
    $locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
    if (is_null($locale))
    {
        foreach ($tpl['lp_arr'] as $v)
        {
            if ($v['is_default'] == 1)
            {
                $locale = $v['id'];
                break;
            }
        }
    }
    if (is_null($locale))
    {
        $locale = @$tpl['lp_arr'][0]['id'];
    }
    ?>
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
    	<div class="multilang"></div>
    <?php endif;?>
	<div class="clear_both">
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminInquiryGenerator&amp;action=pjActionIndex" method="post" id="frmInquiryGenerator" class="form pj-form" autocomplete="off">
    		<input type="hidden" name="action_generate" value="1" />
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
    		
    		<div class="bs-loader-outer">
    			<div class="bs-loader"></div>
        		<div class="float_right w600">
    				<p style="display: none;">
						<label class="title"><?php __('lblDuration'); ?>:</label>
						<label id="tr_duration" class="content"></label>
					</p>
					<p style="display: none;">
						<label class="title"><?php __('lblDistance'); ?>:</label>
						<label id="tr_distance" class="content"></label>
					</p>
                    
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
                            <input type="text" name="c_email" id="c_email" class="pj-form-field w400"/>
                        </span>
                    </div>
                    <div class="p">
                        <label class="title"><?php __('lblBookingPhone'); ?>:</label>
                        <span class="inline-block">
                            <input type="text" name="c_dialing_code" id="c_dialing_code" class="pj-form-field w40"/>
                            <input type="text" name="c_phone" id="c_phone" class="pj-form-field w200"/>
                        </span>
                    </div>
                    
                    <div class="p">
                        <label class="title"><?php __('lblBookingQA'); ?>:</label>
                        <span class="inline-block">
                            <input type="text" name="qa" id="qa" class="pj-form-field w400"/>
                        </span>
                    </div>
                    
                    <div class="p">
            			<label class="title">&nbsp;</label>
            			<span class="inline_block">
            				<input type="submit" value="<?php __('btnGenerateInquiry'); ?>" class="pj-button" />
            			</span>
            		</div>
    			</div>
        		<p>
    				<label class="title"><?php __('lblSelectInquiryTheme'); ?>:</label>
    				<span class="inline-block">
    					<select name="inquiry_template" id="inquiry_template" class="pj-form-field w400 required select-item">
    						<option value="">-- <?php __('lblChoose'); ?>--</option>
    						<?php
    						foreach($tpl['inquiry_template_arr'] as $k => $v)
    						{
    						    ?><option value="<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['name']);?></option><?php
    						} 
    						?>
    					</select>
    				</span>
    			</p>
        		
        		<p>
    				<label class="title"><?php __('lblPickupDate'); ?>:</label>
    				<span class="block overflow">
    					<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
							<input type="text" name="booking_date" id="booking_date" class="pj-form-field pointer w120 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
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
                    <label class="title"><?php __('lblReturnDate'); ?>:</label>
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
					<span class="inline-block">
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
    			
    			<div class="float_left w350">
                	<p style="display: none;">
    					<label class="title"><?php __('lblSubTotal'); ?>:</label>
    					<span class="pj-form-field-custom pj-form-field-custom-before">
    						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    						<input type="text" id="sub_total" name="sub_total" class="pj-form-field number w108"/>
    					</span>
    				</p>
                    <p style="display: none;">
    					<label class="title"><?php __('lblTax'); ?>:</label>
    					<span class="pj-form-field-custom pj-form-field-custom-before">
    						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    						<input type="text" id="tax" name="tax" class="pj-form-field number w108" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
    					</span>
    				</p>
                    <p style="display: none;">
                        <label class="title"><?php __('voucher_discount'); ?>:</label>
    					<span class="pj-form-field-custom pj-form-field-custom-before">
    						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    						<input type="text" id="discount" name="discount" class="pj-form-field number w108"/>
    					</span>
                    </p>
                    <p style="display: none;">
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
    				<p style="display: none;">
    					<label class="title"><?php __('lblDeposit'); ?>:</label>
    					<span class="pj-form-field-custom pj-form-field-custom-before">
    						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    						<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
    					</span>
    				</p>
                </div>
                <div class="float_left w350">
                	<div style="display: none;">
                		<p>
    						<label class="title"><?php __('lblPriceTransfer'); ?>:</label>
    						<span class="pj-form-field-custom pj-form-field-custom-before">
    							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
    							<input type="text" id="price" name="price" class="pj-form-field number w108" />
    						</span>
    					</p>
                	</div>
                	<div style="display: none;">
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
        	</div>
    	</form>
    </div>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminInquiryGenerator&amp;action=pjActionIndex" method="post" id="frmSendInquiry" class="form pj-form" autocomplete="off">
		<input type="hidden" name="send_inquiry" value="1" />
		<div class="bs-loader-outer">
			<div class="bs-loader"></div>
			<div class="inquiryTemplate"></div>
		</div>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.maximum = '<?php echo __('lblMaximum', true, false)?>';
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	myLabel.max_number = "<?php __('lblMaxNumber'); ?>";
	myLabel.loader = '<img src="<?php echo PJ_IMG_PATH;?>backend/pj-preloader.gif" />';
	
	myLabel.install_url = "<?= PJ_INSTALL_URL ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					$('#locale_id').val(ui.index);
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>