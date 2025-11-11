
START TRANSACTION;

ALTER TABLE `clients` ADD COLUMN `dialing_code` varchar(55) DEFAULT NULL AFTER `lname`;
ALTER TABLE `clients` DROP INDEX `email`;

UPDATE `options` SET `value` = '0|1::0' WHERE `key` IN ('o_email_client_account', 'o_admin_email_client_account');
UPDATE `options` SET `is_visible` = 0 WHERE `tab_id` = 3 AND `key` LIKE '%email_client_account%';
UPDATE `options` SET `is_visible` = 0 WHERE `tab_id` = 3 AND `key` LIKE 'o_email_forgot%';

ALTER TABLE `locations` ADD COLUMN `is_airport` tinyint(1) unsigned DEFAULT '0' AFTER `id`;

INSERT INTO `fields` VALUES (NULL, 'lblIsAirport', 'backend', 'Label / Is airport?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is airport?', 'script');

ALTER TABLE `bookings` ADD COLUMN `cc_owner` VARCHAR(255) DEFAULT NULL AFTER `c_cruise_ship`;
ALTER TABLE `bookings` ADD COLUMN `accept_shared_trip` tinyint(1) unsigned DEFAULT '0' AFTER `fleet_id`;
ALTER TABLE `bookings` ADD COLUMN `c_dialing_code` varchar(55) DEFAULT NULL AFTER `c_lname`;

ALTER TABLE `bookings` CHANGE `status` `status` enum('confirmed','cancelled','pending','passed_on') default 'pending';

INSERT INTO `fields` VALUES (NULL, 'booking_statuses_ARRAY_passed_on', 'arrays', 'booking_statuses_ARRAY_passed_on', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passed On', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPassedOnReservations', 'backend', 'Label / Passed On Reservations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passed On Reservations', 'script');

UPDATE `options` SET `value` = 'confirmed|pending|cancelled|passed_on::pending', `label` = 'Confirmed|Pending|Cancelled|Passed On' WHERE `key` = "o_booking_status";

UPDATE `options` SET `value` = 'confirmed|pending|cancelled|passed_on::confirmed', `label` = 'Confirmed|Pending|Cancelled|Passed On' WHERE `key` = "o_payment_status";

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_allow_creditcard_later', 2, 'Yes|No::Yes', 'Yes|No', 'enum', 19, 1, NULL);

ALTER TABLE `bookings` CHANGE `payment_method` `payment_method` enum('paypal','authorize','creditcard','creditcard_later','cash','bank') default NULL;

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_creditcard_later', 'backend', 'Options / Allow credit card', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payment with Credit Card on the day of Transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_creditcard_later', 'arrays', 'payment_methods_ARRAY_creditcard_later', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit Card on the day of Transfer', 'script');



