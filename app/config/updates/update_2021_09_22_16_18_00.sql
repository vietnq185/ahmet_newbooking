START TRANSACTION;


DROP TABLE IF EXISTS `stations`;
CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL,
  `start_fee` decimal(9,2) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,                      
  `lng` float(10,6) DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stations_fees`;
CREATE TABLE IF NOT EXISTS `stations_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(10) unsigned NOT NULL DEFAULT '0',
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `dropoff_place_id` int(10) unsigned DEFAULT NULL COMMENT 'Place ID' AFTER `dropoff_id`;
ALTER TABLE `bookings` ADD COLUMN `station_fee` decimal(9,2) DEFAULT NULL AFTER `price`;
ALTER TABLE `bookings` ADD COLUMN `station_id` int(10) DEFAULT NULL AFTER `station_fee`;
ALTER TABLE `bookings` ADD COLUMN `pickup_type` enum('server','google') DEFAULT 'server' AFTER `location_id`;
ALTER TABLE `bookings` ADD COLUMN `dropoff_type` enum('server','google') DEFAULT 'server' AFTER `dropoff_place_id`;
ALTER TABLE `bookings` ADD COLUMN (
	`pickup_address` varchar(255) DEFAULT NULL,
	`pickup_lat` float(10,6) DEFAULT NULL,                      
 	`pickup_lng` float(10,6) DEFAULT NULL,
 	`pickup_is_airport` tinyint(1) DEFAULT '0',
 	`dropoff_address` varchar(255) DEFAULT NULL,
	`dropoff_lat` float(10,6) DEFAULT NULL,                      
 	`dropoff_lng` float(10,6) DEFAULT NULL,
 	`dropoff_is_airport` tinyint(1) DEFAULT '0',
 	`duration` int(10) DEFAULT NULL,
	`distance` int(10) DEFAULT NULL  
);
ALTER TABLE `bookings` MODIFY `location_id` varchar(255) DEFAULT NULL;
ALTER TABLE `bookings` MODIFY `dropoff_place_id` varchar(255) DEFAULT NULL;


ALTER TABLE `fleets` ADD COLUMN `start_fee` decimal(9,2) DEFAULT NULL AFTER `crossedout_type`;

