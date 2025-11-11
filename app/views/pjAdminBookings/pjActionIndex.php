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
	$statuses = __('booking_statuses', true, false);
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	pjUtil::printNotice(__('infoReservationListTitle', true, false), __('infoReservationListDesc', true, false)); 
	?>
	
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddEnquiry'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w300" placeholder="<?php __('btnSearch', false, true); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status<?= isset($_GET['status']) && $_GET['status'] == 'confirmed'? ' pj-button-active': null; ?>" data-column="status" data-value="confirmed"><?php echo $statuses['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $statuses['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="in_progress"><?php echo $statuses['in_progress']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $statuses['cancelled']; ?></a>
			<a href="#" class="pj-button btn-filter-notes-for-support"><?php __('btnHasNotes'); ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="overflow float_left w600">
				<p>
					<label class="title120"><?php __('lblPickupLocation'); ?>:</label>
					<span class="inline-block">
						<select name="location_id" id="search_pickup_id" class="pj-form-field w300">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['pickup_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id'];?>"><?php echo $v['pickup_location'];?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title120"><?php __('lblDropoffLocation'); ?>:</label>
					<span class="inline-block pjFilterDropoffLocations">
						<select name="dropoff_place_id" id="search_dropoff_place_id" class="pj-form-field w300">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach($tpl['dropoff_place_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['place_name']);?></option><?php
							} 
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title120"><?php __('lblTransferDate'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date" id="date" class="pj-form-field pointer w90 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</p>
				<p>
					<label class="title120">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="overflow float_left w600">
				
				<p>
					<label class="title"><?php __('email'); ?></label>
					<span class="inline-block">
						<input type="text" id="email" name="email" class="pj-form-field w300" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblName'); ?></label>
					<span class="inline-block">
						<input type="text" id="name" name="name" class="pj-form-field w300" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblPhone'); ?></label>
					<span class="inline-block">
						<input type="text" id="phone" name="phone" class="pj-form-field w300" />
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>
	
	<div id="grid" class="pj-grid-bookings"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['fleet_id']) && (int) $_GET['fleet_id'] > 0)
	{
		?>pjGrid.queryString += "&fleet_id=<?php echo (int) $_GET['fleet_id']; ?>";<?php
	}
	if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
	{
		?>pjGrid.queryString += "&client_id=<?php echo (int) $_GET['client_id']; ?>";<?php
	}
	if (isset($_GET['date']))
	{
		?>pjGrid.queryString += "&date=<?php echo $_GET['date']; ?>";<?php
	}
	if (isset($_GET['status']))
	{
		?>pjGrid.queryString += "&status=<?php echo $_GET['status']; ?>";<?php
	}
	if (isset($_GET['driver_id']))
	{
		?>pjGrid.queryString += "&driver_id=<?php echo $_GET['driver_id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.client = "<?php __('lblClient', false, true); ?>";
	myLabel.transfer_date_time = "<?php __('lblTransferDateTime', false, false); ?>";
	myLabel.transfer_destinations = "<?php __('lblTransferDestinations', false, true); ?>";
	myLabel.email = "<?php __('email', false, true); ?>";
	myLabel.passengers = "<?php __('lblPassengers'); ?>";
	myLabel.extras = "<?php __('lblExtras'); ?>";
	myLabel.fleet = "<?php __('lblFleet'); ?>";
	myLabel.payment_method = "<?php __('lblPaymentMethod', false, true); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.print = "<?php __('lblPrint', false, true); ?>";
	myLabel.print_reservation_details = "<?php __('lblPrintReservationDetails', false, true); ?>";
	myLabel.print_reservation_details_single = "<?php __('lblPrintReservationDetailsSingle', false, true); ?>";
	myLabel.remind_client_for_return_via_email = "<?php __('lblGridRemindClientForReturnViaEmail', false, true); ?>";
	myLabel.remind_client_via_email = "<?php __('lblGridRemindClientViaEmail', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.pending = "<?php echo $statuses['pending']; ?>";
	myLabel.in_progress = "<?php echo $statuses['in_progress']; ?>";
	myLabel.passed_on = "<?php echo $statuses['passed_on']; ?>";
	myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	</script>
	<?php
}
?>