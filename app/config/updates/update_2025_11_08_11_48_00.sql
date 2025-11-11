START TRANSACTION;

ALTER TABLE `fleets` ADD COLUMN (
	`return_discount_1_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_2_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_3_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_4_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_5_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_6_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_7_2` decimal(9,2) DEFAULT NULL
);

ALTER TABLE `fleets_discounts` ADD COLUMN `price_level` tinyint(1) DEFAULT '1';

ALTER TABLE `dropoff` ADD COLUMN `price_level` tinyint(1) DEFAULT '1';

INSERT INTO `fields` VALUES (NULL, 'lblDefaultSeasonalPrices', 'backend', 'Label / Default seasonal prices', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default seasonal prices', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPriceLevel2', 'backend', 'Label / Price level 2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price level 2', 'script');

INSERT INTO `fields` VALUES (NULL, '_price_levels_ARRAY_1', 'arrays', '_price_levels_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default price', 'script');

INSERT INTO `fields` VALUES (NULL, '_price_levels_ARRAY_2', 'arrays', '_price_levels_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price level 2', 'script');


COMMIT;