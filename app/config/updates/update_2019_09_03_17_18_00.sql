START TRANSACTION;
--
  INSERT INTO `fields` VALUES (NULL, 'lblBookingDuplicate', 'backend', 'Label / Duplicate booking', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Duplicate booking', 'script');

  DROP TABLE IF EXISTS `arrival_notice`;
  CREATE TABLE `arrival_notice` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `date_from` DATE NULL,
    `date_to` DATE NULL,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB;

  INSERT INTO `fields` VALUES (NULL, 'menuArrivalNotice', 'backend', 'Menu / Send Arrival Notice', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Arrival Notice', 'script');

  INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AOAN01', 'arrays', 'error_titles_ARRAY_AOAN01', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Success', 'script');

  INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AOAN01', 'arrays', 'error_bodies_ARRAY_AOAN01', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrival notice dates saved successful.', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblPrintReservationDetailsSingle', 'backend', 'Label / Print reservation details single', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print reservation details single', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblPrintReservationDetailsSingle2', 'backend', 'Label / Print reservation details single (first trip)', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print reservation details single (first trip)', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblPrintReservationDetailsSingle3', 'backend', 'Label / Print reservation details single (return trip)', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print reservation details single (return trip)', 'script');

  ALTER TABLE `locations` ADD `order_index` INT UNSIGNED NULL AFTER `icon`;
  ALTER TABLE `dropoff` ADD `order_index` INT UNSIGNED NULL AFTER `icon`;

  INSERT INTO `fields` VALUES (NULL, 'lblOrderIndex', 'backend', 'Label / Order index', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set order of appearance', 'script');

  ALTER TABLE `fleets` ADD `order_index` INT UNSIGNED NULL AFTER `status`;

  INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
    (1, 'o_link_to_inquiry_form', 1, NULL, NULL, 'string', 17, 1, NULL);

  INSERT INTO `fields` VALUES (NULL, 'opt_o_link_to_inquiry_form', 'backend', 'Options / Link to inquiry form', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Link to inquiry form', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblSendInquiry', 'backend', 'Label / Send inquiry', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send inquiry', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblVehicleCrossedOutPrice', 'backend', 'Label / Crossed-out price', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Crossed-out price', 'script');

  ALTER TABLE `fleets` ADD COLUMN `is_crossedout_price` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `luggage`;

  INSERT INTO `fields` VALUES (NULL, 'lblReportBookingsCreated', 'backend', 'Label / Bookings created', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bookings created', 'script');

  DROP TABLE IF EXISTS `fleets_discounts_periods`;
  CREATE TABLE `fleets_discounts_periods` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fleet_discount_id` INT UNSIGNED NOT NULL,
    `date_from` DATE NOT NULL,
    `date_to` DATE NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB;

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_authorize', 'arrays', 'payment_methods_short_ARRAY_authorize', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize', 'script');

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_bank', 'arrays', 'payment_methods_short_ARRAY_bank', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bank', 'script');

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_cash', 'arrays', 'payment_methods_short_ARRAY_cash', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_creditcard', 'arrays', 'payment_methods_short_ARRAY_creditcard', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Advance', 'script');

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_creditcard_later', 'arrays', 'payment_methods_short_ARRAY_creditcard_later', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit Card', 'script');

  INSERT INTO `fields` VALUES (NULL, 'payment_methods_short_ARRAY_paypal', 'arrays', 'payment_methods_short_ARRAY_paypal', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal', 'script');

  ALTER TABLE `bookings` ADD `customized_name_plate` VARCHAR(255) NULL AFTER `internal_notes`;

  INSERT INTO `fields` VALUES (NULL, 'lblBookingCustomizedNamePlate', 'backend', 'Label / Customized name plate', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customized name plate', 'script');

COMMIT;