<!doctype html>
<html>
	<head>
		<title><?php __('plugin_invoice_menu_invoices'); ?></title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.$css['path'].$css['file'].'" />';
		}
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].htmlspecialchars($js['file']).'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
		<?php echo $tpl['template']; ?>
		</div>
	</body>
</html>