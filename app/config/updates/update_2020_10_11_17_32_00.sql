START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'front_free_cancellation_msg', 'frontend', 'Label / Free cancellation up to 12 hours before your transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free cancellation up to 12 hours before your transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblVehicleBadget', 'backend', 'Label / Badget', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Badget', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_more_info', 'frontend', 'Label / More info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'More info', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_max_passengers');
UPDATE `multi_lang` SET `content`='Max {NUMBER} passengers per vehicle' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';

SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_estimated_distance');
UPDATE `multi_lang` SET `content`='Estimated distance {NUMBER}km' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';

SET @id := (SELECT `id` FROM `fields` WHERE `key`='front_estimated_time');
UPDATE `multi_lang` SET `content`='Estimated time {NUMBER} mins' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';

INSERT INTO `fields` VALUES (NULL, 'front_no_credit_card_fee', 'frontend', 'Label / No credit card fees', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No credit card fees', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free_cancellation_wt', 'frontend', 'Label / Free cancellation & Waiting Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free cancellation & Waiting Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_meet_freet_service', 'frontend', 'Label / Meet & Greet-Service', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Meet & Greet-Service', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_badget_free_cancellation', 'frontend', 'Label / Free cancellation', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free cancellation', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_your_transfer_from', 'frontend', 'Label / Your transfer is from', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your transfer is from', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_stress_free', 'frontend', 'Label / Travel stress free! We monitor your flight number and in case of delay, we will always wait for you!', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Travel stress free! We monitor your flight number and in case of delay, we will always wait for you!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_select_flight_landing_time', 'frontend', 'Label / Please select your flight landing time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select your flight landing time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_going_to', 'frontend', 'Label / You are goning to', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You are goning to', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_discount_code_desc', 'frontend', 'Label / Discount code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'If you have a discount code, enter the code into the provided field and press enter.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_payment_method_desc', 'frontend', 'Label / Payment method', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can choose between the following %d payment methods', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_authorize', 'arrays', 'payment_methods_desc_ARRAY_authorize', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_bank', 'arrays', 'payment_methods_desc_ARRAY_bank', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_cash', 'arrays', 'payment_methods_desc_ARRAY_cash', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Conveniently pay our driver in cash on the day of your transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_creditcard', 'arrays', 'payment_methods_desc_ARRAY_creditcard', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'After you received your booking confirmation, the full price for your transfer will be charged from your Credit Card. In case of cancellation 12 hours before your transfer, the full amount will be transferred back to you account.', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_creditcard_later', 'arrays', 'payment_methods_desc_ARRAY_creditcard_later', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Conveniently pay our driver by Credit Card on the day of your transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_desc_ARRAY_paypal', 'arrays', 'payment_methods_desc_ARRAY_paypal', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '', 'script');



COMMIT;