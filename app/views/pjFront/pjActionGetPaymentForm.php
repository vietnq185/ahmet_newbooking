<?php
$front_messages = __('front_messages', true, false);
switch ($tpl['arr']['payment_method'])
{
	case 'paypal':
		?><div class="trSystemMessage"><?php echo $front_messages[1]; ?></div><?php
		if (pjObject::getPlugin('pjPaypal') !== NULL)
		{
			$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
		}
		break;
	case 'authorize':
		?><div class="trSystemMessage"><?php echo $front_messages[2]; ?></div><?php
		if (pjObject::getPlugin('pjAuthorize') !== NULL)
		{
			$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
		}
		break;
	case 'bank':
		?>
		<div class="trSystemMessage">
			<?php
			$system_msg = str_replace("[STAG]", "<a href='#' class='trStartOver'>", $front_messages[3]);
			$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
			echo $system_msg; 
			?>
			<br /><br />
			<?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?>
		</div>
		<?php
		break;
	case 'saferpay':
		if($tpl['option_arr']['o_payment_status'] == $tpl['arr']['status'])
		{
			?><p class="text-success text-center"><?php __('front_your_booking_completed');?></p><?php
		}else{ 
			$paysafe = $tpl['paysafe_data'];
			if(isset($paysafe['body']['RedirectUrl']))
			{
				$url = $paysafe['body']['RedirectUrl'];
				?>
				<p class="text-success text-center"><?php echo $front_messages[6]; ?></p>
				<div class="modal fade" id="modalSaferpay_<?php echo $_GET['index'];?>" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="display: none;">
				    <div class="modal-dialog modal-lg">
				        <div class="modal-content">
				        	<iframe name="fields-card-number" id="iframeSaferpay_<?php echo $_GET['index'];?>" id="iframe" scrolling="no" src="<?php echo $url;?>" height="100%" width="100%"></iframe>
				        </div>
				    </div>
				</div>
				<?php
			} else { ?>
				<div class="text-success text-danger">
					<p><?php echo $front_messages[7]; ?></p>
					<?php 
					if (isset($paysafe['body']['ErrorDetail'])) {
						foreach ($paysafe['body']['ErrorDetail'] as $paysafe_err) {
							?>
							<p><?php echo $paysafe_err;?></p>
							<?php 	
						}
					} elseif (isset($paysafe['body']['ErrorMessage'])) {
						?>
						<p><?php echo $paysafe['body']['ErrorMessage'];?></p>
						<?php 
					}
					?>
				</div>
			<?php }
		}
		break;
	case 'creditcard':
	case 'cash':
	default:
		?>
		<div class="trSystemMessage">
			<?php
			$system_msg = str_replace("[STAG]", "<a href='#' class='trStartOver'>", $front_messages[3]);
			$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
			echo $system_msg; 
			?>
		</div>
		<?php
}

?>