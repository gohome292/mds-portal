DROP TABLE IF EXISTS `information`;
CREATE TABLE IF NOT EXISTS `information` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_organization_id` int(10) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `regular` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(40) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_user_id` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `information` ADD INDEX (`customer_organization_id`);



DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `year_month` varchar(6) DEFAULT NULL,
  `customer_organization_id` int(10) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_user_id` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `documents` ADD INDEX (`customer_organization_id`);



DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_organization_id` int(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_user_id` int(10) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `equipment` ADD INDEX (`customer_organization_id`);



DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE IF NOT EXISTS `mail_templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_organization_id` int(10) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `body` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `mail_templates` ADD INDEX (`customer_organization_id`);



DROP TABLE IF EXISTS `mail_informations`;
CREATE TABLE IF NOT EXISTS `mail_informations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mail_history_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `forecast_end_time` varchar(20) DEFAULT NULL,
  `mail_count` int(10) NOT NULL DEFAULT 0,
  `confirm_date` datetime DEFAULT NULL,
  `lock_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `mail_histories`;
CREATE TABLE IF NOT EXISTS `mail_histories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_organization_id` int(10) DEFAULT NULL,
  `send_start_date` datetime DEFAULT NULL,
  `send_end_date` datetime DEFAULT NULL,
  `send_order_count` int(10) NOT NULL DEFAULT 0,
  `success_count` int(10) NOT NULL DEFAULT 0,
  `failure_count` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `mail_histories` ADD INDEX (`customer_organization_id`);



DROP TABLE IF EXISTS `mail_history_details`;
CREATE TABLE IF NOT EXISTS `mail_history_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mail_history_id` int(10) DEFAULT NULL,
  `customer_organization_path` varchar(250) DEFAULT NULL,
  `user_name` varchar(20) DEFAULT NULL,
  `from` varchar(80) DEFAULT NULL,
  `to` varchar(80) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `send_date` datetime DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `mail_history_details` ADD INDEX (`mail_history_id`);



DROP TABLE IF EXISTS `mail_plans`;
CREATE TABLE IF NOT EXISTS `mail_plans` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_organization_path` varchar(250) DEFAULT NULL,
  `user_name` varchar(20) DEFAULT NULL,
  `from` varchar(80) DEFAULT NULL,
  `to` varchar(80) DEFAULT NULL,
  `title` varchar(80) DEFAULT NULL,
  `body` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `customer_organizations`;
CREATE TABLE IF NOT EXISTS `customer_organizations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `top_parent_id` int(10) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `customer_organizations` ADD INDEX (`parent_id`);
ALTER TABLE `customer_organizations` ADD INDEX (`top_parent_id`);



DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `group_id` int(10) DEFAULT NULL,
  `customer_organization_id` int(10) DEFAULT NULL,
  `top_customer_organization_id` int(10) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `company_name_for_mail` varchar(100) DEFAULT NULL,
  `person_name_for_mail` varchar(100) DEFAULT NULL,
  `contact_address` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `freeword1` varchar(100) DEFAULT NULL,
  `freeword2` varchar(100) DEFAULT NULL,
  `sendmail` tinyint(1) NOT NULL DEFAULT 0,
  `login` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  `access_information` datetime DEFAULT NULL,
  `access_documents` datetime DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `users` ADD INDEX (`group_id`);
ALTER TABLE `users` ADD INDEX (`customer_organization_id`);
ALTER TABLE `users` ADD INDEX (`top_customer_organization_id`);



DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` varchar(36) NOT NULL,
  `model` varchar(20) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `basename` varchar(40) DEFAULT NULL,
  `extension` varchar(5) DEFAULT NULL,
  `size` int(10) DEFAULT NULL,
  `alternative` varchar(40) DEFAULT NULL,
  `identifier` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `link` varchar(100) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `groups` varchar(50) DEFAULT NULL,
  `users` varchar(50) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `acos`;
CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(10) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `aros`;
CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) DEFAULT NULL,
  `aco_id` int(10) DEFAULT NULL,
  `_create` varchar(2) DEFAULT NULL,
  `_read` varchar(2) DEFAULT NULL,
  `_update` varchar(2) DEFAULT NULL,
  `_delete` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
