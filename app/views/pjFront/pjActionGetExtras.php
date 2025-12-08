<h3><?php __('front_extras'); ?></h3>
<div class="alert alert-warning d-flex align-items-center" role="alert">
	<i class="fad fa-info-circle"></i><span class="alert-desc"><?php __('front_extras_info');?></span>   		
</div>
<ul>
<?php
$STORE = @$tpl['store'];
foreach($tpl['extra_arr'] as $extra) { 
	$max_qty = isset($tpl['el_arr'][$extra['id']])? $tpl['el_arr'][$extra['id']]: $tpl['option_arr']['o_extras_max_qty'];
	if($max_qty < 1)
	{
		continue;
	}
	$image_url = !empty($extra['image_path'])? PJ_INSTALL_URL . $extra['image_path']: PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/no-image.png';
	?>
	<li class="row d-flex align-items-center">
		<div class="<?php echo $STORE['is_return'] == 0 ? 'col-sm-9' : 'col-sm-6';?> col-12">
			<table>
				<tr>
					<td width="50" align="center"><img src="<?php echo $image_url;?>" class="img-responsive" /></td>
					<td>
						<div class="pjSbExtraName">
						<?php if ((float)$extra['price'] > 0) { ?>
							<?php echo pjSanitize::html($extra['name']);?> <span class="badget-amount"><?php echo number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'];?></span>
						<?php } else { ?>
							<?php echo pjSanitize::html($extra['name']);?> <span class="badget-free"><?php __('front_label_free');?></span>
						<?php } ?>
						</div>
						<div class="pjSbExtraDesc"><?php echo pjSanitize::html($extra['info']);?></div>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-sm-3 <?php echo $STORE['is_return'] == 1 ? 'col-6' : 'col-12';?> pjSbSpinWrap">
			<?php if ($STORE['is_return'] == 1) { ?>
				<small><?php __('front_extra_outward_transfer');?></small>
			<?php } ?>
			<div class="input-group pjSbSpins">
				<div class="pjSbSpinLeft">
					<a href="javascript:void(0);" class="pjSbSpin" data-type="minus" data-min="0" data-id="<?php echo $extra['id'];?>">
						<span class="input-group-addon text-blue">
							<span class="fad fa-minus-circle" aria-hidden="true"></span>
						</span>		
					</a>
				</div>			
				<input type="text" name="extras[<?php echo $extra['id'];?>]" id="trExtra_<?php echo $extra['id'];?>" readonly="readonly" value="<?php echo isset($STORE['extras'][$extra['id']]) ? (int)$STORE['extras'][$extra['id']] : 0; ?>" class="form-control text-center pjSbExtraQty pjSbExtraPickup" data-id="<?php echo $extra['id'];?>" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
				<div class="pjSbSpinRight">
					<a href="javascript:void(0);" class="pjSbSpin" data-type="plus" data-max="<?php echo $max_qty;?>" data-id="<?php echo $extra['id'];?>">
						<span class="input-group-addon text-blue">
							<span class="fad fa-plus-circle" aria-hidden="true"></span>
						</span>		
					</a>
				</div>
			</div>
		</div>
		<?php if ($STORE['is_return'] == 1) { ?>
			<div class="col-sm-3 col-6 pjSbSpinWrap">
				<small><?php __('front_extra_return_transfer');?></small>
				<div class="input-group pjSbSpins">
					<div class="pjSbSpinLeft">
						<a href="javascript:void(0);" class="pjSbSpin" data-type="minus" data-min="0" data-id="<?php echo $extra['id'];?>">
							<span class="input-group-addon text-blue">
								<span class="fad fa-minus-circle" aria-hidden="true"></span>
							</span>		
						</a>
					</div>			
					<input type="text" name="extras_return[<?php echo $extra['id'];?>]" id="trExtraReturn_<?php echo $extra['id'];?>" readonly="readonly" value="<?php echo isset($STORE['extras_return'][$extra['id']]) ? (int)$STORE['extras_return'][$extra['id']] : 0; ?>" class="form-control text-center pjSbExtraQty pjSbExtraReturn" data-msg-required="<?php echo pjSanitize::clean(__('front_required_field', true, false));?>"/>
					<div class="pjSbSpinRight">
						<a href="javascript:void(0);" class="pjSbSpin" data-type="plus" data-max="<?php echo $max_qty;?>" data-id="<?php echo $extra['id'];?>">
							<span class="input-group-addon text-blue">
								<span class="fad fa-plus-circle" aria-hidden="true"></span>
							</span>		
						</a>
					</div>
				</div>
			</div>
		<?php } ?>
	</li>
<?php } ?>
</ul>