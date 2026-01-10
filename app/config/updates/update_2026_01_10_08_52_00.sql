START TRANSACTION;


INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_email_send_pdf', 3, '0|1::1', 'No|Yes',  'enum', 14, 0, NULL),
(1, 'o_email_send_pdf_subject', 3, '', NULL, 'string', 15, 1, NULL),
(1, 'o_email_send_pdf_message', 3, '', NULL, 'text', 16, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'opt_o_email_send_pdf', 'backend', 'Label / Send PDF email', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send PDF email', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_send_pdf_subject', 'backend', 'Label / Subject', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send PDF Email Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_send_pdf_message', 'backend', 'Label / Message body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send PDF Email Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSendPdfInvoice', 'backend', 'Label / Send PDF invoice to Client', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send PDF invoice to Client', 'script');


INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB48', 'arrays', 'error_titles_ARRAY_AB48', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email Sent Successfully', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB48', 'arrays', 'error_bodies_ARRAY_AB48', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your PDF invoice has been delivered to the client.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AB49', 'arrays', 'error_titles_ARRAY_AB49', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Error Sending Email', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AB49', 'arrays', 'error_bodies_ARRAY_AB49', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We couldn''t send the invoice.', 'script');



COMMIT;