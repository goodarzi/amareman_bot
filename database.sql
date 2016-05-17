SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS  `user` (
	`id` bigint(20) DEFAULT NULL COMMENT 'Unique user identifier',
	`uniqid` char(13) NOT NULL COMMENT 'system uniqid',
	`first_name` char(255) NOT NULL DEFAULT '' COMMENT 'User first name',
	`last_name` char(255) DEFAULT NULL COMMENT 'User last name',
	`username` char(255) DEFAULT NULL COMMENT 'User username',
	`command` CHAR(50) DEFAULT NULL,
	`command_level` CHAR(20) DEFAULT NULL,
	`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Entry date creation',
	`updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
	PRIMARY KEY (`id`),
	UNIQUE KEY `uniqid` (`uniqid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS  `vote_type` (
	`id` SMALLINT unsigned AUTO_INCREMENT COMMENT 'Row unique id',
	`name` CHAR(255) DEFAULT NULL,
	`description` CHAR(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS  `vote` (
	`id` bigint(20) unsigned AUTO_INCREMENT COMMENT 'Row unique id',
	`user_id` bigint(20) COMMENT 'Unique user identifier',
	`vote_type_id` SMALLINT unsigned,
	`from_user_id` bigint(20),
  
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `from_user_id` (`from_user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`vote_type_id`) REFERENCES `vote_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

