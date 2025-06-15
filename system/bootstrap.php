<?php
declare(strict_types=1);

/* Core requirements */
require_once WEBROOT . '/vendor/autoload.php';

use Game\System\System;

$system = new System(0);
require_once SYSTEM_DIRECTORY . '/constants.php';

/* Funcs etc */

require_once SYSTEM_DIRECTORY . '/db.php';
require_once WEBROOT . '/functions.php';
require_once WEBROOT . '/mailer.php';
require_once SYSTEM_DIRECTORY . '/logger.php';
/* .env */
$dotenv = Dotenv\Dotenv::createImmutable(WEBROOT);
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
$t['bank']       = $_ENV['SQL_BANK_TBL'];

if ($_SERVER['SCRIPT_NAME'] !== '/index.php' && $_SERVER['SCRIPT_NAME'] !== '/system/cron.php') {
    if (check_session() === true) {
        // Session timeout check - 30 minutes

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_unset();
            session_destroy();
            header('Location: /?session_expired');
            exit();
        }

        $_SESSION['last_activity'] = time();

        // CSRF protection
        if (!isset($_SESSION['csrf-token'])) {
            $_SESSION['csrf-token'] = gen_csrf_token();
        }

        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
    } else {
        header('Location: /?no_login');
        exit();
    }
}