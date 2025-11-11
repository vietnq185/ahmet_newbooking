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

	pjUtil::printNotice(__('infoAddDriverTitle', true, false), __('infoAddDriverBody', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionCreate" method="post" id="frmCreateDriver" class="form pj-form" autocomplete="off">
		<input type="hidden" name="driver_create" value="1" />
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
						?><option value="<?php echo $v; ?>"><?php echo $name_titles[$v]; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingFname'); ?></label>
			<span class="inline_block">
				<input type="text" name="fname" id="fname" class="pj-form-field w400 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingLname'); ?></label>
			<span class="inline_block">
				<input type="text" name="lname" id="lname" class="pj-form-field w400 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w350" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" class="pj-form-field w350" placeholder="(123) 456-7890"/>
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
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
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