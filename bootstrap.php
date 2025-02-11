<?php
/* Core requirements */
require_once 'vendor/autoload.php';

/* Classes */
use Game\Account\Enums\Privileges;
use Game\Traits\PropSync;
use Game\Account\Account;
use Game\Character\Character;
use Game\Abuse\Enums\Type;

/* Funcs etc */
require_once 'logger.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'mailer.php';
require_once 'constants.php';

/* .env */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

const ROOT_WEB_DIR = __DIR__;
const ROOT_ADMIN_DIR = __DIR__ . '/admin/25654';