START TRANSACTION;

DROP TABLE IF EXISTS `fleets_fees_days`;
CREATE TABLE IF NOT EXISTS `fleets_fees_days` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `day` varchar(255) DEFAULT NULL,
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `areas_coords` ADD COLUMN `price_level` tinyint(1) DEFAULT '1';


INSERT INTO `fields` VALUES (NULL, 'lblAreaCoordPrice', 'backend', 'Label / Price', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');


COMMIT;