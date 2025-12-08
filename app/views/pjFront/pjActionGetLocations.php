<?php $index = pjObject::escapeString($_GET['index']); ?>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			<span class="fad fa-map-marker" aria-hidden="true"></span>
		</span>
		<select name="dropoff_id" id="trDropoffId_<?php echo $index;?>" class="form-control select2 required" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>">
		    <option value=""><?php __('front_to', false, false); ?></option>
		    <?php
		    foreach($tpl['dropoff_place_arr'] as $k => $v)
		    {
		        ?><option value="<?php echo $v['id_formated'];?>" data-icon="<?php echo $v['icon']; ?>"><?php echo pjSanitize::html($v['text']);?></option><?php
		    }
		    ?>
		</select>
	</div>
</div>