START TRANSACTION;


ALTER TABLE `dropoff` ADD COLUMN `base_station_id` int(11) DEFAULT NULL;

 ALTER TABLE `fleets` ADD COLUMN `status_on_preselected_route` tinyint(1) DEFAULT '1';


INSERT INTO `fields` VALUES (NULL, '_status_on_preselected_route_ARRAY_1', 'arrays', '_status_on_preselected_route_ARRAY_1', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Show', 'script');

INSERT INTO `fields` VALUES (NULL, '_status_on_preselected_route_ARRAY_0', 'arrays', '_status_on_preselected_route_ARRAY_0', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Disable', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStatusOnPreSelectedRoutes', 'backend', 'Label / Status on pre-selected routes', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status on pre-selected routes', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBaseStation', 'backend', 'Label / Station', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station', 'script');


COMMIT;