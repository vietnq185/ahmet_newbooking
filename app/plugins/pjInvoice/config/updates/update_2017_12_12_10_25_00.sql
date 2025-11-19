
START TRANSACTION;

ALTER TABLE `plugin_invoice_config` ADD COLUMN `p_accept_bambora` tinyint(1) unsigned DEFAULT '0' AFTER `p_accept_paypal`;
ALTER TABLE `plugin_invoice_config` ADD COLUMN `p_bambora_merchant_id` varchar(255) DEFAULT NULL AFTER `p_accept_bank`;
ALTER TABLE `plugin_invoice_config` ADD COLUMN `p_bambora_hash_algorithm` varchar(255) DEFAULT NULL AFTER `p_bambora_merchant_id`;
ALTER TABLE `plugin_invoice_config` ADD COLUMN `p_bambora_hash` varchar(255) DEFAULT NULL AFTER `p_bambora_hash_algorithm`;

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
  (NULL, 'plugin_invoice_i_accept_bambora', 'backend', 'Invoice plugin / Accept payments with Bambora', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
  (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Accept payments with Bambora', 'plugin');

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
  (NULL, 'plugin_invoice_pay_with_bambora', 'backend', 'Invoice plugin / Pay with Bambora', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
  (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pay with Bambora', 'plugin');

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
  (NULL, 'plugin_invoice_bambora_redirect', 'backend', 'Invoice plugin / Bambora redirect', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
  (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You will be redirected to Bambora in 3 seconds. If not please click on the button.', 'plugin');

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
  (NULL, 'plugin_invoice_bambora_proceed', 'backend', 'Invoice plugin / Bambora proceed button', 'plugin', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
  (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Proceed with payment', 'plugin');

COMMIT;