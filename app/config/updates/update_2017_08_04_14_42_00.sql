
START TRANSACTION;

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <br/><br/><div class="float_left w220">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Country}<br/><br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/><br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Hotel}<br/>{Notes}<br/>{Extras}</div><div class="float_left w220"><b>[FromAirport]</b><br/>{FlightNumber}<br/>{AirlineCompany}<br/>{DestinationAddress}<br/><b>[/FromAirport]</b><br/><br/><b>[FromLocation]</b><br/>{Address}<br/>{FlightDepartureTime}<br/><b>[/FromLocation]</b><br/><br/><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{ReturnNotes}<br/><b>[/HasReturn]</b><br/><br/><b>[ReturnToAirport]</b><br/>{ReturnAddress}<br/>{ReturnFlightDepartureTime}<br/><b>[/ReturnToAirport]</b><br/><br/><b>[ReturnToLocation]</b><br/>{ReturnFlightNumber}<br/>{ReturnAirlineCompany}<br/><b>[/ReturnToLocation]</b></div><div class="float_left w220">{PaymentMethod}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/><br/><b>[HasDeposit]</b><br/>{Deposit}<br/>{Rest}<br/>{CCOwner}<br/>{CCNum}<br/>{CCExp}<br/>{CCSec}<br/><b>[/HasDeposit]</b><br/><br/><b>[HasDiscount]</b><br/>{DiscountCode}<br/>{Discount}<br/><b>[/HasDiscount]</b><br/><br/>{CancelURL}</div>' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";

SET @id1 := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_sms_confirmation_message_text");
SET @id2 := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_sms_reminder_message_text");
UPDATE `multi_lang` SET `content` = 'You can also send personalized SMS notifications via the each booking page.<br/>Available Tokens:<br/><br/>{FirstName}<br/>{LastName}<br/>{UniqueID}<br/>{Date}<br/>{Time}<br/>{From}<br/>{To}<br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}' WHERE `foreign_id` IN (@id1, @id2) AND `model` = "pjField" AND `field` = "title";

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_email_sender', 1, 'Company', NULL, 'string', 13, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_sender', 'backend', 'Options / Email sender', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email sender name', 'script');

COMMIT;