DROP TABLE IF EXISTS `extras`;
CREATE TABLE IF NOT EXISTS `extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('T','F') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bookings_extras`;
CREATE TABLE IF NOT EXISTS `bookings_extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned DEFAULT NULL,
  `extra_id` int(10) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_id` (`booking_id`,`extra_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `extras` VALUES
  (NULL, 'T'),
  (NULL, 'T'),
  (NULL, 'T'),
  (NULL, 'T'),
  (NULL, 'T'),
  (NULL, 'T'),
  (NULL, 'T');

INSERT INTO `multi_lang` VALUES
  (NULL, 1, 'pjExtra', '::LOCALE::', 'name', 'LUGGAGE', 'data'),
  (NULL, 2, 'pjExtra', '::LOCALE::', 'name', 'BABY SEAT', 'data'),
  (NULL, 2, 'pjExtra', '::LOCALE::', 'info', '0-1 YEAR', 'data'),
  (NULL, 3, 'pjExtra', '::LOCALE::', 'name', 'CHILD SEAT', 'data'),
  (NULL, 3, 'pjExtra', '::LOCALE::', 'info', '1-5 YEARS', 'data'),
  (NULL, 4, 'pjExtra', '::LOCALE::', 'name', 'BOOSTER SEAT', 'data'),
  (NULL, 4, 'pjExtra', '::LOCALE::', 'info', '5-12 YEARS', 'data'),
  (NULL, 5, 'pjExtra', '::LOCALE::', 'name', 'SKI', 'data'),
  (NULL, 6, 'pjExtra', '::LOCALE::', 'name', 'SNOWBOARD', 'data'),
  (NULL, 7, 'pjExtra', '::LOCALE::', 'name', 'WHEELCHAIR', 'data');

INSERT INTO `fields` VALUES (NULL, 'menuExtras', 'backend', 'Menu Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddExtra', 'backend', 'Label / Add Extra', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Extra', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAllExtras', 'backend', 'Label / All Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblUpdateExtra', 'backend', 'Label / Update Extra', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Extra', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoExtrasTitle', 'backend', 'Infobox / Extras Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoExtrasBody', 'backend', 'Infobox / Extras Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below you can view and manage a list of extras which your clients can add (for example: child seat, GPS navigation, etc.). To add new extra, click on the Add + button below.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddExtraTitle', 'backend', 'Infobox / Add Extra Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add an extra', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddExtraBody', 'backend', 'Infobox / Add Extra Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add an extra, then click Save.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateExtraTitle', 'backend', 'Infobox / Update Extra Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update extra', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateExtraBody', 'backend', 'Infobox / Update Extra Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Change extra name and click on Save button.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AE01', 'arrays', 'error_titles_ARRAY_AE01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AE01', 'arrays', 'error_bodies_ARRAY_AE01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this extra have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AE03', 'arrays', 'error_titles_ARRAY_AE03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra Added', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AE03', 'arrays', 'error_bodies_ARRAY_AE03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New Extra has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AE04', 'arrays', 'error_titles_ARRAY_AE04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AE04', 'arrays', 'error_bodies_ARRAY_AE04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred! Data has not been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AE08', 'arrays', 'error_titles_ARRAY_AE08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AE08', 'arrays', 'error_bodies_ARRAY_AE08', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The extra you are looking for is missing.', 'script');


DROP TABLE IF EXISTS `drivers`;
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `driver_id` int(10) unsigned DEFAULT NULL AFTER `client_id`;

INSERT INTO `fields` VALUES (NULL, 'menuDrivers', 'backend', 'Menu Drivers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drivers', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddDriver', 'backend', 'Button / + Add driver', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add driver', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDriversTitle', 'backend', 'Infobox / Drivers Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of drivers', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDriversBody', 'backend', 'Infobox / Drivers Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of drivers. You can edit a specific driver by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddDriverTitle', 'backend', 'Infobox / Add Driver Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add driver', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddDriverBody', 'backend', 'Infobox / Add Driver Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new driver.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateDriverTitle', 'backend', 'Infobox / Update Driver Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update driver', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateDriverBody', 'backend', 'Infobox / Update Driver Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit driver information.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AD01', 'arrays', 'error_titles_ARRAY_AD01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AD01', 'arrays', 'error_bodies_ARRAY_AD01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this driver have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AD02', 'arrays', 'error_titles_ARRAY_AD02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AD02', 'arrays', 'error_bodies_ARRAY_AD02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The driver you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AD03', 'arrays', 'error_titles_ARRAY_AD03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AD03', 'arrays', 'error_bodies_ARRAY_AD03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New driver has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AD04', 'arrays', 'error_titles_ARRAY_AD04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AD04', 'arrays', 'error_bodies_ARRAY_AD04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the driver has not been added.', 'script');



DROP TABLE IF EXISTS `dialing_codes`;
CREATE TABLE IF NOT EXISTS `dialing_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `fields` VALUES (NULL, 'menuDialingCodes', 'backend', 'Menu Dialing Codes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing Codes', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDialingCode', 'backend', 'Label / Dialing Code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing Code', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddDialingCode', 'backend', 'Button / + Add dialing code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add dialing code', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDialingCodesTitle', 'backend', 'Infobox / Dialing Codes Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of dialing codes', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDialingCodesBody', 'backend', 'Infobox / Dialing Codes Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of dialing codes by country. You can edit a specific code by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddDialingCodeTitle', 'backend', 'Infobox / Add Dialing Code Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add dialing code', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddDialingCodeBody', 'backend', 'Infobox / Add Dialing Code Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new dialing code.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateDialingCodeTitle', 'backend', 'Infobox / Update Dialing Code Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update dialing code', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateDialingCodeBody', 'backend', 'Infobox / Update Dialing Code Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit dialing code.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ADC01', 'arrays', 'error_titles_ARRAY_ADC01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ADC01', 'arrays', 'error_bodies_ARRAY_ADC01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ADC02', 'arrays', 'error_titles_ARRAY_ADC02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ADC02', 'arrays', 'error_bodies_ARRAY_ADC02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The dialing code you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ADC03', 'arrays', 'error_titles_ARRAY_ADC03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ADC03', 'arrays', 'error_bodies_ARRAY_ADC03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New dialing code has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ADC04', 'arrays', 'error_titles_ARRAY_ADC04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ADC04', 'arrays', 'error_bodies_ARRAY_ADC04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the dialing code has not been added.', 'script');



DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `type` enum('amount','percent') DEFAULT NULL,
  `discount` decimal(9,2) unsigned DEFAULT NULL,
  `valid` enum('fixed','period','recurring') DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `every` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `voucher_code` varchar(255) DEFAULT NULL AFTER `deposit`,
                       ADD COLUMN `discount` decimal(9,2) DEFAULT NULL AFTER `sub_total`,
                       ADD COLUMN `c_hotel` varchar(255) DEFAULT NULL AFTER `c_destination_address`;

INSERT INTO `fields` VALUES (NULL, 'menuVouchers', 'backend', 'Menu Vouchers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discounts', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddVoucher', 'backend', 'Button / + Add voucher', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoVouchersTitle', 'backend', 'Infobox / Vouchers Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of discounts', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoVouchersBody', 'backend', 'Infobox / Vouchers Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of discounts. You can edit a specific discount by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_types_ARRAY_amount', 'arrays', 'voucher_types_ARRAY_amount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Amount', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_types_ARRAY_percent', 'arrays', 'voucher_types_ARRAY_percent', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Percent', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_valids_ARRAY_fixed', 'arrays', 'voucher_valids_ARRAY_fixed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fixed date', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_valids_ARRAY_period', 'arrays', 'voucher_valids_ARRAY_period', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Period', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_valids_ARRAY_recurring', 'arrays', 'voucher_valids_ARRAY_recurring', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recurring', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_monday', 'arrays', 'daynames_ARRAY_monday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Monday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_tuesday', 'arrays', 'daynames_ARRAY_tuesday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tuesday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_wednesday', 'arrays', 'daynames_ARRAY_wednesday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Wednesday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_thursday', 'arrays', 'daynames_ARRAY_thursday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thursday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_friday', 'arrays', 'daynames_ARRAY_friday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Friday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_saturday', 'arrays', 'daynames_ARRAY_saturday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saturday', 'script');

INSERT INTO `fields` VALUES (NULL, 'daynames_ARRAY_sunday', 'arrays', 'daynames_ARRAY_sunday', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Sunday', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddVoucherTitle', 'backend', 'Infobox / Add Voucher Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddVoucherBody', 'backend', 'Infobox / Add Voucher Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new discount.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateVoucherTitle', 'backend', 'Infobox / Update Voucher Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateVoucherBody', 'backend', 'Infobox / Update Voucher Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit discount information.', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_code', 'backend', 'voucher_code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher code', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_discount', 'backend', 'voucher_discount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_valid', 'backend', 'voucher_valid', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Valid', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_date', 'backend', 'voucher_date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_time_from', 'backend', 'voucher_time_from', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time from', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_time_to', 'backend', 'voucher_time_to', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time to', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_date_from', 'backend', 'voucher_date_from', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From date/time', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_date_to', 'backend', 'voucher_date_to', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To date/time', 'script');

INSERT INTO `fields` VALUES (NULL, 'voucher_every', 'backend', 'voucher_every', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Every', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AV01', 'arrays', 'error_titles_ARRAY_AV01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AV01', 'arrays', 'error_bodies_ARRAY_AV01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this discount have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AV02', 'arrays', 'error_titles_ARRAY_AV02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AV02', 'arrays', 'error_bodies_ARRAY_AV02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The discount you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AV03', 'arrays', 'error_titles_ARRAY_AV03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AV03', 'arrays', 'error_bodies_ARRAY_AV03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New discount has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AV04', 'arrays', 'error_titles_ARRAY_AV04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AV04', 'arrays', 'error_bodies_ARRAY_AV04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the discount has not been added.', 'script');



INSERT INTO `fields` VALUES (NULL, 'front_btn_select', 'frontend', 'Button / Select', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_continue', 'frontend', 'Button / Continue', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Continue', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_book_now', 'frontend', 'Button / Book Now', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Book Now', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_transfer_type_return', 'frontend', 'Label / Transfer type - Return', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_transfer_type_one_way', 'frontend', 'Label / Transfer type - One way', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One way', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_max_passengers', 'frontend', 'Label / Max passenger per vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Max <strong>{NUMBER} passengers</strong><br/> per vehicle', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_estimated_distance', 'frontend', 'Label / Estimated distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Estimated distance<br/><strong>{NUMBER}km</strong>', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_estimated_time', 'frontend', 'Label / Estimated time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Estimated time<br/><strong>{NUMBER} mins</strong>', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extra_description', 'frontend', 'Label / Extras description', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All Extras are free. However: please select the total number of pieces of baggage and extras for your transfers. If you arrive with more luggage than specified at booking, we cannot guarantee to transport them.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extra', 'frontend', 'Label / Extra', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_price', 'frontend', 'Label / Price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_count', 'frontend', 'Label / Count', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Count', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free', 'frontend', 'Label / Free', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_x', 'frontend', 'Label / Step X', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step {X} -- ', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_choose_vehicle', 'frontend', 'Steps / Choose Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Transfer Type', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_extras', 'frontend', 'Steps / Choose Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Baggage and Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_departure_info', 'frontend', 'Steps / Departure Information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure Information', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_return_info', 'frontend', 'Steps / Return Information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return Information', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_passenger_details', 'frontend', 'Steps / Passenger Details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passenger Details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_payment_details', 'frontend', 'Steps / Payment Details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary', 'frontend', 'Steps / Booking Summary', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thank you. Your booking is now confirmed.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_number_of_passengers', 'frontend', 'Label / Exact number of passengers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Exact number of passengers', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_hotel', 'frontend', 'Label / Name of hotel/pension', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name of hotel/pension', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_hour', 'frontend', 'Label / Hour', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Hour', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_minute', 'frontend', 'Label / Minute', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Minute', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_pickup_time', 'frontend', 'Label / Pick-up time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_pickup_date', 'frontend', 'Label / Pick-up date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_passenger_description', 'frontend', 'Label / Passenger description', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please ensure all of the required fields are completed at the time of booking. This information is imperative to ensure a smooth journey.<br />All fields are required.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_confirm_email', 'frontend', 'Label / Confirm email address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm email address', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_dialing_code', 'frontend', 'Label / Dialing code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dialing code', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_payment_details_description', 'frontend', 'Label / Payment details description', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please ensure all of the required fields are completed at the time of booking. This information is imperative to ensure a smooth journey.<br/>All fields are required.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_discount_code', 'frontend', 'Label / Discount Code', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount Code', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_accept_shared_trip', 'frontend', 'Label / Accept Shared Trip', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'I would be ready to share a car with someone else (up to 30% cheaper)', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_month', 'frontend', 'Label / Month', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Month', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_year', 'frontend', 'Label / Year', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Year', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cc_owner', 'frontend', 'Label / Credit card owner', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card owner', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_booking_summary', 'frontend', 'Label / Booking Summary', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Summary', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_departure', 'frontend', 'Label / Departure', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_return', 'frontend', 'Label / Return', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_date', 'frontend', 'Label / Date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_from', 'frontend', 'Label / From', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_to', 'frontend', 'Label / To', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_cart_passengers', 'frontend', 'Label / Pax.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pax.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_vehicle', 'frontend', 'Label / Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extras', 'frontend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_time', 'frontend', 'Label / Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_discount', 'frontend', 'Label / Discount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_payment', 'frontend', 'Label / Payment', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_email_mismatch', 'frontend', 'Label / Email mismatch', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Emails doesn''t match', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_name_surname', 'frontend', 'Label / Name and surname', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name and surname', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_mobile_number', 'frontend', 'Label / Mobile number', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mobile number', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_how_many_people', 'frontend', 'Label / How many people?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'How many people? <small>(including children)</small>', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_transfer_details', 'frontend', 'Label / Transfer Details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer Details', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_transfer_details', 'frontend', 'Label / Return Transfer Details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return Transfer Details', 'script');










INSERT INTO `fields` VALUES (NULL, 'front_about_us', 'frontend', 'Label / About us', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'About us', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_about_us_text', 'frontend', 'Label / About us', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_need_help', 'frontend', 'Label / Need help?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Need help?', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_need_help_text', 'frontend', 'Label / Need help?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contact us via phone or email:', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_need_help_phone', 'frontend', 'Label / Need help?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+1 555 555 555', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_need_help_email', 'frontend', 'Label / Need help?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'help@transfers.com', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_follow_us', 'frontend', 'Label / Follow us', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Follow us', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_contact_us', 'frontend', 'Label / Contact us', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contact us', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_copyright', 'frontend', 'Label / Copyright', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Copyright 2017, Themeenergy. All rights reserved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_home', 'frontend', 'Label / Home', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Home', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_blog', 'frontend', 'Label / Blog', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Blog', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_help', 'frontend', 'Label / Help', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Help', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_terms_of_use', 'frontend', 'Label / Terms of use', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Terms of use', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_for_partners', 'frontend', 'Label / For partners', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'For partners', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_error', 'frontend', 'Label / Error', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error! Please try again or contact support.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAcceptsSharedTrip', 'backend', 'Label / Accepts shared trip', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Accepts shared trip', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblCCOwner', 'backend', 'Label / CC Owner', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC owner', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblRemindClientViaEmail', 'backend', 'Label / Remind the Client via Email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind the Client via Email', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblRemindClientViaSMS', 'backend', 'Label / Remind the Client via SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind the Client via SMS', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDriver', 'backend', 'Label / Driver', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver', 'script');




INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_email_driver', 6, '0|1::1', 'No|Yes', 'enum', 1, 1, NULL),
  (1, 'o_email_driver_subject', 6, '', NULL, 'string', 2, 1, NULL),
  (1, 'o_email_driver_message', 6, '', NULL, 'text', 3, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_driver', 'backend', 'Options / Assigned transfer email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New transfer is assigned', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_driver_subject', 'backend', 'Options / Assigned transfer email subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_driver_message', 'backend', 'Options / Assigned transfer email message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_driver_text', 'backend', 'Options / Assigned transfer email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want an auto-responder to be sent to drivers after assigning them for a booking transfer.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_driver_message_text', 'backend', 'Options / Assigned transfer email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<u>Available Tokens:</u><br/><br/>{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{Address}<br/>{Date}<br/>{From}<br/>{To}<br/>{Fleet}<br/>{Passengers}<br/>{PaymentMethod}<br/>{Extras}', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_driver_subject', 'New transfer is assigned', 'data');
INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_driver_message', 'New transfer is assigned. Details are below:\r\n\r\nPersonal details:\r\nTitle: {Title}\r\nFirst Name: {FirstName}\r\nLast Name: {LastName}\r\nPhone: {Phone}\r\n\r\nReservation details:\r\nBooking date/time: {Date}, {Time}\r\nFrom: {From}\r\nTo: {To}\r\nFleet: {Fleet}\r\nPassengers: {Passengers}\r\n\r\nExtras: {Extras}\r\n\r\nThank you!', 'data');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_email_reminder', 3, '0|1::1', 'No|Yes', 'enum', 19, 1, NULL),
  (1, 'o_email_reminder_subject', 3, '', NULL, 'string', 20, 1, NULL),
  (1, 'o_email_reminder_message', 3, '', NULL, 'text', 21, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_reminder', 'backend', 'Options / Booking reminder email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking reminder', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_reminder_subject', 'backend', 'Options / Booking reminder email subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_reminder_message', 'backend', 'Options / Booking reminder email message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_reminder_text', 'backend', 'Options / Booking reminder email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want a reminder to be sent to clients for their new bookings.', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_reminder_subject', 'Booking reminder', 'data');
INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_reminder_message', 'Booking reminder. Details are below:\r\n\r\nPersonal details:\r\nTitle: {Title}\r\nFirst Name: {FirstName}\r\nLast Name: {LastName}\r\nPhone: {Phone}\r\n\r\nReservation details:\r\nBooking date/time: {Date}, {Time}\r\nFrom: {From}\r\nTo: {To}\r\nFleet: {Fleet}\r\nPassengers: {Passengers}\r\n\r\nExtras: {Extras}\r\n\r\nThank you!', 'data');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_sms_reminder_message', 3, '', NULL, 'text', 3, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_sms_reminder_message', 'backend', 'Options / Booking reminder SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS reminder', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_sms_reminder_message_text', 'backend', 'Options / Booking reminder SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can also send personalized SMS notifications via the each booking page. Available Tokens:<br/><br/>{FirstName}<br/>{LastName}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/>{Fleet}<br/>{Passengers}', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_sms_reminder_message', NULL, 'data');

INSERT INTO `fields` VALUES (NULL, 'legend_available', 'backend', 'Label / Available', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available', 'script');

INSERT INTO `fields` VALUES (NULL, 'legend_pending', 'backend', 'Label / Pending', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `fields` VALUES (NULL, 'legend_booked', 'backend', 'Label / Booked', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booked', 'script');

INSERT INTO `fields` VALUES (NULL, 'legend_past', 'backend', 'Label / Past', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Past', 'script');


INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_shared_trip_info', 5, '', NULL, 'text', 2, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_shared_trip_info', 'backend', 'Options / Shared trip information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shared trip information', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_deposit_payment_in_advance', 'frontend', 'Label / Deposit payment in advance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'A Deposit of {X}% will be transferred after your booking is confirmed. The remaining amount will be transferred at the day of the transfer.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_deposit', 'frontend', 'Label / Deposit', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Deposit', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_rest', 'frontend', 'Label / Rest', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rest', 'script');

ALTER TABLE `bookings` ADD COLUMN `internal_notes` TEXT DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblInternalNotes', 'backend', 'Label / Internal notes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Internal notes:', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFirstDrive', 'backend', 'Label / First drive', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '1st drive', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSecondDrive', 'backend', 'Label / Second drive', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '2nd drive', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSendPaymentConfirmation', 'backend', 'Label / Send Payment Confirmation to the Client', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Payment Confirmation to the Client', 'script');

COMMIT;