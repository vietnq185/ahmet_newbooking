START TRANSACTION;



INSERT INTO `fields` VALUES (NULL, 'btnSendPdf', 'backend', 'Label / Send PDF', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send PDF', 'script');


COMMIT;