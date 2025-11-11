START TRANSACTION;

ALTER TABLE `locations` ADD COLUMN `color` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblColor', 'backend', 'Label / Color', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Color', 'script');



COMMIT;