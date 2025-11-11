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
	
	pjUtil::printNotice(__('infoUpdateDriverTitle', true, false), __('infoUpdateDriverBody', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionUpdate" method="post" id="frmUpdateDriver" class="form pj-form">
		<input type="hidden" name="driver_update" value="1" />
		<input type="hidden" name="id" value="<?php echo (int) $tpl['arr']['id']; ?>" />
		<p>
			<label class="title"><?php __('lblBookingTitle'); ?></label>
			<span class="inline-block">
				<select name="title" id="title" class="pj-form-field w150 required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					$title_arr = pjUtil::getTitles();
					$name_titles = __('personal_titles', true, false);
					foreach ($title_arr as $v)
					{
						?><option value="<?php echo $v; ?>"<?php echo $tpl['arr']['title'] == $v ? ' selected="selected"' : NULL;?>><?php echo $name_titles[$v]; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingFname'); ?></label>
			<span class="inline_block">
				<input type="text" name="fname" id="fname" value="<?php echo pjSanitize::html($tpl['arr']['fname']); ?>" class="pj-form-field w400 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingLname'); ?></label>
			<span class="inline_block">
				<input type="text" name="lname" id="lname" value="<?php echo pjSanitize::html($tpl['arr']['lname']); ?>" class="pj-form-field w400 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w350" value="<?php echo pjSanitize::html($tpl['arr']['email']); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" value="<?php echo pjSanitize::html($tpl['arr']['phone']); ?>" class="pj-form-field w350" placeholder="(123) 456-7890"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminDrivers&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('email_taken', false, true); ?>";
	</script>
	<?php
}
?>