<?php
    include 'db.php';
   
    function get_mysql_datetime() {
        return date("Y-m-d H:i:s", strtotime("now"));
    }

    function get_user($email, $type) {
        global $db;
        $table = $type == 'account' ? 'SQL_ACCT_TBL' : 'SQL_CHAR_TBL';
        $column = $type == 'account' ? 'email' : 'account_id';

        $sql_query = 'SELECT * FROM ' . $_ENV[$table] . " WHERE $column = ?";
    
        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('s', $email);
        $prepped->execute();

        $result = $prepped->get_result();
        $user = $result->fetch_assoc();

        return $user;
    }

    function get_globals($which) {
        global $db;
        $ret_val = '';
        $sql_query = 'SELECT `value` FROM ' . $_ENV['SQL_GLBL_TBL'] . " WHERE `name` = '$which'";
        $result = $db->query($sql_query);
        $row = $result->fetch_assoc();
        
        return $row['value'];
    }
    
    function set_globals($name, $value) {
        global $db;
        
        $sql_query = "UPDATE `tbl_globals` SET `value` = '$value'" .
                        " WHERE `name` = '$name'";
        $db->query($sql_query);
    }