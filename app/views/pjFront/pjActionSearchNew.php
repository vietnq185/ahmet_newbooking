<?php 
$index = pjObject::escapeString($_GET['index']);
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$is_return = isset($tpl['search_post']['is_return']) ? (int)$tpl['search_post']['is_return'] : 0;
?>
<div id="pjSbCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
<div class="pjSbSearchForm">
	<?php if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr'])) { ?>
		<div class="row pjSbHeadNav d-flex d-sm-none">
			<div class="col-sm-6 col-12 float-end">
				<div class="form-group">
					<?php include 'elements/locale.php';?>
				</div>
			</div>
			<div class="col-sm-6 col-12 float-start">
				<div class="form-group">
					<div class="btn-group pjSbFormNav">
						<a href="#" class="btn pjSbSwitch pjSbSwitchOneWay <?php echo $is_return == 0 ? 'btn-primary' : '';?>"><?php __('front_one_way');?></a>
						<a href="#" class="btn pjSbSwitch pjSbSwitchReturn <?php echo $is_return == 1 ? 'btn-primary' : '';?>"><?php __('front_with_return');?></a>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row pjSbHeadNav d-none d-sm-flex">
			<div class="col-sm-6 col-12 float-start">
				<div class="form-group">
					<div class="btn-group pjSbFormNav">
						<a href="#" class="btn pjSbSwitch pjSbSwitchOneWay <?php echo $is_return == 0 ? 'btn-primary' : '';?>"><?php __('front_one_way');?></a>
						<a href="#" class="btn pjSbSwitch pjSbSwitchReturn <?php echo $is_return == 1 ? 'btn-primary' : '';?>"><?php __('front_with_return');?></a>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-12 float-end">
				<div class="form-group">
					<?php include 'elements/locale.php';?>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="form-group">
			<div class="btn-group pjSbFormNav">
				<a href="#" class="btn pjSbSwitch pjSbSwitchOneWay <?php echo $is_return == 0 ? 'btn-primary' : '';?>"><?php __('front_one_way');?></a>
				<a href="#" class="btn pjSbSwitch pjSbSwitchReturn <?php echo $is_return == 1 ? 'btn-primary' : '';?>"><?php __('front_with_return');?></a>
			</div>
		</div>
	<?php } ?>
	<form id="trSearchForm_<?php echo $index;?>" action="" method="post" class="form">
		<input type="hidden" name="is_return" id="is_return_<?php echo $index;?>" value="<?php echo $is_return;?>">
		<input type="hidden" name="is_seperate_search_form" value="1">
		
		<input type="hidden" name="index" value="<?= $index ?>">
		<input type="hidden" name="autoload_next_step" id="autoloadNextStep_<?php echo $index;?>" value="<?= (int) @$tpl['search_post']['autoload_next_step']; ?>"/>
		<input type="hidden" name="skip_first_step" value="1">
		
		<input type="hidden" name="custom_pickup_id" id="custom_pickup_id_<?php echo $index;?>" value="">
		
		<input type="hidden" name="dropoff_lat" id="dropoff_lat_<?php echo $index;?>" value="">
		<input type="hidden" name="dropoff_lng" id="dropoff_lng_<?php echo $index;?>" value="">
		
		<input type="hidden" name="custom_dropoff_id" id="custom_dropoff_id_<?php echo $index;?>" value="">
		<input type="hidden" name="custom_dropoff_place_id" id="custom_dropoff_place_id_<?php echo $index;?>" value="">
		
		<div class="row pjSbPickupDropoffLocations">
			<div class="col-sm-6 col-xs-12 pjSbPickupLocation <?php echo isset($tpl['search_post']) && !empty($tpl['search_post']) ? 'hasSelected' : '';?>">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
						</span>
						<select name="search_location_id" id="trLocationId_<?php echo $index;?>" class="form-control select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
							<option value=""><?php __('front_from', false, false); ?></option>
							<?php
							foreach($tpl['pickup_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id_formated'];?>" <?php echo isset($tpl['search_post']) ? ($tpl['search_post']['location_id'] == $v['id'] ? ' selected="selected"' : null) : null;?> data-icon="<?php echo $v['icon']; ?>"><?php echo pjSanitize::html($v['text']);?></option><?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="d-none d-sm-block">
					<a href="javascript:void(0);" class="pjSbSwitchLocation pjSbSwitchLocationRightLeft"><i class="fa-solid fa-arrow-right-arrow-left"></i></a>
				</div>
				<div class="d-block d-sm-none">
					<a href="javascript:void(0);" class="pjSbSwitchLocation pjSbSwitchLocationUpDown"><i class="fa-solid fa-arrows-up-down"></i></a>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12 pjSbDropoffLocation <?php echo isset($tpl['search_post']) && !empty($tpl['search_post']) ? 'hasSelected' : '';?>">
				<div id="dropoffBox_<?php echo $index;?>">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<span class="fa-solid fa-location-pin" aria-hidden="true"></span>
							</span>
							<select name="search_dropoff_id" id="trDropoffId_<?php echo $index;?>" class="form-control select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
								<option value=""><?php __('front_to', false, false); ?></option>
								<?php
								if(isset($tpl['search_post']))
								{
									foreach($tpl['dropoff_place_arr'] as $k => $v)
									{
										?><option value="<?php echo $v['id_formated'];?>" <?php echo isset($tpl['search_post']) ? ($tpl['search_post']['dropoff_place_id'] == $v['id'] ? ' selected="selected"' : null) : null;?> data-icon="<?php echo $v['icon']; ?>"><?php echo pjSanitize::html($v['text']);?></option><?php
									}
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="form-group">
					<div class="input-group pjSbTransferDateContainer">
						<span class="input-group-addon">
							<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
						</span>			
						<input type="text" placeholder="<?php __('front_transfer_date', false, true); ?>" id="trDate_<?php echo $index?>" name="search_date" readonly value="<?php echo isset($tpl['search_post']) && isset($tpl['search_post']['date']) ? htmlspecialchars($tpl['search_post']['date']) : null; ?>" class="form-control required hasDatepicker" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					</div>
				</div><!-- /.form-group -->
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="form-group pjSbAddReturnTransfer">
					<div class="input-group">
						<span class="input-group-addon text-blue">
							<span class="fa-solid fa-circle-plus" aria-hidden="true"></span>
						</span>				
						<input type="text" placeholder="<?php __('front_add_return_transfer', false, true); ?>" id="trAddReturnTransfer_<?php echo $index?>" name="add_return_transfer" readonly class="form-control pjSbAddReturnTransfer" />
					</div>
				</div><!-- /.form-group -->
				
				<div class="form-group pjSbReturnTransferDateWrap" style="display: none;">
					<div class="input-group pjSbReturnTransferDateContainer">
						<span class="input-group-addon">
							<span class="fa-solid fa-calendar-days" aria-hidden="true"></span>
						</span>				
						<input type="text" placeholder="<?php __('front_return_transfer_date', false, true); ?>" id="trReturnDate_<?php echo $index?>" name="search_return_date" readonly value="<?php echo isset($tpl['search_post']) && isset($tpl['search_post']['return_date']) ? htmlspecialchars($tpl['search_post']['return_date']) : null; ?>" class="form-control hasDatepicker <?php echo $is_return == 1 ? 'required' : '';?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					</div>
				</div><!-- /.form-group -->
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="form-group pjSbPassengers pjSbSpinWrap">
					<div class="input-group">
						<div class="pjSbSpinLeft">
							<a href="javascript:void(0);" class="pjSbSpin" data-type="minus" data-min="<?php echo (int)$tpl['min_passenger'];?>">
								<span class="input-group-addon text-blue">
									<span class="fa-solid fa-circle-minus" aria-hidden="true"></span>
								</span>		
							</a>
						</div>
						<span class="input-group-addon">
							<span class="fa-solid fa-user" aria-hidden="true"></span>
						</span>				
						<input type="text" name="search_passengers_from_to" id="trNumPassengers_<?php echo $index?>" readonly="readonly" value="<?php echo isset($tpl['search_post']['passengers_from_to']) && (int)$tpl['search_post']['passengers_from_to'] > 0 ? (int)$tpl['search_post']['passengers_from_to'] : 1; ?>" class="form-control text-center required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
						<div class="pjSbSpinRight">
							<a href="javascript:void(0);" class="pjSbSpin" data-type="plus" data-max="<?php echo (int)$tpl['max_passenger'];?>">
								<span class="input-group-addon text-blue">
									<span class="fa-solid fa-circle-plus" aria-hidden="true"></span>
								</span>		
							</a>
						</div>
					</div>
				</div><!-- /.form-group -->
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="form-group d-grid gap-2">
					<button type="submit" class="btn btn-primary"><?php __('front_button_see_prices'); ?></button>
				</div>
			</div>
		</div>
		<div class="trCheckErrorMsg" style="display: none;">
			<div class="alert alert-info"></div>
		</div>
	</form>
	
	<div class="modal fade" id="pjSbTransferDateModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_transfer_date_title');?></div>
        			<div style="overflow:hidden;">
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <div id="trTransferDatePick_<?php echo $index?>"></div>
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
    <div class="modal fade" id="pjSbReturnTransferDateModal_<?php echo $index;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      	<div class="modal-dialog modal-sm">
        	<div class="modal-content">
        		<div class="modal-body">
        			<div align="center" class="fw-bold pb-2"><?php __('front_select_return_transfer_date_title');?></div>
        			<div style="overflow:hidden;">
                       <div class="form-group">
                          <div class="row">
                             <div class="col-md-12">
                                <div id="trReturnTransferDatePick_<?php echo $index?>"></div>
                             </div>
                          </div>
                       </div>
                    </div>
    		    </div>
        	</div>
      	</div>
    </div>
    
</div>