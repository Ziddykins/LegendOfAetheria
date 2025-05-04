<?php
declare(strict_types = 1);

require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->safeLoad();

require_once 'constants.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'logger.php';

if (!isset($argv)) {
    $log->warning('Access to cron.php directly is not allowed!', ['POST' => print_r($_POST, true)]);
    echo 'Access to initdb.php directly is not allowed!';
    exit;
}

$create_users = 'CREATE TABLE IF NOT EXISTS `tbl_users` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `company_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                    `ip_address` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `first_name` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `last_name` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `title` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `email` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `phone` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `password` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `date_registered` DATETIME NULL DEFAULT NULL,
                    `logged_in` ENUM("True","False") NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `last_seen` DATETIME NULL DEFAULT NULL,
                    `avatar` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `user_name` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `verification_code` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `verified` ENUM("True","False") NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `first_login` DATETIME NULL DEFAULT NULL,
                    `last_login` DATETIME NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                    `privileges` INT(11) NULL DEFAULT "4",
                    `theme` ENUM("dark","light") NULL DEFAULT "dark" COLLATE "utf8mb4_general_ci",
                    PRIMARY KEY (`id`) USING BTREE
                )
                COLLATE="utf8mb4_general_ci"
                ENGINE=InnoDB;';

$create_company = 'CREATE TABLE `tbl_companies` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `account_id` INT NULL,
                    `name` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `short_form` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `primary_contact` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `country` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `province` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `city` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `address` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    `phone` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                    PRIMARY KEY (`id`) USING BTREE
                )
                COLLATE="utf8mb4_general_ci"
                ENGINE=InnoDB';

$create_whitelabel = 'CREATE TABLE `tbl_whitelabel` (
                        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `account_id` INT UNSIGNED NULL,
                        `company_id` INT UNSIGNED NULL,
                        `logo_small` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                        `logo_large` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                        `logo_banner` VARCHAR(50) NULL DEFAULT NULL COLLATE "utf8mb4_general_ci",
                        PRIMARY KEY (`id`) USING BTREE
                    )
                    COLLATE="utf8mb4_general_ci"
                    ENGINE=InnoDB;';

$db->execute_query($create_company);
echo 'done';
$db->execute_query($create_users);
echo 'done';
$db->execute_query($create_whitelabel);
echo 'done';