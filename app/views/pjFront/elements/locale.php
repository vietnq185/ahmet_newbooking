<?php
$selected_locale_title = '';
$selected_locale_flag = '';
if(count($tpl['locale_arr']) > 1)
{
	$locale_id = $controller->pjActionGetLocale();
	foreach ($tpl['locale_arr'] as $locale)
	{
		if ($locale_id == $locale['id']) {
			$selected_locale_title = pjSanitize::html($locale['title']);
			$selected_locale_flag = PJ_INSTALL_URL . 'core/framework/libs/pj/img/flags/' . $locale['file'];
		}
	}
}
?>
<div class="text-end">
	<div class="btn-group pjSbNav pjSbNavLang">
		<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<img src="<?php echo $selected_locale_flag;?>" alt="">
			<span class="title"><?php echo $selected_locale_title;?></span>
			<span class="caret"></span>
		</button>
	
		<ul class="dropdown-menu">
			<?php foreach ($tpl['locale_arr'] as $locale) { ?>
			<li class="<?php echo $locale_id == $locale['id'] ? 'active' : '';?>">
				<a href="javascript:void(0);" class="trSelectorLocale" data-id="<?php echo $locale['id'];?>" title="<?php echo pjSanitize::html($locale['title']);?>">
					<img src="<?php echo PJ_INSTALL_URL . 'core/framework/libs/pj/img/flags/' . $locale['file'];?>" alt=""> <?php echo pjSanitize::html($locale['title']);?>				
				</a>
			</li>
			<?php } ?>
		</ul><!-- /.dropdown-menu -->
	</div>
</div>