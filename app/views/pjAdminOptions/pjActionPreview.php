<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap5.0.2.min.css" type="text/css" rel="stylesheet" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body bgcolor="#F7F7F7">
        <div style="max-width: 1000px; margin: 10px auto;">
        	<link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss&original=1" type="text/css" rel="stylesheet" />
	        <script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad<?php echo isset($_GET['locale']) ? '&locale=' . $_GET['locale'] : null;?>"></script>
        </div>
    </body>
</html>