<?php

use Game\Account\Account;
use Game\Character\Character;
    require_once SYSTEM_DIRECTORY . '/bootstrap.php';
    $account = new Account($_SESSION['email']);
    $account->set_sessionID(null);
    $character = new Character($_SESSION['account-id'], $_SESSION['character-id']);
    $character->set_lastAction(date("Y-m-d H:i:s", strtotime("now")));
    
    
    $_SESSION = [];
    
    setcookie(session_name(), '', time() - 100, '/', $_SERVER['HTTP_HOST'], true, true);
    session_destroy();
    header('Location: /?logged_out');
    exit();
?>