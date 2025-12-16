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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
    $yesno = __('_yesno', true);
    $_price_levels = __('_price_levels', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionUpdate' ? ' ui-tabs-active ui-state-active' : null;?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionPrice' ? ' ui-tabs-active ui-state-active' : null;?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblPrices'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoUpdateLocationTitle', true, false), __('infoUpdateLocationDesc', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate" method="post" id="frmUpdateLocation" class="pj-form form">
		<input type="hidden" name="location_update" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif;?>
		<div class="clear_both">
			<div class="p">
				<label class="title"><?php __('lblPickupLocation'); ?>:</label>
				<div class="overflow">
					<table>
						<tr>
							<td>
								<select name="location_icon" class="pj-form-field required" style="margin-top: -10px;">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<option value="airport" <?php echo ($tpl['arr']['icon'] == 'airport') ? 'selected="selected"' : ''; ?>><?php __('lblIconAirport'); ?></option>
									<option value="train" <?php echo ($tpl['arr']['icon'] == 'train') ? 'selected="selected"' : ''; ?>><?php __('lblIconTrain'); ?></option>
									<option value="city" <?php echo ($tpl['arr']['icon'] == 'city') ? 'selected="selected"' : ''; ?>><?php __('lblIconCity'); ?></option>
									<option value="skiing" <?php echo ($tpl['arr']['icon'] == 'skiing') ? 'selected="selected"' : ''; ?>><?php __('lblIconSkiing'); ?></option>
								</select>
							</td>
							<td>
								<?php
									foreach ($tpl['lp_arr'] as $v)
									{
										?>
										<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
											<span class="inline_block">
												<input type="text" name="i18n[<?php echo $v['id']; ?>][pickup_location]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['pickup_location'])); ?>" />
												<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
													<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
												<?php endif;?>
											</span>
										</p>
										<?php
									}
								?>
							</td>
							<td>
								<p><input type="text" name="location_region" placeholder="<?php __('lblTransferRegion');?>" class="pj-form-field w300" value="<?php echo pjSanitize::html($tpl['arr']['region']); ?>" /></p>
							</td>
						</tr>
					</table>
				</div>
			</div>
			
			<p>
				<label class="title"><?php __('lblLocationAddress'); ?></label>
				<span class="inline_block">
					<input type="text" name="address" id="address" value="<?php echo pjSanitize::html($tpl['arr']['address']);?>" class="pj-form-field w400 required" data-msg-required="<?php __('tr_field_required'); ?>" />
				</span>
			</p>
			
			<p>
                <label class="title"><?php __('lblLocationArea'); ?></label>
                <span class="inline_block">
                    <select name="pickup_area_id" id="pickup_area_id" class="pj-form-field w400 select-areas">
                        <option value="">-- <?php __('lblChoose'); ?>--</option>
                         <?php
	                    foreach ($tpl['area_arr'] as $v)
	                    {
	                        ?><option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == $tpl['arr']['area_id'] ? 'selected="selected"' : '';?>><?php echo stripslashes($v['name']); ?></option><?php
	                    }
	                    ?>
                    </select>
                </span>
            </p>
            
            <p>
                <label class="title"><?php __('lblIsAirport'); ?></label>
                <span class="inline_block">
                    <select name="is_airport" id="is_airport" class="pj-form-field required">
                        <option value="">-- <?php __('lblChoose'); ?>--</option>
                        <option value="0"<?php echo 0 == $tpl['arr']['is_airport'] ? ' selected="selected"' : NULL; ?>><?php echo $yesno['F']; ?></option>
                        <option value="1"<?php echo 1 == $tpl['arr']['is_airport'] ? ' selected="selected"' : NULL; ?>><?php echo $yesno['T']; ?></option>
                    </select>
                </span>
            </p>
			<p data-order-index="1" style="<?php echo (0 == $tpl['arr']['is_airport']) ? 'display: none;' : ''; ?>">
				<label class="title"><?php __('lblOrderIndex'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w60 field-int" name="order_index" value="<?php echo $tpl['arr']['order_index']; ?>">
				</span>
			</p>
			
			<div>
    			<label class="title"><?php __('lblColor'); ?></label>
    			<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="color" class="pj-form-field colorSelector w60" value="<?php echo pjSanitize::html($tpl['arr']['color']); ?>" />
					<span class="pj-form-field-after"></span>
				</span>
    		</div>
    		<div class="clear_both"></div><br/>
			
			<div id="tr_dropoff_container" class="p tr-dropoff-container">
				<label class="title"><?php __('lblDropoff'); ?>:</label>
				<table id="tr_dropoff_table" class="tr-dropoff-table">
					<tr>
						<td>
							<label class="tr-column-name">&nbsp;</label>
						</td>
						<td>
							<label class="tr-column-name">&nbsp;</label>
						</td>
						<td>
							<label class="tr-column-name"><?php __('lblLocation');?></label>
							<?php 
							foreach ($tpl['lp_arr'] as $v)
							{ 
								?>
								<p class="pj-column-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
									<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
									<?php endif;?>
								</p>
								<?php
							} 
							?>
						</td>
						<td>
							<label class="tr-column-name"><?php __('lblLocationAreas'); ?></label>
						</td>
						<td><?php __('lblTransferRegion'); ?></td>
						<td>&nbsp;</td>
					</tr>
					<?php
					if(count($tpl['dropoff_arr']) > 0)
					{
						foreach($tpl['dropoff_arr'] as $k => $dropoff)
						{
							?>
							<tr class="tr-location-row" data-index="<?php echo $dropoff['id'];?>">
								<td>
									<div>
										<select name="base_station[<?php echo $dropoff['id']; ?>]" class="pj-form-field w150">
											<option value="">-- <?php __('lblBaseStation');?> --</option>
											<?php foreach ($tpl['station_arr'] as $station) { ?>
												<option value="<?php echo $station['id'];?>" <?php echo $station['id'] == $dropoff['base_station_id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($station['name']); ?></option>
											<?php } ?>
										</select>
									</div>
								</td>
								<td>
									<div>
										<select name="price_level[<?php echo $dropoff['id']; ?>]" class="pj-form-field required">
											<?php foreach ($_price_levels as $pk => $pv) { ?>
												<option value="<?php echo $pk;?>" <?php echo $pk == $dropoff['price_level'] ? 'selected="selected"' : '';?>><?php echo $pv; ?></option>
											<?php } ?>
										</select>
									</div>
								</td>
								<td>
									<?php
									foreach ($tpl['lp_arr'] as $v)
									{
										?>
										<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
											<span class="inline_block">
												<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $dropoff['id']; ?>]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$dropoff['i18n'][$v['id']]['location'])); ?>"/>
											</span>
										</p>
										<?php
									} 
									?>
								</td>
								<td>
									<select name="area_id[<?php echo $dropoff['id'];?>][]" id="area_id_<?php echo $dropoff['id'];?>" multiple="multiple" data-placeholder="-- <?php __('lblChoose'); ?>--" class="pj-form-field select-areas w600 required">
					                    <?php
					                    foreach ($tpl['area_arr'] as $v)
					                    {
					                        ?><option value="<?php echo $v['id']; ?>" <?php echo in_array($v['id'], $dropoff['area_ids']) ? 'selected="selected"' : '';?>><?php echo stripslashes($v['name']); ?></option><?php
					                    }
					                    ?>
					                </select>								
								</td>	
								<td>
									<p>
										<span class="inline_block">
											<input type="text" id="region_<?php echo $dropoff['id']; ?>" name="region[<?php echo $dropoff['id']; ?>]" class="pj-form-field w100" value="<?php echo $dropoff['region']; ?>"/>
										</span>
									</p>
								</td>
								<td>
									<?php if($k > 0) { ?>
									<p>
										<span class="inline_block">
											<a href="#" class="pj-remove-dropoff" data-index="<?php echo $dropoff['id']; ?>" data-cnt="<?php echo $dropoff['cnt'];?>"></a>
										</span>
									</p>
									<?php } ?>
								</td>
							</tr>
							<?php
						}
					} else {
						$index = 'tr_' . rand(1, 999999);
						?>
						<tr class="tr-location-row" data-index="<?php echo $index;?>">
							<td>
								<div>
									<select name="base_station[<?php echo $index; ?>]" class="pj-form-field w150">
										<option value="">-- <?php __('lblBaseStation');?> --</option>
										<?php foreach ($tpl['station_arr'] as $station) { ?>
											<option value="<?php echo $station['id'];?>"><?php echo pjSanitize::html($station['name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</td>
							<td>
								<div>
									<select name="price_level[<?php echo $index; ?>]" class="pj-form-field required">
										<?php foreach ($_price_levels as $pk => $pv) { ?>
											<option value="<?php echo $pk;?>" <?php echo $pk == 1 ? 'selected="selected"' : '';?>><?php echo $pv; ?></option>
										<?php } ?>
									</select>
								</div>
							</td>
							<td>
								<?php
								foreach ($tpl['lp_arr'] as $v)
								{
									?>
									<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
										<span class="inline_block">
											<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $index;?>]"  class="pj-form-field w150" lang="<?php echo $v['id']; ?>" />
										</span>
									</p>
									<?php
								} 
								?>
							</td>
							<td>
								<select name="area_id[<?php echo $index;?>][]" id="area_id_<?php echo $index;?>" multiple="multiple" data-placeholder="-- <?php __('lblChoose'); ?>--" class="pj-form-field select-areas w600 required">
				                    <?php
				                    foreach ($tpl['area_arr'] as $v)
				                    {
				                        ?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
				                    }
				                    ?>
				                </select>								
							</td>	
							<td>
								<p>
									<span class="inline_block">
										<input type="text" id="region_<?php echo $index; ?>" name="region[<?php echo $index; ?>]" class="pj-form-field w100" />
									</span>
								</p>
							</td>
						</tr>
						<?php
					} 
					?>
				</table>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-dropoff" />
				<?php
				if(!empty($tpl['location_arr']))
				{ 
					/*?><a href="#" id="tr_copy_location"><?php __('lblCopyLocation');?></a><?php*/
				} 
				?>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocations&action=pjActionIndex';" />&nbsp;
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice&id=<?php echo $tpl['arr']['id'];?>" id="tr_set_prices"><?php __('lblSetPrices');?></a>
			</p>
		</div>
	</form>
	
	<table id="tr_dropoff_table_clone" style="display: none;">
		<tr class="tr-location-row" data-index="{INDEX}">
			<td>
				<div>
					<select name="base_station[{INDEX}]" class="pj-form-field w150">
						<option value="">-- <?php __('lblBaseStation');?> --</option>
						<?php foreach ($tpl['station_arr'] as $station) { ?>
							<option value="<?php echo $station['id'];?>"><?php echo pjSanitize::html($station['name']); ?></option>
						<?php } ?>
					</select>
				</div>
			</td>
			<td>
				<div>
					<select name="price_level[{INDEX}]" class="pj-form-field required">
						<?php foreach ($_price_levels as $pk => $pv) { ?>
							<option value="<?php echo $pk;?>" <?php echo $pk == 1 ? 'selected="selected"' : '';?>><?php echo $pv; ?></option>
						<?php } ?>
					</select>
				</div>
			</td>
			<td>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][{INDEX}]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" />
						</span>
					</p>
					<?php
				} 
				?>
			</td>
			<td>
				<select name="area_id[{INDEX}][]" id="area_id_{INDEX}" multiple="multiple" data-placeholder="-- <?php __('lblChoose'); ?>--" class="pj-form-field w600 required">
                    <?php
                    foreach ($tpl['area_arr'] as $v)
                    {
                        ?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
                    }
                    ?>
                </select>
				
			</td>
			<td>
				<p>
					<span class="inline_block">
						<input type="text" id="region_{INDEX}" name="region[{INDEX}]" class="pj-form-field w100" />
					</span>
				</p>
			</td>
			<td>
				<p>
					<span class="inline_block">
						<a href="#" class="pj-remove-dropoff"></a>
					</span>
				</p>
			</td>
		</tr>
	</table>
	
	<div id="dialogCopy"  class="trCopyDialog pj-form form" style="display: none" title="<?php __('lblCopyDropoff');?>">
		<p>
			<label class="title"><?php __('lblLocation'); ?>:</label>
			<span class="inline-block">
				<select name="location_id" id="location_id" class="pj-form-field w200 required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach($tpl['location_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id'];?>"><?php echo $v['pickup_location'];?></option><?php
					} 
					?>
				</select>
			</span>
		</p>
	</div>
	<div id="dialogPrompt"  class="pj-form form" style="display: none" title="<?php __('lblDeleteDropoff');?>">
		<p>
			<?php __('lblDeleteDropoffConfirm'); ?>
		</p>
	</div>
	<div id="dialogLocationsStatus" title="<?php echo pjSanitize::html(__('lblStatusTitle', true)); ?>" style="display: none">
		<span class="bxLocationStatus bxLocationStatusStart" style="display: none"><?php __('lblStatusStart'); ?></span>
		<span class="bxLocationStatus bxLocationStatusEnd" style="display: none"><?php __('lblStatusEnd'); ?></span>
		<span class="bxLocationStatus bxLocationStatusFail" style="display: none"><?php __('lblStatusFail'); ?></span>
	</div>
	<script type="text/javascript">
	var locale_array = new Array(); 
	var fleet_array = new Array(); 
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('tr_field_required'); ?>";
	myLabel.positive_number = "<?php __('lblPositiveNumber'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	myLabel.fleet_array = fleet_array;
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