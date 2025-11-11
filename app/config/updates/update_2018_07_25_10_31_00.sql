
START TRANSACTION;

ALTER TABLE `fleets_discounts` ADD COLUMN `valid` enum('always','period') DEFAULT 'always' AFTER `discount`;
ALTER TABLE `fleets_discounts` ADD COLUMN `date_from` date DEFAULT NULL AFTER `valid`;
ALTER TABLE `fleets_discounts` ADD COLUMN `date_to` date DEFAULT NULL AFTER `time_from`;

INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary_1', 'frontend', 'Steps / Booking Summary', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrival notice: Thank you for booking.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary_1_desc', 'frontend', 'Steps / Booking Summary', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We received your order and will send you a booking confirmation via e-mail as soon as possible.<br/>Please check your data for the booked transfer.', 'script');

INSERT INTO `fields` VALUES (NULL, 'fleet_additional_discount_valid', 'backend', 'Label / Valid', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Valid', 'script');

INSERT INTO `fields` VALUES (NULL, '_fleet_additional_discount_valid_ARRAY_always', 'arrays', '_fleet_additional_discount_valid_ARRAY_always', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Always', 'script');

INSERT INTO `fields` VALUES (NULL, '_fleet_additional_discount_valid_ARRAY_period', 'arrays', '_fleet_additional_discount_valid_ARRAY_period', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Period', 'script');

INSERT INTO `fields` VALUES (NULL, 'fleet_additional_discount_date_from', 'backend', 'Label / Date from', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date from', 'script');

INSERT INTO `fields` VALUES (NULL, 'fleet_additional_discount_date_to', 'backend', 'Label / Date to', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date to', 'script');



COMMIT;