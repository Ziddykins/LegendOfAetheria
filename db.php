<?php
    include('logger.php');

    $sql_host = $_ENV['SQL_HOST'];
    $sql_port = $_ENV['SQL_PORT'];
    $sql_db   = $_ENV['SQL_DB'];
    $sql_user = $_ENV['SQL_USER'];
    $sql_pass = $_ENV['SQL_PASS'];
    
    $db = new mysqli($sql_host, $sql_user, $sql_pass, $sql_db, $sql_port);

    if ($db->connect_errno) {
        $log->critical('Unable to make a connection to the SQL database D:');
        die();
    }
    $log->info("Connection to $sql_host:$sql_port successful", ['Username' => $sql_user]);
?>