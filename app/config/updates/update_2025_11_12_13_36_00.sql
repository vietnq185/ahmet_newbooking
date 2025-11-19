START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary_2', 'backend', 'Label / Payment confirmation title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment confirmation', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary_2_desc', 'backend', 'Label / Payment confirmation body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking nummber: {ReferenceNumber}<br/>We have received your payment. You will receive a payment confirmation via Email. Please also check your spam folder. If you cannot find our payment confirmation email, please give us a call on our 24/7 support hotline. ', 'script');



COMMIT;