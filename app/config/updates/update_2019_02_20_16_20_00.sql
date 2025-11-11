START TRANSACTION;

  INSERT INTO `fields` VALUES (NULL, 'lblGridRemindClientForReturnViaEmail', 'backend', 'Label / Remind Client for Return Transfer', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind Client for Return Transfer', 'script');

  INSERT INTO `fields` VALUES (NULL, 'lblGridRemindClientViaEmail', 'backend', 'Label / Remind Client for Transfer', 'script', NULL);
  SET @id := (SELECT LAST_INSERT_ID());
  INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remind Client for Transfer', 'script');

COMMIT;