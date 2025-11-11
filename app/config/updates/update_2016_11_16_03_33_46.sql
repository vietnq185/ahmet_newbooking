
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_email_forgot_subject', 3, '', NULL, 'string', 17, 1, NULL),
(1, 'o_email_forgot_message', 3, '', NULL, 'text', 18, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_forgot_subject', 'backend', 'Options / Subject', 'script', '2016-11-16 02:54:20');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password recovery email subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_forgot_message', 'backend', 'Options / Subject', 'script', '2016-11-16 02:54:31');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password recovery email message', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_forgot_message_text', 'backend', 'Options / Account tokens', 'script', '2016-11-02 06:03:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available Tokens:<br/><br/>{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_forgot_password', 'frontend', 'Label / Forgot password?', 'script', '2016-11-16 02:51:18');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Forgot password?', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_send', 'frontend', 'Button / Send', 'script', '2016-11-16 03:12:14');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_password_sent', 'frontend', 'Label / The password has been sent to your email address.', 'script', '2016-11-16 03:31:04');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The password has been sent to your email address.', 'script');

COMMIT;