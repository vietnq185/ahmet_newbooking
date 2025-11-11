
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_departure', 'frontend', 'Label / Departure', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure', 'script');

COMMIT;