<select name="fleet_id" id="fleet_id" class="pj-form-field w500 required">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	foreach($tpl['fleet_arr'] as $k => $v)
	{
		?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>"><?php echo pjSanitize::html($v['fleet'] .' / '.$v['station_name']);?></option><?php
	} 
	?>
</select>