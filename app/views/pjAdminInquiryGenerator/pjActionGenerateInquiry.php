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
<hr/>
<input type="hidden" name="locale_id" id="locale_id" value="<?php echo $locale; ?>" />
<input type="hidden" name="to" id="to" value="<?php echo @$_POST['c_email']; ?>" />
<div class="clear_both">
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
    	<input type="submit" value="<?php __('btnSendInquiry', false, true); ?>" class="pj-button" />
	</p>
</div>