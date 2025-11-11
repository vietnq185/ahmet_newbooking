
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'btnAddTransfer', 'backend', 'Button / + Add transfer', 'script', '2016-12-05 07:40:25');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddVehicle', 'backend', 'Button / + Add vehicle', 'script', '2016-12-05 07:45:55');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add vehicle', 'script');

COMMIT;