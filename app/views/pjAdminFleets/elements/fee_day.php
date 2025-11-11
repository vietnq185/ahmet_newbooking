<div class="feeDayContainer" id="feeDayContainer_<?php echo $day;?>">
	<div class="p">
    	<label class="title">&nbsp;</label>
    	<div class="overlow float_left">
    		<table id="pjTbPriceTable_<?php echo $day;?>" class="pj-table pjTbPriceTableDay" cellpadding="0" cellspacing="0" style="width: 100%">
    			<thead>
    				<tr>
    					<th style="width: 160px;"><?php __('lblFromInKm'); ?></th>
    					<th style="width: 175px;"><?php __('lblToInKm'); ?></th>
    					<th style="width: 170px;"><?php __('lblPricePerKm'); ?></th>
    					<th style="width: 24px;">&nbsp;</th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php
    				if (isset($tpl['price_day_arr'][$day])) { 
        				foreach($tpl['price_day_arr'][$day] as $k => $v)
        				{
        					?>
        					<tr class="pjTbPriceRow" data-day="<?php echo $day;?>" data-index="<?php echo $day;?>~:~<?php echo $v['id'];?>">
        						<td>
        							<span class="block overflow">
        								<input type="text" name="start_day[<?php echo $day;?>][<?php echo $v['id'];?>]" id="start_day_<?php echo $day;?>_<?php echo $v['id'];?>" value="<?php echo pjSanitize::clean($v['start'])?>" maxlength="10" data-rule-smaller_than="#end_day_<?php echo $day;?>_<?php echo $v['id'];?>" data-msg-smaller_than="<?php __('lblToGreaterThanFrom');?>" class="pj-form-field field-int w110 digits required" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
        							</span>
        						</td>
        						<td>
        							<span class="inline-block">
        								<input type="text" name="end_day[<?php echo $day;?>][<?php echo $v['id'];?>]" id="end_day_<?php echo $day;?>_<?php echo $v['id'];?>" value="<?php echo pjSanitize::clean($v['end'])?>" maxlength="10" data-rule-not_smaller_than="#start_day_<?php echo $day;?>_<?php echo $v['id'];?>" data-msg-not_smaller_than="<?php __('lblToGreaterThanFrom');?>"  class="pj-form-field field-int w110 digits required" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
        							</span>
        						</td>
        						<td>
        							<span class="pj-form-field-custom pj-form-field-custom-before">
        								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
        								<input type="text" name="price_day[<?php echo $day;?>][<?php echo $v['id'];?>]" value="<?php echo pjSanitize::clean($v['price'])?>" class="pj-form-field  required number w50" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
        							</span>
        						</td>
        						<td>
        							<a href="#" class="lnkRemovePriceDay" data-day="<?php echo $day;?>" data-index="<?php echo $v['id'];?>"></a>
        						</td>
        					</tr>
        					<?php
        				} 
    				}
    				?>
    			</tbody>
    		</table>
    	</div>
    </div>
    <p>
    	<label class="title">&nbsp;</label>
    	<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button btnAddPriceDay" data-day="<?php echo $day;?>" />
    </p>
</div>