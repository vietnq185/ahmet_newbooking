START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN (
	`saferpay_request_id` varchar(255) DEFAULT NULL,
	`saferpay_token` varchar(255) DEFAULT NULL
);


COMMIT;