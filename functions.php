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

    function friend_status($email) {
        global $db, $account;
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        $sql_query    = "SELECT * FROM tbl_friends WHERE `email_1` = '" . $account['email'] . "' AND `email_2` LIKE '%$email%'";
        $results_us   = $db->query($sql_query);
        $count_one    = $results_us->num_rows;
     
        $sql_query    = "SELECT * FROM tbl_friends WHERE `email_2` = '". $account['email'] . "' AND `email_1` LIKE '%$email%'";
        $results_them = $db->query($sql_query);
        $count_two    = $results_them->num_rows;
        

        switch (true) {
            case ($count_one && $count_two):
                return FriendStatus::MUTUAL;
            case ($count_one && !$count_two):
//                $result = $results_us->fetch_assoc[
                if (substr($results_us->fetch_assoc()['email_2'], 0, 3) == '¿b¿') {
                    return FriendStatus::BLOCKED;
                }
                return FriendStatus::REQUESTED;
            case ($count_two && !$count_one):
                if (substr($results_them->fetch_assoc()['email_2'], 0, 3) == '¿b¿') {
                    return FriendStatus::BLOCKED_BY;
                }
                return FriendStatus::REQUEST;
            default:
                return FriendStatus::NONE;
        }

        return LOAError::FRIEND_STATUS_ERROR;
    }

    function accept_friend_req($email) {
        global $db, $log, $account;
        
        if (friend_status($email) === FriendStatus::REQUEST) {
            $sql_query = 'INSERT INTO tbl_friends (`email_1`, `email_2`) VALUES (?,?)';
            $prepped = $db->prepare($sql_query);
            $prepped->bind_param("ss", $account['email'], $email);
            $prepped->execute();
            
            $log->info('Friend request accepted', 
                [
                    email_1 => $account['email'], 
                    email_2 => $email
                ]
            );
        }
    }

    function get_friend_counts($which, $id = 0) {
        global $db, $account;
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE `id` <> ' . $account['id'];
        $results = $db->query($sql_query);

        switch ($which) {
        case 'requests':
            $requests = 0;
            while ($row = $results->fetch_assoc()) {
                if (friend_status($row['email']) === FriendStatus::REQUEST) {
                    $requests++;
                }
            }
            return $requests;
        }
    }

    /* TODO: test */
    function block_user($email) {
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_FRND_TBL'] . 
                     "WHERE `email_2` = '$email' " .
                     "AND `email_1` = '" . $account['email'] . "'";
        $result = $db->query($result)->fetch_assoc();
        print_r($result);
        die();
    }

    function save_character($serialized_data) {
        $sql_query = 'UPDATE ' . $_ENV['SQL_ACCT_TBL'] . ' ' .
                     "SET `serialized_character` = '$serialized_data' ";
                     "WHERE `id` = $account_id";
        $db->query($sql_query);
    }

    function load_character($account_id) {
        $sql_query = 'SELECT `serialized_character` ' .
                     'FROM ' . $_ENV['SQL_ACCT_TBL'] . 
                     " WHERE `id` = $account_id";
        $db->query($sql_query);
        $serialized_data = $db->fetch_assoc();
    
        $character = new Character($account_id);
        $character = unserialize($serialized_data);
        
        return $character;
    }
?>
