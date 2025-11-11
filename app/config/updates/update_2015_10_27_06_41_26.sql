
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblStatusTitle', 'backend', 'Label / Status', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStatusStart', 'backend', 'Label / Please wait while locations are saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please wait while locations are saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStatusEnd', 'backend', 'Label / Locations have been saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Locations have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStatusFail', 'backend', 'Label / Locations could not be saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Locations could not be saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceStatusStart', 'backend', 'Label / Please wait while prices are saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please wait while prices are saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceStatusEnd', 'backend', 'Label / Prices have been saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prices have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceStatusFail', 'backend', 'Label / Prices could not be saved.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prices could not be saved.', 'script');

COMMIT;