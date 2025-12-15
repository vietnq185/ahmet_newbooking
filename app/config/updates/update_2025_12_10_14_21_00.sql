START TRANSACTION;

 ALTER TABLE `bookings` ADD COLUMN `is_run_update` tinyint(1) DEFAULT '0';


COMMIT;