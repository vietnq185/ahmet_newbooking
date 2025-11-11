
START TRANSACTION;

ALTER TABLE `dropoff` ADD COLUMN `is_airport` tinyint(1) unsigned DEFAULT '0';

INSERT INTO `fields` VALUES (NULL, 'lblAirport', 'backend', 'Label / Airport?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Airport?', 'script');



COMMIT;