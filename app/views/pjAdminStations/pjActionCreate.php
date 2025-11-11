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
	pjUtil::printNotice(__('infoAddStationTitle', true, false), __('infoAddStationBody', true, false));
	$index = 'tr_' . rand(1, 999999);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStations&amp;action=pjActionCreate" method="post" id="frmCreateStation" class="form pj-form" autocomplete="off">
		<input type="hidden" name="station_create" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="<?php echo $index;?>" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblStationTitle'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('tr_field_required'); ?>" />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php __('lblStationAddress'); ?></label>
				<span class="inline_block">
					<input type="text" name="address" id="address" class="pj-form-field w400 required" data-msg-required="<?php __('tr_field_required'); ?>" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStationStartFee'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="start_fee" class="pj-form-field number pj-grid-field w80" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStationFreeStartingFee'); ?></label>
				<span class="inline-block">
					<input type="text" name="free_starting_fee_in_km" id="free_starting_fee_in_km" class="pj-form-field field-int w80" data-msg-digits="<?php __('pj_digits_validation');?>" />
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
							
						</tbody>
					</table>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddPrice" />
			</p>
				
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminStations&action=pjActionIndex';" />
			</p>
		</div>
	</form>
	
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
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	}
	?>
	myLabel.locale_array = locale_array;
	myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					$('.pj-column-multilang-wrap').css('display','none');
					$(".pj-column-multilang-wrap[data-index='" + ui.index + "']").css('display','block');
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>