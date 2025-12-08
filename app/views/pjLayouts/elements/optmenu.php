<?php
$active = " ui-tabs-active ui-state-active";
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<?php if ($controller->isAdmin()) { ?>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjAdminOptions' || $_GET['action'] != 'pjActionIndex' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex"><?php __('menuGeneral'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm', 'pjActionArrivalNotice', 'pjActionGeneralInformation', 'pjActionInstall') )) || $_GET['controller'] == 'pjAdminExtras' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBooking"><?php __('menuReservation'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionExportEmails' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionExportEmails"><?php __('tabExportEmails'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminEmailThemes' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminEmailThemes&amp;action=pjActionIndex"><?php __('tabEmailsThemes'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminInquiryTemplates' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminInquiryTemplates&amp;action=pjActionIndex"><?php __('tabInquiryTemplates'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminWhatsappMessages' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminWhatsappMessages&amp;action=pjActionIndex"><?php __('tabWhatsappMessages'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjLocale' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLocale&amp;action=pjActionIndex&amp;tab=1"><?php __('menuLocales'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjBackup' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBackup&amp;action=pjActionIndex"><?php __('menuBackup'); ?></a></li>
    		<?php
    		if (pjObject::getPlugin('pjSms') !== NULL)
    		{
    			?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjSms' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjSms&amp;action=pjActionIndex"><?php __('plugin_sms_menu_sms'); ?></a></li><?php
    		} 
    		?>
    		<?php
    		if (pjObject::getPlugin('pjInvoice') !== NULL)
    	    {
        		?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjInvoice' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionIndex"><?php __('plugin_invoice_menu_invoices'); ?></a></li><?php
    		} 
    		?>
			<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminLocations' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex"><?php __('menuLocations'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminAreas' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminAreas&amp;action=pjActionIndex"><?php __('menuAreas'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminStations' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStations&amp;action=pjActionIndex"><?php __('menuStations'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminFleets' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionIndex"><?php __('menuFleets'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminVouchers' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('menuVouchers'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminReports' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex"><?php __('menuReports'); ?></a></li>
            <li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminDialingCodes' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDialingCodes&amp;action=pjActionIndex"><?php __('menuDialingCodes'); ?></a></li>
    		<li class="ui-state-default ui-corner-top <?php echo $_GET['controller'] == 'pjAdminUsers' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex"><?php __('menuUsers'); ?></a></li>
		<?php } else { ?>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminLocations' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex"><?php __('menuLocations'); ?></a></li>
    		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminVouchers' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('menuVouchers'); ?></a></li>
    	<?php } ?>
	</ul>
</div>
<?php
if(in_array($_GET['action'], array('pjActionBooking', 'pjActionBookingForm', 'pjActionConfirmation', 'pjActionTerm', 'pjActionArrivalNotice', 'pjActionGeneralInformation', 'pjActionInstall')) || $_GET['controller'] == 'pjAdminExtras')
{
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/opt_submenu.php';
} 
?>
