<?php
    session_start();

    if (file_exists('/var/lib/php/sessions/sess_' . session_id())) {
        unlink('/var/lib/php/sessions/sess_' . session_id());
    }

    $_SESSION = [];
    
    setcookie(session_name(), '', time() - 100);
    session_destroy();
    header('Location: /?logged_out');
    exit();
?>