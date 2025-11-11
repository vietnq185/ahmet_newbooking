<?php
if(count($tpl['dropoff_arr']) > 0)
{
	$yesno = __('_yesno', true);
	foreach($tpl['dropoff_arr'] as $k => $dropoff)
	{
		$index = 'tr_' . rand(1, 999999); 
		?>
		<tr class="tr-location-row" data-index="<?php echo $index;?>">
			<td>
				<?php if (isset($_GET['type']) && $_GET['type'] == 'location_price') { ?>
					<input type="hidden" name="dropoff_ids[<?php echo $index;?>]" value="<?php echo $dropoff['id'];?>" />
				<?php } ?>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $index; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][location][<?php echo $index; ?>]"  class="pj-form-field w110" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$dropoff['i18n'][$v['id']]['location'])); ?>"/>
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
                        ?><option value="<?php echo $v['id']; ?>" <?php echo in_array($v['id'], $dropoff['area_ids']) ? 'selected="selected"' : '';?>><?php echo stripslashes($v['name']); ?></option><?php
                    }
                    ?>
                </select>								
			</td>
			<td>
				<p>
					<span class="inline_block">
						<a href="#" class="pj-remove-dropoff"></a>
					</span>
				</p>
			</td>
		</tr>
		<?php
	}
} 
?>