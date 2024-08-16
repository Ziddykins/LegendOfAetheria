<?php
    session_start();

    # FIXME: This doesn't work. Need to save session ID for user in SQL db and fill it in from here
    if (file_exists('/var/lib/php/sessions/sess_' . session_id())) {
        unlink('/var/lib/php/sessions/sess_' . session_id());
    }

    $_SESSION = [];
    
    setcookie(session_name(), '', time() - 100, '/', $_SERVER['HTTP_HOST'], true, true);
    session_destroy();
    header('Location: /?logged_out');
    exit();
?>