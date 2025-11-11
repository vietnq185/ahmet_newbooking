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
	?>
	<?php
	$locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
	if (is_null($locale))
	{
		foreach ($tpl['lp_arr'] as $v)
		{
			if ($v['is_default'] == 1)
			{
				$locale = $v['id'];
				break;
			}
		}
	}
	if (is_null($locale))
	{
		$locale = @$tpl['lp_arr'][0]['id'];
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailReturnReminder&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblRemindClientForReturnViaEmail'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionEmailReturnReminder&amp;id=<?php echo $tpl['arr']['id']; ?>" method="post" class="form pj-form" id="frmBookingEmailReturnReminder">
		<input type="hidden" name="email_return_reminder" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<input type="hidden" name="locale_id" id="locale_id" value="<?php echo $locale; ?>" />
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang b10"></div>
		<?php endif;?>
		<div class="clear_both">
			<p>
				<label class="title"><?php __('lblReminderTo', false, true); ?></label>
				<span class="inline-block">
					<input type="text" name="to" id="to" class="pj-form-field w450 required email" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_email'])); ?>" />
				</span>
			</p>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblReminderSubject'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][subject]" class="pj-form-field w450 <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"  value="<?php echo htmlspecialchars(stripslashes(@$tpl['i18n_arr'][$v['id']]['subject'])); ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<?php
            foreach ($tpl['lp_arr'] as $v)
            {
                ?>
                <p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
                    <label class="title"><?php __('lblReminderMessage'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][message]" class="pj-form-field w550 h300 mceEditor"><?php echo htmlspecialchars(stripslashes(@$tpl['i18n_arr'][$v['id']]['message'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
                            <span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
                        <?php endif;?>
					</span>
                </p>
                <?php
            }
            ?>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSend', false, true); ?>" class="pj-button" />&nbsp;
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>" class="pj-back"><?php __('btnBack', false, true);?></a>
			</p>
		</div>
	</form>
	<?php
	
}
?>
<script type="text/javascript">
    var myLabel = myLabel || {};
    myLabel.install_url = "<?= PJ_INSTALL_URL ?>";

    (function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					$('#locale_id').val(ui.index);
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
</script>
