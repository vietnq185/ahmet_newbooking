START TRANSACTION;


INSERT INTO `fields` VALUES (NULL, 'tabBookingConfirmations', 'backend', 'Label / Booking Confirmations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking Confirmations', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabCustomerEmails', 'backend', 'Label / Customer Emails', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer Emails', 'script');




COMMIT;