<?php
    session_start();
<<<<<<< HEAD
    $_SESSION = [];
    setcookie(session_name(), '', time() - 100);
    session_destroy();
    header('Location: /?logged_out');
    exit();
=======
    unset($_SESSION);
    setcookie(session_name(), '', 100);
    session_destroy();
    header('Location: /?logged_out');
>>>>>>> 9806c21609a4f9958274f1980a2e43cead173763
?>