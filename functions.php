<?php
    function get_mysql_datetime() {
        return date("Y-m-d H:i:s", strtotime("now"));
    }

    function get_user($email, $type) {
        global $db;
        $table  = $type == 'account' ? 'SQL_ACCT_TBL' : 'SQL_CHAR_TBL';
        $column = $type == 'account' ? 'email' : 'account_id';

        $sql_query = "SELECT * FROM " . ($_ENV[$table]) . " WHERE $column = ?";
    
        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('s', $email);
        $prepped->execute();

        $result = $prepped->get_result();
        $user   = $result->fetch_assoc();

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
    
    function random_float ($min,$max) {
       return ($min + lcg_value() * (abs($max - $min)));
    }

    function check_mail($what, $id) {
        global $db, $log;
        $log->info("Checking mail for '$what' for id '$id'");
        switch ($what) {
            case 'unread':
                $result = $db->query(
                    "SELECT * FROM tbl_mail WHERE `read` = 'False' AND id = $id"
                )->num_rows;
                $log->info(print_r($result, 1));
                return $result;
            default:
                return LOAError::MAIL_UNKNOWN_DIRECTIVE;
        }
    }
?>
