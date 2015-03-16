


# Dump of table azure_vm
# ------------------------------------------------------------

DROP TABLE IF EXISTS `azure_vm`;

CREATE TABLE `azure_vm` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `externalport` int(10) NOT NULL,
  `os` varchar(50) NOT NULL,
  `cloudservice` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table cloudservices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cloudservices`;

CREATE TABLE `cloudservices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table consumer_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `consumer_info`;

CREATE TABLE `consumer_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lti_consumer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lti_consumer`;

CREATE TABLE `lti_consumer` (
  `consumer_key` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `lti_version` varchar(12) DEFAULT NULL,
  `consumer_name` varchar(255) DEFAULT NULL,
  `consumer_version` varchar(255) DEFAULT NULL,
  `consumer_guid` varchar(255) DEFAULT NULL,
  `css_path` varchar(255) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `enable_from` datetime DEFAULT NULL,
  `enable_until` datetime DEFAULT NULL,
  `last_access` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table lti_context
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lti_context`;

CREATE TABLE `lti_context` (
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  `lti_context_id` varchar(255) DEFAULT NULL,
  `lti_resource_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `settings` text,
  `primary_consumer_key` varchar(255) DEFAULT NULL,
  `primary_context_id` varchar(255) DEFAULT NULL,
  `share_approved` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`,`context_id`),
  KEY `lti_context_context_FK1` (`primary_consumer_key`,`primary_context_id`),
  CONSTRAINT `lti_context_consumer_FK1` FOREIGN KEY (`consumer_key`) REFERENCES `lti_consumer` (`consumer_key`),
  CONSTRAINT `lti_context_context_FK1` FOREIGN KEY (`primary_consumer_key`, `primary_context_id`) REFERENCES `lti_context` (`consumer_key`, `context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table lti_nonce
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lti_nonce`;

CREATE TABLE `lti_nonce` (
  `consumer_key` varchar(255) NOT NULL,
  `value` varchar(32) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`,`value`),
  CONSTRAINT `lti_nonce_consumer_FK1` FOREIGN KEY (`consumer_key`) REFERENCES `lti_consumer` (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table lti_share_key
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lti_share_key`;

CREATE TABLE `lti_share_key` (
  `share_key_id` varchar(32) NOT NULL,
  `primary_consumer_key` varchar(255) NOT NULL,
  `primary_context_id` varchar(255) NOT NULL,
  `auto_approve` tinyint(1) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`share_key_id`),
  KEY `lti_share_key_context_FK1` (`primary_consumer_key`,`primary_context_id`),
  CONSTRAINT `lti_share_key_context_FK1` FOREIGN KEY (`primary_consumer_key`, `primary_context_id`) REFERENCES `lti_context` (`consumer_key`, `context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table lti_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lti_user`;

CREATE TABLE `lti_user` (
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `lti_result_sourcedid` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`,`context_id`,`user_id`),
  CONSTRAINT `lti_user_context_FK1` FOREIGN KEY (`consumer_key`, `context_id`) REFERENCES `lti_context` (`consumer_key`, `context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table os_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `os_images`;

CREATE TABLE `os_images` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `os` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_data`;

CREATE TABLE `user_data` (
  `id` int(10) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `role` varchar(100) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `lti_user_id` varchar(255) NOT NULL DEFAULT '',
  `consumer_info_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_vms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_vms`;

CREATE TABLE `user_vms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) NOT NULL,
  `azure_vm_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
