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
	
	pjUtil::printNotice(__('infoUpdateDialingCodeTitle', true, false), __('infoUpdateDialingCodeBody', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDialingCodes&amp;action=pjActionUpdate" method="post" id="frmUpdateDialingCode" class="form pj-form">
		<input type="hidden" name="dialing_code_update" value="1" />
		<input type="hidden" name="id" value="<?php echo (int) $tpl['arr']['id']; ?>" />
        <p>
            <label class="title"><?php __('lblBookingCountry'); ?></label>
			<span class="inline-block">
				<select name="country_id" id="country_id" class="pj-form-field w400 required">
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
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="code" id="code" value="<?php echo pjSanitize::html($tpl['arr']['code']); ?>" class="pj-form-field w200" placeholder="+44"/>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminDialingCodes&action=pjActionIndex';" />
		</p>
	</form>
	<?php
}
?>