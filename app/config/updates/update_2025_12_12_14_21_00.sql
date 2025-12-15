START TRANSACTION;

 ALTER TABLE `bookings` ADD COLUMN `is_synchronized` tinyint(1) DEFAULT '0';


INSERT INTO `fields` VALUES (NULL, 'lblSynchronized', 'backend', 'Label / Synchronized', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Synchronized', 'script');


COMMIT;