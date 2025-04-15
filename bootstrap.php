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
    
    /* tables */
    $t['accounts']   = $_ENV['SQL_ACCT_TBL'];
    $t['characters'] = $_ENV['SQL_CHAR_TBL'];
    $t['familiars']  = $_ENV['SQL_FMLR_TBL'];
    $t['friends']    = $_ENV['SQL_FRND_TBL'];
    $t['chat']       = $_ENV['SQL_CHAT_TBL'];
    $t['globals']    = $_ENV['SQL_GLBL_TBL'];
    $t['logs']       = $_ENV['SQL_LOGS_TBL'];
    $t['mail']       = $_ENV['SQL_MAIL_TBL'];
    $t['banned']     = $_ENV['SQL_BANS_TBL'];
    $t['monsters']   = $_ENV['SQL_MNST_TBL'];
    $t['statistics'] = $_ENV['SQL_STAT_TBL'];

 

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