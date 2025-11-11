START TRANSACTION;



INSERT INTO `fields` VALUES (NULL, 'front_check_transfer_msg_ARRAY_1', 'arrays', 'front_check_transfer_msg_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum base station distance to pickup location is %s', 'script');


COMMIT;