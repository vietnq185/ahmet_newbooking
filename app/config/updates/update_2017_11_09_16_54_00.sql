
START TRANSACTION;


INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_email_rating_subject', 3, '', NULL, 'string', 25, 1, NULL),
(1, 'o_email_rating_message', 3, '', NULL, 'text', 26, 1, NULL);

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w220">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w220"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{ReturnNotes}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b></div><div class="float_left w220">{PaymentMethod}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";

INSERT INTO `fields` VALUES (NULL, 'lblPricesLoadByDefault', 'backend', 'Label / Prices load by default', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prices load by default', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_pickup_address', 'frontend', 'Label / Pick-up address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up address', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_dropoff_address', 'frontend', 'Label / Drop-off address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drop-off address', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPickupAddress', 'frontend', 'Label / Pick-up address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up address', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDropAddress', 'frontend', 'Label / Drop-off address', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drop-off address', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_rating_subject', 'backend', 'Options / Rating email subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rating email subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_rating_message', 'backend', 'Options / Rating email message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rating email message', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_rating_subject', 'Rating email', 'data');
INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_rating_message', '<p style="text-align: left;">Rating email.</p>', 'data');

INSERT INTO `fields` VALUES (NULL, 'lblSendRatingEmail', 'backend', 'Options / Rating email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Rating Email to the Client', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB13', 'arrays', 'error_titles_ARRAY_AB13', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rating email sent', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB13', 'arrays', 'error_bodies_ARRAY_AB13', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rating email has been sent successfully to the client.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB14', 'arrays', 'error_titles_ARRAY_AB14', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rating email not sent', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB14', 'arrays', 'error_bodies_ARRAY_AB14', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry that the rating email could not be sent successfully.', 'script');



COMMIT;