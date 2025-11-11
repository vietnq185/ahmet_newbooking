
START TRANSACTION;

ALTER TABLE `fleets` ADD COLUMN `min_passengers` int(5) unsigned DEFAULT NULL AFTER `id`;

INSERT INTO `fields` VALUES (NULL, 'lblMinPassenger', 'backend', 'Label / Minimum passenger', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Minimum passenger', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblMaxPassenger', 'backend', 'Label / Maximum passenger', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum passenger', 'script');

COMMIT;