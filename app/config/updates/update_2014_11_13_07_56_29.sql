
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_on");

UPDATE `multi_lang` SET `content` = 'Returning on' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;