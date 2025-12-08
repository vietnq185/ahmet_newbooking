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
		$titles = __('error_titles', true, false);
		$bodies = __('error_bodies', true, false);
		
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	$days = __('days', true);
	$days[7] = $days[0];
	unset($days[0]);
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
    $jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	pjUtil::printNotice(__('infoUpdateFleetTitle', true, false), __('infoUpdateFleetDesc', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionUpdate" method="post" id="frmUpdateFleet" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="fleet_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<input type="hidden" id="index_day_arr" name="index_day_arr" value="" />
		<input type="hidden" id="remove_day_arr" name="remove_day_arr" value="" />
		
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<div class="float_left" style="width: 50%">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
				?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('lblFleet'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][fleet]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['fleet'])); ?>" />
							<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<?php endif;?>
						</span>
					</p>
					<?php
				}
				?>
			</div>
			<div class="float_left" style="width: 50%">
				<p>
					<label class="title" for="order_index"><?php __('lblVehicleBaseStation'); ?></label>
					<span class="inline_block">
						<select name="station_id" id="station_id" class="pj-form-field w300 required">
							<option value="">-- <?php __('lblChoose');?> --</option>
							<?php foreach ($tpl['station_arr'] as $station) { ?>
								<option value="<?php echo $station['id'];?>" <?php echo $tpl['arr']['station_id'] == $station['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($station['name']);?></option>
							<?php } ?>
						</select>
					</span>
				</p>
			</div>
			<div class="float_left" style="width: 50%">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
				?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('lblVehicleModel'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][model]" class="pj-form-field w400" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['model'])); ?>" />
							<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<?php endif;?>
						</span>
					</p>
					<?php
				}
				?>
			</div>
			<div class="float_left" style="width: 50%">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
				?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('lblVehicleBadget'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][badget]" class="pj-form-field w400" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['badget'])); ?>" />
							<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<?php endif;?>
						</span>
					</p>
					<?php
				}
				?>
			</div>
			<br class="clear_both">
			<p>
				<label class="title" for="order_index"><?php __('lblOrderIndex'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w60 field-int" name="order_index" id="order_index" value="<?php echo $tpl['arr']['order_index']; ?>">
				</span>
			</p>

            <?php /*foreach($days as $dayIndex => $dayName): 
				$date_from = $date_to = NULL;
		        if (isset($tpl['fleet_discount_arr'][strtolower($dayName)]['date_from']) && !empty($tpl['fleet_discount_arr'][strtolower($dayName)]['date_from']))
		        {
		            $date_from = date($tpl['option_arr']['o_date_format'], strtotime($tpl['fleet_discount_arr'][strtolower($dayName)]['date_from']));
		        }
				if (isset($tpl['fleet_discount_arr'][strtolower($dayName)]['date_to']) && !empty($tpl['fleet_discount_arr'][strtolower($dayName)]['date_to']))
		        {
		            $date_to = date($tpl['option_arr']['o_date_format'], strtotime($tpl['fleet_discount_arr'][strtolower($dayName)]['date_to']));
		        }
            	?>
                <p>
                    <label class="title"><?= $dayIndex == 1? __('lblReturnDiscount', true): '&nbsp;'; ?></label>
                    <span class="float_left r10">
                        <label class="block float_left w110 t10 r15"><?= $dayName ?></label>

                        <span class="pj-form-field-custom pj-form-field-custom-before">
                            <span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
                            <input type="text" name="return_discount_<?= $dayIndex ?>" id="return_discount_<?= $dayIndex ?>" class="pj-form-field w50 align_right pj-positive-number" value="<?php echo pjSanitize::clean($tpl['arr']["return_discount_{$dayIndex}"])?>">
                        </span>
                    </span>
					<span class="float_left r10">
						<select name="valid[<?php echo strtolower($dayName);?>]" id="valid_<?php echo $dayIndex;?>" class="pj-form-field pjAdditionalDiscountValid">
		                    <?php
								foreach (__('_fleet_additional_discount_valid', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>" <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['valid']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['valid'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
								}
							?>
		                </select>
					</span>

					<span class="float_left pjMainDiscountPeriod" style="display: <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['valid']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['valid'] == 'period' ? 'none' : '';?>">
						<span class="float_left r10">
							<select name="is_subtract[<?php echo strtolower($dayName);?>]" id="is_subtract_<?php echo $dayIndex;?>" class="pj-form-field">
								<?php
									foreach (__('_fleet_additional_discount', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>" <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['is_subtract']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['is_subtract'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
									}
								?>
							</select>
						</span>
						<span class="float_left r10">
							<input type="text" name="discount[<?php echo strtolower($dayName);?>]" id="discount_<?php echo $dayIndex;?>" value="<?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['discount']) ? $tpl['fleet_discount_arr'][strtolower($dayName)]['discount'] : NULL;?>" class="pj-form-field w80 align_right" />
							<select name="type[<?php echo strtolower($dayName);?>]" id="type_<?php echo $dayIndex;?>" class="pj-form-field">
								<?php
									foreach (__('voucher_types', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>" <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['type']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['type'] == $k ? 'selected="selected"' : null;?>><?php echo $k == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option><?php
									}
								?>
							</select>
						</span>
					</span>

					<span class="float_left pjAdditionalDiscountPeriod" style="display: <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['valid']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['valid'] == 'period' ? null : 'none';?>">
						<span class="period-wrapper" data-period="paste">
							<?php if (!empty($tpl['fleet_discount_arr'][strtolower($dayName)]['periods'])): ?>
								<?php foreach ($tpl['fleet_discount_arr'][strtolower($dayName)]['periods'] as $k => $period): ?>
									<span class="period-dates">
										<span class="float_left r10">
											<select name="periods[<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][is_subtract]" class="pj-form-field">
												<?php foreach (__('_fleet_additional_discount', true) as $kk => $v): ?>
													<option value="<?php echo $kk; ?>" <?php echo $period['is_subtract'] == $kk ? 'selected="selected"' : null;?>><?php echo $v; ?></option>
												<?php endforeach; ?>
											</select>
										</span>

										<span class="float_left r10">
											<input type="text" name="periods[<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][discount]" value="<?php echo $period['discount']; ?>" class="pj-form-field w80 align_right" />
											<select name="periods[<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][type]" class="pj-form-field">
												<?php foreach (__('voucher_types', true) as $kk => $v): ?>
													<option value="<?php echo $kk; ?>" <?php echo $period['type'] == $kk ? 'selected="selected"' : null;?>><?php echo $kk == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option>
												<?php endforeach; ?>
											</select>
										</span>

										<span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
											<input type="text" name="periods[<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][date_from]" placeholder="<?php __('fleet_additional_discount_date_from');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($period['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
											<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
										</span>

										 <span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
											<input type="text" name="periods[<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][date_to]" placeholder="<?php __('fleet_additional_discount_date_to');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($period['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
											<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
										</span>

										<span class="float_left">
											<?php if ($k == 0): ?>
												<button type="button" class="pj-button" data-period="add" data-weekday="<?php echo strtolower($dayName);?>"><?php __('btnAdd'); ?></button>
											<?php else: ?>
												<a href="javascript:;" class="period-remove" data-period="remove"></a>
											<?php endif; ?>
										</span>
									</span>
								<?php endforeach; ?>
							<?php else: ?>
								<span class="period-dates">
									<span class="float_left r10">
										<select name="periods[<?php echo strtolower($dayName);?>][0][is_subtract]" class="pj-form-field">
											<?php foreach (__('_fleet_additional_discount', true) as $kk => $v): ?>
												<option value="<?php echo $kk; ?>"><?php echo $v; ?></option>
											<?php endforeach; ?>
										</select>
									</span>

									<span class="float_left r10">
										<input type="text" name="periods[<?php echo strtolower($dayName);?>][0][discount]" value="" class="pj-form-field w80 align_right" />
										<select name="periods[<?php echo strtolower($dayName);?>][0][type]" class="pj-form-field">
											<?php foreach (__('voucher_types', true) as $kk => $v): ?>
												<option value="<?php echo $kk; ?>"><?php echo $kk == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option>
											<?php endforeach; ?>
										</select>
									</span>

									<span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
										<input type="text" name="periods[<?php echo strtolower($dayName);?>][0][date_from]" placeholder="<?php __('fleet_additional_discount_date_from');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
										<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
									</span>

									 <span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
										<input type="text" name="periods[<?php echo strtolower($dayName);?>][0][date_to]" placeholder="<?php __('fleet_additional_discount_date_to');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
										<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
									</span>

									<span class="float_left">
										<button type="button" class="pj-button" data-period="add" data-weekday="<?php echo strtolower($dayName);?>"><?php __('btnAdd'); ?></button>
									</span>
								</span>
							<?php endif; ?>
						</span>
					</span>
					
                </p>
            <?php endforeach;*/ ?>
            
            <!-- Price level 1 -->
            <?php include_once PJ_VIEWS_PATH . 'pjAdminFleets/elements/price_level_1.php';?>
            
            <!-- Price level 2 -->
            <?php include_once PJ_VIEWS_PATH . 'pjAdminFleets/elements/price_level_2.php';?>
            
			<p>
				<label class="title"><?php __('lblMinPassenger'); ?></label>
				<span class="inline-block">
					<input type="text" name="min_passengers" id="min_passengers" class="pj-form-field field-int w80" value="<?php echo pjSanitize::clean($tpl['arr']['min_passengers'])?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblMaxPassenger'); ?></label>
				<span class="inline-block">
					<input type="text" name="passengers" id="passengers" class="pj-form-field field-int w80" value="<?php echo pjSanitize::clean($tpl['arr']['passengers'])?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblLuggage'); ?></label>
				<span class="inline-block">
					<input type="text" name="luggage" id="luggage" class="pj-form-field field-int w80" value="<?php echo pjSanitize::clean($tpl['arr']['luggage'])?>"/>
				</span>
			</p>
			<p>
				<label for="crossed-out_price" class="title"><?php __('lblVehicleCrossedOutPrice'); ?></label>
				<span class="inline-block">
					<input type="text" name="crossedout_price" class="pj-form-field w80 align_right" value="<?php echo $tpl['arr']['crossedout_price']; ?>">
					<select class="pj-form-field" name="crossedout_type">
						<option value="amount" <?php echo ($tpl['arr']['crossedout_type'] == 'amount') ? 'selected' : ''; ?>><?php echo $tpl['option_arr']['o_currency']; ?></option>
						<option value="percent" <?php echo ($tpl['arr']['crossedout_type'] == 'percent') ? 'selected' : ''; ?>><?php __('lblPercent'); ?></option>
					</select>
				</span>
			</p>
			
			<p>
				<label class="title"><?php __('lblFleetStartFee'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="start_fee" value="<?php echo $tpl['arr']['start_fee'];?>" class="pj-form-field number pj-grid-field w80" />
				</span>
			</p>
			
			<div class="p">
				<label class="title"><?php __('lblPrices'); ?></label>
				<div class="overlow float_left">
					<table id="pjTbPriceTable" class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th style="width: 160px;"><?php __('lblFromInKm'); ?></th>
								<th style="width: 175px;"><?php __('lblToInKm'); ?></th>
								<th style="width: 170px;"><?php __('lblPricePerKm'); ?></th>
								<th style="width: 24px;">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($tpl['price_arr'] as $k => $v)
							{
								?>
								<tr class="pjTbPriceRow" data-index="<?php echo $v['id'];?>">
									<td>
										<span class="block overflow">
											<input type="text" name="start[<?php echo $v['id'];?>]" id="start_<?php echo $v['id'];?>" value="<?php echo pjSanitize::clean($v['start'])?>" maxlength="10" data-rule-smaller_than="#end_<?php echo $v['id'];?>" data-msg-smaller_than="<?php __('lblToGreaterThanFrom');?>" class="pj-form-field field-int w110 digits required" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
										</span>
									</td>
									<td>
										<span class="inline-block">
											<input type="text" name="end[<?php echo $v['id'];?>]" id="end_<?php echo $v['id'];?>" value="<?php echo pjSanitize::clean($v['end'])?>" maxlength="10" data-rule-not_smaller_than="#start_<?php echo $v['id'];?>" data-msg-not_smaller_than="<?php __('lblToGreaterThanFrom');?>"  class="pj-form-field field-int w110 digits required" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
										</span>
									</td>
									<td>
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
											<input type="text" name="price[<?php echo $v['id'];?>]" value="<?php echo pjSanitize::clean($v['price'])?>" class="pj-form-field  required number w50" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
										</span>
									</td>
									<td>
										<a href="#" class="lnkRemovePrice" data-index="<?php echo $v['id'];?>"></a>
									</td>
								</tr>
								<?php
							} 
							?>
						</tbody>
					</table>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddPrice" />
			</p>
			
			<p>
				<label class="title"><?php __('lblImage', false, true); ?></label>
				<span class="inline_block">
					<input type="file" name="image" id="image" class="pj-form-field w300"/>
				</span>
			</p>
			<?php
			if(!empty($tpl['arr']['source_path']))
			{
				$thumb_url = PJ_INSTALL_URL . $tpl['arr']['thumb_path'];
				?>
				<p id="image_container">
					<label class="title">&nbsp;</label>
					<span class="inline_block">
						<img class="tr-image" src="<?php echo $thumb_url; ?>" />
						<a href="javascript:void(0);" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('lblDelete');?></a>
					</span>
				</p>
				<?php
			} 
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h150 mceEditor" lang="<?php echo $v['id']; ?>" ><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
            <?php if(!empty($tpl['extra_arr'])): ?>
                <br/>
                <?php pjUtil::printNotice(__('infoExtrasLimitationsTitle', true, false), __('infoExtrasLimitationsDesc', true, false)); ?>
                <?php foreach($tpl['extra_arr'] as $index => $extra): ?>
                    <?php $current_max_qty = isset($tpl['el_arr'][$extra['id']])? $tpl['el_arr'][$extra['id']]: $tpl['option_arr']['o_extras_max_qty']; ?>
                    <div class="p">
                        <label class="title"><?= $index == 0? __('menuExtras', true): '&nbsp;'; ?></label>
                        <span class="inline-block">
                            <span class="w150" style="display: inline-block;"><?= $extra['name'] ?></span>
                            <select name="extras[<?= $extra['id'] ?>]" id="extra_<?= $extra['id'] ?>" class="pj-form-field w50">
                                <?php for($i = 0; $i <= $tpl['option_arr']['o_extras_max_qty']; $i++): ?>
                                    <option value="<?php echo $i; ?>"<?php echo $i == $current_max_qty ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminFleets&action=pjActionIndex';" />
			</p>
		</div>
	</form>

	<div data-period="copy" style="display: none;">
		<span class="period-dates">
			<span class="float_left r10">
				<select name="periods[{LEVEL}][{WEEKDAY}][{INDEX}][is_subtract]" class="pj-form-field">
					<?php foreach (__('_fleet_additional_discount', true) as $kk => $v): ?>
						<option value="<?php echo $kk; ?>"><?php echo $v; ?></option>
					<?php endforeach; ?>
				</select>
			</span>

			<span class="float_left r10">
				<input type="text" name="periods[{LEVEL}][{WEEKDAY}][{INDEX}][discount]" value="" class="pj-form-field w80 align_right" />
				<select name="periods[{LEVEL}][{WEEKDAY}][{INDEX}][type]" class="pj-form-field">
					<?php foreach (__('voucher_types', true) as $kk => $v): ?>
						<option value="<?php echo $kk; ?>"><?php echo $kk == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option>
					<?php endforeach; ?>
				</select>
			</span>

			<span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
				<input type="text" name="periods[{LEVEL}][{WEEKDAY}][{INDEX}][date_from]" placeholder="<?php __('fleet_additional_discount_date_from');?>" class="pj-form-field pointer w80 {DATEPICK}" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>

			 <span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
				<input type="text" name="periods[{LEVEL}][{WEEKDAY}][{INDEX}][date_to]" placeholder="<?php __('fleet_additional_discount_date_to');?>" class="pj-form-field pointer w80 {DATEPICK}" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>

			<span class="float_left">
				<a href="javascript:;" class="period-remove" data-period="remove"></a>
			</span>
		</span>
	</div>

	<table id="pjTbPriceClone" style="display: none">
		<tbody>
			<tr class="pjTbPriceRow" data-index="{INDEX}">
				<td>
					<span class="block overflow">
						<input type="text" name="start[{INDEX}]" id="start_{INDEX}" class="pj-form-field field-int w110 digits required" maxlength="10" data-rule-smaller_than="#end_{INDEX}" data-msg-smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
					</span>
				</td>
				<td>
					<span class="inline-block">
						<input type="text" name="end[{INDEX}]" id="end_{INDEX}" class="pj-form-field field-int w110 digits required" maxlength="10" data-rule-not_smaller_than="#start_{INDEX}" data-msg-not_smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[{INDEX}]" class="pj-form-field  required number w50" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
					</span>
				</td>
				<td>
					<a href="#" class="lnkRemovePrice" data-index="{INDEX}"></a>
				</td>
			</tr>
		</tbody>
	</table>
	
	<table id="pjTbPriceDayClone" style="display: none">
		<tbody>
			<tr class="pjTbPriceRow" data-day="{DAY}" data-index="{DAY}~:~{INDEX}">
				<td>
					<span class="block overflow">
						<input type="text" name="start_day[{DAY}][{INDEX}]" id="start_day_{DAY}_{INDEX}" class="pj-form-field w110 digits required" maxlength="10" data-rule-smaller_than="#end_day_{DAY}_{INDEX}" data-msg-smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
					</span>
				</td>
				<td>
					<span class="inline-block">
						<input type="text" name="end_day[{DAY}][{INDEX}]" id="end_day_{DAY}_{INDEX}" class="pj-form-field w110 digits required" maxlength="10" data-rule-not_smaller_than="#start_day_{DAY}_{INDEX}" data-msg-not_smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price_day[{DAY}][{INDEX}]" class="pj-form-field  required number w50" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
					</span>
				</td>
				<td>
					<a href="#" class="lnkRemovePrice" data-day="{DAY}" data-index="{INDEX}"></a>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="dialogDeleteImage" style="display: none" title="<?php __('lblDeleteImage');?>"><?php __('lblDeleteConfirmation');?></div>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tr_field_required'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
	myLabel.install_url = "<?php echo PJ_INSTALL_URL; ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>