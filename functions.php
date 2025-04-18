<?php

use Game\System\Enums\AbuseType;
use Game\Character\Enums\FriendStatus;
use Game\Character\Enums\Races;
use Game\System\Enums\LOAError;


     /**     * Retrieves a MySQL datetime string based on the provided modifier.
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

        if ($modifier !== 'now') {
            $operand  = substr($modifier, 0, 1);
            $amount   = substr($modifier, 1);
            $modifier = "$operand$amount";
        }

        return date("Y-m-d H:i:s", strtotime((string) $modifier));
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
        global $db, $t;
        $ret_val = '';
        $sql_query = "SELECT `value` FROM {$t['globals']} WHERE `name` = '$which'";
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
        global $db, $t;

        $sql_query = "UPDATE {$t['globals']} SET `value` = '$value' WHERE `name` = '$name'";
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
     * @param   int $precision Round to the supplied amount of decimal places.
     *
     * @return float A random floating-point number within the specified range.
     */
    function random_float($min, $max, $precision): float {
        return round($min + mt_rand() / mt_getrandmax() * (abs($max - $min)), $precision);
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
     * @return int|LOAError|string The number of unread emails for the specified account.
     *                      If an unsupported value is provided for $what, the function returns LOAError::MAIL_UNKNOWN_DIRECTIVE.
     */
    function check_mail($what): int|LOAError|string {
        global $db, $log, $t;

        switch ($what) {
            case 'unread':
                $sql_query = "SELECT * FROM {$t['mail']} WHERE NOT FIND_IN_SET('READ', `status`) AND `r_aid` = ?";
                $result = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->num_rows;
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
     * @return Game\Character\Enums\FriendStatus The friendship status between the current user and the specified user.
     *             The possible return values are:
     *             - FriendStatus::MUTUAL: The current user and the specified user are mutual friends.
     *             - FriendStatus::REQUESTED: The current user has sent a friend request to the specified user.
     *             - FriendStatus::REQUEST: The current user has received a friend request from the specified user.
     *             - FriendStatus::BLOCKED: The current user has blocked the specified user.
     *             - FriendStatus::BLOCKED_BY: The specified user has blocked the current user.
     *             - FriendStatus::NONE: There is no friendship relationship between the current user and the specified user.
     *             - LOAError::FRNDS_FRIEND_STATUS_ERROR: An error occurred while determining the friendship status.
     */
    function friend_status(int $character_id): FriendStatus {
        global $db, $log, $t;

        $status    = FriendStatus::NONE;
        $sql_query = "SELECT * FROM {$t['friends']} WHERE `recipient_id` = ? OR `sender_id` = ?";
        $results   = $db->execute_query($sql_query, [ $character_id, $character_id ])->fetch_assoc();

        if ($results) {
            $status = FriendStatus::name_to_enum($results['friend_status']);
        }

        if ($status == null) {
            $status = FriendStatus::NONE;
        }

        //$log->warning("Friend status between character ID $character_id -> {$status->name}");        
        return $status;
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
    function accept_friend_req($sender):bool {
        global $db, $log, $t;

        if (friend_status($sender) === FriendStatus::REQUEST_RECV) {
            $sql_query = "UPDATE {$t['friends']} SET `friend_status` = ? WHERE `recipient_id` = ?";
            $db->execute_query($sql_query, [ FriendStatus::MUTUAL->value, $_SESSION['character-id'] ]);
            $log->info('Friend request accepted', [ 'sender' => $sender, 'recipient' => $_SESSION['character-id'] ]);
            return true;
        } else {
            return false;
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
     * @return array|int The count of friend requests. If an unsupported value is provided for $which, the function returns 0.
     */
    function get_friend_counts(?FriendStatus $status, bool $return_list=false): array {
        global $db, $character, $t;
        $count = 0;
        $ids = [];

        $sql_query = <<<SQL
            SELECT 
                `friend_status` AS `status`,
                COUNT(`friend_status`) AS `count`
            FROM {$t['friends']}
            WHERE
                sender_id = ? OR
                recipient_id = ?
            GROUP BY `friend_status`
        SQL;

        $statuses = $db->execute_query($sql_query, [ $character->get_id(), $character->get_id() ])->fetch_all(MYSQLI_ASSOC);

        $statuses['MUTUAL'] ?? 0;
        $statuses['REQUEST_RECV'] ?? 0;
        $statuses['REQUEST_SENT'] ?? 0;
        $statuses['BLOCKED'] ?? 0;

        if ($return_list) {
            $status_clause = null;
            if ($status !== null) {
                $status_clause = " AND `friend_status` = '" . $status->name . "'";
            }
                

            $sql_query = <<<SQL
                SELECT DISTINCT
                    IF(`recipient_id` = ?, `sender_id`, `recipient_id`) AS `character_id`
                FROM {$t['friends']}
                WHERE `recipient_id` = ? OR `sender_id` = ? $status_clause
            SQL;
            $ids = $db->execute_query($sql_query, [$character->get_id(), $character->get_id(), $character->get_id()])->fetch_all(MYSQLI_ASSOC);
            $data = [];
            $data['statuses'] = $statuses;
            $data['ids'] = $ids;
            return $data;
        }
        
        return $statuses;
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
     * @return int|LOA::Error Returns LOAError::MAIL_ALREADY_BLOCKED if the user is already blocked,
     *                  otherwise, it prints the result and stops the script.
     */
    function block_user($email_1, $email_2): int {
        global $db, $t;
        $sql_query = "INSERT INTO {$t['friends']} (`sender_id`, `recipient_id`, `friend_status`) VALUES (?, ?, ?) UPDATE ON DUPLICATE KEY `friend_status` = 'BLOCKED'";
        $result = $db->execute_query($sql_query, [$email_1, $email_2, 'BLOCKED']);
        
        return 0;
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
    function check_valid_email($email): bool {
        $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($sanitized_email == $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks for potential abuse based on the provided type and data.
     *global $log;
     * @param Game\System\Enums\AbuseType $type The type of abuse to check for (e.g. MULTISIGNUP)
     * @param mixed $data Additional data to use in the abuse check (e.g. IP address)
     *
     * @return bool True if abuse is detected, false otherwise
     */
    function check_abuse(AbuseType $type, $account_id, $ip, $threshold = 1): bool {
        global $db, $log, $t;

        switch ($type) {
            case AbuseType::MULTISIGNUP:
                $sql_query = <<<SQL
                                SELECT `id` FROM {$t['logs']}
                                WHERE `type` = ?
                                    AND `ip` = ?
                                    AND `date` BETWEEN (NOW() - INTERVAL 1 HOUR) AND NOW()
                            SQL;
                $count = $db->execute_query($sql_query, [ $type->name, $ip ])->num_rows;

                if ($count > $threshold) {
                    return true;
                }

                return false;
            case AbuseType::TAMPERING:
                $sql_query = <<<SQL
                    SELECT `id` FROM {$t['logs']}
                    WHERE `type` = ? AND `ip` = ?
                SQL;
                $count = $db->execute_query($sql_query, [ $type->name, $ip ])->num_rows;

                if ($count > $threshold) {
                    return true;
                }
                return false;
            default:
                $log->error("No type specified for abuse lookup");
        }

        return false;
    }


    function generate_modal($id, $bg_color, $header, $body, ModalButtonType $btn_type): string {
        $btn = null;
        if ($btn_type === ModalButtonType::YesNo) {
            $btn  = '<button type="button" class="btn btn-danger"  data-bs-dismiss="modal">No</button>';
            $btn .= '<button type="button" class="btn btn-success" data-bs-dismiss="modal">Yes</button>';
        } elseif ($btn_type === ModalButtonType::Close) {
            $btn  = '<button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>';
        } elseif ($btn_type === ModalButtonType::OKCancel) {
            $btn   = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>';
            $btn  .= '<button type="button" class="btn btn-primary"   data-bs-dismiss="modal">Okay</button>';
        } else {
            $btn = '<input type="button" value=":(" />';
        }

        $html = '<div class="modal fade" id="' . $id . '-modal" tabindex="-1" aria-hidden="true" style="z-index: 1044;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-' . $bg_color . ' text-bg-' . $bg_color . '">
                                <h1 class="modal-title fs-5">' . $header . '</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <strong>' . $body . '</strong>
                            </div>

                            <div class="modal-footer">
                                ' . $btn . '
                            </div>
                        </div>
                    </div>
                </div>';

        return $html;
    }

    function get_monster_count(): int {
        global $system;
        return count($system->monsters);
    }

    function getNextTableID($table): int {
        global $db, $t;
        $sql_query = "SELECT IF(MAX(`id`) IS NULL, 1, MAX(`id`)+1) AS `next_id` FROM $table";
        $next_id = $db->execute_query($sql_query)->fetch_assoc()['next_id'];
        
        return $next_id;
    }

    function write_log(string $log_type, string $message, string $ip): void {
        global $db, $t;
        
        $sql_query = "INSERT INTO {$t['logs']} (`type`, `message`, `ip`) VALUES (?, ?, ?)";
        $db->execute_query($sql_query, [ $log_type, $message, $ip ]);
    }

    function ban_user($account_id, $length_secs, $reason): void {
        global $db, $t;
        $expires = get_mysql_datetime("+$length_secs seconds");
        $sql_query = "UPDATE {$t['accounts']} SET `banned` = 'True' WHERE `id` = ?";
        $db->execute_query($sql_query, [ $account_id ]);

        $sql_query = <<<SQL
            INSERT INTO {$t['banned']} 
                (`account_id`, `expires`, `reason`)
            VALUES (?, ?, ?)
        SQL;

        $db->execute_query($sql_query, [ $account_id, $expires, $reason ]);
    }

    function validate_race($race): string {
        global $log;

        $valid_race = 0;

        foreach (Races::cases() as $enum_race) {
            if ($race === $enum_race->name) {
                $valid_race = 1;
            }
        }

        if (!$valid_race) {
            $race = Races::random()->name;
            $log->critical(
            "Race submitted wasn't an acceptable selection, choosing random enum: ",
                [ 'Race' => $race ]
            );
        }
        
        return $race;
    }

    function validate_avatar($avatar): string {
        global $log;

        $arr_images = scandir('img/avatars');

        if (!array_search($avatar, $arr_images)) {
            $avatar_now = 'avatar-unknown.webp';
            $log->critical(
                'Avatar wasn\'t found in our ' .
                'accepted list of avatar choices!',
                [
                    'Avatar' => $avatar,
                    'Avatar_now' => $avatar_now,
                ]
            );
            $avatar = $avatar_now;
        }

        return $avatar;
    }

    function safe_serialize($data, ?bool $unserialize=null): mixed {
        global $log;
        $ret_data = null;
        $log->warning("serialize in: ", [ 'Data' => $data, 'Unserialize' => $unserialize ]);
        if ($unserialize === true) {
            $ret_data = unserialize(base64_decode($data));
        } else {
            $ret_data = base64_encode(serialize($data));
        }

        return $ret_data;
    }

    function check_session(): bool {
        global $db, $log, $t;

        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] != 1) {
            $log->warning("Sessions var 'logged-in' not set to 1", [ print_r($_SESSION, true), print_r(debug_backtrace(), true) ] );
            return false;
        }

        $sql_query = "SELECT `session_id` FROM {$t['accounts']} WHERE `id` = ?";
        $result = $db->execute_query($sql_query, [ $_SESSION['account-id'] ])->fetch_assoc();
        
        if (!$result) {
            $log->warning("session_id not found");
            return false;
        }

        $session = $result['session_id'];
        
        if ($session != session_id()) {
            $log->warning("Session ID in db doesn't match browser session id", [ 'SessionDB' => $session, 'SessionBrowser' => session_id() ]);
            return false;
        }

        return true;
    }

    function gen_csrf_token(): string {
        global $log;
        $csrf = bin2hex(random_bytes(14)) . 'L04D' . bin2hex(random_bytes(14));
        $log->warning("csrf: $csrf");
        return $csrf;
    }

    function check_csrf($req_csrf): bool {
        if ($req_csrf != $_SESSION['csrf-token']) {
            $_SESSION = [];
            session_destroy();
            header('Location: /?csrf_fail');
            exit();
        }

        return true;
    }

    function dump_to_html(mixed $obj, ?bool $exit=true): int {
        echo '<pre>';
        print_r($obj);
        
        if ($exit) {
            exit();
        } 

        return 0;
    }

    function fix_name_header(string $name): string {
        $ending_char = substr($name, -1, 1);

        if (preg_match('/[sS]/', $ending_char)) {
            return "$name'";
        }

        return "$name's";
    }