CREATE DATABASE db_gamething;
USE db_gamething;

GRANT ALL PRIVILEGES ON db_gamething.* TO user_gamething IDENTIFIED BY 'G4m3th1n6!';
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
        'UNREGISTERED',
        'OWNER',
        'GLOBAL ADMINISTRATOR',
        'ADMINISTRATOR',
        'SUPER MODERATOR',
        'MODERATOR',
        'VERIFIED USER',
        'USER',
        'MUTED',
        'BANNED') NOT NULL DEFAULT 'UNREGISTERED',
	`last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	`logged_in` ENUM('True', 'False') NOT NULL DEFAULT 'False',
	`failed_logins` INT UNSIGNED NOT NULL DEFAULT '0',
	`ip_address` TINYTEXT NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) COLLATE='utf8mb4_general_ci';