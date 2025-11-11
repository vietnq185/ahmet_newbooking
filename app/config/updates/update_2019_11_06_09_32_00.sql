START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `pickup_google_map_link` text;
ALTER TABLE `bookings` ADD COLUMN `dropoff_google_map_link` text;

INSERT INTO `fields` VALUES (NULL, 'lblBookingPickupGoogleMapsLink', 'backend', 'Label / Pick-up google maps link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up google maps link', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBookingDropoffGoogleMapsLink', 'backend', 'Label / Drop-off google maps link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drop-off google maps link', 'script');



COMMIT;