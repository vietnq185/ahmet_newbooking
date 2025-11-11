
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_select_transfer_date', 'frontend', 'Label / Select transfer date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select your transfer date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_search_placeholder', 'frontend', 'Placeholder / Search', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Search...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_verify_departure_date', 'frontend', 'Label / Verify departure date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please verify your transfer date:', 'script');

ALTER TABLE `fleets` ADD COLUMN `return_discount` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`;

INSERT INTO `fields` VALUES (NULL, 'lblReturnDiscount', 'backend', 'Label / Return discount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_choose_transfer_type', 'frontend', 'Label / Select Transfer Type', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Transfer Type', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_transfer_type_description', 'frontend', 'Label / Transfer type description', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select your transfer type. You have the option to choose a one way transfer or a return transfer.', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_transfer_type_return");
UPDATE `multi_lang` SET `content` = 'With return' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_step_choose_vehicle");
UPDATE `multi_lang` SET `content` = 'Choose Vehicle' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

INSERT INTO `fields` VALUES (NULL, 'front_one_way_transfer', 'frontend', 'Label / One way transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One way transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_transfer', 'frontend', 'Label / Return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_transfer_discount', 'frontend', 'Label / Return transfer discount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '-{X}% return discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_total_price', 'frontend', 'Label / Total price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total price', 'script');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_extras_max_qty', 2, 20, NULL, 'int', NULL, 0, NULL);

DROP TABLE IF EXISTS `extras_limitations`;
CREATE TABLE IF NOT EXISTS `extras_limitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extra_id` int(10) unsigned DEFAULT NULL,
  `fleet_id` int(10) unsigned DEFAULT NULL,
  `max_qty` int(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extra_id` (`extra_id`,`fleet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `fields` VALUES (NULL, 'infoExtrasLimitationsTitle', 'backend', 'Infobox / Extras'' limitations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras'' limitations', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoExtrasLimitationsDesc', 'backend', 'Infobox / Extras'' limitations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make changes to the maximum allowed quantity for each extra you have added to the system. Also you can hide some extras for this vehicle by setting the value to 0.', 'script');


COMMIT;