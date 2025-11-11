START TRANSACTION;



INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_email_payment_link_confirmation', 3, '0|1::1', 'No|Yes', 'enum', 30, 0, NULL),
(1, 'o_email_payment_link_subject', 3, '', NULL, 'string', 31, 1, NULL),
(1, 'o_email_payment_link_message', 3, '', NULL, 'text', 32, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'opt_o_email_payment_link', 'backend', 'Options / Payment link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment link', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_payment_link_text', 'backend', 'Options / Payment link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want to send payment link to the customers.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_payment_link_subject', 'backend', 'Options / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send payment link email subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_payment_link_message', 'backend', 'Options / Message body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send payment link email message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSendPaymentLink', 'backend', 'Label / Send payment link', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Payment Link to the Client', 'script');



SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w400"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[FromLocationToLocation]</b><br/>{Address}<br/>{DropoffAddress}<br/><b>[/FromLocationToLocation]</b><br/><br/>{PriceFirstTransfer}<br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PriceReturnTransfer}<br/>{ReturnNotes}<br/>{ReturnExtras}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b><br/><br/><b>[ReturnToLocationToLocation]</b><br/>{ReturnAddress}<br/>{ReturnDropoffAddress}<br/><b>[/ReturnToLocationToLocation]</b></div><div class="float_left w400">{PaymentMethod}<br/>{StationFee}<br/>{ExtraPrice}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{PaymentURL}<br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";


COMMIT;