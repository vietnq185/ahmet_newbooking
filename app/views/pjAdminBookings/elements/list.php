<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 10px;">
	<thead>
		<tr>
			<th style="width: 70px;"><?php __('lblBookingID');?></th>
			<th style="width: 120px;"><?php __('lblClient');?></th>
			<th style="width: 70px;"><?php __('lblType');?></th>
			<th style="width: 100px;"><?php __('lblDateAndTime');?></th>
			<th style="width: 150px;"><?php __('lblFromTo');?></th>
			<th style="width: 100px;"><?php __('lblVehicle');?></th>
			<th style="width: 100px;"><?php __('lblPassengers');?></th>
			<th style="width: 120px;"><?php __('lblPayment');?></th>
			<th colspan="2"><?php __('lblAdditionalInfo');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(count($tpl['transfer_arr']) > 0)
		{
			$name_titles = __('personal_titles', true, false);
			$statuses = __('booking_statuses', true, false);
            $payment_methods = __('payment_methods', true, false);
			
			$row = 1;
			foreach($tpl['transfer_arr'] as $v)
			{
				$client_name_arr = array();
				$additional_arr = array();
				if(!empty($v['c_title']))
				{
					$client_name_arr[] = $name_titles[$v['c_title']];
				}
				if(!empty($v['c_fname']))
				{
					$client_name_arr[] = pjSanitize::clean($v['c_fname']);
				}
				if(!empty($v['c_lname']))
				{
					$client_name_arr[] = pjSanitize::clean($v['c_lname']);
				}

                $client_name = implode(' ', $client_name_arr);

                $isOneWay = $isFirstDrive = $isSecondDrive = false;
                $deposit = !empty($v['deposit'])? pjUtil::formatCurrencySign($v['deposit'], $tpl['option_arr']['o_currency']): null;
                $total = (in_array($v['payment_method'], array('bank', 'creditcard'))) ? 0 : $v['total'];
                if(empty($v['return_date']) && empty($v['return_id']))
                {
                    $isOneWay = true;
                    // One Way
                    $type = __('front_transfer_type_one_way', true);
                    $total = pjUtil::formatCurrencySign($total, $tpl['option_arr']['o_currency']);
                }
                else
                {
                    // Return Trip
                    $type = __('lblReturnTrip', true) . '<br/>';
                    $total = pjUtil::formatCurrencySign(number_format($total / 2, 2), $tpl['option_arr']['o_currency']);

                    if(!empty($v['return_date']))
                    {
                        $isFirstDrive = true;
                        // 1st drive
                        $type .= __('lblFirstDrive', true);
                    }
                    elseif(!empty($v['return_id']))
                    {
                        $isSecondDrive = true;
                        // 2nd drive
                        $type .= __('lblSecondDrive', true);
                        $deposit = null;
                    }
                }

                $field_arr = array();
                $field_arr['c_phone'] = __('front_mobile_number', true, false);
                $field_arr['c_email'] = __('front_email', true, false);
                $field_arr['c_country'] = __('front_country', true, false);

                if($isOneWay || $isFirstDrive)
                {
                    if($v['is_airport'])
                    {
                        $field_arr['c_flight_number'] = __('front_flight_number', true, false);
                        $field_arr['c_airline_company'] = __('front_airline_company', true, false);
                        $field_arr['c_destination_address'] = __('front_destination_address', true, false);
                        $field_arr['c_hotel'] = __('front_hotel', true, false);
                    }
                    else
                    {
                        $field_arr['c_address'] = __('front_pickup_address', true, false);
                        $field_arr['c_destination_address'] = __('front_dropoff_address', true, false);
                        $field_arr['c_hotel'] = __('front_hotel', true, false);
                        $field_arr['c_departure_flight_time'] = __('front_flight_departure_time', true, false);
                    }
                }
                elseif($isSecondDrive)
                {
                    if($v['is_return_airport'])
                    {
                        $field_arr['c_address'] = __('front_address', true, false);
                        $field_arr['c_departure_flight_time'] = __('front_flight_departure_time', true, false);
                    }
                    else
                    {
                    	$field_arr['c_address'] = __('front_pickup_address', true, false);
                        $field_arr['c_destination_address'] = __('front_dropoff_address', true, false);
                        $field_arr['c_flight_number'] = __('front_flight_number', true, false);
                        $field_arr['c_airline_company'] = __('front_airline_company', true, false);
                    }
                }

                $field_arr['c_notes'] = __('front_notes', true, false);
                $field_arr['extras'] = __('front_extras', true, false);

				foreach($field_arr as $field => $title)
				{
                    if($field == 'extras')
                    {
                        if(!empty($v['extra_arr']))
                        {
                            $extras = array();
                            foreach($v['extra_arr'] as $index => $extra)
                            {
                                $extras[] = $extra['quantity'] . ' x ' . $extra['name'] . (!empty($extra['info'])? " ({$extra['info']})": null);
                            }
                            $additional_arr[] = '<td>'.$title.'</td><td>'.implode('<br/>', $extras).'</td>';
                        }
                    }
					elseif(!empty($v[$field]))
					{
                        if($field == 'c_notes')
                        {
                            $value = nl2br($v[$field]);
                        }
                        if($field == 'c_phone')
                        {
                            $value = $v['c_dialing_code'] . $v[$field];
                        }
                        else
                        {
                            $value = $v[$field];
                        }
						$additional_arr[] = '<td>'.$title.'</td><td>'.$value.'</td>';
					}
				}

				$row_span = count($additional_arr) > 0 ? count($additional_arr) : 1;

                foreach($additional_arr as $k => $addition)
                {
                    if($k == 0)
                    {
                        ?>
                        <tr class="<?php echo $row%2==0? 'even' : 'odd';?>">
                            <td rowspan="<?php echo $row_span;?>"><?php echo !empty($v['uuid']) ? pjSanitize::clean($v['uuid']) : pjSanitize::clean($v['uuid2']);?></td>
                            <td rowspan="<?php echo $row_span;?>"><b><?php echo $client_name; ?></b></td>
                            <td rowspan="<?php echo $row_span;?>"><?= $type ?></td>
                            <td rowspan="<?php echo $row_span;?>"><b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></b></td>
                            <td rowspan="<?php echo $row_span;?>"><b><?php __('lblFrom');?>: <?php echo empty($v['return_id']) ? $v['location'] : $v['location2']; ;?></b><br/><b><?php __('lblTo');?>: <?php echo empty($v['return_id']) ? $v['dropoff_location'] : $v['dropoff_location2'];?></b></td>
                            <td rowspan="<?php echo $row_span;?>"><?php echo pjSanitize::clean($v['vehicle']);?></td>
                            <td rowspan="<?php echo $row_span;?>"><?php echo $v['passengers'] . ' ' . ($v['passengers'] != 1 ? __('lblPassengers', true, false) : __('lblPassenger', true, false)) ;?></td>
                            <td rowspan="<?php echo $row_span;?>">
                                <b><?php echo __('lblTotal', true, false) . ': ' . $total;?></b>
                                <?php if($deposit): ?>
                                    <br/>
                                    <b><?php __('lblDeposit'); ?>: <?= $deposit ?></b>
                                <?php endif; ?>
                                <?php if(!empty($v['payment_method'])): ?>
                                    <br/>
                                    <?php __('lblVia'); ?> <?php echo $payment_methods[$v['payment_method']];?>
                                <?php endif; ?>
                                <br/>
                                <b><?php echo __('lblStatus', true, false) . ': ' . $statuses[$v['status']];?></b>
                            </td>
                            <?php echo $addition; ?>
                        </tr>
                        <?php
                    }else{
                        ?>
                        <tr class="<?php echo $row%2==0? 'even' : 'odd';?>">
                            <?php echo $addition;?>
                        </tr>
                        <?php
                    }
                }
				$row++;
			}
		} else {
			?>
			<tr>
				<td colspan="10"><?php __('gridEmptyResult');?></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>