START TRANSACTION;

ALTER TABLE `bookings` MODIFY `status` enum('confirmed','cancelled','pending','passed_on','in_progress') DEFAULT 'pending';


INSERT INTO `fields` VALUES (NULL, 'booking_statuses_ARRAY_in_progress', 'arrays', 'booking_statuses_ARRAY_in_progress', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In Progress', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInProgressReservations', 'backend', 'Label / Booking in Progress', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking in Progress', 'script');

UPDATE `options` SET `value` = 'confirmed|pending|cancelled|passed_on|in_progress::pending', `label` = 'Confirmed|Pending|Cancelled|Passed On|In Progress' WHERE `key` = "o_booking_status";

UPDATE `options` SET `value` = 'confirmed|pending|cancelled|passed_on|in_progress::pending', `label` = 'Confirmed|Pending|Cancelled|Passed On|In Progress' WHERE `key` = "o_payment_status";


COMMIT;