START TRANSACTION;

ALTER TABLE `emails_themes` ADD COLUMN `type` enum('custom','inquiry') DEFAULT 'custom';

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
   `status` enum('T','F') DEFAULT 'T',      
   `domain` varchar(255) DEFAULT NULL,
   `created` datetime default NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `region` varchar(255) DEFAULT NULL;
ALTER TABLE `locations` ADD COLUMN `region` varchar(255) DEFAULT NULL;
ALTER TABLE `dropoff` ADD COLUMN `region` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblTransferRegion', 'backend', 'Label / Region', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Region', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoteName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoteSubject', 'backend', 'Label / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblNoteBody', 'backend', 'Label / Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoNotesTitle', 'backend', 'Infobox / Notes Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of notes', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoNotesBody', 'backend', 'Infobox / Notes Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of notes. You can edit a specific note by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddNoteTitle', 'backend', 'Infobox / Add Note Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add note', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddNoteBody', 'backend', 'Infobox / Add Note Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new note.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateNoteTitle', 'backend', 'Infobox / Update Note Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update note', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateNoteBody', 'backend', 'Infobox / Update Note Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit note.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ANOTE01', 'arrays', 'error_titles_ARRAY_ANOTE01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ANOTE01', 'arrays', 'error_bodies_ARRAY_ANOTE01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this note have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ANOTE02', 'arrays', 'error_titles_ARRAY_ANOTE02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ANOTE02', 'arrays', 'error_bodies_ARRAY_ANOTE02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The note you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ANOTE03', 'arrays', 'error_titles_ARRAY_ANOTE03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ANOTE03', 'arrays', 'error_bodies_ARRAY_ANOTE03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New note has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_ANOTE04', 'arrays', 'error_titles_ARRAY_ANOTE04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Note failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_ANOTE04', 'arrays', 'error_bodies_ARRAY_ANOTE04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the note has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddNote', 'backend', 'Label / Add note', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add note', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSendNote', 'backend', 'Label / Send note', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send note', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabInquiryTemplates', 'backend', 'Label / Inquiry Templates', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry Templates', 'script');


INSERT INTO `fields` VALUES (NULL, 'lblInquiryTemplateName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInquiryTemplateSubject', 'backend', 'Label / Subject', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblInquiryTemplateBody', 'backend', 'Label / Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Body', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoInquiryTemplateTitle', 'backend', 'Infobox / Inquiry Templates Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of inquiry templates', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoInquiryTemplateBody', 'backend', 'Infobox / Inquiry Templates Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can see below the list of inquiry templates. You can edit a specific inquiry templates by clicking on the pencil icon on the corresponding entry.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddInquiryTemplateTitle', 'backend', 'Infobox / Add Inquiry Template Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add inquiry template', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAddInquiryTemplateBody', 'backend', 'Infobox / Add Inquiry Template Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below and click "Save" button to add new inquiry template.<br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/></div><div class="float_left w400">{Date}<br/>{Time}<br/>{From}<br/>{To}<br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Total}<br/></div><div class="float_left w400"><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b></div><br class="clear_both" />', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateInquiryTemplateTitle', 'backend', 'Infobox / Update Inquiry Template Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update inquiry template', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateInquiryTemplateBody', 'backend', 'Infobox / Update Inquiry Template Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can make any changes on the form below and click "Save" button to edit inquiry template.<br/><br/><div class="float_left w400">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/></div><div class="float_left w400">{Date}<br/>{Time}<br/>{From}<br/>{To}<br/>{Passengers}<br/>{Fleet}<br/>{Duration}<br/>{Distance}<br/>{Total}<br/></div><div class="float_left w400"><b>[HasReturn]</b><br/>{ReturnDate}<br/>{ReturnTime}<br/>{ReturnFrom}<br/>{ReturnTo}<br/>{PassengersReturn}<br/><b>[/HasReturn]</b></div><br class="clear_both" />', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AIQT01', 'arrays', 'error_titles_ARRAY_AIQT01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry template updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AIQT01', 'arrays', 'error_bodies_ARRAY_AIQT01', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to this inquiry template have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AIQT02', 'arrays', 'error_titles_ARRAY_AIQT02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry template not found.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AIQT02', 'arrays', 'error_bodies_ARRAY_AIQT02', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oops! The inquiry template you are looking for is missing.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AIQT03', 'arrays', 'error_titles_ARRAY_AIQT03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry template added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AIQT03', 'arrays', 'error_bodies_ARRAY_AIQT03', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New inquiry template has been added to the list.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AIQT04', 'arrays', 'error_titles_ARRAY_AIQT04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry template failed to add.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AIQT04', 'arrays', 'error_bodies_ARRAY_AIQT04', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry, but the inquiry template has not been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAddInquiryTemplate', 'backend', 'Label / Add inquiry template', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add inquiry template', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuInquirygenerator', 'backend', 'Label / Inquiry generator', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry generator', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoInquiryGenaratorTitle', 'backend', 'Label / Inquiry generator title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inquiry generator', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoInquiryGenaratorBody', 'backend', 'Label / Inquiry generator body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This form lets you generate a custom transfer inquiry by selecting your travel date, locations, vehicle, and passenger details. Click Generate Inquiry to receive your quote.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblSelectInquiryTheme', 'backend', 'Label / Select inquiry theme', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select inquiry theme', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnGenerateInquiry', 'backend', 'Label / Generate Inquiry', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Generate Inquiry', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnSendInquiry', 'backend', 'Label / Send Inquiry', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Inquiry', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReturnDate', 'backend', 'Label / Return date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_invalid_time', 'frontend', 'Label / Invalid time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The selected time is invalid. Please choose a time in the future.', 'script');

INSERT INTO `fields` VALUES (NULL, 'tabNotes', 'backend', 'Label / Notes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');


INSERT INTO `fields` VALUES (NULL, 'front_label_first_transfer', 'frontend', 'Label / First Transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'First Transfer', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_label_return_transfer', 'frontend', 'Label / Return Transfer', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return Transfer', 'script');


COMMIT;