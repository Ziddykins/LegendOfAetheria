<?php
// Core requirements
require_once 'vendor/autoload.php';
use Game\Account\Enums\Privileges;
use Game\Traits\PropSync;
use Game\Account\Account;
use Game\Character\Character;
use Game\Abuse\Enums\Type;

require_once 'logger.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'mailer.php';
require_once 'constants.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();