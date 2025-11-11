<!DOCTYPE html>
<html>
	<head>
		<title>Payment</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<head>
	<body bgcolor="#F7F7F7">
		<div style="max-width: 1200px; margin: 0 auto;">		
			<link href="core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" />
<link href="index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="index.php?controller=pjFront&action=pjActionLoad&skip_first_step=<?php echo @$_GET['skip_first_step']; ?>&search_pickup_id=<?php echo @$_GET['search_pickup_id']; ?>&search_location_id=<?php echo @$_GET['search_location_id']; ?>&search_dropoff_id=<?php echo @$_GET['search_dropoff_id']; ?>&search_passengers_from_to=<?php echo @$_GET['search_passengers_from_to']; ?>&search_date=<?php echo @$_GET['search_date']; ?>&search_is_return=<?php echo @$_GET['search_is_return']; ?>&search_return_date=<?php echo @$_GET['search_return_date']; ?><?php echo isset($_GET['loadSummary']) ? ('&loadSummary='.$_GET['loadSummary']) : '';?><?php echo isset($_GET['booking_id']) ? ('&booking_id='.$_GET['booking_id']) : '';?><?php echo isset($_GET['loadPayment']) ? ('&loadPayment='.$_GET['loadPayment']) : '';?><?php echo isset($_GET['booking_uuid']) ? ('&booking_uuid='.$_GET['booking_uuid']) : '';?>"></script>
		</div>
	</body>
</html>