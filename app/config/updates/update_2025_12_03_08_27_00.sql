START TRANSACTION;

DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `available_for` enum('reservation_manager','admin','both') NOT NULL DEFAULT 'both', 
  `order` int(10) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,                         
  `status` enum('T','F') NOT NULL DEFAULT 'T',         
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),                                  
  KEY `status` (`status`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `fields` VALUES (NULL, 'infoWhatsappMessagesTitle', 'backend', 'Infobox / Whatsapp Messages Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages List', 'script');
				
INSERT INTO `fields` VALUES (NULL, 'infoWhatsappMessagesBody', 'backend', 'Infobox / Whatsapp Messages Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the Whatsapp Messages that you operate.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWMSubject', 'backend', 'Label / Subject', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWMMessage', 'backend', 'Label / Message', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWMOrder', 'backend', 'Label / Order', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWMAvailableFor', 'backend', 'Label / Available for', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available for', 'script');
				
INSERT INTO `fields` VALUES (NULL, 'lblWMStatus', 'backend', 'Label / Status', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `fields` VALUES (NULL, 'wm_available_for_ARRAY_reservation_manager', 'arrays', 'wm_available_for_ARRAY_reservation_manager', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reservations manager', 'script');

INSERT INTO `fields` VALUES (NULL, 'wm_available_for_ARRAY_admin', 'arrays', 'wm_available_for_ARRAY_admin', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin', 'script');

INSERT INTO `fields` VALUES (NULL, 'wm_available_for_ARRAY_both', 'arrays', 'wm_available_for_ARRAY_both', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin & Reservations manager', 'script');

INSERT INTO `fields` VALUES (NULL, 'wm_statuses_ARRAY_T', 'arrays', 'wm_statuses_ARRAY_T', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script');

INSERT INTO `fields` VALUES (NULL, 'wm_statuses_ARRAY_F', 'arrays', 'wm_statuses_ARRAY_F', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'script');
  			
INSERT INTO `fields` VALUES (NULL, 'infoAddWhatsappMessageTitle', 'backend', 'Infobox / Add Whatsapp Message Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new Whatsapp message', 'script');
				
INSERT INTO `fields` VALUES (NULL, 'infoAddWhatsappMessageBody', 'backend', 'Infobox / Add Whatsapp Message Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new Whatsapp message.', 'script');
				
INSERT INTO `fields` VALUES (NULL, 'infoUpdateWhatsappMessageTitle', 'backend', 'Infobox / Update Whatsapp Message Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Whatsapp message', 'script');
				
INSERT INTO `fields` VALUES (NULL, 'infoUpdateWhatsappMessageBody', 'backend', 'Infobox / Update Whatsapp Message Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your Whatsapp message.', 'script');
  		
INSERT INTO `fields` VALUES (NULL, 'infoAddUpdateWhatsappMessageToken', 'backend', 'Infobox / Add/Update Whatsapp Message Token', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<strong>Available tokens: </strong>{Title}, {FirstName}, {LastName}, {UniqueID}, {Fleet}, {Date}, {Time}, {From}, {To}', 'script');
	
  
INSERT INTO `fields` VALUES (NULL, 'btnWhatsApp', 'backend', 'Label / WhatsApp', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'btnSendWS', 'backend', 'Label / Send WhatsApp Message', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send WhatsApp Message', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblAddWhatsappMessage', 'backend', 'Label / Add Whatsapp Message', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Whatsapp Message', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblSelectLanguage', 'backend', 'Label / Language', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblSendWhatsappTo', 'backend', 'Label / To', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To', 'script');
  

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWHATSAPP01', 'arrays', 'error_titles_ARRAY_AWHATSAPP01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp message updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWHATSAPP01', 'arrays', 'error_bodies_ARRAY_AWHATSAPP01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this Whatsapp message have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWHATSAPP02', 'arrays', 'error_titles_ARRAY_AWHATSAPP02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp message not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWHATSAPP02', 'arrays', 'error_bodies_ARRAY_AWHATSAPP02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The Whatsapp message you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWHATSAPP03', 'arrays', 'error_titles_ARRAY_AWHATSAPP03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp message added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWHATSAPP03', 'arrays', 'error_bodies_ARRAY_AWHATSAPP03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New Whatsapp message has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWHATSAPP04', 'arrays', 'error_titles_ARRAY_AWHATSAPP04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp message failed to add.', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWHATSAPP04', 'arrays', 'error_bodies_ARRAY_AWHATSAPP04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the Whatsapp message has not been added.', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblBookingWhatsappMessages', 'backend', 'Label / Whatsapp Messages', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages', 'script');
 
 
 
 
INSERT INTO `fields` VALUES (NULL, 'tabWhatsappMessages', 'backend', 'Label / Whatsapp Messages', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages', 'script');
 

COMMIT;