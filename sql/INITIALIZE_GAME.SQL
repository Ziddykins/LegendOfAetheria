CREATE DATABASE db_loa;
USE db_loa;

GRANT ALL PRIVILEGES ON db_loa.* TO user_loa IDENTIFIED BY 'G4m3th1n6!';
FLUSH PRIVILEGES;

CREATE TABLE `tbl_accounts` (
	`id` INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(256) NULL DEFAULT NULL,
	`password` VARCHAR(64) NULL DEFAULT NULL,
	`date_registered` DATETIME NOT NULL DEFAULT CURDATE(),
	`verified` ENUM('True', 'False') NOT NULL DEFAULT 'False',
	`verification_code` VARCHAR(40) NULL DEFAULT NULL,
	`banned` ENUM('True', 'False') NOT NULL DEFAULT 'False',
	`muted` ENUM('True', 'False') NOT NULL DEFAULT 'False',
	`privileges` SET(
        'BANNED',
		'MUTED',
		'UNREGISTERED',
		'UNVERIFIED',
        'USER',        
		'MODERATOR',
		'SUPER_MODERATOR',
        'GLOBAL_ADMINISTRATOR',
		'ADMINISTRATOR',
		'OWNER',
		'ROOTED'
	) NOT NULL DEFAULT 'UNREGISTERED',
	`last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	`logged_in` ENUM('True', 'False') NOT NULL DEFAULT 'False',
	`failed_logins` INT UNSIGNED NOT NULL DEFAULT '0',
	`ip_address` TINYTEXT NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

CREATE TABLE `tbl_characters` (
	`id` INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
	`account_id` INT(9) NOT NULL,
	`name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`race` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`avatar` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`str` BIGINT(20) NOT NULL,
	`int` BIGINT(20) NOT NULL,
	`def` BIGINT(20) NOT NULL,
	`x` INT(9) NOT NULL DEFAULT '0',
	`y` INT(9) NOT NULL DEFAULT '0',
	`location` VARCHAR(50) NOT NULL DEFAULT 'The Shrine' COLLATE 'utf8mb4_general_ci',
	`hp` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100',
	`max_hp` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100',
	`mp` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100',
	`max_mp` BIGINT(20) UNSIGNED NOT NULL DEFAULT '100',
	`alignment` INT(9) NOT NULL DEFAULT '0',
	`gold` BIGINT(20) UNSIGNED NOT NULL DEFAULT '1000',
	`exp` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`ep` INT(9) UNSIGNED NOT NULL DEFAULT '100',
	`max_ep` INT(9) UNSIGNED NOT NULL DEFAULT '100',
	`floor` INT(9) UNSIGNED NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `account_id` (`account_id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

-- Don't like this
CREATE TABLE `tbl_globals` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`value` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;

INSERT INTO tbl_globals (id, name, value) VALUES (1, 'weather',   'Raining');
INSERT INTO tbl_globals (id, name, value) VALUES (2, 'epoch',     '1692894200');
INSERT INTO tbl_globals (id, name, value) VALUES (3, 'next_tick', '1692894972');
INSERT INTO tbl_globals (id, name, value) VALUES (4, 'exp_multiplier', '1');
INSERT INTO tbl_globals (id, name, value) VALUES (5, 'gold_multiplier', '1');

CREATE TABLE `tbl_mail` (
	`id` INT(9) UNSIGNED NOT NULL,
	`account_id` INT(9) UNSIGNED NOT NULL,
	`folder` ENUM('Inbox','Outbox','Deleted') NOT NULL COLLATE 'utf8mb4_general_ci',
	`to` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`from` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`subject` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`message` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`date` DATETIME NOT NULL,
	`read` ENUM('True','False') NOT NULL DEFAULT 'False' COLLATE 'utf8mb4_general_ci',
	`important` ENUM('True','False') NULL DEFAULT 'False' COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `account_id` (`account_id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;