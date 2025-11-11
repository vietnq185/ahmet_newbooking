START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_button_price_inquiry', 'frontend', 'Button / Price Inquiry', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price Inquiry', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_price_inquiry_text', 'frontend', 'Label / Price Inquiry text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We are sorry the price calculation is not possible. Please follow the Price Inquiry for more individual offers.', 'script');


COMMIT;