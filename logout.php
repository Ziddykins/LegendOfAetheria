<?php
    session_start();
    $_SESSION = [];
    setcookie(session_name(), '', time() - 100);
    session_destroy();
    header('Location: /?logged_out');
    exit();
?>