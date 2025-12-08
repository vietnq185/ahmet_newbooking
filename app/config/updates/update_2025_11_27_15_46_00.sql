START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `dropoff_region` varchar(255) DEFAULT NULL;


INSERT INTO `fields` VALUES (NULL, 'lblBookingQA', 'backend', 'Label / Q&A', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Q&A', 'script');


COMMIT;