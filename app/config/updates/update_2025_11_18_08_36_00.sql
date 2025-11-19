START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'front_booking_sesstion_expired_title', 'frontend', 'Label / Booking Session Expired Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Session Expired', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_booking_sesstion_expired_desc', 'frontend', 'Label / Booking Session Expired Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We apologize, but your current session has expired due to prolonged inactivity. Please Restart Booking to proceed and ensure vehicle availability.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_restart_booking', 'frontend', 'Label / Restart Booking', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Restart Booking', 'script');


COMMIT;