<?php
    require_once '../../../vendor/autoload.php';
    require_once 'logger.php';
    require_once 'constants.php';

    /**
     * Retrieves a MySQL datetime string based on the provided modifier.
     *
     * This function takes an optional modifier parameter, which defaults to 'now'.
     * It extracts the operand (first character) and amount (remaining characters) from the modifier.
     * Then, it uses PHP's strtotime function to calculate the datetime based on the modifier.
     * Finally, it formats the datetime as a string in the 'Y-m-d H:i:s' format and returns it.
     *
     * @param string $modifier The modifier for the datetime calculation. Defaults to 'now'.
     * @return string The MySQL datetime string.
     */
    function get_mysql_datetime($modifier = 'now') {
        global $log;

        $operand  = substr($modifier, 0, 1);
        $amount   = substr($modifier, 1);
        $modifier = $operand . $amount;

        return date("Y-m-d H:i:s", strtotime("$modifier"));
    }

    /**
     * Calculates the difference in seconds between two MySQL datetime strings.
     *
     * This function takes two MySQL datetime strings as input and calculates the difference in seconds between them.
     * It uses PHP's strtotime function to convert the datetime strings into Unix timestamps, and then subtracts the
     * timestamps to obtain the difference in seconds.
     *
     * @param string $date_one The first MySQL datetime string.
     * @param string $date_two The second MySQL datetime string.
     *
     * @return int The difference in seconds between the two datetime strings.
     */
    function sub_mysql_datetime(string $date_one, string $date_two) {
        $date_one_secs = strtotime($date_one);
        $date_two_secs = strtotime($date_two);
        $seconds_left  = $date_two_secs - $date_one_secs;

        return $seconds_left;
    }

    /**
     * Retrieves a record from the specified table based on the provided identifier and type.
     *
     * This function connects to the database, selects the appropriate table and column based on the provided type,
     * and then retrieves a record from the table where the specified column matches the provided identifier.
     *
     * @param string $identifier The identifier to search for in the specified column.
     * @param string $type       The type of record to retrieve. This determines the table and column to search in.
     *                           Valid values are 'account', 'character', and 'familiar'.
     *
     * @return array|null The retrieved record as an associative array, or null if no matching record is found.
     *                    If an invalid type is provided, the function logs a critical error and returns null.
     */
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
                return null;
        }

        $sql_query = "SELECT * FROM $table WHERE `$column` = ?";
        
        $prepped = $db->prepare($sql_query);
        $prepped->bind_param('s', $identifier);
        $prepped->execute();

        $result = $prepped->get_result();
        $row    = $result->fetch_assoc();

        return $row;
    }
    
    /**
     * Retrieves a global value from the database based on the provided name.
     *
     * Selects the 'value' column from the globals table and retrieves the
     * value where the 'name' column matches the provided parameter.
     *
     * @param string $which The name of the global value to retrieve.
     *
     * @return string|null The retrieved global value, or null if no matching record is found.
     */
    function get_globals($which) {
        global $db;
        $ret_val = '';
        $sql_query = 'SELECT `value` FROM ' . $_ENV['SQL_GLBL_TBL'] . " WHERE `name` = '$which'";
        $result = $db->query($sql_query);
        $row = $result->fetch_assoc();
        
        return $row['value'];
    }
    
    /**
     * Updates a global value in the database.
     *
     * This function connects to the database and updates the 'value' column in the 'tbl_globals' table
     * where the 'name' column matches the provided parameter.
     *
     * @param string $name  The name of the global value to update.
     * @param string $value The new value for the global.
     *
     * @return void
     */
    function set_globals($name, $value) {
        global $db;
        
        $sql_query = "UPDATE `tbl_globals` SET `value` = '$value'" .
                        " WHERE `name` = '$name'";
        $db->query($sql_query);
    }
    
    /**
     * Generates a random floating-point number within a specified range.
     *
     * This function uses the PHP's lcg_value() function to generate a pseudo-random number between 0 and 1.
     * It then multiplies this number by the absolute difference between the maximum and minimum values,
     * and adds the minimum value to the result. This ensures that the generated number falls within the specified range.
     *
     * @param float $min The minimum value for the generated number.
     * @param float $max The maximum value for the generated number.
     *
     * @return float A random floating-point number within the specified range.
     */
    function random_float($min, $max) {
        return ($min + lcg_value() * (abs($max - $min)));
    }

    /**
     * Checks the number of unread emails for a specific account.
     *
     * Selects the relevant emails from the specified envelope status
     * and counts the number of unread emails for the given account ID.
     *
     * @param string $what The type of email to check. Currently, only 'unread' is supported.
     * @param int $account_id The ID of the account for which to check unread emails.
     *
     * @return int|LOAError The number of unread emails for the specified account.
     *                      If an unsupported value is provided for $what, the function returns LOAError::MAIL_UNKNOWN_DIRECTIVE.
     */
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

    /**
     * Determines the friendship status between the current user and another user based on their email address.
     *
     * This function checks the friendship status between the current user and another user by querying the database
     * for records in the tbl_friends table. It sanitizes the email address to prevent SQL injection attacks.
     *
     * @param string $email The email address of the user to check the friendship status with.
     *
     * @return int The friendship status between the current user and the specified user.
     *             The possible return values are:
     *             - FriendStatus::MUTUAL: The current user and the specified user are mutual friends.
     *             - FriendStatus::REQUESTED: The current user has sent a friend request to the specified user.
     *             - FriendStatus::REQUEST: The current user has received a friend request from the specified user.
     *             - FriendStatus::BLOCKED: The current user has blocked the specified user.
     *             - FriendStatus::BLOCKED_BY: The specified user has blocked the current user.
     *             - FriendStatus::NONE: There is no friendship relationship between the current user and the specified user.
     *             - LOAError::FRNDS_FRIEND_STATUS_ERROR: An error occurred while determining the friendship status.
     */
    function friend_status($email) {
        global $db, $account, $log;
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        $sql_query    = 'SELECT * FROM tbl_friends WHERE `email_1` = \'' . $account['email'] . "' AND `email_2` LIKE '%$email%'";
        $log->debug('Friend status, "us" sql: {$sql_query}');
        // deepcode ignore Sqli: Email is sanitized
        $results_us   = $db->query($sql_query);
        $count_one    = $results_us->num_rows;
    
        $sql_query    = 'SELECT * FROM tbl_friends WHERE `email_2` = \'' . $account['email'] . "' AND `email_1` LIKE '%$email%'";
        $log->debug('Friend status, "them" sql: {$sql_query}');
        
        // deepcode ignore Sqli: Email is sanitized
        $results_them = $db->query($sql_query);
        $count_two    = $results_them->num_rows;
        
        $return = FriendStatus::NONE;

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
        
        $log->info("Checking friend statuses for $email -> " . $return->name);
        return LOAError::FRNDS_FRIEND_STATUS_ERROR;
    }

    /**
     * Accepts a friend request from the specified email address.
     *
     * This function checks if a friend request exists from the specified email address to the current user.
     * If a request exists, it inserts a new record into the tbl_friends table with the current user's email
     * and the specified email address. It also logs the acceptance of the friend request.
     *
     * @param string $email The email address of the user who sent the friend request.
     *
     * @return void
     */
    function accept_friend_req($email) {
        global $db, $log, $account;
        
        if (friend_status($email) === FriendStatus::REQUEST) {
            $sql_query = 'INSERT INTO tbl_friends (`email_1`, `email_2`) VALUES (?,?)';
            $db->execute_query($sql_query, [$account['email'], $email]);
            
            $log->info('Friend request accepted', [ 'email_1' => $account['email'], 'email_2' => $email ]);
        }
    }

    /**
     * Retrieves the count of friend requests or other specified friend-related data.
     *
     * This function connects to the database, retrieves a list of distinct email addresses from the account table,
     * and then iterates through each email to determine the count of friend requests.
     *
     * @param string $which The type of friend-related data to retrieve. Currently, only 'requests' is supported.
     * @param int $id The ID of the account for which to retrieve friend-related data. If not provided, the current user's ID is used.
     *
     * @return int The count of friend requests. If an unsupported value is provided for $which, the function returns 0.
     */
    function get_friend_counts($which, $id = 0) {
        global $db, $account;
        $sql_query = 'SELECT DISTINCT email FROM ' . $_ENV['SQL_ACCT_TBL'] . ' WHERE `id` <> ' . $account['id'];
        $results = $db->query($sql_query);

        switch ($which) {
            case 'requests':
                $requests = 0;
                while ($row = $results->fetch_assoc()) {
                    if (friend_status($row['email']) === FriendStatus::REQUEST->name) {
                        $requests++;
                    }
                }
                return $requests;
        }
    }

    /* TODO: test */
    /**
     * Blocks a user from the current user's friend list.
     *
     * This function checks if the specified user is already blocked by the current user.
     * If the user is not already blocked, it updates the friend list to block the user.
     *
     * @param string $email_1 The email of the current user.
     * @param string $email_2 The email of the user to be blocked.
     *
     * @return int|null Returns LOAError::MAIL_ALREADY_BLOCKED if the user is already blocked,
     *                  otherwise, it prints the result and stops the script.
     */
    function block_user($email_1, $email_2) {
        global $db;
        $sqlQuery = 'SELECT email_2 FROM ' . $_ENV['SQL_FRND_TBL'] . 
                    "WHERE `email_1` = ? AND `email_2` = ?"; 
        
        $result = $db->execute_query($sqlQuery, [$email_1, $email_2])->fetch_assoc();

        if (str_starts_with($result['email_2'], '¿b¿')) {
            return LOAError::MAIL_ALREADY_BLOCKED;
        }

        print_r($result);
        die();
    }

    /**
     * Converts a class property name to a corresponding table column name.
     *
     * @param string $property The name of the class property.
     * @return string The corresponding table column name.
     *
     */    
    function clsprop_to_tblcol($property) {
        global $log;

        // Split the property name at each capital letter, allowing for two capital letters in a row.
        // This is to handle cases where the property name follows a camelCase convention.
        $splits = preg_split('/(?=[A-Z]{1,2})/', $property); 

        $log->debug(print_r($splits, 1)); 

        if (count($splits) === 1) {
            return $property;
        }

        $table_column = $splits[0]. '_'. strtolower($splits[1]);

        if (isset($splits[2])) {
            $table_column.= strtolower($splits[2]);
        }

        return $table_column;
    }

    /**
     * Validates an email address using PHP's built-in filter functions.
     *
     * This function takes an email address as input and performs two checks after sanitizing the
     * provided email with PHP's FILTER_SANITIZE_EMAIL.
     * 1. Checks if the sanitized email matches the email originally provided.
     * 2. Checks to see if the sanitized email is a valid email, if the above step is successful.
     *
     * @param string $email The email address to validate.
     *
     * @return bool True if the email address is valid, false otherwise.
     */
    function check_valid_email($email) {
        $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($sanitized_email == $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }
        return false;
    }
?>
