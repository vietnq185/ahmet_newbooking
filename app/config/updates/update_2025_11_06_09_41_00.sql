START TRANSACTION;


ALTER TABLE `bookings` ADD COLUMN `notes_for_support` text DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblNoteForSupportTeam', 'backend', 'Label / Note for support team', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note for support team', 'script');



DROP TABLE IF EXISTS `emails_themes`;
CREATE TABLE IF NOT EXISTS `emails_themes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
   `status` enum('T','F') DEFAULT 'T',      
   `domain` varchar(255) DEFAULT NULL,
   `created` datetime default NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



INSERT INTO `fields` VALUES (NULL, 'tabEmailsThemes', 'backend', 'Label / Emails Themes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Emails Themes', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEmailThemeName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEmailThemeSubject', 'backend', 'Label / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEmailThemeBody', 'backend', 'Label / Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoEmailThemesTitle', 'backend', 'Infobox / Emails Themes Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of emails themes', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoEmailThemesBody', 'backend', 'Infobox / Emails Themes Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of emails themes. You can edit a specific email theme by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddEmailThemeTitle', 'backend', 'Infobox / Add Email Theme Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add email theme', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddEmailThemeBody', 'backend', 'Infobox / Add Email Theme Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new email theme.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateEmailThemeTitle', 'backend', 'Infobox / Update Email Theme Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update email theme', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateEmailThemeBody', 'backend', 'Infobox / Update Email Theme Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit email theme.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AET01', 'arrays', 'error_titles_ARRAY_AET01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email theme updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AET01', 'arrays', 'error_bodies_ARRAY_AET01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this email theme have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AET02', 'arrays', 'error_titles_ARRAY_AET02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email theme not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AET02', 'arrays', 'error_bodies_ARRAY_AET02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The email theme you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AET03', 'arrays', 'error_titles_ARRAY_AET03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email theme added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AET03', 'arrays', 'error_bodies_ARRAY_AET03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New email theme has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AET04', 'arrays', 'error_titles_ARRAY_AET04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email theme failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AET04', 'arrays', 'error_bodies_ARRAY_AET04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the email theme has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddEmailTheme', 'backend', 'Label / Add email theme', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add email theme', 'script');

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoAddEmailThemeBody");
UPDATE `multi_lang` SET `content` = 'Fill in the form below and click "Save" button to add new email theme.<br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/>{PriceFirstTransfer}<br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PriceReturnTransfer}<br/>{ReturnNotes}<br/>{ReturnExtras}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{StationFee}<br/>{ExtraPrice}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{PaymentURL}<br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoUpdateEmailThemeBody");
UPDATE `multi_lang` SET `content` = 'You can make any changes on the form below and click "Save" button to edit email theme.<br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/>{PriceFirstTransfer}<br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PriceReturnTransfer}<br/>{ReturnNotes}<br/>{ReturnExtras}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{StationFee}<br/>{ExtraPrice}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{PaymentURL}<br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1) AND `model` = "pjField" AND `field` = "title";


INSERT INTO `fields` VALUES (NULL, 'lblSendCustomEmail', 'backend', 'Label / Send custom email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send custom email', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB18', 'arrays', 'error_titles_ARRAY_AB18', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email not sent', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB18', 'arrays', 'error_bodies_ARRAY_AB18', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry that the email could not be sent successfully.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB19', 'arrays', 'error_titles_ARRAY_AB19', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email sent', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB19', 'arrays', 'error_bodies_ARRAY_AB19', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email has been sent successfully to the client.', 'script');


COMMIT;