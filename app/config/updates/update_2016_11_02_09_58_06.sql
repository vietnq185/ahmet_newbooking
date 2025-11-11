
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_client_account', 'backend', 'Options / New client account email', 'script', '2016-11-02 05:48:22');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New client account email', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_client_account_text', 'backend', 'Options / New client account email', 'script', '2016-11-02 05:49:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select "Yes" if you want the system to send automatic emails to clients after client accounts are created', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_client_account_subject', 'backend', 'Options / Subject', 'script', '2016-11-02 05:51:28');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_client_account_message', 'backend', 'Options / Subject', 'script', '2016-11-02 05:51:14');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message body', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_email_client_account_message_text', 'backend', 'Options / Account tokens', 'script', '2016-11-02 06:03:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available Tokens:<br/><br/>{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_client_account_text', 'backend', 'Options / New client account email', 'script', '2016-11-02 05:55:37');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select "Yes" if you want the system to send automatic emails to administrator after new client accounts created.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_email_client_account_message_text', 'backend', 'Options / Account tokens', 'script', '2016-11-02 06:03:00');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available Tokens:<br/><br/>{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Password}<br/>{Phone}', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuClients', 'backend', 'Menu / Clients', 'script', '2016-11-02 06:22:31');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Clients', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AC01', 'arrays', 'error_bodies_ARRAY_AC01', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this client have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AC03', 'arrays', 'error_bodies_ARRAY_AC03', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this client have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AC04', 'arrays', 'error_bodies_ARRAY_AC04', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the client has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AC08', 'arrays', 'error_bodies_ARRAY_AC08', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client your looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AC01', 'arrays', 'error_titles_ARRAY_AC01', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AC03', 'arrays', 'error_titles_ARRAY_AC03', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AC04', 'arrays', 'error_titles_ARRAY_AC04', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AC08', 'arrays', 'error_titles_ARRAY_AC08', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddClient', 'backend', 'Label / Add client', 'script', '2016-11-02 06:45:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoClientsTitle', 'backend', 'Infobox / List of clients', 'script', '2016-11-02 06:51:16');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of clients', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoClientsDesc', 'backend', 'Infobox / List of clients', 'script', '2016-11-02 06:52:28');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of clients. You can edit a specific client by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddClientTitle', 'backend', 'Infobox / Add client', 'script', '2016-11-02 06:53:45');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddClientDesc', 'backend', 'Infobox / Add client', 'script', '2016-11-02 06:54:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new client.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateClientTitle', 'backend', 'Infobox / Update', 'script', '2016-11-02 07:01:48');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update client', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateClientDesc', 'backend', 'Infobox / Update', 'script', '2016-11-02 07:02:02');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit client information.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblUpdateClient', 'backend', 'Label / Update client', 'script', '2016-11-02 07:02:27');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update client', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblExistingClient', 'backend', 'Label / Existing client', 'script', '2016-11-02 07:12:34');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Existing client', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNewClient', 'backend', 'Label / New client', 'script', '2016-11-02 07:16:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New client', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblEnquiries', 'backend', 'Label / Enquiries', 'script', '2016-11-02 08:24:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enquiries', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_password', 'frontend', 'Label / Password', 'script', '2016-11-02 08:38:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_new_client', 'frontend', 'Label / New client', 'script', '2016-11-02 08:41:43');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New client', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_existing_client', 'frontend', 'Label / Existing client', 'script', '2016-11-02 08:42:39');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Existing client', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_email_does_not_exist', 'frontend', 'Label / Email does not exist.', 'script', '2016-11-02 09:32:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email does not exist.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_incorrect_password', 'frontend', 'Label / Password is not correct.', 'script', '2016-11-02 09:32:34');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password is not correct.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_your_account_disabled', 'frontend', 'Label / Your account is disabled.', 'script', '2016-11-02 09:32:58');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', ' Your account is disabled.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_already_logged_in', 'frontend', 'Label / You already logged in.', 'script', '2016-11-02 09:49:29');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You already logged in.', 'script');

COMMIT;