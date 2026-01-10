START TRANSACTION;


ALTER TABLE `fleets` ADD COLUMN `price_per` enum('default','distance') DEFAULT 'default';



INSERT INTO `fields` VALUES (NULL, '_vehicle_price_per_ARRAY_default', 'arrays', '_vehicle_price_per_ARRAY_default', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default', 'script');

INSERT INTO `fields` VALUES (NULL, '_vehicle_price_per_ARRAY_distance', 'arrays', '_vehicle_price_per_ARRAY_distance', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Via km', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblVehiclePricePer', 'backend', 'Label / Price per', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price per', 'script');


COMMIT;