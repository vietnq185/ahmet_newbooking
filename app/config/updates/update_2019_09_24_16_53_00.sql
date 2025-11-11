START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_driver_script_path', 1, NULL, NULL, 'string', 18, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_driver_script_path', 'backend', 'Options / Path to Driver Script', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Path to Driver Script', 'script');

ALTER TABLE `bookings` ADD COLUMN `price` decimal(9,2) DEFAULT NULL AFTER `deposit`;

INSERT INTO `fields` VALUES (NULL, 'lblPriceTransfer', 'backend', 'Label / Transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceFirstTransfer', 'backend', 'Label / Price first transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price first transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceReturnTransfer', 'backend', 'Label / Price return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price return transfer', 'script');



COMMIT;