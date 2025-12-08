START TRANSACTION;

 
INSERT INTO `fields` VALUES (NULL, 'front_max_passengers_over_8', 'frontend', 'Label / Max passenger per vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Max {NUMBER} passengers', 'script');


COMMIT;