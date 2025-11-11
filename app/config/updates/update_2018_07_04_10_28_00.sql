
START TRANSACTION;


DROP TABLE IF EXISTS `fleets_discounts`;
CREATE TABLE IF NOT EXISTS `fleets_discounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned DEFAULT NULL,
  `day` varchar(255) DEFAULT NULL,
  `type` enum('amount','percent') DEFAULT NULL,                                                       
  `discount` decimal(9,2) unsigned DEFAULT NULL,
  `time_from` time DEFAULT NULL,                                                                      
  `time_to` time DEFAULT NULL, 
  `is_subtract` enum('T','F') DEFAULT 'T',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_message_bird_originator', 1, NULL, NULL, 'string', 14, 1, NULL),
(1, 'o_message_bird_access_key', 1, NULL, NULL, 'string', 15, 1, NULL),
(1, 'o_email_arrival_confirmation', 3, '0|1::1', 'No|Yes', 'enum', 27, 1, NULL),
(1, 'o_email_arrival_confirmation_subject', 3, '', NULL, 'string', 28, 1, NULL),
(1, 'o_email_arrival_confirmation_message', 3, '', NULL, 'text', 29, 1, NULL),
(1, 'o_admin_email_arrival_confirmation', 3, '0|1::1', 'No|Yes', 'enum', 14, 1, NULL),
(1, 'o_admin_email_arrival_confirmation_subject', 3, '', NULL, 'string', 15, 1, NULL),
(1, 'o_admin_email_arrival_confirmation_message', 3, '', NULL, 'text', 16, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_message_bird_originator', 'backend', 'Options / Message bird originator', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message bird originator', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_message_bird_access_key', 'backend', 'Options / Message bird access key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message bird access key', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblStatusReturnTrip', 'backend', 'Label / Status return trip', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status return trip', 'script');

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{ReturnNotes}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";

INSERT INTO `fields` VALUES (NULL, 'lblExtras', 'backend', 'Label / Extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPassengersReturn', 'backend', 'Label / Passengers return', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passengers return', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabExportEmails', 'backend', 'Tab / Export Emails', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Export Emails', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblExportFrom', 'backend', 'Label / From', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblExportTo', 'backend', 'Label / To', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnExport', 'backend', 'Button / Export', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Export', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPrintReservationDetails', 'backend', 'Label / Print reservation details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print reservation details', 'script');

INSERT INTO `fields` VALUES (NULL, '_fleet_additional_discount_ARRAY_F', 'arrays', '_fleet_additional_discount_ARRAY_F', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Markup', 'script');

INSERT INTO `fields` VALUES (NULL, '_fleet_additional_discount_ARRAY_T', 'arrays', '_fleet_additional_discount_ARRAY_T', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Discount', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_arrival_confirmation', 'backend', 'Options / New booking arrival', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New booking arrival', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_arrival_confirmation_text', 'backend', 'Options / New booking arrival', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want an auto-responder to be sent to clients after submiting new booking.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_arrival_confirmation_subject', 'backend', 'Options / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_arrival_confirmation_message', 'backend', 'Options / Message body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_arrival_confirmation', 'backend', 'Options / New booking arrival', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New booking arrival', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_arrival_confirmation_text', 'backend', 'Options / New booking arrival', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want an auto-responder to be sent to clients after submiting new booking.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_arrival_confirmation_subject', 'backend', 'Options / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_arrival_confirmation_message', 'backend', 'Options / Message body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblCopyLocationAndPrice', 'backend', 'Label / Copy prices & locations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'or copy prices & drop-off locations from another location', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_return_discount_info', 'frontend', 'Label / Return discount info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '%s discount if you book return transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_recommended_airport_pickup_time_info', 'frontend', 'Label / Recommended pick-up time info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The transfer from %s to %s takes approximately %s minutes. We advise arriving at the airport 2 hours in advance of your flight departure.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_recommended_pickup_time_info', 'frontend', 'Label / Recommended pick-up time info', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The transfer from %s to %s takes approximately %s minutes.', 'script');




COMMIT;