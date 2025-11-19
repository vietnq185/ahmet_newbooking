START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `paid_via_payment_link` tinyint(1) DEFAULT '0';


COMMIT;