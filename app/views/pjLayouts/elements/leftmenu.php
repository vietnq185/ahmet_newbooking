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
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminLocations' ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuLocations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminAreas&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminAreas' ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuAreas'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStations&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminStations' ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuStations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminFleets' ? 'menu-focus' : NULL; ?>"><span class="menu-fleets">&nbsp;</span><?php __('menuFleets'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings'  || ($_GET['controller'] == 'pjInvoice' && in_array($_GET['action'], array('pjActionInvoices', 'pjActionUpdate', 'pjActionCreateInvoice'))) ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminVouchers' ? 'menu-focus' : NULL; ?>"><span class="menu-vouchers">&nbsp;</span><?php __('menuVouchers'); ?></a></li>
		<!--<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminDrivers' ? 'menu-focus' : NULL; ?>"><span class="menu-clients">&nbsp;</span><?php __('menuDrivers'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminClients' ? 'menu-focus' : NULL; ?>"><span class="menu-clients">&nbsp;</span><?php __('menuClients'); ?></a></li>-->
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminReports' ? 'menu-focus' : NULL; ?>"><span class="menu-reports">&nbsp;</span><?php __('menuReports'); ?></a></li>
            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDialingCodes&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminDialingCodes' ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuDialingCodes'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm', 'pjActionExportEmails', 'pjActionArrivalNotice', 'pjActionInstall'))) || in_array($_GET['controller'], array('pjAdminExtras', 'pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms', 'pjAdminEmailThemes'))  || ($_GET['controller'] == 'pjInvoice' && $_GET['action'] == 'pjActionIndex') ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
		<?php
	}
	if($controller->isEditor())
	{
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
		<?php
	}
	?>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
</ul>