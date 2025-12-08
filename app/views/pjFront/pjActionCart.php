<?php
$STORE = @$tpl['store'];
$FORM_DEPARTURE = @$tpl['form']['departure'];
$cartIndex = str_replace('pjAction', '', $_GET['action']) . '_' . pjObject::escapeString($_GET['index']);
$cart = @$tpl['cart'];
$months = __('months', true);
$days = __('days', true);
ksort($months);
ksort($days);
$_date = pjUtil::formatDate($cart['date'], $tpl['option_arr']['o_date_format']);
$booking_date = $_date . ' ' . $cart['time'] . ':00';
?>
<div class="pjSbBox">
	<aside id="trCart_<?php echo $cartIndex;?>" class="one-fourth sidebar right">
	    <div class="widget">
	        <h3><?php __('front_booking_summary'); ?></h3>
	        <div class="summary">
                <h5><?php __('front_cart_departure'); ?></h5>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-calendar-alt"></i>
                	<span><?php __('front_date'); ?><br/><strong><?php echo $cart['date']; ?> (<?php echo @$days[date('w', strtotime($cart['date']))];?> <?php echo date('d', strtotime($cart['date']));?>, <?php echo @$months[date('n', strtotime($cart['date']))];?>)</strong></span>
                </div>
                <?php if (!empty($cart['time'])) { ?>
                	<div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-clock"></i>
	                	<span><?php __('front_pickup_time'); ?><br/><strong><?php echo $cart['time']; ?> (<?php echo date('h:i A', strtotime($booking_date));?>)</strong></span>
	                </div>
                <?php } ?>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-map-marker"></i>
                	<span><?php __('front_cart_from'); ?><br/><strong><?php echo pjSanitize::html($cart['pickup_location_name']); ?></strong></span>
                </div>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-map-marker"></i>
                	<span><?php __('front_cart_to'); ?><br/><strong><?php echo pjSanitize::html($cart['dropoff_location_name']); ?></strong></span>
                </div>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-taxi"></i>
                	<span><?php __('front_vehicle'); ?><br/><strong><?php echo pjSanitize::html($cart['fleet']); ?></strong></span>
                </div>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-user"></i>
                	<span><?php __('front_passengers'); ?><br/><strong class="trCartPax"><?php echo isset($cart['passengers']) && (int)$cart['passengers'] > 0 ? $cart['passengers'] : (isset($STORE['search']['passengers_from_to']) ? (int)$STORE['search']['passengers_from_to'] : 1); ?></strong></span>
                </div>
                <?php if (!empty($cart['extras'])) { ?>
                	<h5><?php __('front_extras'); ?></h5>
                	<div class="summary-item d-flex align-items-center summary-extras">
                		<span><?php echo $cart['extras'];?></span>
                	</div>
                <?php } ?>            
	        </div>
	        <?php if($cart['is_return']) { 
	            $_return_date = pjUtil::formatDate($cart['return_date'], $tpl['option_arr']['o_date_format']);
	            $return_date = $_return_date . ' ' . $cart['return_time'] . ':00';
	            ?>
		        <div class="summary">
		        	<h5><?php __('front_cart_return'); ?></h5>
		        	<?php if (!empty($cart['return_date'])) { ?>
			        	<div class="summary-item d-flex align-items-center">
		                	<i class="fad fa-calendar-alt"></i>
		                	<span><?php __('front_date'); ?><br/><strong><?php echo $cart['return_date']; ?> (<?php echo @$days[date('w', strtotime($cart['return_date']))];?> <?php echo date('d', strtotime($cart['return_date']));?>, <?php echo @$months[date('n', strtotime($cart['return_date']))];?>)</strong></span>
		                </div>
		            <?php } ?>
	                <?php if (!empty($cart['return_time'])) { ?>
	                	<div class="summary-item d-flex align-items-center">
		                	<i class="fad fa-clock"></i>
		                	<span><?php __('front_time'); ?><br/><strong><?php echo $cart['return_time']; ?> (<?php echo date('h:i A', strtotime($return_date));?>)</strong></span>
		                </div>
	                <?php } ?>
	                <div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-map-marker"></i>
	                	<span><?php __('front_cart_from'); ?><br/><strong><?php echo pjSanitize::html($cart['dropoff_location_name']); ?></strong></span>
	                </div>
	                <div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-map-marker"></i>
	                	<span><?php __('front_cart_to'); ?><br/><strong><?php echo pjSanitize::html($cart['pickup_location_name']); ?></strong></span>
	                </div>
	                <div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-taxi"></i>
	                	<span><?php __('front_vehicle'); ?><br/><strong><?php echo pjSanitize::html($cart['fleet']); ?></strong></span>
	                </div>
	                <div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-user"></i>
	                	<span><?php __('front_passengers'); ?><br/><strong class="trCartReturnPax"><?php echo isset($cart['passengers_return']) && (int)$cart['passengers_return'] > 0 ? (int)$cart['passengers_return'] : (!isset($FORM['passengers_return']) && isset($FORM_DEPARTURE['passengers']) ? (int)$FORM_DEPARTURE['passengers'] : 1); ?></strong></span>
	                </div>
	                <?php if (!empty($cart['extras_return'])) { ?>
	                	<h5><?php __('front_extras'); ?></h5>
	                	<div class="summary-item d-flex align-items-center summary-extras">
	                		<span><?php echo $cart['extras_return'];?></span>
	                	</div>
	                <?php } ?>
		        </div>
	        <?php } ?>
	        
	        <?php if(in_array($_GET['action'], array('pjActionPassenger'))) { 
		        	$payment_methods = __('payment_methods', true);
		        	$deposit = $cart['total'] * $tpl['option_arr']['o_deposit_payment'] / 100;
                    $rest = $cart['total'] - $deposit;
		        	?>
	        	<div class="summary">
	        		<h5><?php __('front_payment'); ?></h5>
	        		<div class="summary-item d-flex align-items-center">
	                	<i class="fad fa-money-bill"></i>
	                	<span>
	                		<span class="pjSbCartPaymentMethod"><?php echo @$payment_methods[$cart['payment_method']]; ?></span>
	                		<?php if($tpl['option_arr']['o_deposit_payment'] > 0) { ?>
	                            <br/><?php __('front_now_to_pay'); ?>: <span class="pjSbCartDeposit"><?php echo number_format($cart['deposit'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
	                            <br/><?php __('front_rest_to_pay'); ?>: <span class="pjSbCartRest"><?php echo number_format($rest, 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
	                        <?php } ?>
	                	</span>		                	
	                </div>
	                <div class="summary-item align-items-center pjSbCartDiscount <?php echo (float)$cart['discount'] > 0 ? 'd-flex' : 'hide';?>">
	                	<i class="fad fa-certificate"></i>
	                	<span>
		                	<span><?php __('front_discount');?></span>	
		                	<br/><span class="pjSbCartDiscountPrint"><?php echo number_format($cart['discount'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
		                </span>	                	
	                </div>
	        	</div>
	        <?php } ?>
	        
	        <div class="summary text-center pjSbTotalPrice">
	        	<h3 class="pjSbCartTotal"><?php echo number_format($cart['total'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></h3>
	        	<small><?php __('front_total_price_all_inclusive');?></small>
	        </div>
	        
	        <div class="summary">
	        	<h5><?php __('front_route_details');?></h5>
	        </div>
	        <div class="summary pjSbRouteDetails">
	        	<div class="summary-item d-flex align-items-center">
                	<i class="fad fa-route"></i>
                	<span><strong><?php echo str_replace('{NUMBER}', round($STORE['search']['distance']/1000), __('front_cart_estimated_distance', true, false));?></strong></span>
                </div>
                <div class="summary-item d-flex align-items-center">
                	<i class="fad fa-history"></i>
                	<span><strong><?php echo str_replace('{NUMBER}', round($STORE['search']['duration']/60), __('front_cart_estimated_time', true, false));?></strong></span>
                </div>
	        </div>
	    </div>
	</aside>
</div>