DROP TABLE IF EXISTS `fleets_fees`;
CREATE TABLE IF NOT EXISTS `fleets_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,       
  `order_index` int(10) unsigned DEFAULT NULL,
   `status` enum('T','F') DEFAULT 'T',       
   `modified` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `areas_coords`;
CREATE TABLE IF NOT EXISTS `areas_coords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,             
  `area_id` int(10) unsigned DEFAULT NULL,               
  `type` enum('circle','polygon','rectangle') DEFAULT NULL,  
  `icon` varchar(255) DEFAULT NULL,
  `is_airport` tinyint(1) DEFAULT '0',
  `data` text,                    
  `tmp_hash` varchar(255) DEFAULT NULL,                      
 `created` datetime DEFAULT NULL, 
  PRIMARY KEY (`id`),                                        
  KEY `area_id` (`area_id`),                         
  KEY `type` (`type`),
  KEY `tmp_hash` (`tmp_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dropoff_areas`;
CREATE TABLE IF NOT EXISTS `dropoff_areas` (
  `dropoff_id` int(10) unsigned DEFAULT NULL,               
  `area_id` int(10) unsigned DEFAULT NULL, 
  UNIQUE KEY `dropoff_id` (`dropoff_id`,`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `locations` ADD COLUMN (
	`address` varchar(255) DEFAULT NULL,                   
	`lat` float(10,6) DEFAULT NULL,                        
	`lng` float(10,6) DEFAULT NULL
);

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_google_api_key', 1, NULL, NULL, 'string', 19, 1, NULL),
(1, 'o_default_country', 1, '236', NULL, 'int', 20, 1, NULL),
(1, 'o_max_base_station_distance', 2, NULL, NULL, 'float', 22, 1, NULL),
(1, 'o_min_travel_distance', 2, NULL, NULL, 'float', 23, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_google_api_key', 'backend', 'Options / Google API key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google API key', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_default_country', 'backend', 'Options / Default country', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default country', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_max_base_station_distance', 'backend', 'Label / Maximum Base station distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum Base station distance', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_min_travel_distance', 'backend', 'Label / Minimum travel distance', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Minimum travel distance', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuStations', 'backend', 'Menu / Stations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stations', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationTitle', 'backend', 'Label / Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Title', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationAddress', 'backend', 'Label / Address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationBaseStation', 'backend', 'Label / Base station', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Base station', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationStartFee', 'backend', 'Label / Start fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start fee', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAddStation', 'backend', 'Button / + Add station', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '+ Add station', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoStationsTitle', 'backend', 'Infobox / Stations Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of stations', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoStationsBody', 'backend', 'Infobox / Stations Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of stations. You can edit a specific station by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddStationTitle', 'backend', 'Infobox / Add Station Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add station', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddStationBody', 'backend', 'Infobox / Add Station Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new station.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateStationTitle', 'backend', 'Infobox / Update Station Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update station', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateStationBody', 'backend', 'Infobox / Update Station Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit station information.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ASTA01', 'arrays', 'error_titles_ARRAY_ASTA01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ASTA01', 'arrays', 'error_bodies_ARRAY_ASTA01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this station have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ASTA02', 'arrays', 'error_titles_ARRAY_ASTA02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ASTA02', 'arrays', 'error_bodies_ARRAY_ASTA02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The station you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ASTA03', 'arrays', 'error_titles_ARRAY_ASTA03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ASTA03', 'arrays', 'error_bodies_ARRAY_ASTA03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New station has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ASTA04', 'arrays', 'error_titles_ARRAY_ASTA04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ASTA04', 'arrays', 'error_bodies_ARRAY_ASTA04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the station has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFromInKm', 'backend', 'Label / From (km)', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From (km)', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblToInKm', 'backend', 'Label / To (km)', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To (km)', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPricePerKm', 'backend', 'Label / Price per km', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price per km', 'script');

INSERT INTO `fields` VALUES (NULL, 'pj_digits_validation', 'backend', 'Label / Please enter only digits', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter only digits', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblToGreaterThanFrom', 'backend', 'Label / To must be greater than From', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To must be greater than From', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationAddress', 'backend', 'Label / Pick-up location address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location address', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationRadius', 'backend', 'Label / Radius', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Radius', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationFee', 'backend', 'Label / Station fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station fee', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStationFeePerPerson', 'backend', 'Label / Fee per person', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fee per person', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFleetStartFee', 'backend', 'Label / Start fee', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start fee', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblFleetFeePerPerson', 'backend', 'Label / Fee per person', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fee per person', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuAreas', 'backend', 'Menu / Areas', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Areas', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAreaName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnDeleteShape', 'backend', 'Label / Delete Selected Shape', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete Selected Shape', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAreasTitle', 'backend', 'Infobox / Areas Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of areas', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAreasBody', 'backend', 'Infobox / Areas Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of areas. You can edit a specific area by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddAreaTitle', 'backend', 'Infobox / Add Area Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add area', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddAreaBody', 'backend', 'Infobox / Add Area Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new area. You can add many cities, places, etc. in one area.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateAreaTitle', 'backend', 'Infobox / Update Area Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update area', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateAreaBody', 'backend', 'Infobox / Update Area Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit area. You can add many cities, places, etc. in one area.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AAREA01', 'arrays', 'error_titles_ARRAY_AAREA01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AAREA01', 'arrays', 'error_bodies_ARRAY_AAREA01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this area have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AAREA02', 'arrays', 'error_titles_ARRAY_AAREA02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AAREA02', 'arrays', 'error_bodies_ARRAY_AAREA02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The area you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AAREA03', 'arrays', 'error_titles_ARRAY_AAREA03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AAREA03', 'arrays', 'error_bodies_ARRAY_AAREA03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New area has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AAREA04', 'arrays', 'error_titles_ARRAY_AAREA04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AAREA04', 'arrays', 'error_bodies_ARRAY_AAREA04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the area has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddArea', 'backend', 'Label / Add area', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add area', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationAreas', 'backend', 'Label / Areas', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Areas', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPlaceName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnClose', 'backend', 'Button / Close', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Close', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPlacesCities', 'backend', 'Label / Places/Cities', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Places/Cities', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_search_locations_empty', 'frontend', 'Label / No results found', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No results found', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_search_locations_searching', 'frontend', 'Label / Searching...', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Searching...', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSetPlaceName', 'backend', 'Label / Set place name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set place name', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_send_inquiry', 'frontend', 'Button / Send Inquiry', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Inquiry', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTransferFrom', 'backend', 'Label / From', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTransferTo', 'backend', 'Label / To', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To', 'script');


SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{ReturnNotes}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{StationFee}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";


COMMIT;