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
        die(Error::SQLDB_NOCONNECTIONT);
    }
    
    $log->info("Connection to $sql_host:$sql_port successful", ['Username' => $sql_user]);
    
    function do_sql($action, $targets, $table, $values = null, $conditions = null) {
        $query = '';
        [$targets, $target_count] = explode(':', $targets);
        $targets = explode(',', $targets);
        
        switch ($action) {
            case 'INSERT':
                $query = "INSERT INTO $table ($targets) VALUES(";
                $query .= str_repeat('?,', $target_count);
                $query = preg_replace('/,$/', ')', $query);
                
                break;
            case 'UPDATE':
                $query = "UPDATE $table SET ";
                for ($i=0; $i<$target_count - 1; $i++) {
                    $query .= $targets[$i] . " = ?,";
                    
                }
                rtrim(',' $query);
                
                if ($conditions) {
                    $query .= " WHERE $conditions";
                }
                
                break;
            case 'SELECT':
                $query = 'SELECT ';
                $query .= implode(',', $targets);
                $query .= " FROM $table";
                
                if ($conditions) {
                    $query .= ' WHERE ';
                    for ($i=0; $i<$target_count - 1; $i++) {
                        $query .= $targets[$i] . " = ?,";
                    }
                    rtrim(',', $query);
                }
                
                break;
            default:
                return Error::FUNCT_DOSQL_INVALIDACTION;
                
        }
        
        $bind_str = str_repeat('s', $target_count);
        $prepped  = $db->prepare($query);
        $prepped->bind_param($bind_str, ...$values);
        
        if ($prepped->execute()) {
            $results = $prepped->get_results();
        } else {
            $log->error("Couldn't execute generated query in sql wrapper",
                    [ 
                        'Query' => $query,
                        'Error' => $prepped->error,
                    ]
            );
            
            return Error::SQLDB_PREPPED_EXECUTE;
        }
        
        
        if ($results->num_rows) {
            return $results;
        }
        
        return 0;
    }
?>
