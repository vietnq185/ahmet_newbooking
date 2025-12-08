<?php
if (pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<ul class="menu">
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
	<?php
	if ($controller->isAdmin())
	{
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' || ($_GET['controller'] == 'pjInvoice' && in_array($_GET['action'], array('pjActionInvoices', 'pjActionUpdate', 'pjActionCreateInvoice'))) ? 'menu-focus' : NULL; ?>"><span class="menu-reports">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminInquiryGenerator&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminInquiryGenerator' ? 'menu-focus' : NULL; ?>"><span class="menu-vouchers">&nbsp;</span><?php __('menuInquirygenerator'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminNotes&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminNotes' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('tabNotes'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm', 'pjActionExportEmails', 'pjActionInstall'))) || in_array($_GET['controller'], array('pjAdminExtras', 'pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms', 'pjAdminEmailThemes', 'pjAdminInquiryTemplates', 'pjAdminLocations', 'pjAdminFleets', 'pjAdminVouchers', 'pjAdminReports', 'pjAdminDialingCodes', 'pjAdminUsers', 'pjAdminAreas', 'pjAdminStations', 'pjAdminWhatsappMessages')) || ($_GET['controller'] == 'pjInvoice' && $_GET['action'] == 'pjActionIndex') ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
		<?php
	}
	if($controller->isEditor())
	{
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminInquiryGenerator&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminInquiryGenerator' ? 'menu-focus' : NULL; ?>"><span class="menu-vouchers">&nbsp;</span><?php __('menuInquirygenerator'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminNotes&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminNotes' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('tabNotes'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm', 'pjActionExportEmails', 'pjActionInstall'))) || in_array($_GET['controller'], array('pjAdminExtras', 'pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms', 'pjAdminEmailThemes', 'pjAdminInquiryTemplates', 'pjAdminLocations', 'pjAdminFleets', 'pjAdminVouchers', 'pjAdminReports', 'pjAdminDialingCodes', 'pjAdminUsers')) || ($_GET['controller'] == 'pjInvoice' && $_GET['action'] == 'pjActionIndex') ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
		<?php
	}
	?>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
</ul>