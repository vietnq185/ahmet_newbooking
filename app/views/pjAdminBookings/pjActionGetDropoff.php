<?php if (isset($_GET['is_search'])) { ?>
	<select name="dropoff_place_id" id="search_dropoff_place_id" class="pj-form-field w300">
		<option value="">-- <?php __('lblChoose'); ?>--</option>
		<?php
		foreach($tpl['dropoff_place_arr'] as $k => $v)
		{
			?><option value="<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['place_name']);?></option><?php
		} 
		?>
	</select>
<?php } else { ?>
	<select name="dropoff_id" id="dropoff_id" class="pj-form-field w500 required">
		<option value="server~::~0">-- <?php __('lblChoose'); ?>--</option>
		<?php
		foreach($tpl['dropoff_place_arr'] as $k => $v)
		{
			?><option value="server~::~<?php echo $v['id'];?>~::~<?php echo $v['dropoff_id'];?>" data-icon="<?php echo $v['icon']; ?>"><?php echo pjSanitize::html($v['text']);?></option><?php
		}
		?>
	</select>
<?php } ?>

