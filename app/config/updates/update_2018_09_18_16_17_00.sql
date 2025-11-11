START TRANSACTION;

  ALTER TABLE `locations` ADD `icon` VARCHAR(255) NULL AFTER `status`;
  ALTER TABLE `dropoff` ADD `icon` VARCHAR(255) NULL AFTER `is_airport`;

  INSERT INTO `fields` VALUES (NULL, 'lblIconAirport', 'backend', 'Label / Icon airport', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Airport', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblIconTrain', 'backend', 'Label / Icon train station', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Train station', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblIconCity', 'backend', 'Label / Icon city', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'City', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblIconSkiing', 'backend', 'Label / Icon skiing', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Skiing', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblIcon', 'backend', 'Label / Icon', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Icon', 'script');

  INSERT INTO `fields` VALUES (NULL, 'menuInstall2', 'backend', 'Menu / Install 2', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install 2', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblStartingPoint', 'backend', 'Label / Starting point', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'What is your starting point?', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblYourDestination', 'backend', 'Label / Your destination', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'What is your destination?', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblYourDate', 'backend', 'Label / Date', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblSeePrices', 'backend', 'Label / See prices', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'See prices', 'script');

  INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
    (1, 'o_site_url', 1, NULL, NULL, 'string', 16, 1, NULL);

  INSERT INTO `fields` VALUES (NULL, 'opt_o_site_url', 'backend', 'Options / Site URL', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Site URL', 'script');

COMMIT;