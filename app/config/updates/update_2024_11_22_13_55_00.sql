START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `name_sign` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblUploadNameSign', 'backend', 'Label / Upload name sign', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upload name sign', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDeleteNameSign', 'backend', 'Label / Delete name sign', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete name sign', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDeleteNameSignDesc', 'backend', 'Label / Delete name sign', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete this name sign?', 'script');



COMMIT;