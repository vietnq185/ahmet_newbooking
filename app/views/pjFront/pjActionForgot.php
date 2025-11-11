<?php
$STORE = @$tpl['store'];
$FORM = @$tpl['form'];
$index = pjObject::escapeString($_GET['index']);
?>
<div class="trHeader">
	<h1><?php __('front_forgot_password', false, false);?></h1>
	<?php
	include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
	?>
</div>
<div class="trContainerInner">
	<form id="trForgotForm_<?php echo $index;?>" action="" method="post" class="trForm">
		<input type="hidden" name="step_forgot" value="1"/>
		
		<p>
			<label class="trTitle"><?php __('front_email'); ?> <span class="trFormStar">*</span>:</label>
			<span class="trContent">
				<input type="text" name="email" class="trField tr-w90p required"/>
			</span>
		</p>
		<p style="display:none;width: 100%;">
			<span id="trForgotMsg_<?php echo $index?>" class="trContent trLoginMsg"></span>
		</p>
		<div class="trRow">
			<p><a href="#" class="trButton trBigButton tr-w90p trGrayButton pjSbsBackToCheckout"><?php __('front_back', false, false);?></a></p>
			<p><a href="#" id="trBtnSendForgot_<?php echo $index;?>" class="trButton trBigButton trYellowButton tr-w90p"><?php __('front_btn_send', false, false);?></a></p>
		</div>
		<div style="clear: both;"></div>
	</form>
</div>