<?php 
$cartIndex = str_replace('pjAction', '', $_GET['action']) . '_' . pjObject::escapeString($_GET['index']);
?>
<div class="pjSbBoxWrap pjSbPaymentWrap">
	<div class="pjSbProgress progress">
		<div class="progress-bar" style="width:66%"></div>
	</div>
	<?php if($tpl['status'] == 'OK') { ?>
		<div class="row">
			<div class="col-md-8 col-12">
				<form id="trPaymentForm_<?php echo $index;?>" action="" method="post" class="pjSbForm">
					<input type="hidden" name="step_payment" value="1"/>
					<input type="hidden" name="booking_id" value="<?php echo $tpl['arr']['id'];?>"/>
					<div class="pjSbPassengerInfo pjSbBox">
						<h3><?php __('front_payment_title'); ?></h3>
						<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
							<?php if ((float)$tpl['option_arr']['o_deposit_payment'] >= 100) { ?>
								<h6 class="text-light-grey pjSbFullPriceChargedDesc" style="display: <?php echo in_array($tpl['arr']['payment_method'], array('saferpay', 'creditcard')) ? '' : 'none';?>"><?php __('front_full_price_charged_desc');?></h6>
							<?php } else { ?>
								<h6 class="text-light-grey"><?= str_replace('{X}', (float) $tpl['option_arr']['o_deposit_payment'], __('front_deposit_payment_in_advance', true)); ?></h6>
							<?php } ?>
						<?php } ?>
						<div class="row">
							<div class="col-xs-12">
								<?php 
								if(!empty($tpl['arr']['txn_id']))
								{
									?>
									<div class="alert alert-success d-flex align-items-center">
										<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><?php __('front_messages_ARRAY_10');?></span>		
									</div>
									<?php
								}else{
									$paysafe = $tpl['paysafe_data'];
									if(isset($paysafe['body']['RedirectUrl']))
									{
										$url = $paysafe['body']['RedirectUrl'];
										?>
										<div id="trSaferpayForm_<?php echo $_GET['index'];?>">
											<iframe name="trSaferpay" id="trSaferpay_<?php echo $_GET['index'];?>" class="trSaferpayIframe" scrolling="no" src="<?php echo $url;?>" height="100%" width="100%" style="min-height: 450px;"></iframe>
										</div>
										<?php
									} else { ?>
										<div class="alert alert-warning d-flex align-items-center">
											<i class="fa-solid fa-triangle-exclamation"></i>
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
								?>
							</div>
						</div>		                		                		                		                
					</div>		
					<?php if(empty($tpl['arr']['txn_id']) && !isset($tpl['arr']['allow_saferpay_only'])) { ?>
						<div class="alert alert-warning d-flex align-items-center">
							<i class="fa-solid fa-circle-info"></i><span class="alert-desc"><?php echo __('front_select_payment_options_desc'); ?></span>		
						</div>
						<div class="pjSbPaymentInfo pjSbBox">
							<?php if($tpl['option_arr']['o_payment_disable'] == 'No') { ?>
								
								<?php
								$payment_methods_desc = __('payment_methods_desc', true);
			                	$num_pm = 0;
			                	$idx = 0;
			                	$pm_sort_arr = array('cash','creditcard_later');
			                	$payment_methods = __('payment_methods', true);
			                	$map_pm_icons = array(
			                		'cash' => '<i class="fa-solid fa-money-bill-1"></i>',
			                		'creditcard_later' => '<i class="fa-solid fa-credit-card"></i>',
			                		'saferpay' => '<i class="fa-solid fa-credit-card"></i>',
			                		'creditcard' => '<i class="fa-solid fa-display"></i>'
			                	);
			                	$total = round($tpl['arr']['total'] - $tpl['arr']['credit_card_fee']);			                	
			                	$deposit = in_array($tpl['arr']['payment_method'], array('creditcard', 'paypal', 'authorize', 'saferpay')) || is_null($tpl['arr']['payment_method']) ? (($total * $tpl['option_arr']['o_deposit_payment']) / 100): 0;
			                	$deposit = round($deposit);
			                	?>
							<?php } ?>
							<div class="pjSbPaymentMethods">
		                		<?php foreach($pm_sort_arr as $k): ?>
									<?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): 
    									$cc_fee = 0;
    									if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) {
    									    $cc_fee = round(($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100);
    									} else if ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) {
    									    $cc_fee = round(($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100);
    									}
									   ?>
										<div class="pjSbPaymentMethod">
											<div class="row form-group d-flex align-items-center">
												<div class="col-sm-9">
													<table width="100%">
														<tr>
															<td width="60"><?php echo @$map_pm_icons[$k];?></td>
															<td>
																<div class="payment-method-name"><?php echo @$payment_methods[$k];?> <?php echo number_format($total + $cc_fee, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></div>
																<div class="payment-method-desc"><?php echo @$payment_methods_desc[$k];?></div>
															</td>
														</tr>
													</table>
												</div>
												<div class="col-sm-3 text-end">
													<input type="radio" id="payment_method_<?php echo $k;?>" name="radio_payment_method" class="form-check-input trPaymentMethodSelector" value="<?php echo $k;?>">
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
			                                <?php foreach(__('payment_methods', true, false) as $k => $v): ?>
			                                    <?php if($tpl['option_arr']['o_allow_' . $k] == 'Yes'): ?>
			                                    	<?php if ($k == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format(round(($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($deposit + round((($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total + round((($total * (float)$tpl['option_arr']['o_creditcard_later_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
    		                                    	<?php } elseif ($k == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
    		                                    		<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="<?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format(round(($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?>" data-deposit="<?php echo number_format($deposit + round((($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total + round((($total * (float)$tpl['option_arr']['o_saferpay_fee'])/100)), 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
    		                                    	<?php } else { ?>
    		                                        	<option value="<?php echo $k; ?>" data-pm="<?php echo $controller->defaultPaySafePaymentMethod;?>" data-html_cc_fee="" data-deposit="<?php echo number_format($deposit, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" data-total="<?php echo number_format($total, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?>" <?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option>
    		                                        <?php } ?>
			                                    <?php endif; ?>
			                                <?php endforeach; ?>
			                            </select>
			                        </div>
			                    </div>
	                		</div>
							
						</div>		
					
						<div class="row form-group d-flex align-items-center">
							<div class="col-12 text-end"><button type="button" class="btn btn-primary btnFinishBooking" style="display: none;" data-id="<?php echo $tpl['arr']['id'];?>"><?php echo __('front_btn_finish_your_booking', true); ?></button></div>
						</div>
					
						<div id="trPaymentMsg_<?php echo $index?>" style="display: none;">
		                    <div class="alert alert-info"></div>
		                </div>
	                <?php } ?>
				</form>
			</div>
			<div class="col-md-4 col-12">
				<div class="pjSbCartWrap pjSbCartWrap_<?php echo $_GET['action'];?>">
					<div class="pjSbBox">
						<aside id="trCart_<?php echo $cartIndex;?>" class="one-fourth sidebar right">
						    <div class="widget">
						        <h3><?php __('front_booking_summary'); ?></h3>
						        <div class="summary">
					                <h5><?php __('front_cart_departure'); ?></h5>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-calendar-days"></i>
					                	<span><?php __('front_date'); ?><br/><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['booking_date'])); ?> (<?php echo @$days[date('w', strtotime($tpl['arr']['booking_date']))];?> <?php echo date('d', strtotime($tpl['arr']['booking_date']));?>, <?php echo @$months[date('n', strtotime($tpl['arr']['booking_date']))];?>)</strong></span>
					                </div>
				                	<div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-clock"></i>
					                	<span><?php __('front_time'); ?><br/><strong><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?></strong></span>
					                </div>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-location-pin"></i>
					                	<span><?php __('front_cart_from'); ?><br/><strong><?php echo $tpl['arr']['dropoff_type'] == 'server' ? pjSanitize::html($tpl['dropoff_arr']['place_name']) : pjSanitize::html($tpl['arr']['dropoff_address']);?></strong></span>
					                </div>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-location-pin"></i>
					                	<span><?php __('front_cart_to'); ?><br/><strong><?php echo $tpl['arr']['pickup_type'] == 'server' ? pjSanitize::html($tpl['pickup_arr']['pickup_location']) : pjSanitize::html($tpl['arr']['pickup_address']);?></strong></span>
					                </div>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-taxi"></i>
					                	<span><?php __('front_vehicle'); ?><br/><strong><?php echo pjSanitize::html($tpl['fleet']['fleet']);?></strong></span>
					                </div>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-user"></i>
					                	<span><?php __('front_passengers'); ?><br/><strong><?php echo pjSanitize::html($tpl['arr']['passengers']);?></strong></span>
					                </div>
					                <?php if(!empty($tpl['extra_arr'])) { 
					                	$extra_arr = array();
					                	foreach($tpl['extra_arr'] as $extra) {
					                	    if (!empty($extra['image_path'])) {
					                	        $extra_arr[] = '<img src="'.PJ_INSTALL_URL . $extra['image_path'].'" class="img-responsive" /> '.$extra['quantity'] .' x '.pjSanitize::html($extra['name']);
					                	    } else {
					                	        $extra_arr[] = $extra['quantity'] .' x '.pjSanitize::html($extra['name']);
					                	    }
					                	}
					                	?>
					                	<h5><?php __('front_extras'); ?></h5>
					                	<div class="summary-item d-flex align-items-center summary-extras">
					                		<span><?php echo implode('<br/>', $extra_arr);?></span>
					                	</div>
					                <?php } ?>            
						        </div>
						        <?php if(!empty($tpl['return_arr'])) { ?>
							        <div class="summary">
							        	<h5><?php __('front_cart_return'); ?></h5>
							        	<div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-calendar-days"></i>
						                	<span><?php __('front_date'); ?><br/><strong><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['return_date'])); ?> (<?php echo @$days[date('w', strtotime($tpl['arr']['return_date']))];?> <?php echo date('d', strtotime($tpl['arr']['return_date']));?>, <?php echo @$months[date('n', strtotime($tpl['arr']['return_date']))];?>)</strong></span>
						                </div>
					                	<div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-clock"></i>
						                	<span><?php __('front_time'); ?><br/><strong><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['return_date']));?></strong></span>
						                </div>
						                <div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-location-pin"></i>
						                	<span><?php __('front_cart_from'); ?><br/><strong><?php echo $tpl['arr']['dropoff_type'] == 'server' ? pjSanitize::html($tpl['dropoff_arr']['place_name']) : pjSanitize::html($tpl['arr']['dropoff_address']);?></strong></span>
						                </div>
						                <div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-location-pin"></i>
						                	<span><?php __('front_cart_to'); ?><br/><strong><?php echo $tpl['arr']['pickup_type'] == 'server' ? pjSanitize::html($tpl['pickup_arr']['pickup_location']) : pjSanitize::html($tpl['arr']['pickup_address']);?></strong></span>
						                </div>
						                <div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-taxi"></i>
						                	<span><?php __('front_vehicle'); ?><br/><strong><?php echo pjSanitize::html($tpl['fleet']['fleet']);?></strong></span>
						                </div>
						                <div class="summary-item d-flex align-items-center">
						                	<i class="fa-solid fa-user"></i>
						                	<span><?php __('front_passengers'); ?><br/><strong><?php echo pjSanitize::html($tpl['return_arr']['passengers']);?></strong></span>
						                </div>
						                <?php if(!empty($tpl['extra_return_arr'])) { 
							                $extra_return_arr = array();
						                	foreach($tpl['extra_return_arr'] as $extra) {
						                		if (!empty($extra['image_path'])) {
						                		    $extra_return_arr[] = '<img src="'.PJ_INSTALL_URL . $extra['image_path'].'" class="img-responsive" /> '.$extra['quantity'] .' x '.pjSanitize::html($extra['name']);
						                		} else {
						                		    $extra_return_arr[] = $extra['quantity'] .' x '.pjSanitize::html($extra['name']);
						                		}
						                	}
						                	?>
						                	<h5><?php __('front_extras'); ?></h5>
						                	<div class="summary-item d-flex align-items-center summary-extras">
					                			<span><?php echo implode('<br/>', $extra_arr);?></span>
						                	</div>
						                <?php } ?> 
							        </div>
						        <?php } ?>
						        
						        <?php 
						        $payment_methods = __('payment_methods', true);
					        	$deposit = $tpl['arr']['deposit'];
			                    $rest = $tpl['arr']['total'] - $deposit;
					        	?>
					        	<div class="summary">
					        		<h5><?php __('front_payment'); ?></h5>
					        		<div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-money-bill"></i>
					                	<span>
					                		<?php if ($tpl['arr']['payment_method'] == 'creditcard_later' && (float)$tpl['option_arr']['o_creditcard_later_fee'] > 0) { ?>
					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?><br/><?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_creditcard_later_fee'].'%', number_format($tpl['arr']['credit_card_fee'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?></span>
					                		<?php } elseif ($tpl['arr']['payment_method'] == 'saferpay' && (float)$tpl['option_arr']['o_saferpay_fee'] > 0) { ?>
					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?><br/><?php echo sprintf(__('front_credit_card_fee', true), (float)$tpl['option_arr']['o_saferpay_fee'].'%', number_format($tpl['arr']['credit_card_fee'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency']);?></span>
					                		<?php } else { ?>
					                			<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$tpl['arr']['payment_method']]; ?></span>
					                		<?php } ?>
					                		<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
					                			<span class="pjSbFullPriceChargedDesc" style="display: <?php echo in_array($tpl['arr']['payment_method'], array('saferpay', 'creditcard')) ? '' : 'none';?>">
						                            <br/><?php __('front_now_to_pay'); ?>: <span class="pjSbCartDeposit"><?php echo number_format($deposit, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
						                            <br/><?php __('front_rest_to_pay'); ?>: <span class="pjSbCartRest"><?php echo number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
					                            </span>
					                        <?php } ?>
					                	</span>		                	
					                </div>
					                <?php if ((float)$tpl['arr']['discount'] > 0) { ?>
					                <div class="summary-item align-items-center d-flex">
					                	<i class="fa-solid fa-certificate"></i>
					                	<span>
						                	<span><?php __('front_discount');?></span>	
						                	<br/><span class="pjSbCartDiscountPrint"><?php echo number_format($tpl['arr']['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
						                </span>	                	
					                </div>
					                <?php } ?>
					        	</div>
						        
						        <div class="summary text-center pjSbTotalPrice">
						        	<h3 class="pjSbCartTotal"><?php echo number_format($tpl['arr']['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></h3>
						        	<small><?php __('front_total_price_all_inclusive');?></small>
						        </div>
						        
						        <div class="summary">
						        	<h5><?php __('front_route_details');?></h5>
						        </div>
						        <div class="summary pjSbRouteDetails">
						        	<div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-route"></i>
					                	<span><strong><?php echo str_replace('{NUMBER}', $tpl['arr']['distance'], __('front_cart_estimated_distance', true, false));?></strong></span>
					                </div>
					                <div class="summary-item d-flex align-items-center">
					                	<i class="fa-solid fa-clock-rotate-left"></i>
					                	<span><strong><?php echo str_replace('{NUMBER}', $tpl['arr']['duration'], __('front_cart_estimated_time', true, false));?></strong></span>
					                </div>
						        </div>
						    </div>
						</aside>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="alert alert-warning d-flex align-items-center">
			<i class="fa-solid fa-circle-info"></i><span class="alert-desc"><span class="alert-title"><?php __('front_error')?></span><?php __('front_search_fleets_error_desc');?></span>   		
		</div>
	<?php } ?>
</div>
