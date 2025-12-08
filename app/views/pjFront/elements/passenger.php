<div class="pjSbBoxWrap">
	<div class="pjSbProgress progress">
		<div class="progress-bar" style="width:66%"></div>
	</div>
	<?php if($tpl['status'] == 'OK') { 
	    $cart = @$tpl['cart'];
	    ?>
		<div class="row">
			<div class="col-md-8 col-12">
				<form id="trPassengerForm_<?php echo $index;?>" action="" method="post" class="pjSbForm">
					<input type="hidden" name="step_checkout" value="1"/>
					<div class="alert alert-warning d-flex align-items-center">
						<i class="fad fa-info-circle"></i><span class="alert-desc"><?php __('front_passgener_details_desc');?></span>   		
					</div>
					<div class="pjSbPassengerInfo pjSbBox">
						<h3><?php __('front_passgener_details'); ?></h3>
		                <div class="form-group pjSbPersonalTitles">
		                	 <div class="btn-group btn-group-sm">
		                	 	<?php foreach($title_arr as $k => $v) { 
		                	 		if ((isset($FORM['title']) && $FORM['title'] == $v) || (!isset($FORM['title']) && $k == 0)) {
		                	 			$title = $v;
		                	 		} 
		                	 		?>
		                	 		<button type="button" class="btn pjSbPersonalTitle <?php echo $title == $v ? 'btn-primary' : '';?>" data-value="<?php echo $v;?>"><?php echo pjSanitize::html($name_titles[$v]);?></button>	
		                	 	<?php } ?>
		                	 	<input type="hidden" name="title" id="trPersonalTitle_<?php echo $index;?>" value="<?php echo pjSanitize::html($title);?>" />
							</div> 
		                </div>
		                <div class="row">
		                	<div class="col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_fname'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fad fa-user" aria-hidden="true"></span>
										</span>				
										<input type="text" name="fname" id="fname" placeholder="<?php __('front_fname_placeholder', false, true); ?>" class="form-control required" value="<?php echo isset($FORM['fname']) ? pjSanitize::clean($FORM['fname']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
									</div>
									<small><?php __('front_fname_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
							<div class="col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_lname'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fad fa-user" aria-hidden="true"></span>
										</span>				
										<input type="text" name="lname" id="lname" placeholder="<?php __('front_lname_placeholder', false, true); ?>" class="form-control required" value="<?php echo isset($FORM['lname']) ? pjSanitize::clean($FORM['lname']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
									</div>
									<small><?php __('front_lname_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
		                </div>
		                
		                <div class="row">
		                	<div class="col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_email'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fad fa-envelope" aria-hidden="true"></span>
										</span>				
										<input type="email" name="email" id="email" placeholder="<?php __('front_email', false, true); ?>" class="form-control required" value="<?php echo isset($FORM['email']) ? pjSanitize::clean($FORM['email']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-msg-email="<?php echo pjSanitize::clean(__('front_invalid_email', true, false));?>"/>
									</div>
									<small><?php __('front_front_email_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
							<div class="col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_confirm_email'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fad fa-envelope-square" aria-hidden="true"></span>
										</span>				
										<input type="email" name="email2" id="email2" placeholder="<?php __('front_confirm_email', false, true); ?>" class="form-control required" value="<?php echo isset($FORM['email2']) ? pjSanitize::clean($FORM['email2']) : NULL;?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>" data-msg-email="<?php echo pjSanitize::clean(__('front_invalid_email', true, false));?>" data-msg-equalTo="<?php echo pjSanitize::clean(__('front_email_mismatch', true, false));?>"/>
									</div>
									<small><?php __('front_confirm_email_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
		                </div>
		                
		                <div class="row">
		                	<div class="col-sm-6 col-12">
								<div class="form-group">
									<label class="control-label"><?php __('front_country'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fad fa-globe" aria-hidden="true"></span>
										</span>				
										<select name="country_id" id="trCountryId_<?= $index ?>" class="form-control select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
				                            <option value="" data-code=""><?php __('front_choose', false, false); ?></option>
				                            <?php foreach($tpl['country_arr'] as $v) { ?>
				                                <option value="<?php echo $v['id']; ?>" data-code="<?php echo pjSanitize::html($v['code']);?>" <?php echo (isset($FORM['country_id']) && $FORM['country_id'] == $v['id']) || (!isset($FORM['country_id']) && isset($tpl['default_country_code']) && $tpl['default_country_code'] == $v['alpha_2']) ? ' selected="selected"': null; ?>><?php echo pjSanitize::html($v['country_title']);?></option>
				                            <?php } ?>
				                        </select>
									</div>
									<small><?php __('front_country_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
							<div class="col-sm-6 col-12">
								<?php 
								$full_phone_number = '';
								$dialing_code = isset($FORM['dialing_code']) ? pjSanitize::clean($FORM['dialing_code']) : (isset($tpl['default_country_phone']) ? pjSanitize::clean($tpl['default_country_phone']) : null);
								$phone = isset($FORM['phone']) ? pjSanitize::clean($FORM['phone']) : NULL;
								if (!empty($dialing_code) && !empty($phone)) {
									$full_phone_number = $dialing_code.$phone;
								}
								?>
								<div class="form-group">
									<label class="control-label"><?php __('front_mobile_phone'); ?></label>
									<div class="input-group">
									  <input type="text" name="dialing_code" id="trDialingCode_<?= $index ?>" value="<?php echo $dialing_code;?>" class="form-control pjSbDialingCode" placeholder="+43">
									  <input type="text" name="phone" id="trPhone_<?= $index ?>" value="<?php echo $phone;?>" class="form-control pjSbPhone" placeholder="123456789">
									  <input type="hidden" name="c_phone" value="<?php echo $full_phone_number;?>" class="form-control required" id="trFullPhoneNumber_<?= $index ?>" />
									</div>
									<small><?php __('front_mobile_phone_desc'); ?></small>
								</div><!-- /.form-group -->
							</div>
		                </div>		                
					</div>		
					
					<div class="pjSbPaymentInfo pjSbBox">
						<h3><?php __('front_step_payment_details'); ?></h3>
						<?php if($tpl['option_arr']['o_payment_disable'] == 'No') { ?>
							<div class="row">
								<div class="col-md-7 col-sm-8 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_discount_code'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fad fa-certificate" aria-hidden="true"></span>
											</span>				
											<input type="text" name="voucher_code" id="voucher_code" autocomplete="off" placeholder="<?php __('front_discount_code', false, true); ?>" class="form-control" value="<?php echo isset($FORM['voucher_code']) ? pjSanitize::clean($FORM['voucher_code']) : NULL;?>"/>
										</div>
										<small><?php __('front_discount_code_desc'); ?></small>
									</div><!-- /.form-group -->
								</div>
							</div>
							<?php
							$payment_methods_desc = __('payment_methods_desc', true);
		                	$selected_pm = isset($FORM['payment_method']) && $FORM['payment_method'] ? $FORM['payment_method'] : ''; 
		                	$num_pm = 0;
		                	$idx = 0;
		                	$pm_sort_arr = array('saferpay','paypal','authorize','cash','bank','creditcard_later','creditcard');
		                	foreach(__('payment_methods', true, false) as $k => $v) {
		                		if($tpl['option_arr']['o_allow_' . $k] == 'Yes') {
		                			if ($selected_pm == '' && $idx == 0) {
		                				$selected_pm = $k;
		                			}
		                			$num_pm++;
		                			$idx++;
		                		}	
		                	} 
		                	$payment_methods = __('payment_methods', true);
		                	$map_pm_icons = array(
		                		'cash' => '<i class="fad fa-money-bill-alt"></i>',
		                		'creditcard_later' => '<i class="fad fa-credit-card"></i>',
		                		'saferpay' => '<i class="fad fa-credit-card"></i>',
		                		'creditcard' => '<i class="fad fa-tv"></i>'
		                	);
		                	?>
						<?php } ?>
						<div class="pjSbPaymentMethods">
	                		<?php foreach($pm_sort_arr as $k): ?>
								<?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): 
								   $cc_fee = 0;
							       if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) {
							           $cc_fee = round(($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100);
							       } else if ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) {
							           $cc_fee = round(($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100);
							       }
							    ?>
									<div class="pjSbPaymentMethod">
										<div class="row form-group d-flex align-items-center">
											<div class="col-sm-9">
												<table width="100%">
													<tr>
														<td width="60"><?php echo @$map_pm_icons[$k];?></td>
														<td>
															<div class="payment-method-name"><?php echo @$payment_methods[$k];?> <?php echo number_format($cart['total'] + $cc_fee, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></div>
															<div class="payment-method-desc"><?php echo @$payment_methods_desc[$k];?></div>
														</td>
													</tr>
												</table>
											</div>
											<div class="col-sm-3 text-end">
												<input type="radio" id="payment_method_<?php echo $k;?>" name="radio_payment_method" class="form-check-input trPaymentMethodSelector <?php echo $selected_pm == $k ? 'trPaymentMethodSelected' : '';?>" value="<?php echo $k;?>" <?php echo $selected_pm == $k ? 'checked="checked"' : '';?>>
											</div>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
							<div class="f-row" style="display:none;">
		                        <div class="one-half">
		                            <label for="payment_method"><?php __('front_payment_medthod'); ?></label>
		                            <select name="payment_method" id="trPaymentMethod_<?php echo $index;?>" class="required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
		                                <option value="" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>"><?php __('front_choose', false, false); ?></option>
		                                <?php foreach(__('payment_methods', true, false) as $k => $v) { ?>
		                                    <?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes') { ?>
		                                    	<?php if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format(round(($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($cart['deposit'] + round((($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'] + round((($cart['total'] * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
		                                    	<?php } elseif ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format(round(($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($cart['deposit'] + round((($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'] + round((($cart['total'] * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
		                                    	<?php } else { ?>
		                                        	<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="" data-deposit="<?php echo number_format($cart['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($cart['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $selected_pm == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
		                                        <?php } ?>
		                                    <?php }  ?>
		                                <?php } ?>
		                            </select>
		                        </div>
		                    </div>
                		</div>
						<div id="trCCData_<?php echo $index;?>" style="display: <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'block' : 'none'; ?>">
							<div class="row">
								<div class="col-md-6 col-sm-12 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_cc_owner'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fad fa-user" aria-hidden="true"></span>
											</span>				
											<input type="text" name="cc_owner" class="form-control <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'required' : ''; ?>" placeholder="<?php __('front_cc_owner', false, true); ?>" value="<?php echo isset($FORM['cc_owner']) ? pjSanitize::clean($FORM['cc_owner']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small><?php __('front_cc_owner_desc'); ?></small>
									</div><!-- /.form-group -->
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-sm-12 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_cc_num'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fad fa-credit-card" aria-hidden="true"></span>
											</span>				
											<input type="text" name="cc_num" class="form-control <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'required' : ''; ?>" placeholder="<?php __('front_cc_num_placeholder', false, true); ?>" value="<?php echo isset($FORM['cc_num']) ? pjSanitize::clean($FORM['cc_num']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small></small>
									</div><!-- /.form-group -->
								</div>
								<div class="col-md-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_cc_expire_date'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fad fa-calendar-alt" aria-hidden="true"></span>
											</span>				
											<input type="text" name="cc_exp" class="form-control <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'required' : ''; ?>" placeholder="MM/YYYY" value="<?php echo isset($FORM['cc_exp']) ? pjSanitize::clean($FORM['cc_exp']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small></small>
									</div><!-- /.form-group -->
								</div>
								<div class="col-md-3 col-sm-6 col-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_cc_code'); ?></label>
										<div class="input-group">
											<span class="input-group-addon">
												<span class="fad fa-id-card" aria-hidden="true"></span>
											</span>				
											<input type="text" name="cc_code" class="form-control <?php echo isset($FORM['payment_method']) && ($FORM['payment_method'] == 'creditcard' || ($FORM['payment_method'] == 'saferpay' && $controller->defaultPaySafePaymentMethod == 'direct')) ? 'required' : ''; ?>" placeholder="CVV" value="<?php echo isset($FORM['cc_code']) ? pjSanitize::clean($FORM['cc_code']) : null;?>" autocomplete="off" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
										</div>
										<small></small>
									</div><!-- /.form-group -->
								</div>
							</div>
						</div>
						
						<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
							<?php if ((float)$tpl['option_arr']['o_deposit_payment'] >= 100) { ?>
								<h5 class="text-light-grey pjSbFullPriceChargedDesc" style="display: <?php echo in_array($selected_pm, array('saferpay', 'creditcard')) ? '' : 'none';?>"><?php __('front_full_price_charged_desc');?></h5>
							<?php } else { ?>
								<h5 class="text-light-grey"><?= str_replace('{X}', (float) $tpl['option_arr']['o_deposit_payment'], __('front_deposit_payment_in_advance', true)); ?></h5>
							<?php } ?>
						<?php } ?>
					</div>		
					
					<div class="pjSbTermsInfo pjSbBox">
						<div class="form-group">
							<div class="form-check">
							  <input class="form-check-input required" type="checkbox" id="trAgree_<?php echo $index?>" name="agreement" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
							  <label class="form-check-label" for="trAgree_<?php echo $index?>">
							    <?php __('front_agree'); ?><?php if(!empty($tpl['terms_conditions'])): ?>
		                        &nbsp;<a href="#" id="trBtnTerms_<?php echo $index?>"><i class="fad fa-info-circle" aria-hidden="true"></i></a>
		                    <?php endif; ?>
							  </label>
							</div>
						</div><!-- /.form-group -->
						<?php if(!empty($tpl['terms_conditions'])): ?>
		                    <div id="trTermContainer_<?php echo $index;?>"  style="display: none;">
		                        <p><?php echo $tpl['terms_conditions'];?></p>
		                    </div>
		                <?php endif; ?>
					</div>	
					
					<div class="row form-group d-flex align-items-center">
						<div class="col-sm-6 col-12"><a href="javascript:void(0);" class="pjSbBtnGoBack pjSbLoadDeparture"><i class="fad fa-arrow-circle-left"></i> <?php __('front_button_go_back');?></a></div>
						<div class="col-sm-6 col-12 text-end"><button type="submit" class="btn btn-primary btnBook" data-html_book="<?php __('front_btn_book_now'); ?>" data-html_book_pay="<?php __('front_btn_book_and_pay'); ?>"><?php echo $selected_pm == 'saferpay' ? __('front_btn_book_now', true) : __('front_btn_book_and_pay', true); ?></button></div>
					</div>
					
					<div id="trBookingMsg_<?php echo $index?>" style="display: none;">
	                    <div class="alert alert-info"></div>
	                </div>
				</form>
				
				<div class="pjCrBookingSesstionExpired alert alert-warning" role="alert" style="margin-top: 40px;display: none;">
                  <span class="alert-title" style="font-size: 16px; font-weight: 600;"><i class="fad fa-info-circle me-2"></i> <?php __('front_booking_sesstion_expired_title');?></span><br/><?php __('front_booking_sesstion_expired_desc');?>
                  <p align="center"><button type="button" class="btn btn-primary pjCrRestartBooking"><?php __('front_btn_restart_booking'); ?></button></p>
                </div>
			</div>
			<div class="col-md-4 col-12">
				<?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
			</div>
		</div>
	<?php } else { ?>
		<div class="alert alert-warning d-flex align-items-center">
			<i class="fad fa-info-circle"></i><span class="alert-desc"><span class="alert-title"><?php __('front_error')?></span><?php __('front_search_fleets_error_desc');?></span>   		
		</div>
	<?php } ?>
</div>
