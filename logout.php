<?php
    
    $_SESSION = [];
    
    setcookie(session_name(), '', time() - 100, '/', $_SERVER['HTTP_HOST'], true, true);
    session_destroy();
    header('Location: /?logged_out');
    exit();
?>