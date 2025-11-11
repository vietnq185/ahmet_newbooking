
START TRANSACTION;

ALTER TABLE `fleets` ADD COLUMN `return_discount_7` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_6` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_5` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_4` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_3` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_2` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`,
                     ADD COLUMN `return_discount_1` DECIMAL(9,2) DEFAULT NULL AFTER `passengers`;
UPDATE `fleets` SET `return_discount_1` = `return_discount`,
                    `return_discount_2` = `return_discount`,
                    `return_discount_3` = `return_discount`,
                    `return_discount_4` = `return_discount`,
                    `return_discount_5` = `return_discount`,
                    `return_discount_6` = `return_discount`,
                    `return_discount_7` = `return_discount`
                WHERE `return_discount` IS NOT NULL;
ALTER TABLE `fleets` DROP COLUMN `return_discount`;

ALTER TABLE `prices` ADD COLUMN `price_1` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_2` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_3` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_4` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_5` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_6` DECIMAL(9,2) DEFAULT NULL,
                     ADD COLUMN `price_7` DECIMAL(9,2) DEFAULT NULL;
UPDATE `prices` SET `price_1` = `price`,
                    `price_2` = `price`,
                    `price_3` = `price`,
                    `price_4` = `price`,
                    `price_5` = `price`,
                    `price_6` = `price`,
                    `price_7` = `price`
                WHERE `price` IS NOT NULL;
ALTER TABLE `prices` DROP COLUMN `price`;

INSERT INTO `fields` VALUES (NULL, 'front_btn_choose_date', 'frontend', 'Button / Choose Date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose Date', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_price_not_available', 'frontend', 'Label / Price not available', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'N/A', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_price_not_available_text', 'frontend', 'Label / Price not available text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choosing a date is required for price calculations.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_date_change_message', 'frontend', 'Label / Date change message', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please note that if you change your transfer date the prices may change also.', 'script');

COMMIT;