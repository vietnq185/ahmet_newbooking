START TRANSACTION;


SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "front_step_booking_summary");
UPDATE `multi_lang` SET `content` = 'Thank you. Your booking is now confirmed. Ref Nr. {ReferenceNumber}' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "front_step_booking_summary_1");
UPDATE `multi_lang` SET `content` = 'Arrival notice: Thank you for booking. Ref Nr. {ReferenceNumber}' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";



COMMIT;