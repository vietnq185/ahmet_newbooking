
START TRANSACTION;

ALTER TABLE `clients` ADD COLUMN `company` varchar(255) default NULL AFTER `phone`;
ALTER TABLE `clients` ADD COLUMN `address` varchar(255) default NULL AFTER `company`;
ALTER TABLE `clients` ADD COLUMN `city` varchar(255) default NULL AFTER `address`;
ALTER TABLE `clients` ADD COLUMN `state` varchar(255) default NULL AFTER `city`;
ALTER TABLE `clients` ADD COLUMN `zip` varchar(255) default NULL AFTER `state`;
ALTER TABLE `clients` ADD COLUMN `country_id`  int(10) unsigned default NULL AFTER `zip`;

COMMIT;