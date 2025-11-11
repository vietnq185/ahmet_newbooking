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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	?>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('menuInstall'); ?></a></li>
			<li><a href="#tabs-2"><?php __('menuInstall2'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php pjUtil::printNotice(__('lblInstallJs1_title', true), __('lblInstallJs1_body', true), false, false); ?>

			<form action="" method="get" class="pj-form form">
				<fieldset class="fieldset white">
					<legend><?php __('lblInstallConfig'); ?></legend>
                    <?php if (count($tpl['locale_arr']) > 1) : ?>
					<p>
						<label class="title"><?php __('lblInstallConfigLocale'); ?></label>
						<select class="pj-form-field w400 pj-install-config" id="install_locale" name="install_locale">
							<option value="">-- <?php __('lblAll'); ?> --</option>
							<?php
							foreach ($tpl['locale_arr'] as $locale)
							{
								?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
							}
							?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('lblInstallConfigHide'); ?></label>
						<span class="left">
							<input type="checkbox" name="install_hide" value="1" />
						</span>
					</p>
                    <?php endif; ?>
                    <p>
                        <label class="title">Theme</label>
                        <span class="inline-block">
                            <select class="pj-form-field w400 pj-install-config" id="install_theme" name="install_theme">
                                <option value="">-- <?php __('lblChoose'); ?>--</option>
                                <?php
                                $themes = array(
                                    'beige' => 'Beige',
                                    'dblue' => 'Dark Blue',
                                    'dgreen' => 'Dark Green',
                                    'grey' => 'Grey',
                                    'lblue' => 'Light Blue',
                                    'lgreen' => 'Light Green',
                                    'lime' => 'Lime',
                                    'orange' => 'Orange',
                                    'peach' => 'Peach',
                                    'pink' => 'Pink',
                                    'purple' => 'Purple',
                                    'red' => 'Red',
                                    'teal' => 'Teal',
                                    'turquoise' => 'Turquoise',
                                    'yellow' => 'Yellow',
                                );
                                foreach($themes as $k => $v)
                                {
                                    ?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php
                                }
                                ?>
                            </select>
                        </span>
                    </p>
                    <p>
                        <label class="title"><?php __('lblPickupLocation'); ?></label>
                        <span class="inline-block">
                            <select class="pj-form-field w400 pj-install-config" id="install_location_id" name="install_location_id">
                                <option value="">-- <?php __('lblChoose'); ?>--</option>
                                <?php
                                foreach($tpl['pickup_arr'] as $k => $v)
                                {
                                    ?><option value="server~::~<?php echo $k;?>"><?php echo pjSanitize::html($v);?></option><?php
                                }
                                ?>
                            </select>
                        </span>
                    </p>
                    <p>
                        <label class="title"><?php __('lblDropoffLocation'); ?></label>
                        <span id="trDropoffContainer" class="inline-block">
                            <select class="pj-form-field w400 pj-install-config" id="install_dropoff_id" name="install_dropoff_id">
                                <option value="">-- <?php __('lblChoose'); ?>--</option>
                            </select>
                        </span>
                    </p>
                    <p>
                        <label class="title"><?php __('lblPricesLoadByDefault'); ?></label>
                        <span class="left">
                            <input type="checkbox" name="install_price" id="install_price" />
                        </span>
                    </p>
				</fieldset>
			</form>
			
			<p style="margin: 20px 0 7px; font-weight: bold"><?php __('lblInstallJs1_1'); ?></p>
			<textarea class="pj-form-field textarea_install" id="install_code" style="overflow: auto; height:150px; width: 1200px;">
&lt;link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad&skip_first_step=&lt;?php echo @$_GET['skip_first_step']; ?&gt;&search_pickup_id=&lt;?php echo @$_GET['search_pickup_id']; ?&gt;&search_location_id=&lt;?php echo @$_GET['search_location_id']; ?&gt;&search_dropoff_id=&lt;?php echo @$_GET['search_dropoff_id']; ?&gt;&search_passengers_from_to=&lt;?php echo @$_GET['search_passengers_from_to']; ?&gt;&search_date=&lt;?php echo @$_GET['search_date']; ?&gt;&search_is_return=&lt;?php echo @$_GET['search_is_return']; ?&gt;&search_return_date=&lt;?php echo @$_GET['search_return_date']; ?&gt;&lt;?php echo isset($_GET['loadSummary']) ? ('&loadSummary='.$_GET['loadSummary']) : '';?&gt;&lt;?php echo isset($_GET['booking_id']) ? ('&booking_id='.$_GET['booking_id']) : '';?&gt;&lt;?php echo isset($_GET['loadPayment']) ? ('&loadPayment='.$_GET['loadPayment']) : '';?&gt;&lt;?php echo isset($_GET['booking_uuid']) ? ('&booking_uuid='.$_GET['booking_uuid']) : '';?&gt;"&gt;&lt;/script&gt;</textarea>

			<div style="display:none" id="hidden_code">&lt;link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadJS&skip_first_step=&lt;?php echo @$_GET['skip_first_step']; ?&gt;&search_pickup_id=&lt;?php echo @$_GET['search_pickup_id']; ?&gt;&search_location_id=&lt;?php echo @$_GET['search_location_id']; ?&gt;&search_dropoff_id=&lt;?php echo @$_GET['search_dropoff_id']; ?&gt;&search_passengers_from_to=&lt;?php echo @$_GET['search_passengers_from_to']; ?&gt;&search_date=&lt;?php echo @$_GET['search_date']; ?&gt;&search_is_return=&lt;?php echo @$_GET['search_is_return']; ?&gt;&search_return_date=&lt;?php echo @$_GET['search_return_date']; ?&gt;&lt;?php echo isset($_GET['loadSummary']) ? ('&loadSummary='.$_GET['loadSummary']) : '';?&gt;&lt;?php echo isset($_GET['booking_id']) ? ('&booking_id='.$_GET['booking_id']) : '';?&gt;&lt;?php echo isset($_GET['loadPayment']) ? ('&loadPayment='.$_GET['loadPayment']) : '';?&gt;&lt;?php echo isset($_GET['booking_uuid']) ? ('&booking_uuid='.$_GET['booking_uuid']) : '';?&gt;"&gt;&lt;/script&gt;</div>
		</div>
		<div id="tabs-2">
			<form action="" method="get" class="pj-form form">
				<fieldset class="fieldset white">
					<?php if (count($tpl['locale_arr']) > 1) : ?>
						<p>
							<label class="title"><?php __('lblInstallConfigLocale'); ?></label>
							<select class="pj-form-field w400 pj-install-config2" id="install_locale2" name="install_locale2">
								<option value="">-- <?php __('lblAll'); ?> --</option>
								<?php
									foreach ($tpl['locale_arr'] as $locale)
									{
										?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
									}
								?>
							</select>
						</p>
						<p>
							<label class="title"><?php __('lblInstallConfigHide'); ?></label>
								<span class="left">
									<input type="checkbox" name="install_hide2" value="1" />
								</span>
						</p>
					<?php endif; ?>

					<textarea class="pj-form-field" id="install_code2" name="install_code2" style="overflow: auto; height:80px; width: 1200px;">
&lt;link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" /&gt;					
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCssNew" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadNew"&gt;&lt;/script&gt;</textarea>

					<div style="display:none" id="hidden_code2" name="hidden_code2">&lt;link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCssNew" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadNewJS"&gt;&lt;/script&gt;</div>

				</fieldset>
			</form>
		</div>
	</div>
	<?php
}
?>