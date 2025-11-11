START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `extra_price` decimal(9,2) DEFAULT NULL AFTER `price`;
ALTER TABLE `bookings` ADD COLUMN `price_by_distance` enum('T','F') DEFAULT 'F';
ALTER TABLE `bookings` MODIFY `payment_method` enum('paypal','authorize','saferpay','creditcard','creditcard_later','cash','bank') DEFAULT NULL;
ALTER TABLE `bookings_payments` MODIFY `payment_method` enum('paypal','authorize','saferpay','creditcard','creditcard_later','cash','bank') DEFAULT NULL;
ALTER TABLE `bookings_extras` ADD COLUMN `price` decimal(9,2) DEFAULT NULL;
ALTER TABLE `extras` ADD COLUMN `price` decimal(9,2) DEFAULT NULL AFTER `id`;

UPDATE `options` SET `tab_id`=7, `order`=1 WHERE `key`='o_shared_trip_info';

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_no_credit_card_fees_info', 7, '', NULL, 'text', 2, 1, NULL),
(1, 'o_free_waiting_time_info', 7, '', NULL, 'text', 3, 1, NULL),
(1, 'o_meet_greet_service_info', 7, '', NULL, 'text', 4, 1, NULL),
(1, 'o_no_additional_costs_info', 7, '', NULL, 'text', 5, 0, NULL),
(1, 'o_meet_greet_info', 7, '', NULL, 'text', 6, 0, NULL),
(1, 'o_no_payment_in_advance_info', 7, '', NULL, 'text', 7, 0, NULL),
(1, 'o_customer_service_info', 7, '', NULL, 'text', 8, 0, NULL),
(1, 'o_allow_saferpay', 2, 'Yes|No::No', NULL, 'enum', 22, 1, NULL),
(1, 'o_saferpay_username', 2, NULL, NULL, 'string', 23, 1, NULL),
(1, 'o_saferpay_password', 2, NULL, NULL, 'string', 24, 1, NULL),
(1, 'o_saferpay_customer_id', 2, NULL, NULL, 'string', 25, 1, NULL),
(1, 'o_saferpay_terminal_id', 2, NULL, NULL, 'string', 26, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_saferpay', 'arrays', 'payment_methods_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_saferpay', 'arrays', 'payment_methods_desc_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_no_credit_card_fees_info', 'backend', 'Options / No credit card fees', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No credit card fees', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_free_waiting_time_info', 'backend', 'Options / Free waiting time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free waiting time', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_meet_greet_service_info', 'backend', 'Options / Meet & Greet – Service', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Meet & Greet – Service', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_no_additional_costs_info', 'backend', 'Options / No additional costs', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No additional costs for delays', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_meet_greet_info', 'backend', 'Options / Meet & Greet', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Meet & Greet', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_no_payment_in_advance_info', 'backend', 'Options / No payment in advance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No payment in advance and free cancellation', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_customer_service_info', 'backend', 'Options / 24/7 Customer service', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '24/7 Customer service', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_saferpay', 'backend', 'Options / Allow payments with PaySafe', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payments with PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_username', 'backend', 'Options / Username', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Username', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_password', 'backend', 'Options / Password', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_customer_id', 'backend', 'Options / Customer ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_saferpay_terminal_id', 'backend', 'Options / Terminal ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terminal ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabGeneralInformation', 'backend', 'Tab / General Information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General Information', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblVehicleModel', 'backend', 'Label / Model', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblExtraPrice', 'backend', 'Label / Price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFree', 'backend', 'Label / Free', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free', 'script');

INSERT INTO `fields` VALUES (NULL, 'pj_number_validation', 'backend', 'Label / Please enter a valid number.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter a valid number.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoGeneralInformationTitle', 'backend', 'Info / General information title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General information', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoGeneralInformationDesc', 'backend', 'Info / General information body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can define the general information that will be used on the Front-End.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AO07', 'arrays', 'error_titles_ARRAY_AO07', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Changes saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AO07', 'arrays', 'error_bodies_ARRAY_AO07', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes to the General Information have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_label_free', 'frontend', 'Label / Free', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceExtra', 'backend', 'Label / Extra price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra price', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extra_price', 'frontend', 'Label / Extra price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra price', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_your_booking_completed', 'frontend', 'Label / Your reservation has been completed.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your reservation has been completed.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_6', 'arrays', 'front_messages_ARRAY_6', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You will be taken to PaySafe Payment Gateway to process payment. Please wait...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_7', 'arrays', 'front_messages_ARRAY_7', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There was a problem while redirecting to payment page.', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_saferpay', 'arrays', 'payment_methods_short_ARRAY_saferpay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_0', 'arrays', 'short_days_ARRAY_0', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Su', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_1', 'arrays', 'short_days_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mo', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_2', 'arrays', 'short_days_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tu', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_3', 'arrays', 'short_days_ARRAY_3', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_4', 'arrays', 'short_days_ARRAY_4', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Th', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_5', 'arrays', 'short_days_ARRAY_5', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fr', 'script');

INSERT INTO `fields` VALUES (NULL, 'short_days_ARRAY_6', 'arrays', 'short_days_ARRAY_6', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sa', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_add_return_transfer', 'frontend', 'Label / Add return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_one_way', 'frontend', 'Label / One Way', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One Way', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_with_return', 'frontend', 'Label / With Return', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'With Return', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_transfer_date', 'frontend', 'Label / Return transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return transfer date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_transfer_date', 'frontend', 'Label / Transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_button_see_prices', 'frontend', 'Button / See Prices', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'See Prices', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free_cancellation_title', 'frontend', 'Info / Free cancellation title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free cancellation', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free_cancellation_desc', 'frontend', 'Info / Free cancellation desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'up to 24 hours before your transfer: Book today, lock the price. You can cancel for free within the <strong>%s</strong>', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_search_fleets_error_title', 'frontend', 'Info / Price canculation not possible title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price canculation not possible', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_search_fleets_error_desc', 'frontend', 'Info / Price canculation not possible desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry the price canculation is not possible. Please follow the Price Inquiry for more individual offers.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_fleets_empty_title', 'frontend', 'Info / No vehicles found title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No vehicles found', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_fleets_empty_desc', 'frontend', 'Info / No vehicles found desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There are no vehicles found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_max_suitcases', 'frontend', 'Label / Max suitcases per vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '{NUMBER} Medium Suitcases', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free_wt', 'frontend', 'Label / Free waiting time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free waiting time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_total_oneway_price', 'frontend', 'Label / Total one way price for all passengers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total <strong>one way price</strong> for all passengers', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_total_roundtrip_price', 'frontend', 'Label / Free waiting time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total <strong>two way price</strong> for all passengers', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_button_more_info', 'frontend', 'Button / More information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'More information', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_label_vehicle', 'frontend', 'Label / Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_info', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Excellent cars, exclusive service and the best price!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_1_info_title', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No additional costs for delays!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_1_info_desc', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Do not worry, if your flight is deplayed! Our driver is tracking your flight and will be waiting for you until you arrive. Since a delay can happend any time, we do not charge additional costs!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_2_info_title', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Meet & Greet', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_2_info_desc', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Our drivers will pick you up from the airport with a name plate. For a personal welcome!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_3_info_title', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No payment in advance and free cancellation!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_3_info_desc', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Book now without any obligations. No payment in advance is needed - you only have to pay on the day of the transfer. In case you decide to change your plans, you can cancel for free until 24 hours before the transfer.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_4_info_title', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '24/7 Customer Service', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle_services_4_info_desc', 'frontend', 'Label / Vehicle service info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Do not worry, if your flight is deplayed! Our driver is tracking your flight and will be waiting for you until you arrive. Since a delay can happend any time, we do not charge additional costs!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_add_return_transfer_note', 'frontend', 'Label / Add return transfer note', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Ads a return transfer to your journey from %s to %s.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_add_return_transfer_note_with_discount', 'frontend', 'Label / Add return transfer note', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Ads a return transfer to your journey from %s to %s you will get %s discount on your total price.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_button_add_return_transfer', 'frontend', 'Button / Add return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extras_info', 'frontend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select the total number of pieces of baggage and extras for your transfers. If you arrive with more luggage than specified in the booking, we cannot guarantee to transport them.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_booking_details_departure', 'frontend', 'Label / Booking details departure', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Details Departure', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_departure_desc', 'frontend', 'Label / Booking details departure desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travel stress free! We monitor your flight number and in case of delay, we will always wait for you!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_how_many_persons', 'frontend', 'Label / How many persons?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'How many persons?', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_arrival_time', 'frontend', 'Label / Arrival time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrival time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_optional', 'frontend', 'Label / Optional', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Optional', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_dropoff_address_desc', 'frontend', 'Label / Please enter drop off address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter drop off address', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_hotel_pension', 'frontend', 'Label / Name of Hotel or Pension', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name of Hotel or Pension', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_accommodation_name', 'frontend', 'Label / Accommodation name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Accommodation name', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_accommodation_name_desc', 'frontend', 'Label / Accommodation name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter your accommodation name (optional)', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_select_pickup_time', 'frontend', 'Label / Select pick-up time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select your pick-up time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_pickup_address_desc', 'frontend', 'Label / Pick-up address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter pick-up address', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_select_flight_departure_time', 'frontend', 'Label / Departure time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select your flight departure time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_booking_details_return', 'frontend', 'Label / Booking Details Return', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Details Return', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_remove_return_transfer', 'frontend', 'Label / Remove return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_verify_return_transfer_date', 'frontend', 'Label / Please verify your return transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please verify your return transfer date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_total_price_all_inclusive', 'frontend', 'Label / Total price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total price (all inclusive)', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_route_details', 'frontend', 'Label / Route details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Route details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_estimated_time', 'frontend', 'Label / Estimated time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Estimated time<br/>{NUMBER} mins', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_estimated_distance', 'frontend', 'Label / Estimated distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Estimated distance<br/>{NUMBER}km', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_button_go_back', 'frontend', 'Button / Go back', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Go back', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_passgener_details', 'frontend', 'Info / Passenger details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passenger details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_passgener_details_desc', 'frontend', 'Info / Passenger details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please ensure all of the required fields are completed at the time of booking. This information is imperative to ensure a smooth journey. All fields are required. It is very important that you provide your mobile phone number, since you will receive a notification 24 hours before your transfer. Your phone number is essential, as our driver will be able to contact you in case of a delayed flight.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_fname_placeholder', 'frontend', 'Label / First name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'First name of leader person', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_fname_desc', 'frontend', 'Label / First name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter your name', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_lname_placeholder', 'frontend', 'Label / Surname', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Surname of leader person', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_lname_desc', 'frontend', 'Label / Surname', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter your surname', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_front_country_desc', 'frontend', 'Label / Email address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email address', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_mobile_phone', 'frontend', 'Label / Mobile phone number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mobile phone number', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_mobile_phone_placeholder', 'frontend', 'Label / Mobile phone number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mobile phone number', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_mobile_phone_desc', 'frontend', 'Label / Mobile phone number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter your mobile phone number', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_country_desc', 'frontend', 'Label / Choose your contry', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please choose your contry', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cc_owner_desc', 'frontend', 'Label / Credit card owner', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter your name same as on the credit card', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cc_num_placeholder', 'frontend', 'Label / Card number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Card number', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cc_expire_date', 'frontend', 'Label / Card expiredate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Valid until Month & Year', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_now_to_pay', 'frontend', 'Label / Now to pay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Now to pay', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_rest_to_pay', 'frontend', 'Label / Rest to pay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rest to pay', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_full_price_charged_desc', 'frontend', 'Label / Full price charged', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Full price 100% will be charged after your booking is confirmed.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_8', 'arrays', 'front_messages_ARRAY_8', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to save booking. Please try again!', 'script');



SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/>{PriceFirstTransfer}<br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PriceReturnTransfer}<br/>{ReturnNotes}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{StationFee}<br/>{ExtraPrice}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";


SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "front_notes");
UPDATE `multi_lang` SET `content` = 'Further information or requests' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "front_step_booking_summary_desc");
UPDATE `multi_lang` SET `content` = 'Thank you for your booking. Please check your booking details below for any mistakes. You will receive a booking confirmation via Email. Please also check your spam folder. If you cannot find our confirmation email, please give us a call on our 24/7 support hotline.<br/>If you made a last-minute booking, you will receive a confirmation Email as well as a notification on your mobile phone' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "front_step_booking_summary_1_desc");
UPDATE `multi_lang` SET `content` = 'We received your order and will send you a booking confirmation via e-mail as soon as possible. Please check your data for the booked transfer.' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";


COMMIT;