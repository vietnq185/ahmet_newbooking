START TRANSACTION;

ALTER TABLE `clients` ADD COLUMN `modified` datetime DEFAULT NULL AFTER `created`;

ALTER TABLE `drivers` ADD COLUMN `modified` datetime DEFAULT NULL AFTER `created`;

ALTER TABLE `fleets` ADD COLUMN `modified` datetime DEFAULT NULL;

ALTER TABLE `extras` ADD COLUMN `modified` datetime DEFAULT NULL;

ALTER TABLE `locations` ADD COLUMN `modified` datetime DEFAULT NULL;

ALTER TABLE `prices` ADD COLUMN `modified` datetime DEFAULT NULL;

ALTER TABLE `vouchers` ADD COLUMN `modified` datetime DEFAULT NULL;



COMMIT;