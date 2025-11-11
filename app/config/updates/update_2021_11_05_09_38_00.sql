START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `pickup_id` int(10) DEFAULT NULL AFTER `dropoff_google_map_link`;

ALTER TABLE `locations` ADD COLUMN `area_id` int(10) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblLocationArea', 'backend', 'Label / Area', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area', 'script');


COMMIT;