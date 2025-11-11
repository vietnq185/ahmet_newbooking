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
	
	pjUtil::printNotice(__('infoUpdateClientTitle', true, false), __('infoUpdateClientDesc', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate" method="post" id="frmUpdateClient" class="form pj-form">
		<input type="hidden" name="client_update" value="1" />
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
				<input type="text" name="fname" id="fname" value="<?php echo pjSanitize::html($tpl['arr']['fname']); ?>" class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingLname'); ?></label>
			<span class="inline_block">
				<input type="text" name="lname" id="lname" value="<?php echo pjSanitize::html($tpl['arr']['lname']); ?>" class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w200" value="<?php echo pjSanitize::html($tpl['arr']['email']); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="password" name="password" id="password" class="pj-form-field required w200" value="<?php echo pjSanitize::html($tpl['arr']['password']); ?>" />
			</span>
		</p>
		
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" value="<?php echo pjSanitize::html($tpl['arr']['phone']); ?>" class="pj-form-field w200" placeholder="(123) 456-7890"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingCompany'); ?></label>
			<span class="inline-block">
				<input type="text" name="company" id="company" value="<?php echo pjSanitize::html($tpl['arr']['company']); ?>" class="pj-form-field w300" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingAddress'); ?></label>
			<span class="inline-block">
				<input type="text" name="address" id="address" value="<?php echo pjSanitize::html($tpl['arr']['address']); ?>" class="pj-form-field w300" />
			</span>
		</p>
		
		<p>
			<label class="title"><?php __('lblBookingCity'); ?></label>
			<span class="inline-block">
				<input type="text" name="city" id="city" value="<?php echo pjSanitize::html($tpl['arr']['city']); ?>" class="pj-form-field w200"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingState'); ?></label>
			<span class="inline-block">
				<input type="text" name="state" id="state" value="<?php echo pjSanitize::html($tpl['arr']['state']); ?>" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingZip'); ?></label>
			<span class="inline-block">
				<input type="text" name="zip" id="zip" value="<?php echo pjSanitize::html($tpl['arr']['zip']); ?>" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblBookingCountry'); ?></label>
			<span class="inline-block">
				<select name="country_id" id="country_id" class="pj-form-field w400">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['country_arr'] as $v)
					{
						?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['country_id'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['country_title']); ?></option><?php
					}
					?>
				</select>
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
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminClients&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('email_taken', false, true); ?>";
	</script>
	<?php
}
?>