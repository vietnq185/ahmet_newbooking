
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_step_booking_summary_desc', 'frontend', 'Steps / Booking Summary', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thank you for your booking. Please check your booking details below for any mistakes. You will receive a booking confirmation via Email. Please also check your spam folder. If you cannot find our confirmation email, please give us a call on our 24/7 support hotline.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSendCancellationEmail', 'backend', 'Label / Send Cancellation Email to the Client', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Cancellation Email to the Client', 'script');

COMMIT;