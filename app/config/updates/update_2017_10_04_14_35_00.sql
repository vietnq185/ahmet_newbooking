
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_email_return_reminder', 3, '0|1::1', 'No|Yes', 'enum', 22, 1, NULL),
  (1, 'o_email_return_reminder_subject', 3, '', NULL, 'string', 23, 1, NULL),
  (1, 'o_email_return_reminder_message', 3, '', NULL, 'text', 24, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_return_reminder', 'backend', 'Options / Return transfer reminder email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return transfer reminder', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_return_reminder_subject', 'backend', 'Options / Return transfer reminder email subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_return_reminder_message', 'backend', 'Options / Return transfer reminder email message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_return_reminder_text', 'backend', 'Options / Return transfer reminder email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select ''Yes'' if you want a reminder to be sent to clients for their return transfers.', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_return_reminder_subject', 'Return transfer reminder', 'data');
INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_email_return_reminder_message', '<p style="text-align: left;">Return transfer reminder.</p><p style="text-align: left;">&nbsp;</p><p style="text-align: left;">Details are below:</p><p style="text-align: left;">&nbsp;</p><p style="text-align: left;"><strong>Personal details:</strong></p><p style="text-align: left;">Name: {Title} {FirstName} {LastName}</p><p style="text-align: left;">Phone: {Phone}</p><p style="text-align: left;">&nbsp;</p><p style="text-align: left;"><strong>Reservation details:</strong></p><p style="text-align: left;">Transfer date/time:&nbsp;{ReturnDate}, {ReturnTime}</p><p style="text-align: left;">From: {ReturnFrom}</p><p style="text-align: left;">To: {ReturnTo}</p><p style="text-align: left;">Fleet: {Fleet}</p><p style="text-align: left;">Passengers: {Passengers}</p><p style="text-align: left;">&nbsp;</p><p style="text-align: left;">Extras: {Extras}</p><p style="text-align: left;">&nbsp;</p><p style="text-align: left;">Thank you!</p>', 'data');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
  (1, 'o_sms_return_reminder_message', 3, '', NULL, 'text', 4, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_sms_return_reminder_message', 'backend', 'Options / Return transfer reminder SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return transfer reminder SMS', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_sms_return_reminder_message_text', 'backend', 'Options / Return transfer reminder SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can also send personalized SMS notifications via the each booking page.<br/>Available Tokens:<br/><br/>{FirstName}<br/>{LastName}<br/>{UniqueID}<br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}', 'script');

INSERT INTO `multi_lang` VALUES (NULL, 1, 'pjOption', '::LOCALE::', 'o_sms_return_reminder_message', NULL, 'data');

INSERT INTO `fields` VALUES (NULL, 'lblRemindClientForReturnViaEmail', 'backend', 'Label / Remind the Client for the Return trip via Email', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind the Client for the Return trip via Email', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblRemindClientForReturnViaSMS', 'backend', 'Label / Remind the Client for the Return trip via SMS', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind the Client for the Return trip via SMS', 'script');

COMMIT;