START TRANSACTION;

DROP TABLE IF EXISTS `bookings_history`;
CREATE TABLE IF NOT EXISTS `bookings_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,           
  `booking_id` int(10) unsigned DEFAULT NULL,                                           
  `user_id` int(10) unsigned DEFAULT NULL,    
  `action` text,
  `created` datetime DEFAULT NULL,                                         
  PRIMARY KEY (`id`),                                                                   
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `fields` VALUES (NULL, 'btnHasNotes', 'backend', 'Label / Has Notes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Has Notes', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabLog', 'backend', 'Label / Log', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Log', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBokingHistoryCreated', 'backend', 'Label / Created', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBokingHistoryContent', 'backend', 'Label / Content', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Content', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBokingHistoryBy', 'backend', 'Label / By', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'By', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblBokingHistoryByOnline', 'backend', 'Label / Online', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Online', 'script');

INSERT INTO `fields` VALUES (NULL, '_yesno_ARRAY_1', 'arrays', '_yesno_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `fields` VALUES (NULL, '_yesno_ARRAY_0', 'arrays', '_yesno_ARRAY_0', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');



COMMIT;