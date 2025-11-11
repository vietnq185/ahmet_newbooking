<div style="margin-bottom: 14px; margin-top: 10px; font-weight: bold; font-size: 16px;">
    <?php __('lblReservationPrint'); ?>
</div>

<?php foreach ($tpl['transfer_arr'] as $transfer): ?>
    <?php
        $client_name = __('personal_titles_ARRAY_' . $transfer['c_title'], true, false) . ' ' .  $transfer['c_fname'] . ' ' . $transfer['c_lname'];
    ?>
    <div style="overflow: hidden; margin-bottom: 16px;">
        <div style="width: 48%;margin-right: 4%;float:left;">
            <table class="table" cellspacing="0" cellpadding="0" style="width: 100%;margin-bottom: 40px;">
                <thead>
                <tr>
                    <th colspan="2"><?php __('front_step_passenger_details');?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $statuses = __('booking_statuses', true, false);
                    $payment_methods = __('payment_methods', true, false);
                ?>
                <tr class="bold">
                    <td style="width:40%;"><?php __('front_name_surname', false, false);?></td>
                    <td style="width:60%;"><?= $client_name ?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_mobile_number', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_dialing_code'] . $transfer['c_phone']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_email', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_email']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('front_country', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_country']);?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="width: 48%;float:right;">
            <table class="table" cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom: 40px;">
                <thead>
                <tr>
                    <th colspan="2"><?php __('lblTransfer');?>: <?php echo (!empty($transfer['return_date']) || !empty($transfer['return_id'])) ? __('lblRoundTrip') : __('lblSingle');?></th>
                </tr>
                </thead>
                <tbody>
                <tr class="bold">
                    <td style="width:40%;"><?php __('lblVehicle', false, false);?></td>
                    <td style="width:60%;"><?php echo pjSanitize::clean($transfer['vehicle']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('lblPassengers', false, false);?></td>
                    <td style="width:60%;"><?php echo $transfer['passengers'];?></td>
                </tr>

                <?php if(isset($transfer['extra_arr']) && !empty($transfer['extra_arr'])): ?>
                    <?php foreach($transfer['extra_arr'] as $index => $extra): ?>
                        <tr>
                            <td style="width:40%;"><?= $index == 0? __('front_extras', true): '&nbsp;'; ?></td>
                            <td style="width:60%;">
                                <?= $extra['quantity'] . ' x ' . $extra['name'] ?>
                                <?php if(!empty($extra['info'])): ?>
                                    <i>(<?= $extra['info'] ?>)</i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if($transfer['discount'] > 0): ?>
                    <tr>
                        <td style="width:40%;"><?php __('front_discount', false, false);?></td>
                        <td style="width:60%;"><?php echo pjUtil::formatCurrencySign($transfer['discount'], $tpl['option_arr']['o_currency']); ?> (<?= $transfer['voucher_code'] ?>)</td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td style="width:40%;"><?php __('lblPayment', false, false);?></td>
                    <td style="width:60%;"><?php echo pjUtil::formatCurrencySign((in_array($transfer['payment_method'], array('bank', 'creditcard'))) ? 0 : ((!empty($transfer['return_date']) || !empty($transfer['return_id'])) ? number_format($transfer['total'] / 2, 2) : $transfer['total']), $tpl['option_arr']['o_currency']);?> / <?php echo $payment_methods[$transfer['payment_method']];?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('lblDeposit', false, false);?></td>
                    <td style="width:60%;"><?php echo pjUtil::formatCurrencySign($transfer['deposit'], $tpl['option_arr']['o_currency']);?></td>
                </tr>
                <tr>
                    <td style="width:40%;"><?php __('lblStatus', false, false);?></td>
                    <td style="width:60%;"><?php echo $statuses[$transfer['status']];?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="overflow: hidden; margin-bottom: 16px;">
        <?php if(empty($transfer['return_id'])): ?>
            <div style="width: 48%;margin-right: 4%;float:left;">
                <table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <thead>
                    <tr>
                        <th colspan="2"><?php __('lblPickup');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="bold">
                        <td style="width:40%;"><?php __('lblDateAndTime', false, false);?></td>
                        <td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($transfer['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($transfer['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
                    </tr>
                    <tr>
                        <td style="width:40%;"><?php __('lblFrom', false, false);?></td>
                        <td style="width:60%;"><?php echo $transfer['location'];?></td>
                    </tr>
                    <tr>
                        <td style="width:40%;"><?php __('lblTo', false, false);?></td>
                        <td style="width:60%;"><?php echo $transfer['dropoff_location'];?><br/><?php echo stripslashes($transfer['dropoff_address']);?></td>
                    </tr>
                    <?php if($transfer['is_airport']): ?>
                        <?php if(!empty($transfer['c_flight_number'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_number', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_flight_number']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_airline_company'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_airline_company', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_airline_company']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_destination_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_destination_address', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_destination_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_hotel'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_hotel', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_hotel']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(!empty($transfer['c_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_pickup_address', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_destination_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_dropoff_address', false, false);?></td>
                                <td style="width:60%;"><?php echo stripslashes($transfer['c_destination_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_hotel'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_hotel', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_hotel']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_departure_flight_time'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_departure_time', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_departure_flight_time']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(!empty($transfer['c_notes'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_notes', false, false);?></td>
                            <td style="width:60%;"><?php echo nl2br(pjSanitize::clean($transfer['c_notes']));?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if(!empty($transfer['return_id'])): ?>
            <div style="width: 48%;float:left;">
                <table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <thead>
                    <tr>
                        <th colspan="2"><?php __('lblReturn');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="bold">
                        <td style="width:40%;"><?php __('lblDateAndTime', false, false);?></td>
                        <td style="width:60%;"><?php echo pjUtil::formatDate(date('Y-m-d', strtotime($transfer['booking_date'])), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date('H:i:s', strtotime($transfer['booking_date'])), 'H:i:s', $tpl['option_arr']['o_time_format']);?></td>
                    </tr>
                    <tr>
                        <td style="width:40%;"><?php __('lblFrom', false, false);?></td>
                        <td style="width:60%;"><?php echo $transfer['location2'];?><br/><?php echo $transfer['address2'];?></td>
                    </tr>
                    <tr>
                        <td style="width:40%;"><?php __('lblTo', false, false);?></td>
                        <td style="width:60%;"><?php echo $transfer['dropoff_location2'];?></td>
                    </tr>

                    <?php if($transfer['is_airport']): ?>
                        <?php if(!empty($transfer['c_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_address', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_departure_flight_time'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_departure_time', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_departure_flight_time']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(!empty($transfer['c_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_pickup_address', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_destination_address'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_dropoff_address', false, false);?></td>
                                <td style="width:60%;"><?php echo stripslashes($transfer['c_destination_address']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_flight_number'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_flight_number', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_flight_number']);?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if(!empty($transfer['c_airline_company'])): ?>
                            <tr>
                                <td style="width:40%;"><?php __('front_airline_company', false, false);?></td>
                                <td style="width:60%;"><?php echo pjSanitize::clean($transfer['c_airline_company']);?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(!empty($transfer['c_notes'])): ?>
                        <tr>
                            <td style="width:40%;"><?php __('front_notes', false, false);?></td>
                            <td style="width:60%;"><?php echo nl2br(pjSanitize::clean($transfer['c_notes']));?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="page-break">
        <div class="client-name-plate">
            <table class="table" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <th><?php echo (!empty($transfer['customized_name_plate'])) ? $transfer['customized_name_plate'] : $client_name; ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="page-break"></div>
<?php endforeach; ?>