
START TRANSACTION;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `password` blob,
  `title` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `client_id` int(10) unsigned DEFAULT NULL AFTER `uuid`;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_email_client_account', 3, '0|1::1', 'No|Yes',  'enum', 14, 1, NULL),
(1, 'o_email_client_account_subject', 3, '', NULL, 'string', 15, 1, NULL),
(1, 'o_email_client_account_message', 3, '', NULL, 'text', 16, 1, NULL),

(1, 'o_admin_email_client_account', 3, '0|1::1', 'No|Yes',  'enum', 14, 1, NULL),
(1, 'o_admin_email_client_account_subject', 3, '', NULL, 'string', 15, 1, NULL),
(1, 'o_admin_email_client_account_message', 3, '', NULL, 'text', 16, 1, NULL);

COMMIT;