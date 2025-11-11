START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_btn_book_and_pay', 'frontend', 'Button / Book Now & Pay', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Book Now & Pay', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_btn_finish_your_booking', 'frontend', 'Button / Finish your booking', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Finish your booking', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_select_payment_options_desc', 'frontend', 'Info / Select payment options note', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'If your selected payment option is not working, you can still change to Cash or Credit Card on the day of Transfer.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_payment_title', 'frontend', 'Label / Payment', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');


COMMIT;