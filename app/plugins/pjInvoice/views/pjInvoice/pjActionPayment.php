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
		?>
	</head>
	<body>
		<div id="container">
		<?php
		switch ($_POST['payment_method'])
		{
			case 'paypal':
				if (pjObject::getPlugin('pjPaypal') !== NULL)
				{
					$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					__('plugin_invoice_paypal_redirect');
					?>
					<input type="button" value="<?php __('plugin_invoice_paypal_proceed'); ?>" id="pinBtnProceed" />
					<script type="text/javascript">
					(function () {
						function proceed() {
							var frm = document.getElementById("pinPaypal");
							if (frm) {
								frm.submit();
							}
						}

						window.setTimeout(function () {
							proceed.call(null);
						}, 3000);
						
						var btn = document.getElementById("pinBtnProceed");
						if (btn) {
							btn.onclick = function () {
								proceed.call(null);
							};
						}
					})();
					</script>
					<?php
				}
				break;
			case 'authorize':
				if (pjObject::getPlugin('pjAuthorize') !== NULL)
				{
					$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					__('plugin_invoice_authorize_redirect');
					?>
					<input type="button" value="<?php __('plugin_invoice_authorize_proceed'); ?>" id="pinBtnProceed" />
					<script type="text/javascript">
					(function () {
						function proceed() {
							var frm = document.getElementById("pinAuthorize");
							if (frm) {
								frm.submit();
							}
						}

						window.setTimeout(function () {
							proceed.call(null);
						}, 3000);
						
						var btn = document.getElementById("pinBtnProceed");
						if (btn) {
							btn.onclick = function () {
								proceed.call(null);
							};
						}
					})();
					</script>
					<?php
				}
				break;
            case 'bambora':
                $hashValue = 'merchant_id=' . $tpl['config_arr']['p_bambora_merchant_id'];
                $refHashValue = $hashValue . '&trnAmount=' . number_format($tpl['params']['amount'], 2, '.', '') . '&trnOrderNumber=' . $tpl['arr']['order_id'];
                if($tpl['config_arr']['p_bambora_hash_algorithm'] == 'SHA-1')
                {
                    $hashValue = sha1($hashValue . $tpl['config_arr']['p_bambora_hash']);
                    $refHashValue = sha1($refHashValue . $tpl['config_arr']['p_bambora_hash']);
                }
                elseif($tpl['config_arr']['p_bambora_hash_algorithm'] == 'MD5')
                {
                    $hashValue = md5($hashValue . $tpl['config_arr']['p_bambora_hash']);
                    $refHashValue = md5($refHashValue . $tpl['config_arr']['p_bambora_hash']);
                }
                ?>
                <form action="https://web.na.bambora.com/scripts/payment/payment.asp" method="get" style="display: inline" name="<?php echo $tpl['params']['name']; ?>" id="<?php echo $tpl['params']['id']; ?>" target="<?php echo $tpl['params']['target']; ?>">
                    <input type="hidden" name="merchant_id" value="<?php echo $tpl['config_arr']['p_bambora_merchant_id']; ?>" />
                    <input type="hidden" name="hashValue" value="<?php echo $hashValue; ?>" />

                    <input type="hidden" name="trnAmount" value="<?php echo number_format($tpl['params']['amount'], 2, '.', ''); ?>" />
                    <input type="hidden" name="trnOrderNumber" value="<?php echo $tpl['arr']['order_id']; ?>" />
                    <input type="hidden" name="trnType" value="P" />
                    <input type="hidden" name="trnCardOwner" value="<?php echo $tpl['arr']['b_name']; ?>" />
                    <input type="hidden" name="trnLanguage" value="eng" />

                    <input type="hidden" name="ordName" value="<?php echo $tpl['arr']['b_name']; ?>" />
                    <input type="hidden" name="ordPhoneNumber" value="<?php echo $tpl['arr']['b_phone']; ?>" />
                    <input type="hidden" name="ordEmailAddress" value="<?php echo $tpl['arr']['b_email']; ?>" />
                    <input type="hidden" name="ordAddress1" value="<?php echo $tpl['arr']['b_billing_address']; ?>" />
                    <input type="hidden" name="ordAddress2" value="<?php echo $tpl['arr']['b_address']; ?>" />
                    <input type="hidden" name="ordCity" value="<?php echo $tpl['arr']['b_city']; ?>" />
                    <input type="hidden" name="ordProvince" value="<?php echo $tpl['arr']['b_state']; ?>" />
                    <input type="hidden" name="ordPostalCode" value="<?php echo $tpl['arr']['b_zip']; ?>" />
                    <input type="hidden" name="ordCountry" value="<?php echo $tpl['arr']['b_country_alpha_2']; ?>" />

                    <input type="hidden" name="shipName" value="<?php echo $tpl['arr']['b_name']; ?>" />
                    <input type="hidden" name="shipPhoneNumber" value="<?php echo $tpl['arr']['b_phone']; ?>" />
                    <input type="hidden" name="shipEmailAddress" value="<?php echo $tpl['arr']['b_email']; ?>" />
                    <input type="hidden" name="shipAddress1" value="<?php echo $tpl['arr']['b_billing_address']; ?>" />
                    <input type="hidden" name="shipAddress2" value="<?php echo $tpl['arr']['b_address']; ?>" />
                    <input type="hidden" name="shipCity" value="<?php echo $tpl['arr']['b_city']; ?>" />
                    <input type="hidden" name="shipProvince" value="<?php echo $tpl['arr']['b_state']; ?>" />
                    <input type="hidden" name="shipPostalCode" value="<?php echo $tpl['arr']['b_zip']; ?>" />
                    <input type="hidden" name="shipCountry" value="<?php echo $tpl['arr']['b_country_alpha_2']; ?>" />

                    <input type="hidden" name="approvedPage" value="<?php echo $tpl['params']['notify_url']; ?>" />
                    <input type="hidden" name="declinedPage" value="<?php echo $tpl['params']['return']; ?>" />

                    <input type="hidden" name="ref1" value="<?php echo $refHashValue; ?>" />
                    <input type="hidden" name="ref2" value="<?php echo $tpl['params']['custom']; ?>" />
                </form>
                <?php
                __('plugin_invoice_bambora_redirect');
                ?>
                <input type="button" value="<?php __('plugin_invoice_bambora_proceed'); ?>" id="pinBtnProceed" />
                <script type="text/javascript">
                (function () {
                    function proceed() {
                        var frm = document.getElementById("pinBambora");
                        if (frm) {
                            frm.submit();
                        }
                    }

                    window.setTimeout(function () {
                        proceed.call(null);
                    }, 3000);

                    var btn = document.getElementById("pinBtnProceed");
                    if (btn) {
                        btn.onclick = function () {
                            proceed.call(null);
                        };
                    }
                })();
                </script>
                <?php
            break;
			case 'bank':
				echo stripslashes(nl2br($tpl['config_arr']['p_bank_account']));
				break;
			case 'creditcard':
			case 'cash':
			default:
				echo '<br/><br/>' . __('plugin_invoice_i_payment_confirm', true);
		}
		?>
		</div>
		
	</body>
</html>