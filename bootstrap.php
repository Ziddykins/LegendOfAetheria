<?php
    declare(strict_types = 1);
    session_start();
    /* Core requirements */
    require_once 'vendor/autoload.php';

    use Game\System\System;
    $system = new System(0);

    define('PATH_WEBROOT',   '/var/www/html/kali.local/loa');
    define('PATH_ADMINROOT', PATH_WEBROOT . '/admini/strator');
    define('WEB_ADMINROOT', '/admini/strator');

    /* Funcs etc */
    require_once PATH_WEBROOT . '/logger.php';
    require_once PATH_WEBROOT . '/db.php';
    require_once PATH_WEBROOT . '/functions.php';
    require_once PATH_WEBROOT . '/mailer.php';
    require_once PATH_WEBROOT . '/constants.php';

    /* .env */
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    if ($_SERVER['SCRIPT_NAME'] !== '/index.php' && $_SERVER['SCRIPT_NAME'] !== '/cron.php') {
        if (check_session() === true) {
            if (!isset($_SESSION['csrf-token'])) {
                $_SESSION['csrf-token'] = gen_csrf_token();
            }
        } else {
            header('Location: /?no_login');
            exit();
        }
    }