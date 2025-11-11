<h3><?php __('lblDefaultSeasonalPrices');?></h3>
<?php foreach($days as $dayIndex => $dayName): 
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
			<select name="valid[1][<?php echo strtolower($dayName);?>]" id="valid_<?php echo $dayIndex;?>_1" class="pj-form-field pjAdditionalDiscountValid">
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
				<select name="is_subtract[1][<?php echo strtolower($dayName);?>]" id="is_subtract_<?php echo $dayIndex;?>_1" class="pj-form-field">
					<?php
						foreach (__('_fleet_additional_discount', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>" <?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['is_subtract']) && $tpl['fleet_discount_arr'][strtolower($dayName)]['is_subtract'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
						}
					?>
				</select>
			</span>
			<span class="float_left r10">
				<input type="text" name="discount[1][<?php echo strtolower($dayName);?>]" id="discount_<?php echo $dayIndex;?>_1" value="<?php echo isset($tpl['fleet_discount_arr'][strtolower($dayName)]['discount']) ? $tpl['fleet_discount_arr'][strtolower($dayName)]['discount'] : NULL;?>" class="pj-form-field w80 align_right" />
				<select name="type[1][<?php echo strtolower($dayName);?>]" id="type_<?php echo $dayIndex;?>" class="pj-form-field">
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
								<select name="periods[1][<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][is_subtract]" class="pj-form-field">
									<?php foreach (__('_fleet_additional_discount', true) as $kk => $v): ?>
										<option value="<?php echo $kk; ?>" <?php echo $period['is_subtract'] == $kk ? 'selected="selected"' : null;?>><?php echo $v; ?></option>
									<?php endforeach; ?>
								</select>
							</span>

							<span class="float_left r10">
								<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][discount]" value="<?php echo $period['discount']; ?>" class="pj-form-field w80 align_right" />
								<select name="periods[1][<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][type]" class="pj-form-field">
									<?php foreach (__('voucher_types', true) as $kk => $v): ?>
										<option value="<?php echo $kk; ?>" <?php echo $period['type'] == $kk ? 'selected="selected"' : null;?>><?php echo $kk == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option>
									<?php endforeach; ?>
								</select>
							</span>

							<span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
								<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][date_from]" placeholder="<?php __('fleet_additional_discount_date_from');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($period['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>

							 <span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
								<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][<?php echo $period['id']; ?>][date_to]" placeholder="<?php __('fleet_additional_discount_date_to');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($period['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>

							<span class="float_left">
								<?php if ($k == 0): ?>
									<button type="button" class="pj-button" data-period="add" data-level="1" data-weekday="<?php echo strtolower($dayName);?>"><?php __('btnAdd'); ?></button>
								<?php else: ?>
									<a href="javascript:;" class="period-remove" data-period="remove"></a>
								<?php endif; ?>
							</span>
						</span>
					<?php endforeach; ?>
				<?php else: ?>
					<span class="period-dates">
						<span class="float_left r10">
							<select name="periods[1][<?php echo strtolower($dayName);?>][0][is_subtract]" class="pj-form-field">
								<?php foreach (__('_fleet_additional_discount', true) as $kk => $v): ?>
									<option value="<?php echo $kk; ?>"><?php echo $v; ?></option>
								<?php endforeach; ?>
							</select>
						</span>

						<span class="float_left r10">
							<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][0][discount]" value="" class="pj-form-field w80 align_right" />
							<select name="periods[1][<?php echo strtolower($dayName);?>][0][type]" class="pj-form-field">
								<?php foreach (__('voucher_types', true) as $kk => $v): ?>
									<option value="<?php echo $kk; ?>"><?php echo $kk == 'amount' ? $tpl['option_arr']['o_currency'] : $v; ?></option>
								<?php endforeach; ?>
							</select>
						</span>

						<span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
							<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][0][date_from]" placeholder="<?php __('fleet_additional_discount_date_from');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>

						 <span class="float_left pj-form-field-custom pj-form-field-custom-after r10">
							<input type="text" name="periods[1][<?php echo strtolower($dayName);?>][0][date_to]" placeholder="<?php __('fleet_additional_discount_date_to');?>" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>

						<span class="float_left">
							<button type="button" class="pj-button" data-period="add" data-level="1" data-weekday="<?php echo strtolower($dayName);?>"><?php __('btnAdd'); ?></button>
						</span>
					</span>
				<?php endif; ?>
			</span>
		</span>
		
    </p>
<?php endforeach; ?>