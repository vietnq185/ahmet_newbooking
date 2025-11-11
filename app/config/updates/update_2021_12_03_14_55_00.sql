START TRANSACTION;

ALTER TABLE `stations` ADD COLUMN `free_starting_fee_in_km` int(10) DEFAULT NULL AFTER `lng`;

ALTER TABLE `stations` ADD COLUMN `max_base_station_distance` int(10) DEFAULT NULL AFTER `free_starting_fee_in_km`;
ALTER TABLE `stations` ADD COLUMN `min_travel_distance` int(10) DEFAULT NULL AFTER `max_base_station_distance`;

ALTER TABLE `areas_coords` ADD COLUMN `is_disabled` tinyint(1) DEFAULT '0';

UPDATE `options` SET `is_visible`='0' WHERE `key` IN('o_max_base_station_distance','o_min_travel_distance');

ALTER TABLE `fleets` ADD COLUMN `station_id` int(10) DEFAULT NULL AFTER `image_name`;

INSERT INTO `fields` VALUES (NULL, 'lblStationFreeStartingFee', 'backend', 'Label / Free starting free', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Free starting fee if the location is closer than XX kilometers', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationMinTravelDistance', 'backend', 'Label / Minimum travel distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Minimum travel distance', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationMaxBaseStationDistance', 'backend', 'Label / Maximum base station distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum base station distance', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDisableThisArea', 'backend', 'Label / Disable this area', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Disable this area', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblVehicleBaseStation', 'backend', 'Label / Base station', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Base station', 'script');


COMMIT;