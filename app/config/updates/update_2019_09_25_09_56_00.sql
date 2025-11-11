START TRANSACTION;

  INSERT INTO `fields` VALUES (NULL, 'lblPercent', 'backend', 'Label / Percent', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Percent', 'script');

  ALTER TABLE `fleets`
    ADD `crossedout_price` DECIMAL(9,2) NULL AFTER `luggage`,
    ADD `crossedout_type` ENUM('amount','percent') NULL AFTER `crossedout_price`;

  ALTER TABLE `fleets` DROP `is_crossedout_price`;

  ALTER TABLE `fleets_discounts_periods`
    ADD `is_subtract` ENUM('T','F') NULL AFTER `date_to`,
    ADD `discount` DECIMAL(9,2) NULL AFTER `is_subtract`,
    ADD `type` ENUM('amount','percent') NULL AFTER `discount`;

COMMIT;