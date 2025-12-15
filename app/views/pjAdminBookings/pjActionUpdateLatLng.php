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
	pjUtil::printNotice('Update coordinates', 'Update coordinates for existing bookings');
	?>
	<div class="b10" align="center">
		<p class="b10">Total records: <?php echo $tpl['total'];?></p>
		<p class="b10">Pages: <?php echo $tpl['pages'];?></p>
		<br/>
		<div class="pjUpdateLatLngWrapper">
			<button type="button" class="pj-button btnStartUpdateLatLng">Start</button>
		</div>
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.totalPages = "<?php echo (int)$tpl['pages'];?>"
	</script>
	<?php
}
?>