<?php
    session_start();
    unset($_SESSION);
    setcookie(session_name(), '', 100);
    session_destroy();
?>