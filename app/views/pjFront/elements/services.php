<?php if($tpl['status'] == 'OK') { ?>
	<?php if (count($tpl['fleet_arr']) > 0) { ?>
		<?php if (isset($date) && !empty($date)) { 
			$next_day_ts = strtotime($date.' -1 day');
			$start_ts = time();
			$end_ts = strtotime($date);
			$diff = abs($end_ts - $start_ts)/3600;
			if ($diff >= 24) {
				?>
				<div class="alert alert-success d-flex align-items-center">
					<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><span class="alert-title"><?php __('front_free_cancellation_title')?></span><?php echo sprintf(__('front_free_cancellation_desc', true), date('j', $next_day_ts).' '.@$months[date('n', $next_day_ts)].' '.date('Y', $next_day_ts));?></span>   		
				</div>
			<?php } else { ?>
				<div class="alert alert-success d-flex align-items-center">
					<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><span class="alert-title"><?php __('front_free_cancellation_title')?></span><?php __('front_free_cancellation_desc_1');?></span>   		
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="alert alert-success d-flex align-items-center">
				<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><span class="alert-title"><?php __('front_free_cancellation_title')?></span><?php __('front_free_cancellation_msg');?></span>   		
			</div>
		<?php } ?>
		<div class="pjSbVehicles">
		<?php
		$is_return = isset($STORE['search']['is_return']) ? $STORE['search']['is_return'] : 0; 
		foreach($tpl['fleet_arr'] as $k => $v)
		{
			$allow_book = 1;
			if (($STORE['search']['pickup_type'] == 'google' && (int)$STORE['search']['custom_pickup_id'] <= 0) || ($STORE['search']['dropoff_type'] == 'google' && (int)$STORE['search']['custom_dropoff_id'] <= 0)) {
				$params = array(
					'pickup_lat' => $STORE['search']['pickup_lat'],
					'pickup_lng' => $STORE['search']['pickup_lng'],
					'dropoff_lat' => $STORE['search']['dropoff_lat'],
					'dropoff_lng' => $STORE['search']['dropoff_lng'],
					'distance' => $STORE['search']['distance'],
					'vehicle_arr' => $v
				);
				$price_arr = $controller->getPricesBasedOnDistance($params, $tpl['option_arr']);
				$one_way_price = $price_arr['rental_price'];
				if ($price_arr['station_distance'] <= (float)$price_arr['max_base_station_distance'] && round($STORE['search']['distance']/1000) < (float)$price_arr['min_travel_distance']) {
					$allow_book = 0;
				}
				$price_by_distance = 1;
			} else {
				$one_way_price = $v['price'];
				$price_by_distance = 0;
			}
			$fleet_discount_arr = $controller->getFleetDiscount($date, $v['id'], $tpl['price_level']);
			if ($fleet_discount_arr) {
				if ($fleet_discount_arr['is_subtract'] == 'T') {
					if ($fleet_discount_arr['type'] == 'amount') {
						$one_way_price = $one_way_price - $fleet_discount_arr['discount'];
					} else {
						$one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
					}
				} else {
				    if ($price_by_distance == 0) {
    					if ($fleet_discount_arr['type'] == 'amount') {
    						$one_way_price = $one_way_price + $fleet_discount_arr['discount'];
    					} else {
    						$one_way_price = $one_way_price + (($one_way_price * $fleet_discount_arr['discount']) / 100);
    					}
				    }
				}
				if ($one_way_price < 0) {
					$one_way_price = 0;
				}
			}
			if ($price_by_distance == 1 && $tpl['price_level'] == 2) {
			    $distance = round($STORE['search']['distance']/1000);
			    $price_level2_arr = $controller->getPriceLevel2ByDistance($date, $v['id'], $distance);
			    $one_way_price = $one_way_price + ((float)$price_level2_arr['price'] * $distance);
			}
			
			$one_way_price = round($one_way_price);
			$return_price = $one_way_price;
			$extra_price = 0;
			$return_discount = $is_return == 1 ? (float)$v['return_discount'] : 0;
			$result = pjUtil::calPrice($one_way_price, $return_price, $extra_price, $is_return, $return_discount, $tpl['option_arr'], '', '');
			$price = $result['total'];
			$thumb_url = !empty($v['thumb_path'])? PJ_INSTALL_URL . $v['thumb_path']: PJ_INSTALL_URL . PJ_IMG_PATH . 'uploads/img.jpg';
			?>
			<div class="pjSbVehicle pjSbHasDiscount <?php echo $is_return == 0 && (float)$v['return_discount'] > 0 ? 'pjSbHasReturnDiscount' : '';?>">
				<div class="traveller-choice-img"><img src="<?php echo PJ_INSTALL_URL;?>app/web/img/frontend/TC_LL.svg"/></div>
				<div class="row">
					<div class="col-lg-9 col-md-12 col-sm-12 pjSbVehicleData">
						<div class="row">
							<div class="col-md-4 col-sm-12 text-center">
								<img src="<?php echo $thumb_url;?>" alt="" />
								<div class="fw-bold"><?php echo pjSanitize::html($v['model']);?></div>
							</div>
							<div class="col-md-8 col-sm-12">
								<div class="pjSbVehicleInfo">
									<div class="pjSbVehicleTitle">
		                            	<?php echo pjSanitize::clean($v['fleet']);?>
		                            	<?php if (!empty($v['badget'])) { ?>
		                            		<span class="badget"><?php echo pjSanitize::clean($v['badget']);?></span>
		                            	<?php } ?>
		                            </div>
		                            <div class="row text-blue">
		                            	<div class="col-sm-6"><i class="fa-solid fa-user" aria-hidden="true"></i> <?php echo str_replace('{NUMBER}', $v['passengers'], __('front_max_passengers', true, false)) ?></div>
		                            	<div class="col-sm-6"><i class="fa-solid fa-suitcase"></i> <?php echo str_replace('{NUMBER}', $v['luggage'], __('front_max_suitcases', true, false)) ?></div>
		                            </div>
		                            <ul>
		                            	<li><i class="fa-solid fa-credit-card"></i> <a href="javascript:void(0);" class="pjSbVehicleTipInfo" title="<?php echo pjSanitize::html($tpl['o_no_credit_card_fees_info']);?>"><?php __('front_no_credit_card_fee');?></a></li>
		                            	<li><i class="fa-solid fa-clock"></i> <a href="javascript:void(0);" class="pjSbVehicleTipInfo"  title="<?php echo pjSanitize::html($tpl['o_free_waiting_time_info']);?>"><?php __('front_free_wt');?></a></li>
		                            	<li><i class="fa-solid fa-sign-hanging"></i> <a href="javascript:void(0);" class="pjSbVehicleTipInfo" title="<?php echo pjSanitize::html($tpl['o_meet_greet_service_info']);?>"><?php __('front_meet_freet_service');?></a></li>
		                            	<li><i class="fa-solid fa-clock-rotate-left"></i> <?php echo str_replace('{NUMBER}', round($STORE['search']['duration']/60), __('front_estimated_time', true, false)); ?></li>
		                            	<li class="pjSbVehicleMoreInfoButton"><a href="javascript:void(0);" class="pjSbVehicleMoreInfo"><?php __('front_button_more_info');?></a></li>
		                            </ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-12 col-sm-12 pjSbPriceData text-center">
						<?php if($tpl['no_date_selected']) { ?>
							<div class="selectdate text-red"><i class="fa-solid fa-calendar-day"></i><br/><?php __('front_price_not_available_text')?></div>
							<a href="javascript:void(0);" class="btn btn-primary trChooseDateButton"><?php __('front_btn_choose_date', false, false);?></a>
						<?php } elseif ((float)$price <= 0 || $allow_book == 0){ ?>
							<a href="<?php echo $tpl['option_arr']['i18n'][$controller->getLocaleId()]['o_link_to_inquiry_form'];?>" class="btn btn-primary trSendInquiryButton" data-allow_book="0" data-id="<?php echo $v['id'];?>"><span><?php __('front_btn_send_inquiry');?></span></a>
						<?php } else { ?>
							<div class="prices <?php echo !empty($v['crossedout_price']) ? 'pjSbPriceHasDiscount' : '';?>">
								<?php if (!empty($v['crossedout_price'])) { ?>
									<div class="crossed-out-price fw-bold">
										<?php
											$crossedOutPrice = ($v['crossedout_type'] == 'percent') ? round($price + ($price * $v['crossedout_price'] / 100), 2) : $v['crossedout_price'];
											echo number_format(round($crossedOutPrice), 2, ',', ' ')
										?>
										<small><?php echo $tpl['option_arr']['o_currency']; ?></small>
									</div>
								<?php } ?>	
								<div class="price"><?php echo number_format($price, 2, ',', ' ');?> <small><?php echo $tpl['option_arr']['o_currency']; ?></small></div>
								<div><?php echo $is_return == 0 ? __('front_total_oneway_price', true) : __('front_total_roundtrip_price', true);?></div>
								<div class="text-green">
                                    <i class="fa-solid fa-circle-check"></i> <?php __('front_badget_free_cancellation');?>
                           		</div>
							</div>
							<a href="<?php echo $allow_book == 0 ? $tpl['option_arr']['i18n'][$controller->getLocaleId()]['o_link_to_inquiry_form'] : 'javascript:void(0);';?>" class="btn btn-primary trChooseVehicleButton" data-is_return="<?php echo $is_return;?>" data-allow_book="<?php echo $allow_book;?>" data-id="<?php echo $v['id'];?>"><i class="fas fa-check-circle" aria-hidden="true"></i> <span><?php echo $allow_book == 0 ? __('front_button_price_inquiry', true, false) : __('front_btn_select', true, false);?></span></a>
						<?php } ?>
					</div>
				</div>
				<?php if ($is_return == 0 && (float)$v['return_discount'] > 0) { ?>
					<div class="return-discount-info">
						<i class="fa-solid fa-circle-info"></i> <?php echo sprintf(__('front_return_discount_info', true), (float)$v['return_discount'].'%');?>
					</div>
				<?php } ?>
				<div class="pjSbVehicleFullDesc" style="display: none;">
					<div class="pjSbVehicleDesc">
						<h3><?php __('front_label_vehicle');?></h3>
						<?php echo $v['description']; ?>
					</div>
					<div class="pjSbVehicleServicesDesc">
						<h3><?php __('front_vehicle_services_info');?></h3>
						<div class="row form-group">
							<div class="col-md-6 col-sm-12">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="50"><i class="fa-solid fa-plane-circle-exclamation"></i></td>
										<td><div class="fw-bold mb-3"><?php __('front_vehicle_services_1_info_title');?></div><?php __('front_vehicle_services_1_info_desc');?></td>
									</tr>
								</table>
							</div>
							<div class="col-md-6 col-sm-12">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="50"><i class="fa-solid fa-sign-hanging"></i></td>
										<td><div class="fw-bold mb-3"><?php __('front_vehicle_services_2_info_title');?></div><?php __('front_vehicle_services_2_info_desc');?></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="50"><i class="fa-solid fa-credit-card"></i></i></td>
										<td><div class="fw-bold mb-3"><?php __('front_vehicle_services_3_info_title');?></div><?php __('front_vehicle_services_3_info_desc');?></td>
									</tr>
								</table>
							</div>
							<div class="col-md-6 col-sm-12">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="50"><i class="fa-solid fa-square-phone"></i></td>
										<td><div class="fw-bold mb-3"><?php __('front_vehicle_services_4_info_title');?></div><?php __('front_vehicle_services_4_info_desc');?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="text-end"><a href="javascript:void(0)" class="pjSbVehicleLessInfo"><i class="fa-solid fa-circle-xmark"></i></a></div>
				</div>
			</div>
			<?php
		}
		?>
		</div>
	<?php } else { ?>
		<div class="alert alert-info d-flex align-items-center">
			<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><span class="alert-title"><?php __('front_fleets_empty_title')?></span><?php __('front_fleets_empty_desc');?></span>   		
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="alert alert-info d-flex align-items-center">
		<i class="fa-solid fa-circle-check"></i><span class="alert-desc"><span class="alert-title"><?php __('front_search_fleets_error_title')?></span><?php __('front_search_fleets_error_desc');?></span>   		
	</div>
<?php } ?>
