<?php
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
                if (substr($results_them->fetch_assoc()[email_2], 0, 3) == '¿b¿') {
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

    function get_friend_counts($which) {
        global $db, $account;
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE `id` <> ' . $account['id'];
        $results = $db->query($sql_query);

        switch ($which) {
        case 'requests':
            $requests = 0;
            while ($row = $results->fetch_assoc()) {
                if (friend_stats($email) === FriendStatus::REQUEST) {
                    $requests++;
                }
            }
            return $requests;
        }
    }

    function block_user($email) {
        $sql_query = 'SELECT * FROM ' . $_ENV['SQL_FRND_TBL'] . 
                     "WHERE `email_2` = '$email' " .
                     "AND `email_1` = '" . $account['email'] . "'";
        $result = $db->query($result)->fetch_assoc();
        print_r($result);
        die();

    }
?>
