<?php
    declare(strict_types = 1);
    session_start();
    require '../vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    require_once 'logger.php';
    require_once 'db.php';
    require_once 'constants.php';
    require_once 'functions.php';

    global $log;
    
    /*

        $data = [
            type => 'account|character|familiar',
            id => 1,
            tdata => [
                column_name => 'value',
                column_name2 => 'value',
            ]
        ]

    */


    if (isset($_REQUEST['data'])) {
        global $log, $db;
        $data_obj = json_decode($_REQUEST['data']);
        $table = "";
        $log->info("Request to Save",
            [
                "Type" => $data_obj->type,
                "ID"   => $data_obj->id
            ]
        );

        switch ($data_obj->type) {
            case 'account':
                $table = $_ENV['SQL_ACCT_TBL'];
                break;
            case 'character':
                $table = $_ENV['SQL_CHAR_TBL'];
                break;
            case 'familiar':
                $table = $_ENV['SQL_FMLR_TBL'];
                break;
            default:
                exit(LOAError::SQLDB_UNKNOWN_SAVE_TYPE);
        }

        $sql_query = "UPDATE $table SET ";

        foreach ($kvp as $data => $tdata) {
            $column = key($kvp);
            //$value  = value($kvp);
            
            if (is_string($value)) {
                $value = "'$value'";
            }
            
            $sql_query .= "`$column` = $value,";
        }
        rtrim($sql_query, ',');
        
        $sql_query .= " WHERE `id` = " . 1;

        if ($db->query($sql_query)) {
            echo "Error";
        } else {
            echo "Success";
        }
    }
?>

<div id="output"></div>
