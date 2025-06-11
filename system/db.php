<?php

    use Game\System\Enums\LOAError;
    
    try {
        @include_once "../constants.php";
    } catch (Exception $e) {
        @include_once "./constants.php";
    } finally {
        @include_once "../../constants.php";
    }

    require WEBROOT . '/vendor/autoload.php';
    require_once SYSTEM_DIRECTORY . '/logger.php';

    
    $dotenv = Dotenv\Dotenv::createImmutable(WEBROOT);
    $dotenv->safeLoad();
    

    $sql_host = $_ENV['SQL_HOST'];
    $sql_port = $_ENV['SQL_PORT'];
    $sql_db   = $_ENV['SQL_DB'];
    $sql_user = $_ENV['SQL_USER'];
    $sql_pass = $_ENV['SQL_PASS'];
    
    $db = new mysqli($sql_host, $sql_user, $sql_pass, $sql_db, $sql_port);

    if ($db->connect_errno) {
        $log->critical('Unable to make a connection to the SQL database: ' . $db->connect_error);
        die(LOAError::SQLDB_NOCONNECTION);
    }