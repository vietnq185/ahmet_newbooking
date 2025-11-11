START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_extra_outward_transfer', 'frontend', 'Label / Outward transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Outward transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_extra_return_transfer', 'frontend', 'Label / Return transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_11', 'arrays', 'front_messages_ARRAY_11', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Make payment', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_12', 'arrays', 'front_messages_ARRAY_12', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your reservation is saved. Please make payment to complete your reservation.', 'script');


COMMIT;