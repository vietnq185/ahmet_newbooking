<select class="pj-form-field w400 pj-install-config" id="install_dropoff_id" name="install_dropoff_id">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	foreach($tpl['dropoff_place_arr'] as $k => $v)
	{
		?><option value="server~::~<?php echo $v['id'].'~::~'.$v['dropoff_id'];?>"><?php echo pjSanitize::html($v['place_name']);?></option><?php
	} 
	?>
</select>
