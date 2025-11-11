
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_miles', 'frontend', 'Label / miles', 'script', '2016-11-21 02:24:43');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'miles', 'script');

COMMIT;