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
    include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
    pjUtil::printNotice(__('infoAddWhatsappMessageTitle', true, false), __('infoAddWhatsappMessageBody', true, false).'<br/><br/>'.__('infoAddUpdateWhatsappMessageToken', true, false), false); 
	
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
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif;?>
	<div class="clear_both">
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminWhatsappMessages&amp;action=pjActionCreate" method="post" id="frmCreate" class="form pj-form" autocomplete="off">
    		<input type="hidden" name="action_create" value="1" />
    		<?php
    		foreach ($tpl['lp_arr'] as $v)
    		{
    			?>
    			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
    				<label class="title"><?php __('lblWMSubject'); ?></label>
    				<span class="inline_block">
    					<input type="text" name="i18n[<?php echo $v['id']; ?>][subject]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('tr_field_required'); ?>" />
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
                    <label class="title"><?php __('lblWMMessage'); ?></label>
    				<span class="inline_block">
    					<textarea name="i18n[<?php echo $v['id']; ?>][message]" class="pj-form-field w550 h300"></textarea>
    					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
                            <span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
                        <?php endif;?>
    				</span>
                </p>
                <?php
            }
            ?>
            
             <p>
    			<label class="title"><?php __('lblWMAvailableFor'); ?></label>
    			<span class="inline_block">
    				<select name="available_for" id="available_for" class="pj-form-field required">
    					<option value="">-- <?php __('lblChoose'); ?>--</option>
    					<?php
    					foreach (__('wm_available_for', true) as $k => $v)
    					{
    						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
    					}
    					?>
    				</select>
    			</span>
    		</p>
    		
    		<p>
				<label class="title" for="order"><?php __('lblWMOrder'); ?></label>
				<span class="inline_block">
					<input type="text" class="pj-form-field w60 field-int" name="order" id="order" value="">
				</span>
			</p>
			
            <p>
    			<label class="title"><?php __('lblWMStatus'); ?></label>
    			<span class="inline_block">
    				<select name="status" id="status" class="pj-form-field required">
    					<option value="">-- <?php __('lblChoose'); ?>--</option>
    					<?php
    					foreach (__('wm_statuses', true) as $k => $v)
    					{
    						?><option value="<?php echo $k; ?>" <?php echo $k == 'T' ? 'selected="selected"' : '';?>><?php echo $v; ?></option><?php
    					}
    					?>
    				</select>
    			</span>
    		</p>
    		
    		<p>
    			<label class="title">&nbsp;</label>
    			<span class="inline_block">
    				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
    				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminWhatsappMessages&action=pjActionIndex';" />
    			</span>
    		</p>
    	</form>
    </div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.install_url = "<?= PJ_INSTALL_URL ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>