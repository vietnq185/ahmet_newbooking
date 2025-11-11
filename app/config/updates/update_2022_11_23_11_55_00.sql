START TRANSACTION;

ALTER TABLE `extras` ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `price`;

INSERT INTO `fields` VALUES (NULL, 'lblExtraImage', 'backend', 'Label / Image', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Image', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_free_cancellation_desc_1', 'frontend', 'Info / Free cancellation desc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'up to 24 hours before your transfer: Book today, lock the price.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_label_done', 'frontend', 'Label / Done', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Done', 'script');
INSERT INTO `fields` VALUES (NULL, 'menuReturnExtras', 'backend', 'Label / Return extras', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_9', 'arrays', 'front_messages_ARRAY_9', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to make the payment. Please check your credit card information, or choose another payment method and try again!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_messages_ARRAY_10', 'arrays', 'front_messages_ARRAY_10', 'script', '2021-12-07 11:28:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your booking has been successfully booked and paid!', 'script');


COMMIT;