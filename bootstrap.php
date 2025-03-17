<?php
/* Core requirements */
require_once 'vendor/autoload.php';

use Game\System\System;
$system = new System(0);

define('PATH_WEBROOT',   '/var/www/html/kali.local/loa');
define('PATH_ADMINROOT', PATH_WEBROOT . '/admini/strator');
define('WEB_ADMINROOT', '/admin/25654');

/* Funcs etc */
require_once PATH_WEBROOT . '/logger.php';
require_once PATH_WEBROOT . '/db.php';
require_once PATH_WEBROOT . '/functions.php';
require_once PATH_WEBROOT . '/mailer.php';
require_once PATH_WEBROOT . '/constants.php';

/* .env */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();