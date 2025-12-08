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
    include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	pjUtil::printNotice(__('infoAddAreaTitle', true, false), __('infoAddAreaBody', true, false)); 
	$yesno = __('_yesno', true);
	$_price_levels = __('_price_levels', true);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminAreas&amp;action=pjActionCreate" method="post" id="frmCreateArea" class="form pj-form frmArea" autocomplete="off">
		<input type="hidden" name="area_create" value="1" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblAreaName'); ?></label>
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
				<label class="title" for="order_index"><?php __('lblOrderIndex'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w60 field-int" name="order_index" id="order_index" >
				</span>
			</p>
			<div class="sbs-map-holder sbs-loader-outer">
				<div class="sbs-loader"></div>
				<div id="sbs_map_canvas" class="sbs-map-canvas"></div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminAreas&action=pjActionIndex';" />
					<input type="button" value="<?php __('btnDeleteShape'); ?>" style="display:none" class="pj-button btnDeleteShape" />
				</span>
			</p>
		</div>
	</form>
	
	<div id="dialogSetPlaceName" title="<?php __('lblSetPlaceName');?>" style="display: none">
		<form action="" class="form pj-form">
			<input type="hidden" name="coord_id" value="" />
			<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
			<div class="multilang"></div>
			<?php endif;?>
			<div class="clear_both">
				<p>
					<label class="title" for="icon"><?php __('lblIcon'); ?></label>
					<span class="inline_block float_left r10">
						<select name="location_icon" id="location_icon" class="pj-form-field w200 sbs-place-name" >
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<option value="airport"><?php __('lblIconAirport'); ?></option>
							<option value="train"><?php __('lblIconTrain'); ?></option>
							<option value="city"><?php __('lblIconCity'); ?></option>
							<option value="skiing"><?php __('lblIconSkiing'); ?></option>
						</select>
					</span>
					<span class="left">
						<?php __('lblDisableThisArea');?> <input type="checkbox" name="is_disabled" id="is_disabled" value="1" />
					</span>
				</p>
				<p>
					<label class="title" for="is_airport"><?php __('lblIsAirport'); ?></label>
					<span class="inline_block">
						<select name="is_airport" id="is_airport" class="pj-form-field w200 sbs-place-name">
	                        <option value="0" selected="selected"><?php echo $yesno['F']; ?></option>
	                        <option value="1"><?php echo $yesno['T']; ?></option>
	                    </select>
					</span>
				</p>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title" for="order_index"><?php __('lblPlaceName'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][place_name]" id="i18n_place_name_<?php echo $v['id'];?>" class="pj-form-field sbs-place-name w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('tr_field_required'); ?>" />
							<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<?php endif;?>
						</span>
					</p>
					<?php
				}
				?>
				
				<p>
					<label class="title" for="price_level"><?php __('lblAreaCoordPrice'); ?></label>
					<span class="inline_block">
						<select name="price_level" id="price_level" class="pj-form-field w200 sbs-place-name">
							<?php foreach ($_price_levels as $k => $v) { ?>
	                        	<option value="<?php echo $k;?>"><?php echo $v; ?></option>
	                        <?php } ?>
	                    </select>
					</span>
				</p>
				
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
	var locale_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.default_lat = "<?php echo $tpl['default_lat'];?>";
	myLabel.default_lng = "<?php echo $tpl['default_lng'];?>";
	myLabel.btnSave = "<?php __('btnSave');?>";
	myLabel.btnClose = "<?php __('btnClose');?>";
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