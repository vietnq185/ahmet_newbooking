<html lang="en">
    <head>
        <title>Success Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript">
          function iframe_breakout()
          {
        	  <?php if (!in_array($_GET['type'], array('fail','abort'))) { ?>
	        	  if (top.location !== location){
	                  //top.location.href = document.location.href;
	        		  top.location.href = "<?php echo $tpl['siteUrl'];?>?loadSummary=1&booking_id=<?php echo $tpl['arr']['id']?>";
	              }
        	  <?php } else { ?>
        	  	top.location.href = "<?php echo $tpl['siteUrl'];?>?loadPayment=1&booking_uuid=<?php echo $tpl['arr']['uuid']?>";
	      	  <?php } ?>
          }
        </script>
    </head>
    <body onload="iframe_breakout()">
    	<?php if (!in_array($_GET['type'], array('fail','abort'))) { ?>
			<div style="max-width: 1200px; margin: 10px auto;">
	        	<link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" />
		        <script type="text/javascript" src="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionLoad&loadSummary&booking_id=<?php echo $tpl['arr']['id'];?>"></script>
	        </div>
        <?php } ?>
    </body>
</html>