<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require_once 'logger.php';
    require_once 'constants.php';

    function get_mysql_datetime($modifier = 'now') {
        global $log;

        $operand  = substr($modifier, 0, 1);
        $amount   = substr($modifier, 1);
        $modifier = $operand . $amount;

        return date("Y-m-d h:i:s", strtotime("$modifier"));
    }

    function table_to_obj($identifier, $type) {
        global $db, $log;
        $table = '';
        $column = '';

        switch ($type) {
            case 'account':
                $column = 'email';
                $table  = $_ENV['SQL_ACCT_TBL'];
                break;
            case 'character':
                $column = 'account_id';
                $table  = $_ENV['SQL_CHAR_TBL'];
                break;
            case 'familiar':
                $column = 'account_id';
                $table  = $_ENV['SQL_FMLR_TBL'];
                break;
            default:
                $log->critical("Invalid 'type' provided to " . __FUNCTION__ . ": $type"); 
                break;
        }

        $sql_query = "SELECT * FROM $table WHERE `$column` = ?";
        
        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('s', $identifier);
        $prepped->execute();

        $result = $prepped->get_result();
        $row    = $result->fetch_assoc();

        return $row;
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

    function check_mail($what, $account_id) {
        global $db, $log;

        switch ($what) {
            case 'unread':
                $result = $db->query('SELECT * FROM ' . $_ENV['SQL_MAIL_TBL'] . ' ' .
                                     "WHERE `read` = 'False' AND `account_id` = $account_id")->num_rows;
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

    function save_character($id, $character) {
        global $db;
        $sql_query = 'UPDATE ' . $_ENV['SQL_ACCT_TBL'] . ' ' .
                     "SET `serialized_character` = '$serialized_data' ";
                     "WHERE `id` = $id";
        $db->query($sql_query); 
    }

    function load_character($id) {
        $sql_query = 'SELECT `serialized_character` ' .
                     'FROM ' . $_ENV['SQL_ACCT_TBL'] . 
                     " WHERE `id` = $account_id";
        
        $db->query($sql_query);
        
        $serialized_data = $db->fetch_assoc();
    
        $character = new Character($account_id);
        $character = unserialize($serialized_data);
        
        return $character;
    }

    function generate_egg($familiar) {
        global $log;
 
        $rarity_roll  = random_float(0, 100);
        $rarity       = ObjectRarity::getObjectRarity($rarity_roll);
        $rarity_color = get_rarity_color($rarity);

        $familiar->set_level(1);
        
        $familiar->set_rarityColor($rarity_color);
        $familiar->set_rarity($rarity);
        
        $familiar->set_dateAcquired(get_mysql_datetime());
        $familiar->set_hatchTime(get_mysql_datetime('+8 hours'));
        
        $familiar->saveFamiliar();
    }

    function get_rarity_color($rarity) {
        switch($rarity->name) {
            case "WORTHLESS":
                return "#FACEF0";
                break;
            case "TARNISHED":
                return "#779988";
                break;
            case "COMMON":
                return "#ADD8D7";
                break;
            case "ENCHANTED":
                return "#08E71C";
                break;
            case "MAGICAL":
                return "#A6D9F8";
                break;
            case "LEGENDARY":
                return "#F8C81C";
                break;
            case "EPIC":
                return "#CAB51F";
                break;
            case "MYSTIC":
                return "#01CBF6";
                break;
            case "HEROIC":
                return "#1C4F2C";
                break;
            case "INFAMOUS":
                return "#CB20EE";
                break;
            case "GODLY":
                return "#FF2501";
                break;
        }
    }

    /* This ridiculousness is directly related to camelCase being standard */
    /*         for PHP class properties - via PHPCS's tool on GitHub       */
    /*             Pass it: ourProperty - Get back: our_property           */
    function clsprop_to_tblcol($property) {
        global $log;

        $splits = preg_split('/(?=[A-Z])/', $property);

        if (count($splits) != 2) {
            return $property;
        }
 
        $table_column = $splits[0] . '_' . strtolower($splits[1]);
        $log->critical("prop: $property - splits: " . print_r($splits, 1) . " - return: $table_clumn"); 
            
        return $table_column;
    }
            
?>
