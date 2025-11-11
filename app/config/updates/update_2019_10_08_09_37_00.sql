START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `google_map_link` text;

INSERT INTO `fields` VALUES (NULL, 'lblBookingGoogleMapsLink', 'backend', 'Label / Google maps link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google maps link', 'script');



COMMIT;